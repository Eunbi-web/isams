<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('page-title','Dashboard'); ?>
<?php $__env->startSection('page-sub','Welcome back — ISAMS Student Portal'); ?>
<?php $__env->startSection('content'); ?>
<?php $aiScore = $student ? (function($s){ $apps=\App\Models\ScholarshipApplication::where('student_id',$s->id)->orderByDesc('ai_score')->first(); return $apps?$apps->ai_score:0; })($student) : 0; $eligibility=$aiScore>=75?'Eligible':($aiScore>=50?'For Review':'Not Evaluated'); ?>
<div style="background:linear-gradient(135deg,#0d5c24,#1a6b2f);border-radius:var(--r);padding:20px 24px;display:flex;align-items:center;gap:18px;margin-bottom:20px;border:1px solid var(--gm);" class="an">
<div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;flex-shrink:0;"><i class="fas fa-<?php echo e($eligibility==='Eligible'?'check-circle':'robot'); ?>"></i></div>
<div style="flex:1;"><div style="font-family:'Sora',sans-serif;font-size:20px;font-weight:800;color:#fff;"><?php echo e($eligibility==='Eligible'?'You are Eligible!':($eligibility==='For Review'?'Profile Under Review':'Check Your Eligibility')); ?></div>
<div style="font-size:13px;color:rgba(255,255,255,.75);margin-top:3px;">GWA: <strong style="color:#fff;"><?php echo e(number_format($student?->gwa??0,2)); ?></strong> · <?php echo e($student?->enrollment_type??'N/A'); ?> · <?php echo e(Str::limit($student?->course??'N/A',30)); ?></div>
<div style="margin-top:10px;"><a href="<?php echo e(route('student.eligibility')); ?>" class="btn btn-ac btn-sm" style="font-size:11px;"><i class="fas fa-robot"></i> View AI Eligibility</a></div></div>
<div style="text-align:center;flex-shrink:0;"><div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;flex-direction:column;align-items:center;justify-content:center;"><div style="font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:800;color:#fff;"><?php echo e($aiScore); ?>%</div><div style="font-size:9px;color:rgba(255,255,255,.7);">AI Score</div></div></div>
</div>
<div class="sg" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
<div class="sc an d1"><div class="si y"><i class="fas fa-award"></i></div><div class="sv"><div class="lbl">Open Programs</div><div class="val"><?php echo e($openScholarships->count()); ?></div><div class="chg">Available now</div></div></div>
<div class="sc an d2"><div class="si g"><i class="fas fa-file-alt"></i></div><div class="sv"><div class="lbl">My Applications</div><div class="val"><?php echo e($myApplications->count()); ?></div><div class="chg"><?php echo e($myApplications->where('status','Approved')->count()); ?> approved</div></div></div>
<div class="sc an d3"><div class="si dg"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Approved</div><div class="val"><?php echo e($myApplications->where('status','Approved')->count()); ?></div><div class="chg">Active grants</div></div></div>
<div class="sc an d4"><div class="si t"><i class="fas fa-heart"></i></div><div class="sv"><div class="lbl">Counseling</div><div class="val"><?php echo e(\App\Models\CounselingSession::where('student_id',auth()->user()->student?->id??0)->count()); ?></div><div class="chg">Sessions</div></div></div>
</div>
<div class="g2 mb3">
<div class="card an"><div class="ch"><i class="fas fa-award" style="color:var(--yd);"></i><h2>Open Scholarships</h2><div class="ch-acts"><a href="<?php echo e(route('student.scholarships')); ?>" class="btn btn-o btn-sm">Browse All</a></div></div>
<div class="cb" style="padding:0;">
<?php $__empty_1 = true; $__currentLoopData = $openScholarships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div style="padding:13px 18px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:11px;">
<div class="si y" style="width:38px;height:38px;font-size:14px;border-radius:10px;flex-shrink:0;"><i class="fas fa-award"></i></div>
<div style="flex:1;"><div class="fws" style="font-size:13px;"><?php echo e($sch->name); ?></div><div class="tm" style="font-size:12px;"><?php echo e(Str::limit($sch->benefits??'',45)); ?></div></div>
<!-- Apply button removed from student dashboard -->
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div style="padding:20px;text-align:center;color:var(--tm);">No open scholarships at the moment.</div>
<?php endif; ?>
</div></div>
<div>
<div class="card an mb3"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>My Applications</h2><div class="ch-acts"><a href="<?php echo e(route('student.applications')); ?>" class="btn btn-o btn-sm">View All</a></div></div>
<div class="cb" style="padding:0;">
<?php $__empty_1 = true; $__currentLoopData = $myApplications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div style="display:flex;align-items:center;gap:11px;padding:12px 18px;border-bottom:1px solid var(--bd);">
<div class="si g" style="width:36px;height:36px;font-size:13px;border-radius:9px;flex-shrink:0;"><i class="fas fa-file-alt"></i></div>
<div style="flex:1;"><div class="fws" style="font-size:13px;"><?php echo e(Str::limit($app->scholarship->name??'—',30)); ?></div><div class="mono tm" style="font-size:11px;">Score: <?php echo e($app->ai_score); ?>%</div></div>
<span class="badge <?php echo e($app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w')); ?>"><?php echo e($app->status); ?></span>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div style="padding:18px;text-align:center;color:var(--tm);font-size:13px;">No applications yet. <a href="<?php echo e(route('student.scholarships')); ?>" style="color:var(--gm);">Browse scholarships</a></div>
<?php endif; ?>
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-heart" style="color:#e87070;"></i><h2>Counseling</h2><div class="ch-acts"><a href="<?php echo e(route('student.counseling.index')); ?>" class="btn btn-o btn-sm">Manage</a></div></div>
<div class="cb"><div style="background:var(--gp);border-radius:var(--rs);padding:12px 14px;margin-bottom:12px;font-size:13px;color:var(--g);"><i class="fas fa-info-circle" style="margin-right:6px;"></i><strong>Auto-Queue:</strong> All requests are accepted and queued.</div>
<a href="<?php echo e(route('student.counseling.index')); ?>" class="btn btn-p btn-sm" style="width:100%;justify-content:center;"><i class="fas fa-plus"></i> Request Session</a>
</div></div>
</div>
</div>
<div class="card an"><div class="ch"><i class="fas fa-bullhorn" style="color:var(--gm);"></i><h2>Announcements</h2><div class="ch-acts"><a href="<?php echo e(route('student.announcements')); ?>" class="btn btn-o btn-sm">View All</a></div></div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;padding:18px;">
<?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:13px;transition:all .2s;" onmouseover="this.style.borderColor='var(--gm)'" onmouseout="this.style.borderColor='var(--bd)'">
<div class="fws" style="font-size:13px;margin-bottom:4px;"><?php echo e(Str::limit($ann->title,40)); ?></div>
<div class="tm" style="font-size:12px;margin-bottom:6px;"><?php echo e(Str::limit($ann->body,80)); ?></div>
<div style="font-size:11px;color:var(--tm);"><i class="fas fa-clock" style="margin-right:3px;"></i><?php echo e($ann->created_at->diffForHumans()); ?></div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div style="color:var(--tm);font-size:13px;">No announcements.</div>
<?php endif; ?>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/student/dashboard/index.blade.php ENDPATH**/ ?>