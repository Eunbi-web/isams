<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use App\Models\DisciplineRecord;
use Illuminate\Http\Request;

class DisciplineController extends Controller {
    public function index(Request $r) {
        $records = DisciplineRecord::query()
            ->when($r->search, function($q,$s){
                $q->where('edp_number','like',"%$s%")->orWhere('student_name','like',"%$s%");
            })
            ->when($r->category,   fn($q,$v)=>$q->where('offense_category',$v))
            ->when($r->department, fn($q,$v)=>$q->where('department',$v))
            ->when($r->status,     fn($q,$v)=>$q->where('status',$v))
            ->latest()
            ->paginate(20);

        $stats = [
            'total'  => DisciplineRecord::count(),
            'major'  => DisciplineRecord::where('offense_category','Major')->count(),
            'minor'  => DisciplineRecord::where('offense_category','Minor')->count(),
            'open'   => DisciplineRecord::where('status','Open')->count(),
        ];

        return view('counselor.discipline.index', compact('records','stats'));
    }

    public function store(Request $r) {
        $data = $r->validate([
            'edp_number'       => 'required|string|max:20',
            'student_name'     => 'required|string|max:255',
            'department'       => 'nullable|string|max:100',
            'offense_category' => 'required|in:Major,Minor',
            'description'      => 'required|string',
            'incident_date'    => 'required|date',
            'guardian_name'    => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:20',
        ]);
        DisciplineRecord::create($data + ['created_by'=>auth()->id()]);

        if ($r->wantsJson() || $r->ajax()) {
            return response()->json(['message'=>'Discipline record added successfully.','reload'=>true]);
        }
        return redirect()->route('counselor.discipline.index')->with('success','Discipline record added successfully.');
    }

    public function update(Request $r, DisciplineRecord $record) {
        $data = $r->validate([
            'edp_number'       => 'required|string|max:20',
            'student_name'     => 'required|string|max:255',
            'department'       => 'nullable|string|max:100',
            'offense_category' => 'required|in:Major,Minor',
            'description'      => 'required|string',
            'incident_date'    => 'required|date',
            'guardian_name'    => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:20',
        ]);
        $record->update($data);

        if ($r->wantsJson() || $r->ajax()) {
            return response()->json(['message'=>'Discipline record updated successfully.','reload'=>true]);
        }
        return redirect()->route('counselor.discipline.index')->with('success','Discipline record updated successfully.');
    }

    public function destroy(Request $r, DisciplineRecord $record) {
        $record->delete();
        if ($r->wantsJson() || $r->ajax()) {
            return response()->json(['message'=>'Discipline record deleted.']);
        }
        return redirect()->route('counselor.discipline.index')->with('success','Discipline record deleted.');
    }

    public function updateStatus(Request $r, DisciplineRecord $record) {
        $data = $r->validate(['status'=>'required|in:Open,Under Review,Closed']);
        $record->update(['status'=>$data['status']]);
        return response()->json(['message'=>'Status updated.']);
    }
}
