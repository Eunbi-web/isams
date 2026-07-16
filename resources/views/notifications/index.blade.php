@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', 'System alerts and updates')

@section('content')
<div style="max-width:760px;">
    <div class="d-flex justify-between align-center mb-3">
        <span class="text-muted" style="font-size:14px;">You have <strong>5 unread</strong> notifications</span>
        <a href="#" class="btn btn-outline btn-sm"><i class="fas fa-check-double"></i> Mark All Read</a>
    </div>

    <div class="card animate">
        <div class="card-body" style="padding:0;">
            @foreach([
                ['fas fa-user-plus','New Student Enrolled','Maria Santos has been enrolled in BS Computer Science (2024-0001).','2 minutes ago','unread','success'],
                ['fas fa-heart','Urgent Counseling Request','Mark Vilar has submitted a high-priority counseling request regarding anxiety.','15 minutes ago','unread','danger'],
                ['fas fa-gavel','Discipline Case Filed','A new discipline case (2024-DC-014) has been filed against Carlos Mendoza.','1 hour ago','unread','warning'],
                ['fas fa-award','Scholarship Application','67 new scholarship applications are pending review.','3 hours ago','unread','gold'],
                ['fas fa-calendar-check','Event Reminder','Leadership Summit 2024 is scheduled for tomorrow (Apr 25).','5 hours ago','unread','primary'],
                ['fas fa-chart-bar','Monthly Report Ready','The April 2024 monthly report has been generated and is ready for review.','Yesterday','read','info'],
                ['fas fa-users','Organization Registration','CS Society has submitted their annual re-registration requirements.','2 days ago','read','teal'],
                ['fas fa-exclamation-triangle','GWA Alert','8 scholars are currently on academic probation due to low GWA.','3 days ago','read','warning'],
            ] as $n)
            <div style="display:flex;gap:14px;padding:18px 22px;border-bottom:1px solid var(--border);background:{{ $n[4]=='unread' ? '#fafcff' : 'transparent' }};transition:background .2s;"
                 onmouseover="this.style.background='#f7fafc'" onmouseout="this.style.background='{{ $n[4]=='unread' ? '#fafcff' : 'transparent' }}'">
                <div style="width:44px;height:44px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:16px;
                    background:{{ $n[5]=='success'?'#e2f5ec':($n[5]=='danger'?'#fde8e6':($n[5]=='warning'?'#fef3cd':($n[5]=='gold'?'#fef6e0':($n[5]=='info'?'#e0f3f8':'#e8f0fb')))) }};
                    color:{{ $n[5]=='success'?'var(--success)':($n[5]=='danger'?'var(--danger)':($n[5]=='warning'?'var(--warning)':($n[5]=='gold'?'#c8972b':($n[5]=='info'?'var(--info)':'var(--primary-light)')))) }};">
                    <i class="{{ $n[0] }}"></i>
                </div>
                <div style="flex:1;">
                    <div class="fw-semi" style="font-size:14px;{{ $n[4]=='unread' ? 'color:var(--primary);' : '' }}">
                        {{ $n[1] }}
                        @if($n[4]=='unread')
                            <span style="display:inline-block;width:7px;height:7px;background:var(--accent);border-radius:50%;margin-left:6px;"></span>
                        @endif
                    </div>
                    <div class="text-muted" style="font-size:13px;margin:3px 0;">{{ $n[2] }}</div>
                    <div style="font-size:11px;color:var(--text-muted);"><i class="fas fa-clock" style="margin-right:4px;"></i>{{ $n[3] }}</div>
                </div>
                @if($n[4]=='unread')
                <a href="#" class="btn btn-outline btn-sm" style="align-self:center;white-space:nowrap;font-size:11px;">Mark Read</a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
