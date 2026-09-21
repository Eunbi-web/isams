
<?php
$criteria = is_array($scholarship->ai_criteria) ? $scholarship->ai_criteria : json_decode($scholarship->ai_criteria ?? '[]', true);
?>
<div class="card an"><div class="ch"><div class="si y" style="width:40px;height:40px;font-size:16px;border-radius:10px;flex-shrink:0;"><i class="fas fa-award"></i></div><div><h2><?php echo e($scholarship->name); ?></h2><div class="tm" style="font-size:12px;"><?php echo e($scholarship->type); ?></div></div><div class="ch-acts"><span class="badge <?php echo e($scholarship->status==='Active'?'b-s':'b-d'); ?>"><?php echo e($scholarship->status); ?></span></div></div>
<div class="cb">
<div class="g2 mb3">
<?php $__currentLoopData = [['Benefit',$scholarship->benefits??'—','fas fa-gift'],['Amount (₱)',$scholarship->amount??'—','fas fa-money-bill-wave'],['Slots',$scholarship->slots??'—','fas fa-users'],['Source',$scholarship->source??'—','fas fa-university'],['Deadline',$scholarship->end_date?->format('M d, Y')??'—','fas fa-calendar'],['Slots Remaining',$scholarship->slots_remaining,'fas fa-user-check']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:12px;"><div style="font-size:11px;color:var(--tm);margin-bottom:3px;"><i class="<?php echo e($d[2]); ?>" style="margin-right:4px;"></i><?php echo e($d[0]); ?></div><div class="fws" style="font-size:14px;"><?php echo e($d[1]); ?></div></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php if($scholarship->description): ?><div class="alert al-i" style="margin-bottom:14px;font-size:13px;"><i class="fas fa-align-left"></i><span><?php echo e($scholarship->description); ?></span></div><?php endif; ?>
<?php if($scholarship->requirements): ?><div class="alert al-w" style="margin-bottom:14px;font-size:13px;white-space:pre-wrap;"><i class="fas fa-list"></i><span><?php echo e($scholarship->requirements); ?></span></div><?php endif; ?>
<?php if(!empty($criteria)): ?>
<div class="alert al-ai" style="margin-bottom:0;font-size:12px;align-items:flex-start;"><i class="fas fa-robot"></i><span><strong>AI Eligibility Criteria:</strong><br>
GWA of <?php echo e($criteria['gwa_max'] ?? 1.75); ?> or better &nbsp;·&nbsp; Family income not above ₱<?php echo e(number_format((float)($criteria['income_max'] ?? 400000))); ?> per year &nbsp;·&nbsp; <?php echo e(($criteria['no_failing'] ?? true) ? 'No failing grades' : 'Failing grades allowed'); ?> &nbsp;·&nbsp; <?php echo e(($criteria['no_discipline'] ?? false) ? 'No disciplinary case' : 'Disciplinary cases allowed'); ?></span></div>
<?php else: ?>
<div class="alert al-ai" style="margin-bottom:0;font-size:12px;"><i class="fas fa-robot"></i><span>No AI eligibility criteria set for this program yet.</span></div>
<?php endif; ?>
</div></div>
<div class="card an mt3"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Applications (<?php echo e($scholarship->applications->count()); ?>)</h2></div>
<div class="tw"><table><thead><tr><th>Student</th><th>GWA</th><th>AI Score</th><th>Eligibility</th><th>Status</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $scholarship->applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<?php $sc=$app->ai_score??0; $el=$app->ai_eligibility==='Eligible'?'el':($app->ai_eligibility==='For Review'?'rv':'no'); ?>
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s"><?php echo e(strtoupper(substr($app->student->first_name??'S',0,1))); ?></div><span class="fws" style="font-size:13px;"><?php echo e($app->student->full_name??'—'); ?></span></div></td>
<td class="mono fwb" style="color:var(--gm);"><?php echo e(number_format($app->gwa??0,2)); ?></td>
<td><div style="display:flex;align-items:center;gap:5px;"><div style="flex:1;"><div class="asb"><div class="asf <?php echo e($sc>=75?'ash':($sc>=50?'asm':'asl')); ?>" style="width:<?php echo e($sc); ?>%;"></div></div></div><span class="mono" style="font-size:11px;font-weight:700;"><?php echo e($sc); ?>%</span></div></td>
<td><span class="badge elig-<?php echo e($el); ?>" style="font-size:10px;"><?php echo e($app->ai_eligibility??'—'); ?></span></td>
<td><span class="badge <?php echo e($app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w')); ?>"><?php echo e($app->status); ?></span></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="5" style="text-align:center;padding:16px;color:var(--tm);">No applications yet.</td></tr>
<?php endif; ?>
</tbody></table></div></div>
<?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/scholarships/_details.blade.php ENDPATH**/ ?>