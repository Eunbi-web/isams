<?php $__env->startSection('title','Student'); ?>
<?php $__env->startSection('page-title','Student Profile'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:760px;">
<div style="margin-bottom:14px;display:flex;gap:8px;"><a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a><a href="<?php echo e(route('admin.students.edit',$student->id)); ?>" class="btn btn-p btn-sm"><i class="fas fa-edit"></i> Edit</a></div>
<div class="g2">
<div class="card an"><div class="ch"><div class="av av-m"><?php echo e(strtoupper(substr($student->first_name,0,1))); ?></div><div><h2><?php echo e($student->full_name); ?></h2><div class="tm" style="font-size:12px;"><?php echo e($student->student_id); ?></div></div><div class="ch-acts"><span class="badge <?php echo e($student->status==='Active'?'b-s':'b-d'); ?>"><?php echo e($student->status); ?></span></div></div>
<div class="cb"><?php $__currentLoopData = [['Course',$student->course],['Year Level',$student->year_level],['GWA',number_format($student->gwa??0,2)],['Enrollment',$student->enrollment_type],['Email',$student->email??'—'],['Contact',$student->contact_number??'—']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--bd);font-size:13px;"><span class="tm"><?php echo e($d[0]); ?></span><span class="fws"><?php echo e($d[1]); ?></span></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div>
<div class="card an"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Applications (<?php echo e($student->applications->count()); ?>)</h2></div>
<div class="cb" style="padding:0;">
<?php $__empty_1 = true; $__currentLoopData = $student->applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div style="display:flex;align-items:center;gap:10px;padding:12px 18px;border-bottom:1px solid var(--bd);">
<div style="flex:1;"><div class="fws" style="font-size:13px;"><?php echo e($app->scholarship->name??'—'); ?></div><div class="mono tm" style="font-size:11px;">AI: <?php echo e($app->ai_score??0); ?>% · <?php echo e($app->ai_eligibility??'—'); ?></div></div>
<span class="badge <?php echo e($app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w')); ?>"><?php echo e($app->status); ?></span>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div style="padding:18px;text-align:center;color:var(--tm);font-size:13px;">No applications.</div>
<?php endif; ?>
</div></div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/students/show.blade.php ENDPATH**/ ?>