@extends('admin.layouts.app')
@section('title','Edit Announcement')
@section('page-title','Edit Announcement')
@section('content')
<div style="max-width:680px;">
<form method="POST" action="{{ route('admin.announcements.update',$announcement->id) }}">@csrf @method('PUT')
<div class="card an mb3"><div class="ch"><i class="fas fa-bullhorn" style="color:var(--gm);"></i><h2>Edit Announcement</h2></div><div class="cb">
<div class="fg"><label class="fl">Title</label><input type="text" name="title" class="fc" value="{{ $announcement->title }}" required></div>
<div class="fg"><label class="fl">Message</label><textarea name="body" class="fc" rows="5" required>{{ $announcement->body }}</textarea></div>
<div class="g2">
<div class="fg"><label class="fl">Type</label><select name="type" class="fc">@foreach(['Scholarship','Renewal','General','System'] as $t)<option value="{{ $t }}" {{ $announcement->type===$t?'selected':'' }}>{{ $t }}</option>@endforeach</select></div>
<div class="fg"><label class="fl">Priority</label><select name="priority" class="fc">@foreach(['Normal','High','Urgent'] as $p)<option value="{{ $p }}" {{ $announcement->priority===$p?'selected':'' }}>{{ $p }}</option>@endforeach</select></div>
</div>
</div></div>
<div style="display:flex;gap:10px;"><button type="submit" class="btn btn-p"><i class="fas fa-save"></i> Update</button><a href="{{ route('admin.announcements.index') }}" class="btn btn-o">Cancel</a></div>
</form></div>
@endsection
