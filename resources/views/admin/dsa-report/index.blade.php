@extends('admin.layouts.app')
@section('title','DSA Report')
@section('page-title','DSA Report — Active Scholars')
@section('page-sub','Printable list of currently active scholars (DSA portal only)')
@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;" class="no-print">
<div class="tm" style="font-size:13px;"><i class="fas fa-circle-info"></i> Generated {{ $generatedAt->format('M d, Y g:i A') }} — list reflects the scholar list maintained by the Accounting staff.</div>
<div style="display:flex;gap:8px;flex-wrap:wrap;">
<select id="dsaTypeFilter" class="fc" style="width:150px;" onchange="filterType(this.value)">
<option value="">All Types</option>
<option value="Internal" {{ request('type')==='Internal'?'selected':'' }}>Internal</option>
<option value="External" {{ request('type')==='External'?'selected':'' }}>External</option>
</select>
<a href="{{ route('admin.dsa-report.export') }}" class="btn btn-o btn-sm"><i class="fas fa-file-csv"></i> Export CSV</a>
<button type="button" onclick="window.print()" class="btn btn-p btn-sm"><i class="fas fa-print"></i> Print Report</button>
</div>
</div>

<div class="card an" id="dsaReportCard">
<div class="ch no-print"><i class="fas fa-award" style="color:var(--gm);"></i><h2>Active Scholars</h2><span class="badge b-p">{{ $active->count() }}</span></div>
<div class="cb" style="padding:18px 22px 6px;">
{{-- Letterhead (visible on print) --}}
<div class="print-head" style="text-align:center;margin-bottom:14px;">
<div style="font-weight:800;font-size:16px;">SAINT COLUMBAN COLLEGE</div>
<div style="font-size:12px;color:var(--tm);">Student Affairs Office — Pagadian City</div>
<div style="font-weight:700;font-size:14px;margin-top:8px;">REPORT OF ACTIVE SCHOLARS</div>
<div style="font-size:11px;color:var(--tm);">As of {{ $generatedAt->format('F j, Y') }}</div>
</div>
</div>
<div class="tw"><table id="dsaReportTable"><thead><tr><th>No.</th><th>Student No.</th><th>Scholar</th><th>Course / Year</th><th>Scholarship</th><th>Type</th><th>GWA</th><th>Graduation</th></tr></thead>
<tbody>
@forelse($active as $i=>$s)
<tr data-type="{{ $s->scholarship_type }}">
<td class="mono tm" style="font-size:11px;">{{ $i+1 }}</td>
<td class="mono" style="font-size:12px;">{{ $s->student_number ?? '—' }}</td>
<td class="fws" style="font-size:13px;">{{ $s->full_name }}</td>
<td style="font-size:12px;color:var(--tm);">{{ \Illuminate\Support\Str::limit($s->course ?? '—',28) }}{{ $s->year_level ? ' · '.$s->year_level : '' }}</td>
<td style="font-size:12px;">{{ \Illuminate\Support\Str::limit($s->scholarship_name,32) }}</td>
<td><span class="badge {{ $s->scholarship_type==='Internal'?'b-p':'b-w' }}" style="font-size:10px;">{{ $s->scholarship_type }}</span></td>
<td class="mono">{{ $s->current_gwa ? number_format((float)$s->current_gwa,2) : '—' }}</td>
<td><span class="badge {{ $s->graduation_status==='On Track'?'b-p':($s->graduation_status==='Graduated'?'b-s':'b-w') }}">{{ $s->graduation_status }}</span></td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;padding:24px;color:var(--tm);"><i class="fas fa-award" style="font-size:30px;color:var(--bd);margin-bottom:10px;display:block;"></i>No active scholars on record.</td></tr>
@endforelse
</tbody></table></div>
<div style="padding:12px 22px 16px;font-size:11px;color:var(--tm);display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;">
<span>Total active scholars: <strong>{{ $active->count() }}</strong> — Internal: {{ $internal }} · External: {{ $external }}</span>
<span>Prepared by the Scholarship / Accounting Office — Saint Columban College</span>
</div>
</div>

<style>
@media print{
.sidebar,.topbar,.no-print,.mob-toggle{display:none !important;}
body{background:#fff;}
.main{margin-left:0;}
.card{border:none;box-shadow:none;}
.card .ch{display:none;}
.print-head{display:block !important;}
}
.print-head{display:none;}
</style>
@endsection
@push('scripts')
<script>
function filterType(v){
    var rows = document.querySelectorAll('#dsaReportTable tbody tr[data-type]');
    rows.forEach(function(r){ r.style.display = (!v || r.dataset.type === v) ? '' : 'none'; });
}
</script>
@endpush
