<?php $__env->startSection('title','Edit User'); ?>
<?php $__env->startSection('page-title','Edit User & Role'); ?>
<?php $__env->startSection('page-sub','Update user information and role assignment'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:720px;">
<div style="margin-bottom:14px;"><a href="<?php echo e(route('superadmin.users.index')); ?>" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
<form method="POST" action="<?php echo e(route('superadmin.users.update',$user->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<div class="card an mb3"><div class="ch">
<div class="av" style="width:40px;height:40px;font-size:15px;background:<?php echo e($user->role==='superadmin'?'linear-gradient(135deg,var(--y),var(--yd))':'rgba(240,192,32,.15)'); ?>;color:<?php echo e($user->role==='superadmin'?'#0d3318':'var(--y)'); ?>;"><?php echo e(strtoupper(substr($user->name,0,1))); ?></div>
<div><h2><?php echo e($user->name); ?></h2><div style="font-size:12px;color:var(--tm);"><?php echo e($user->email); ?></div></div>
<div class="ch-acts"><span class="badge <?php echo e($user->role==='superadmin'?'b-sa':($user->role==='student'?'b-g':($user->role==='admin'?'b-y':'b-i'))); ?>"><?php echo e(ucfirst($user->role)); ?></span><span class="badge <?php echo e($user->is_active?'b-s':'b-d'); ?>"><?php echo e($user->is_active?'Active':'Inactive'); ?></span></div>
</div><div class="cb">
<div class="g2">
<div class="fg"><label class="fl">Full Name</label><input type="text" name="name" class="fc" value="<?php echo e(old('name',$user->name)); ?>" required></div>
<div class="fg"><label class="fl">Email Address</label><input type="email" name="email" class="fc" value="<?php echo e(old('email',$user->email)); ?>" required></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">New Password <span style="font-size:11px;color:var(--tm);font-weight:400;">(blank to keep)</span></label><input type="password" name="password" class="fc" placeholder="••••••••"></div>
<div class="fg"><label class="fl">Confirm Password</label><input type="password" name="password_confirmation" class="fc" placeholder="••••••••"></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Department</label><input type="text" name="department" class="fc" value="<?php echo e(old('department',$user->department)); ?>" placeholder="e.g. Scholarship Office"></div>
<div class="fg"><label class="fl">Account Status</label><select name="is_active" class="fc"><option value="1" <?php echo e($user->is_active?'selected':''); ?>>Active</option><option value="0" <?php echo e(!$user->is_active?'selected':''); ?>>Inactive</option></select></div>
</div>
</div></div>

<div class="card an mb3"><div class="ch"><i class="fas fa-shield-alt" style="color:var(--y);"></i><h2>Role Assignment</h2></div><div class="cb">
<div class="alert al-y" style="margin-bottom:14px;font-size:12px;"><i class="fas fa-exclamation-triangle"></i><span>Current role: <strong><?php echo e(ucfirst($user->role)); ?></strong>. Changing role will change what portals this user can access.</span></div>
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
<?php $__currentLoopData = [['student','Student','fas fa-user-graduate'],['officer','Officer','fas fa-user-tie'],['admin','Admin','fas fa-user-shield'],['superadmin','Super Admin','fas fa-crown']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<label style="display:flex;align-items:center;gap:10px;padding:11px;border:2px solid <?php echo e(old('role',$user->role)===$role[0]?'var(--y)':'var(--bd)'); ?>;border-radius:var(--rs);cursor:pointer;transition:all .2s;background:var(--card2);">
<input type="radio" name="role" value="<?php echo e($role[0]); ?>" <?php echo e(old('role',$user->role)===$role[0]?'checked':''); ?> style="accent-color:var(--y);" onchange="document.querySelectorAll('.rol').forEach(l=>l.closest('label').style.borderColor='var(--bd)');this.closest('label').style.borderColor='var(--y)'">
<i class="<?php echo e($role[2]); ?>" style="color:var(--y);font-size:14px;"></i><span class="fws rol" style="color:#fff;font-size:13px;"><?php echo e($role[1]); ?></span>
</label>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div></div>

<div style="display:flex;gap:10px;">
<button type="submit" class="btn btn-ac"><i class="fas fa-save"></i> Save Changes</button>
<a href="<?php echo e(route('superadmin.users.index')); ?>" class="btn btn-o">Cancel</a>
<?php if($user->role!=='superadmin'): ?>
<form method="POST" action="<?php echo e(route('superadmin.users.toggle',$user->id)); ?>" style="margin-left:auto;"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
<button class="btn <?php echo e($user->is_active?'btn-d':'btn-s'); ?>" onclick="return confirm('<?php echo e($user->is_active?'Deactivate':'Activate'); ?> this user?')"><i class="fas fa-<?php echo e($user->is_active?'ban':'check'); ?>"></i> <?php echo e($user->is_active?'Deactivate':'Activate'); ?></button>
</form>
<?php endif; ?>
</div>
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('superadmin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/superadmin/users/edit.blade.php ENDPATH**/ ?>