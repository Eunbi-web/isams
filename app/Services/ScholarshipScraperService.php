<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ScholarshipScraperService
{
    protected array $sources = [
        'ched' => [
            'name'   => 'CHED Scholarship',
            'agency' => 'Commission on Higher Education',
            'url'    => 'https://ched.gov.ph/scholarships/',
            'type'   => 'Government',
        ],
        'dost' => [
            'name'   => 'DOST-SEI Scholarship',
            'agency' => 'Dept. of Science and Technology — SEI',
            'url'    => 'https://www.sei.dost.gov.ph/',
            'type'   => 'Government',
        ],
        'unifast' => [
            'name'   => 'UniFAST / TES',
            'agency' => 'Unified Financial Assistance System for Tertiary Education',
            'url'    => 'https://unifast.gov.ph/',
            'type'   => 'Government',
        ],
        'pvao' => [
            'name'   => 'PVAO Scholarship',
            'agency' => 'Philippine Veterans Affairs Office',
            'url'    => 'https://pvao.gov.ph/',
            'type'   => 'Government',
        ],
        'dswd' => [
            'name'   => 'DSWD Scholarship',
            'agency' => 'Dept. of Social Welfare and Development',
            'url'    => 'https://www.dswd.gov.ph/',
            'type'   => 'Government',
        ],
        'gsis' => [
            'name'   => 'GSIS Educational Loan',
            'agency' => 'Government Service Insurance System',
            'url'    => 'https://www.gsis.gov.ph/',
            'type'   => 'Government',
        ],
        'sm' => [
            'name'   => 'SM Foundation Scholarship',
            'agency' => 'SM Foundation, Inc.',
            'url'    => 'https://www.smfoundation.org/',
            'type'   => 'Private',
        ],
        'ayala' => [
            'name'   => 'Ayala Foundation Scholarship',
            'agency' => 'Ayala Foundation, Inc.',
            'url'    => 'https://www.ayalafoundation.org/',
            'type'   => 'Private',
        ],
        'jg-summit' => [
            'name'   => 'JG Summit Scholarship',
            'agency' => 'JG Summit Holdings, Inc.',
            'url'    => 'https://www.jgsummit.com.ph/',
            'type'   => 'Private',
        ],
        'metrobank' => [
            'name'   => 'Metrobank Foundation Scholarship',
            'agency' => 'Metrobank Foundation, Inc.',
            'url'    => 'https://www.metrobankfoundation.org/',
            'type'   => 'Private',
        ],
    ];

    /**
     * Scrape all sources.
     */
    public function scrapeAll(): array
    {
        $results = [];
        foreach (array_keys($this->sources) as $key) {
            try {
                $items   = $this->scrapeSource($key);
                $results = array_merge($results, $items);
            } catch (\Exception $e) {
                continue;
            }
        }
        return $results;
    }

    /**
     * Scrape a single source by key.
     */
    public function scrapeSource(string $sourceKey): array
    {
        if (!isset($this->sources[$sourceKey])) {
            throw new \Exception("Unknown source: $sourceKey");
        }

        $source = $this->sources[$sourceKey];

        // Try to fetch the page
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; ISAMS/1.0)'])
                ->get($source['url']);
            $html = $response->ok()
                ? substr(strip_tags($response->body()), 0, 2000)
                : '';
        } catch (\Exception $e) {
            $html = '';
        }

        // Use Groq to extract scholarship info
        $groqKey = config('services.groq.key', env('GROQ_API_KEY', ''));

        if ($groqKey) {
            try {
                $prompt = "You are a Philippine scholarship data extractor.\n\n"
                    . "Extract scholarship information from {$source['name']} ({$source['agency']}, {$source['url']}).\n"
                    . "PAGE CONTENT: " . ($html ?: 'Not available — use your knowledge about this program.') . "\n\n"
                    . "Return a JSON array of 1-3 scholarships. Each object must have exactly these keys:\n"
                    . "name, source, type, benefits, requirements, slots (integer or null), end_date (YYYY-MM-DD or null), link.\n"
                    . "Return ONLY a valid JSON array. No markdown, no explanation, no code blocks.";

                $res = Http::timeout(20)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $groqKey,
                        'Content-Type'  => 'application/json',
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model'       => 'llama-3.1-8b-instant',
                        'messages'    => [['role' => 'user', 'content' => $prompt]],
                        'max_tokens'  => 500,
                        'temperature' => 0.3,
                    ]);

                if ($res->ok()) {
                    $text   = $res->json('choices.0.message.content', '');
                    $text   = trim(preg_replace('/```json|```/', '', $text));
                    $parsed = json_decode($text, true);
                    if (is_array($parsed) && count($parsed) > 0) {
                        // Filter out any items missing required 'name' key
                        $parsed = array_filter($parsed, function($item) {
                            return !empty($item['name'] ?? '');
                        });
                        if (!empty($parsed)) {
                            return array_values($parsed);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fall through to static fallback
            }
        }

        // Static fallback
        return [[
            'name'         => $source['name'],
            'source'       => $source['agency'],
            'type'         => $source['type'],
            'benefits'     => 'Full tuition, stipend, and book allowance. Visit ' . $source['url'] . ' for details.',
            'requirements' => 'Filipino citizen. Good academic standing (GWA 1.75 or better). Financial need may apply.',
            'slots'        => null,
            'end_date'     => null,
            'link'         => $source['url'],
        ]];
    }

    public function getSources(): array
    {
        return $this->sources;
    }
}
