<?php $__env->startSection('title','Edit Student'); ?>
<?php $__env->startSection('page-title','Edit Student'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:700px;">
<div style="margin-bottom:14px;"><a href="<?php echo e(route('admin.students.show',$student->id)); ?>" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
<form method="POST" action="<?php echo e(route('admin.students.update',$student->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<div class="card an mb3"><div class="ch"><i class="fas fa-user-graduate" style="color:var(--gm);"></i><h2>Edit: <?php echo e($student->full_name); ?></h2></div><div class="cb">
<div class="g2">
<div class="fg"><label class="fl">First Name</label><input type="text" name="first_name" class="fc" value="<?php echo e($student->first_name); ?>" required></div>
<div class="fg"><label class="fl">Last Name</label><input type="text" name="last_name" class="fc" value="<?php echo e($student->last_name); ?>" required></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">GWA</label><input type="number" name="gwa" class="fc mono" step="0.01" min="1" max="5" value="<?php echo e($student->gwa); ?>"></div>
<div class="fg"><label class="fl">Enrollment Type</label><select name="enrollment_type" class="fc"><option value="Regular" <?php echo e($student->enrollment_type==='Regular'?'selected':''); ?>>Regular</option><option value="Irregular" <?php echo e($student->enrollment_type==='Irregular'?'selected':''); ?>>Irregular</option></select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Course</label><select name="course" class="fc"><?php $__currentLoopData = ['BS Computer Science','BS Information Technology','BS Engineering','BS Education','BS Nursing','BS Business Administration','AB Communication','BS Psychology','BS Accountancy','BS Tourism']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c); ?>" <?php echo e($student->course===$c?'selected':''); ?>><?php echo e($c); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="fg"><label class="fl">Year Level</label><select name="year_level" class="fc"><?php $__currentLoopData = ['1st Year','2nd Year','3rd Year','4th Year']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($y); ?>" <?php echo e($student->year_level===$y?'selected':''); ?>><?php echo e($y); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Status</label><select name="status" class="fc"><option value="Active" <?php echo e($student->status==='Active'?'selected':''); ?>>Active</option><option value="Inactive" <?php echo e($student->status==='Inactive'?'selected':''); ?>>Inactive</option></select></div>
<div class="fg"><label class="fl">Contact</label><input type="text" name="contact_number" class="fc" value="<?php echo e($student->contact_number); ?>"></div>
</div>
</div></div>
<div style="display:flex;gap:10px;"><button type="submit" class="btn btn-p"><i class="fas fa-save"></i> Update</button><a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-o">Cancel</a></div>
</form></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/students/edit.blade.php ENDPATH**/ ?>