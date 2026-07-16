<?php $__env->startSection('title','Login Logs'); ?>
<?php $__env->startSection('page-title','Login Logs'); ?>
<?php $__env->startSection('page-sub','All system login activity and audit trail'); ?>
<?php $__env->startSection('content'); ?>
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
<div style="display:flex;gap:9px;flex-wrap:wrap;">
<div style="position:relative;"><i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;pointer-events:none;"></i><input type="text" id="lSearch" class="fc" placeholder="Search email or IP..." style="padding-left:32px;width:200px;"></div>
<form method="GET" action="<?php echo e(route('superadmin.logs')); ?>" style="display:flex;gap:8px;">
<select name="status" class="fc" style="width:130px;" onchange="this.form.submit()"><option value="">All Status</option><option value="success" <?php echo e(request('status')==='success'?'selected':''); ?>>Success</option><option value="failed" <?php echo e(request('status')==='failed'?'selected':''); ?>>Failed</option><option value="blocked" <?php echo e(request('status')==='blocked'?'selected':''); ?>>Blocked</option></select>
<select name="role" class="fc" style="width:130px;" onchange="this.form.submit()"><option value="">All Roles</option><option value="superadmin" <?php echo e(request('role')==='superadmin'?'selected':''); ?>>Super Admin</option><option value="admin" <?php echo e(request('role')==='admin'?'selected':''); ?>>Admin</option><option value="officer" <?php echo e(request('role')==='officer'?'selected':''); ?>>Officer</option><option value="student" <?php echo e(request('role')==='student'?'selected':''); ?>>Student</option></select>
</form>
</div>
<div style="font-size:13px;color:var(--tm);">Total: <strong style="color:#fff;"><?php echo e($logs->total()); ?></strong></div>
</div>
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
<?php $__currentLoopData = [["Today's Logins",\App\Models\LoginLog::whereDate('logged_in_at',today())->count(),'y'],['Successful',\App\Models\LoginLog::where('status','success')->count(),'g'],['Failed',\App\Models\LoginLog::where('status','failed')->count(),'r'],['Blocked',\App\Models\LoginLog::where('status','blocked')->count(),'o']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div style="background:var(--card);border:1px solid var(--bd);border-radius:20px;padding:7px 16px;display:flex;align-items:center;gap:8px;">
<span style="font-family:'Sora',sans-serif;font-size:18px;font-weight:800;color:<?php echo e($chip[2]==='y'?'var(--y)':($chip[2]==='g'?'var(--gm)':($chip[2]==='r'?'var(--danger)':'var(--warn)'))); ?>;"><?php echo e($chip[1]); ?></span>
<span style="font-size:12px;color:var(--tm);"><?php echo e($chip[0]); ?></span>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="card an"><div class="ch"><i class="fas fa-history" style="color:var(--y);"></i><h2>Login Activity Log</h2></div>
<div class="tw"><table id="lTable">
<thead><tr><th>#</th><th>User</th><th>Role</th><th>IP Address</th><th>Status</th><th>Login Time</th><th>Logout</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr>
<td class="mono" style="font-size:11px;color:var(--tm);"><?php echo e($log->id); ?></td>
<td><div><div class="fws" style="font-size:12px;color:#fff;"><?php echo e($log->user?->name??'Unknown'); ?></div><div class="mono" style="font-size:10px;color:var(--tm);"><?php echo e($log->email); ?></div></div></td>
<td><?php if($log->role): ?><span class="badge <?php echo e($log->role==='superadmin'?'b-sa':($log->role==='student'?'b-g':($log->role==='admin'?'b-y':'b-i'))); ?>" style="font-size:10px;"><?php echo e(ucfirst($log->role)); ?></span><?php else: ?><span class="badge b-gray" style="font-size:10px;">—</span><?php endif; ?></td>
<td class="mono" style="font-size:12px;color:var(--tm);"><?php echo e($log->ip_address??'—'); ?></td>
<td><span class="badge <?php echo e($log->status==='success'?'b-s':($log->status==='failed'?'b-d':'b-w')); ?>" style="font-size:10px;"><i class="fas fa-<?php echo e($log->status==='success'?'check':'times'); ?>" style="font-size:9px;margin-right:3px;"></i><?php echo e(ucfirst($log->status)); ?></span></td>
<td class="mono" style="font-size:11px;"><?php echo e($log->logged_in_at?->format('M d, Y h:i A')??'—'); ?></td>
<td class="mono" style="font-size:11px;color:var(--tm);"><?php echo e($log->logged_out_at?->format('h:i A')??'—'); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--tm);">No login logs yet.</td></tr>
<?php endif; ?>
</tbody></table></div>
<?php if($logs->hasPages()): ?><div style="padding:13px 18px;border-top:1px solid var(--bd);"><?php echo e($logs->links()); ?></div><?php endif; ?>
</div>
<?php $__env->startPush('scripts'); ?>
<script>document.getElementById('lSearch').addEventListener('input',function(){const q=this.value.toLowerCase();document.querySelectorAll('#lTable tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(q)?'':'none');});</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('superadmin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/superadmin/logs/index.blade.php ENDPATH**/ ?>