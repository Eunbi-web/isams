@extends('admin.layouts.app')
@section('title','Edit Scholarship')
@section('page-title','Edit Scholarship Program')
@section('page-sub','Update scholarship details, requirements, and AI criteria')
@section('content')
<div style="max-width:760px;">
<div style="margin-bottom:14px;"><a href="{{ route('admin.scholarships.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
@php $criteria = is_array($scholarship->ai_criteria) ? $scholarship->ai_criteria : json_decode($scholarship->ai_criteria ?? '{}', true); @endphp
<form method="POST" action="{{ route('admin.scholarships.update', $scholarship->id) }}">@csrf @method('PUT')

{{-- Basic Info --}}
<div class="card an mb3">
    <div class="ch"><i class="fas fa-award" style="color:var(--yd);"></i><h2>Scholarship Details</h2></div>
    <div class="cb">
        <div class="fg"><label class="fl">Scholarship Name <span style="color:var(--danger);">*</span></label>
        <input type="text" name="name" class="fc" value="{{ old('name', $scholarship->name) }}" required></div>
        <div class="g2">
            <div class="fg"><label class="fl">Type <span style="color:var(--danger);">*</span></label>
            <select name="type" class="fc" required>
            @foreach(['Government','Institutional','Private'] as $t)
            <option value="{{ $t }}" {{ old('type',$scholarship->type)===$t?'selected':'' }}>{{ $t }}</option>
            @endforeach</select></div>
            <div class="fg"><label class="fl">Status</label>
            <select name="status" class="fc">
            <option value="Active" {{ $scholarship->status==='Active'?'selected':'' }}>Active</option>
            <option value="Closed" {{ $scholarship->status==='Closed'?'selected':'' }}>Closed</option></select></div>
        </div>
        <div class="g2">
            <div class="fg"><label class="fl">Source / Organization</label>
            <input type="text" name="source" class="fc" value="{{ old('source', $scholarship->source) }}"></div>
            <div class="fg"><label class="fl">Total Slots</label>
            <input type="number" name="slots" class="fc mono" value="{{ old('slots', $scholarship->slots) }}" min="0"></div>
        </div>
        <div class="g2">
            <div class="fg"><label class="fl">Amount (₱)</label>
            <input type="number" name="amount" class="fc mono" step="0.01" value="{{ old('amount', $scholarship->amount) }}"></div>
            <div class="fg"><label class="fl">Deadline</label>
            <input type="date" name="end_date" class="fc" value="{{ old('end_date', $scholarship->end_date?->format('Y-m-d')) }}"></div>
        </div>
        <div class="fg"><label class="fl">Benefit Package <span style="color:var(--danger);">*</span></label>
        <input type="text" name="benefits" class="fc" value="{{ old('benefits', $scholarship->benefits) }}" required></div>
        <div class="fg"><label class="fl">Description</label>
        <textarea name="description" class="fc" rows="3">{{ old('description', $scholarship->description) }}</textarea></div>
    </div>
</div>

{{-- Full Requirements --}}
<div class="card an mb3">
    <div class="ch"><i class="fas fa-list-ul" style="color:var(--gm);"></i><h2>Requirements & Eligibility Details</h2></div>
    <div class="cb">
        <div class="fg"><label class="fl">Full Requirements Text</label>
        <textarea name="requirements" class="fc" rows="6" placeholder="• Filipino citizen&#10;• GWA of 1.75 or better&#10;• Regular enrollment&#10;• No failing grades&#10;• Annual income below ₱400,000&#10;• No disciplinary case">{{ old('requirements', $scholarship->requirements) }}</textarea>
        <div style="font-size:11px;color:var(--tm);margin-top:4px;"><i class="fas fa-info-circle" style="margin-right:3px;"></i>This is displayed to students when they expand the scholarship card.</div></div>
    </div>
</div>

{{-- AI Criteria --}}
<div class="card an mb3">
    <div class="ch" style="background:linear-gradient(135deg,#0d3318,#1a5c28);">
        <i class="fas fa-robot" style="color:var(--y);font-size:17px;"></i>
        <div><h2 style="color:#fff;">AI Eligibility Criteria</h2>
        <div style="font-size:11px;color:rgba(255,255,255,.6);">Used by the AI engine to evaluate students</div></div>
    </div>
    <div class="cb">
        <div class="g2">
            <div class="fg"><label class="fl">Maximum GWA (gwa_max)</label>
            <input type="number" name="ai_gwa_max" class="fc mono" step="0.01" min="1" max="5" value="{{ old('ai_gwa_max', $criteria['gwa_max'] ?? 1.75) }}">
            <div style="font-size:11px;color:var(--tm);margin-top:3px;">Current: <strong style="color:var(--g);">{{ $criteria['gwa_max'] ?? '1.75' }}</strong> or better required</div></div>
            <div class="fg"><label class="fl">Maximum Family Income (₱/year)</label>
            <input type="number" name="ai_income_max" class="fc mono" value="{{ old('ai_income_max', $criteria['income_max'] ?? 400000) }}">
            <div style="font-size:11px;color:var(--tm);margin-top:3px;">Current: <strong style="color:var(--g);">₱{{ number_format($criteria['income_max'] ?? 400000) }}</strong>/year</div></div>
        </div>
        <div class="g2">
            <label style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;background:var(--bg);">
                <input type="checkbox" name="ai_no_failing" value="1" {{ (!empty($criteria['no_failing']))?'checked':'' }} style="margin-top:2px;accent-color:var(--g);width:15px;height:15px;flex-shrink:0;">
                <div><div style="font-size:13px;font-weight:600;color:var(--tx);">Require No Failing Grades</div>
                <div style="font-size:11px;color:var(--tm);margin-top:2px;">Students with failing grades score lower</div></div>
            </label>
            <label style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;background:var(--bg);">
                <input type="checkbox" name="ai_no_discipline" value="1" {{ (!empty($criteria['no_discipline']))?'checked':'' }} style="margin-top:2px;accent-color:var(--g);width:15px;height:15px;flex-shrink:0;">
                <div><div style="font-size:13px;font-weight:600;color:var(--tx);">Require No Disciplinary Case</div>
                <div style="font-size:11px;color:var(--tm);margin-top:2px;">Students with active cases score lower</div></div>
            </label>
        </div>
    </div>
</div>

<div style="display:flex;gap:10px;">
    <button type="submit" class="btn btn-p"><i class="fas fa-save"></i> Update Scholarship</button>
    <a href="{{ route('admin.scholarships.index') }}" class="btn btn-o">Cancel</a>
    <form method="POST" action="{{ route('admin.scholarships.destroy', $scholarship->id) }}" style="margin-left:auto;">@csrf @method('DELETE')
        <button class="btn btn-d btn-sm" onclick="return confirm('Delete this scholarship? This cannot be undone.')"><i class="fas fa-trash"></i> Delete</button>
    </form>
</div>
</form>
</div>
@endsection
