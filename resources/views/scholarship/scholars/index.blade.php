@extends('scholarship.layouts.app')
@section('title','Scholar List')
@section('page-title','Scholar List Management')
@section('page-sub','Maintain the list of current scholarship recipients (Accounting)')
@section('content')
<div class="sg">
<div class="sc an d1"><div class="si g"><i class="fas fa-users"></i></div><div class="sv"><div class="lbl">Total Scholars</div><div class="val">{{ $stats['total'] }}</div><div class="chg">All records on file</div></div></div>
<div class="sc an d2"><div class="si dg"><i class="fas fa-user-check"></i></div><div class="sv"><div class="lbl">Active Scholars</div><div class="val">{{ $stats['active'] }}</div><div class="chg">Currently receiving benefits</div></div></div>
<div class="sc an d3"><div class="si y"><i class="fas fa-building-columns"></i></div><div class="sv"><div class="lbl">Internal (SCC)</div><div class="val">{{ $stats['internal'] }}</div><div class="chg">Institutional grants</div></div></div>
<div class="sc an d4"><div class="si o"><i class="fas fa-flag"></i></div><div class="sv"><div class="lbl">External Grants</div><div class="val">{{ $stats['external'] }}</div><div class="chg">Partner agencies</div></div></div>
<div class="sc an d5"><div class="si t"><i class="fas fa-graduation-cap"></i></div><div class="sv"><div class="lbl">Graduated</div><div class="val">{{ $stats['graduated'] }}</div><div class="chg">Completed their studies</div></div></div>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
<form method="GET" action="{{ route('scholarship.scholars.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <div style="position:relative;">
        <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;"></i>
        <input type="text" name="search" value="{{ request('search') }}" class="fc" placeholder="Search name, student no., grant..." style="padding-left:30px;width:220px;">
    </div>
    <select name="status" class="fc" style="width:130px;">
        <option value="">All Status</option>
        @foreach(['Active','Inactive','Graduated'] as $st)
        <option value="{{ $st }}" {{ request('status')===$st?'selected':'' }}>{{ $st }}</option>
        @endforeach
    </select>
    <select name="type" class="fc" style="width:130px;">
        <option value="">All Types</option>
        <option value="Internal" {{ request('type')==='Internal'?'selected':'' }}>Internal</option>
        <option value="External" {{ request('type')==='External'?'selected':'' }}>External</option>
    </select>
    <button type="submit" class="btn btn-p btn-sm"><i class="fas fa-filter"></i> Filter</button>
    <a href="{{ route('scholarship.scholars.index') }}" class="btn btn-o btn-sm">Clear</a>
</form>
<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <button type="button" onclick="openModal('importExcelModal')" class="btn btn-o btn-sm"><i class="fas fa-file-excel"></i> Upload Excel</button>
    <button type="button" onclick="importExternal(this)" class="btn btn-ac btn-sm" {{ \App\Models\Scholar::where('source','External Import')->exists() ? 'disabled title="External list already imported"' : '' }}><i class="fas fa-cloud-download-alt"></i> Import External List</button>
    <button type="button" onclick="openModal('addScholarModal')" class="btn btn-p btn-sm"><i class="fas fa-user-plus"></i> Add Scholar</button>
</div>
</div>

<div class="card an"><div class="ch"><i class="fas fa-award" style="color:var(--gm);"></i><h2>Current Scholars</h2><span class="badge b-p">{{ $scholars->total() }}</span></div>
<div class="tw"><table><thead><tr><th>Scholar</th><th>Student No.</th><th>Course / Year</th><th>Scholarship</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($scholars as $s)
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($s->first_name,0,1)) }}</div><div class="fws" style="font-size:13px;">{{ $s->full_name }}</div></div></td>
<td class="mono" style="font-size:12px;">{{ $s->student_number ?? '—' }}</td>
<td style="font-size:12px;color:var(--tm);">{{ \Illuminate\Support\Str::limit($s->course ?? '—',26) }}{{ $s->year_level ? ' · '.$s->year_level : '' }}</td>
<td style="font-size:12px;">{{ \Illuminate\Support\Str::limit($s->scholarship_name,30) }}</td>
<td><span class="badge {{ $s->scholarship_type==='Internal'?'b-p':'b-w' }}" style="font-size:10px;">{{ $s->scholarship_type }}</span></td>
<td><span class="badge {{ $s->status==='Active'?'b-s':($s->status==='Graduated'?'b-p':'b-gray') }}">{{ $s->status }}</span></td>
<td><div style="display:flex;gap:5px;">
@php $editData = json_encode($s->only(["id","student_number","first_name","middle_name","last_name","course","year_level","scholarship_name","scholarship_type","status","remarks"]), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); @endphp
<button type="button" class="btn btn-o btn-sm btn-ic" onclick='openEditModal({!! $editData !!})' title="Edit"><i class="fas fa-edit"></i></button>
<button type="button" class="btn btn-d btn-sm btn-ic" data-ajax-delete="true" data-url="{{ route('scholarship.scholars.destroy',$s->id) }}" data-confirm="Remove {{ $s->full_name }} from the scholar list?" title="Delete"><i class="fas fa-trash"></i></button>
</div></td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--tm);"><i class="fas fa-award" style="font-size:30px;color:var(--bd);margin-bottom:10px;display:block;"></i>No scholars on the list yet. Add one manually, upload an Excel file, or import the external list.</td></tr>
@endforelse
</tbody></table></div>
@if($scholars->hasPages())<div class="c-pagination" style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $scholars->links() }}</div>@endif
</div>

{{-- Add Scholar Modal --}}
<div class="mo" id="addScholarModal">
<div class="mb" style="max-width:560px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-user-plus"></i></div><div><h3>Add Scholar</h3><div class="tm" style="font-size:12px;">Manual input of scholar details</div></div><button class="mc" onclick="closeModal('addScholarModal')"><i class="fas fa-times"></i></button></div>
<form method="POST" action="{{ route('scholarship.scholars.store') }}" data-ajax="true">@csrf
<div class="mbody">
<div class="g2">
<div class="fg"><label class="fl">First Name <span style="color:var(--danger)">*</span></label><input type="text" name="first_name" class="fc" required></div>
<div class="fg"><label class="fl">Last Name <span style="color:var(--danger)">*</span></label><input type="text" name="last_name" class="fc" required></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Middle Name</label><input type="text" name="middle_name" class="fc"></div>
<div class="fg"><label class="fl">Student No. / EDP</label><input type="text" name="student_number" class="fc" placeholder="e.g. scc2024-194333"></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Course</label><input type="text" name="course" class="fc" placeholder="e.g. BS Information Technology"></div>
<div class="fg"><label class="fl">Year Level</label><select name="year_level" class="fc"><option value="">—</option>@foreach(['1st Year','2nd Year','3rd Year','4th Year','5th Year'] as $y)<option value="{{ $y }}">{{ $y }}</option>@endforeach</select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Scholarship <span style="color:var(--danger)">*</span></label><input type="text" name="scholarship_name" class="fc" required></div>
<div class="fg"><label class="fl">Type <span style="color:var(--danger)">*</span></label><select name="scholarship_type" class="fc" required><option value="Internal">Internal</option><option value="External">External</option></select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Status <span style="color:var(--danger)">*</span></label><select name="status" class="fc" required><option value="Active">Active</option><option value="Inactive">Inactive</option><option value="Graduated">Graduated</option></select></div>
<div class="fg"><label class="fl">Remarks</label><input type="text" name="remarks" class="fc" placeholder="Optional notes"></div>
</div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('addScholarModal')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-save"></i> Save Scholar</button></div>
</form>
</div>
</div>

{{-- Edit Scholar Modal --}}
<div class="mo" id="editScholarModal">
<div class="mb" style="max-width:560px;">
<div class="mh"><div class="si y" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-user-pen"></i></div><div><h3>Edit Scholar</h3><div class="tm" style="font-size:12px;" id="editScholarSub">—</div></div><button class="mc" onclick="closeModal('editScholarModal')"><i class="fas fa-times"></i></button></div>
<form method="POST" id="editScholarForm" data-ajax="true">@csrf @method('PUT')
<div class="mbody">
<div class="g2">
<div class="fg"><label class="fl">First Name <span style="color:var(--danger)">*</span></label><input type="text" name="first_name" id="ef_first_name" class="fc" required></div>
<div class="fg"><label class="fl">Last Name <span style="color:var(--danger)">*</span></label><input type="text" name="last_name" id="ef_last_name" class="fc" required></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Middle Name</label><input type="text" name="middle_name" id="ef_middle_name" class="fc"></div>
<div class="fg"><label class="fl">Student No. / EDP</label><input type="text" name="student_number" id="ef_student_number" class="fc"></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Course</label><input type="text" name="course" id="ef_course" class="fc"></div>
<div class="fg"><label class="fl">Year Level</label><select name="year_level" id="ef_year_level" class="fc"><option value="">—</option>@foreach(['1st Year','2nd Year','3rd Year','4th Year','5th Year'] as $y)<option value="{{ $y }}">{{ $y }}</option>@endforeach</select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Scholarship <span style="color:var(--danger)">*</span></label><input type="text" name="scholarship_name" id="ef_scholarship_name" class="fc" required></div>
<div class="fg"><label class="fl">Type <span style="color:var(--danger)">*</span></label><select name="scholarship_type" id="ef_scholarship_type" class="fc" required><option value="Internal">Internal</option><option value="External">External</option></select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Status <span style="color:var(--danger)">*</span></label><select name="status" id="ef_status" class="fc" required><option value="Active">Active</option><option value="Inactive">Inactive</option><option value="Graduated">Graduated</option></select></div>
<div class="fg"><label class="fl">Remarks</label><input type="text" name="remarks" id="ef_remarks" class="fc"></div>
</div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('editScholarModal')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-save"></i> Save Changes</button></div>
</form>
</div>
</div>

{{-- Upload Excel Modal --}}
<div class="mo" id="importExcelModal">
<div class="mb" style="max-width:480px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-file-excel"></i></div><div><h3>Upload Excel File</h3><div class="tm" style="font-size:12px;">.xlsx or .csv with a header row</div></div><button class="mc" onclick="closeModal('importExcelModal')"><i class="fas fa-times"></i></button></div>
<form id="importExcelForm">@csrf
<div class="mbody">
<div class="alert al-i" style="font-size:12px;"><i class="fas fa-circle-info"></i><span>Required headers: <strong>First Name</strong> and <strong>Last Name</strong>. Optional: Middle Name, Student Number, Course, Year, Scholarship, Type, Status.</span></div>
<div class="fg"><label class="fl">Excel / CSV File <span style="color:var(--danger)">*</span></label><input type="file" name="file" id="importExcelFile" class="fc" accept=".xlsx,.xls,.csv" required></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('importExcelModal')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-upload"></i> Import File</button></div>
</form>
</div>
</div>
@endsection
@push('scripts')
<script>
function openEditModal(s){
    var f = document.getElementById('editScholarForm');
    f.action = '{{ url('scholarship/scholars') }}/' + s.id;
    document.getElementById('editScholarSub').textContent = s.first_name + ' ' + s.last_name;
    ['first_name','middle_name','last_name','student_number','course','year_level','scholarship_name','scholarship_type','status','remarks'].forEach(function(k){
        var el = document.getElementById('ef_' + k);
        if (el) el.value = s[k] || '';
    });
    openModal('editScholarModal');
}

function importExternal(btn){
    if (!confirm('Import the external partner-agency scholar list (50 scholars)?')) return;
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
    fetch('{{ route('scholarship.scholars.import-external.post') }}', {
        method:'POST', headers:{'X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
    }).then(function(r){ return r.json().catch(function(){ return {}; }); })
      .then(function(d){
          if (d.message) window.isamsToast ? isamsToast(d.message, d.message.indexOf('Imported') === 0 ? 'success' : 'error') : alert(d.message);
          if (d.reload || (d.message && d.message.indexOf('Imported') === 0)) setTimeout(function(){ window.location.reload(); }, 900);
          else { btn.disabled = false; btn.innerHTML = '<i class="fas fa-cloud-download-alt"></i> Import External List'; }
      }).catch(function(){ btn.disabled = false; btn.innerHTML = '<i class="fas fa-cloud-download-alt"></i> Import External List'; });
}

document.getElementById('importExcelForm').addEventListener('submit', function(e){
    e.preventDefault();
    var form = e.target, btn = form.querySelector('button[type=submit]'), fd = new FormData(form);
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
    fetch('{{ route('scholarship.scholars.import-excel') }}', {
        method:'POST', headers:{'X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body: fd
    }).then(function(r){ return r.json().catch(function(){ return {}; }); })
      .then(function(d){
          window.isamsToast ? isamsToast(d.message || 'Import finished.', d.message && d.message.indexOf('Import finished') === 0 ? 'success' : 'error') : alert(d.message || 'Import finished.');
          if (d.message && d.message.indexOf('Import finished') === 0) setTimeout(function(){ window.location.reload(); }, 900);
          else { btn.disabled = false; btn.innerHTML = '<i class="fas fa-upload"></i> Import File'; }
      }).catch(function(){ btn.disabled = false; btn.innerHTML = '<i class="fas fa-upload"></i> Import File'; });
});
</script>
@endpush
