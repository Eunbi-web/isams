<?php
namespace App\Http\Controllers\Scholarship;
use App\Http\Controllers\Controller;
use App\Models\Scholar;
use Illuminate\Http\Request;

class MonitoringController extends Controller {
    public function index(Request $r) {
        $scholars = Scholar::query()
            ->when($r->search, function($q,$s){
                $q->where(function($qq) use ($s){
                    $qq->where('first_name','like',"%$s%")
                       ->orWhere('last_name','like',"%$s%")
                       ->orWhere('student_number','like',"%$s%");
                });
            })
            ->when($r->status, fn($q,$v)=>$q->where('status',$v))
            ->when($r->enrollment, fn($q,$v)=>$q->where('enrollment_status',$v))
            ->when($r->graduation, fn($q,$v)=>$q->where('graduation_status',$v))
            ->orderByRaw("CASE WHEN status = 'Active' THEN 0 ELSE 1 END")
            ->orderBy('last_name')
            ->paginate(20)->withQueryString();

        $stats = [
            'total'        => Scholar::count(),
            'maintaining'  => Scholar::where('status','Active')->where('enrollment_status','Enrolled')->where('requirements_met',true)->count(),
            'graduated'    => Scholar::where('graduation_status','Graduated')->count(),
            'onTrack'      => Scholar::where('graduation_status','On Track')->where('status','Active')->count(),
            'atRisk'       => Scholar::where('graduation_status','At Risk')->where('status','Active')->count(),
            'notEnrolled'  => Scholar::where('enrollment_status','Not Enrolled')->where('status','Active')->count(),
        ];

        return view('scholarship.monitoring.index', compact('scholars','stats'));
    }

    /** AJAX update of a scholar's performance monitoring record. */
    public function update(Request $r, Scholar $scholar) {
        $data = $r->validate([
            'current_gwa'       => 'nullable|numeric|min:1|max:5',
            'enrollment_status' => 'required|in:Enrolled,Not Enrolled',
            'requirements_met'  => 'nullable|boolean',
            'graduation_status' => 'required|in:On Track,Graduated,At Risk',
            'remarks'           => 'nullable|string|max:1000',
        ]);
        $data['requirements_met'] = $r->boolean('requirements_met');
        $data['last_monitored_at'] = now();
        $scholar->update($data);

        return response()->json([
            'message' => 'Monitoring record updated for '.$scholar->full_name.'.',
            'reload'  => true,
        ]);
    }
}
