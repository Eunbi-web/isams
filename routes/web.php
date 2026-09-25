<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\{DashboardController as AdminDash, ScholarshipController as AdminScholarship, ApplicationController as AdminApp, AiController as AdminAi, StudentController as AdminStudent, ReportController as AdminReport, AnnouncementController as AdminAnn, NotificationController as AdminNotif, SettingsController as AdminSettings, ScraperController as AdminScraper};
use App\Http\Controllers\Counselor\{DashboardController as CounselorDash, CounselingController as CounselorCounseling, AnnouncementController as CounselorAnn, DisciplineController as CounselorDiscipline, NotificationController as CounselorNotif, SettingsController as CounselorSettings};
use App\Http\Controllers\Student\{DashboardController as StudentDash, ScholarshipController as StudentScholarship, ApplicationController as StudentApp, EligibilityController as StudentEligibility, CounselingController as StudentCounseling, AnnouncementController as StudentAnn, NotificationController as StudentNotif, ProfileController as StudentProfile};
use App\Http\Controllers\SuperAdmin\{DashboardController as SADash, UserController as SAUser, LogController as SALog, MonitoringController as SAMonitor};
use App\Http\Controllers\Scholarship\DashboardController as ScholDash;
use App\Http\Controllers\Scholarship\ProgramController as ScholProgram;
use App\Http\Controllers\Scholarship\ScraperController as ScholScraper;
use App\Http\Controllers\Scholarship\ApplicationController as ScholApp;
use App\Http\Controllers\Scholarship\AiController as ScholAi;
use App\Http\Controllers\Scholarship\StudentController as ScholStudent;
use App\Http\Controllers\Scholarship\ReportController as ScholReport;
use App\Http\Controllers\Scholarship\AnnouncementController as ScholAnn;
use App\Http\Controllers\Scholarship\NotificationController as ScholNotif;
use App\Http\Controllers\Scholarship\SettingsController as ScholSettings;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class,'showLogin']);
    Route::get('/login', [AuthController::class,'showLogin'])->name('login');
    Route::post('/login', [AuthController::class,'login']);
    Route::get('/forgot-password', [AuthController::class,'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class,'sendResetLink'])->name('password.email');
});
Route::post('/logout', [AuthController::class,'logout'])->name('logout');

// ISAAC Chatbot (available on all portals, auth required)
Route::post('/chatbot/ask', [App\Http\Controllers\ChatbotController::class, 'ask'])->middleware('auth')->name('chatbot.ask');

// Theme toggle (AJAX): system/light/dark
Route::post('/settings/theme', function(\Illuminate\Http\Request $r) {
    $user = auth()->user();
    abort_if(!$user, 401);

    $data = $r->validate([
        'theme' => 'required|in:system,light,dark',
    ]);

    $user->theme = $data['theme'];
    $user->save();

    return response()->json(['ok' => true, 'theme' => $user->theme]);
})->middleware(['auth'])->name('settings.theme.update');


// SUPERADMIN
Route::middleware(['auth','role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('dashboard', [SADash::class,'index'])->name('dashboard');
    Route::get('monitoring', [SAMonitor::class,'index'])->name('monitoring');
    Route::get('logs', [SALog::class,'index'])->name('logs');
    Route::get('users', [SAUser::class,'index'])->name('users.index');
    Route::get('users/create', [SAUser::class,'create'])->name('users.create');
    Route::post('users', [SAUser::class,'store'])->name('users.store');
    Route::get('users/{user}/edit', [SAUser::class,'edit'])->name('users.edit');
    Route::match(['put','patch'], 'users/{user}', [SAUser::class,'update'])->name('users.update');
    Route::patch('users/{user}/toggle', [SAUser::class,'toggleActive'])->name('users.toggle');
    Route::delete('users/{user}', [SAUser::class,'destroy'])->name('users.destroy');
    Route::get('settings', fn()=>view('superadmin.settings.index'))->name('settings');
    Route::post('settings', function(\Illuminate\Http\Request $r) {
        $user=auth()->user(); $r->validate(['name'=>'required','email'=>'required|email|unique:users_all,email,'.$user->id]);
        $data=$r->only('name','email');
        if ($r->filled('current_password')&&\Hash::check($r->current_password,$user->password)&&$r->filled('password')){ $data['password']=\Hash::make($r->password); }
        $user->update($data); return back()->with('success','Settings saved!');
    })->name('settings.update');
});

// ADMIN
Route::middleware(['auth','role:admin,officer'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDash::class,'index'])->name('dashboard');

    // Scraper routes MUST come before Route::resource('scholarships') to avoid
    // the {scholarship} wildcard swallowing the "scraper" segment as an ID
    Route::get('scholarships/scraper',                   [AdminScraper::class,'index'])->name('scraper.index');
    Route::post('scholarships/scraper/run',              [AdminScraper::class,'run'])->name('scraper.run');
    Route::post('scholarships/scraper/source/{source}',  [AdminScraper::class,'runSource'])->name('scraper.run-source');
    Route::post('scholarships/scraper/import-all',       [AdminScraper::class,'importAll'])->name('scraper.import-all');
    Route::post('scholarships/scraper/{scraped}/import', [AdminScraper::class,'import'])->name('scraper.import');
    Route::delete('scholarships/scraper/{scraped}',      [AdminScraper::class,'destroySynced'])->name('scraper.destroy');

    // Scraper route aliases — blade files use admin.scraper.* names
    Route::get('scholarships/scraper',                   [AdminScraper::class,'index'])->name('scholarships.scraper.index');
    Route::post('scholarships/scraper/import-all',       [AdminScraper::class,'importAll'])->name('scholarships.scraper.import-all');

    // Additional aliases for layouts/blades that use old admin.scraper.* naming
    Route::get('scraper',                                [AdminScraper::class,'index'])->name('scraper.index');
    Route::post('scraper/run',                           [AdminScraper::class,'run'])->name('scraper.run');
    Route::post('scraper/run-source',                    [AdminScraper::class,'runSource'])->name('scraper.run-source');
    Route::post('scraper/import-all',                    [AdminScraper::class,'importAll'])->name('scraper.import-all');
    Route::post('scraper/{scraped}/import',              [AdminScraper::class,'import'])->name('scraper.import');
    Route::post('scraper/{scraped}/dismiss',             [AdminScraper::class,'destroySynced'])->name('scraper.dismiss');
      Route::delete('scraper/{scraped}',                   [AdminScraper::class,'destroySynced'])->name('scraper.destroy');

    Route::resource('scholarships', AdminScholarship::class);

    // Bulk routes MUST come before resource to avoid {application} wildcard conflicts
    Route::post('applications/bulk-approve', [AdminApp::class,'bulkApprove'])->name('applications.bulk-approve');
    Route::post('applications/bulk-reject',  [AdminApp::class,'bulkReject'])->name('applications.bulk-reject');

    Route::resource('applications', AdminApp::class);

    // ADDED: explicit approve/reject PATCH routes for AJAX buttons
    Route::patch('applications/{application}/approve', [AdminApp::class,'approve'])->name('applications.approve');
    Route::patch('applications/{application}/reject',  [AdminApp::class,'reject'])->name('applications.reject');
    // ADDED: status-change route used by the applications show page buttons
    Route::patch('applications/{application}/status', [AdminApp::class,'updateStatus'])->name('applications.updateStatus');

    Route::get('ai', [AdminAi::class,'index'])->name('ai.index');
    // Aliases for old blade references — redirect to ai.index
    Route::post('ai/run',                      [AdminAi::class,'index'])->name('ai.run');
    Route::get('ai/results',                   [AdminAi::class,'index'])->name('ai.results');
    // CHANGED: runSingle now points to the real AJAX evaluation handler
    Route::post('ai/{application}/run-single', [AdminAi::class,'runSingle'])->name('ai.runSingle');
    // NEW (CHANGE 3): quick status change from the AI Filter table
    Route::patch('ai/{application}/status', [AdminAi::class,'updateStatus'])->name('ai.updateStatus');

    Route::get('students/export', [AdminStudent::class,'export'])->name('students.export');
    Route::get('students/import', [AdminStudent::class,'importForm'])->name('students.import-form');
    Route::post('students/import', [AdminStudent::class,'import'])->name('students.import');

    // Admin "Add Student" lookup/autofill (EDP/student_id or name from imported list)
    Route::post('students/lookup', [AdminStudent::class,'lookupFromImported'])->name('students.lookup');

    Route::resource('students', AdminStudent::class);



    Route::get('reports', [AdminReport::class,'index'])->name('reports.index');
    Route::get('reports/download/{type}', [AdminReport::class,'download'])->name('reports.download');

    // Announcements (Admin) — Counseling remains exclusively in the Counselor Portal
    Route::resource('announcements', AdminAnn::class);

    Route::get('notifications', [AdminNotif::class,'index'])->name('notifications.index');
    Route::patch('notifications/{id}', [AdminNotif::class,'markRead'])->name('notifications.read');

    Route::get('settings', [AdminSettings::class,'index'])->name('settings.index');
    Route::post('settings', [AdminSettings::class,'update'])->name('settings.update');

    // Notification dropdown routes
    Route::get('notifications/dropdown',  [AdminNotif::class,'dropdown'])->name('notifications.dropdown');
    Route::post('notifications/mark-all', [AdminNotif::class,'markAllRead'])->name('notifications.mark-all');

    // Discipline Records (Admin)
    Route::get('discipline', [\App\Http\Controllers\Admin\DisciplineAdminController::class,'index'])->name('discipline.index');
    Route::get('discipline/create', [\App\Http\Controllers\Admin\DisciplineAdminController::class,'create'])->name('discipline.create');
    Route::post('discipline', [\App\Http\Controllers\Admin\DisciplineAdminController::class,'store'])->name('discipline.store');
    Route::post('discipline/lookup-edp', [\App\Http\Controllers\Admin\DisciplineAdminController::class,'lookupEdp'])->name('discipline.lookup-edp');
});

// SCHOLARSHIP — Scholarship Management Portal
Route::middleware(['auth','role:scholarship'])->prefix('scholarship')->name('scholarship.')->group(function () {
    Route::get('dashboard', [ScholDash::class,'index'])->name('dashboard');

    // Scraper routes MUST come before Route::resource('programs') to avoid
    // the {program} wildcard swallowing the "scraper" segment as an ID
    Route::get('programs/scraper',                      [ScholScraper::class,'index'])->name('scraper.index');
    Route::post('programs/scraper/run',                 [ScholScraper::class,'run'])->name('scraper.run');
    Route::post('programs/scraper/source/{source}',     [ScholScraper::class,'runSource'])->name('scraper.run-source');
    Route::post('programs/scraper/import-all',          [ScholScraper::class,'importAll'])->name('scraper.import-all');
    Route::post('programs/scraper/{scraped}/import',    [ScholScraper::class,'import'])->name('scraper.import');
    Route::post('programs/scraper/{scraped}/dismiss',   [ScholScraper::class,'destroySynced'])->name('scraper.dismiss');
    Route::delete('programs/scraper/{scraped}',         [ScholScraper::class,'destroySynced'])->name('scraper.destroy');

    Route::resource('programs', ScholProgram::class);

    // Bulk routes MUST come before resource to avoid {application} wildcard conflicts
    Route::post('applications/bulk-approve', [ScholApp::class,'bulkApprove'])->name('applications.bulk-approve');
    Route::post('applications/bulk-reject',  [ScholApp::class,'bulkReject'])->name('applications.bulk-reject');

    Route::resource('applications', ScholApp::class);

    // Explicit approve/reject PATCH routes for AJAX buttons
    Route::patch('applications/{application}/approve', [ScholApp::class,'approve'])->name('applications.approve');
    Route::patch('applications/{application}/reject',  [ScholApp::class,'reject'])->name('applications.reject');
    Route::patch('applications/{application}/status',  [ScholApp::class,'updateStatus'])->name('applications.updateStatus');

    Route::get('ai', [ScholAi::class,'index'])->name('ai.index');
    Route::post('ai/run',                      [ScholAi::class,'index'])->name('ai.run');
    Route::get('ai/results',                   [ScholAi::class,'index'])->name('ai.results');
    Route::post('ai/{application}/run-single', [ScholAi::class,'runSingle'])->name('ai.runSingle');
    Route::patch('ai/{application}/status',    [ScholAi::class,'updateStatus'])->name('ai.updateStatus');

    Route::get('students/export', [ScholStudent::class,'export'])->name('students.export');
    Route::get('students/import', [ScholStudent::class,'importForm'])->name('students.import-form');
    Route::post('students/import', [ScholStudent::class,'import'])->name('students.import');
    Route::post('students/lookup', [ScholStudent::class,'lookupFromImported'])->name('students.lookup');

    Route::resource('students', ScholStudent::class);

    Route::get('reports', [ScholReport::class,'index'])->name('reports.index');
    Route::get('reports/download/{type}', [ScholReport::class,'download'])->name('reports.download');

    Route::resource('announcements', ScholAnn::class);

    Route::get('notifications', [ScholNotif::class,'index'])->name('notifications.index');
    Route::patch('notifications/{id}', [ScholNotif::class,'markRead'])->name('notifications.read');

    Route::get('settings', [ScholSettings::class,'index'])->name('settings.index');
    Route::post('settings', [ScholSettings::class,'update'])->name('settings.update');

    // Notification dropdown routes
    Route::get('notifications/dropdown',  [ScholNotif::class,'dropdown'])->name('notifications.dropdown');
    Route::post('notifications/mark-all', [ScholNotif::class,'markAllRead'])->name('notifications.mark-all');
});

// COUNSELOR — Guidance Counseling Portal
Route::middleware(['auth','role:counselor'])->prefix('counselor')->name('counselor.')->group(function () {
    Route::get('dashboard', [CounselorDash::class,'index'])->name('dashboard');

    Route::get('counseling', [CounselorCounseling::class,'index'])->name('counseling.index');
    Route::post('counseling/{counseling}/schedule', [CounselorCounseling::class,'schedule'])->name('counseling.schedule');
    Route::post('counseling/{counseling}/complete', [CounselorCounseling::class,'complete'])->name('counseling.complete');
    Route::delete('counseling/{counseling}', [CounselorCounseling::class,'destroy'])->name('counseling.destroy');

    Route::get('announcements', [CounselorAnn::class,'index'])->name('announcements');

    Route::get('discipline', [CounselorDiscipline::class,'index'])->name('discipline.index');
    Route::post('discipline', [CounselorDiscipline::class,'store'])->name('discipline.store');
    Route::put('discipline/{record}', [CounselorDiscipline::class,'update'])->name('discipline.update');
    Route::delete('discipline/{record}', [CounselorDiscipline::class,'destroy'])->name('discipline.destroy');
    Route::patch('discipline/{record}/status', [CounselorDiscipline::class,'updateStatus'])->name('discipline.status');

    Route::get('notifications', [CounselorNotif::class,'index'])->name('notifications');
    Route::get('notifications/dropdown', [CounselorNotif::class,'dropdown'])->name('notifications.dropdown');
    Route::post('notifications/mark-all', [CounselorNotif::class,'markAllRead'])->name('notifications.mark-all');
    Route::patch('notifications/{id}', [CounselorNotif::class,'markRead'])->name('notifications.read');

    Route::get('settings', [CounselorSettings::class,'index'])->name('settings');
    Route::post('settings', [CounselorSettings::class,'update'])->name('settings.update');
});

// STUDENT
Route::middleware(['auth','role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('dashboard', [StudentDash::class,'index'])->name('dashboard');
    Route::get('scholarships', [StudentScholarship::class,'index'])->name('scholarships');
    Route::get('scholarships/{id}', [StudentScholarship::class,'show'])->name('scholarships.show');
    Route::get('applications', [StudentApp::class,'index'])->name('applications');
    Route::get('apply/{scholarship}', [StudentApp::class,'create'])->name('apply');
    Route::post('applications', [StudentApp::class,'store'])->name('applications.store');
    Route::get('applications/{id}', [StudentApp::class,'show'])->name('applications.show');
    Route::get('eligibility', [StudentEligibility::class,'index'])->name('eligibility');
    Route::post('eligibility/compute', [StudentEligibility::class,'compute'])->name('eligibility.compute');
    // Notification dropdown routes
    Route::get('notifications/dropdown',  [StudentNotif::class,'dropdown'])->name('notifications.dropdown');
    Route::post('notifications/mark-all', [StudentNotif::class,'markAllRead'])->name('notifications.mark-all');

    Route::get('counseling', [StudentCounseling::class,'index'])->name('counseling.index');
    Route::post('counseling', [StudentCounseling::class,'store'])->name('counseling.store');
    // ADDED: student can cancel their counseling request
    Route::delete('counseling/{session}', [StudentCounseling::class,'destroy'])->name('counseling.destroy');
    Route::get('announcements', [StudentAnn::class,'index'])->name('announcements');
    Route::get('announcements/{id}', [StudentAnn::class,'show'])->name('announcements.show');
    Route::get('notifications', [StudentNotif::class,'index'])->name('notifications');
    Route::patch('notifications/{id}', [StudentNotif::class,'markRead'])->name('notifications.read');
    Route::get('profile', [StudentProfile::class,'index'])->name('profile');
    Route::post('profile', [StudentProfile::class,'update'])->name('profile.update');
});
