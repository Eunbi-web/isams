<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Scholarship, ScholarshipApplication, Student};
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller {
    public function index()  { return view('admin.reports.index'); }

    public function download(string $type)
    {
        [$title, $headings, $rows] = match ($type) {
            'applications' => $this->applicationsReport(),
            'eligible'     => $this->eligibleReport(),
            'scholarships' => $this->scholarshipsReport(),
            'students'     => $this->studentsReport(),
            default        => abort(404),
        };

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'title'    => $title,
            'headings' => $headings,
            'rows'     => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('isams-'.str_replace(' ', '-', strtolower($title)).'-'.now()->format('Y-m-d').'.pdf');
    }

    private function applicationsReport(): array
    {
        $apps = ScholarshipApplication::with(['student','scholarship'])
            ->orderByDesc('created_at')
            ->limit(1000)
            ->get();

        $rows = $apps->map(fn ($a) => [
            $a->student?->student_id ?? '—',
            $a->student?->full_name ?? '—',
            $a->scholarship?->name ?? '—',
            number_format((float) ($a->gwa ?? 0), 2),
            ($a->ai_score ?? 0).'%',
            $a->ai_eligibility ?? '—',
            $a->status ?? '—',
            optional($a->created_at)->format('M d, Y') ?? '—',
        ])->all();

        return ['Scholarship Applications Report',
            ['EDP No.', 'Student Name', 'Scholarship', 'GWA', 'AI Score', 'Eligibility', 'Status', 'Date Applied'],
            $rows];
    }

    private function eligibleReport(): array
    {
        $apps = ScholarshipApplication::with(['student','scholarship'])
            ->where('ai_eligibility', 'Eligible')
            ->orderByDesc('ai_score')
            ->limit(1000)
            ->get();

        $rows = $apps->map(fn ($a) => [
            $a->student?->student_id ?? '—',
            $a->student?->full_name ?? '—',
            $a->scholarship?->name ?? '—',
            number_format((float) ($a->gwa ?? 0), 2),
            ($a->ai_score ?? 0).'%',
            $a->status ?? '—',
        ])->all();

        return ['AI-Eligible Applicants Report',
            ['EDP No.', 'Student Name', 'Scholarship', 'GWA', 'AI Score', 'Status'],
            $rows];
    }

    private function scholarshipsReport(): array
    {
        $rows = Scholarship::withCount(['applications', 'applications as approved_count' => fn ($q) => $q->where('status', 'Approved')])
            ->orderBy('name')
            ->get()
            ->map(fn ($s) => [
                $s->name,
                $s->type ?? '—',
                $s->benefits ?? '—',
                (string) ($s->slots ?? '—'),
                (string) ($s->slots_remaining ?? 0),
                (string) $s->applications_count,
                (string) $s->approved_count,
                optional($s->end_date)->format('M d, Y') ?? '—',
                $s->status ?? '—',
            ])->all();

        return ['Scholarship Programs Report',
            ['Program', 'Type', 'Benefit', 'Slots', 'Slots Left', 'Applications', 'Approved', 'Deadline', 'Status'],
            $rows];
    }

    private function studentsReport(): array
    {
        $rows = Student::orderBy('last_name')
            ->limit(1000)
            ->get()
            ->map(fn ($s) => [
                $s->student_id ?? '—',
                $s->full_name ?? '—',
                $s->course ?? '—',
                $s->year_level ?? '—',
                number_format((float) ($s->gwa ?? 0), 2),
                $s->enrollment_type ?? '—',
                $s->status ?? '—',
            ])->all();

        return ['Student Records Report',
            ['EDP No.', 'Student Name', 'Course', 'Year Level', 'GWA', 'Enrollment', 'Status'],
            $rows];
    }
}
