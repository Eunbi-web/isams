<?php $__env->startSection('title','Add Student'); ?>
<?php $__env->startSection('page-title','Add Student'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width:700px;">
    <form method="POST" action="<?php echo e(route('admin.students.store')); ?>"><?php echo csrf_field(); ?>
        <div class="card an mb3">
            <div class="ch"><i class="fas fa-user-graduate" style="color:var(--gm);"></i><h2>Student Information</h2></div>
            <div class="cb">

                
                <div class="card an" style="border-style:dashed;border-width:1px;margin-bottom:14px;">
                    <div class="ch" style="border-bottom:1px dashed var(--bd);">
                        <i class="fas fa-search" style="color:var(--gm);"></i>
                        <h2 style="margin:0;font-size:14px;">Find from CSV (Import Source)</h2>
                    </div>
                    <div class="cb" style="padding-top:12px;">
                        <div class="g2" style="grid-template-columns: 1fr auto; align-items:end;">
                            <div class="fg" style="margin-bottom:0;">
                                <label class="fl">Search by Name or EDP (Student ID)</label>
                                <input id="studentLookupQ" type="text" class="fc" placeholder="e.g. Juan Dela Cruz or 2024-0001">
                            </div>
                            <div style="display:flex;gap:10px;justify-content:flex-end;">
                                <button type="button" class="btn btn-p" onclick="lookupStudent()"><i class="fas fa-search"></i> Lookup</button>
                            </div>
                        </div>
                        <div id="studentLookupMsg" class="alert" style="display:none;margin-top:14px;"></div>
                    </div>
                </div>

                <div class="g2">
                    <div class="fg"><label class="fl">First Name <span style="color:var(--danger);">*</span></label><input id="studentFirstName" type="text" name="first_name" class="fc" required></div>
                    <div class="fg"><label class="fl">Last Name <span style="color:var(--danger);">*</span></label><input id="studentLastName" type="text" name="last_name" class="fc" required></div>
                </div>

                <div class="g2">
                    <div class="fg"><label class="fl">Student ID <span style="color:var(--danger);">*</span></label><input id="studentStudentId" type="text" name="student_id" class="fc mono" required placeholder="2024-0001"></div>
                    <div class="fg"><label class="fl">GWA</label><input type="number" name="gwa" class="fc mono" step="0.01" min="1" max="5" placeholder="1.75"></div>
                </div>

                <div class="g2">
                    <div class="fg"><label class="fl">Course <span style="color:var(--danger);">*</span></label><select name="course" class="fc" required><option value="">Select course</option><?php $__currentLoopData = ['BS Computer Science','BS Information Technology','BS Engineering','BS Education','BS Nursing','BS Business Administration','AB Communication','BS Psychology','BS Accountancy','BS Tourism']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c); ?>"><?php echo e($c); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div class="fg"><label class="fl">Year Level <span style="color:var(--danger);">*</span></label><select name="year_level" class="fc" required><option>1st Year</option><option>2nd Year</option><option>3rd Year</option><option>4th Year</option></select></div>
                </div>

                <div class="g2">
                    <div class="fg"><label class="fl">Enrollment Type</label><select name="enrollment_type" class="fc"><option value="Regular">Regular</option><option value="Irregular">Irregular</option></select></div>
                    <div class="fg"><label class="fl">Contact Number</label><input type="text" name="contact_number" class="fc" placeholder="0917XXXXXXX"></div>
                </div>

                <div class="fg"><label class="fl">Email Address</label><input type="email" name="email" class="fc" placeholder="student@email.com"></div>

            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-p"><i class="fas fa-save"></i> Save Student</button>
            <a href="<?php echo e(route('admin.students.index')); ?>" class="btn btn-o">Cancel</a>
        </div>
    </form>
</div>

<script>
function setLookupMsg(type, text){
    const el=document.getElementById('studentLookupMsg');
    el.style.display='flex';
    el.classList.remove('al-s','al-d','al-w','al-i','al-ai');
    if(type==='success') el.classList.add('al-s');
    else el.classList.add('al-d');
    el.innerHTML='<i class="fas '+(type==='success'?'fa-check-circle':'fa-exclamation-circle')+'"></i> '+text;
}

function lookupStudent(){
    const q=(document.getElementById('studentLookupQ').value||'').trim();
    if(!q){
        document.getElementById('studentLookupMsg').style.display='none';
        setLookupMsg('error','Enter a name or student_id (EDP).');
        return;
    }

    const csrf=document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('<?php echo e(route('admin.students.lookup')); ?>',{
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json','Accept':'application/json'},
        body:JSON.stringify({ query:q })
    })
    .then(r=>r.json())
    .then(data=>{
        if(!data.ok){
            setLookupMsg('error', data.message || 'Student not found.');
            return;
        }
        // Fill only: first/last/student_id
        document.getElementById('studentFirstName').value = data.student.first_name || '';
        document.getElementById('studentLastName').value  = data.student.last_name || '';
        document.getElementById('studentStudentId').value = data.student.student_id || '';
        setLookupMsg('success', 'Student details filled from import list.');
    })
    .catch(()=>setLookupMsg('error','Lookup failed. Please try again.'));
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/students/create.blade.php ENDPATH**/ ?>