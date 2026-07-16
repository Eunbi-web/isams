<?php $__env->startSection('title','Reports'); ?>
<?php $__env->startSection('page-title','Reports & Analytics'); ?>
<?php $__env->startSection('page-sub','Download scholarship and student reports'); ?>
<?php $__env->startSection('content'); ?>
<?php
$eligible=\App\Models\ScholarshipApplication::where('ai_eligibility','Eligible')->count();
$total=\App\Models\ScholarshipApplication::count();
$approved=\App\Models\ScholarshipApplication::where('status','Approved')->count();
$students=\App\Models\Student::count();
?>
<div class="sg">
<div class="sc an"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Eligible</div><div class="val"><?php echo e($eligible); ?></div><div class="chg">AI-verified</div></div></div>
<div class="sc an"><div class="si y"><i class="fas fa-file-alt"></i></div><div class="sv"><div class="lbl">Total Apps</div><div class="val"><?php echo e($total); ?></div></div></div>
<div class="sc an"><div class="si dg"><i class="fas fa-award"></i></div><div class="sv"><div class="lbl">Approved</div><div class="val"><?php echo e($approved); ?></div></div></div>
<div class="sc an"><div class="si t"><i class="fas fa-users"></i></div><div class="sv"><div class="lbl">Students</div><div class="val"><?php echo e($students); ?></div></div></div>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;">
<?php $__currentLoopData = [['Scholarship Applications','All application records with AI scores','applications','fas fa-file-alt','g'],['Eligible Students','All AI-eligible applicants','eligible','fas fa-check-circle','dg'],['Scholarship Programs','Program details and slot counts','scholarships','fas fa-award','y'],['Student Records','Complete student academic records','students','fas fa-users','t']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="card an"><div class="ch"><div class="si <?php echo e($r[4]); ?>" style="width:36px;height:36px;font-size:14px;border-radius:9px;"><i class="<?php echo e($r[3]); ?>"></i></div><h2><?php echo e($r[0]); ?></h2></div>
<div class="cb"><div class="tm" style="font-size:13px;margin-bottom:14px;"><?php echo e($r[1]); ?></div>
<a href="<?php echo e(route('admin.reports.download',$r[2])); ?>" class="btn btn-p btn-sm"><i class="fas fa-download"></i> Download Report</a>
</div></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>