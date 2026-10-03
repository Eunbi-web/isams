@extends('scholarship.layouts.app')
@section('title','Performance Monitoring')
@section('page-title','Scholar Performance Monitoring')
@section('page-sub','Track GWA, enrollment, requirements and graduation progress of every scholar')
@section('content')
<div class="sg">
<div class="sc an d1"><div class="si g"><i class="fas fa-shield-halved"></i></div><div class="sv"><div class="lbl">Maintaining</div><div class="val">{{ $stats['maintaining'] }}</div><div class="chg">Active · Enrolled · Requirements met</div></div></div>
<div class="sc an d2"><div class="si t"><i class="fas fa-arrow-trend-up"></i></div><div class="sv"><div class="lbl">On Track to Graduate</div><div class="val">{{ $stats['onTrack'] }}</div><div class="chg">Active scholars</div></div></div>
<div class="sc an d3"><div class="si dg"><i class="fas fa-graduation-cap"></i></div><div class="sv"><div class="lbl">Graduated</div><div class="val">{{ $stats['graduated'] }}</div><div class="chg">Completed studies</div></div></div>
<div class="sc an d4"><div class="si o"><i class="fas fa-triangle-exclamation"></i></div><div class="sv"><div class="lbl">At Risk</div><div class="val">{{ $stats['atRisk'] }}</div><div class="chg">Needs intervention</div></div></div>
<div class="sc an d5"><div class="si y"><i class="fas fa-user-slash"></i></div><div class="sv"><div class="lbl">Not Enrolled</div><div class="val">{{ $stats['notEnrolled'] }}</div><div class="chg">Active but not enrolled</div></div></div>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
<form method="GET" action="{{ route('scholarship.monitoring.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <div style="position:relative;">
        <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;"></i>
        <input type="text" name="search" value="{{ request('search') }}" class="fc" placeholder="Search scholar..." style="padding-left:30px;width:200px;">
    </div>
    <select name="status" class="fc" style="width:130px;">
        <option value="">All Status</option>
        @foreach(['Active','Inactive','Graduated'] as $st)
        <option value="{{ $st }}" {{ request('status')===$st?'selected':'' }}>{{ $st }}</option>
        @endforeach
    </select>
    <select name="enrollment" class="fc" style="width:140px;">
        <option value="">All Enrollment</option>
        <option value="Enrolled" {{ request('enrollment')==='Enrolled'?'selected':'' }}>Enrolled</option>
        <option value="Not Enrolled" {{ request('enrollment')==='Not Enrolled'?'selected':'' }}>Not Enrolled</option>
    </select>
    <select name="graduation" class="fc" style="width:130px;">
        <option value="">All Graduation</option>
        @foreach(['On Track','Graduated','At Risk'] as $g)
        <option value="{{ $g }}" {{ request('graduation')===$g?'selected':'' }}>{{ $g }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-p btn-sm"><i class="fas fa-filter"></i> Filter</button>
    <a href="{{ route('scholarship.monitoring.index') }}" class="btn btn-o btn-sm">Clear</a>
</form>
</div>

<div class="card an"><div class="ch"><i class="fas fa-chart-line" style="color:var(--gm);"></i><h2>Monitoring Records</h2><span class="badge b-p">{{ $scholars->total() }}</span></div>
<div class="tw"><table><thead><tr><th>Scholar</th><th>Scholarship</th><th>Current GWA</th><th>Enrollment</th><th>Requirements</th><th>Graduation</th><th>Last Monitored</th><th>Action</th></tr></thead>
<tbody>
@forelse($scholars as $s)
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($s->first_name,0,1)) }}</div><div><div class="fws" style="font-size:13px;">{{ $s->full_name }}</div><div class="tm" style="font-size:11px;">{{ \Illuminate\Support\Str::limit($s->course ?? '—',22) }}{{ $s->year_level ? ' · '.$s->year_level : '' }}</div></div></div></td>
<td style="font-size:12px;">{{ \Illuminate\Support\Str::limit($s->scholarship_name,26) }} <span class="badge {{ $s->scholarship_type==='Internal'?'b-p':'b-w' }}" style="font-size:9px;">{{ $s->scholarship_type }}</span></td>
<td class="mono fwb" style="color:{{ !$s->current_gwa ? 'var(--tm)' : ((float)$s->current_gwa <= 1.75 ? 'var(--gm)' : ((float)$s->current_gwa <= 2.25 ? 'var(--warn)' : 'var(--danger)')) }}">{{ $s->current_gwa ? number_format((float)$s->current_gwa,2) : '—' }}</td>
<td><span class="badge {{ $s->enrollment_status==='Enrolled'?'b-s':'b-d' }}">{{ $s->enrollment_status }}</span></td>
<td><span class="badge {{ $s->requirements_met ? 'b-s' : 'b-d' }}">{{ $s->requirements_met ? 'Maintaining' : 'Not Maintaining' }}</span></td>
<td><span class="badge {{ $s->graduation_status==='On Track'?'b-p':($s->graduation_status==='Graduated'?'b-s':'b-d') }}">{{ $s->graduation_status }}</span></td>
<td class="mono tm" style="font-size:11px;">{{ $s->last_monitored_at?->format('M d, Y') ?? 'Never' }}</td>
@php $monitorData = json_encode($s->only(["id","first_name","last_name","current_gwa","enrollment_status","requirements_met","graduation_status","remarks"]), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); @endphp
<td><button type="button" class="btn btn-o btn-sm btn-ic" onclick='openMonitorModal({!! $monitorData !!})' title="Update Monitoring"><i class="fas fa-pen-to-square"></i></button></td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;padding:24px;color:var(--tm);"><i class="fas fa-chart-line" style="font-size:30px;color:var(--bd);margin-bottom:10px;display:block;"></i>No scholars to monitor yet. Add scholars in the Scholar List first.</td></tr>
@endforelse
</tbody></table></div>
@if($scholars->hasPages())<div class="c-pagination" style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $scholars->links() }}</div>@endif
</div>

{{-- Update Monitoring Modal --}}
<div class="mo" id="monitorModal">
<div class="mb" style="max-width:480px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-chart-line"></i></div><div><h3>Update Monitoring</h3><div class="tm" style="font-size:12px;" id="monitorSub">—</div></div><button class="mc" onclick="closeModal('monitorModal')"><i class="fas fa-times"></i></button></div>
<form id="monitorForm">@csrf @method('PATCH')
<div class="mbody">
<div class="fg"><label class="fl">Current GWA</label><input type="number" step="0.01" min="1" max="5" name="current_gwa" id="mf_current_gwa" class="fc" placeholder="e.g. 1.50"></div>
<div class="g2">
<div class="fg"><label class="fl">Enrollment Status <span style="color:var(--danger)">*</span></label><select name="enrollment_status" id="mf_enrollment_status" class="fc" required><option value="Enrolled">Enrolled</option><option value="Not Enrolled">Not Enrolled</option></select></div>
<div class="fg"><label class="fl">Graduation Status <span style="color:var(--danger)">*</span></label><select name="graduation_status" id="mf_graduation_status" class="fc" required><option value="On Track">On Track</option><option value="Graduated">Graduated</option><option value="At Risk">At Risk</option></select></div>
</div>
<div class="fg" style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="requirements_met" id="mf_requirements_met" value="1" style="width:auto;accent-color:var(--g);"><label for="mf_requirements_met" class="fl" style="margin:0;">Still maintaining scholarship requirements</label></div>
<div class="fg"><label class="fl">Remarks</label><textarea name="remarks" id="mf_remarks" class="fc" rows="2" placeholder="Optional notes on this evaluation..."></textarea></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('monitorModal')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-save"></i> Save Monitoring</button></div>
</form>
</div>
</div>
@endsection
@push('scripts')
<script>
var monitorId = null;
function openMonitorModal(s){
    monitorId = s.id;
    document.getElementById('monitorSub').textContent = s.first_name + ' ' + s.last_name;
    document.getElementById('mf_current_gwa').value = s.current_gwa || '';
    document.getElementById('mf_enrollment_status').value = s.enrollment_status || 'Enrolled';
    document.getElementById('mf_graduation_status').value = s.graduation_status || 'On Track';
    document.getElementById('mf_requirements_met').checked = !!s.requirements_met;
    document.getElementById('mf_remarks').value = s.remarks || '';
    openModal('monitorModal');
}
document.getElementById('monitorForm').addEventListener('submit', function(e){
    e.preventDefault();
    var form = e.target, btn = form.querySelector('button[type=submit]'), fd = new FormData(form);
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    fetch('{{ url('scholarship/monitoring') }}/' + monitorId, {
        method:'POST', headers:{'X-CSRF-TOKEN':csrf,'X-HTTP-Method-Override':'PATCH','X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body: fd
    }).then(function(r){ return r.json().catch(function(){ return {}; }); })
      .then(function(d){
          window.isamsToast ? isamsToast(d.message || 'Saved.', d.message && d.message.indexOf('updated') > 0 ? 'success' : 'error') : alert(d.message || 'Saved.');
          if (d.message && d.message.indexOf('updated') > 0) setTimeout(function(){ window.location.reload(); }, 900);
          else { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Save Monitoring'; }
      }).catch(function(){ btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Save Monitoring'; });
});
</script>
@endpush
