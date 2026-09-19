<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('page-title','Counselor Dashboard'); ?>
<?php $__env->startSection('page-sub','Guidance Counseling Staff Portal'); ?>
<?php $__env->startSection('content'); ?>
<div class="sg">
<div class="sc an d1"><div class="si t"><i class="fas fa-clock"></i></div><div class="sv"><div class="lbl">Counseling In Queue</div><div class="val"><?php echo e($inQueue); ?></div><div class="chg">Awaiting schedule</div></div></div>
<div class="sc an d2"><div class="si g"><i class="fas fa-calendar-check"></i></div><div class="sv"><div class="lbl">Scheduled Sessions</div><div class="val"><?php echo e($scheduled); ?></div><div class="chg">Upcoming</div></div></div>
<div class="sc an d3"><div class="si dg"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Completed Sessions</div><div class="val"><?php echo e($completed); ?></div><div class="chg">Finished</div></div></div>
<div class="sc an d4"><div class="si y"><i class="fas fa-bullhorn"></i></div><div class="sv"><div class="lbl">Total Announcements</div><div class="val"><?php echo e($totalAnnouncements); ?></div><div class="chg">Published</div></div></div>
</div>

<div class="card an">
<div class="ch"><i class="fas fa-comments" style="color:var(--gm);"></i><h2>Recent Counseling Requests</h2><div class="ch-acts"><a href="<?php echo e(route('counselor.counseling.index')); ?>" class="btn btn-o btn-sm">View All</a></div></div>
<div class="tw"><table>
<thead><tr><th>Student Name</th><th>Concern Type</th><th>Priority</th><th>Preferred Date</th><th>Status</th><th>Action</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr <?php echo e($session->status==='In Queue'?'style="background:rgba(26,107,47,0.04);"':''); ?>>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s"><?php echo e(strtoupper(substr($session->student->first_name??'S',0,1))); ?></div><div class="fws" style="font-size:13px;"><?php echo e($session->student->full_name??'—'); ?></div></div></td>
<td style="font-size:13px;"><?php echo e($session->concern_type); ?></td>
<td><span class="badge <?php echo e($session->priority==='Emergency'?'b-d':($session->priority==='Urgent'?'b-w':'b-gray')); ?>"><?php echo e($session->priority); ?></span></td>
<td class="mono tm" style="font-size:11px;"><?php echo e($session->preferred_date?->format('M d Y')??'—'); ?></td>
<td><span class="badge <?php echo e($session->status==='Completed'?'b-s':($session->status==='Scheduled'?'b-p':'b-i')); ?>"><?php echo e($session->status); ?></span></td>
<td><a href="<?php echo e(route('counselor.counseling.index')); ?>" class="btn btn-o btn-sm"><i class="fas fa-eye"></i> View</a></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="6" style="text-align:center;padding:28px;color:var(--tm);"><i class="fas fa-comments" style="font-size:30px;color:var(--bd);margin-bottom:10px;display:block;"></i>No counseling requests yet</td></tr>
<?php endif; ?>
</tbody></table></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('counselor.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/counselor/dashboard/index.blade.php ENDPATH**/ ?>