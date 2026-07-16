<?php
namespace App\Http\Controllers\Admin;
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

        return view('admin.scholarships.scraper', compact('scraped', 'stats', 'sources'));
    }

    /**
     * Run ALL sources and save results to scraped_scholarships table.
     */
    public function run(Request $request)
    {
        try {
            $results = $this->scraper->scrapeAll();
            $saved   = 0;

            foreach ($results as $item) {
                // Skip items with no name — Groq may return malformed entries
                if (empty($item['name'] ?? '')) continue;

                $exists = ScrapedScholarship::where('name', $item['name'])
                    ->where('source', $item['source'] ?? '')
                    ->first();

                if (!$exists) {
                    ScrapedScholarship::create([
                        'name'         => $item['name'],
                        'source'       => $item['source']      ?? '',
                        'type'         => $item['type']        ?? 'Government',
                        'benefits'     => $item['benefits']    ?? '',
                        'requirements' => $item['requirements'] ?? '',
                        'slots'        => is_numeric($item['slots'] ?? null) ? (int)$item['slots'] : null,
                        'end_date'     => !empty($item['end_date']) ? $item['end_date'] : null,
                        'link'         => $item['link']        ?? '',
                        'imported'     => false,
                    ]);
                    $saved++;
                }
            }

            $msg = "Sync complete. Found ".count($results)." scholarships, $saved new ones saved.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg, 'reload' => true]);
            }
            return redirect()->route('admin.scraper.index')->with('success', $msg);

        } catch (\Exception $e) {
            $msg = 'Sync failed: ' . $e->getMessage();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg], 500);
            }
            return redirect()->route('admin.scraper.index')->with('error', $msg);
        }
    }

    /**
     * Run a SINGLE source and save results.
     */
    public function runSource(Request $request, string $source)
    {
        try {
            $results = $this->scraper->scrapeSource($source);
            $saved   = 0;

            foreach ($results as $item) {
                // Skip items with no name
                if (empty($item['name'] ?? '')) continue;

                $exists = ScrapedScholarship::where('name', $item['name'])
                    ->where('source', $item['source'] ?? $source)
                    ->first();

                if (!$exists) {
                    ScrapedScholarship::create([
                        'name'         => $item['name'],
                        'source'       => $item['source']      ?? $source,
                        'type'         => $item['type']        ?? 'Government',
                        'benefits'     => $item['benefits']    ?? '',
                        'requirements' => $item['requirements'] ?? '',
                        'slots'        => is_numeric($item['slots'] ?? null) ? (int)$item['slots'] : null,
                        'end_date'     => !empty($item['end_date']) ? $item['end_date'] : null,
                        'link'         => $item['link']        ?? '',
                        'imported'     => false,
                    ]);
                    $saved++;
                }
            }

            $label = ucwords(str_replace(['-','_'], ' ', $source));
            $msg   = "$label synced. Found ".count($results)." scholarships, $saved new saved.";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg, 'reload' => true]);
            }
            return redirect()->route('admin.scraper.index')->with('success', $msg);

        } catch (\Exception $e) {
            $msg = 'Sync failed for '.$source.': '.$e->getMessage();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => $msg], 500);
            }
            return redirect()->route('admin.scraper.index')->with('error', $msg);
        }
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
            $scholarship = Scholarship::create([
                'name'         => $scraped->name,
                'type'         => $scraped->type         ?? 'Government',
                'benefits'     => $scraped->benefits     ?? '',
                'requirements' => $scraped->requirements ?? '',
                'slots'        => $scraped->slots        ?? null,
                'end_date'     => $scraped->end_date     ?? null,
                'source'       => $scraped->source       ?? '',
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
        return redirect()->route('admin.scraper.index')->with('success', $msg);
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
            'type'         => $scraped->type         ?? 'Government',
            'benefits'     => $scraped->benefits     ?? '',
            'requirements' => $scraped->requirements ?? '',
            'slots'        => $scraped->slots        ?? null,
            'end_date'     => $scraped->end_date     ?? null,
            'source'       => $scraped->source       ?? '',
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
        return redirect()->route('admin.scraper.index')->with('success', $msg);
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
