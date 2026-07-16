@extends('layouts.app')
@section('title', 'Student Profile')
@section('page-title', 'Student Profile')
@section('page-subtitle', 'View and manage student information')

@section('content')
@php $s = $student ?? (object)[
    'student_id' => '2024-0001', 'first_name' => 'Maria', 'last_name' => 'Santos',
    'course' => 'BS Computer Science', 'year_level' => '3rd Year', 'section' => 'A',
    'email' => 'maria@isams.edu.ph', 'contact_number' => '09171234567',
    'sex' => 'Female', 'status' => 'Active', 'gwa' => 1.50,
    'academic_year' => '2023-2024', 'semester' => '2nd',
    'guardian_name' => 'Jose Santos', 'guardian_relationship' => 'Father',
    'guardian_contact' => '09181234567',
    'address' => 'Cagayan de Oro City, Misamis Oriental',
    'enrollment_type' => 'Regular',
]; @endphp

<div class="d-flex gap-2 mb-3">
    <a href="{{ route('students.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    <a href="{{ isset($student) ? route('students.edit', $student->id) : '#' }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit Record</a>
    <a href="#" class="btn btn-outline btn-sm"><i class="fas fa-print"></i> Print</a>
</div>

<div class="grid-2" style="gap:22px;align-items:start;">
    <!-- Profile Card -->
    <div>
        <div class="card animate mb-3">
            <div class="card-body" style="text-align:center;padding:32px;">
                <div class="avatar" style="width:80px;height:80px;font-size:30px;margin:0 auto 16px;background:linear-gradient(135deg,var(--primary),var(--primary-light));">
                    {{ strtoupper(substr($s->first_name ?? 'M', 0, 1)) }}
                </div>
                <div style="font-family:'Sora',sans-serif;font-size:22px;font-weight:700;color:var(--primary);">
                    {{ isset($student) ? $student->full_name : 'Maria Santos' }}
                </div>
                <div class="text-muted" style="font-size:13px;margin:4px 0 12px;" class="font-mono">
                    {{ $s->student_id }}
                </div>
                <span class="badge badge-success" style="font-size:13px;padding:5px 16px;">{{ $s->status }}</span>
            </div>
            <div style="border-top:1px solid var(--border);padding:20px 24px;">
                @foreach([
                    ['Course', $s->course, 'fas fa-graduation-cap'],
                    ['Year Level', $s->year_level . ($s->section ? ' — Sec. '.$s->section : ''), 'fas fa-layer-group'],
                    ['Academic Year', ($s->academic_year ?? '2023-2024') . ' | ' . ($s->semester ?? '2nd') . ' Sem', 'fas fa-calendar'],
                    ['Enrollment Type', $s->enrollment_type ?? 'Regular', 'fas fa-id-badge'],
                    ['GWA', number_format($s->gwa ?? 1.50, 2), 'fas fa-star'],
                ] as $info)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
                    <div style="width:32px;height:32px;background:var(--bg);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--primary-light);">
                        <i class="{{ $info[2] }}" style="font-size:13px;"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">{{ $info[0] }}</div>
                        <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $info[1] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card animate">
            <div class="card-header"><i class="fas fa-address-book" style="color:var(--primary-light);"></i><h2>Contact Information</h2></div>
            <div class="card-body">
                @foreach([
                    ['Email', $s->email ?? 'N/A', 'fas fa-envelope'],
                    ['Contact No.', $s->contact_number ?? 'N/A', 'fas fa-phone'],
                    ['Address', $s->address ?? 'N/A', 'fas fa-map-marker-alt'],
                    ['Guardian', ($s->guardian_name ?? 'N/A') . ($s->guardian_relationship ? ' (' . $s->guardian_relationship . ')' : ''), 'fas fa-user-friends'],
                    ['Guardian Contact', $s->guardian_contact ?? 'N/A', 'fas fa-phone-alt'],
                ] as $c)
                <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
                    <i class="{{ $c[2] }}" style="color:var(--primary-light);margin-top:3px;width:16px;"></i>
                    <div>
                        <div style="font-size:11px;color:var(--text-muted);font-weight:600;">{{ $c[0] }}</div>
                        <div style="font-size:14px;">{{ $c[1] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div>
        <!-- Tabs -->
        <div class="card animate">
            <div style="display:flex;border-bottom:1px solid var(--border);overflow-x:auto;">
                @foreach(['Counseling','Discipline','Scholarships','Organizations'] as $tab)
                <button onclick="showTab('{{ strtolower($tab) }}')" id="tab-{{ strtolower($tab) }}"
                    style="padding:14px 20px;border:none;background:none;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap;
                    color:{{ $loop->first ? 'var(--primary)' : 'var(--text-muted)' }};
                    border-bottom:{{ $loop->first ? '2px solid var(--primary)' : '2px solid transparent' }};
                    transition:all .2s;">
                    {{ $tab }}
                </button>
                @endforeach
            </div>

            <div id="pane-counseling" class="card-body">
                <p class="text-muted mb-2" style="font-size:13px;">Counseling sessions for this student.</p>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Type</th><th>Date</th><th>Status</th><th>Priority</th></tr></thead>
                        <tbody>
                            @forelse($student->counselingSessions ?? [] as $cs)
                            <tr>
                                <td>{{ $cs->concern_type }}</td>
                                <td class="font-mono text-muted">{{ $cs->session_date?->format('M d, Y') ?? '—' }}</td>
                                <td><span class="badge badge-primary">{{ $cs->status }}</span></td>
                                <td><span class="badge badge-{{ $cs->priority=='High'?'danger':($cs->priority=='Medium'?'warning':'gray') }}">{{ $cs->priority }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:24px;">No counseling sessions on record.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-2"><a href="{{ route('counseling.create') }}" class="btn btn-outline btn-sm"><i class="fas fa-plus"></i> Add Session</a></div>
            </div>

            <div id="pane-discipline" class="card-body" style="display:none;">
                <p class="text-muted mb-2" style="font-size:13px;">Discipline cases involving this student.</p>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Case No.</th><th>Violation</th><th>Date Filed</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($student->disciplineCases ?? [] as $dc)
                            <tr>
                                <td class="font-mono" style="font-size:12px;">{{ $dc->case_number }}</td>
                                <td>{{ $dc->violation_type }}</td>
                                <td class="text-muted font-mono" style="font-size:12px;">{{ $dc->created_at?->format('M d, Y') }}</td>
                                <td><span class="badge badge-warning">{{ $dc->status }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:24px;">No discipline cases on record.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="pane-scholarships" class="card-body" style="display:none;">
                <p class="text-muted mb-2" style="font-size:13px;">Scholarships and grants.</p>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Scholarship</th><th>Type</th><th>Awarded</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($student->scholarships ?? [] as $sch)
                            <tr>
                                <td class="fw-semi">{{ $sch->name }}</td>
                                <td class="text-muted">{{ $sch->type }}</td>
                                <td class="text-muted font-mono" style="font-size:12px;">{{ $sch->pivot->awarded_at ?? '—' }}</td>
                                <td><span class="badge badge-success">{{ $sch->pivot->status ?? 'Active' }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:24px;">No scholarship grants.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="pane-organizations" class="card-body" style="display:none;">
                <p class="text-muted mb-2" style="font-size:13px;">Organization memberships.</p>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    @forelse($student->organizations ?? [] as $org)
                    <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-users" style="color:var(--primary-light);"></i>
                        <span class="fw-semi" style="font-size:13px;">{{ $org->name }}</span>
                        <span class="badge badge-gray" style="font-size:10px;">{{ $org->pivot->role ?? 'Member' }}</span>
                    </div>
                    @empty
                    <p style="color:var(--text-muted);font-size:14px;">Not a member of any organization.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(name) {
    ['counseling','discipline','scholarships','organizations'].forEach(t => {
        document.getElementById('pane-'+t).style.display = t === name ? '' : 'none';
        const btn = document.getElementById('tab-'+t);
        btn.style.color = t === name ? 'var(--primary)' : 'var(--text-muted)';
        btn.style.borderBottom = t === name ? '2px solid var(--primary)' : '2px solid transparent';
    });
}
</script>
@endpush
@endsection
