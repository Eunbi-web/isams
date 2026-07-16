@extends('student.layouts.app')
@section('title','Browse Scholarships')
@section('page-title','Scholarship Programs')
@section('page-sub','Click any scholarship card to see full details and requirements')
@section('content')

{{-- Filter & Search Bar --}}
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
    <div style="display:flex;gap:6px;flex-wrap:wrap;flex:1;" id="filterBtns">
        @foreach(['All','Government','Institutional','Private'] as $f)
        <button type="button" onclick="filterCards('{{ $f }}',this)" class="btn {{ $loop->first?'btn-p':'btn-o' }} btn-sm" style="border-radius:20px;">{{ $f }}</button>
        @endforeach
    </div>
    <div style="position:relative;">
        <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;pointer-events:none;"></i>
        <input type="text" id="schSearch" class="fc" placeholder="Search scholarships..." style="padding-left:30px;width:200px;">
    </div>
</div>



{{-- Scholarship Grid (compact cards only) --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:16px;" id="schGrid">

{{-- ── ISAMS ACTIVE SCHOLARSHIPS ── --}}
@forelse($scholarships as $sch)
@php
    $slots = $sch->slots ?? 0;
    $used  = $sch->applications->where('status','Approved')->count();
    $rem   = max(0, $slots - $used);
@endphp
<div class="sch-card an" data-type="{{ $sch->type }}" data-id="sys-{{ $sch->id }}" onclick="openDetail('sys-{{ $sch->id }}')">
    <div style="padding:16px 17px;display:flex;align-items:flex-start;gap:11px;">
        <div style="width:10px;height:10px;border-radius:50%;background:var(--gm);flex-shrink:0;margin-top:5px;box-shadow:0 0 6px var(--gm);"></div>
        <div style="flex:1;min-width:0;">
            <div class="fws" style="font-size:14px;line-height:1.3;">{{ $sch->name }}</div>
            <div class="tm" style="font-size:11px;margin-top:2px;">{{ $sch->source ?? $sch->type }}</div>
        </div>
        <span class="badge {{ $sch->type==='Government'?'b-p':($sch->type==='Private'?'b-s':'b-i') }}" style="font-size:10px;flex-shrink:0;">{{ $sch->type }}</span>
    </div>
    <div style="padding:0 17px 14px;display:flex;gap:14px;align-items:center;">
        <div style="font-size:11px;color:var(--tm);"><i class="fas fa-gift" style="color:var(--gm);margin-right:3px;"></i>{{ Str::limit($sch->benefits??'—',32) }}</div>
        @if($rem > 0)
        <div style="font-size:11px;color:{{ $rem<=5?'var(--danger)':'var(--gm)' }};margin-left:auto;flex-shrink:0;font-weight:600;white-space:nowrap;">{{ $rem }} slots</div>
        @endif
        <i class="fas fa-expand-alt" style="color:var(--tm);font-size:11px;flex-shrink:0;"></i>
    </div>
</div>
@empty
<div style="grid-column:1/-1;text-align:center;padding:28px;color:var(--tm);">No active scholarship programs at the moment.</div>
@endforelse

{{-- ── PH SYNCED SCHOLARSHIPS ── --}}
@foreach($synced as $s)
<div class="sch-card an" data-type="{{ $s->source_type }}" data-id="syn-{{ $s->id }}" onclick="openDetail('syn-{{ $s->id }}')">
    <div style="padding:16px 17px;display:flex;align-items:flex-start;gap:11px;">
        <div style="width:10px;height:10px;border-radius:50%;background:#0038a8;flex-shrink:0;margin-top:5px;"></div>
        <div style="flex:1;min-width:0;">
            <div class="fws" style="font-size:14px;line-height:1.3;">{{ $s->name }}</div>
            <div class="tm" style="font-size:11px;margin-top:2px;">{{ $s->source_agency }}</div>
        </div>
        <span class="badge {{ $s->source_type==='Government'?'b-p':($s->source_type==='Private'?'b-s':'b-i') }}" style="font-size:10px;flex-shrink:0;">{{ $s->source_type }}</span>
    </div>
    <div style="padding:0 17px 14px;display:flex;gap:14px;align-items:center;">
        <div style="font-size:11px;color:var(--tm);"><i class="fas fa-gift" style="color:#0038a8;margin-right:3px;"></i>{{ Str::limit($s->benefits??'See site',32) }}</div>
        <div style="font-size:11px;margin-left:auto;flex-shrink:0;white-space:nowrap;"><span style="display:inline-flex;align-items:center;gap:4px;"><span style="width:6px;height:6px;border-radius:50%;background:{{ $s->is_open?'var(--gm)':'var(--danger)' }};"></span>{{ $s->is_open?'Open':'Closed' }}</span></div>
        <i class="fas fa-expand-alt" style="color:var(--tm);font-size:11px;flex-shrink:0;"></i>
    </div>
</div>
@endforeach

</div>

{{-- ═══════════════ HOVER-EXPAND OVERLAY PANELS (hidden, full content) ═══════════════ --}}

{{-- Overlay backdrop --}}
<div class="detail-overlay" id="detailOverlay" onclick="closeDetail()"></div>

{{-- ISAMS scholarship detail panels --}}
@foreach($scholarships as $sch)
@php
    $slots = $sch->slots ?? 0;
    $used  = $sch->applications->where('status','Approved')->count();
    $rem   = max(0, $slots - $used);
    $pct   = $slots > 0 ? round(($used/$slots)*100) : 0;
    $criteria = is_array($sch->ai_criteria) ? $sch->ai_criteria : json_decode($sch->ai_criteria ?? '{}', true);
    $myApp = auth()->user()->student ? $sch->applications->where('student_id', auth()->user()->student->id)->first() : null;
@endphp
<div class="detail-panel" id="detail-sys-{{ $sch->id }}">
    <div class="detail-panel-inner">
        <button type="button" class="detail-close" onclick="closeDetail()"><i class="fas fa-times"></i> Close</button>
        <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:18px;padding-right:90px;">
            <div class="si y" style="width:50px;height:50px;font-size:20px;border-radius:13px;flex-shrink:0;"><i class="fas fa-award"></i></div>
            <div>
                <div style="font-family:'Sora',sans-serif;font-size:19px;font-weight:800;color:var(--g);line-height:1.3;">{{ $sch->name }}</div>
                <div style="font-size:12px;color:var(--tm);margin-top:3px;">{{ $sch->source ?? $sch->type }}</div>
                <span class="badge {{ $sch->type==='Government'?'b-p':($sch->type==='Private'?'b-s':'b-i') }}" style="font-size:10px;margin-top:6px;display:inline-flex;">{{ $sch->type }}</span>
            </div>
        </div>

        <div style="background:var(--gp);border-radius:var(--rs);padding:13px 15px;margin-bottom:14px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--g);margin-bottom:5px;"><i class="fas fa-gift" style="margin-right:5px;"></i>Benefit Package</div>
            <div style="font-size:14px;color:var(--tx);">{{ $sch->benefits ?? 'See official announcement.' }}</div>
        </div>

        <div style="margin-bottom:14px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tm);margin-bottom:8px;"><i class="fas fa-list-ul" style="margin-right:5px;"></i>Requirements & Eligibility</div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;">
                    <i class="fas fa-star" style="color:var(--yd);width:16px;text-align:center;"></i><span style="color:var(--tm);">Minimum GWA:</span>
                    <strong style="color:var(--g);margin-left:auto;">{{ isset($criteria['gwa_max']) ? number_format($criteria['gwa_max'],2).' or better' : 'See requirements' }}</strong>
                </div>
                <div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;">
                    <i class="fas fa-id-badge" style="color:var(--info);width:16px;text-align:center;"></i><span style="color:var(--tm);">Enrollment:</span>
                    <strong style="color:var(--g);margin-left:auto;">Regular enrollment required</strong>
                </div>
                @if(!empty($criteria['no_failing']))
                <div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;">
                    <i class="fas fa-graduation-cap" style="color:var(--gm);width:16px;text-align:center;"></i><span style="color:var(--tm);">Academic Standing:</span>
                    <strong style="color:var(--g);margin-left:auto;">No failing grades</strong>
                </div>
                @endif
                @if(!empty($criteria['no_discipline']))
                <div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;">
                    <i class="fas fa-gavel" style="color:var(--warn);width:16px;text-align:center;"></i><span style="color:var(--tm);">Disciplinary Record:</span>
                    <strong style="color:var(--g);margin-left:auto;">No active case</strong>
                </div>
                @endif
                @if(!empty($criteria['income_max']))
                <div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;">
                    <i class="fas fa-hand-holding-usd" style="color:var(--gm);width:16px;text-align:center;"></i><span style="color:var(--tm);">Family Income:</span>
                    <strong style="color:var(--g);margin-left:auto;">Below ₱{{ number_format($criteria['income_max']) }}/year</strong>
                </div>
                @endif
                <div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;">
                    <i class="fas fa-flag" style="color:#0038a8;width:16px;text-align:center;"></i><span style="color:var(--tm);">Citizenship:</span>
                    <strong style="color:var(--g);margin-left:auto;">Filipino citizen</strong>
                </div>
                @if($sch->requirements)
                <div style="padding:10px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;color:var(--tm);line-height:1.7;">
                    <i class="fas fa-file-alt" style="color:var(--g);margin-right:6px;"></i>{{ $sch->requirements }}
                </div>
                @endif
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
            <div style="background:var(--bg);border-radius:var(--rs);padding:12px;text-align:center;">
                <div style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Available Slots</div>
                <div style="font-family:'Sora',sans-serif;font-size:24px;font-weight:800;color:{{ $rem<=5?'var(--danger)':'var(--g)' }};">{{ $rem }}</div>
                <div style="font-size:11px;color:var(--tm);">of {{ $slots }} total</div>
                <div class="slot-bar" style="margin-top:7px;"><div class="slot-fill" style="width:{{ $pct }}%;background:{{ $pct>=90?'linear-gradient(90deg,var(--danger),#e87070)':($pct>=70?'linear-gradient(90deg,var(--warn),#f0b429)':'linear-gradient(90deg,var(--g),var(--gm))') }};"></div></div>
            </div>
            <div style="background:var(--bg);border-radius:var(--rs);padding:12px;text-align:center;">
                <div style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Deadline</div>
                @if($sch->end_date)
                <div style="font-family:'Sora',sans-serif;font-size:16px;font-weight:800;color:var(--g);margin-top:5px;">{{ $sch->end_date->format('M d, Y') }}</div>
                <div style="font-size:10px;color:{{ $sch->end_date->isPast()?'var(--danger)':($sch->end_date->diffInDays()<=7?'var(--warn)':'var(--gm)') }};font-weight:700;margin-top:4px;">
                    {{ $sch->end_date->isPast()?'Deadline passed':($sch->end_date->diffInDays().' days left') }}
                </div>
                @else
                <div style="font-size:13px;color:var(--tm);margin-top:10px;">Open / TBA</div>
                @endif
            </div>
        </div>

        @if($myApp)
        <div style="background:{{ $myApp->ai_eligibility==='Eligible'?'#d0f0d8':($myApp->ai_eligibility==='For Review'?'#fef3cd':'#fde8e6') }};border-radius:var(--rs);padding:12px 14px;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-robot" style="color:{{ $myApp->ai_eligibility==='Eligible'?'var(--gm)':($myApp->ai_eligibility==='For Review'?'var(--yd)':'var(--danger)') }};font-size:18px;"></i>
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--tx);">Your AI Score: {{ $myApp->ai_score }}% — {{ $myApp->ai_eligibility }}</div>
                <div style="font-size:12px;color:var(--tm);">Status: {{ $myApp->status }}</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endforeach

{{-- PH Synced detail panels --}}
@foreach($synced as $s)
<div class="detail-panel" id="detail-syn-{{ $s->id }}">
    <div class="detail-panel-inner">
        <button type="button" class="detail-close" onclick="closeDetail()"><i class="fas fa-times"></i> Close</button>
        <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:18px;padding-right:90px;">
            <div class="si" style="width:50px;height:50px;font-size:20px;border-radius:13px;flex-shrink:0;background:#e8eef8;color:#0038a8;"><i class="fas fa-flag"></i></div>
            <div>
                <div style="font-family:'Sora',sans-serif;font-size:19px;font-weight:800;color:var(--g);line-height:1.3;">{{ $s->name }}</div>
                <div style="font-size:12px;color:var(--tm);margin-top:3px;">{{ $s->source_agency }}</div>
                <span class="badge {{ $s->source_type==='Government'?'b-p':($s->source_type==='Private'?'b-s':'b-i') }}" style="font-size:10px;margin-top:6px;display:inline-flex;">{{ $s->source_type }}</span>
            </div>
        </div>

        <div style="background:#e8eef8;border-radius:var(--rs);padding:13px 15px;margin-bottom:14px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#0038a8;margin-bottom:5px;"><i class="fas fa-gift" style="margin-right:5px;"></i>Benefit Package</div>
            <div style="font-size:14px;color:var(--tx);">{{ $s->benefits ?? 'Visit official website for benefit details.' }}</div>
        </div>

        <div style="margin-bottom:14px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tm);margin-bottom:8px;"><i class="fas fa-list-ul" style="margin-right:5px;"></i>Requirements & Eligibility</div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;align-items:center;gap:8px;padding:9px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;">
                    <i class="fas fa-flag" style="color:#0038a8;width:16px;text-align:center;"></i><span style="color:var(--tm);">Citizenship:</span>
                    <strong style="color:var(--g);margin-left:auto;">Filipino citizen</strong>
                </div>
                @if($s->requirements)
                <div style="padding:10px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;color:var(--tm);line-height:1.7;">
                    <i class="fas fa-file-alt" style="color:var(--g);margin-right:6px;"></i>{{ $s->requirements }}
                </div>
                @else
                <div style="padding:10px 12px;background:var(--bg);border-radius:var(--rs);font-size:13px;color:var(--tm);">
                    <i class="fas fa-external-link-alt" style="margin-right:6px;"></i>Visit the official website for full requirements.
                </div>
                @endif
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
            <div style="background:var(--bg);border-radius:var(--rs);padding:12px;text-align:center;">
                <div style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Available Slots</div>
                <div style="font-family:'Sora',sans-serif;font-size:24px;font-weight:800;color:var(--g);">{{ $s->slots ?? '—' }}</div>
            </div>
            <div style="background:var(--bg);border-radius:var(--rs);padding:12px;text-align:center;">
                <div style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Deadline</div>
                @if($s->deadline)
                <div style="font-family:'Sora',sans-serif;font-size:16px;font-weight:800;color:var(--g);margin-top:5px;">{{ \Carbon\Carbon::parse($s->deadline)->format('M d, Y') }}</div>
                @else
                <div style="font-size:13px;color:var(--tm);margin-top:10px;">Open / TBA</div>
                @endif
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:8px;padding:11px 13px;background:var(--bg);border-radius:var(--rs);font-size:13px;margin-bottom:14px;">
            <span style="width:8px;height:8px;border-radius:50%;background:{{ $s->is_open?'var(--gm)':'var(--danger)' }};flex-shrink:0;"></span>
            <span style="color:var(--tm);">{{ $s->is_open ? 'Currently accepting applications' : 'Not accepting / Closed' }}</span>
        </div>

        <div style="background:var(--yp);border:1px solid var(--yd);border-radius:var(--rs);padding:11px 14px;font-size:13px;color:#6b4a00;margin-bottom:14px;">
            <i class="fas fa-info-circle" style="margin-right:6px;color:var(--yd);"></i>This scholarship is sourced from an official Philippine website. Visit the official site for requirements and to apply.
        </div>
        <a href="{{ $s->source_url }}" target="_blank" class="btn btn-o" style="justify-content:center;border-color:#0038a8;color:#0038a8;width:100%;">
            <i class="fas fa-external-link-alt"></i> Visit Official Website
        </a>
    </div>
</div>
@endforeach

@push('styles')
<style>
.sch-card{background:var(--card);border-radius:var(--r);border:1px solid var(--bd);box-shadow:var(--shadow);overflow:hidden;transition:box-shadow .2s,border-color .2s,transform .2s;cursor:pointer;}
.sch-card:hover{box-shadow:0 8px 24px rgba(26,107,47,.16);border-color:var(--gm);transform:translateY(-2px);}

.detail-overlay{
    position:fixed;inset:0;background:rgba(13,30,18,.6);backdrop-filter:blur(3px);
    z-index:998;display:none;opacity:0;transition:opacity .25s ease;
}
.detail-overlay.open{display:block;opacity:1;}

.detail-panel{
    position:fixed;
    top:50%;left:50%;
    transform:translate(-50%,-50%) scale(.92);
    width:92%;max-width:760px;
    max-height:88vh;
    background:var(--card);
    border-radius:18px;
    box-shadow:0 30px 80px rgba(0,0,0,.35);
    z-index:999;
    display:none;
    opacity:0;
    overflow:hidden;
    transition:opacity .25s ease, transform .25s ease;
}
.detail-panel.open{display:block;opacity:1;transform:translate(-50%,-50%) scale(1);}
.detail-panel-inner{padding:26px 28px;overflow-y:auto;max-height:88vh;}

.detail-close{
    position:absolute;top:18px;right:20px;
    background:var(--bg);border:1.5px solid var(--bd);border-radius:20px;
    padding:7px 16px;font-size:13px;font-weight:600;color:var(--tm);
    cursor:pointer;display:inline-flex;align-items:center;gap:6px;
    transition:all .2s;z-index:2;
}
.detail-close:hover{background:var(--danger);border-color:var(--danger);color:#fff;}

@media(max-width:600px){
    .detail-panel{width:96%;max-height:92vh;}
    .detail-panel-inner{padding:60px 18px 20px;}
    .detail-close{top:12px;right:12px;padding:6px 12px;font-size:12px;}
}
</style>
@endpush

@push('scripts')
<script>
let currentOpenPanel = null;

function openDetail(id) {
    closeDetail(); // close any other open panel first
    const panel   = document.getElementById('detail-' + id);
    const overlay = document.getElementById('detailOverlay');
    if (!panel) return;
    overlay.classList.add('open');
    panel.classList.add('open');
    document.body.style.overflow = 'hidden';
    currentOpenPanel = panel;
}

function closeDetail() {
    document.querySelectorAll('.detail-panel.open').forEach(p => p.classList.remove('open'));
    document.getElementById('detailOverlay').classList.remove('open');
    document.body.style.overflow = '';
    currentOpenPanel = null;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDetail();
});

function filterCards(f, btn) {
    document.querySelectorAll('#filterBtns button').forEach(function(b){
        b.className = 'btn btn-o btn-sm'; b.style.borderRadius = '20px';
    });
    btn.className = 'btn btn-p btn-sm'; btn.style.borderRadius = '20px';
    document.querySelectorAll('#schGrid .sch-card').forEach(function(c){
        c.style.display = (f === 'All' || c.dataset.type === f) ? '' : 'none';
    });
}

document.getElementById('schSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#schGrid .sch-card').forEach(function(c){
        c.style.display = c.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection