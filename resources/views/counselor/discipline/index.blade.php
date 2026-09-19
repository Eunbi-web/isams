@extends('counselor.layouts.app')
@section('title','Discipline Records')
@section('page-title','Discipline Records')
@section('page-sub','Manage student disciplinary cases and offense records')
@section('content')
<div class="sg">
<div class="sc an d1"><div class="si t"><i class="fas fa-folder"></i></div><div class="sv"><div class="lbl">Total Records</div><div class="val">{{ $stats['total'] }}</div></div></div>
<div class="sc an d2"><div class="si r"><i class="fas fa-exclamation-triangle"></i></div><div class="sv"><div class="lbl">Major Offenses</div><div class="val">{{ $stats['major'] }}</div></div></div>
<div class="sc an d3"><div class="si o"><i class="fas fa-exclamation-circle"></i></div><div class="sv"><div class="lbl">Minor Offenses</div><div class="val">{{ $stats['minor'] }}</div></div></div>
<div class="sc an d4"><div class="si dg"><i class="fas fa-folder-open"></i></div><div class="sv"><div class="lbl">Open Cases</div><div class="val">{{ $stats['open'] }}</div></div></div>
</div>

<div class="card mb3">
<div class="cb" style="padding:14px 18px;">
<form method="GET" action="{{ route('counselor.discipline.index') }}" id="dFilterForm" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
<input type="text" name="search" id="dSearch" class="fc" placeholder="Search by EDP number or student name" value="{{ request('search') }}" style="flex:1;min-width:200px;">
<select name="category" class="fc" style="width:150px;" onchange="document.getElementById('dFilterForm').submit()">
<option value="">All Categories</option>
<option value="Major" {{ request('category')==='Major'?'selected':'' }}>Major</option>
<option value="Minor" {{ request('category')==='Minor'?'selected':'' }}>Minor</option>
</select>
<select name="department" class="fc" style="width:170px;" onchange="document.getElementById('dFilterForm').submit()">
<option value="">All Departments</option>
@foreach(['BSIT','BSCS','BSED','BEED','BSN','BSBA','BSCRIM','HRM','Other'] as $dept)
<option value="{{ $dept }}" {{ request('department')===$dept?'selected':'' }}>{{ $dept }}</option>
@endforeach
</select>
<select name="status" class="fc" style="width:150px;" onchange="document.getElementById('dFilterForm').submit()">
<option value="">All Status</option>
<option value="Open" {{ request('status')==='Open'?'selected':'' }}>Open</option>
<option value="Under Review" {{ request('status')==='Under Review'?'selected':'' }}>Under Review</option>
<option value="Closed" {{ request('status')==='Closed'?'selected':'' }}>Closed</option>
</select>
<a href="{{ route('counselor.discipline.index') }}" class="btn btn-o btn-sm"><i class="fas fa-rotate-left"></i> Clear Filters</a>
</form>
</div>
</div>

<div class="card an">
<div class="ch"><i class="fas fa-gavel" style="color:var(--gm);"></i><h2>Discipline Records</h2><span class="badge b-p">{{ $records->total() }}</span><div class="ch-acts"><button type="button" onclick="openModal('addDisciplineModal')" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> Add Discipline Record</button></div></div>
<div class="tw"><table>
<thead><tr><th>No.</th><th>EDP Number</th><th>Student Name</th><th>Department</th><th>Category</th><th>Description</th><th>Incident Date</th><th>Guardian Name</th><th>Guardian Contact</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($records as $i=>$record)
<tr @if($record->offense_category==='Major')style="background:rgba(192,57,43,0.04);"@endif>
<td class="mono tm" style="font-size:11px;">{{ ($records->currentPage()-1)*$records->perPage()+$i+1 }}</td>
<td class="mono" style="font-size:12px;">{{ $record->edp_number }}</td>
<td class="fws" style="font-size:13px;">{{ $record->student_name }}</td>
<td style="font-size:12px;color:var(--tm);">{{ $record->department??'—' }}</td>
<td>@if($record->offense_category==='Major')<span class="badge" style="background:#c0392b;color:#fff;">Major</span>@else<span class="badge" style="background:#d68910;color:#fff;">Minor</span>@endif</td>
<td style="font-size:12px;color:var(--tm);max-width:200px;">{{ \Illuminate\Support\Str::limit($record->description,55) }}</td>
<td class="mono tm" style="font-size:11px;">{{ $record->incident_date?->format('M d Y')??'—' }}</td>
<td style="font-size:12px;">{{ $record->guardian_name??'—' }}</td>
<td style="font-size:12px;">{{ $record->guardian_contact??'—' }}</td>
<td><span class="badge {{ $record->status==='Open'?'b-d':($record->status==='Under Review'?'b-i':'b-s') }}">{{ $record->status }}</span></td>
<td><div style="display:flex;gap:4px;align-items:center;">
<button type="button" onclick="openModal('viewDisciplineModal-{{ $record->id }}')" class="btn btn-o btn-sm" title="View"><i class="fas fa-eye"></i></button>
<button type="button" onclick="openModal('editDisciplineModal-{{ $record->id }}')" class="btn btn-ai btn-sm" title="Edit"><i class="fas fa-edit"></i></button>
<select onchange="setDisciplineStatus({{ $record->id }},this)" class="fc" style="width:120px;padding:5px 8px;font-size:11px;">
@foreach(['Open','Under Review','Closed'] as $st)
<option value="{{ $st }}" {{ $record->status===$st?'selected':'' }}>{{ $st }}</option>
@endforeach
</select>
<button type="button" class="btn btn-d btn-sm" data-ajax-delete="true" data-url="{{ route('counselor.discipline.destroy', $record->id) }}" data-confirm="Delete this discipline record? This cannot be undone." title="Delete"><i class="fas fa-trash"></i></button>
</div></td>
</tr>
@empty
<tr><td colspan="11" style="text-align:center;padding:28px;color:var(--tm);"><i class="fas fa-gavel" style="font-size:30px;color:var(--bd);margin-bottom:10px;display:block;"></i>No discipline records found</td></tr>
@endforelse
</tbody></table></div>
@if($records->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $records->links() }}</div>@endif
</div>

{{-- Add Discipline Record Modal --}}
<div class="mo" id="addDisciplineModal">
<div class="mb">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-plus"></i></div><div><h3>Add Discipline Record</h3></div><button class="mc" onclick="closeModal('addDisciplineModal')"><i class="fas fa-times"></i></button></div>
<form method="POST" action="{{ route('counselor.discipline.store') }}" data-ajax="true">@csrf
<div class="mbody">
<div class="g2">
<div class="fg"><label class="fl">EDP Number <span style="color:var(--danger)">*</span></label><input type="text" name="edp_number" class="fc mono" maxlength="20" required></div>
<div class="fg"><label class="fl">Student Name <span style="color:var(--danger)">*</span></label><input type="text" name="student_name" class="fc" required></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Department <span style="color:var(--danger)">*</span></label><select name="department" class="fc" required><option value="">Select Department</option>@foreach(['BSIT','BSCS','BSED','BEED','BSN','BSBA','BSCRIM','HRM','Other'] as $dept)<option value="{{ $dept }}">{{ $dept }}</option>@endforeach</select></div>
<div class="fg"><label class="fl">Category of Offense <span style="color:var(--danger)">*</span></label><select name="offense_category" class="fc" required><option value="">Select Category</option><option value="Major">Major</option><option value="Minor">Minor</option></select></div>
</div>
<div class="fg"><label class="fl">Description <span style="color:var(--danger)">*</span></label><textarea name="description" class="fc" rows="3" required></textarea></div>
<div class="g2">
<div class="fg"><label class="fl">Date of Incident <span style="color:var(--danger)">*</span></label><input type="date" name="incident_date" class="fc" required></div>
<div class="fg"><label class="fl">Name of Parent or Guardian</label><input type="text" name="guardian_name" class="fc"></div>
</div>
<div class="fg"><label class="fl">Contact Number of Parent or Guardian</label><input type="text" name="guardian_contact" class="fc mono" maxlength="20"></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('addDisciplineModal')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-save"></i> Save Record</button></div>
</form>
</div>
</div>

@foreach($records as $record)
{{-- Edit Modal --}}
<div class="mo" id="editDisciplineModal-{{ $record->id }}">
<div class="mb">
<div class="mh"><div class="si y" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-edit"></i></div><div><h3>Edit Discipline Record</h3><div class="tm" style="font-size:12px;">{{ $record->student_name }} — {{ $record->edp_number }}</div></div><button class="mc" onclick="closeModal('editDisciplineModal-{{ $record->id }}')"><i class="fas fa-times"></i></button></div>
<form method="POST" action="{{ route('counselor.discipline.update', $record->id) }}" data-ajax="true">@csrf @method('PUT')
<div class="mbody">
<div class="g2">
<div class="fg"><label class="fl">EDP Number <span style="color:var(--danger)">*</span></label><input type="text" name="edp_number" class="fc mono" maxlength="20" value="{{ old('edp_number',$record->edp_number) }}" required></div>
<div class="fg"><label class="fl">Student Name <span style="color:var(--danger)">*</span></label><input type="text" name="student_name" class="fc" value="{{ old('student_name',$record->student_name) }}" required></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Department <span style="color:var(--danger)">*</span></label><select name="department" class="fc" required><option value="">Select Department</option>@foreach(['BSIT','BSCS','BSED','BEED','BSN','BSBA','BSCRIM','HRM','Other'] as $dept)<option value="{{ $dept }}" {{ old('department',$record->department)===$dept?'selected':'' }}>{{ $dept }}</option>@endforeach</select></div>
<div class="fg"><label class="fl">Category of Offense <span style="color:var(--danger)">*</span></label><select name="offense_category" class="fc" required>@foreach(['Major','Minor'] as $cat)<option value="{{ $cat }}" {{ old('offense_category',$record->offense_category)===$cat?'selected':'' }}>{{ $cat }}</option>@endforeach</select></div>
</div>
<div class="fg"><label class="fl">Description <span style="color:var(--danger)">*</span></label><textarea name="description" class="fc" rows="3" required>{{ old('description',$record->description) }}</textarea></div>
<div class="g2">
<div class="fg"><label class="fl">Date of Incident <span style="color:var(--danger)">*</span></label><input type="date" name="incident_date" class="fc" value="{{ old('incident_date',$record->incident_date?->format('Y-m-d')) }}" required></div>
<div class="fg"><label class="fl">Name of Parent or Guardian</label><input type="text" name="guardian_name" class="fc" value="{{ old('guardian_name',$record->guardian_name) }}"></div>
</div>
<div class="fg"><label class="fl">Contact Number of Parent or Guardian</label><input type="text" name="guardian_contact" class="fc mono" maxlength="20" value="{{ old('guardian_contact',$record->guardian_contact) }}"></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('editDisciplineModal-{{ $record->id }}')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-save"></i> Save Changes</button></div>
</form>
</div>
</div>

{{-- View Modal --}}
<div class="mo" id="viewDisciplineModal-{{ $record->id }}">
<div class="mb">
<div class="mh"><div class="si t" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-eye"></i></div><div><h3>Discipline Record Details</h3></div><button class="mc" onclick="closeModal('viewDisciplineModal-{{ $record->id }}')"><i class="fas fa-times"></i></button></div>
<div class="mbody">
<div class="g2">
<div><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">EDP Number</div><div class="mono fws">{{ $record->edp_number }}</div></div>
<div><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">Student Name</div><div class="fws">{{ $record->student_name }}</div></div>
<div><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">Department</div><div>{{ $record->department??'—' }}</div></div>
<div><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">Category</div><div>@if($record->offense_category==='Major')<span class="badge" style="background:#c0392b;color:#fff;">Major</span>@else<span class="badge" style="background:#d68910;color:#fff;">Minor</span>@endif</div></div>
<div style="grid-column:1/-1;"><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">Description</div><div style="line-height:1.6;">{{ $record->description }}</div></div>
<div><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">Incident Date</div><div class="mono">{{ $record->incident_date?->format('M d Y')??'—' }}</div></div>
<div><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">Status</div><div><span class="badge {{ $record->status==='Open'?'b-d':($record->status==='Under Review'?'b-i':'b-s') }}">{{ $record->status }}</span></div></div>
<div><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">Guardian Name</div><div>{{ $record->guardian_name??'—' }}</div></div>
<div><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">Guardian Contact</div><div class="mono">{{ $record->guardian_contact??'—' }}</div></div>
<div style="grid-column:1/-1;"><div class="tm" style="font-size:11px;text-transform:uppercase;font-weight:700;">Created At</div><div class="mono" style="font-size:12px;">{{ $record->created_at?->format('M d, Y g:i A') }}</div></div>
</div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('viewDisciplineModal-{{ $record->id }}')" class="btn btn-o btn-sm">Close</button></div>
</div>
</div>
@endforeach
@endsection
@push('scripts')
<script>
// Live search with 400ms debounce
(function(){
    var t;
    var inp=document.getElementById('dSearch');
    if(inp) inp.addEventListener('keyup',function(){
        clearTimeout(t);
        t=setTimeout(function(){ document.getElementById('dFilterForm').submit(); },400);
    });
})();
// Status update via AJAX PATCH
function setDisciplineStatus(id, sel){
    var CSRF=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch({{ json_encode(url('counselor/discipline')) }}+'/'+id+'/status',{
        method:'PATCH',
        headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
        body:JSON.stringify({status:sel.value})
    }).then(function(r){return r.json();})
    .then(function(d){
        if(window.isamsToast) isamsToast(d.message||'Status updated.','success');
        setTimeout(function(){ window.location.reload(); },800);
    }).catch(function(){ if(window.isamsToast) isamsToast('Network error.','error'); });
}
</script>
@endpush
