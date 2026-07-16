@extends('admin.layouts.app')
@section('title','AI Scholarship Screening')
@section('page-title','AI Scholarship Screening')
@section('page-sub','Smart screening dashboard — rank physical applicants and generate approval recommendations')

@section('ai-bar')
<div class="ai-bar">
    <div class="ai-bar-label"><div class="ai-dot"></div>Groq AI Active</div>
    <div class="ai-bar-stats">
        <div class="ai-stat">Total Applications: <strong>{{ $stats['total'] }}</strong></div>
        <div class="ai-stat">Eligible: <strong>{{ $stats['eligible'] }}</strong></div>
        <div class="ai-stat">For Review: <strong>{{ $stats['review'] }}</strong></div>
        <div class="ai-stat">Not Eligible: <strong>{{ $stats['not_eligible'] }}</strong></div>
        <div class="ai-stat">Avg AI Score: <strong>{{ $stats['avg_score'] }}%</strong></div>
    </div>
</div>
@endsection

@section('content')

@php
$gk = '';
try { $gk = config('services.groq.key',''); } catch(\Exception $e){}
if(!$gk){ $gk = getenv('GROQ_API_KEY') ?: ''; }
if(!$gk && file_exists(base_path('.env'))){
    foreach(file(base_path('.env'),FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $ln){
        $ln=trim($ln); if(strpos($ln,'#')===0) continue;
        if(strpos($ln,'GROQ_API_KEY=')===0){ $gk=trim(substr($ln,15)," \t\"'"); break; }
    }
}
$hasKey = !empty(trim($gk));
@endphp

<input type="hidden" id="gk" value="{{ htmlspecialchars($gk,ENT_QUOTES,'UTF-8') }}">

{{-- STAT CARDS --}}
<div class="sg" style="grid-template-columns:repeat(5,1fr);margin-bottom:20px;">
    <div class="sc an d1"><div class="si t"><i class="fas fa-users"></i></div><div class="sv"><div class="lbl">Total Evaluated</div><div class="val">{{ $stats['total'] }}</div></div></div>
    <div class="sc an d2"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Eligible</div><div class="val">{{ $stats['eligible'] }}</div><div class="chg">Score ≥ 75%</div></div></div>
    <div class="sc an d3"><div class="si o"><i class="fas fa-exclamation-circle"></i></div><div class="sv"><div class="lbl">For Review</div><div class="val">{{ $stats['review'] }}</div><div class="chg">Score 50–74%</div></div></div>
    <div class="sc an d4"><div class="si r"><i class="fas fa-times-circle"></i></div><div class="sv"><div class="lbl">Not Eligible</div><div class="val">{{ $stats['not_eligible'] }}</div></div></div>
    <div class="sc an d5"><div class="si y"><i class="fas fa-star"></i></div><div class="sv"><div class="lbl">Avg AI Score</div><div class="val">{{ $stats['avg_score'] }}%</div></div></div>
</div>

{{-- ═══ BATCH ANALYSIS + SCREENING ═══ --}}
<div class="card an mb3" style="border:2px solid var(--y);">
    <div class="ch" style="background:linear-gradient(135deg,#0d3318,#1a6b2f);">
        <div style="width:42px;height:42px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:12px;display:flex;align-items:center;justify-content:justify;font-size:19px;color:#0d3318;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-brain"></i></div>
        <div>
            <h2 style="color:#fff;font-size:16px;">AI Smart Screening Dashboard</h2>
            <div style="font-size:11px;color:rgba(255,255,255,.6);">Generate approval recommendations, screen physical applicants, and get batch analysis reports</div>
        </div>
        <div class="ch-acts" style="display:flex;gap:8px;flex-wrap:wrap;">
            <button type="button" onclick="runBatchAnalysis()" id="batchBtn" class="btn btn-ac" style="font-size:12px;white-space:nowrap;"><i class="fas fa-play"></i> Run Batch Analysis</button>
            <button type="button" onclick="generateApprovalReport()" id="approvalBtn" class="btn btn-s btn-sm" style="white-space:nowrap;"><i class="fas fa-clipboard-check"></i> Approval Report</button>
        </div>
    </div>
    <div style="padding:20px;">

        @if(!$hasKey)
        <div class="alert al-d" style="margin-bottom:14px;font-size:12px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>GROQ_API_KEY not detected. Add to <code>.env</code> and run <code>php artisan config:clear</code>.</span>
        </div>
        @endif

        {{-- Loading --}}
        <div id="aiLoading" style="display:none;text-align:center;padding:22px;background:var(--bg);border-radius:var(--rs);margin-bottom:14px;">
            <div style="display:inline-flex;align-items:center;gap:12px;">
                <div style="width:20px;height:20px;border:3px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:aiSpin .7s linear infinite;"></div>
                <span id="aiLoadingMsg" style="font-size:13px;color:var(--tm);">Groq AI is analyzing applications...</span>
            </div>
        </div>

        {{-- AI Result --}}
        <div id="aiResult" style="display:none;border:1.5px solid var(--y);border-radius:var(--r);overflow:hidden;margin-bottom:14px;">
            <div style="background:linear-gradient(135deg,#0d3318,#1a6b2f);padding:12px 18px;display:flex;align-items:center;gap:10px;">
                <div style="width:28px;height:28px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#0d3318;flex-shrink:0;"><i class="fas fa-robot"></i></div>
                <span id="aiResultTitle" style="font-size:13px;font-weight:700;color:#fff;">AI Analysis Report</span>
                <div style="margin-left:auto;display:flex;gap:6px;">
                    <button type="button" onclick="printResult()" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:4px 10px;font-size:11px;color:rgba(255,255,255,.7);cursor:pointer;"><i class="fas fa-print" style="margin-right:4px;"></i>Print</button>
                    <button type="button" onclick="document.getElementById('aiResult').style.display='none'" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:4px 10px;font-size:11px;color:rgba(255,255,255,.7);cursor:pointer;">Close</button>
                </div>
            </div>
            <div id="aiResultText" style="padding:20px 24px;font-size:13.5px;color:var(--tx);line-height:1.9;white-space:pre-wrap;background:#fff;"></div>
        </div>

        <div id="aiError" style="display:none;background:#fde8e6;border:1.5px solid var(--danger);border-radius:var(--rs);padding:12px 14px;font-size:13px;color:#7a1a14;margin-bottom:14px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i><span id="aiErrorMsg"></span>
        </div>

        {{-- Quick Queries --}}
        <div style="border-top:1px solid var(--bd);padding-top:14px;">
            <div style="font-size:12px;color:var(--tm);font-weight:600;margin-bottom:8px;">Quick admin queries:</div>
            <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:10px;">
                <button type="button" onclick="askAdmin('Who should be approved first and why?')" class="btn btn-o btn-sm" style="border-radius:20px;font-size:12px;">Who to approve first?</button>
                <button type="button" onclick="askAdmin('List all applicants with score above 80% and their scholarship')" class="btn btn-o btn-sm" style="border-radius:20px;font-size:12px;">Score above 80%</button>
                <button type="button" onclick="askAdmin('Which scholarship has the most qualified applicants?')" class="btn btn-o btn-sm" style="border-radius:20px;font-size:12px;">Most qualified scholarship</button>
                <button type="button" onclick="askAdmin('List applicants for review and what documents to verify')" class="btn btn-o btn-sm" style="border-radius:20px;font-size:12px;">Review list + documents</button>
                <button type="button" onclick="askAdmin('Generate a rejection explanation for not eligible applicants')" class="btn btn-o btn-sm" style="border-radius:20px;font-size:12px;">Rejection explanations</button>
            </div>
            <div style="display:flex;gap:8px;">
                <input type="text" id="adminInput" class="fc" placeholder="Ask AI about applicants, screening, or recommendations..." style="flex:1;" onkeydown="if(event.key==='Enter'){askAdmin(this.value);}">
                <button type="button" onclick="askAdmin(document.getElementById('adminInput').value)" class="btn btn-ai" style="flex-shrink:0;"><i class="fas fa-paper-plane"></i> Ask</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ PHYSICAL APPLICANT SCREENER ═══ --}}
<div class="card an mb3" style="border:1.5px solid var(--info);">
    <div class="ch" style="background:linear-gradient(135deg,#0d2d4a,#1a5a8a);">
        <div style="width:42px;height:42px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:19px;color:#fff;flex-shrink:0;"><i class="fas fa-user-check"></i></div>
        <div>
            <h2 style="color:#fff;font-size:16px;">Physical Applicant Quick Screener</h2>
            <div style="font-size:11px;color:rgba(255,255,255,.6);">Enter a walk-in applicant's details and AI instantly evaluates their eligibility</div>
        </div>
    </div>
    <div style="padding:20px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:14px;">
            <div class="fg" style="margin-bottom:0;"><label class="fl" style="font-size:12px;">Applicant Name</label>
            <input type="text" id="screenName" class="fc" placeholder="Juan Dela Cruz"></div>
            <div class="fg" style="margin-bottom:0;"><label class="fl" style="font-size:12px;">GWA</label>
            <input type="number" id="screenGWA" class="fc mono" step="0.01" min="1" max="5" placeholder="1.75"></div>
            <div class="fg" style="margin-bottom:0;"><label class="fl" style="font-size:12px;">Enrollment Type</label>
            <select id="screenEnroll" class="fc">
                <option value="Regular">Regular</option>
                <option value="Irregular">Irregular</option>
            </select></div>
            <div class="fg" style="margin-bottom:0;"><label class="fl" style="font-size:12px;">Family Income</label>
            <select id="screenIncome" class="fc">
                <option value="below_200">Below ₱200,000/year</option>
                <option value="200_400">₱200,000–₱400,000/year</option>
                <option value="above_400">Above ₱400,000/year</option>
            </select></div>
            <div class="fg" style="margin-bottom:0;"><label class="fl" style="font-size:12px;">Has Failing Grades?</label>
            <select id="screenFailing" class="fc">
                <option value="no">No</option>
                <option value="yes">Yes</option>
            </select></div>
            <div class="fg" style="margin-bottom:0;"><label class="fl" style="font-size:12px;">Scholarship Applying For</label>
            <select id="screenScholarship" class="fc">
                <option value="">— Select Scholarship —</option>
                @foreach($scholarships as $sch)
                <option value="{{ $sch->id }}" data-name="{{ $sch->name }}">{{ $sch->name }}</option>
                @endforeach
            </select></div>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="button" onclick="screenApplicant()" id="screenBtn" class="btn btn-p" style="flex:1;justify-content:center;padding:11px;">
                <i class="fas fa-search"></i> Screen This Applicant
            </button>
        </div>

        <div id="screenLoading" style="display:none;text-align:center;padding:16px;margin-top:12px;background:var(--bg);border-radius:var(--rs);">
            <div style="display:inline-flex;align-items:center;gap:10px;">
                <div style="width:16px;height:16px;border:3px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:aiSpin .7s linear infinite;"></div>
                <span style="font-size:13px;color:var(--tm);">Screening applicant...</span>
            </div>
        </div>

        <div id="screenResult" style="display:none;margin-top:14px;border:1.5px solid var(--bd);border-radius:var(--r);overflow:hidden;">
            <div id="screenResultHeader" style="padding:12px 16px;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-user-check" style="font-size:18px;"></i>
                <span id="screenResultTitle" style="font-size:14px;font-weight:700;"></span>
                <span id="screenResultScore" style="margin-left:auto;font-family:'Sora',sans-serif;font-size:22px;font-weight:800;"></span>
            </div>
            <div id="screenResultBody" style="padding:14px 18px;font-size:13px;color:var(--tx);line-height:1.8;white-space:pre-wrap;background:#fff;"></div>
        </div>
    </div>
</div>

{{-- ═══ FILTERS ═══ --}}
<div class="card an mb3">
    <div class="ch"><i class="fas fa-filter" style="color:var(--gm);"></i><h2>Filter Applications</h2></div>
    <div class="cb">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:140px;"><label class="fl" style="font-size:12px;">Eligibility</label>
            <select name="eligibility" class="fc" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Eligible"     {{ request('eligibility')==='Eligible'?'selected':'' }}>Eligible</option>
                <option value="For Review"   {{ request('eligibility')==='For Review'?'selected':'' }}>For Review</option>
                <option value="Not Eligible" {{ request('eligibility')==='Not Eligible'?'selected':'' }}>Not Eligible</option>
            </select></div>
            <div style="flex:1;min-width:140px;"><label class="fl" style="font-size:12px;">Scholarship</label>
            <select name="scholarship_id" class="fc" onchange="this.form.submit()">
                <option value="">All Scholarships</option>
                @foreach($scholarships as $sch)
                <option value="{{ $sch->id }}" {{ request('scholarship_id')==$sch->id?'selected':'' }}>{{ $sch->name }}</option>
                @endforeach
            </select></div>
            <div style="flex:1;min-width:140px;"><label class="fl" style="font-size:12px;">Min AI Score</label>
            <select name="min_score" class="fc" onchange="this.form.submit()">
                <option value="">Any Score</option>
                <option value="90" {{ request('min_score')==='90'?'selected':'' }}>90%+ (Top)</option>
                <option value="75" {{ request('min_score')==='75'?'selected':'' }}>75%+ (Eligible)</option>
                <option value="50" {{ request('min_score')==='50'?'selected':'' }}>50%+ (Review)</option>
            </select></div>
            <div style="flex:1;min-width:140px;"><label class="fl" style="font-size:12px;">Status</label>
            <select name="status" class="fc" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="Pending"  {{ request('status')==='Pending'?'selected':'' }}>Pending</option>
                <option value="Approved" {{ request('status')==='Approved'?'selected':'' }}>Approved</option>
                <option value="Rejected" {{ request('status')==='Rejected'?'selected':'' }}>Rejected</option>
            </select></div>
            <a href="{{ route('admin.ai.index') }}" class="btn btn-o btn-sm">Clear</a>
        </form>
    </div>
</div>

{{-- ═══ AUTO-RANKED TABLE ═══ --}}
<div class="card an">
    <div class="ch">
        <i class="fas fa-sort-amount-down" style="color:var(--gm);"></i>
        <h2>Applications — Ranked by AI Score</h2>
        <span class="badge b-p" style="margin-left:6px;">{{ $applications->total() }}</span>
        <div class="ch-acts"><span style="font-size:11px;color:var(--tm);">Highest AI score first &nbsp;·&nbsp; Physical applications only</span></div>
    </div>
    <div class="tw"><table>
        <thead>
            <tr>
                <th style="text-align:center;width:50px;">Rank</th>
                <th>Student</th>
                <th>Scholarship</th>
                <th>GWA</th>
                <th>AI Score</th>
                <th>AI Eligibility</th>
                <th>AI Reasoning</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($applications as $rank => $app)
        @php
            $sc  = $app->ai_score ?? 0;
            $sfC = $sc>=75?'ash':($sc>=50?'asm':'asl');
            $el  = $app->ai_eligibility ?? 'N/A';
            $elC = $el==='Eligible'?'el':($el==='For Review'?'rv':'no');
            $absRank = (($applications->currentPage()-1)*$applications->perPage())+$rank+1;
            $rowBg = $sc>=75?'background:#f0faf2;':($sc>=50?'background:#fffcf0;':'');
        @endphp
        <tr style="{{ $rowBg }}">
            <td style="text-align:center;">
                <div style="width:30px;height:30px;border-radius:50%;background:{{ $absRank<=3?'linear-gradient(135deg,#1a6b2f,#2d9e4f)':($sc>=75?'#d0f0d8':($sc>=50?'#fef3cd':'#fde8e6')) }};color:{{ $absRank<=3?'#f0c020':($sc>=75?'#0d6624':($sc>=50?'#a07c00':'#c0392b')) }};display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;margin:0 auto;">{{ $absRank }}</div>
            </td>
            <td>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div class="av av-s">{{ strtoupper(substr($app->student?->user?->name??'?',0,1)) }}</div>
                    <div>
                        <div class="fws" style="font-size:13px;">{{ $app->student?->user?->name ?? 'N/A' }}</div>
                        <div style="font-size:11px;color:var(--tm);">{{ $app->student?->course ?? '' }} · {{ $app->student?->year_level ?? '' }}</div>
                    </div>
                </div>
            </td>
            <td>
                <div class="fws" style="font-size:12px;">{{ \Illuminate\Support\Str::limit($app->scholarship?->name??'N/A',28) }}</div>
                <span class="badge {{ $app->scholarship?->type==='Government'?'b-p':($app->scholarship?->type==='Private'?'b-s':'b-i') }}" style="font-size:10px;">{{ $app->scholarship?->type }}</span>
            </td>
            <td class="mono fwb" style="color:var(--g);">{{ number_format($app->gwa,2) }}</td>
            <td style="min-width:120px;">
                <div style="display:flex;align-items:center;gap:7px;">
                    <div style="flex:1;"><div class="asb"><div class="asf {{ $sfC }}" style="width:{{ $sc }}%;"></div></div></div>
                    <span class="mono fwb" style="font-size:13px;color:{{ $sc>=75?'var(--gm)':($sc>=50?'var(--warn)':'var(--danger)') }}">{{ $sc }}%</span>
                </div>
            </td>
            <td>
                <span class="badge elig-{{ $elC }}">{{ $el }}</span>
                @if($app->ai_tag)<br><span style="font-size:10px;color:var(--tm);margin-top:2px;display:block;">{{ $app->ai_tag }}</span>@endif
            </td>
            <td style="max-width:180px;font-size:11px;color:var(--tm);line-height:1.5;">{{ \Illuminate\Support\Str::limit($app->ai_reasoning??'—',85) }}</td>
            <td><span class="badge {{ $app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w') }}">{{ $app->status }}</span></td>
            <td>
                <div style="display:flex;gap:4px;">
                    <a href="{{ route('admin.applications.show',$app->id) }}" class="btn btn-o btn-sm btn-ic" title="View"><i class="fas fa-eye"></i></a>
                    @if($app->status==='Pending')
                    <form method="POST" action="{{ route('admin.applications.approve',$app->id) }}">@csrf
                        <button class="btn btn-s btn-sm btn-ic" title="Approve (AI Score: {{ $sc }}%)"><i class="fas fa-check"></i></button>
                    </form>
                    <form method="POST" action="{{ route('admin.applications.reject',$app->id) }}">@csrf
                        <button class="btn btn-d btn-sm btn-ic" title="Reject"><i class="fas fa-times"></i></button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:28px;color:var(--tm);">No applications found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($applications->hasPages())
    <div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $applications->appends(request()->query())->links() }}</div>
    @endif
</div>

{{-- App data for JS --}}
<div id="jsAppData" style="display:none;">{{ json_encode($applications->map(function($a){
    return [
        'name'        => $a->student?->user?->name ?? 'N/A',
        'course'      => $a->student?->course ?? '',
        'year'        => $a->student?->year_level ?? '',
        'gwa'         => $a->gwa,
        'scholarship' => $a->scholarship?->name ?? 'N/A',
        'type'        => $a->scholarship?->type ?? '',
        'ai_score'    => $a->ai_score ?? 0,
        'eligibility' => $a->ai_eligibility ?? 'N/A',
        'status'      => $a->status,
        'tag'         => $a->ai_tag ?? '',
        'reasoning'   => \Illuminate\Support\Str::limit($a->ai_reasoning ?? '',100),
    ];
})) }}</div>

<div id="jsStatsData" style="display:none;">{{ json_encode($stats) }}</div>

<div id="jsSchData" style="display:none;">{{ json_encode($scholarships->map(function($s){
    return [
        'id'   => $s->id,
        'name' => $s->name,
        'type' => $s->type ?? '',
        'ai_criteria' => is_array($s->ai_criteria) ? $s->ai_criteria : json_decode($s->ai_criteria ?? '{}', true),
    ];
})) }}</div>

<style>@keyframes aiSpin{to{transform:rotate(360deg);}}</style>

<script>
(function(){
    var GK    = document.getElementById('gk').value;
    var APPS  = JSON.parse(document.getElementById('jsAppData').textContent || '[]');
    var STATS = JSON.parse(document.getElementById('jsStatsData').textContent || '{}');
    var SCHS  = JSON.parse(document.getElementById('jsSchData').textContent || '[]');

    function callGemini(prompt, onSuccess, onError, maxTokens){
        if(!GK||!GK.trim()){ onError('GROQ_API_KEY is missing. Add to .env and run: php artisan config:clear'); return; }
        fetch('https://api.groq.com/openai/v1/chat/completions', {
            method:'POST', headers:{'Content-Type':'application/json','Authorization':'Bearer '+GK},
            body: JSON.stringify({model:'llama-3.1-8b-instant',messages:[{role:'user',content:prompt}],max_tokens:maxTokens||500,temperature:0.6})
        })
        .then(function(r){ return r.json().then(function(d){ return {ok:r.ok,status:r.status,data:d}; }); })
        .then(function(r){
            if(!r.ok){ onError('Groq AI Error: '+(r.data&&r.data.error?r.data.error.message:'HTTP '+r.status)); return; }
            var t=r.data&&r.data.choices&&r.data.choices[0]&&r.data.choices[0].message?r.data.choices[0].message.content:'';
            if(!t){ onError('Empty response from Groq. Please try again.'); return; }
            onSuccess(t);
        })
        .catch(function(e){ onError('Network error: '+e.message); });
    }

    function appSummary(){
        return APPS.map(function(a,i){
            return (i+1)+'. '+a.name+' | '+a.scholarship+' ('+a.type+') | GWA:'+a.gwa+' | Score:'+a.ai_score+'% | '+a.eligibility+(a.tag?' | '+a.tag:'')+' | '+a.status;
        }).join('\n');
    }

    function showAIResult(title, text){
        document.getElementById('aiResultTitle').textContent = title;
        document.getElementById('aiResultText').textContent = text;
        document.getElementById('aiResult').style.display = 'block';
        document.getElementById('aiResult').scrollIntoView({behavior:'smooth',block:'nearest'});
    }

    function showAIError(msg){
        document.getElementById('aiErrorMsg').textContent = msg;
        document.getElementById('aiError').style.display = 'block';
    }

    function setLoading(on, msg){
        document.getElementById('aiLoading').style.display = on?'block':'none';
        if(msg) document.getElementById('aiLoadingMsg').textContent = msg;
        document.getElementById('aiResult').style.display = 'none';
        document.getElementById('aiError').style.display = 'none';
    }

    window.runBatchAnalysis = function(){
        var btn = document.getElementById('batchBtn');
        btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Analyzing...';
        setLoading(true,'Groq AI is running batch analysis on '+APPS.length+' applications...');

        var p = 'You are ISAMS AI for admin officers at Saint Columban College, Pagadian City, Philippines.\n\n'
            +'STATISTICS:\nTotal:'+STATS.total+' | Eligible:'+STATS.eligible+' | Review:'+STATS.review+' | Not Eligible:'+STATS.not_eligible+' | Avg Score:'+STATS.avg_score+'%\n\n'
            +'APPLICATIONS (ranked by AI score):\n'+appSummary()+'\n\n'
            +'Generate a structured BATCH ANALYSIS REPORT with:\n'
            +'1. EXECUTIVE SUMMARY: Overall picture in 2-3 sentences.\n'
            +'2. TOP PRIORITY APPROVALS: Applicants with 75%+ score who should be approved first. List name, scholarship, score, and reason.\n'
            +'3. FOR REVIEW: 50-74% applicants and what documents to verify from them.\n'
            +'4. NOT RECOMMENDED: Below 50% applicants and why they do not qualify yet.\n'
            +'5. ADMIN ACTION ITEMS: 3-5 concrete next steps for the scholarship committee.\n\n'
            +'Be specific with names and scores. Plain text only. No asterisks. Professional tone.';

        callGemini(p, function(text){
            setLoading(false);
            showAIResult('Batch Analysis Report', text);
            btn.disabled=false; btn.innerHTML='<i class="fas fa-play"></i> Run Batch Analysis';
        }, function(err){
            setLoading(false); showAIError(err);
            btn.disabled=false; btn.innerHTML='<i class="fas fa-play"></i> Run Batch Analysis';
        }, 700);
    };

    window.generateApprovalReport = function(){
        var btn = document.getElementById('approvalBtn');
        btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Generating...';
        setLoading(true,'Generating approval recommendation report...');

        var p = 'You are ISAMS AI for admin officers at Saint Columban College, Pagadian City, Philippines.\n\n'
            +'APPLICATIONS:\n'+appSummary()+'\n\n'
            +'Generate a formal SCHOLARSHIP APPROVAL RECOMMENDATION REPORT that can be used by the scholarship committee. Include:\n'
            +'1. RECOMMENDED FOR APPROVAL: List each eligible applicant with their score, scholarship applied for, and one sentence justification.\n'
            +'2. RECOMMENDED FOR FURTHER REVIEW: Applicants needing document verification before approval decision.\n'
            +'3. NOT RECOMMENDED: Applicants who do not meet minimum requirements, with specific reason per applicant.\n'
            +'4. COMMITTEE NOTES: Any patterns or concerns the committee should be aware of.\n\n'
            +'Format as a formal document. Plain text only. No asterisks. Professional.';

        callGemini(p, function(text){
            setLoading(false);
            showAIResult('Scholarship Approval Recommendation Report', text);
            btn.disabled=false; btn.innerHTML='<i class="fas fa-clipboard-check"></i> Approval Report';
        }, function(err){
            setLoading(false); showAIError(err);
            btn.disabled=false; btn.innerHTML='<i class="fas fa-clipboard-check"></i> Approval Report';
        }, 700);
    };

    window.askAdmin = function(question){
        if(!question||!question.trim()) return;
        document.getElementById('adminInput').value = question;
        setLoading(true,'Analyzing...');

        var p = 'You are ISAMS AI for admin at Saint Columban College, Philippines.\n\n'
            +'Stats: Total:'+STATS.total+' | Eligible:'+STATS.eligible+' | Review:'+STATS.review+' | Not Eligible:'+STATS.not_eligible+' | Avg:'+STATS.avg_score+'%\n\n'
            +'Applications:\n'+appSummary()+'\n\n'
            +'Admin asks: "'+question+'"\n\n'
            +'Answer helpfully for an admin officer. Plain text, no asterisks. Max 200 words.';

        callGemini(p, function(text){
            setLoading(false);
            showAIResult('AI Response', text);
        }, function(err){
            setLoading(false); showAIError(err);
        }, 350);
    };

    window.screenApplicant = function(){
        var name     = document.getElementById('screenName').value.trim();
        var gwa      = parseFloat(document.getElementById('screenGWA').value) || 5.0;
        var enroll   = document.getElementById('screenEnroll').value;
        var income   = document.getElementById('screenIncome').value;
        var failing  = document.getElementById('screenFailing').value;
        var schEl    = document.getElementById('screenScholarship');
        var schName  = schEl.options[schEl.selectedIndex]?schEl.options[schEl.selectedIndex].text:'Not specified';

        if(!name){ alert('Please enter the applicant name.'); return; }

        var btn = document.getElementById('screenBtn');
        btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Screening...';
        document.getElementById('screenLoading').style.display='block';
        document.getElementById('screenResult').style.display='none';

        var p = 'You are ISAMS AI scholarship screener at Saint Columban College, Pagadian City, Philippines.\n\n'
            +'WALK-IN APPLICANT DETAILS:\n'
            +'Name: '+name+'\nGWA: '+gwa+'\nEnrollment: '+enroll+'\nFamily Income: '+income+'\nFailing Grades: '+failing+'\nScholarship: '+schName+'\n\n'
            +'AVAILABLE SCHOLARSHIPS:\n'+SCHS.map(function(s){ return s.name+' ('+s.type+')'; }).join('\n')+'\n\n'
            +'Calculate an AI eligibility score (0-100) using: GWA up to 40pts, Enrollment up to 20pts, No Failing up to 20pts, Income up to 15pts, Discipline 10pts.\n\n'
            +'Provide:\n'
            +'1. ELIGIBILITY SCORE: X/100 and label (Eligible/For Review/Not Eligible)\n'
            +'2. SCORE BREAKDOWN: Points earned per criterion\n'
            +'3. RECOMMENDATION: Whether to accept their physical application\n'
            +'4. DOCUMENTS TO REQUEST: What to ask them to submit\n'
            +'5. NOTES: Any concerns or special considerations\n\n'
            +'Be concise and direct. Plain text only. No asterisks.';

        callGemini(p, function(text){
            document.getElementById('screenLoading').style.display='none';
            var score = text.match(/(\d{1,3})\s*\/\s*100/);
            var sc = score ? parseInt(score[1]) : 50;
            var hdr = document.getElementById('screenResultHeader');
            var scCol = sc>=75?'#0d6624':sc>=50?'#a07c00':'#c0392b';
            var scBg  = sc>=75?'#d0f0d8':sc>=50?'#fef3cd':'#fde8e6';
            hdr.style.background = scBg;
            document.getElementById('screenResultTitle').style.color = scCol;
            document.getElementById('screenResultTitle').textContent = name + ' — Screening Result';
            document.getElementById('screenResultScore').style.color = scCol;
            document.getElementById('screenResultScore').textContent = sc + '/100';
            document.getElementById('screenResultBody').textContent = text;
            document.getElementById('screenResult').style.display='block';
            btn.disabled=false; btn.innerHTML='<i class="fas fa-search"></i> Screen This Applicant';
        }, function(err){
            document.getElementById('screenLoading').style.display='none';
            document.getElementById('screenResult').style.display='block';
            document.getElementById('screenResultHeader').style.background='#fde8e6';
            document.getElementById('screenResultTitle').textContent='Error';
            document.getElementById('screenResultTitle').style.color='#c0392b';
            document.getElementById('screenResultScore').textContent='';
            document.getElementById('screenResultBody').textContent=err;
            btn.disabled=false; btn.innerHTML='<i class="fas fa-search"></i> Screen This Applicant';
        }, 400);
    };

    window.printResult = function(){
        var content = document.getElementById('aiResultText').textContent;
        var title   = document.getElementById('aiResultTitle').textContent;
        var win = window.open('','_blank');
        win.document.write('<html><head><title>'+title+'</title><style>body{font-family:Arial,sans-serif;padding:30px;font-size:13px;line-height:1.8;color:#1a2e1a;}h1{color:#1a6b2f;border-bottom:2px solid #1a6b2f;padding-bottom:8px;}pre{white-space:pre-wrap;font-family:inherit;}</style></head><body>');
        win.document.write('<h1>ISAMS — '+title+'</h1>');
        win.document.write('<p style="color:#5a7a60;font-size:12px;">Saint Columban College · Pagadian City · Generated: '+new Date().toLocaleString()+'</p>');
        win.document.write('<pre>'+content+'</pre>');
        win.document.write('</body></html>');
        win.document.close();
        win.print();
    };
})();
</script>
@endsection
