<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\CounselingSession;
use App\Models\DisciplineCase;
use App\Models\Organization;
use App\Models\Scholarship;
use App\Models\Event;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'students'      => Student::count(),
            'organizations' => Organization::count(),
            'scholars'      => 892,
            'counseling'    => CounselingSession::count(),
            'discipline'    => DisciplineCase::count(),
            'events'        => Event::count(),
        ];

        $enrollmentByCourse = Student::selectRaw('course, COUNT(*) as total')
            ->groupBy('course')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        $counselingByType = CounselingSession::selectRaw('concern_type, COUNT(*) as total')
            ->groupBy('concern_type')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        return view('reports.index', compact('stats', 'enrollmentByCourse', 'counselingByType'));
    }

    public function students()
    {
        $students = Student::orderBy('last_name')->get();
        return view('reports.students', compact('students'));
    }

    public function counseling()
    {
        $sessions = CounselingSession::with(['student', 'counselor'])->latest()->get();
        return view('reports.counseling', compact('sessions'));
    }

    public function discipline()
    {
        $cases = DisciplineCase::with('student')->latest()->get();
        return view('reports.discipline', compact('cases'));
    }

    public function scholarships()
    {
        $scholarships = Scholarship::withCount('grantees')->get();
        return view('reports.scholarships', compact('scholarships'));
    }

    public function organizations()
    {
        $organizations = Organization::withCount('members')->get();
        return view('reports.organizations', compact('organizations'));
    }

    public function events()
    {
        $events = Event::orderBy('event_date', 'desc')->get();
        return view('reports.events', compact('events'));
    }

    public function download(string $type)
    {
        // In production: integrate barryvdh/laravel-dompdf or maatwebsite/excel
        return back()->with('info', "Download for '$type' report would be generated here.");
    }
}
