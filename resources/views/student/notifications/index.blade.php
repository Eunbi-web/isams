@extends('student.layouts.app')
@section('title','Notifications')
@section('page-title','Notifications')
@section('page-sub','Your scholarship updates and system notifications')
@section('content')

<div class="card an">
    <div class="ch">
        <i class="fas fa-bell" style="color:var(--gm);"></i>
        <h2>All Notifications</h2>
        <span class="badge b-p" style="margin-left:6px;">{{ $notifications->total() }}</span>
        <div class="ch-acts">
            @if($notifications->total() > 0)
            <button type="button" onclick="markAllStudentRead()" class="btn btn-o btn-sm">
                <i class="fas fa-check-double"></i> Mark All Read
            </button>
            @endif
        </div>
    </div>
    <div>
        @forelse($notifications as $notif)
        @php
            $icons = [
                'scholarship'  => ['icon'=>'fas fa-award',       'bg'=>'#d0f0d8','color'=>'#1a6b2f'],
                'application'  => ['icon'=>'fas fa-file-alt',    'bg'=>'#dce8ff','color'=>'#0038a8'],
                'counseling'   => ['icon'=>'fas fa-comments',    'bg'=>'#fef3cd','color'=>'#a07c00'],
                'announcement' => ['icon'=>'fas fa-bullhorn',    'bg'=>'#fde8e6','color'=>'#c0392b'],
                'system'       => ['icon'=>'fas fa-cog',         'bg'=>'#f0f0f0','color'=>'#5a5a5a'],
            ];
            $cfg = $icons[$notif->type] ?? ['icon'=>'fas fa-bell','bg'=>'#e8f5ec','color'=>'#2d9e4f'];
        @endphp
        <div style="display:flex;align-items:flex-start;gap:13px;padding:14px 20px;border-bottom:1px solid var(--bd);background:{{ !$notif->read ? '#f0faf2' : '#fff' }};transition:background .2s;" id="notif-{{ $notif->id }}">
            <div style="width:38px;height:38px;border-radius:50%;background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">
                <i class="{{ $cfg['icon'] }}"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:{{ !$notif->read ? '700' : '500' }};color:var(--tx);margin-bottom:3px;">
                    {{ $notif->title }}
                    @if(!$notif->read)
                    <span style="display:inline-block;width:7px;height:7px;background:var(--gm);border-radius:50%;margin-left:6px;vertical-align:middle;"></span>
                    @endif
                </div>
                <div style="font-size:13px;color:var(--tm);line-height:1.5;margin-bottom:5px;">{{ $notif->message }}</div>
                <div style="font-size:11px;color:var(--tm);">
                    <i class="fas fa-clock" style="margin-right:4px;"></i>
                    {{ $notif->created_at->diffForHumans() }}
                    &nbsp;·&nbsp;
                    <span class="badge {{ $notif->read ? 'b-gray' : 'b-s' }}" style="font-size:10px;">
                        {{ $notif->read ? 'Read' : 'Unread' }}
                    </span>
                </div>
            </div>
            @if(!$notif->read)
            <button type="button"
                onclick="markOneRead({{ $notif->id }})"
                style="background:none;border:1px solid var(--bd);border-radius:7px;padding:5px 11px;font-size:11px;color:var(--gm);cursor:pointer;font-weight:600;white-space:nowrap;flex-shrink:0;">
                Mark read
            </button>
            @endif
        </div>
        @empty
        <div style="padding:40px;text-align:center;">
            <i class="fas fa-bell-slash" style="font-size:36px;color:var(--bd);margin-bottom:12px;display:block;"></i>
            <div style="font-size:15px;font-weight:600;color:var(--tm);">No notifications yet</div>
            <div style="font-size:13px;color:var(--tm);margin-top:4px;">Scholarship updates and application status changes will appear here.</div>
        </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div style="padding:13px 18px;border-top:1px solid var(--bd);">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

<script>
var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function markOneRead(id) {
    fetch('/student/notifications/' + id, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json'},
        body: JSON.stringify({_method: 'PATCH'})
    }).then(function() {
        var row = document.getElementById('notif-' + id);
        if (row) {
            row.style.background = '#fff';
            var title = row.querySelector('[style*="font-weight"]');
            if (title) title.style.fontWeight = '500';
            var dot = row.querySelector('[style*="border-radius:50%;margin-left"]');
            if (dot) dot.remove();
            var btn = row.querySelector('button');
            if (btn) btn.remove();
            var badge = row.querySelector('.badge');
            if (badge) { badge.className = 'badge b-gray'; badge.textContent = 'Read'; }
        }
        if (window.isamsToast) isamsToast('Marked as read.', 'success');
    });
}

function markAllStudentRead() {
    fetch('/student/notifications/mark-all', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json'},
        body: JSON.stringify({})
    }).then(function() {
        if (window.isamsToast) isamsToast('All notifications marked as read.', 'success');
        setTimeout(function() { window.location.reload(); }, 800);
    });
}
</script>
@endsection
