@extends('student.layouts.app')
@section('title','Eligibility Test')
@section('page-title','Eligibility Test')
@section('page-sub','Fill in your scholarship data, run the test, and see which scholarships you qualify for')
@section('content')

@php
$hasProfile = $profile !== null;
$gwa        = (float)($profile?->gwa ?? 0);
$isRegular  = strtolower($profile?->enrollment_type ?? '') === 'regular';
$bracket    = $profile?->income_bracket ?? '';
$bannerClass= $overallEligibility==='Eligible'?'eligible':($overallEligibility==='For Review'?'review':'not');
$bannerIcon = $overallEligibility==='Eligible'?'check-circle':($overallEligibility==='For Review'?'exclamation-circle':'times-circle');

// Sort scholarships by score
$mapped = [];
foreach($scholarships as $s){
    $d = $eligibilityMap[$s->id] ?? [];
    $mapped[] = [
        'id'          => $s->id,
        'name'        => $s->name,
        'type'        => $s->type ?? '',
        'benefits'    => \Illuminate\Support\Str::limit($s->benefits ?? '',80),
        'score'       => (int)($d['score'] ?? 0),
        'eligibility' => $d['eligibility'] ?? 'N/A',
        'tag'         => $d['tag'] ?? '',
        'reasoning'   => $d['reasoning'] ?? '',
        'applied'     => (bool)($d['applied'] ?? false),
        'status'      => $d['status'] ?? null,
        'source'      => $s->source ?? '',
        'slots'       => $s->slots ?? 0,
        'end_date'    => $s->end_date ? $s->end_date->format('M d, Y') : 'Open',
        'requirements'=> $s->requirements ?? '',
    ];
}
usort($mapped, function($a,$b){ return $b['score'] - $a['score']; });

$eligible_count = count(array_filter($mapped, fn($x) => $x['eligibility']==='Eligible'));
$review_count   = count(array_filter($mapped, fn($x) => $x['eligibility']==='For Review'));

$bracketLabels = \App\Http\Controllers\Student\EligibilityController::INCOME_BRACKETS;
$yearLevels    = \App\Http\Controllers\Student\EligibilityController::YEAR_LEVELS;
$enrollTypes   = \App\Http\Controllers\Student\EligibilityController::ENROLLMENT_TYPES;
$honorLevels   = \App\Http\Controllers\Student\EligibilityController::ACADEMIC_HONORS;
@endphp

{{-- STEP INDICATOR --}}
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:20px;" class="an">
    @php
        $steps = $hasProfile
            ? [['1','Fill Up Your Data','done'],['2','Run the Eligibility Test','done'],['3','See Your Results','active']]
            : [['1','Fill Up Your Data','active'],['2','Run the Eligibility Test',''],['3','See Your Results','']];
    @endphp
    @foreach($steps as $i => $st)
    <div style="display:flex;align-items:center;gap:8px;">
        <div style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0;{{ $st[2]==='done' ? 'background:var(--g);color:#fff;' : ($st[2]==='active' ? 'background:var(--y);color:#0d3318;' : 'background:var(--bg);border:1.5px solid var(--bd);color:var(--tm);') }}">
            @if($st[2]==='done')<i class="fas fa-check"></i>@else{{ $st[0] }}@endif
        </div>
        <span style="font-size:12.5px;font-weight:700;color:{{ $st[2] ? 'var(--tx)' : 'var(--tm)' }};">{{ $st[1] }}</span>
        @if($i < 2)<i class="fas fa-chevron-right" style="font-size:11px;color:var(--tm);margin:0 4px;"></i>@endif
    </div>
    @endforeach
</div>

{{-- ═══ STEP 1 — STUDENT DATA FORM ═══ --}}
<div class="card an mb3" id="dataCard" style="{{ $hasProfile ? 'display:none;' : '' }}border:2px solid var(--g);">
    <div class="ch" style="background:linear-gradient(135deg,#0d3318,#1a6b2f);padding:16px 20px;">
        <div style="width:42px;height:42px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:19px;color:#0d3318;"><i class="fas fa-user-edit"></i></div>
        <div>
            <h2 style="color:#fff;font-size:16px;">@if($hasProfile) Edit Your Eligibility Data @else Step 1 — Fill Up Your Scholarship Data @endif</h2>
            <div style="font-size:11px;color:rgba(255,255,255,.6);">These are the common requirements scholarships look at. The system uses them to determine which scholarships you are eligible for.</div>
        </div>
    </div>
    <div style="padding:22px 24px;">
        @if($errors->any())
        <div style="background:#fde8e6;border:1.5px solid var(--danger);border-radius:var(--rs);padding:12px 14px;font-size:13px;color:#7a1a14;margin-bottom:16px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>Please complete all fields correctly before running the test.
        </div>
        @endif

        <form method="POST" action="{{ route('student.eligibility.profile') }}">
        @csrf
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px;">

            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:var(--tx);margin-bottom:6px;">GWA (General Weighted Average) <span style="color:var(--danger);">*</span></label>
                <input type="number" name="gwa" value="{{ old('gwa', $profile?->gwa) }}" step="0.01" min="1" max="5" required
                    placeholder="e.g., 1.75"
                    style="width:100%;padding:10px 12px;border:1.5px solid var(--bd);border-radius:var(--rs);background:var(--card);color:var(--tx);font-size:14px;font-family:'Sora',sans-serif;">
                <div style="font-size:11px;color:var(--tm);margin-top:4px;">Philippine scale: 1.00 is highest, 5.00 is lowest.</div>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:var(--tx);margin-bottom:6px;">Year Level <span style="color:var(--danger);">*</span></label>
                <select name="year_level" required
                    style="width:100%;padding:10px 12px;border:1.5px solid var(--bd);border-radius:var(--rs);background:var(--card);color:var(--tx);font-size:14px;">
                    <option value="" disabled {{ old('year_level', $profile?->year_level) ? '' : 'selected' }}>Select year level</option>
                    @foreach($yearLevels as $yl)
                    <option value="{{ $yl }}" {{ old('year_level', $profile?->year_level) === $yl ? 'selected' : '' }}>{{ $yl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:var(--tx);margin-bottom:6px;">Enrollment Type <span style="color:var(--danger);">*</span></label>
                <select name="enrollment_type" required
                    style="width:100%;padding:10px 12px;border:1.5px solid var(--bd);border-radius:var(--rs);background:var(--card);color:var(--tx);font-size:14px;">
                    @foreach($enrollTypes as $et)
                    <option value="{{ $et }}" {{ old('enrollment_type', $profile?->enrollment_type ?? 'Regular') === $et ? 'selected' : '' }}>{{ $et }}</option>
                    @endforeach
                </select>
                <div style="font-size:11px;color:var(--tm);margin-top:4px;">Regular = complete load per curriculum.</div>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:var(--tx);margin-bottom:6px;">Annual Family Income <span style="color:var(--danger);">*</span></label>
                <select name="income_bracket" required
                    style="width:100%;padding:10px 12px;border:1.5px solid var(--bd);border-radius:var(--rs);background:var(--card);color:var(--tx);font-size:14px;">
                    @foreach($bracketLabels as $key => $label)
                    <option value="{{ $key }}" {{ old('income_bracket', $profile?->income_bracket ?? 'below_200') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:var(--tx);margin-bottom:6px;">Academic Honors <span style="color:var(--danger);">*</span></label>
                <select name="academic_honors" required
                    style="width:100%;padding:10px 12px;border:1.5px solid var(--bd);border-radius:var(--rs);background:var(--card);color:var(--tx);font-size:14px;">
                    @foreach($honorLevels as $h)
                    <option value="{{ $h }}" {{ old('academic_honors', $profile?->academic_honors ?? 'None') === $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
                <div style="font-size:11px;color:var(--tm);margin-top:4px;">Honors received in your last completed level.</div>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:var(--tx);margin-bottom:6px;">Do you have failing grades this semester? <span style="color:var(--danger);">*</span></label>
                <select name="has_failing" required
                    style="width:100%;padding:10px 12px;border:1.5px solid var(--bd);border-radius:var(--rs);background:var(--card);color:var(--tx);font-size:14px;">
                    <option value="0" {{ old('has_failing', $profile?->has_failing ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('has_failing', $profile?->has_failing ? '1' : '0') === '1' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:var(--tx);margin-bottom:6px;">Do you have an active disciplinary case? <span style="color:var(--danger);">*</span></label>
                <select name="has_discipline" required
                    style="width:100%;padding:10px 12px;border:1.5px solid var(--bd);border-radius:var(--rs);background:var(--card);color:var(--tx);font-size:14px;">
                    <option value="0" {{ old('has_discipline', $profile?->has_discipline ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('has_discipline', $profile?->has_discipline ? '1' : '0') === '1' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
        </div>

        <div style="margin-top:22px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-ac" style="font-size:14px;padding:12px 26px;">
                <i class="fas fa-magic"></i> @if($hasProfile) Update &amp; Re-run Eligibility Test @else Take Eligibility Test @endif
            </button>
            @if($hasProfile)
            <button type="button" onclick="cancelEdit()" class="btn btn-o" style="font-size:13px;">Cancel</button>
            @endif
            <span style="font-size:12px;color:var(--tm);"><i class="fas fa-lock" style="margin-right:5px;"></i>Your data is saved so you can edit it and re-run the test anytime.</span>
        </div>
        </form>
    </div>
</div>

@if($hasProfile)
{{-- OVERALL BANNER (Step 3 — results) --}}
<div class="elig-banner {{ $bannerClass }} an" style="margin-bottom:20px;">
    <div class="elig-icon"><i class="fas fa-{{ $bannerIcon }}" style="font-size:28px;color:#fff;"></i></div>
    <div style="flex:1;">
        <div class="elig-title">
            @if($overallEligibility==='Eligible') Scholarship-Ready! Visit the SAO Office to Apply.
            @elseif($overallEligibility==='For Review') Partially Qualified — See Improvement Tips Below
            @else Not Yet Qualified — Follow Your Action Plan Below
            @endif
        </div>
        <div class="elig-sub">The Eligibility Test evaluated your data against {{ count($mapped) }} active scholarship programs. Applications are done physically at the Student Affairs Office.</div>
        <div style="font-size:12px;color:rgba(255,255,255,.65);margin-top:5px;">
            GWA: <strong style="color:#fff;">{{ number_format($gwa,2) }}</strong> &nbsp;·&nbsp;
            {{ $profile?->year_level }} &nbsp;·&nbsp;
            {{ $profile?->enrollment_type }} &nbsp;·&nbsp;
            {{ $bracketLabels[$bracket] ?? 'N/A' }} &nbsp;·&nbsp;
            {{ $profile?->academic_honors }}
        </div>
    </div>
    <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:8px;">
        <div class="chance-circle">
            <div class="chance-val">{{ $overallScore }}%</div>
            <div class="chance-lbl">Best Score</div>
        </div>
        <button type="button" onclick="editData()" class="btn btn-o" style="border-color:rgba(255,255,255,.4);color:#fff;font-size:12px;">
            <i class="fas fa-pen"></i> Edit Data
        </button>
    </div>
</div>

{{-- QUICK STATS --}}
<div class="sg" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
    <div class="sc an d1"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Eligible For</div><div class="val">{{ $eligible_count }}</div><div class="chg">scholarships</div></div></div>
    <div class="sc an d2"><div class="si o"><i class="fas fa-exclamation-circle"></i></div><div class="sv"><div class="lbl">For Review</div><div class="val">{{ $review_count }}</div><div class="chg">need verification</div></div></div>
    <div class="sc an d3"><div class="si y"><i class="fas fa-star"></i></div><div class="sv"><div class="lbl">Best AI Score</div><div class="val">{{ $overallScore }}%</div><div class="chg">out of 100</div></div></div>
    <div class="sc an d4"><div class="si t"><i class="fas fa-clipboard-check"></i></div><div class="sv"><div class="lbl">Test Data</div><div class="val">Saved</div><div class="chg">updated {{ $profile?->updated_at?->format('M d, Y') ?? '—' }}</div></div></div>
</div>

{{-- ═══ ELIGIBILITY RESULTS ═══ --}}
<div class="card an mb3">
    <div class="ch" style="background:linear-gradient(135deg,#0d3318,#1a6b2f);">
        <div style="width:42px;height:42px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:19px;color:#0d3318;"><i class="fas fa-poll"></i></div>
        <div><h2 style="color:#fff;font-size:16px;">Your Eligibility Results</h2>
        <div style="font-size:11px;color:rgba(255,255,255,.6);">Based on the data you entered — scholarships ranked by your AI score</div></div>
        <div class="ch-acts">
            <span style="font-size:11px;color:rgba(255,255,255,.6);">Tested {{ $profile?->updated_at?->format('M d, Y h:i A') }}</span>
        </div>
    </div>
    <div style="padding:20px;">
        <div style="display:flex;flex-direction:column;gap:9px;">
        @foreach($mapped as $s)
        @php $sfC = $s['score']>=75?'var(--gm)':($s['score']>=50?'var(--warn)':'var(--danger)'); @endphp
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:12.5px;font-weight:700;color:var(--tx);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s['name'] }} @if($s['applied'])<span style="font-size:10px;font-weight:700;color:var(--gm);">· Applied</span>@endif</div>
                <div style="font-size:11px;color:var(--tm);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ \Illuminate\Support\Str::limit($s['reasoning'],90) }}</div>
            </div>
            <div style="width:120px;height:8px;background:#e0e0e0;border-radius:20px;overflow:hidden;flex-shrink:0;">
                <div style="width:{{ $s['score'] }}%;height:100%;background:{{ $sfC }};border-radius:20px;"></div>
            </div>
            <div style="font-weight:800;font-size:12px;color:{{ $sfC }};width:34px;text-align:right;flex-shrink:0;">{{ $s['score'] }}%</div>
            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;flex-shrink:0;background:{{ $s['eligibility']==='Eligible'?'#d0f0d8':($s['eligibility']==='For Review'?'#fef3cd':'#fde8e6') }};color:{{ $s['eligibility']==='Eligible'?'#0d6624':($s['eligibility']==='For Review'?'#a07c00':'#c0392b') }};">{{ $s['eligibility'] }}</span>
        </div>
        @endforeach
        </div>

        <div style="margin-top:16px;background:linear-gradient(135deg,#0d3318,#1a6b2f);border-radius:10px;padding:13px 16px;display:flex;align-items:center;gap:12px;">
            <i class="fas fa-map-marker-alt" style="color:var(--y);font-size:20px;flex-shrink:0;"></i>
            <div>
                <div style="font-size:13px;font-weight:700;color:#fff;">Apply Physically at the Student Affairs Office</div>
                <div style="font-size:12px;color:rgba(255,255,255,.7);margin-top:2px;">Saint Columban College — Pagadian City, Zamboanga del Sur &nbsp;·&nbsp; Bring your original documents</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ GAP ANALYSIS ═══ --}}
<div class="card an mb3">
    <div class="ch" style="background:linear-gradient(135deg,#1a3a6b,#0038a8);">
        <div style="width:42px;height:42px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:19px;color:#fff;flex-shrink:0;"><i class="fas fa-chart-line"></i></div>
        <div><h2 style="color:#fff;font-size:16px;">Gap Analysis — What You Need to Qualify</h2>
        <div style="font-size:11px;color:rgba(255,255,255,.6);">Exact requirements vs your current data for each scholarship</div></div>
        <div class="ch-acts">
            <button type="button" onclick="generateGapAnalysis()" id="gapBtn" class="btn btn-ac" style="font-size:12px;">
                <i class="fas fa-search"></i> Run AI Gap Analysis
            </button>
        </div>
    </div>
    <div style="padding:20px;">

        {{-- Static gap cards per scholarship --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;margin-bottom:16px;">
        @foreach($mapped as $s)
        @php
            $sc  = $s['score'];
            $gap = 100 - $sc;
            $scC = $sc>=75?'#0d6624':($sc>=50?'#a07c00':'#c0392b');
            $scB = $sc>=75?'#f0faf2':($sc>=50?'#fffcf0':'#fff8f8');
            $brd = $sc>=75?'#a0d8b0':($sc>=50?'#f0d060':'#f0a0a0');
        @endphp
        <div style="background:{{ $scB }};border:1.5px solid {{ $brd }};border-radius:10px;padding:14px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <div style="font-size:13px;font-weight:700;color:var(--tx);">{{ \Illuminate\Support\Str::limit($s['name'],35) }}</div>
                <div style="font-family:'Sora',sans-serif;font-size:18px;font-weight:800;color:{{ $scC }};">{{ $sc }}%</div>
            </div>
            <div style="background:#e0e0e0;border-radius:20px;overflow:hidden;height:7px;margin-bottom:8px;">
                <div style="width:{{ $sc }}%;height:100%;background:{{ $scC }};border-radius:20px;"></div>
            </div>
            <div style="font-size:11px;color:var(--tm);margin-bottom:8px;line-height:1.5;">
                {{ \Illuminate\Support\Str::limit($s['reasoning'],100) }}
            </div>
            <div style="font-size:11px;">
                @if($sc >= 75)
                <span style="color:#0d6624;font-weight:700;"><i class="fas fa-check-circle" style="margin-right:4px;"></i>You meet the requirements. Visit SAO to apply.</span>
                @elseif($sc >= 50)
                <span style="color:#a07c00;font-weight:700;"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>{{ $gap }} pts needed. Submit supporting documents at SAO.</span>
                @else
                <span style="color:#c0392b;font-weight:700;"><i class="fas fa-times-circle" style="margin-right:4px;"></i>{{ $gap }} pts gap. Focus on GWA and enrollment this semester.</span>
                @endif
            </div>
        </div>
        @endforeach
        </div>

        {{-- AI Gap Analysis result --}}
        <div id="gapLoading" style="display:none;text-align:center;padding:20px;background:var(--bg);border-radius:var(--rs);">
            <div style="display:inline-flex;align-items:center;gap:10px;">
                <div style="width:16px;height:16px;border:3px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:aiSpin .7s linear infinite;"></div>
                <span style="font-size:13px;color:var(--tm);">AI is running your gap analysis...</span>
            </div>
        </div>

        <div id="gapResult" style="display:none;border:1.5px solid var(--y);border-radius:var(--r);overflow:hidden;margin-top:4px;">
            <div style="background:linear-gradient(135deg,#0d3318,#1a6b2f);padding:11px 16px;display:flex;align-items:center;gap:10px;">
                <div style="width:26px;height:26px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#0d3318;flex-shrink:0;"><i class="fas fa-search"></i></div>
                <span style="font-size:13px;font-weight:700;color:#fff;">AI Gap Analysis Report</span>
                <button type="button" onclick="document.getElementById('gapResult').style.display='none'" style="margin-left:auto;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:4px 10px;font-size:11px;color:rgba(255,255,255,.7);cursor:pointer;">Close</button>
            </div>
            <div id="gapResultText" style="padding:18px 20px;font-size:13.5px;color:var(--tx);line-height:1.9;white-space:pre-wrap;background:var(--card);"></div>
        </div>

        <div id="gapError" style="display:none;background:#fde8e6;border:1.5px solid var(--danger);border-radius:var(--rs);padding:12px 14px;font-size:13px;color:#7a1a14;margin-top:10px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i><span id="gapErrorMsg"></span>
        </div>
    </div>
</div>

{{-- ═══ PRIORITY ACTION PLAN ═══ --}}
<div class="card an mb3">
    <div class="ch">
        <div style="width:38px;height:38px;background:linear-gradient(135deg,var(--g),var(--gm));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--y);flex-shrink:0;"><i class="fas fa-tasks"></i></div>
        <div><h2 style="font-size:15px;">Your Priority Action Plan This Semester</h2>
        <div style="font-size:11px;color:var(--tm);">Follow these steps to qualify for more scholarships</div></div>
        <div class="ch-acts">
            <button type="button" onclick="generateActionPlan()" id="planBtn" class="btn btn-p btn-sm">
                <i class="fas fa-robot"></i> Generate AI Plan
            </button>
        </div>
    </div>
    <div style="padding:18px 20px;">

        {{-- Static action items based on test data --}}
        <div style="display:flex;flex-direction:column;gap:10px;" id="staticPlan">
        @php
            $actions = [];
            if($gwa > 1.75) $actions[] = ['high','Improve GWA to 1.75 or better','Your GWA of '.number_format($gwa,2).' is above the cutoff for most scholarships. Focus on your academic performance this semester.','fas fa-star'];
            if(!$isRegular) $actions[] = ['high','Shift to Regular Enrollment','Irregular students lose 10 points on every scholarship score. Shifting to Regular doubles your enrollment score.','fas fa-id-badge'];
            if($bracket==='above_400'||$bracket==='') $actions[] = ['medium','Update your income bracket','Income bracket affects up to 15 points of your AI score. Click Edit Data to make sure yours is accurate.','fas fa-hand-holding-usd'];
            if($gwa > 1.25 && $gwa <= 1.75) $actions[] = ['low','Push GWA to 1.25 for Excellence Bonus','You are close to qualifying for the +5 point Excellence Bonus on all scholarships.','fas fa-trophy'];
            $actions[] = ['high','Visit SAO Office to get the official scholarship application form','Physical applications are required. Bring your Eligibility Test results, transcript, and proof of income.','fas fa-map-marker-alt'];
            $actions[] = ['medium','Prepare supporting documents','Requirements typically include: transcript of records, certificate of enrollment, income tax return or certificate of indigency, and 2x2 ID photos.','fas fa-file-alt'];
            $actions[] = ['low','Monitor scholarship deadlines','Check with the SAO office regularly for opening and closing dates of each scholarship program.','fas fa-calendar-check'];
        @endphp
        @foreach($actions as $idx => $action)
        @php
            $pCol = $action[0]==='high'?'#c0392b':($action[0]==='medium'?'#d68910':'#1a6b2f');
            $pBg  = $action[0]==='high'?'#fde8e6':($action[0]==='medium'?'#fef3cd':'#d0f0d8');
            $pLbl = $action[0]==='high'?'Priority':($action[0]==='medium'?'Important':'Recommended');
        @endphp
        <div style="display:flex;align-items:flex-start;gap:13px;padding:13px 15px;background:var(--bg);border:1px solid var(--bd);border-radius:10px;">
            <div style="width:34px;height:34px;background:linear-gradient(135deg,var(--g),var(--gm));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;color:var(--y);flex-shrink:0;">{{ $idx+1 }}</div>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                    <span style="font-size:13px;font-weight:700;color:var(--tx);"><i class="fas {{ $action[3] }}" style="color:var(--gm);margin-right:5px;"></i>{{ $action[1] }}</span>
                    <span style="background:{{ $pBg }};color:{{ $pCol }};padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;">{{ $pLbl }}</span>
                </div>
                <div style="font-size:12px;color:var(--tm);line-height:1.6;">{{ $action[2] }}</div>
            </div>
        </div>
        @endforeach
        </div>

        {{-- AI Plan Result --}}
        <div id="planLoading" style="display:none;text-align:center;padding:18px;background:var(--bg);border-radius:var(--rs);margin-top:12px;">
            <div style="display:inline-flex;align-items:center;gap:10px;">
                <div style="width:16px;height:16px;border:3px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:aiSpin .7s linear infinite;"></div>
                <span style="font-size:13px;color:var(--tm);">AI is generating your action plan...</span>
            </div>
        </div>

        <div id="planResult" style="display:none;border:1.5px solid var(--gm);border-radius:var(--r);overflow:hidden;margin-top:12px;">
            <div style="background:linear-gradient(135deg,var(--g),var(--gm));padding:11px 16px;display:flex;align-items:center;gap:10px;">
                <div style="width:26px;height:26px;background:rgba(255,255,255,.2);border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff;flex-shrink:0;"><i class="fas fa-robot"></i></div>
                <span style="font-size:13px;font-weight:700;color:#fff;">AI Priority Action Plan</span>
                <button type="button" onclick="document.getElementById('planResult').style.display='none'" style="margin-left:auto;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:4px 10px;font-size:11px;color:rgba(255,255,255,.7);cursor:pointer;">Close</button>
            </div>
            <div id="planResultText" style="padding:18px 20px;font-size:13.5px;color:var(--tx);line-height:1.9;white-space:pre-wrap;background:var(--card);"></div>
        </div>

        <div id="planError" style="display:none;background:#fde8e6;border:1.5px solid var(--danger);border-radius:var(--rs);padding:12px 14px;font-size:13px;color:#7a1a14;margin-top:10px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i><span id="planErrorMsg"></span>
        </div>
    </div>
</div>

{{-- ═══ HOW TO APPLY PHYSICALLY ═══ --}}
<div class="card an mb3" style="border:2px solid var(--g);">
    <div class="ch"><i class="fas fa-map-marker-alt" style="color:var(--gm);font-size:18px;"></i><h2>How to Apply — Physical Application Process</h2></div>
    <div style="padding:18px 20px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
        @foreach([
            ['1','fas fa-user-edit','Fill Up Your Data','Enter your GWA, income bracket, enrollment type, year level, honors, and status on this page.','t'],
            ['2','fas fa-robot','Run the Eligibility Test','Click the Eligibility Test button to see which scholarships you qualify for.','y'],
            ['3','fas fa-file-alt','Prepare Documents','Gather your transcript of records, COE, income proof, birth certificate, and 2x2 ID photos.','g'],
            ['4','fas fa-map-marker-alt','Visit SAO Office','Go to the Student Affairs Office at Saint Columban College during office hours.','o'],
            ['5','fas fa-clipboard-list','Submit Application','Fill out the official application form, attach your documents, and submit to the SAO officer.','g'],
            ['6','fas fa-bell','Wait for Result','The SAO office will notify you of the result. AI eligibility scores assist but final decision rests with the committee.','t'],
        ] as $step)
        <div style="background:var(--bg);border:1px solid var(--bd);border-radius:10px;padding:13px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:7px;">
                <div style="width:28px;height:28px;background:linear-gradient(135deg,var(--g),var(--gm));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;color:var(--y);flex-shrink:0;">{{ $step[0] }}</div>
                <div class="si {{ $step[4] }}" style="width:28px;height:28px;border-radius:7px;font-size:12px;flex-shrink:0;"><i class="{{ $step[1] }}"></i></div>
                <div class="fws" style="font-size:13px;">{{ $step[2] }}</div>
            </div>
            <div style="font-size:12px;color:var(--tm);line-height:1.5;">{{ $step[3] }}</div>
        </div>
        @endforeach
        </div>
    </div>
</div>
@endif

<style>
@keyframes aiSpin{to{transform:rotate(360deg);}}
</style>

<script>
(function(){
    // AI generation runs server-side (student/eligibility/ai) — the API key
    // stays in .env and the prompt/model live in EligibilityController.
    function callAI(action, onSuccess, onError){
        fetch('{{ route('student.eligibility.ai') }}', {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With':'XMLHttpRequest'
            },
            body: JSON.stringify({action:action})
        })
        .then(function(r){ return r.json().then(function(d){ return {ok:r.ok,data:d}; }); })
        .then(function(r){
            if(!r.ok){ onError(r.data&&r.data.message?r.data.message:'AI request failed. Please try again.'); return; }
            if(!r.data.text){ onError('Empty AI response. Please try again.'); return; }
            onSuccess(r.data.text);
        })
        .catch(function(e){ onError('Network error: '+e.message); });
    }

    // Step 3 — edit function: reveal the pre-filled data form so the
    // student can update their entries and re-run the test.
    window.editData = function(){
        var card = document.getElementById('dataCard');
        card.style.display = 'block';
        card.scrollIntoView({behavior:'smooth', block:'start'});
    };

    window.cancelEdit = function(){
        document.getElementById('dataCard').style.display = 'none';
    };

    window.generateGapAnalysis = function(){
        var btn = document.getElementById('gapBtn');
        btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Analyzing...';
        document.getElementById('gapLoading').style.display='block';
        document.getElementById('gapResult').style.display='none';
        document.getElementById('gapError').style.display='none';

        callAI('gap', function(text){
            document.getElementById('gapLoading').style.display='none';
            document.getElementById('gapResultText').textContent=text;
            document.getElementById('gapResult').style.display='block';
            btn.disabled=false; btn.innerHTML='<i class="fas fa-search"></i> Re-run Gap Analysis';
        }, function(err){
            document.getElementById('gapLoading').style.display='none';
            document.getElementById('gapErrorMsg').textContent=err;
            document.getElementById('gapError').style.display='block';
            btn.disabled=false; btn.innerHTML='<i class="fas fa-search"></i> Run AI Gap Analysis';
        });
    };

    window.generateActionPlan = function(){
        var btn = document.getElementById('planBtn');
        btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Generating...';
        document.getElementById('planLoading').style.display='block';
        document.getElementById('planResult').style.display='none';
        document.getElementById('planError').style.display='none';

        callAI('plan', function(text){
            document.getElementById('planLoading').style.display='none';
            document.getElementById('planResultText').textContent=text;
            document.getElementById('planResult').style.display='block';
            btn.disabled=false; btn.innerHTML='<i class="fas fa-robot"></i> Regenerate Plan';
        }, function(err){
            document.getElementById('planLoading').style.display='none';
            document.getElementById('planErrorMsg').textContent=err;
            document.getElementById('planError').style.display='block';
            btn.disabled=false; btn.innerHTML='<i class="fas fa-robot"></i> Generate AI Plan';
        });
    };
})();
</script>
@endsection
