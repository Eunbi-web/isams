<?php
namespace App\Console\Commands;
use App\Services\ScholarshipScraperService;
use App\Models\ScrapedScholarship;
use Illuminate\Console\Command;
class ScrapeScholarships extends Command {
    protected $signature   = 'scamp:scrape {--auto-import : Auto import eligible scholarships}';
    protected $description = 'AI-powered scholarship scraper — fetches and updates scholarships from live websites';
    public function handle(ScholarshipScraperService $scraper) {
        $this->info('🤖 SCAMP AI Scholarship Scraper starting...');
        $this->newLine();
        $results = $scraper->scrapeAll();
        $totalNew=$totalUpdated=$totalFound=0;
        foreach ($results as $r) {
            $icon = $r['status']==='success'?'✅':($r['status']==='unreachable'?'⚠️':'❌');
            $this->line("  {$icon} {$r['source']}: found={$r['found']} new={$r['new']} updated={$r['updated']}");
            $totalNew     += $r['new'];
            $totalUpdated += $r['updated'];
            $totalFound   += $r['found'] ?? 0;
        }
        $this->newLine();
        $this->info("📊 Total found: {$totalFound} | New: {$totalNew} | Updated: {$totalUpdated}");
        if ($this->option('auto-import')) {
            $pending = ScrapedScholarship::where('imported',false)->where('ai_confidence','>=',75)->get();
            $imported = 0;
            foreach ($pending as $s) { $scraper->autoImport($s); $imported++; }
            $this->info("✅ Auto-imported {$imported} high-confidence scholarships.");
        }
        $this->newLine();
        $this->info('🎓 SCAMP AI Scraper complete!');
        return Command::SUCCESS;
    }
}
