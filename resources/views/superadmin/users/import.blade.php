@extends('superadmin.layouts.app')
@section('title','Import Student Accounts')
@section('page-title','Import Student Accounts')
@section('page-sub','Bulk-create student accounts from a CSV file')
@section('content')
<div style="max-width:820px;">
<div style="margin-bottom:14px;"><a href="{{ route('superadmin.users.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back to Users</a></div>

<div class="card an">
<div class="ch"><i class="fas fa-file-import" style="color:var(--y);"></i><h2>Upload Students CSV</h2></div>
<div class="cb">
<p style="font-size:13px;color:var(--tm);margin-bottom:14px;">Upload a CSV file of students. Every row automatically gets a student account created, formatted as:</p>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:10px;margin-bottom:16px;">
<div style="background:var(--card2);border:1px solid var(--bd);border-radius:var(--rs);padding:12px 14px;"><div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tm);margin-bottom:4px;"><i class="fas fa-user" style="color:var(--gm);margin-right:5px;"></i>Username</div><div class="mono" style="font-size:13px;color:#fff;">firstname.lastname@sccpag.edu.ph</div><div style="font-size:11px;color:var(--tm);margin-top:3px;">all lowercase, no spaces</div></div>
<div style="background:var(--card2);border:1px solid var(--bd);border-radius:var(--rs);padding:12px 14px;"><div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tm);margin-bottom:4px;"><i class="fas fa-key" style="color:var(--y);margin-right:5px;"></i>Password</div><div class="mono" style="font-size:13px;color:#fff;">scc&lt;EDP No.&gt;</div><div style="font-size:11px;color:var(--tm);margin-top:3px;">e.g. EDP 194333 → <span class="mono">scc194333</span></div></div>
</div>

<p style="font-size:12px;color:var(--tm);margin-bottom:14px;">CSV header must contain <span class="mono" style="color:#fff;">EDP No.</span>, <span class="mono" style="color:#fff;">First Name</span> and <span class="mono" style="color:#fff;">Last Name</span> columns (an optional <span class="mono" style="color:#fff;">Middle Name</span> column is also accepted). Importing a list already in the system is safe — existing accounts are kept and only corrected when they don't match the format above.</p>

<form method="POST" action="{{ route('superadmin.users.import') }}" enctype="multipart/form-data">
@csrf
<div class="fg">
<label class="fl">CSV File <span style="color:var(--danger);">*</span></label>
<input type="file" name="file" class="fc" accept=".csv,text/csv" required>
@error('file')<div class="alert al-d an" style="margin-top:10px;">{{ $message }}</div>@enderror
</div>
<button type="submit" class="btn btn-p" style="margin-top:14px;"><i class="fas fa-upload"></i> Import &amp; Create Accounts</button>
</form>
</div>
</div>

@if(isset($summary) && !empty($summary))
<div class="card an" style="margin-top:18px;">
<div class="ch"><i class="fas fa-clipboard-check" style="color:var(--gm);"></i><h2>Import Summary</h2><div class="ch-acts"><span class="badge b-p">{{ $summary['total_rows'] ?? 0 }} rows</span></div></div>
<div class="cb">
@if(isset($summary['error']))
<div class="alert al-d an" style="margin-bottom:0;"><i class="fas fa-times-circle"></i><div>{{ $summary['error'] }}</div></div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;">
<div class="sc" style="box-shadow:none;"><div class="si g"><i class="fas fa-user-plus"></i></div><div><div class="lbl">Accounts Created</div><div class="val">{{ $summary['accounts_created'] ?? 0 }}</div></div></div>
<div class="sc" style="box-shadow:none;"><div class="si y"><i class="fas fa-key"></i></div><div><div class="lbl">Passwords Reset</div><div class="val">{{ $summary['accounts_updated'] ?? 0 }}</div></div></div>
<div class="sc" style="box-shadow:none;"><div class="si t"><i class="fas fa-user-graduate"></i></div><div><div class="lbl">Student Records Created</div><div class="val">{{ $summary['students_created'] ?? 0 }}</div></div></div>
<div class="sc" style="box-shadow:none;"><div class="si o"><i class="fas fa-pen"></i></div><div><div class="lbl">Student Records Updated</div><div class="val">{{ $summary['students_updated'] ?? 0 }}</div></div></div>
<div class="sc" style="box-shadow:none;"><div class="si w"><i class="fas fa-check"></i></div><div><div class="lbl">Already Up To Date</div><div class="val">{{ $summary['unchanged'] ?? 0 }}</div></div></div>
<div class="sc" style="box-shadow:none;"><div class="si r"><i class="fas fa-triangle-exclamation"></i></div><div><div class="lbl">Failed Rows</div><div class="val">{{ $summary['failed_rows'] ?? 0 }}</div></div></div>
</div>

@if(!empty($summary['errors']))
<div style="margin-top:16px;">
<div class="fl">Errors</div>
<div class="tw" style="margin-top:8px;">
<table>
<thead><tr><th>Row</th><th>EDP No.</th><th>Message</th></tr></thead>
<tbody>
@foreach($summary['errors'] as $err)
<tr><td class="mono" style="font-size:12px;">{{ $err['row'] ?? '—' }}</td><td class="mono" style="font-size:12px;">{{ $err['edp'] ?? '—' }}</td><td style="font-size:12px;color:var(--tm);">{{ $err['message'] ?? '' }}</td></tr>
@endforeach
</tbody></table>
</div>
</div>
@endif

<div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
<a href="{{ route('superadmin.users.index') }}" class="btn btn-p"><i class="fas fa-users"></i> View Users</a>
<a href="{{ route('superadmin.users.import-form') }}" class="btn btn-o"><i class="fas fa-file-import"></i> Import Another</a>
</div>
@endif
</div>
</div>
@endif
</div>
@endsection
