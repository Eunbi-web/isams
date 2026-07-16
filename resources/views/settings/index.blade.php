@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'System Settings')
@section('page-subtitle', 'Manage account and system preferences')

@section('content')
<div style="max-width:760px;">
    <div class="card animate mb-3">
        <div class="card-header">
            <i class="fas fa-user-circle" style="color:var(--primary-light);"></i>
            <h2>Account Settings</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ auth()->user()->name ?? 'System Administrator' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ auth()->user()->email ?? 'admin@isams.edu.ph' }}">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->role ?? 'Administrator' }}" disabled style="background:#f7fafc;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" value="{{ auth()->user()->department ?? 'Office of Student Affairs' }}">
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--border);margin:20px 0;">
                <div class="mb-2 fw-semi" style="color:var(--primary);">Appearance</div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Theme</label>
                        @php
                            $theme = auth()->user()->theme ?? 'system';
                        @endphp
                        <select name="theme" class="form-control">
                            <option value="system" {{ $theme === 'system' ? 'selected' : '' }}>System</option>
                            <option value="light" {{ $theme === 'light' ? 'selected' : '' }}>Light</option>
                            <option value="dark" {{ $theme === 'dark' ? 'selected' : '' }}>Dark</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preview</label>
                        <div class="card" style="padding:14px; background:transparent; box-shadow:none; border-style:dashed;">
                            <div style="font-weight:700;color:var(--text);">This page will update across the whole system.</div>
                            <div class="text-muted" style="font-size:13px;margin-top:6px;">Your selection is saved in your account settings.</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top:10px;"><i class="fas fa-save"></i> Save Changes</button>

                <hr style="border:none;border-top:1px solid var(--border);margin:20px 0;">
                <div class="mb-2 fw-semi" style="color:var(--primary);">Change Password</div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </form>
        </div>
    </div>

    <div class="card animate mb-3">
        <div class="card-header">
            <i class="fas fa-university" style="color:var(--accent);"></i>
            <h2>Institution Settings</h2>
        </div>
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Institution Name</label>
                    <input type="text" class="form-control" value="Your University">
                </div>
                <div class="form-group">
                    <label class="form-label">Academic Year</label>
                    <input type="text" class="form-control" value="2023-2024">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Current Semester</label>
                    <select class="form-control">
                        <option>1st Semester</option>
                        <option selected>2nd Semester</option>
                        <option>Summer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">System Timezone</label>
                    <select class="form-control">
                        <option selected>Asia/Manila (UTC+8)</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary"><i class="fas fa-save"></i> Save Institution Settings</button>
        </div>
    </div>

    <div class="card animate">
        <div class="card-header">
            <i class="fas fa-users-cog" style="color:var(--success);"></i>
            <h2>System Users</h2>
            <div class="card-actions"><a href="#" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add User</a></div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Last Login</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach([
                        ['System Administrator','admin@isams.edu.ph','Administrator','Just now','Active'],
                        ['Dr. Maria Rivera','counselor@isams.edu.ph','Counselor','1 hour ago','Active'],
                        ['Atty. Jose Santos','discipline@isams.edu.ph','Discipline Officer','2 hours ago','Active'],
                        ['Ms. Ana Cruz','scholarship@isams.edu.ph','Scholarship Officer','Yesterday','Active'],
                    ] as $u)
                    <tr>
                        <td>
                            <div class="d-flex align-center gap-2">
                                <div class="avatar avatar-sm">{{ substr($u[0],0,1) }}</div>
                                <span class="fw-semi">{{ $u[0] }}</span>
                            </div>
                        </td>
                        <td class="text-muted" style="font-size:13px;">{{ $u[1] }}</td>
                        <td><span class="badge badge-primary">{{ $u[2] }}</span></td>
                        <td class="text-muted" style="font-size:12px;">{{ $u[3] }}</td>
                        <td><span class="badge badge-success">{{ $u[4] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
