@extends('layouts.app')
@section('title', 'Organizations')
@section('page-title', 'Student Organizations')
@section('page-subtitle', 'Manage recognized student organizations and activities')

@section('content')
<div class="d-flex align-center justify-between mb-3">
    <div class="d-flex gap-2">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" placeholder="Search organizations..." style="width:260px;" id="orgSearch">
        </div>
        <select class="form-control" style="width:160px;">
            <option value="">All Types</option>
            <option>Academic</option>
            <option>Cultural</option>
            <option>Sports</option>
            <option>Religious</option>
            <option>Service</option>
            <option>Political</option>
        </select>
    </div>
    <a href="{{ route('organizations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Register Org
    </a>
</div>

<div class="stats-grid mb-3" style="grid-template-columns:repeat(4,1fr);">
    <div class="stat-card animate delay-1"><div class="stat-icon blue"><i class="fas fa-users"></i></div><div class="stat-info"><div class="label">Total Organizations</div><div class="value">38</div></div></div>
    <div class="stat-card animate delay-2"><div class="stat-icon green"><i class="fas fa-check-circle"></i></div><div class="stat-info"><div class="label">Recognized</div><div class="value">34</div></div></div>
    <div class="stat-card animate delay-3"><div class="stat-icon gold"><i class="fas fa-user-friends"></i></div><div class="stat-info"><div class="label">Total Members</div><div class="value">2,140</div></div></div>
    <div class="stat-card animate delay-4"><div class="stat-icon teal"><i class="fas fa-calendar-check"></i></div><div class="stat-info"><div class="label">Events Conducted</div><div class="value">84</div></div></div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px;" id="orgsGrid">
    @foreach([
        ['CS Society','Academic','BS Computer Science Students','2018','125','Active','blue','fas fa-laptop-code'],
        ['Supreme Student Government','Political','University Student Government','2000','48','Active','gold','fas fa-landmark'],
        ['Cultural Dance Troupe','Cultural','Performing Arts & Culture','2010','62','Active','purple','fas fa-theater-masks'],
        ['Red Cross Youth','Service','Community & Emergency Services','2005','94','Active','red','fas fa-first-aid'],
        ['Nursing Students Assoc.','Academic','BS Nursing Students','2012','138','Active','teal','fas fa-heartbeat'],
        ['ROTC Corps','Service','Military Training & Service','2001','200','Active','green','fas fa-shield-alt'],
        ['Music Ensemble','Cultural','Choir and Band','2015','56','Active','purple','fas fa-music'],
        ['Business Org','Academic','BS Business Admin Students','2014','110','Active','blue','fas fa-briefcase'],
        ['Sports Council','Sports','University Athletics','2003','320','Active','green','fas fa-running'],
        ['Environment Club','Service','Environmental Awareness','2019','78','Active','teal','fas fa-leaf'],
        ['Engineering Society','Academic','BS Engineering Students','2011','145','Active','blue','fas fa-cog'],
        ['Campus Ministry','Religious','Faith & Spiritual Formation','2002','230','Active','gold','fas fa-praying-hands'],
    ] as $org)
    <div class="card animate" style="transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 28px rgba(26,58,92,0.15)';" onmouseout="this.style.transform='';this.style.boxShadow='';">
        <div class="card-body">
            <div class="d-flex align-center gap-3 mb-2">
                <div class="stat-icon {{ $org[7] == 'fas fa-laptop-code' || $org[7]=='fas fa-briefcase'||$org[7]=='fas fa-cog'||$org[7]=='fas fa-engineering' ? 'blue' : ($org[7]=='fas fa-landmark'||$org[7]=='fas fa-praying-hands' ? 'gold' : ($org[7]=='fas fa-theater-masks'||$org[7]=='fas fa-music' ? 'purple' : ($org[7]=='fas fa-first-aid' ? 'red' : ($org[7]=='fas fa-heartbeat'||$org[7]=='fas fa-leaf' ? 'teal' : 'green')))) }}"
                     style="border-radius:12px;width:48px;height:48px;font-size:18px;flex-shrink:0;">
                    <i class="{{ $org[7] }}"></i>
                </div>
                <div style="flex:1;">
                    <div class="fw-semi" style="font-size:15px;">{{ $org[0] }}</div>
                    <span class="badge badge-gray" style="font-size:10px;">{{ $org[1] }}</span>
                </div>
                <span class="badge badge-success">{{ $org[5] }}</span>
            </div>
            <div class="text-muted" style="font-size:13px;margin-bottom:12px;line-height:1.5;">{{ $org[2] }}</div>
            <div style="display:flex;gap:16px;font-size:12px;color:var(--text-muted);border-top:1px solid var(--border);padding-top:12px;">
                <span><i class="fas fa-users" style="margin-right:4px;"></i>{{ $org[4] }} members</span>
                <span><i class="fas fa-calendar" style="margin-right:4px;"></i>Est. {{ $org[3] }}</span>
                <a href="#" style="margin-left:auto;color:var(--primary-light);font-weight:600;text-decoration:none;">View <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
document.getElementById('orgSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#orgsGrid .card').forEach(card => {
        card.style.display = card.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
