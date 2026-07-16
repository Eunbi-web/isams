<?php $__env->startSection('title','New Announcement'); ?>
<?php $__env->startSection('page-title','New Announcement'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:680px;">
<form method="POST" action="<?php echo e(route('admin.announcements.store')); ?>"><?php echo csrf_field(); ?>
<div class="card an mb3"><div class="ch"><i class="fas fa-bullhorn" style="color:var(--gm);"></i><h2>Announcement Details</h2></div><div class="cb">
<div class="fg"><label class="fl">Title <span style="color:var(--danger);">*</span></label><input type="text" name="title" class="fc" required placeholder="e.g. CHED Applications Now Open"></div>
<div class="fg"><label class="fl">Message <span style="color:var(--danger);">*</span></label><textarea name="body" class="fc" rows="5" required placeholder="Write the full announcement here..."></textarea></div>
<div class="g2">
<div class="fg"><label class="fl">Type</label><select name="type" class="fc"><option value="Scholarship">Scholarship</option><option value="Renewal">Renewal</option><option value="General">General</option><option value="System">System</option></select></div>
<div class="fg"><label class="fl">Priority</label><select name="priority" class="fc"><option value="Normal">Normal</option><option value="High">High</option><option value="Urgent">Urgent</option></select></div>
</div>
</div></div>
<div style="display:flex;gap:10px;"><button type="submit" class="btn btn-p"><i class="fas fa-paper-plane"></i> Publish</button><a href="<?php echo e(route('admin.announcements.index')); ?>" class="btn btn-o">Cancel</a></div>
</form></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/announcements/create.blade.php ENDPATH**/ ?>