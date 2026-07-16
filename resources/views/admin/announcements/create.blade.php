@extends('admin.layouts.app')
@section('title','New Announcement')
@section('page-title','New Announcement')
@section('content')
<div style="max-width:680px;">
<form method="POST" action="{{ route('admin.announcements.store') }}">@csrf
<div class="card an mb3"><div class="ch"><i class="fas fa-bullhorn" style="color:var(--gm);"></i><h2>Announcement Details</h2></div><div class="cb">
<div class="fg"><label class="fl">Title <span style="color:var(--danger);">*</span></label><input type="text" name="title" class="fc" required placeholder="e.g. CHED Applications Now Open"></div>
<div class="fg"><label class="fl">Message <span style="color:var(--danger);">*</span></label><textarea name="body" class="fc" rows="5" required placeholder="Write the full announcement here..."></textarea></div>
<div class="g2">
<div class="fg"><label class="fl">Type</label><select name="type" class="fc"><option value="Scholarship">Scholarship</option><option value="Renewal">Renewal</option><option value="General">General</option><option value="System">System</option></select></div>
<div class="fg"><label class="fl">Priority</label><select name="priority" class="fc"><option value="Normal">Normal</option><option value="High">High</option><option value="Urgent">Urgent</option></select></div>
</div>
</div></div>
<div style="display:flex;gap:10px;"><button type="submit" class="btn btn-p"><i class="fas fa-paper-plane"></i> Publish</button><a href="{{ route('admin.announcements.index') }}" class="btn btn-o">Cancel</a></div>
</form></div>
@endsection
