<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('page-title','Super Admin Dashboard'); ?>
<?php $__env->startSection('page-sub','System overview — user management and monitoring'); ?>
<?php $__env->startSection('content'); ?>
<div class="sg">
<div class="sc an d1"><div class="si w"><i class="fas fa-users"></i></div><div class="sv"><div class="lbl">Total Users</div><div class="val"><?php echo e($stats['total_users']); ?></div><div class="chg"><?php echo e($stats['active_users']); ?> active</div></div></div>
<div class="sc an d2"><div class="si g"><i class="fas fa-user-graduate"></i></div><div class="sv"><div class="lbl">Students</div><div class="val"><?php echo e($stats['students']); ?></div><div class="chg">Registered</div></div></div>
<div class="sc an d3"><div class="si y"><i class="fas fa-user-tie"></i></div><div class="sv"><div class="lbl">Staff</div><div class="val"><?php echo e($stats['staff']); ?></div><div class="chg">Admin + Officers</div></div></div>
<div class="sc an d4"><div class="si t"><i class="fas fa-sign-in-alt"></i></div><div class="sv"><div class="lbl">Logins Today</div><div class="val"><?php echo e($stats['online_today']); ?></div><div class="chg">Unique users</div></div></div>
<div class="sc an d5"><div class="si r"><i class="fas fa-times-circle"></i></div><div class="sv"><div class="lbl">Failed Logins</div><div class="val"><?php echo e($stats['failed_logins']); ?></div><div class="chg dn">All time</div></div></div>
<div class="sc an d6"><div class="si o"><i class="fas fa-file-alt"></i></div><div class="sv"><div class="lbl">Applications</div><div class="val"><?php echo e($stats['applications']); ?></div><div class="chg">Scholarship apps</div></div></div>
</div>
<div class="g2 mb3">
<div class="card an"><div class="ch"><i class="fas fa-shield-alt" style="color:var(--y);"></i><h2>Users by Role</h2><div class="ch-acts"><a href="<?php echo e(route('superadmin.users.index')); ?>" class="btn btn-o btn-sm">Manage</a></div></div>
<div class="cb">
<?php $__currentLoopData = [['superadmin','Super Admin','fas fa-crown','b-sa'],['admin','Admin','fas fa-user-shield','b-y'],['officer','Officer','fas fa-user-tie','b-i'],['student','Student','fas fa-user-graduate','b-g']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php $cnt=$usersByRole[$role[0]]??0; ?>
<div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--bd);">
<div style="width:40px;height:40px;border-radius:10px;background:rgba(240,192,32,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="<?php echo e($role[2]); ?>" style="color:var(--y);font-size:15px;"></i></div>
<div style="flex:1;"><div class="fws" style="font-size:13px;color:#fff;"><?php echo e($role[1]); ?></div><div style="font-size:11px;color:var(--tm);text-transform:uppercase;letter-spacing:.5px;"><?php echo e($role[0]); ?></div></div>
<div style="text-align:right;"><div style="font-family:'Sora',sans-serif;font-size:22px;font-weight:800;color:var(--y);"><?php echo e($cnt); ?></div><div style="font-size:11px;color:var(--tm);">users</div></div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div style="margin-top:14px;"><a href="<?php echo e(route('superadmin.users.create')); ?>" class="btn btn-ac" style="width:100%;justify-content:center;"><i class="fas fa-user-plus"></i> Create User & Assign Role</a></div>
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-history" style="color:var(--y);"></i><h2>Recent Login Activity</h2><div class="ch-acts"><a href="<?php echo e(route('superadmin.logs')); ?>" class="btn btn-o btn-sm">View All</a></div></div>
<div class="tw"><table><thead><tr><th>User</th><th>Role</th><th>Status</th><th>Time</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $recentLogins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s"><?php echo e(strtoupper(substr($log->email,0,1))); ?></div><div><div class="fws" style="font-size:12px;color:#fff;"><?php echo e($log->user?->name??'Unknown'); ?></div><div class="mono" style="font-size:10px;color:var(--tm);"><?php echo e($log->ip_address); ?></div></div></div></td>
<td><span class="badge <?php echo e($log->role==='superadmin'?'b-sa':($log->role==='student'?'b-g':'b-y')); ?>" style="font-size:10px;"><?php echo e($log->role??'—'); ?></span></td>
<td><span class="badge <?php echo e($log->status==='success'?'b-s':($log->status==='failed'?'b-d':'b-w')); ?>" style="font-size:10px;"><?php echo e(ucfirst($log->status)); ?></span></td>
<td class="mono" style="font-size:11px;color:var(--tm);"><?php echo e($log->logged_in_at?->diffForHumans()); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="4" style="text-align:center;color:var(--tm);padding:18px;">No login records yet.</td></tr>
<?php endif; ?>
</tbody></table></div></div>
</div>
<div class="card an"><div class="ch"><i class="fas fa-bolt" style="color:var(--y);"></i><h2>Quick Actions</h2></div>
<div class="cb" style="display:flex;flex-wrap:wrap;gap:10px;">
<a href="<?php echo e(route('superadmin.users.create')); ?>" class="btn btn-ac"><i class="fas fa-user-plus"></i> Create User & Assign Role</a>
<a href="<?php echo e(route('superadmin.users.index')); ?>" class="btn btn-w"><i class="fas fa-users"></i> Manage Users</a>
<a href="<?php echo e(route('superadmin.monitoring')); ?>" class="btn btn-w"><i class="fas fa-chart-line"></i> Live Monitoring</a>
<a href="<?php echo e(route('superadmin.logs')); ?>" class="btn btn-w"><i class="fas fa-history"></i> Login Logs</a>
<a href="<?php echo e(route('superadmin.logs')); ?>?status=failed" class="btn btn-d btn-sm"><i class="fas fa-exclamation-triangle"></i> Failed Logins</a>
<a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-o" target="_blank"><i class="fas fa-external-link-alt"></i> Admin Panel</a>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('superadmin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/superadmin/dashboard/index.blade.php ENDPATH**/ ?>