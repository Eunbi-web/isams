<?php $__env->startSection('title','Counseling'); ?>
<?php $__env->startSection('page-title','Guidance Counseling'); ?>
<?php $__env->startSection('page-sub','Auto-queue management — all requests accepted'); ?>
<?php $__env->startSection('content'); ?>
<div class="alert al-i an"><i class="fas fa-info-circle"></i><span><strong>Auto-Queue Policy:</strong> All student counseling requests are automatically accepted. No requests are declined.</span></div>
<div class="sg" style="grid-template-columns:repeat(4,1fr);">
<div class="sc an"><div class="si t"><i class="fas fa-stream"></i></div><div class="sv"><div class="lbl">In Queue</div><div class="val"><?php echo e(\App\Models\CounselingSession::where('status','In Queue')->count()); ?></div></div></div>
<div class="sc an"><div class="si g"><i class="fas fa-calendar-check"></i></div><div class="sv"><div class="lbl">Scheduled</div><div class="val"><?php echo e(\App\Models\CounselingSession::where('status','Scheduled')->count()); ?></div></div></div>
<div class="sc an"><div class="si o"><i class="fas fa-exclamation-triangle"></i></div><div class="sv"><div class="lbl">Urgent</div><div class="val"><?php echo e(\App\Models\CounselingSession::where('priority','Urgent')->count()); ?></div></div></div>
<div class="sc an"><div class="si dg"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Completed</div><div class="val"><?php echo e(\App\Models\CounselingSession::where('status','Completed')->count()); ?></div></div></div>
</div>
<div class="card an">
<div class="ch" style="background:linear-gradient(135deg,var(--g),#1a5c28);"><i class="fas fa-stream" style="color:var(--y);"></i><h2 style="color:#fff;">Counseling Queue</h2><div class="ch-acts"><span style="font-size:12px;color:rgba(255,255,255,.7);"><?php echo e($sessions->total()); ?> total</span></div></div>
<div class="tw"><table>
<thead><tr><th>#</th><th>Student</th><th>Concern</th><th>Priority</th><th>Preferred</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr>
<td class="mono tm" style="font-size:11px;">#<?php echo e($ses->queue_position??'—'); ?></td>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s"><?php echo e(strtoupper(substr($ses->student->first_name??'S',0,1))); ?></div><div class="fws" style="font-size:13px;"><?php echo e($ses->student->full_name??'—'); ?></div></div></td>
<td style="font-size:13px;"><?php echo e($ses->concern_type); ?></td>
<td><span class="badge <?php echo e($ses->priority==='Urgent'?'b-d':($ses->priority==='Medium'?'b-w':'b-p')); ?>"><?php echo e($ses->priority); ?></span></td>
<td class="mono tm" style="font-size:11px;"><?php echo e($ses->preferred_date?->format('M d')??'—'); ?> <?php echo e($ses->preferred_time??''); ?></td>
<td><span class="badge <?php echo e($ses->status==='Completed'?'b-s':($ses->status==='Scheduled'?'b-p':'b-i')); ?>"><?php echo e($ses->status); ?></span></td>
<td><div style="display:flex;gap:4px;">
<?php if($ses->status==='In Queue'): ?>
<button onclick="openModal('sched-<?php echo e($ses->id); ?>')" class="btn btn-p btn-sm btn-ic" title="Schedule"><i class="fas fa-calendar-plus"></i></button>
<?php elseif($ses->status==='Scheduled'): ?>
<form method="POST" action="<?php echo e(route('admin.counseling.complete',$ses->id)); ?>"><?php echo csrf_field(); ?><button class="btn btn-s btn-sm btn-ic" title="Complete"><i class="fas fa-check"></i></button></form>
<?php endif; ?>
</div></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="7" style="text-align:center;padding:18px;color:var(--tm);">No counseling sessions yet.</td></tr>
<?php endif; ?>
</tbody></table></div>
<?php if($sessions->hasPages()): ?><div style="padding:13px 18px;border-top:1px solid var(--bd);"><?php echo e($sessions->links()); ?></div><?php endif; ?>
</div>
<?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($ses->status==='In Queue'): ?>
<div class="mo" id="sched-<?php echo e($ses->id); ?>">
<div class="mb" style="max-width:480px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-calendar-plus"></i></div><div><h3>Schedule Session</h3><div class="tm" style="font-size:12px;"><?php echo e($ses->student->full_name??'—'); ?> — <?php echo e($ses->concern_type); ?></div></div><button class="mc" onclick="closeModal('sched-<?php echo e($ses->id); ?>')"><i class="fas fa-times"></i></button></div>
<form method="POST" action="<?php echo e(route('admin.counseling.schedule',$ses->id)); ?>"><?php echo csrf_field(); ?>
<div class="mbody">
<div class="fg"><label class="fl">Session Date</label><input type="date" name="session_date" class="fc" min="<?php echo e(date('Y-m-d')); ?>" required></div>
<div class="fg"><label class="fl">Session Time</label><select name="session_time" class="fc"><option>8:00 AM – 9:00 AM</option><option>9:00 AM – 10:00 AM</option><option>10:00 AM – 11:00 AM</option><option>1:00 PM – 2:00 PM</option><option>2:00 PM – 3:00 PM</option><option>3:00 PM – 4:00 PM</option></select></div>
<div class="fg"><label class="fl">Venue / Mode</label><select name="venue" class="fc"><option value="Guidance Office — Room 201">Guidance Office — Room 201</option><option value="Online (Zoom)">Online (Zoom)</option><option value="Online (Google Meet)">Online (Google Meet)</option></select></div>
<div class="fg"><label class="fl">Notes</label><textarea name="notes" class="fc" rows="2" placeholder="Instructions for student..."></textarea></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('sched-<?php echo e($ses->id); ?>')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-calendar-check"></i> Confirm Schedule</button></div>
</form>
</div>
</div>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/counseling/index.blade.php ENDPATH**/ ?>