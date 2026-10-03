@extends('scholarship.layouts.app')
@section('title','Scholarships')
@section('page-title','Scholarship Programs')
@section('page-sub','Manage all scholarship programs and AI criteria')
@section('content')
<div style="display:flex;justify-content:flex-end;margin-bottom:16px;"><a href="{{ route('scholarship.programs.create') }}" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> Add Program</a></div>
<div class="card an">
<div class="ch"><i class="fas fa-award" style="color:var(--yd);"></i><h2>All Programs</h2><span class="badge b-p" style="margin-left:6px;">{{ $scholarships->total() }}</span><span class="badge b-w" style="margin-left:6px;" title="Programs with posted updates"><i class="fas fa-bullhorn"></i> {{ $scholarships->sum(fn($s) => $s->updates_count) }} update{{ $scholarships->sum(fn($s) => $s->updates_count) === 1 ? '' : 's' }} posted</span></div>
<div class="tw"><table>
<thead><tr><th>Name</th><th>Type</th><th>Benefit</th><th>Slots</th><th>Applications</th><th>Updates</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($scholarships as $sch)
<tr @if(($sch->updates_count ?? 0) > 0)style="background:rgba(26,107,47,0.045);"@endif>
<td><div class="fws" style="font-size:13px;">{{ $sch->name }}</div></td>
<td><span class="badge {{ $sch->type==='Government'?'b-p':($sch->type==='Private'?'b-s':'b-i') }}">{{ $sch->type }}</span></td>
<td style="font-size:12px;color:var(--tm);">{{ Str::limit($sch->benefits??'—',40) }}</td>
<td class="mono fwb" style="color:var(--g);">{{ $sch->slots??'—' }}</td>
<td class="mono" style="color:var(--tm);">{{ $sch->applications_count??0 }}</td>
<td><button type="button" class="btn btn-o btn-sm" style="padding:3px 10px;font-size:11.5px;" onclick="openUpdatesModal({{ $sch->id }}, this.dataset.name)" data-name="{{ $sch->name }}"><i class="fas fa-bullhorn" style="font-size:10px;color:var(--gm);"></i> {{ $sch->updates_count ?? 0 }}</button></td>
<td><span class="badge {{ $sch->status==='Active'?'b-s':'b-d' }}">{{ $sch->status }}</span></td>
<td><div style="display:flex;gap:5px;">
<button type="button" class="btn btn-o btn-sm btn-ic" title="Quick view" onclick="openProgramModal({{ $sch->id }})"><i class="fas fa-eye"></i></button>
<a href="{{ route('scholarship.programs.edit',$sch->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-edit"></i></a>
<form method="POST" action="{{ route('scholarship.programs.destroy',$sch->id) }}">@csrf @method('DELETE')<button class="btn btn-d btn-sm btn-ic" onclick="return confirm('Delete this scholarship?')"><i class="fas fa-trash"></i></button></form>
</div></td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;padding:20px;color:var(--tm);">No scholarships yet. <a href="{{ route('scholarship.programs.create') }}" style="color:var(--gm);">Add one</a></td></tr>
@endforelse
</tbody></table></div>
@if($scholarships->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $scholarships->links() }}</div>@endif
</div>

{{-- Quick View Modal --}}
<div class="mo" id="programModal">
<div class="mb" style="max-width:820px;">
<div class="mh"><i class="fas fa-award" style="color:var(--yd);font-size:18px;"></i><h3 id="programModalTitle">Program Details</h3><button class="mc" onclick="closeModal('programModal')"><i class="fas fa-times"></i></button></div>
<div class="mbody" id="programModalBody" style="min-height:120px;">
<div style="text-align:center;padding:26px;color:var(--tm);font-size:13px;"><div style="width:22px;height:22px;border:3px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:progSpin .7s linear infinite;margin:0 auto 12px;"></div>Loading program details...</div>
</div>
<div class="mfoot">
<span id="programModalEditWrap" style="display:none;margin-right:auto;"><a id="programModalEditLink" href="#" class="btn btn-o btn-sm"><i class="fas fa-edit"></i> Edit</a></span>
<button type="button" class="btn btn-o btn-sm" onclick="closeModal('programModal')">Close</button>
</div>
</div>
</div>
<style>@keyframes progSpin{to{transform:rotate(360deg);}}</style>
<script>
function openProgramModal(id){
    openModal('programModal');
    var body=document.getElementById('programModalBody');
    var editWrap=document.getElementById('programModalEditWrap');
    editWrap.style.display='none';
    body.innerHTML='<div style="text-align:center;padding:26px;color:var(--tm);font-size:13px;"><div style="width:22px;height:22px;border:3px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:progSpin .7s linear infinite;margin:0 auto 12px;"></div>Loading program details...</div>';
    fetch('{{ url('scholarship/programs') }}/'+id+'?modal=1',{headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(function(r){ if(!r.ok) throw new Error('HTTP '+r.status); return r.text(); })
    .then(function(html){
        body.innerHTML=html;
        document.getElementById('programModalEditLink').href='{{ url('scholarship/programs') }}/'+id+'/edit';
        editWrap.style.display='block';
    })
    .catch(function(e){
        body.innerHTML='<div class="alert al-d" style="margin:0;"><i class="fas fa-exclamation-circle"></i> Unable to load program details. '+e.message+'</div>';
    });
}
</script>

{{-- Program Updates Modal --}}
<div class="mo" id="updatesModal">
<div class="mb" style="max-width:680px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-bullhorn"></i></div><div><h3>Program Updates</h3><div class="tm" style="font-size:12px;" id="updatesProgName">—</div></div><button class="mc" onclick="closeModal('updatesModal')"><i class="fas fa-times"></i></button></div>
<div class="mbody" style="padding:0;">
<div id="updatesList" style="max-height:300px;overflow-y:auto;"></div>
<div style="border-top:1px solid var(--bd);padding:14px 18px;background:var(--bg);">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
<span class="fws" style="font-size:12.5px;color:var(--g);"><i class="fas fa-plus-circle"></i> <span id="updatesFormTitle">Post an Update</span></span>
</div>
<form id="updatesForm">@csrf
<input type="hidden" name="update_id" id="upd_update_id" value="">
<div class="fg"><input type="text" name="title" id="upd_title" class="fc" placeholder="Update title (e.g. Application deadline extended)" required></div>
<div class="g2">
<div class="fg" style="margin-bottom:0;"><select name="source_type" id="upd_source_type" class="fc" required><option value="Government">Government report</option><option value="Private">Private report</option><option value="Institutional">Institutional report</option></select></div>
<div class="fg" style="margin-bottom:0;display:flex;gap:6px;"><button type="submit" id="updSubmitBtn" class="btn btn-p btn-sm" style="flex:1;justify-content:center;"><i class="fas fa-paper-plane"></i> Post Update</button><button type="button" id="updCancelEditBtn" style="display:none;" class="btn btn-o btn-sm" onclick="resetUpdateForm()"><i class="fas fa-times"></i></button></div>
</div>
<div class="fg" style="margin-bottom:0;"><textarea name="body" id="upd_body" class="fc" rows="3" placeholder="Details of the update or change reported by the agency/office..." required></textarea></div>
</form>
</div>
</div>
<div class="mfoot"><button type="button" class="btn btn-o btn-sm" onclick="closeModal('updatesModal')">Close</button></div>
</div>
</div>
<script>
var updatesProgId = null;
function openUpdatesModal(id, name){
    updatesProgId = id;
    document.getElementById('updatesProgName').textContent = name;
    resetUpdateForm();
    openModal('updatesModal');
    loadUpdates();
}
function loadUpdates(){
    var list = document.getElementById('updatesList');
    list.innerHTML = '<div style="text-align:center;padding:22px;color:var(--tm);font-size:12.5px;"><i class="fas fa-spinner fa-spin"></i> Loading updates...</div>';
    fetch('{{ url('scholarship/programs') }}/' + updatesProgId + '/updates', { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (!d.updates.length) {
                list.innerHTML = '<div style="text-align:center;padding:22px;color:var(--tm);font-size:12.5px;"><i class="fas fa-bullhorn" style="font-size:24px;color:var(--bd);display:block;margin-bottom:8px;"></i>No updates posted yet for this program.</div>';
                return;
            }
            list.innerHTML = d.updates.map(function(u){
                var cls = u.source_type === 'Government' ? 'b-p' : (u.source_type === 'Private' ? 'b-s' : 'b-i');
                return '<div style="padding:12px 18px;border-bottom:1px solid var(--bd);">'
                    + '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;"><span class="fws" style="font-size:13px;">' + u.title + '</span>'
                    + '<span class="badge ' + cls + '" style="font-size:9.5px;">' + u.source_type + '</span></div>'
                    + '<div style="font-size:12px;color:var(--tm);margin-top:4px;white-space:pre-wrap;">' + u.body.replace(/&/g,'&amp;').replace(/</g,'&lt;') + '</div>'
                    + '<div style="display:flex;align-items:center;gap:8px;margin-top:7px;font-size:10.5px;color:var(--tm);"><i class="fas fa-user-pen"></i> ' + u.posted_by + ' · <i class="fas fa-clock"></i> ' + u.posted_at
                    + '<span style="margin-left:auto;display:flex;gap:5px;">'
                    + '<button type="button" class="btn btn-o btn-sm btn-ic" title="Edit" onclick=\'editUpdate(' + JSON.stringify(u).replace(/'/g,"&#39;") + ')\'><i class="fas fa-edit"></i></button>'
                    + '<button type="button" class="btn btn-d btn-sm btn-ic" title="Delete" onclick="deleteUpdate(' + u.id + ')"><i class="fas fa-trash"></i></button>'
                    + '</span></div></div>';
            }).join('');
        })
        .catch(function(){ list.innerHTML = '<div class="alert al-d" style="margin:12px 18px;"><i class="fas fa-exclamation-circle"></i> Could not load updates.</div>'; });
}
function resetUpdateForm(){
    document.getElementById('updatesForm').reset();
    document.getElementById('upd_update_id').value = '';
    document.getElementById('updatesFormTitle').textContent = 'Post an Update';
    document.getElementById('updSubmitBtn').innerHTML = '<i class="fas fa-paper-plane"></i> Post Update';
    document.getElementById('updCancelEditBtn').style.display = 'none';
}
function editUpdate(u){
    document.getElementById('upd_update_id').value = u.id;
    document.getElementById('upd_title').value = u.title;
    document.getElementById('upd_source_type').value = u.source_type;
    document.getElementById('upd_body').value = u.body;
    document.getElementById('updatesFormTitle').textContent = 'Edit Update';
    document.getElementById('updSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Save Changes';
    document.getElementById('updCancelEditBtn').style.display = '';
    document.getElementById('upd_title').scrollIntoView({ behavior:'smooth', block:'center' });
}
function deleteUpdate(id){
    if (!confirm('Delete this update?')) return;
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('{{ url('scholarship/program-updates') }}/' + id, { method:'DELETE', headers:{'X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
        .then(function(r){ return r.json().catch(function(){ return {}; }); })
        .then(function(d){ window.isamsToast ? isamsToast(d.message || 'Update removed.', 'success') : null; loadUpdates(); })
        .catch(function(){ window.isamsToast ? isamsToast('Could not delete the update.', 'error') : null; });
}
document.getElementById('updatesForm').addEventListener('submit', function(e){
    e.preventDefault();
    var form = e.target, btn = document.getElementById('updSubmitBtn');
    var fd = new FormData(form);
    var editing = document.getElementById('upd_update_id').value;
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    btn.disabled = true;
    var url = editing ? '{{ url('scholarship/program-updates') }}/' + editing : '{{ url('scholarship/programs') }}/' + updatesProgId + '/updates';
    fetch(url, { method:'POST', headers:{'X-CSRF-TOKEN':csrf,'X-HTTP-Method-Override':editing ? 'PATCH' : 'POST','X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body: fd })
        .then(function(r){ return r.json().catch(function(){ return {}; }); })
        .then(function(d){
            btn.disabled = false;
            var ok = d.message && (d.message.indexOf('posted') === 0 || d.message.indexOf('Update') === 0 || d.message.indexOf('edited') > 0);
            window.isamsToast ? isamsToast(d.message || 'Saved.', ok ? 'success' : 'error') : alert(d.message || 'Saved.');
            if (ok) { resetUpdateForm(); loadUpdates(); setTimeout(function(){ window.location.reload(); }, 1200); }
        })
        .catch(function(){ btn.disabled = false; });
});
</script>
@endsection
