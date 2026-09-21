<?php $__env->startSection('title','Students'); ?>
<?php $__env->startSection('page-title','Student Records'); ?>
<?php $__env->startSection('content'); ?>
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <div style="position:relative;">
        <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;"></i>
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="fc" placeholder="Search students..." style="padding-left:30px;width:200px;">
    </div>
    <select name="course" class="fc" style="width:170px;">
        <option value="">All Courses</option>
        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($c); ?>" <?php echo e(request('course')===$c?'selected':''); ?>><?php echo e(Str::limit($c,26)); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="year_level" class="fc" style="width:120px;">
        <option value="">All Years</option>
        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($y); ?>" <?php echo e(request('year_level')===$y?'selected':''); ?>><?php echo e($y); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="enrollment_type" class="fc" style="width:140px;">
        <option value="">All Enrollment</option>
        <option value="Regular" <?php echo e(request('enrollment_type')==='Regular'?'selected':''); ?>>Regular</option>
        <option value="Irregular" <?php echo e(request('enrollment_type')==='Irregular'?'selected':''); ?>>Irregular</option>
    </select>
    <select name="status" class="fc" style="width:120px;">
        <option value="">All Status</option>
        <option value="Active" <?php echo e(request('status')==='Active'?'selected':''); ?>>Active</option>
        <option value="Inactive" <?php echo e(request('status')==='Inactive'?'selected':''); ?>>Inactive</option>
    </select>
    <button type="submit" class="btn btn-p btn-sm"><i class="fas fa-filter"></i> Filter</button>
    <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-o btn-sm">Clear</a>
</form>

<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <a href="<?php echo e(route('admin.students.import-form')); ?>" class="btn btn-p btn-sm"><i class="fas fa-file-import"></i> Import Students</a>
    <a href="<?php echo e(route('admin.students.create')); ?>" class="btn btn-p btn-sm"><i class="fas fa-user-plus"></i> Add Student</a>
</div>
</div>


<div class="card an"><div class="ch"><i class="fas fa-users" style="color:var(--gm);"></i><h2>Students</h2><span class="badge b-p" style="margin-left:6px;"><?php echo e($students->total()); ?></span></div>
<div class="tw"><table><thead><tr><th>Student</th><th>Student ID</th><th>Course</th><th>Year</th><th>GWA</th><th>Enrollment</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<tr>

<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s"><?php echo e(strtoupper(substr($s->first_name,0,1))); ?></div><div class="fws" style="font-size:13px;"><?php echo e($s->full_name); ?></div></div></td>
<td class="mono" style="font-size:12px;"><?php echo e($s->student_id); ?></td>
<td style="font-size:12px;color:var(--tm);"><?php echo e(Str::limit($s->course,25)); ?></td>
<td style="font-size:12px;"><?php echo e($s->year_level); ?></td>
<td class="mono fwb" style="color:<?php echo e((float)($s->gwa??0)<=1.75?'var(--gm)':((float)($s->gwa??0)<=2.25?'var(--warn)':'var(--danger)')); ?>"><?php echo e(number_format($s->gwa??0,2)); ?></td>
<td><span class="badge <?php echo e($s->enrollment_type==='Regular'?'b-p':'b-w'); ?>" style="font-size:10px;"><?php echo e($s->enrollment_type); ?></span></td>
<td><span class="badge <?php echo e($s->status==='Active'?'b-s':'b-d'); ?>"><?php echo e($s->status); ?></span></td>
<td><div style="display:flex;gap:5px;"><a href="<?php echo e(route('admin.students.show',$s->id)); ?>" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a><a href="<?php echo e(route('admin.students.edit',$s->id)); ?>" class="btn btn-o btn-sm btn-ic"><i class="fas fa-edit"></i></a></div></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<tr><td colspan="8" style="text-align:center;padding:18px;color:var(--tm);">No students found.</td></tr>
<?php endif; ?>
</tbody></table></div>
<?php if($students->hasPages()): ?><div style="padding:13px 18px;border-top:1px solid var(--bd);"><?php echo e($students->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/students/index.blade.php ENDPATH**/ ?>