<?php $__env->startSection('title', 'Add Discipline Record'); ?>
<?php $__env->startSection('page-title', 'File Student Discipline Record'); ?>
<?php $__env->startSection('page-subtitle', 'Input a discipline case in order, categorized, and filterable'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width:980px;">
    <form method="POST" action="<?php echo e(route('admin.discipline.store')); ?>">
        <?php echo csrf_field(); ?>

        <div class="card an mb2">
            <div class="ch">
                <i class="fas fa-gavel" style="color:var(--gm);"></i>
                <h2>Student Information</h2>
            </div>

            <div class="cb">
                <div class="g2">
                    <div class="fg">
                        <label class="fl">EDP <span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="text" id="edpInput" class="fc" name="edp" value="<?php echo e(old('edp')); ?>" placeholder="Type EDP number" autocomplete="off" required>
                            <button type="button" class="btn btn-o" id="lookupBtn">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                        <div id="edpStatus" style="font-size:11px;color:var(--tm);margin-top:4px;"></div>
                    </div>

                    <div class="fg">
                        <label class="fl">NAME <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="nameInput" class="fc" name="name" value="<?php echo e(old('name')); ?>" placeholder="Auto-filled from EDP" readonly />
                    </div>

                    <div class="fg">
                        <label class="fl">DEPARTMENT <span style="color:var(--danger)">*</span></label>
                        <select id="deptInput" class="fc" name="department" required>
                            <option value="" disabled <?php echo e(old('department') ? '' : 'selected'); ?>>Select department</option>
                            <?php $__currentLoopData = ['CTEAS','CBE','CCS','COC']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dept); ?>" <?php echo e(old('department')===$dept?'selected':''); ?>><?php echo e($dept); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="fg">
                        <label class="fl">Contact No. (parent/guardian)</label>
                        <input type="text" id="contactInput" class="fc" name="contact_number" value="<?php echo e(old('contact_number')); ?>" />
                    </div>

                    <div class="fg" style="grid-column:1 / -1;">
                        <label class="fl">Name of Parent/Guardian</label>
                        <input type="text" id="guardianInput" class="fc" name="guardian_name" value="<?php echo e(old('guardian_name')); ?>" />
                    </div>
                </div>

                <div class="alert al-i an" style="margin-top:14px;">
                    <i class="fas fa-info-circle"></i>
                    Type the student's EDP number and press Search (or Enter) — the Name, Department, Contact No. and Parent/Guardian are auto-filled automatically.
                </div>
            </div>
        </div>

        <div class="card an mb2">
            <div class="ch">
                <i class="fas fa-clipboard" style="color:var(--gm);"></i>
                <h2>Discipline Case Details</h2>
            </div>

            <div class="cb">
                <div class="g2">
                    <div class="fg">
                        <label class="fl">Category of Offense (Major or Minor) <span style="color:var(--danger)">*</span></label>
                        <select name="offense_category" class="fc" required>
                            <option value="Major" <?php echo e(old('offense_category')==='Major'?'selected':''); ?>>Major</option>
                            <option value="Minor" <?php echo e(old('offense_category')==='Minor'?'selected':''); ?>>Minor</option>
                        </select>
                    </div>

                    <div class="fg">
                        <label class="fl">Date <span style="color:var(--danger)">*</span></label>
                        <input type="date" name="date" class="fc" value="<?php echo e(old('date', date('Y-m-d'))); ?>" required>
                    </div>
                </div>

                <div class="fg" style="margin-top:12px;">
                    <label class="fl">Description</label>
                    <textarea name="description" class="fc" rows="6" style="width:100%;" placeholder="Write the incident details..."><?php echo e(old('description')); ?></textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:8px;">
            <button type="submit" class="btn btn-p"><i class="fas fa-file-medical"></i> Save Record</button>
            <a href="<?php echo e(route('admin.discipline.index')); ?>" class="btn btn-o"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>

<script>
    (function(){
        const edpInput = document.getElementById('edpInput');
        const edpStatus = document.getElementById('edpStatus');
        const nameInput = document.getElementById('nameInput');
        const deptInput = document.getElementById('deptInput');
        const contactInput = document.getElementById('contactInput');
        const guardianInput = document.getElementById('guardianInput');
        const lookupBtn = document.getElementById('lookupBtn');

        function setStatus(msg, isError){
            edpStatus.textContent = msg || '';
            edpStatus.style.color = isError ? 'var(--danger)' : 'var(--gm)';
        }

        function clearFields(){
            nameInput.value = '';
            deptInput.value = '';
            contactInput.value = '';
            guardianInput.value = '';
        }

        function setFields(data){
            nameInput.value = data.name || '';
            // Auto-select the department when the student's record has one
            if (data.department) {
                deptInput.value = data.department;
            } else if (data.course) {
                // Try to map the course to a known department
                const c = String(data.course).toUpperCase();
                const map = [
                    ['CTEAS', ['EDUC','BEED','BSED','TEACH','CTE']],
                    ['CBE',   ['BSBA','BUS','ACCOUNT','ENTREP','MARKET','FIN','CBE','HRM']],
                    ['CCS',   ['IT','CS','IS','COMPUT','CCS','BSIT','BSCS','BSIS']],
                    ['COC',   ['CRIM','COC']]
                ];
                let matched = '';
                for (const [dept, keys] of map) {
                    if (keys.some(k => c.includes(k))) { matched = dept; break; }
                }
                if (matched) { deptInput.value = matched; }
            }
            contactInput.value = data.contact_number || '';
            guardianInput.value = data.guardian_name || '';
        }

        async function lookup(){
            const edp = (edpInput.value || '').trim();
            if(!edp) {
                clearFields();
                setStatus('');
                return;
            }

            lookupBtn.disabled = true;
            lookupBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';

            try {
                const res = await fetch('<?php echo e(route('admin.discipline.lookup-edp')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ edp })
                });

                const data = await res.json().catch(()=>({ok:false, message:'Student not found'}));

                if(!res.ok || !data.ok){
                    clearFields();
                    setStatus(data.message || 'Student not found', true);
                    return;
                }

                setFields(data);
                edpInput.value = data.edp || edp;
                setStatus('Student found: ' + (data.name || ''), false);
            } catch(e){
                clearFields();
                setStatus('Lookup error — please try again', true);
            } finally {
                lookupBtn.disabled = false;
                lookupBtn.innerHTML = '<i class="fas fa-search"></i> Search';
            }
        }

        lookupBtn.addEventListener('click', lookup);
        edpInput.addEventListener('keydown', (e)=>{
            if(e.key === 'Enter'){
                e.preventDefault();
                lookup();
            }
        });
        // Auto-lookup once enough digits are typed
        edpInput.addEventListener('input', ()=>{
            const v = edpInput.value.trim();
            if(v.length >= 6){
                lookup();
            }
        });
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/discipline/create.blade.php ENDPATH**/ ?>