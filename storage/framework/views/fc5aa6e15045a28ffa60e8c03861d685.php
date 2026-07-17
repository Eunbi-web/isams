<?php $__env->startSection('title','My Applications'); ?>
<?php $__env->startSection('page-title','My Applications'); ?>
<?php $__env->startSection('page-sub','Track the status of all your scholarship applications'); ?>
<?php $__env->startSection('content'); ?>
<div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
<a href="<?php echo e(route('student.scholarships')); ?>" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> Apply to Scholarship</a>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>All Applications</h2><span class="badge b-p" style="margin-left:6px;"><?php echo e($applications->count()); ?></span></div>
<div class="tw">
<table><thead><tr><th>Scholarship</th><th>GWA</th><th>AI Score</th><th>Eligibility</th><th>Status</th><th>Date Filed</th><th>Action</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<?php $sc=$app->ai_score??0; $el=$app->ai_eligibility==='Eligible'?'el':($app->ai_eligibility==='For Review'?'rv':'no'); ?>
<tr>
<td><div class="fws" style="font-size:13px;"><?php echo e($app->scholarship->name??'—'); ?></div><div class="tm mono" style="font-size:11px;"><?php echo e($app->scholarship->type??''); ?></div></td>
<td class="mono fwb" style="color:<?php echo e((float)$app->gwa<=1.75?'var(--gm)':((float)$app->gwa<=2.25?'var(--warn)':'var(--danger)')); ?>"><?php echo e(number_format($app->gwa,2)); ?></td>
<td style="min-width:100px;"><div style="display:flex;align-items:center;gap:5px;"><div style="flex:1;"><div class="asb"><div class="asf <?php echo e($sc>=75?'ash':($sc>=50?'asm':'asl')); ?>" style="width:<?php echo e($sc); ?>%;"></div></div></div><span class="mono" style="font-size:11px;font-weight:700;"><?php echo e($sc); ?>%</span></div></td>
<td><span class="badge elig-<?php echo e($el); ?>" style="font-size:10px;"><?php echo e($app->ai_eligibility??'Pending'); ?></span></td>
<td><span class="badge <?php echo e($app->status==='Approved' || $app->status==='Scholarship Granted'?'b-s':($app->status==='Rejected'?'b-d':'b-w')); ?>"><?php echo e($app->status); ?></span></td>
<td class="mono" style="font-size:11px;color:var(--tm);"><?php echo e($app->created_at->format('M d, Y')); ?></td>
<td><a href="<?php echo e(route('student.applications.show',$app->id)); ?>" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--tm);">No applications yet. <a href="<?php echo e(route('student.scholarships')); ?>" style="color:var(--gm);">Browse scholarships</a></td></tr>
<?php endif; ?>
</tbody></table>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/student/applications/index.blade.php ENDPATH**/ ?>