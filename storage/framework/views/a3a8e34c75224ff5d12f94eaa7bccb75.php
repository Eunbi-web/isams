
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
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">EDP <span style="color:var(--danger)">*</span></label>
                        <div class="d-flex gap-2" style="align-items:center;">
                            <input type="text" id="edpInput" class="form-control" name="edp" placeholder="Type EDP" required>
                            <button type="button" class="btn btn-o" id="lookupBtn">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">NAME <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="nameInput" class="fc" name="name" value="" placeholder="Full name" />
                    </div>

                    <div class="form-group">
                        <label class="form-label">DEPARTMENT</label>
                        <input type="text" id="deptInput" class="fc" name="department" value="" />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact No. (parent/guardian)</label>
                        <input type="text" id="contactInput" class="fc" name="contact_number" value="" />
                    </div>

                    <div class="form-group" style="grid-column:1 / -1;">
                        <label class="form-label">Name of Parent/Guardian</label>
                        <input type="text" id="guardianInput" class="fc" name="guardian_name" value="" />
                    </div>
                </div>

                <div class="alert al-i an" style="margin-top:14px;">
                    <i class="fas fa-info-circle"></i>
                    After searching by EDP, only the NAME field will be auto-filled. Department/Contact/Guardian are to be entered manually.
                </div>

                
                <input type="hidden" name="student_id" id="studentIdHidden" value="">
            </div>
        </div>

        <div class="card an mb2">
            <div class="ch">
                <i class="fas fa-clipboard" style="color:var(--gm);"></i>
                <h2>Discipline Case Details</h2>
            </div>

            <div class="cb">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Category of Offense (Major or Minor) <span style="color:var(--danger)">*</span></label>
                        <select name="offense_category" class="form-control" required>
                            <option value="Major">Major</option>
                            <option value="Minor">Minor</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date <span style="color:var(--danger)">*</span></label>
                        <input type="date" name="date" class="form-control" value="<?php echo e(old('date', date('Y-m-d'))); ?>" required>
                    </div>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="6" style="width:100%;" placeholder="Write the incident details..."><?php echo e(old('description')); ?></textarea>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-p"><i class="fas fa-file-medical"></i> Save Record</button>
            <a href="<?php echo e(route('admin.discipline.index')); ?>" class="btn btn-o"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>

<script>
    (function(){
        const edpInput = document.getElementById('edpInput');
        const nameInput = document.getElementById('nameInput');
        const lookupBtn = document.getElementById('lookupBtn');

        function setError(msg){
            alert(msg);
            nameInput.value = '';
        }

        async function lookup(){
            const edp = (edpInput.value || '').trim();
            if(!edp) {
                nameInput.value = '';
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

                if(!res.ok){
                    const msg = await res.json().catch(()=>({message:'Student not found'}));
                    setError(msg.message || 'Student not found');
                    return;
                }

                const data = await res.json();
                if(data.ok){
                    nameInput.value = data.name || '';
                }
            } catch(e){
                setError('Lookup error');
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
    })();
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/discipline/create.blade.php ENDPATH**/ ?>