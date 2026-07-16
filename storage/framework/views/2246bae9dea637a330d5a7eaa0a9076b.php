<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('page-title','Dashboard'); ?>
<?php $__env->startSection('page-sub','ISAMS — Integrated Student Affairs Management System'); ?>
<?php $__env->startSection('ai-bar'); ?>
<div class="ai-bar"><div class="ai-bar-label"><div class="ai-dot"></div>AI Engine Active</div><div class="ai-bar-stats"><div class="ai-stat">Eligible: <strong><?php echo e($stats['eligible']); ?></strong></div><div class="ai-stat">For Review: <strong><?php echo e($stats['for_review']); ?></strong></div><div class="ai-stat">Not Eligible: <strong><?php echo e($stats['not_eligible']); ?></strong></div><div class="ai-stat">Processed Today: <strong><?php echo e($stats['processed_today']); ?></strong></div></div></div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="sg">
<div class="sc an d1"><div class="si y"><i class="fas fa-award"></i></div><div class="sv"><div class="lbl">Active Programs</div><div class="val"><?php echo e($stats['programs']); ?></div><div class="chg">Scholarship programs</div></div></div>
<div class="sc an d2"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">AI Eligible</div><div class="val"><?php echo e($stats['eligible']); ?></div><div class="chg"><i class="fas fa-robot" style="font-size:9px;"></i> AI-verified</div></div></div>
<div class="sc an d3"><div class="si o"><i class="fas fa-hourglass-half"></i></div><div class="sv"><div class="lbl">Pending Apps</div><div class="val"><?php echo e($stats['pending']); ?></div><div class="chg">Awaiting review</div></div></div>
<div class="sc an d4"><div class="si dg"><i class="fas fa-user-graduate"></i></div><div class="sv"><div class="lbl">Active Scholars</div><div class="val"><?php echo e($stats['active_scholars']); ?></div><div class="chg">Approved</div></div></div>
<div class="sc an d5"><div class="si t"><i class="fas fa-stream"></i></div><div class="sv"><div class="lbl">Counseling Queue</div><div class="val"><?php echo e($stats['counseling_queue']); ?></div><div class="chg">Auto-queued</div></div></div>
<div class="sc an d6"><div class="si r"><i class="fas fa-times-circle"></i></div><div class="sv"><div class="lbl">Not Eligible</div><div class="val"><?php echo e($stats['not_eligible']); ?></div><div class="chg dn">Needs attention</div></div></div>
</div>
<div class="g2 mb3">
<div class="card an"><div class="ch"><i class="fas fa-robot" style="color:var(--yd);"></i><h2>AI Eligibility Summary</h2><div class="ch-acts"><a href="<?php echo e(route('admin.ai.index')); ?>" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> Full Filter</a></div></div>
<div class="cb">
<?php $__currentLoopData = [['Eligible',$stats['eligible'],'68%','g','check-circle','ash'],['For Review',$stats['for_review'],'19%','y','exclamation-circle','asm'],['Not Eligible',$stats['not_eligible'],'13%','r','times-circle','asl']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div style="display:flex;align-items:center;gap:13px;padding:13px 0;border-bottom:1px solid var(--bd);">
<div class="si <?php echo e($c[3]); ?>" style="width:42px;height:42px;font-size:16px;border-radius:11px;flex-shrink:0;"><i class="fas fa-<?php echo e($c[4]); ?>"></i></div>
<div style="flex:1;"><div style="display:flex;justify-content:space-between;margin-bottom:5px;"><span class="fws"><?php echo e($c[0]); ?></span><span class="fwb"><?php echo e($c[1]); ?> <span class="tm" style="font-weight:400;font-size:11px;">(<?php echo e($c[2]); ?>)</span></span></div><div class="asb"><div class="asf <?php echo e($c[5]); ?>" style="width:<?php echo e($c[2]); ?>;"></div></div></div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div class="alert al-ai mt3" style="margin-bottom:0;font-size:12px;"><i class="fas fa-lightbulb"></i><span><strong>AI Insight:</strong> Run a fresh AI scan to update all eligibility scores.</span></div>
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-bolt" style="color:var(--yd);"></i><h2>Quick Actions</h2></div>
<div class="cb" style="display:flex;flex-wrap:wrap;gap:9px;">
<a href="<?php echo e(route('admin.applications.create')); ?>" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> New Application</a>
<a href="<?php echo e(route('admin.ai.index')); ?>" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> AI Filter</a>
<form method="POST" action="<?php echo e(route('admin.ai.run')); ?>"><?php echo csrf_field(); ?><button class="btn btn-ac btn-sm"><i class="fas fa-play"></i> Run AI Scan</button></form>
<a href="<?php echo e(route('admin.scholarships.create')); ?>" class="btn btn-o btn-sm"><i class="fas fa-award"></i> Add Program</a>
<a href="<?php echo e(route('admin.students.create')); ?>" class="btn btn-o btn-sm"><i class="fas fa-user-plus"></i> Add Student</a>
<a href="<?php echo e(route('admin.counseling.index')); ?>" class="btn btn-o btn-sm"><i class="fas fa-heart"></i> Counseling Queue</a>
<a href="<?php echo e(route('admin.reports.index')); ?>" class="btn btn-ac btn-sm"><i class="fas fa-chart-bar"></i> Reports</a>
<a href="<?php echo e(route('admin.announcements.create')); ?>" class="btn btn-o btn-sm"><i class="fas fa-bullhorn"></i> Announce</a>
</div></div>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Recent Applications</h2><div class="ch-acts"><a href="<?php echo e(route('admin.applications.index')); ?>" class="btn btn-o btn-sm">View All</a><a href="<?php echo e(route('admin.ai.index')); ?>" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> AI Filter</a></div></div>
<div class="tw"><table><thead><tr><th>Student</th><th>Scholarship</th><th>AI Score</th><th>Eligibility</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php $recentApps = \App\Models\ScholarshipApplication::with(['student','scholarship'])->latest()->take(8)->get(); ?>
<?php $__empty_1 = true; $__currentLoopData = $recentApps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<?php $sc=$app->ai_score??0; $el=$app->ai_eligibility==='Eligible'?'el':($app->ai_eligibility==='For Review'?'rv':'no'); ?>
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s"><?php echo e(strtoupper(substr($app->student->first_name??'S',0,1))); ?></div><div><div class="fws" style="font-size:13px;"><?php echo e($app->student->full_name??'—'); ?></div><div class="mono tm" style="font-size:11px;"><?php echo e($app->student->student_id??'—'); ?></div></div></div></td>
<td style="font-size:12px;color:var(--tm);"><?php echo e(Str::limit($app->scholarship->name??'—',25)); ?></td>
<td style="min-width:90px;"><div style="display:flex;align-items:center;gap:5px;"><div style="flex:1;"><div class="asb"><div class="asf <?php echo e($sc>=75?'ash':($sc>=50?'asm':'asl')); ?>" style="width:<?php echo e($sc); ?>%;"></div></div></div><span class="mono" style="font-size:11px;font-weight:700;"><?php echo e($sc); ?>%</span></div></td>
<td><span class="badge elig-<?php echo e($el); ?>" style="font-size:10px;"><?php echo e($app->ai_eligibility??'—'); ?></span></td>
<td><span class="badge <?php echo e($app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w')); ?>"><?php echo e($app->status); ?></span></td>
<td><a href="<?php echo e(route('admin.applications.show',$app->id)); ?>" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="6" style="text-align:center;padding:18px;color:var(--tm);">No applications yet. <a href="<?php echo e(route('admin.applications.create')); ?>" style="color:var(--gm);">Add one</a></td></tr>
<?php endif; ?>
</tbody></table></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>