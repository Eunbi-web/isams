@extends('student.layouts.app')
@section('title','Counseling')
@section('page-title','Guidance Counseling')
@if($setting->scheduling_mode === 'slots')
@section('page-sub','Book a counseling session — pick a date and an open time slot')
@else
@section('page-sub','Request a session — auto-queued, all requests accepted')
@endif
@section('content')
@if($setting->scheduling_mode === 'slots')
<div class="alert al-i an"><i class="fas fa-calendar-days"></i><span><strong>Time Slot Booking:</strong> Choose an available date on the calendar, then pick an open time slot. Dates and slots that are fully booked are greyed out.</span></div>
@else
<div class="alert al-i an"><i class="fas fa-info-circle"></i><span><strong>Auto-Queue:</strong> All counseling requests are automatically accepted and queued. No requests are declined.</span></div>
@endif
<div class="g2" style="align-items:start;">
<div class="card an"><div class="ch"><i class="fas fa-plus-circle" style="color:var(--gm);"></i><h2>{{ $setting->scheduling_mode === 'slots' ? 'Book Counseling Session' : 'Request Counseling Session' }}</h2></div><div class="cb">
<form method="POST" action="{{ route('student.counseling.store') }}" id="counselingForm">@csrf
<input type="hidden" name="session_date" id="session_date" value="{{ old('session_date') }}">
<input type="hidden" name="session_time" id="session_time" value="{{ old('session_time') }}">
<div class="fg"><label class="fl">Type of Concern <span style="color:var(--danger);">*</span></label>
<select name="concern_type" id="concern_type" class="fc" required onchange="toggleOtherConcern(this)">
    <option value="">Select concern type...</option>
    @foreach(['Academic Stress','Personal Issue','Career Guidance','Mental Health / Anxiety','Family Concern','Financial Stress','Relationship Issue','General Wellness'] as $type)
    <option value="{{ $type }}">{{ $type }}</option>
    @endforeach
    <option value="Other Concern">Other Concern</option>
</select>
<div id="other_concern_wrapper" style="display:none;margin-top:7px;">
    <input type="text" name="other_concern_text" id="other_concern_text" class="fc" placeholder="Please specify your concern...">
</div>
</div>
<div class="fg"><label class="fl">Priority Level</label><div style="display:grid;grid-template-columns:repeat(3,1fr);gap:7px;">@foreach([['Normal','fas fa-circle'],['Medium','fas fa-exclamation'],['Urgent','fas fa-exclamation-triangle']] as $p)<label style="display:flex;align-items:center;gap:6px;padding:8px 10px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;font-size:13px;"><input type="radio" name="priority" value="{{ $p[0] }}" {{ $p[0]==='Normal'?'checked':'' }} style="accent-color:var(--g);"><i class="{{ $p[1] }}" style="font-size:11px;"></i> {{ $p[0] }}</label>@endforeach</div></div>

@if($setting->scheduling_mode === 'slots')
<div class="fg"><label class="fl"><i class="fas fa-calendar-days" style="font-size:11px;color:var(--gm);"></i> Select a Date <span style="color:var(--danger);">*</span></label>
<div id="slotCalendar">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
        <button type="button" class="btn btn-o btn-sm btn-ic" onclick="calNav(-1)" aria-label="Previous month"><i class="fas fa-chevron-left"></i></button>
        <span id="calLabel" class="fws" style="font-size:13px;"></span>
        <button type="button" class="btn btn-o btn-sm btn-ic" onclick="calNav(1)" aria-label="Next month"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div id="calGrid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;"></div>
    <div class="tm" style="font-size:10.5px;margin-top:7px;display:flex;gap:12px;flex-wrap:wrap;">
        <span><span style="display:inline-block;width:9px;height:9px;border-radius:3px;background:var(--gp);border:1px solid var(--gm);margin-right:4px;"></span>Available</span>
        <span><span style="display:inline-block;width:9px;height:9px;border-radius:3px;background:#c9c9c9;margin-right:4px;"></span>Fully booked / unavailable</span>
    </div>
</div>
</div>
<div class="fg"><label class="fl">Time Slot <span style="color:var(--danger);">*</span></label>
<div id="slotList" style="display:grid;grid-template-columns:1fr 1fr;gap:7px;">
<div class="tm" style="grid-column:1/-1;font-size:12px;">Select a date above to see available time slots.</div>
</div>
</div>
<div id="slotSummary" class="alert al-s" style="display:none;font-size:12px;"><i class="fas fa-calendar-check"></i> <span id="slotSummaryText"></span></div>
@else
<div class="fg"><label class="fl">Preferred Date <span style="font-size:11px;color:var(--tm);font-weight:400;">(optional)</span></label><input type="date" name="preferred_date" class="fc" min="{{ date('Y-m-d',strtotime('+1 day')) }}"></div>
<div class="fg"><label class="fl">Preferred Time</label><select name="preferred_time" class="fc"><option value="">No preference</option><option>8:00 AM – 9:00 AM</option><option>9:00 AM – 10:00 AM</option><option>10:00 AM – 11:00 AM</option><option>1:00 PM – 2:00 PM</option><option>2:00 PM – 3:00 PM</option><option>3:00 PM – 4:00 PM</option></select></div>
@endif

<div class="fg"><label class="fl">Brief Description</label><textarea name="concern_detail" class="fc" rows="4" placeholder="Briefly describe what you'd like to discuss..."></textarea></div>
<div style="background:var(--gp);border-radius:var(--rs);padding:11px 13px;margin-bottom:14px;font-size:12px;color:var(--g);"><i class="fas fa-shield-alt" style="margin-right:6px;color:var(--gm);"></i><strong>Confidential:</strong> Your session details are strictly confidential.</div>
<button type="submit" class="btn btn-p" style="width:100%;justify-content:center;"><i class="fas fa-paper-plane"></i> {{ $setting->scheduling_mode === 'slots' ? 'Book Session' : 'Submit Request — Auto Queue' }}</button>
</form></div></div>
<div>
<div class="card an mb3"><div class="ch"><i class="fas fa-list" style="color:var(--gm);"></i><h2>My Sessions</h2></div>
<div class="cb" style="padding:0;">
@forelse($sessions as $ses)
<div style="display:flex;align-items:center;gap:11px;padding:13px 18px;border-bottom:1px solid var(--bd);">
<div style="width:40px;height:40px;border-radius:11px;background:{{ $ses->status==='Completed'?'#d0f0d8':($ses->status==='Scheduled'?'var(--gp)':'#e0f3f8') }};display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-{{ $ses->status==='Completed'?'check-circle':($ses->status==='Scheduled'?'calendar-check':'clock') }}" style="color:{{ $ses->status==='Completed'?'#0d6624':($ses->status==='Scheduled'?'var(--gm)':'var(--info)') }};font-size:15px;"></i></div>
<div style="flex:1;"><div class="fws" style="font-size:13px;">{{ $ses->concern_type }}</div><div class="tm" style="font-size:12px;">{{ $ses->session_date?->format('M d, Y')??($ses->preferred_date?->format('M d, Y') ?? ' — ') }} {{ $ses->session_time??$ses->preferred_time }}</div></div>
<span class="badge {{ $ses->status==='Completed'?'b-s':($ses->status==='Scheduled'?'b-p':'b-i') }}">{{ $ses->status }}</span>
</div>
@empty
<div style="padding:20px;text-align:center;color:var(--tm);font-size:13px;">No sessions yet. Submit a request above.</div>
@endforelse
</div></div>
@if($setting->scheduling_mode !== 'slots')
<div class="card an"><div class="ch" style="background:linear-gradient(135deg,var(--g),#1a5c28);"><i class="fas fa-stream" style="color:var(--y);"></i><h2 style="color:#fff;">Queue Status</h2></div>
<div class="cb" style="text-align:center;">
<div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,var(--info),#1a5580);display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;"><div style="font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:800;color:#fff;">#{{ $queuePos + 1 }}</div></div>
<div class="fws" style="font-size:14px;color:var(--g);">Queue Size</div>
<div class="tm" style="font-size:12px;margin-top:3px;">{{ $queuePos }} ahead in queue</div>
<div class="alert al-i mt3" style="margin-bottom:0;font-size:12px;"><i class="fas fa-bell"></i><span>You'll be notified when your session is scheduled.</span></div>
</div></div>
@endif
</div>
</div>
@endsection

@push('scripts')
<script>
function toggleOtherConcern(select) {
    const wrapper = document.getElementById('other_concern_wrapper');
    const textInput = document.getElementById('other_concern_text');
    if (select.value === 'Other Concern') {
        wrapper.style.display = 'block';
        textInput.setAttribute('required', 'required');
    } else {
        wrapper.style.display = 'none';
        textInput.removeAttribute('required');
    }
}

@if($setting->scheduling_mode === 'slots')
// ── Time slot booking calendar ──────────────────────────────────────────────
var calMonth = '{{ now()->format('Y-m') }}';
var calData = null, selectedDate = null, selectedSlot = null;
var MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

function calNav(dir){
    var d = new Date(calMonth + '-02');
    d.setMonth(d.getMonth() + dir);
    if (d < new Date('{{ now()->startOfMonth()->toDateString() }}')) return;
    calMonth = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
    loadCalendar();
}

function loadCalendar(){
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('{{ route('student.counseling.calendar') }}?month=' + calMonth, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
        .then(function(r){ return r.json(); })
        .then(function(d){
            calData = d.days;
            renderCalendar();
        });
}

function renderCalendar(){
    var grid = document.getElementById('calGrid');
    var label = document.getElementById('calLabel');
    var parts = calMonth.split('-');
    label.textContent = MONTH_NAMES[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
    var html = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'].map(function(d){
        return '<div style="text-align:center;font-size:10px;font-weight:700;color:var(--tm);padding:3px 0;">' + d + '</div>';
    }).join('');
    var first = new Date(parts[0], parseInt(parts[1], 10) - 1, 1);
    var startOffset = (first.getDay() + 6) % 7; // Monday-first
    for (var i = 0; i < startOffset; i++) html += '<div></div>';
    var dim = new Date(parts[0], parseInt(parts[1], 10), 0).getDate();
    var todayStr = new Date().toLocaleDateString('sv'); // YYYY-MM-DD local
    for (var day = 1; day <= dim; day++) {
        var key = calMonth + '-' + String(day).padStart(2, '0');
        var info = (calData && calData[key]) || { state: 'closed' };
        var isToday = key === todayStr;
        if (info.state === 'open') {
            html += '<button type="button" onclick="pickDate(\'' + key + '\')" data-day="' + key + '" style="border:1px solid var(--gm);background:var(--gp);color:var(--g);border-radius:8px;padding:7px 0;font-size:12px;font-weight:700;cursor:pointer;' + (isToday ? 'outline:2px solid var(--g);outline-offset:-3px;' : '') + (selectedDate === key ? 'background:var(--g);color:#fff;' : '') + '">' + day + '</button>';
        } else {
            var title = info.state === 'past' ? 'Past date' : (info.state === 'full' ? 'Fully booked' : 'Closed (Sunday)');
            html += '<div title="' + title + '" style="border:1px solid #e3e3e3;background:#c9c9c9;color:#8a8a8a;border-radius:8px;padding:7px 0;font-size:12px;font-weight:600;text-align:center;cursor:not-allowed;' + (isToday ? 'outline:2px solid #b5b5b5;outline-offset:-3px;' : '') + '">' + day + '</div>';
        }
    }
    grid.innerHTML = html;
}

function pickDate(key){
    selectedDate = key;
    document.getElementById('session_date').value = key;
    renderCalendar();
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var list = document.getElementById('slotList');
    list.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--tm);font-size:12px;padding:8px 0;"><i class="fas fa-spinner fa-spin"></i> Loading slots...</div>';
    fetch('{{ route('student.counseling.slots') }}?date=' + key, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
        .then(function(r){ return r.json(); })
        .then(function(d){ renderSlots(d.slots); });
}

function renderSlots(slots){
    var list = document.getElementById('slotList');
    var niceDate = new Date(selectedDate + 'T00:00:00').toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric' });
    if (!slots.length) { list.innerHTML = '<div class="tm" style="grid-column:1/-1;font-size:12px;">No slots for this date.</div>'; return; }
    list.innerHTML = slots.map(function(s){
        var full = s.full;
        var sel = selectedSlot === s.time;
        var style = full
            ? 'border:1px solid #e3e3e3;background:#c9c9c9;color:#8a8a8a;cursor:not-allowed;'
            : (sel ? 'border:2px solid var(--g);background:var(--g);color:#fff;cursor:pointer;'
                   : 'border:1px solid var(--gm);background:var(--gp);color:var(--g);cursor:pointer;');
        return '<button type="button" ' + (full ? 'disabled title="Fully booked"' : 'onclick="pickSlot(this)"') + ' data-time="' + s.time + '" style="' + style + 'border-radius:8px;padding:9px 6px;font-size:11.5px;font-weight:700;text-align:center;">'
            + s.time + '<div style="font-size:9.5px;font-weight:600;opacity:.85;margin-top:2px;">' + (full ? 'Fully booked' : (s.capacity - s.booked) + ' slot' + (s.capacity - s.booked > 1 ? 's' : '') + ' left') + '</div></button>';
    }).join('');
    var anyOpen = slots.some(function(s){ return !s.full; });
    if (!anyOpen) list.innerHTML += '<div class="tm" style="grid-column:1/-1;font-size:11px;">All time slots on ' + niceDate + ' are fully booked. Please pick another date.</div>';
}

function pickSlot(btn){
    selectedSlot = btn.dataset.time;
    document.getElementById('session_time').value = selectedSlot;
    document.querySelectorAll('#slotList button[data-time]').forEach(function(b){
        var full = b.disabled, sel = b.dataset.time === selectedSlot;
        b.style.border = sel ? '2px solid var(--g)' : (full ? '1px solid #e3e3e3' : '1px solid var(--gm)');
        b.style.background = sel ? 'var(--g)' : (full ? '#c9c9c9' : 'var(--gp)');
        b.style.color = sel ? '#fff' : (full ? '#8a8a8a' : 'var(--g)');
    });
    var niceDate = new Date(selectedDate + 'T00:00:00').toLocaleDateString('en-US', { weekday:'long', month:'long', day:'numeric', year:'numeric' });
    document.getElementById('slotSummaryText').textContent = 'Booking: ' + niceDate + ' · ' + selectedSlot;
    document.getElementById('slotSummary').style.display = '';
}

document.addEventListener('DOMContentLoaded', loadCalendar);
@endif

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('counselingForm');
    const select = document.getElementById('concern_type');
    const textInput = document.getElementById('other_concern_text');

    form.addEventListener('submit', function(e) {
        if (select.value === 'Other Concern') {
            const customText = textInput.value.trim();
            if (!customText) {
                e.preventDefault();
                textInput.focus();
                textInput.style.borderColor = 'var(--danger)';
                alert('Please specify your concern.');
                return;
            }
            // Replace the select value with the custom text before submission
            select.value = customText;
        }
        @if($setting->scheduling_mode === 'slots')
        if (!document.getElementById('session_date').value || !document.getElementById('session_time').value) {
            e.preventDefault();
            alert('Please select a date and a time slot on the calendar.');
        }
        @endif
    });
});
</script>
@endpush
