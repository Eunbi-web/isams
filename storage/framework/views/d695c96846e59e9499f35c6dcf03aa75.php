<?php $__env->startSection('title','Add Scholarship'); ?>
<?php $__env->startSection('page-title','Add Scholarship Program'); ?>
<?php $__env->startSection('page-sub','Create a new scholarship with full details and AI criteria'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:760px;">
<form method="POST" action="<?php echo e(route('admin.scholarships.store')); ?>"><?php echo csrf_field(); ?>


<div class="card an mb3">
    <div class="ch"><i class="fas fa-award" style="color:var(--yd);"></i><h2>Scholarship Details</h2></div>
    <div class="cb">
        <div class="fg"><label class="fl">Scholarship Name <span style="color:var(--danger);">*</span></label>
        <input type="text" name="name" class="fc" value="<?php echo e(old('name')); ?>" required placeholder="e.g. CHED Merit Scholarship Program"></div>
        <div class="g2">
            <div class="fg"><label class="fl">Type <span style="color:var(--danger);">*</span></label>
            <select name="type" class="fc" required><option value="">Select type</option>
            <option value="Government" <?php echo e(old('type')==='Government'?'selected':''); ?>>Government</option>
            <option value="Institutional" <?php echo e(old('type')==='Institutional'?'selected':''); ?>>Institutional</option>
            <option value="Private" <?php echo e(old('type')==='Private'?'selected':''); ?>>Private</option></select></div>
            <div class="fg"><label class="fl">Status</label>
            <select name="status" class="fc">
            <option value="Active">Active</option>
            <option value="Closed">Closed</option></select></div>
        </div>
        <div class="g2">
            <div class="fg"><label class="fl">Source / Organization</label>
            <input type="text" name="source" class="fc" value="<?php echo e(old('source')); ?>" placeholder="Commission on Higher Education"></div>
            <div class="fg"><label class="fl">Total Slots</label>
            <input type="number" name="slots" class="fc mono" value="<?php echo e(old('slots')); ?>" placeholder="250" min="0"></div>
        </div>
        <div class="g2">
            <div class="fg"><label class="fl">Amount (₱)</label>
            <input type="number" name="amount" class="fc mono" step="0.01" value="<?php echo e(old('amount')); ?>" placeholder="40000.00"></div>
            <div class="fg"><label class="fl">Deadline</label>
            <input type="date" name="end_date" class="fc" value="<?php echo e(old('end_date')); ?>"></div>
        </div>
        <div class="fg"><label class="fl">Benefit Package <span style="color:var(--danger);">*</span></label>
        <input type="text" name="benefits" class="fc" value="<?php echo e(old('benefits')); ?>" required placeholder="Full Tuition + ₱2,000/month stipend + book allowance"></div>
        <div class="fg"><label class="fl">Description</label>
        <textarea name="description" class="fc" rows="3" placeholder="Brief description of the scholarship program..."><?php echo e(old('description')); ?></textarea></div>
    </div>
</div>


<div class="card an mb3">
    <div class="ch"><i class="fas fa-list-ul" style="color:var(--gm);"></i><h2>Requirements & Eligibility Details</h2>
    <span style="font-size:12px;color:var(--tm);margin-left:6px;">Shown to students when they expand the card</span></div>
    <div class="cb">
        <div class="fg"><label class="fl">Full Requirements Text</label>
        <textarea name="requirements" class="fc" rows="5" placeholder="List all requirements here. Example:&#10;• Filipino citizen currently enrolled in a Philippine college or university&#10;• GWA of 1.75 or better&#10;• Regular enrollment status&#10;• No failing grades in the previous semester&#10;• Annual family income not exceeding ₱400,000&#10;• No pending disciplinary case"><?php echo e(old('requirements')); ?></textarea>
        <div style="font-size:11px;color:var(--tm);margin-top:4px;"><i class="fas fa-info-circle" style="margin-right:3px;"></i>This text appears in the requirements section when a student expands the scholarship card.</div></div>
    </div>
</div>


<div class="card an mb3">
    <div class="ch" style="background:linear-gradient(135deg,#0d3318,#1a5c28);">
        <i class="fas fa-robot" style="color:var(--y);font-size:17px;"></i>
        <div><h2 style="color:#fff;">AI Eligibility Criteria</h2>
        <div style="font-size:11px;color:rgba(255,255,255,.6);">These values are used by the AI engine to score applicants 0–100</div></div>
    </div>
    <div class="cb">
        <div class="alert al-ai" style="font-size:12px;margin-bottom:16px;"><i class="fas fa-info-circle"></i>
        <span>The AI engine uses these criteria to evaluate each student application automatically. Set them to match the official scholarship requirements.</span></div>
        <div class="g2">
            <div class="fg"><label class="fl">Maximum GWA (gwa_max)</label>
            <input type="number" name="ai_gwa_max" class="fc mono" step="0.01" min="1" max="5" value="<?php echo e(old('ai_gwa_max', 1.75)); ?>" placeholder="1.75">
            <div style="font-size:11px;color:var(--tm);margin-top:3px;">Students with GWA equal to or better than this qualify. Example: 1.75 means GWA must be 1.75 or lower.</div></div>
            <div class="fg"><label class="fl">Maximum Family Income (₱/year)</label>
            <input type="number" name="ai_income_max" class="fc mono" value="<?php echo e(old('ai_income_max', 400000)); ?>" placeholder="400000">
            <div style="font-size:11px;color:var(--tm);margin-top:3px;">Annual family income limit. Students below this threshold score higher.</div></div>
        </div>
        <div class="g2">
            <label style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;background:var(--bg);">
                <input type="checkbox" name="ai_no_failing" value="1" <?php echo e(old('ai_no_failing',1)?'checked':''); ?> style="margin-top:2px;accent-color:var(--g);width:15px;height:15px;flex-shrink:0;">
                <div><div style="font-size:13px;font-weight:600;color:var(--tx);">Require No Failing Grades</div>
                <div style="font-size:11px;color:var(--tm);margin-top:2px;">Students with failing grades this/last semester will score lower</div></div>
            </label>
            <label style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;background:var(--bg);">
                <input type="checkbox" name="ai_no_discipline" value="1" <?php echo e(old('ai_no_discipline')?'checked':''); ?> style="margin-top:2px;accent-color:var(--g);width:15px;height:15px;flex-shrink:0;">
                <div><div style="font-size:13px;font-weight:600;color:var(--tx);">Require No Disciplinary Case</div>
                <div style="font-size:11px;color:var(--tm);margin-top:2px;">Students with active discipline cases will score lower</div></div>
            </label>
        </div>
        
        <div style="background:#f3f8f4;border:1px solid var(--bd);border-radius:var(--rs);padding:13px;margin-top:4px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tm);margin-bottom:8px;">AI Score Breakdown Preview</div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                <?php $__currentLoopData = [['GWA','40 pts max','fas fa-star','y'],['Enrollment Type','20 pts max','fas fa-id-badge','g'],['No Failing Grades','20 pts max','fas fa-graduation-cap','g'],['Family Income','15 pts max','fas fa-hand-holding-usd','t'],['No Discipline Case','10 pts max','fas fa-gavel','o'],['GWA Excellence Bonus','+5 pts','fas fa-award','y']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:center;gap:6px;background:#fff;border:1px solid var(--bd);border-radius:20px;padding:5px 10px;font-size:11px;">
                    <i class="<?php echo e($s[2]); ?>" style="color:<?php echo e($s[3]==='y'?'var(--yd)':($s[3]==='g'?'var(--gm)':($s[3]==='t'?'var(--info)':'var(--warn)'))); ?>;font-size:11px;"></i>
                    <span style="color:var(--tm);"><?php echo e($s[0]); ?></span>
                    <strong style="color:var(--g);"><?php echo e($s[1]); ?></strong>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>

<div style="display:flex;gap:10px;">
    <button type="submit" class="btn btn-p"><i class="fas fa-save"></i> Save Scholarship</button>
    <a href="<?php echo e(route('admin.scholarships.index')); ?>" class="btn btn-o">Cancel</a>
</div>
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/scholarships/create.blade.php ENDPATH**/ ?>