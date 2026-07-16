<?php $__env->startSection('title','User Management'); ?>
<?php $__env->startSection('page-title','User Management'); ?>
<?php $__env->startSection('page-sub','Create accounts and assign roles — all role assignments done by Super Admin only'); ?>
<?php $__env->startSection('content'); ?>
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:18px;">
<div style="display:flex;gap:9px;flex-wrap:wrap;">
<div style="position:relative;"><i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;pointer-events:none;"></i><input type="text" id="uSearch" class="fc" placeholder="Search name or email..." style="padding-left:32px;width:220px;"></div>
<select class="fc" style="width:150px;" id="rFilter"><option value="">All Roles</option><option value="superadmin">Super Admin</option><option value="admin">Admin</option><option value="officer">Officer</option><option value="student">Student</option></select>
</div>
<a href="<?php echo e(route('superadmin.users.create')); ?>" class="btn btn-ac"><i class="fas fa-user-plus"></i> Create User & Assign Role</a>
</div>
<div class="card an"><div class="ch"><i class="fas fa-users" style="color:var(--y);"></i><h2>All System Users</h2><span class="badge b-y" style="margin-left:6px;"><?php echo e($users->total()); ?> total</span></div>
<div class="tw"><table id="uTable">
<thead><tr><th>User</th><th>Role</th><th>Department</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr data-role="<?php echo e($user->role); ?>">
<td><div style="display:flex;align-items:center;gap:9px;"><div class="av" style="width:34px;height:34px;font-size:13px;background:<?php echo e($user->role==='superadmin'?'linear-gradient(135deg,var(--y),var(--yd))':'rgba(240,192,32,.15)'); ?>;color:<?php echo e($user->role==='superadmin'?'#0d3318':'var(--y)'); ?>;"><?php echo e(strtoupper(substr($user->name,0,1))); ?></div><div><div class="fws" style="font-size:13px;color:#fff;"><?php echo e($user->name); ?></div><div class="mono" style="font-size:11px;color:var(--tm);"><?php echo e($user->email); ?></div></div></div></td>
<td><span class="badge <?php echo e($user->role==='superadmin'?'b-sa':($user->role==='student'?'b-g':($user->role==='admin'?'b-y':'b-i'))); ?>"><?php if($user->role==='superadmin'): ?><i class="fas fa-crown" style="margin-right:4px;font-size:9px;"></i><?php endif; ?><?php echo e(ucfirst($user->role)); ?></span></td>
<td style="font-size:12px;color:var(--tm);"><?php echo e($user->department??'—'); ?></td>
<td><span class="badge <?php echo e($user->is_active?'b-s':'b-d'); ?>"><?php echo e($user->is_active?'Active':'Inactive'); ?></span></td>
<td class="mono" style="font-size:11px;color:var(--tm);"><?php echo e($user->last_login_at?$user->last_login_at->diffForHumans():'Never'); ?></td>
<td><div style="display:flex;gap:5px;">
<a href="<?php echo e(route('superadmin.users.edit',$user->id)); ?>" class="btn btn-o btn-sm btn-ic" title="Edit"><i class="fas fa-edit"></i></a>
<?php if($user->role!=='superadmin'): ?>
<form method="POST" action="<?php echo e(route('superadmin.users.toggle',$user->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
<button class="btn <?php echo e($user->is_active?'btn-d':'btn-s'); ?> btn-sm btn-ic" onclick="return confirm('<?php echo e($user->is_active?'Deactivate':'Activate'); ?> this user?')"><i class="fas fa-<?php echo e($user->is_active?'ban':'check'); ?>"></i></button>
</form>
<?php endif; ?>
</div></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="6" style="text-align:center;padding:18px;color:var(--tm);">No users found. <a href="<?php echo e(route('superadmin.users.create')); ?>" style="color:var(--y);">Create one</a></td></tr>
<?php endif; ?>
</tbody></table></div>
<?php if($users->hasPages()): ?><div style="padding:13px 18px;border-top:1px solid var(--bd);"><?php echo e($users->links()); ?></div><?php endif; ?>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('uSearch').addEventListener('input',function(){const q=this.value.toLowerCase();document.querySelectorAll('#uTable tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(q)?'':'none');});
document.getElementById('rFilter').addEventListener('change',function(){const v=this.value;document.querySelectorAll('#uTable tbody tr[data-role]').forEach(r=>r.style.display=(!v||r.dataset.role===v)?'':'none');});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('superadmin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/superadmin/users/index.blade.php ENDPATH**/ ?>