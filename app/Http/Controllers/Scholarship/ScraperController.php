<?php
namespace App\Http\Controllers\Scholarship;
use App\Http\Controllers\Controller;
use App\Models\ScrapedScholarship;
use App\Models\Scholarship;
use App\Services\ScholarshipScraperService;
use Illuminate\Http\Request;

class ScraperController extends Controller
{
    protected ScholarshipScraperService $scraper;

    public function __construct(ScholarshipScraperService $scraper)
    {
        $this->scraper = $scraper;
    }

    public function index()
    {
        $scraped = ScrapedScholarship::latest()->paginate(20);
        $sources = $this->scraper->getSources();

        $total    = ScrapedScholarship::count();
        $imported = ScrapedScholarship::where('imported', true)->count();
        $newCount = ScrapedScholarship::where('imported', false)->count();

        $highConf = ScrapedScholarship::where('imported', false)
            ->whereNotNull('benefits')
            ->where('benefits', '!=', '')
            ->count();

        $stats = [
            'total'     => $total,
            'new'       => $newCount,
            'updated'   => 0,
            'imported'  => $imported,
            'high_conf' => $highConf,
            'last_run'  => ScrapedScholarship::max('created_at'),
        ];

        return view('scholarship.scraper.index', compact('scraped', 'stats', 'sources'));
    }

    /**
     * Run ALL sources: syncs results and auto-imports them straight into
     * the Scholarship Programs list — no separate import step needed.
     */
    public function run(Request $request)
    {
        try {
            $results = $this->scraper->scrapeAll();
            [$saved, $imported] = $this->persistResults($results, '');

            $msg = "Sync complete. {$saved} new scholarship(s) synced and saved directly to Scholarship Programs.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg, 'reload' => true]);
            }
            return redirect()->route('scholarship.scraper.index')->with('success', $msg);

        } catch (\Exception $e) {
            $msg = 'Sync failed: ' . $e->getMessage();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg], 500);
            }
            return redirect()->route('scholarship.scraper.index')->with('error', $msg);
        }
    }

    /**
     * Run a SINGLE source: syncs and auto-imports into Scholarship Programs.
     */
    public function runSource(Request $request, string $source)
    {
        try {
            $results = $this->scraper->scrapeSource($source);
            [$saved, $imported] = $this->persistResults($results, $source);

            $label = ucwords(str_replace(['-','_'], ' ', $source));
            $msg   = "$label synced — ".count($results)." found, $imported new program(s) saved directly to Scholarship Programs.";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg, 'reload' => true]);
            }
            return redirect()->route('scholarship.scraper.index')->with('success', $msg);

        } catch (\Exception $e) {
            $msg = 'Sync failed for '.$source.': '.$e->getMessage();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg], 500);
            }
            return redirect()->route('scholarship.scraper.index')->with('error', $msg);
        }
    }

    /**
     * Save scraped items with the real scraped_scholarships columns and
     * immediately create the matching Scholarship program row. Returns
     * [newSyncedCount, importedToProgramsCount].
     */
    private function persistResults(array $results, string $defaultSourceKey): array
    {
        $sources   = $this->scraper->getSources();
        $sourceDef = $sources[$defaultSourceKey] ?? null;

        // Scholarship.type drives Government/Private/Institutional badges —
        // the AI sometimes returns award-level types ("Full Scholarship"),
        // so fall back to the source's own classification when unknown.
        $allowedTypes = ['Government','Private','Institutional'];

        // The extractor regenerates names on every run, so exact-name
        // dedupe misses near-identical repeats ("... (PSU) Scholarship" vs
        // "... (PSU) Scholarships") and would duplicate rows indefinitely.
        $existingScraped     = ScrapedScholarship::all(['id','name','imported','scholarship_id']);
        $existingProgramIds  = Scholarship::pluck('id','name');

        $saved = 0;
        $importedCount = 0;

        foreach ($results as $item) {
            // Skip items with no name — AI may return malformed entries
            if (empty($item['name'] ?? '')) continue;

            $itemType = in_array($item['type'] ?? '', $allowedTypes, true)
                ? $item['type']
                : ($sourceDef['type'] ?? ($item['source_type'] ?? 'Government'));

            $itemAgency = $item['source'] ?? ($sourceDef['agency'] ?? ($defaultSourceKey ?: ''));
            $itemUrl    = $item['link'] ?? ($sourceDef['url'] ?? '');

            $existing = $existingScraped->first(
                fn ($row) => $this->namesMatch($row->name, $item['name'])
            );

            if ($existing) {
                // Refresh sync metadata; program row is managed below
                $existing->update([
                    'benefits'        => $item['benefits']    ?? $existing->benefits,
                    'requirements'    => $item['requirements'] ?? $existing->requirements,
                    'deadline'        => !empty($item['end_date']) ? $item['end_date'] : $existing->deadline,
                    'slots'           => is_numeric($item['slots'] ?? null) ? (int)$item['slots'] : $existing->slots,
                    'source_url'      => $itemUrl ?: $existing->source_url,
                    'source_agency'   => $itemAgency ?: $existing->source_agency,
                    'source_type'     => $itemType ?? $existing->source_type,
                    'status'          => 'updated',
                    'last_scraped_at' => now(),
                ]);
                $scraped = $existing;
            } else {
                $scraped = ScrapedScholarship::create([
                    'name'            => $item['name'],
                    'benefits'        => $item['benefits']     ?? '',
                    'requirements'    => $item['requirements'] ?? '',
                    'deadline'        => !empty($item['end_date']) ? $item['end_date'] : null,
                    'slots'           => is_numeric($item['slots'] ?? null) ? (int)$item['slots'] : null,
                    'source_url'      => $itemUrl,
                    'source_agency'   => $itemAgency,
                    'source_type'     => $itemType,
                    'status'          => 'new',
                    'ai_confidence'   => 75,
                    'imported'        => false,
                    'last_scraped_at' => now(),
                ]);
                $saved++;
            }

            // Auto-import: create the Scholarship program immediately so it
            // shows up in the Programs list without a manual import step.
            if (!$scraped->imported) {
                $existingProgramId = $existingProgramIds->first(
                    fn ($id, $name) => $this->namesMatch($name, $scraped->name)
                );

                if ($existingProgramId) {
                    $scraped->update(['imported' => true, 'scholarship_id' => $existingProgramId]);
                } else {
                    $scholarship = Scholarship::create([
                        'name'         => $scraped->name,
                        'type'         => $scraped->source_type ?? 'Government',
                        'benefits'     => $scraped->benefits    ?? '',
                        'requirements' => $scraped->requirements ?? '',
                        'slots'        => $scraped->slots       ?? null,
                        'end_date'     => $scraped->deadline    ?? null,
                        'source'       => $scraped->source_agency ?? '',
                        'status'       => 'Active',
                        'ai_criteria'  => json_encode([
                            'gwa_max'       => 1.75,
                            'income_max'    => 400000,
                            'no_failing'    => true,
                            'no_discipline' => false,
                        ]),
                    ]);
                    $scraped->update(['imported' => true, 'scholarship_id' => $scholarship->id]);
                    $existingProgramIds->put($scholarship->name, $scholarship->id);
                    $importedCount++;
                }
            }
        }

        return [$saved, $importedCount];
    }

    /**
     * Loose scholarship-name comparison used for dedupe across sync runs
     * (the extractor rarely reproduces a name verbatim).
     */
    private function namesMatch(string $a, string $b): bool
    {
        $na = preg_replace('/[^A-Z0-9]/', '', strtoupper($a));
        $nb = preg_replace('/[^A-Z0-9]/', '', strtoupper($b));

        if ($na === '' || $nb === '') return false;
        if ($na === $nb) return true;
        if (str_contains($na, $nb) || str_contains($nb, $na)) return true;

        // 0.9 — high enough that sibling programs from one source
        // ("...Veterans' Widows/Spouses/Children") stay distinct, low
        // enough to absorb run-to-run wording drift from the extractor.
        return similar_text($na, $nb) / max(strlen($na), strlen($nb)) >= 0.9;
    }

    /**
     * Import ALL high-confidence unimported scholarships at once.
     */
    public function importAll(Request $request)
    {
        $toImport = ScrapedScholarship::where('imported', false)
            ->whereNotNull('benefits')
            ->where('benefits', '!=', '')
            ->get();

        $count = 0;
        foreach ($toImport as $scraped) {
            if (Scholarship::where('name', $scraped->name)->exists()) {
                $scraped->update(['imported' => true]);
                continue;
            }
            $scholarship = Scholarship::create([
                'name'         => $scraped->name,
                'type'         => $scraped->source_type ?? 'Government',
                'benefits'     => $scraped->benefits     ?? '',
                'requirements' => $scraped->requirements ?? '',
                'slots'        => $scraped->slots        ?? null,
                'end_date'     => $scraped->deadline     ?? null,
                'source'       => $scraped->source_agency ?? '',
                'status'       => 'Active',
                'ai_criteria'  => json_encode([
                    'gwa_max'       => 1.75,
                    'income_max'    => 400000,
                    'no_failing'    => true,
                    'no_discipline' => false,
                ]),
            ]);
            $scraped->update(['imported' => true, 'scholarship_id' => $scholarship->id]);
            $count++;
        }

        $msg = "$count scholarship(s) imported successfully into Scholarship Programs.";
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $msg, 'reload' => true]);
        }
        return redirect()->route('scholarship.scraper.index')->with('success', $msg);
    }

    /**
     * Import a single scraped scholarship into the main scholarships table.
     */
    public function import(Request $request, ScrapedScholarship $scraped)
    {
        if ($scraped->imported) {
            $msg = 'This scholarship has already been imported.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $scholarship = Scholarship::create([
            'name'         => $scraped->name,
            'type'         => $scraped->source_type ?? 'Government',
            'benefits'     => $scraped->benefits     ?? '',
            'requirements' => $scraped->requirements ?? '',
            'slots'        => $scraped->slots        ?? null,
            'end_date'     => $scraped->deadline     ?? null,
            'source'       => $scraped->source_agency ?? '',
            'status'       => 'Active',
            'ai_criteria'  => json_encode([
                'gwa_max'       => 1.75,
                'income_max'    => 400000,
                'no_failing'    => true,
                'no_discipline' => false,
            ]),
        ]);

        $scraped->update([
            'imported'       => true,
            'scholarship_id' => $scholarship->id,
        ]);

        $msg = '"'.$scraped->name.'" imported successfully into Scholarship Programs.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $msg, 'reload' => true]);
        }
        return redirect()->route('scholarship.scraper.index')->with('success', $msg);
    }

    /**
     * Delete a scraped scholarship from the synced list.
     */
    public function destroySynced(Request $request, ScrapedScholarship $scraped)
    {
        $scraped->delete();
        $msg = 'Removed from synced list.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $msg]);
        }
        return back()->with('success', $msg);
    }
}
