@extends('admin.layouts.app')
@section('title','AI Comprehensive Report')
@section('page-title','ISAMS AI Comprehensive Report Generator')
@section('page-sub','One Button. One Complete Report. Everything DSA Needs.')
@section('content')
<style>
.report-doc{background:#fff;color:#1c1c1c;font-family:Georgia,'Times New Roman',serif;font-size:13.5px;line-height:1.65;padding:36px 42px;border-radius:12px;min-height:460px;}
.report-doc h1,.report-doc h2{color:#12355f;}
.report-doc h2{font-size:16px;border-bottom:1.5px solid #12355f;padding-bottom:5px;margin:22px 0 10px;}
.report-doc h3{font-size:14px;color:#12355f;margin:16px 0 8px;}
.report-doc p{margin:0 0 11px;text-align:justify;}
.report-doc table{width:100%;border-collapse:collapse;margin:10px 0 16px;font-size:11.5px;}
.report-doc th{background:#e8eef7;border:1px solid #9db0cb;padding:6px 8px;text-align:left;font-size:10.5px;text-transform:uppercase;letter-spacing:.4px;color:#12355f;}
.report-doc td{border:1px solid #c3cfdf;padding:6px 8px;font-size:12px;}
.report-doc ul,.report-doc ol{margin:0 0 12px 22px;padding:0;}
.report-doc li{margin-bottom:5px;}
.report-empty{min-height:460px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;color:var(--tm);text-align:center;padding:40px 20px;}
.report-empty i{font-size:44px;color:var(--bd);}
.report-loading{min-height:460px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;color:var(--tm);text-align:center;padding:40px 20px;}
.report-loading i{font-size:38px;color:var(--gm);}
.scope-chip{display:flex;align-items:center;gap:9px;border:1px solid var(--bd);border-radius:10px;padding:10px 14px;cursor:pointer;user-select:none;transition:all .15s;}
.scope-chip input{width:auto;accent-color:var(--g);}
.scope-chip.checked{border-color:var(--g);background:rgba(60,160,90,.08);}
</style>

<div class="card an">
<div class="ch"><i class="fas fa-wand-magic-sparkles" style="color:var(--yd);"></i><h2>Generate Comprehensive Report</h2><span class="badge b-p">AI</span></div>
<div class="cb">
<div class="alert al-ai" style="margin-bottom:14px;"><i class="fas fa-robot"></i><span><strong>AI-assisted Narrative &amp; Summative Report</strong> — when you click <strong>Generate Report</strong>, Groq AI reads all the relevant data and writes one complete, professional, ready-to-print narrative document covering scholarship status, scholar performance, and counseling services — in one go.</span></div>
<div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
<div class="fg" style="margin:0;min-width:270px;"><label class="fl">Report Scope (what to include)</label>
<div style="display:flex;gap:8px;flex-wrap:wrap;">
<label class="scope-chip checked" id="chip_sch"><input type="checkbox" id="scope_sch" checked onchange="refreshChips()"> <span><i class="fas fa-award"></i> <strong>Scholarship Monitoring</strong><br><span class="tm" style="font-size:10.5px;">Programs, scholar status &amp; performance</span></span></label>
<label class="scope-chip checked" id="chip_cns"><input type="checkbox" id="scope_cns" checked onchange="refreshChips()"> <span><i class="fas fa-comments"></i> <strong>Guidance Counseling</strong><br><span class="tm" style="font-size:10.5px;">Sessions, concerns &amp; utilization</span></span></label>
</div></div>
<div class="fg" style="margin:0;"><label class="fl">Academic Year</label>
<select id="rp_ay" class="fc" style="width:150px;">
@foreach($ayOptions as $ay)<option value="{{ $ay }}" {{ $ay===$currentAy?'selected':'' }}>{{ $ay }}</option>@endforeach
</select></div>
<div class="fg" style="margin:0;"><label class="fl">Semester</label>
<select id="rp_sem" class="fc" style="width:150px;">
@foreach(['1st Semester','2nd Semester','Summer'] as $sem)<option value="{{ $sem }}" {{ $sem===$currentSem?'selected':'' }}>{{ $sem }}</option>@endforeach
</select></div>
<button type="button" class="btn btn-p" id="btnGenerate" onclick="generateReport()"><i class="fas fa-wand-magic-sparkles"></i> Generate Report</button>
</div>
<div class="tm" style="font-size:11.5px;margin-top:9px;"><i class="fas fa-circle-info"></i> Generation takes roughly 10&ndash;30 seconds. The report is saved automatically so DSA staff can re-open, print or export it later.</div>
</div>
</div>

<div class="card an">
<div class="ch"><i class="fas fa-file-lines" style="color:var(--gm);"></i><h2>Report Preview</h2>
<div class="ch-acts" id="reportActions" style="display:none;gap:7px;">
<button type="button" class="btn btn-o btn-sm" onclick="printReport()"><i class="fas fa-print"></i> Print</button>
<button type="button" class="btn btn-o btn-sm" onclick="copyReport()"><i class="fas fa-copy"></i> Copy Text</button>
<a id="lnkWord" class="btn btn-o btn-sm" href="#"><i class="fas fa-file-word"></i> Word</a>
<a id="lnkPdf" class="btn btn-o btn-sm" href="#"><i class="fas fa-file-pdf"></i> PDF</a>
<button type="button" class="btn btn-p btn-sm" onclick="generateReport()"><i class="fas fa-rotate"></i> Regenerate</button>
</div></div>
<div class="cb" style="padding:14px;">
<div id="reportEmpty" class="report-empty">
<i class="fas fa-file-lines"></i>
<div style="max-width:460px;"><strong>No report generated yet.</strong><br>Select the report scope, academic year and semester above, then click <strong>Generate Report</strong> to have Groq AI write the complete formal report.</div>
</div>
<div id="reportLoading" class="report-loading" style="display:none;">
<i class="fas fa-spinner fa-spin"></i>
<div><strong>Writing your report&hellip;</strong><br>Groq AI is analyzing scholarship and counseling data. This may take up to a minute.</div>
</div>
<div id="reportPreview" class="report-doc" style="display:none;"></div>
</div>
</div>

<div class="card an">
<div class="ch"><i class="fas fa-folder-open" style="color:var(--info);"></i><h2>Generated Reports</h2><span class="badge b-p">{{ $reports->count() }}</span></div>
<div class="tw"><table><thead><tr><th>Report</th><th>Scope</th><th>Period</th><th>Generated</th><th style="width:210px;">Actions</th></tr></thead>
<tbody id="reportsTbody">
@forelse($reports as $rep)
<tr data-report-row="{{ $rep->id }}">
<td class="fws" style="font-size:13px;">{{ $rep->title }}</td>
<td><span class="badge {{ $rep->scope==='both' ? 'b-p' : ($rep->scope==='scholarship' ? 'b-s' : 'b-w') }}" style="font-size:9.5px;text-transform:capitalize;">{{ $rep->scope==='both' ? 'Scholarship + Counseling' : $rep->scope }}</span></td>
<td class="tm" style="font-size:12px;">A.Y. {{ $rep->academic_year }} &middot; {{ $rep->semester }}</td>
<td class="mono tm" style="font-size:11px;">{{ $rep->created_at->format('M d, Y g:i A') }}</td>
<td><div style="display:flex;gap:6px;flex-wrap:wrap;">
<button type="button" class="btn btn-o btn-sm" onclick="viewReport({{ $rep->id }})"><i class="fas fa-eye"></i> View</button>
<a class="btn btn-o btn-sm" href="{{ url('admin/ai-reports/'.$rep->id.'/pdf') }}"><i class="fas fa-file-pdf"></i> PDF</a>
<a class="btn btn-o btn-sm" href="{{ url('admin/ai-reports/'.$rep->id.'/word') }}"><i class="fas fa-file-word"></i> Word</a>
</div></td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:24px;color:var(--tm);"><i class="fas fa-folder-open" style="font-size:30px;color:var(--bd);margin-bottom:10px;display:block;"></i>No reports generated yet.</td></tr>
@endforelse
</tbody></table></div>
</div>
@endsection
@push('scripts')
<script>
var AI_BASE = '{{ url('admin/ai-reports') }}';

function csrfToken(){ return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

function refreshChips(){
    document.getElementById('chip_sch').classList.toggle('checked', document.getElementById('scope_sch').checked);
    document.getElementById('chip_cns').classList.toggle('checked', document.getElementById('scope_cns').checked);
}

function currentScope(){
    var sch = document.getElementById('scope_sch').checked, cns = document.getElementById('scope_cns').checked;
    return sch && cns ? 'both' : (sch ? 'scholarship' : (cns ? 'counseling' : null));
}

function generateReport(){
    var scope = currentScope();
    if (!scope) { window.isamsToast ? isamsToast('Select at least one scope (scholarship or counseling).', 'warning') : alert('Select at least one scope.'); return; }
    var btn = document.getElementById('btnGenerate');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    document.getElementById('reportEmpty').style.display = 'none';
    document.getElementById('reportPreview').style.display = 'none';
    document.getElementById('reportActions').style.display = 'none';
    document.getElementById('reportLoading').style.display = 'flex';
    fetch(AI_BASE + '/generate', {
        method:'POST',
        headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken(),'X-Requested-With':'XMLHttpRequest'},
        body: JSON.stringify({ scope: scope, academic_year: document.getElementById('rp_ay').value, semester: document.getElementById('rp_sem').value })
    }).then(function(r){ return r.json().catch(function(){ return { message:'Unexpected server error. Please try again.' }; }); })
      .then(function(d){
          document.getElementById('reportLoading').style.display = 'none';
          btn.disabled = false; btn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> Generate Report';
          if (d.report) { showReport(d); window.isamsToast ? isamsToast(d.message || 'Report generated.', 'success') : null; }
          else { showEmpty(); window.isamsToast ? isamsToast(d.message || 'Failed to generate report.', 'error') : alert(d.message || 'Failed.'); }
      }).catch(function(){
          document.getElementById('reportLoading').style.display = 'none';
          btn.disabled = false; btn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> Generate Report';
          showEmpty(); window.isamsToast ? isamsToast('Network error while generating the report.', 'error') : null;
      });
}

function showReport(d){
    var pv = document.getElementById('reportPreview');
    pv.innerHTML = d.report;
    pv.style.display = 'block';
    document.getElementById('reportEmpty').style.display = 'none';
    document.getElementById('reportActions').style.display = 'flex';
    document.getElementById('lnkWord').href = AI_BASE + '/' + d.id + '/word';
    document.getElementById('lnkPdf').href  = AI_BASE + '/' + d.id + '/pdf';
    addReportRow(d);
}

function showEmpty(){
    document.getElementById('reportEmpty').style.display = 'flex';
    document.getElementById('reportActions').style.display = 'none';
}

function addReportRow(d){
    var tb = document.getElementById('reportsTbody');
    var emptyRow = tb.querySelector('td[colspan]');
    if (emptyRow) emptyRow.closest('tr').remove();
    if (tb.querySelector('[data-report-row="'+d.id+'"]')) return;
    var scopeLabel = d.scope === 'both' ? 'Scholarship + Counseling' : (d.scope === 'scholarship' ? 'Scholarship' : 'Counseling');
    var tr = document.createElement('tr');
    tr.setAttribute('data-report-row', d.id);
    tr.innerHTML = '<td class="fws" style="font-size:13px;"></td>'
        + '<td><span class="badge b-p" style="font-size:9.5px;text-transform:capitalize;">' + scopeLabel + '</span></td>'
        + '<td class="tm" style="font-size:12px;">A.Y. ' + document.getElementById('rp_ay').value + ' &middot; ' + document.getElementById('rp_sem').value + '</td>'
        + '<td class="mono tm" style="font-size:11px;">' + (d.generated_at || '') + '</td>'
        + '<td><div style="display:flex;gap:6px;flex-wrap:wrap;">'
        + '<button type="button" class="btn btn-o btn-sm" onclick="viewReport('+d.id+')"><i class="fas fa-eye"></i> View</button>'
        + '<a class="btn btn-o btn-sm" href="' + AI_BASE + '/' + d.id + '/pdf"><i class="fas fa-file-pdf"></i> PDF</a>'
        + '<a class="btn btn-o btn-sm" href="' + AI_BASE + '/' + d.id + '/word"><i class="fas fa-file-word"></i> Word</a>'
        + '</div></td>';
    tr.querySelector('td').textContent = d.title;
    tb.insertBefore(tr, tb.firstChild);
}

function viewReport(id){
    var pv = document.getElementById('reportPreview');
    pv.style.display = 'none';
    document.getElementById('reportEmpty').style.display = 'none';
    document.getElementById('reportActions').style.display = 'none';
    document.getElementById('reportLoading').style.display = 'flex';
    fetch(AI_BASE + '/' + id + '/view', { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
        .then(function(r){ return r.json(); })
        .then(function(d){
            document.getElementById('reportLoading').style.display = 'none';
            if (d.report) showReport(d);
            else showEmpty();
        }).catch(function(){ document.getElementById('reportLoading').style.display = 'none'; showEmpty(); });
}

function printReport(){
    var html = document.getElementById('reportPreview').innerHTML;
    if (!html.trim()) return;
    var win = window.open('', '_blank');
    win.document.write('<!DOCTYPE html><html><head><title>AI Comprehensive Report</title><style>'
        + 'body{font-family:Georgia,"Times New Roman",serif;font-size:12.5px;line-height:1.65;color:#1c1c1c;max-width:740px;margin:0 auto;padding:36px 30px;}'
        + 'h1,h2{color:#12355f;}h2{border-bottom:1.5px solid #12355f;padding-bottom:5px;margin:22px 0 10px;font-size:16px;}'
        + 'h3{color:#12355f;margin:16px 0 8px;}p{text-align:justify;margin:0 0 11px;}'
        + 'table{width:100%;border-collapse:collapse;margin:10px 0 16px;font-size:11.5px;}'
        + 'th{background:#e8eef7;border:1px solid #9db0cb;padding:6px 8px;text-align:left;text-transform:uppercase;font-size:10.5px;color:#12355f;}'
        + 'td{border:1px solid #c3cfdf;padding:6px 8px;}ul,ol{margin:0 0 12px 22px;padding:0;}li{margin-bottom:5px;}'
        + '@page{margin:14mm;}'
        + '</style></head><body>' + html + '</body></html>');
    win.document.close();
    setTimeout(function(){ win.focus(); win.print(); }, 400);
}

function copyReport(){
    var text = document.getElementById('reportPreview').innerText;
    if (!text.trim()) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function(){ window.isamsToast ? isamsToast('Report text copied to clipboard.', 'success') : null; })
            .catch(function(){ fallbackCopy(text); });
    } else { fallbackCopy(text); }
}
function fallbackCopy(text){
    var ta = document.createElement('textarea');
    ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
    document.body.appendChild(ta); ta.select();
    try { document.execCommand('copy'); window.isamsToast ? isamsToast('Report text copied to clipboard.', 'success') : null; }
    catch (e) { window.isamsToast ? isamsToast('Could not copy automatically.', 'error') : null; }
    document.body.removeChild(ta);
}
</script>
@endpush
