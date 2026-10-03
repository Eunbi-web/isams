<?php
namespace App\Http\Controllers\Concerns;

use App\Models\AiReport;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Shared helpers for the AI narrative/comprehensive report pages:
 * academic-period defaults, PDF export (dompdf) and Word export
 * (MSO-wrapped HTML — opens natively in Microsoft Word).
 */
trait InteractsWithAiReports {
    /** Academic-year select options: current AY first, then the four before it. */
    protected function ayOptions(): array {
        $start = now()->month >= 6 ? (int)now()->format('Y') : (int)now()->format('Y') - 1;
        $opts = [];
        for ($i = 0; $i < 5; $i++) {
            $opts[] = ($start - $i).'-'.($start - $i + 1);
        }
        return $opts;
    }

    /** Best guess of the current semester from the calendar month. */
    protected function guessSemester(): string {
        $m = (int)now()->format('n');
        if ($m >= 8) return '1st Semester';
        if ($m >= 6) return 'Summer';
        return '2nd Semester';
    }

    protected function pdfResponse(AiReport $report) {
        $pdf = Pdf::loadView('ai-reports.pdf', ['report' => $report])->setPaper('a4', 'portrait');
        return $pdf->download('ai-report-'.str_replace(' ', '-', strtolower($report->academic_year)).'-'.now()->format('Ymd-His').'.pdf');
    }

    protected function wordResponse(AiReport $report) {
        $css = 'body{font-family:"Times New Roman",serif;font-size:11.5pt;line-height:1.45;}'
            .'h2{font-size:13.5pt;color:#1a3c6e;border-bottom:1.5px solid #1a3c6e;padding-bottom:3pt;margin:20pt 0 8pt;}'
            .'h3{font-size:12pt;color:#1a3c6e;margin:14pt 0 6pt;}'
            .'p{margin:0 0 9pt;text-align:justify;}'
            .'table{border-collapse:collapse;width:100%;margin:10pt 0;font-size:9.5pt;}'
            .'th{background:#e8eef7;border:1px solid #44618c;padding:4pt 6pt;text-align:left;font-size:9pt;text-transform:uppercase;}'
            .'td{border:1px solid #9db0cb;padding:4pt 6pt;}'
            .'ul,ol{margin:0 0 10pt 22pt;padding:0;}li{margin-bottom:4pt;}';
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'
            .'<head><meta charset="utf-8"><title>'.e($report->title).'</title>'
            .'<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml><![endif]-->'
            .'<style>'.$css.'</style></head><body>'
            .'<div style="text-align:center;border-bottom:2.5px solid #1a3c6e;padding-bottom:10pt;margin-bottom:14pt;">'
            .'<div style="font-size:9.5pt;letter-spacing:2px;color:#555;">SAINT COLUMBAN COLLEGE &middot; PAGADIAN CITY</div>'
            .'<div style="font-size:8.5pt;color:#777;">Integrated Student Affairs Management System &mdash; '.($report->portal === 'scholarship' ? 'Scholarship Office' : 'Student Affairs Office').'</div>'
            .'</div>'
            .$report->content_html
            .'</body></html>';

        return response($html)
            ->header('Content-Type', 'application/msword; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="ai-report-'.str_replace(' ', '-', strtolower($report->academic_year)).'-'.now()->format('Ymd-His').'.doc"');
    }
}
