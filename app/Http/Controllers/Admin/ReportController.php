<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
class ReportController extends Controller {
    public function index()  { return view('admin.reports.index'); }
    public function download(string $type) { return back()->with('info',"Report '$type' would be downloaded. Install barryvdh/laravel-dompdf for PDF export."); }
}
