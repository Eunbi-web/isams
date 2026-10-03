<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\CounselingSetting;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
class CounselingController extends Controller {
    public function index() {
        $student  = auth()->user()->student;
        $sessions = $student ? CounselingSession::where('student_id',$student->id)->latest()->get() : collect();
        $queuePos = CounselingSession::where('status','In Queue')->count();
        $setting  = CounselingSetting::current();
        return view('student.counseling.index',compact('sessions','queuePos','setting'));
    }

    /** Availability of every day in a month (for the booking calendar). */
    public function calendar(Request $r) {
        $month = $r->query('month', now()->format('Y-m'));
        abort_unless(preg_match('/^\d{4}-\d{2}$/', $month), 422);

        $setting  = CounselingSetting::current();
        $capacity = max(1, (int)$setting->slot_capacity);
        $slotCount = count(CounselingSetting::SLOTS);

        $start = CarbonImmutable::parse($month.'-01')->startOfMonth();
        $end   = $start->endOfMonth();
        $today = CarbonImmutable::today();

        $booked = CounselingSession::whereBetween('session_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', ['In Queue','Scheduled'])
            ->selectRaw('session_date, count(*) as total')
            ->groupBy('session_date')
            ->pluck('total','session_date');

        $days = [];
        for ($d = $start; $d <= $end; $d = $d->addDay()) {
            $key = $d->toDateString();
            if ($d->lt($today))                       { $days[$key] = ['state'=>'past']; }
            elseif ($d->isSunday())                   { $days[$key] = ['state'=>'closed']; }
            else {
                $used = (int)($booked[$key] ?? 0);
                $days[$key] = [
                    'state'     => $used >= $capacity * $slotCount ? 'full' : 'open',
                    'remaining' => max(0, $capacity * $slotCount - $used),
                ];
            }
        }
        return response()->json(['month'=>$month,'capacity'=>$capacity,'slots'=>count(CounselingSetting::SLOTS),'days'=>$days]);
    }

    /** Availability of every time slot on one date. */
    public function slots(Request $r) {
        $r->validate(['date'=>'required|date|after_or_equal:today']);
        abort_if(CarbonImmutable::parse($r->query('date'))->isSunday(), 422);

        $setting  = CounselingSetting::current();
        $capacity = max(1, (int)$setting->slot_capacity);

        $booked = CounselingSession::where('session_date', $r->query('date'))
            ->whereIn('status', ['In Queue','Scheduled'])
            ->selectRaw('session_time, count(*) as total')
            ->groupBy('session_time')
            ->pluck('total','session_time');

        $slotList = [];
        foreach (CounselingSetting::SLOTS as $slot) {
            $used = (int)($booked[$slot] ?? 0);
            $slotList[] = ['time'=>$slot,'booked'=>$used,'capacity'=>$capacity,'full'=>$used >= $capacity];
        }
        return response()->json(['date'=>$r->query('date'),'slots'=>$slotList]);
    }

    public function store(Request $request) {
        $slotMode = CounselingSetting::schedulingMode() === 'slots';

        $rules = [
            'concern_type'   => 'required|string',
            'priority'       => 'required|string',
            'concern_detail' => 'nullable|string',
        ];
        if ($slotMode) {
            $rules['session_date'] = 'required|date|after:today';
            $rules['session_time'] = 'required|string|in:'.implode(',', CounselingSetting::SLOTS);
        } else {
            $rules['preferred_date'] = 'nullable|date|after:today';
            $rules['preferred_time'] = 'nullable|string';
        }
        $request->validate($rules);

        $student = auth()->user()->student;
        if (!$student) return back()->with('error','Student profile not found.');

        if ($slotMode) {
            $setting  = CounselingSetting::current();
            $capacity = max(1, (int)$setting->slot_capacity);
            $booked   = CounselingSession::where('session_date',$request->session_date)
                ->where('session_time',$request->session_time)
                ->whereIn('status',['In Queue','Scheduled'])->count();
            if ($booked >= $capacity) {
                return back()->with('error','That time slot was just fully booked. Please pick another slot.');
            }
            CounselingSession::create([
                'student_id'   => $student->id,
                'concern_type' => $request->concern_type,
                'concern_detail'=> $request->concern_detail,
                'priority'     => $request->priority,
                'session_date' => $request->session_date,
                'session_time' => $request->session_time,
                'venue'        => 'Guidance Office',
                'status'       => 'Scheduled',
            ]);
            return back()->with('success','Your session is booked for '.\Carbon\Carbon::parse($request->session_date)->format('M d, Y').' at '.$request->session_time.'.');
        }

        $queuePos = CounselingSession::where('status','In Queue')->count() + 1;
        CounselingSession::create(['student_id'=>$student->id,'concern_type'=>$request->concern_type,'concern_detail'=>$request->concern_detail,'priority'=>$request->priority,'preferred_date'=>$request->preferred_date,'preferred_time'=>$request->preferred_time,'queue_position'=>$queuePos,'status'=>'In Queue']);
        return back()->with('success',"Request submitted! You are #{$queuePos} in the queue. A counselor will be assigned shortly.");
    }
}
