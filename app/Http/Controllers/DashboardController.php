<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\CounselingSession;
use App\Models\DisciplineCase;
use App\Models\Organization;
use App\Models\Scholarship;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students'      => Student::count(),
            'total_organizations' => Organization::count(),
            'total_scholars'      => Scholarship::withCount('grantees')->get()->sum('grantees_count'),
            'total_counseling'    => CounselingSession::whereMonth('created_at', now()->month)->count(),
            'active_cases'        => DisciplineCase::whereNotIn('status', ['Resolved', 'Dismissed'])->count(),
            'events_month'        => Event::whereMonth('event_date', now()->month)->count(),
        ];

        $recent_students  = Student::latest()->take(5)->get();
        $upcoming_events  = Event::where('event_date', '>=', now())
                                ->orderBy('event_date')
                                ->take(5)->get();

        return view('dashboard.index', compact('stats', 'recent_students', 'upcoming_events'));
    }
}
