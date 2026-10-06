@extends('admin.layouts.app')
@section('title','Send Message to Student')
@section('page-title','Send Message to Student')
@section('page-sub','One-way message that the student can read in their portal')
@section('content')
<div class="card an">
<div class="ch"><i class="fas fa-paper-plane" style="color:var(--gm);"></i><h2>Send Message to Student</h2></div>
<div class="cb">
<form method="POST" action="{{ route('admin.messages.store') }}" id="msgForm">
@csrf
<div class="fg"><label class="fl">Recipient EDP <span style="color:var(--danger);">*</span></label>
<input type="text" id="edpInput" class="fc mono" autocomplete="off" placeholder="Type the student EDP number (e.g. 099001)" value="{{ old('edp_q') }}" style="max-width:340px;">
<input type="hidden" name="student_id" id="studentIdInput" value="{{ old('student_id') }}">
<div id="edpResult" style="margin-top:8px;font-size:13px;">
@if ($selected)
<span style="color:var(--g);font-weight:600;"><i class="fas fa-user-check"></i> <span class="mono">{{ $selected->student_id }}</span> — {{ $selected->full_name }}</span>
@else
<span style="color:var(--tm);">Type the student EDP above to find their name.</span>
@endif
</div></div>
<div class="fg"><label class="fl">Subject <span style="color:var(--danger);">*</span></label>
<input type="text" name="subject" class="fc" value="{{ old('subject') }}" required placeholder="Enter the message subject"></div>
<div class="fg"><label class="fl">Message Body <span style="color:var(--danger);">*</span></label>
<textarea name="body" class="fc" rows="8" required placeholder="Write your message to the student here...">{{ old('body') }}</textarea></div>
<div style="display:flex;gap:9px;align-items:center;">
<button type="submit" class="btn btn-p"><i class="fas fa-paper-plane"></i> Send Message</button>
<a href="{{ route('admin.messages.index') }}" class="btn btn-o">Cancel</a>
</div>
</form>
</div>
</div>

<script>
(function () {
    var input = document.getElementById('edpInput');
    var hidden = document.getElementById('studentIdInput');
    var box = document.getElementById('edpResult');
    var form = document.getElementById('msgForm');
    var timer = null;
    var lastQ = null;
    var foundId = '';

    function esc(s) {
        var d = document.createElement('div');
        d.textContent = s == null ? '' : String(s);
        return d.innerHTML;
    }

    function showIdle() {
        box.innerHTML = '<span style="color:var(--tm);">Type the student EDP above to find their name.</span>';
    }

    function lookup() {
        var q = input.value.trim();
        if (q === lastQ) return;
        lastQ = q;
        foundId = '';
        hidden.value = '';
        if (q.length < 3) { showIdle(); return; }
        box.innerHTML = '<span style="color:var(--tm);"><i class="fas fa-spinner fa-spin"></i> Searching...</span>';
        fetch("{{ route('admin.messages.lookup') }}?edp=" + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d && d.ok) {
                    foundId = d.student.id;
                    hidden.value = foundId;
                    box.innerHTML = '<span style="color:var(--g);font-weight:600;"><i class="fas fa-user-check"></i> <span class="mono">' + esc(d.student.student_id) + '</span> — ' + esc(d.student.full_name) + '</span>';
                } else {
                    hidden.value = '';
                    box.innerHTML = '<span style="color:var(--danger);font-weight:600;"><i class="fas fa-user-times"></i> No student found for EDP "' + esc(q) + '"</span>';
                }
            })
            .catch(function () {
                box.innerHTML = '<span style="color:var(--danger);">Lookup failed — please try again.</span>';
            });
    }

    // Keep the server-rendered "old" recipient visible until the user edits the field
    var oldRestored = hidden.value !== '';
    if (oldRestored) {
        lastQ = input.value.trim();
        input.addEventListener('input', function () { oldRestored = false; }, { once: true });
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(lookup, 350);
    });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); clearTimeout(timer); lastQ = null; lookup(); }
    });
    form.addEventListener('submit', function (e) {
        if (!hidden.value) {
            e.preventDefault();
            input.focus();
            lastQ = null;
            lookup();
        }
    });
})();
</script>
@endsection
