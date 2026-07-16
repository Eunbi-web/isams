<?php $__env->startSection('title','Scholarships'); ?>
<?php $__env->startSection('page-title','Scholarship Programs'); ?>
<?php $__env->startSection('page-sub','Manage all scholarship programs'); ?>
<?php $__env->startSection('content'); ?>
<div style="display:flex;justify-content:flex-end;margin-bottom:16px;"><a href="<?php echo e(route('admin.scholarships.create')); ?>" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> Add Program</a></div>
<div class="card an">
<div class="ch"><i class="fas fa-award" style="color:var(--yd);"></i><h2>All Programs</h2><span class="badge b-p" style="margin-left:6px;"><?php echo e($scholarships->total()); ?></span></div>
<div class="tw"><table>
<thead><tr><th>Name</th><th>Type</th><th>Benefit</th><th>Slots</th><th>Applications</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $scholarships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr>
<td><div class="fws" style="font-size:13px;"><?php echo e($sch->name); ?></div></td>
<td><span class="badge <?php echo e($sch->type==='Government'?'b-p':($sch->type==='Private'?'b-s':'b-i')); ?>"><?php echo e($sch->type); ?></span></td>
<td style="font-size:12px;color:var(--tm);"><?php echo e(Str::limit($sch->benefits??'—',40)); ?></td>
<td class="mono fwb" style="color:var(--g);"><?php echo e($sch->slots??'—'); ?></td>
<td class="mono" style="color:var(--tm);"><?php echo e($sch->applications_count??0); ?></td>
<td><span class="badge <?php echo e($sch->status==='Active'?'b-s':'b-d'); ?>"><?php echo e($sch->status); ?></span></td>
<td><div style="display:flex;gap:5px;">
<a href="<?php echo e(route('admin.scholarships.show',$sch->id)); ?>" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a>
<a href="<?php echo e(route('admin.scholarships.edit',$sch->id)); ?>" class="btn btn-o btn-sm btn-ic"><i class="fas fa-edit"></i></a>
<form method="POST" action="<?php echo e(route('admin.scholarships.destroy',$sch->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-d btn-sm btn-ic" onclick="return confirm('Delete this scholarship?')"><i class="fas fa-trash"></i></button></form>
</div></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--tm);">No scholarships yet. <a href="<?php echo e(route('admin.scholarships.create')); ?>" style="color:var(--gm);">Add one</a></td></tr>
<?php endif; ?>
</tbody></table></div>
<?php if($scholarships->hasPages()): ?><div style="padding:13px 18px;border-top:1px solid var(--bd);"><?php echo e($scholarships->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/scholarships/index.blade.php ENDPATH**/ ?>