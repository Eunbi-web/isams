<?php $__env->startSection('title','Application Details'); ?>
<?php $__env->startSection('page-title','Application Details'); ?>
<?php $__env->startSection('page-sub','<?php echo e($application->scholarship->name ?? "Scholarship Application"); ?>'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:700px;">
<div style="margin-bottom:14px;"><a href="<?php echo e(route('student.applications')); ?>" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
<?php $sc=$application->ai_score??0; $el=$application->ai_eligibility==='Eligible'?'eligible':($application->ai_eligibility==='For Review'?'review':'not'); ?>
<div class="elig-banner <?php echo e($el); ?> an">
<div class="elig-icon" style="font-size:24px;color:#fff;"><i class="fas fa-<?php echo e($el==='eligible'?'check-circle':($el==='review'?'exclamation-circle':'times-circle')); ?>"></i></div>
<div style="flex:1;"><div class="elig-title"><?php echo e($application->ai_eligibility??'Pending'); ?></div><div class="elig-sub"><?php echo e($application->scholarship->name); ?></div><div style="margin-top:8px;"><span class="tag tag-<?php echo e($application->ai_tag==='Renewal Ready'?'renewal':($application->ai_tag==='Needs Requirements'?'needs':'disq')); ?>"><?php echo e($application->ai_tag??'—'); ?></span></div></div>
<div class="chance-circle"><div><div class="chance-val"><?php echo e($sc); ?>%</div><div class="chance-lbl">AI Score</div></div></div>
</div>
<div class="card an mt3 mb3"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Application Details</h2><div class="ch-acts"><span class="badge <?php echo e($application->status==='Approved'?'b-s':($application->status==='Rejected'?'b-d':'b-w')); ?>"><?php echo e($application->status); ?></span></div></div>
<div class="cb">
<div class="g2 mb3">
<?php $__currentLoopData = [['GWA',number_format($application->gwa,2),'fas fa-star'],['Enrollment',$application->enrollment_type,'fas fa-id-badge'],['Has Failing',$application->has_failing?'Yes':'No','fas fa-exclamation'],['Has Discipline',$application->has_discipline?'Yes':'No','fas fa-gavel'],['Income Bracket',$application->income_bracket,'fas fa-hand-holding-usd'],['Filed',\Carbon\Carbon::parse($application->created_at)->format('M d, Y'),'fas fa-calendar']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:11px 13px;"><div style="font-size:11px;color:var(--tm);font-weight:600;margin-bottom:3px;"><i class="<?php echo e($d[2]); ?>" style="margin-right:4px;"></i><?php echo e($d[0]); ?></div><div class="fws" style="font-size:14px;"><?php echo e($d[1]); ?></div></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php if($application->ai_reasoning): ?><div class="alert al-ai" style="margin-bottom:0;font-size:12px;"><i class="fas fa-robot"></i><span><strong>AI Reasoning:</strong> <?php echo e($application->ai_reasoning); ?></span></div><?php endif; ?>
</div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/student/applications/show.blade.php ENDPATH**/ ?>