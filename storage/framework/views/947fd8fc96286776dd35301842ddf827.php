
<?php $__env->startSection('title', 'Discipline Records'); ?>
<?php $__env->startSection('page-title', 'Student Discipline Records'); ?>
<?php $__env->startSection('content'); ?>
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;" aria-label="Discipline records filters">
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="fc" placeholder="Search (EDP, name, case no, description...)" style="padding-left:30px;width:280px;">
        </div>

        <select name="offense_category" class="fc" style="width:210px;" aria-label="Offense category">
            <option value="">All Offense Categories</option>
            <option value="Major" <?php echo e(request('offense_category')==='Major'?'selected':''); ?>>Major</option>
            <option value="Minor" <?php echo e(request('offense_category')==='Minor'?'selected':''); ?>>Minor</option>
        </select>

        <select name="sort" class="fc" style="width:185px;" aria-label="Sort by">
            <option value="date_filed" <?php echo e(request('sort')==='date_filed'?'selected':''); ?>>Date Filed</option>
            <option value="incident_date" <?php echo e(request('sort')==='incident_date'?'selected':''); ?>>Incident Date</option>
        </select>

        <select name="direction" class="fc" style="width:135px;" aria-label="Sort direction">
            <option value="desc" <?php echo e(request('direction')==='desc'?'selected':''); ?>>Newest</option>
            <option value="asc" <?php echo e(request('direction')==='asc'?'selected':''); ?>>Oldest</option>
        </select>

        <button type="submit" class="btn btn-p btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="<?php echo e(route('admin.discipline.index')); ?>" class="btn btn-o btn-sm">Clear</a>
    </form>

    <a href="<?php echo e(route('admin.discipline.create')); ?>" class="btn btn-p btn-sm"><i class="fas fa-file-medical"></i> Add Record</a>
</div>

<div class="card an">
    <div class="ch">
        <i class="fas fa-gavel" style="color:var(--gm);"></i>
        <h2>Discipline Cases</h2>
        <span class="badge b-p" style="margin-left:6px;"><?php echo e($cases->total()); ?></span>
    </div>

    <div class="tw">
        <table>
            <thead>
                <tr>
                    <th>EDP</th>
                    <th>NAME</th>
                    <th>DEPARTMENT</th>
                    <th>Category of Offense</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Contact No.</th>
                    <th>Name of Parent/Guardian</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $s = $c->student;
                    $department = $s?->course;
                    $contact = $s?->contact_number;
                    $guardian = $s?->guardian_name;
                ?>
                <tr>
                    <td class="mono" style="font-size:12px;"><?php echo e($s?->student_id ?? '—'); ?></td>
                    <td style="max-width:220px;">
                        <div style="display:flex;align-items:center;gap:7px;">
                            <div class="av av-s"><?php echo e(strtoupper(substr($s?->first_name ?? 'X',0,1))); ?></div>
                            <div>
                                <div class="fws" style="font-size:13px;"><?php echo e($s?->full_name ?? '—'); ?></div>
                                <div class="mono" style="font-size:11px;color:var(--tm);"><?php echo e($c->case_number); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:12px;color:var(--tm);"><?php echo e($department ?: '—'); ?></td>
                    <td>
                        <span class="badge <?php echo e($c->violation_type==='Major' ? 'b-d' : 'b-w'); ?>"><?php echo e($c->violation_type); ?></span>
                    </td>
                    <td style="max-width:260px;"><?php echo e(Str::limit($c->description ?? '—', 60)); ?></td>
                    <td class="mono" style="font-size:12px;"><?php echo e($c->incident_date ? $c->incident_date->format('Y-m-d') : '—'); ?></td>
                    <td><?php echo e($contact ?: '—'); ?></td>
                    <td><?php echo e($guardian ?: '—'); ?></td>
                    <td>
                        <div style="display:flex;gap:5px;">
                            <a href="#" class="btn btn-o btn-sm btn-ic" title="View (not part of this update)"><i class="fas fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" style="text-align:center;padding:18px;color:var(--tm);">No discipline records found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($cases->hasPages()): ?>
        <div style="padding:13px 18px;border-top:1px solid var(--bd);"><?php echo e($cases->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/discipline/index.blade.php ENDPATH**/ ?>