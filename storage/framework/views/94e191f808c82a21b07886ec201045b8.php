<?php $__env->startSection('title','Announcements'); ?>
<?php $__env->startSection('page-title','Announcements'); ?>
<?php $__env->startSection('content'); ?>
<div style="display:flex;justify-content:flex-end;margin-bottom:14px;"><a href="<?php echo e(route('admin.announcements.create')); ?>" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> New Announcement</a></div>
<div class="card an"><div class="ch"><i class="fas fa-bullhorn" style="color:var(--gm);"></i><h2>All Announcements</h2></div>
<div class="tw"><table><thead><tr><th>Title</th><th>Type</th><th>Priority</th><th>Date</th><th>Actions</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr>
<td class="fws" style="font-size:13px;"><?php echo e(Str::limit($ann->title,50)); ?></td>
<td><span class="badge b-p" style="font-size:10px;"><?php echo e($ann->type); ?></span></td>
<td><span class="badge <?php echo e($ann->priority==='Urgent'?'b-d':($ann->priority==='High'?'b-w':'b-gray')); ?>"><?php echo e($ann->priority); ?></span></td>
<td class="mono tm" style="font-size:11px;"><?php echo e($ann->created_at->format('M d, Y')); ?></td>
<td><div style="display:flex;gap:5px;">
<a href="<?php echo e(route('admin.announcements.edit',$ann->id)); ?>" class="btn btn-o btn-sm btn-ic"><i class="fas fa-edit"></i></a>
<form method="POST" action="<?php echo e(route('admin.announcements.destroy',$ann->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-d btn-sm btn-ic" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button></form>
</div></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="5" style="text-align:center;padding:16px;color:var(--tm);">No announcements.</td></tr>
<?php endif; ?>
</tbody></table></div>
<?php if($announcements->hasPages()): ?><div style="padding:13px 18px;border-top:1px solid var(--bd);"><?php echo e($announcements->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/announcements/index.blade.php ENDPATH**/ ?>