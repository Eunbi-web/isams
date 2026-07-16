<?php $__env->startSection('title','Announcement'); ?>
<?php $__env->startSection('page-title','Announcement'); ?>
<?php $__env->startSection('page-sub','<?php echo e($announcement->title); ?>'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:720px;">
<div style="margin-bottom:14px;"><a href="<?php echo e(route('student.announcements')); ?>" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
<div class="card an"><div class="ch"><i class="fas fa-bullhorn" style="color:var(--gm);"></i><h2><?php echo e($announcement->title); ?></h2><span class="badge <?php echo e($announcement->priority==='Urgent'?'b-d':($announcement->priority==='High'?'b-w':'b-gray')); ?>" style="margin-left:6px;"><?php echo e($announcement->priority); ?></span></div>
<div class="cb"><div style="font-size:14px;line-height:1.8;color:var(--tx);"><?php echo e($announcement->body); ?></div>
<div style="margin-top:18px;padding-top:14px;border-top:1px solid var(--bd);font-size:12px;color:var(--tm);"><i class="fas fa-clock" style="margin-right:4px;"></i>Posted <?php echo e($announcement->created_at->diffForHumans()); ?></div>
</div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/student/announcements/show.blade.php ENDPATH**/ ?>