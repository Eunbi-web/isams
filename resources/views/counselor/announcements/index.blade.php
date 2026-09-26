@extends('counselor.layouts.app')
@section('title','Announcements')
@section('page-title','Announcements')
@section('page-sub','Announcements visible to students and staff')
@section('content')
<div class="card an mb2">
<div class="ch"><i class="fas fa-bullhorn" style="color:var(--y);"></i><h2>All Announcements</h2><span class="cnt">{{ $announcements->count() }}</span><div class="ch-acts"><button type="button" onclick="openModal('publishModal')" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> Publish Announcement</button></div></div>
</div>

@forelse($announcements as $ann)
<div class="card an mb2">
<div class="ch"><i class="fas fa-bullhorn" style="color:var(--y);"></i><h2 style="font-size:15px;">{{ $ann->title }}</h2><div class="ch-acts"><span class="mono tm" style="font-size:11px;">{{ $ann->published_at?->format('M d Y')??$ann->created_at->format('M d Y') }}</span><button type="button" onclick="openModal('editModal-{{ $ann->id }}')" class="btn btn-sm" style="background:#1a4a6b;color:#fff;" title="Edit"><i class="fas fa-pen"></i></button><button type="button" class="btn btn-d btn-sm" data-ajax-delete="true" data-url="{{ route('counselor.announcements.destroy', $ann->id) }}" data-confirm="Delete this announcement? Students will no longer see it." title="Delete"><i class="fas fa-trash"></i></button></div></div>
<div class="cb" style="font-size:13px;line-height:1.6;">{{ $ann->body }}</div>
</div>

{{-- Edit Modal --}}
<div class="mo" id="editModal-{{ $ann->id }}">
<div class="mb" style="max-width:520px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-pen"></i></div><div><h3>Edit Announcement</h3><div class="tm" style="font-size:12px;">{{ $ann->title }}</div></div><button class="mc" onclick="closeModal('editModal-{{ $ann->id }}')"><i class="fas fa-times"></i></button></div>
<form method="POST" action="{{ route('counselor.announcements.update', $ann->id) }}" data-ajax="true">@csrf @method('PUT')
<div class="mbody">
<div class="fg"><label class="fl">Title <span style="color:var(--danger);">*</span></label><input type="text" name="title" class="fc" value="{{ $ann->title }}" required placeholder="e.g. CHED Applications Now Open"></div>
<div class="fg"><label class="fl">Message <span style="color:var(--danger);">*</span></label><textarea name="body" class="fc" rows="5" required placeholder="Write the full announcement here...">{{ $ann->body }}</textarea></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('editModal-{{ $ann->id }}')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-save"></i> Save Changes</button></div>
</form>
</div>
</div>
@empty
<div class="card an">
<div style="padding:50px 20px;text-align:center;">
<i class="fas fa-bullhorn" style="font-size:40px;color:var(--bd);margin-bottom:14px;display:block;"></i>
<div style="font-size:15px;font-weight:600;color:var(--tm);">No announcements have been published yet</div>
<div style="font-size:13px;color:var(--tm);margin-top:6px;">Click &quot;Publish Announcement&quot; to create the first one.</div>
</div>
</div>
@endforelse

{{-- Publish Modal --}}
<div class="mo" id="publishModal">
<div class="mb" style="max-width:520px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-bullhorn"></i></div><div><h3>Publish Announcement</h3><div class="tm" style="font-size:12px;">Visible to students and staff portals</div></div><button class="mc" onclick="closeModal('publishModal')"><i class="fas fa-times"></i></button></div>
<form method="POST" action="{{ route('counselor.announcements.store') }}" data-ajax="true">@csrf
<div class="mbody">
<div class="fg"><label class="fl">Title <span style="color:var(--danger);">*</span></label><input type="text" name="title" class="fc" required placeholder="e.g. CHED Applications Now Open"></div>
<div class="fg"><label class="fl">Message <span style="color:var(--danger);">*</span></label><textarea name="body" class="fc" rows="5" required placeholder="Write the full announcement here..."></textarea></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('publishModal')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-paper-plane"></i> Publish</button></div>
</form>
</div>
</div>
@endsection
