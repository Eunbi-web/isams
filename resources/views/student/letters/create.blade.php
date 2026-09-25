@extends('student.layouts.app')
@section('title','Write Confiscated Item Letter')
@section('page-title','Write Confiscated Item Letter')
@section('page-sub','Fill in the details and edit the letter template below')
@push('styles')
<style>
.lt-toolbar{background:#f1f3f4;border-bottom:1px solid #dadce0;padding:6px 12px;display:flex;gap:8px;align-items:center;}
.lt-tool{background:#fff;border:1px solid #dadce0;border-radius:4px;min-width:30px;height:28px;padding:0 9px;font-size:13px;cursor:pointer;color:#3c4043;font-family:inherit;display:inline-flex;align-items:center;justify-content:center;gap:5px;}
.lt-tool:hover{background:#e8eaed;}
.lt-tool.fmt{font-weight:700;}
.lt-tool.fmt i{font-style:italic;}
.lt-tool.fmt u{text-decoration:underline;}
.lt-doc{background:#ffffff;width:100%;min-height:500px;padding:60px;font-family:Georgia,serif;font-size:12pt;line-height:1.8;box-shadow:0 2px 12px rgba(0,0,0,0.15);border-radius:4px;color:#202124;outline:none;}
.lt-doc p{margin:0 0 2px;}
.ph{color:#1a6b2f;background:#e8f5ec;border-radius:3px;padding:0 2px;}
</style>
@endpush
@section('content')
<div class="card an">
<div class="ch"><i class="fas fa-file-signature" style="color:var(--gm);"></i><h2>Confiscated Item Letter Editor</h2></div>
<div class="cb">
<form method="POST" action="{{ route('student.letters.store') }}" id="letterForm">
@csrf
<div style="display:grid;grid-template-columns:30% 1fr;gap:18px;align-items:start;" class="lt-grid">
<div>
<h3 style="font-family:'Sora',sans-serif;font-size:13px;color:var(--g);margin-bottom:12px;"><i class="fas fa-box" style="margin-right:6px;"></i>Item Details</h3>
<div class="fg"><label class="fl">Item Description <span style="color:var(--danger);">*</span></label>
<textarea name="item_description" id="itemDescription" class="fc" rows="3" required placeholder="e.g. Samsung A35 mobile phone">{{ old('item_description') }}</textarea></div>
<div class="fg"><label class="fl">Date Confiscated</label>
<input type="date" name="date_confiscated" id="dateConfiscated" class="fc" value="{{ old('date_confiscated') }}"></div>
<div class="fg"><label class="fl">Confiscated By</label>
<input type="text" name="confiscated_by" id="confiscatedBy" class="fc" value="{{ old('confiscated_by') }}" placeholder="e.g. Mr. Santos — Security Guard"></div>
<div class="fg"><label class="fl">Reason Confiscated</label>
<textarea name="reason_confiscated" id="reasonConfiscated" class="fc" rows="2" placeholder="e.g. Using phone during class">{{ old('reason_confiscated') }}</textarea></div>
<div class="alert al-w" style="font-size:12px;margin-bottom:0;"><i class="fas fa-lightbulb"></i><span>The letter updates automatically as you fill in these fields.</span></div>
</div>
<div>
<div class="lt-toolbar">
<button type="button" class="lt-tool fmt" onclick="fmtCmd('bold')" title="Bold"><i>B</i></button>
<button type="button" class="lt-tool fmt" onclick="fmtCmd('italic')" title="Italic"><i style="font-style:italic;">I</i></button>
<button type="button" class="lt-tool fmt" onclick="fmtCmd('underline')" title="Underline"><u>U</u></button>
<span style="flex:1;"></span>
<button type="button" class="lt-tool" onclick="resetTemplate()"><i class="fas fa-undo"></i> Reset Template</button>
</div>
<div class="lt-doc" id="letterEditor" contenteditable="true"></div>
</div>
</div>
<textarea name="letter_content" id="letterContentHidden" style="display:none;"></textarea>
<div style="display:flex;gap:9px;align-items:center;margin-top:18px;">
<button type="submit" class="btn btn-p"><i class="fas fa-paper-plane"></i> Submit Letter</button>
<a href="{{ route('student.letters') }}" class="btn btn-o">Cancel</a>
</div>
</form>
</div>
</div>
@push('scripts')
<script>
var studentName = @json(trim(($student->first_name ?? '').' '.($student->last_name ?? '')));
var studentCourse = @json($student->course ?? '');
var studentYear = @json($student->year_level ?? '');
var studentEdp = @json($student->student_id ?? '');
var letterDate = @json(now()->format('F j, Y'));
var originalTemplate = '';

function buildTemplate(){
    return '<p>' + letterDate + '</p>'
    + '<p>&nbsp;</p><p>&nbsp;</p>'
    + '<p>The Student Affairs Officer</p>'
    + '<p>Saint Columban College</p>'
    + '<p>Pagadian City, Zamboanga del Sur</p>'
    + '<p>&nbsp;</p><p>&nbsp;</p>'
    + '<p><strong>RE: REQUEST FOR THE RELEASE OF CONFISCATED ITEM</strong></p>'
    + '<p>&nbsp;</p><p>&nbsp;</p>'
    + '<p>Dear Sir/Ma\u2019am,</p>'
    + '<p>&nbsp;</p><p>&nbsp;</p>'
    + '<p>I, <strong>' + studentName + '</strong>, a ' + (studentYear || '___') + ' student of ' + (studentCourse || '___') + ' with Student ID ' + (studentEdp || '___') + ', would like to formally request the release of my confiscated item. The item was confiscated on <span class="ph" id="phDate">________</span> by <span class="ph" id="phBy">________</span> due to <span class="ph" id="phReason">________</span>.</p>'
    + '<p>&nbsp;</p>'
    + '<p>I fully understand and acknowledge the school policy regarding the confiscation of said item, and I sincerely apologize for any violation I may have committed. I assure the school administration that this will not happen again, and I will abide by all school rules and regulations going forward.</p>'
    + '<p>&nbsp;</p>'
    + '<p>In light of the above, I respectfully request that my item, specifically <span class="ph" id="phItem">________</span>, be released to me at your earliest convenience. I am willing to comply with any additional requirements or conditions that the school may impose before the release of the item.</p>'
    + '<p>&nbsp;</p><p>&nbsp;</p>'
    + '<p>Respectfully yours,</p>'
    + '<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>'
    + '<p><strong>' + studentName + '</strong></p>'
    + '<p>Student ID: ' + (studentEdp || '___') + '</p>'
    + '<p>' + (studentCourse || '___') + ' \u2014 ' + (studentYear || '___') + '</p>';
}

function syncFromField(){
    var map = {phItem:'itemDescription', phDate:'dateConfiscated', phBy:'confiscatedBy', phReason:'reasonConfiscated'};
    Object.keys(map).forEach(function(phId){
        var el = document.getElementById(phId);
        if(!el) return;
        var src = document.getElementById(map[phId]);
        var v = (src && src.value.trim()) ? src.value.trim() : '________';
        if(phId === 'phDate' && src && src.value.trim()){
            var d = new Date(src.value + 'T00:00:00');
            v = isNaN(d) ? src.value : d.toLocaleDateString('en-US', {year:'numeric', month:'long', day:'numeric'});
        }
        el.textContent = v;
    });
}

function fmtCmd(cmd){ document.execCommand(cmd, false, null); document.getElementById('letterEditor').focus(); }

function resetTemplate(){ document.getElementById('letterEditor').innerHTML = originalTemplate; }

(function(){
    var editor = document.getElementById('letterEditor');
    originalTemplate = buildTemplate();
    editor.innerHTML = originalTemplate;

    ['itemDescription','dateConfiscated','confiscatedBy','reasonConfiscated'].forEach(function(id){
        var el = document.getElementById(id);
        if(el) el.addEventListener('input', syncFromField);
    });

    document.getElementById('letterForm').addEventListener('submit', function(){
        document.getElementById('letterContentHidden').value = document.getElementById('letterEditor').innerHTML;
    });
})();
</script>
@endpush
@endsection
