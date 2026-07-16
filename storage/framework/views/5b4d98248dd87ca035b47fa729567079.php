
<?php $__env->startSection('title','Import Students'); ?>
<?php $__env->startSection('page-title','Import Students'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:760px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <a href="<?php echo e(route('admin.students.export')); ?>" class="btn btn-o btn-sm" title="Export current students (JSON)"><i class="fas fa-download"></i> Export (JSON)</a>
    </div>

    <div class="card an mb3">
        <div class="ch"><i class="fas fa-file-upload" style="color:var(--gm);"></i><h2>Upload CSV</h2></div>
        <div class="cb">
            <p class="tm" style="margin-bottom:14px;">
                Upload a <span class="fwb">CSV</span> file. Your CSV header must contain these fields:
                <span class="mono">student_id</span>, <span class="mono">first_name</span>, <span class="mono">middle_name</span>, <span class="mono">last_name</span>.
            </p>


            <form method="POST" action="<?php echo e(route('admin.students.import')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="fg">
                    <label class="fl">CSV File <span style="color:var(--danger);">*</span></label>
                    <input type="file" name="file" class="fc" accept=".csv,text/csv" required>
                    <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="alert al-d an" style="margin-top:10px;"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:14px;">
                    <button type="submit" class="btn btn-p"><i class="fas fa-upload"></i> Import</button>
                    <button type="button" class="btn btn-o" onclick="downloadSample()"><i class="fas fa-table"></i> Download Sample</button>
                </div>

                <div class="mt3">
                    <div class="alert al-i an" style="margin-bottom:0;">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <div class="fwb" style="margin-bottom:2px;">Auto-login credentials</div>
                            <div style="font-size:12px;color:var(--tm);">
                                Email: <span class="mono">studentFirstname.studentLastname@sccpag.edu.ph</span><br>
                                Password: <span class="mono">scc<student_id></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if(!empty($summary)): ?>
        <div class="card an">
            <div class="ch"><i class="fas fa-list-check" style="color:var(--gm);"></i><h2>Import Summary</h2><div class="ch-acts"><span class="badge b-p"><?php echo e($summary['total_rows'] ?? 0); ?></span></div></div>
            <div class="cb">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;">
                    <div class="sc"><div class="si g"><?php echo e($summary['created_users'] ?? 0); ?></div><div><div class="lbl">Users Created</div><div class="val"><?php echo e($summary['created_users'] ?? 0); ?></div></div></div>
                    <div class="sc"><div class="si y"><?php echo e($summary['updated_students'] ?? 0); ?></div><div><div class="lbl">Students Updated</div><div class="val"><?php echo e($summary['updated_students'] ?? 0); ?></div></div></div>
                    <div class="sc"><div class="si r"><?php echo e($summary['failed_rows'] ?? 0); ?></div><div><div class="lbl">Failed Rows</div><div class="val"><?php echo e($summary['failed_rows'] ?? 0); ?></div></div></div>
                </div>

                <?php if(!empty($summary['errors']) && is_array($summary['errors']) && count($summary['errors'])>0): ?>
                    <div class="mt3">
                        <div class="fl">Errors</div>
                        <div class="tw">
                            <table><thead><tr><th>#</th><th>Row</th><th>Message</th></tr></thead>
                                <tbody>
                                <?php $__currentLoopData = $summary['errors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="mono" style="font-size:12px;"><?php echo e($idx+1); ?></td>
                                        <td class="mono" style="font-size:12px;"><?php echo e($err['row'] ?? '—'); ?></td>
                                        <td style="color:var(--tm);"><?php echo e($err['message'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;margin-top:16px;">
                    <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-p"><i class="fas fa-users"></i> View Students</a>
                    <a href="<?php echo e(route('admin.students.create')); ?>" class="btn btn-o"><i class="fas fa-user-plus"></i> Add Student Manually</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function downloadSample(){
  const headers = [
    'student_id','first_name','middle_name','last_name'
  ];
  const rows = [
    ['2024-0001','John','K','Smith']
  ];

  let csv = headers.join(',') + '\n' + rows.map(r=>r.map(v=>{
    v = String(v ?? '');
    if(v.includes(',')||v.includes('"')||v.includes('\n')) v = '"'+v.replace(/"/g,'""')+'"';
    return v;
  }).join(',')).join('\n');

  const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'students_import_sample.csv';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/students/import.blade.php ENDPATH**/ ?>