<?php $__env->startSection('title','Counseling'); ?>
<?php $__env->startSection('page-title','Guidance Counseling'); ?>
<?php $__env->startSection('page-sub','Request a session — auto-queued, all requests accepted'); ?>
<?php $__env->startSection('content'); ?>
<div class="alert al-i an"><i class="fas fa-info-circle"></i><span><strong>Auto-Queue:</strong> All counseling requests are automatically accepted and queued. No requests are declined.</span></div>
<div class="g2" style="align-items:start;">
<div class="card an"><div class="ch"><i class="fas fa-plus-circle" style="color:var(--gm);"></i><h2>Request Counseling Session</h2></div><div class="cb">
<form method="POST" action="<?php echo e(route('student.counseling.store')); ?>"><?php echo csrf_field(); ?>
<div class="fg"><label class="fl">Type of Concern <span style="color:var(--danger);">*</span></label><select name="concern_type" class="fc" required><option value="">Select concern type...</option><?php $__currentLoopData = ['Academic Stress','Personal Issue','Career Guidance','Mental Health / Anxiety','Family Concern','Financial Stress','Relationship Issue','General Wellness']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($type); ?>"><?php echo e($type); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div class="fg"><label class="fl">Priority Level</label><div style="display:grid;grid-template-columns:repeat(3,1fr);gap:7px;"><?php $__currentLoopData = [['Normal','fas fa-circle'],['Medium','fas fa-exclamation'],['Urgent','fas fa-exclamation-triangle']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><label style="display:flex;align-items:center;gap:6px;padding:8px 10px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;font-size:13px;"><input type="radio" name="priority" value="<?php echo e($p[0]); ?>" <?php echo e($p[0]==='Normal'?'checked':''); ?> style="accent-color:var(--g);"><i class="<?php echo e($p[1]); ?>" style="font-size:11px;"></i> <?php echo e($p[0]); ?></label><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div>
<div class="fg"><label class="fl">Preferred Date <span style="font-size:11px;color:var(--tm);font-weight:400;">(optional)</span></label><input type="date" name="preferred_date" class="fc" min="<?php echo e(date('Y-m-d',strtotime('+1 day'))); ?>"></div>
<div class="fg"><label class="fl">Preferred Time</label><select name="preferred_time" class="fc"><option value="">No preference</option><option>8:00 AM – 9:00 AM</option><option>9:00 AM – 10:00 AM</option><option>10:00 AM – 11:00 AM</option><option>1:00 PM – 2:00 PM</option><option>2:00 PM – 3:00 PM</option><option>3:00 PM – 4:00 PM</option></select></div>
<div class="fg"><label class="fl">Brief Description</label><textarea name="concern_detail" class="fc" rows="4" placeholder="Briefly describe what you'd like to discuss..."></textarea></div>
<div style="background:var(--gp);border-radius:var(--rs);padding:11px 13px;margin-bottom:14px;font-size:12px;color:var(--g);"><i class="fas fa-shield-alt" style="margin-right:6px;color:var(--gm);"></i><strong>Confidential:</strong> Your session details are strictly confidential.</div>
<button type="submit" class="btn btn-p" style="width:100%;justify-content:center;"><i class="fas fa-paper-plane"></i> Submit Request — Auto Queue</button>
</form></div></div>
<div>
<div class="card an mb3"><div class="ch"><i class="fas fa-list" style="color:var(--gm);"></i><h2>My Sessions</h2></div>
<div class="cb" style="padding:0;">
<?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div style="display:flex;align-items:center;gap:11px;padding:13px 18px;border-bottom:1px solid var(--bd);">
<div style="width:40px;height:40px;border-radius:11px;background:<?php echo e($ses->status==='Completed'?'#d0f0d8':($ses->status==='Scheduled'?'var(--gp)':'#e0f3f8')); ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-<?php echo e($ses->status==='Completed'?'check-circle':($ses->status==='Scheduled'?'calendar-check':'clock')); ?>" style="color:<?php echo e($ses->status==='Completed'?'#0d6624':($ses->status==='Scheduled'?'var(--gm)':'var(--info)')); ?>;font-size:15px;"></i></div>
<div style="flex:1;"><div class="fws" style="font-size:13px;"><?php echo e($ses->concern_type); ?></div><div class="tm" style="font-size:12px;"><?php echo e($ses->session_date?->format('M d, Y')??' — '); ?> <?php echo e($ses->session_time??''); ?></div></div>
<span class="badge <?php echo e($ses->status==='Completed'?'b-s':($ses->status==='Scheduled'?'b-p':'b-i')); ?>"><?php echo e($ses->status); ?></span>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div style="padding:20px;text-align:center;color:var(--tm);font-size:13px;">No sessions yet. Submit a request above.</div>
<?php endif; ?>
</div></div>
<div class="card an"><div class="ch" style="background:linear-gradient(135deg,var(--g),#1a5c28);"><i class="fas fa-stream" style="color:var(--y);"></i><h2 style="color:#fff;">Queue Status</h2></div>
<div class="cb" style="text-align:center;">
<div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,var(--info),#1a5580);display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;"><div style="font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:800;color:#fff;">#<?php echo e($queuePos + 1); ?></div></div>
<div class="fws" style="font-size:14px;color:var(--g);">Queue Size</div>
<div class="tm" style="font-size:12px;margin-top:3px;"><?php echo e($queuePos); ?> ahead in queue</div>
<div class="alert al-i mt3" style="margin-bottom:0;font-size:12px;"><i class="fas fa-bell"></i><span>You'll be notified when your session is scheduled.</span></div>
</div></div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/student/counseling/index.blade.php ENDPATH**/ ?>