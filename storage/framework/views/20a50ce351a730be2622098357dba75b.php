<?php $__env->startSection('title','PH Scholarship Sync'); ?>
<?php $__env->startSection('page-title','PH Scholarship Sync'); ?>
<?php $__env->startSection('page-sub','Automatically fetch and sync Philippine scholarship programs from official sources'); ?>

<?php $__env->startSection('ai-bar'); ?>
<div class="ai-bar">
    <div class="ai-bar-label"><div class="ai-dot"></div>AI Sync Engine Active</div>
    <div class="ai-bar-stats">
        <div class="ai-stat">Total Found: <strong><?php echo e($stats['total']); ?></strong></div>
        <div class="ai-stat">New: <strong><?php echo e($stats['new']); ?></strong></div>
        <div class="ai-stat">Updated: <strong><?php echo e($stats['updated']); ?></strong></div>
        <div class="ai-stat">Imported: <strong><?php echo e($stats['imported']); ?></strong></div>
        <div class="ai-stat">Last Sync: <strong><?php echo e($stats['last_run'] ? \Carbon\Carbon::parse($stats['last_run'])->diffForHumans() : 'Never'); ?></strong></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<div style="background:linear-gradient(135deg,#0d3318,#1a3a6b,#0d3344);border-radius:var(--r);padding:22px 26px;margin-bottom:20px;display:flex;align-items:center;gap:18px;border:1px solid rgba(240,192,32,.2);" class="an">
    <div style="width:60px;height:60px;background:linear-gradient(135deg,#0038a8,#ce1126);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:26px;color:#fff;flex-shrink:0;box-shadow:0 6px 20px rgba(0,56,168,.4);">
        <i class="fas fa-flag"></i>
    </div>
    <div style="flex:1;">
        <div style="font-family:'Sora',sans-serif;font-size:20px;font-weight:800;color:#fff;">PH Scholarship Sync</div>
        <div style="font-size:13px;color:rgba(255,255,255,.7);margin-top:3px;">Automatically fetches and syncs <strong style="color:var(--y);">Philippine-only</strong> scholarship programs from <?php echo e(count($sources)); ?> official government and private sources — CHED, DOST-SEI, DSWD, PVAO, SM Foundation, and more.</div>
    </div>
    <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;">
        <form method="POST" action="<?php echo e(route('admin.scraper.run')); ?>"><?php echo csrf_field(); ?>
            <button class="btn btn-ac" onclick="return confirm('Run full PH Scholarship Sync on all <?php echo e(count($sources)); ?> sources? This may take 1–2 minutes.')">
                <i class="fas fa-sync-alt"></i> Sync All PH Sources
            </button>
        </form>
        <?php if($stats['high_conf'] > 0): ?>
        <form method="POST" action="<?php echo e(route('admin.scraper.import-all')); ?>"><?php echo csrf_field(); ?>
            <button class="btn btn-s btn-sm" style="width:100%;justify-content:center;">
                <i class="fas fa-download"></i> Auto-Import <?php echo e($stats['high_conf']); ?> High-Confidence
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>


<div class="sg" style="grid-template-columns:repeat(5,1fr);margin-bottom:20px;">
    <div class="sc an d1"><div class="si" style="background:#e8eef8;color:#0038a8;"><i class="fas fa-flag"></i></div><div class="sv"><div class="lbl">PH Sources</div><div class="val"><?php echo e(count($sources)); ?></div><div class="chg">Official websites</div></div></div>
    <div class="sc an d2"><div class="si g"><i class="fas fa-star"></i></div><div class="sv"><div class="lbl">New Found</div><div class="val"><?php echo e($stats['new']); ?></div><div class="chg">Not yet imported</div></div></div>
    <div class="sc an d3"><div class="si o"><i class="fas fa-sync"></i></div><div class="sv"><div class="lbl">Updated</div><div class="val"><?php echo e($stats['updated']); ?></div><div class="chg">Content changed</div></div></div>
    <div class="sc an d4"><div class="si dg"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Imported</div><div class="val"><?php echo e($stats['imported']); ?></div><div class="chg">In your system</div></div></div>
    <div class="sc an d5"><div class="si t"><i class="fas fa-robot"></i></div><div class="sv"><div class="lbl">High Confidence</div><div class="val"><?php echo e($stats['high_conf']); ?></div><div class="chg">AI score ≥ 75%</div></div></div>
</div>


<div class="card an mb3">
    <div class="ch">
        <i class="fas fa-globe" style="color:var(--yd);"></i>
        <h2>Monitored Philippine Scholarship Sources</h2>
        <div class="ch-acts"><span style="font-size:12px;color:var(--tm);"><i class="fas fa-flag" style="color:#0038a8;margin-right:4px;"></i>Philippines only</span></div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;padding:18px;">
        <?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:14px;display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="si <?php echo e($src['type']==='Government'?'g':'y'); ?>" style="width:36px;height:36px;border-radius:9px;font-size:13px;flex-shrink:0;">
                    <i class="fas fa-<?php echo e($src['type']==='Government'?'university':'building'); ?>"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="fws" style="font-size:13px;"><?php echo e($src['name']); ?></div>
                    <div class="tm" style="font-size:11px;"><?php echo e($src['agency']); ?></div>
                </div>
                <span class="badge <?php echo e($src['type']==='Government'?'b-p':'b-s'); ?>" style="font-size:10px;flex-shrink:0;"><?php echo e($src['type']); ?></span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:7px;">
                <a href="<?php echo e($src['url']); ?>" target="_blank" class="btn btn-o btn-sm" style="font-size:11px;flex:1;justify-content:center;"><i class="fas fa-external-link-alt"></i> Visit Site</a>
                <form method="POST" action="<?php echo e(route('admin.scraper.run-source')); ?>"><?php echo csrf_field(); ?>
                    <input type="hidden" name="source_index" value="<?php echo e($idx); ?>">
                    <button class="btn btn-ai btn-sm" style="font-size:11px;"><i class="fas fa-sync-alt"></i> Sync</button>
                </form>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<div class="card an">
    <div class="ch">
        <i class="fas fa-list" style="color:var(--gm);"></i>
        <h2>Synced Philippine Scholarships</h2>
        <span class="badge b-p" style="margin-left:6px;"><?php echo e($scraped->total()); ?> found</span>
        <div class="ch-acts">
            <form method="GET" style="display:flex;gap:8px;">
                <select name="status" class="fc" style="width:130px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="new"     <?php echo e(request('status')==='new'?'selected':''); ?>>New</option>
                    <option value="updated" <?php echo e(request('status')==='updated'?'selected':''); ?>>Updated</option>
                </select>
                <select name="type" class="fc" style="width:130px;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="Government"    <?php echo e(request('type')==='Government'?'selected':''); ?>>Government</option>
                    <option value="Private"       <?php echo e(request('type')==='Private'?'selected':''); ?>>Private</option>
                    <option value="Institutional" <?php echo e(request('type')==='Institutional'?'selected':''); ?>>Institutional</option>
                </select>
                <select name="imported" class="fc" style="width:130px;" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="0" <?php echo e(request('imported')==='0'?'selected':''); ?>>Not Imported</option>
                    <option value="1" <?php echo e(request('imported')==='1'?'selected':''); ?>>Imported</option>
                </select>
            </form>
        </div>
    </div>
    <div class="tw">
        <table>
            <thead>
                <tr>
                    <th>Scholarship Name</th>
                    <th>Source / Agency</th>
                    <th>Type</th>
                    <th>Benefits</th>
                    <th>Deadline</th>
                    <th>Slots</th>
                    <th>AI Confidence</th>
                    <th>Status</th>
                    <th>Last Synced</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $scraped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $conf    = $s->ai_confidence ?? 0;
                    $confCls = $conf >= 75 ? 'ash' : ($conf >= 50 ? 'asm' : 'asl');
                    $confCol = $conf >= 75 ? 'var(--gm)' : ($conf >= 50 ? 'var(--warn)' : 'var(--danger)');
                ?>
                <tr>
                    <td>
                        <div class="fws" style="font-size:13px;"><?php echo e(Str::limit($s->name, 45)); ?></div>
                        <a href="<?php echo e($s->source_url); ?>" target="_blank" style="font-size:11px;color:var(--info);">
                            <i class="fas fa-external-link-alt" style="margin-right:3px;"></i>View Source
                        </a>
                    </td>
                    <td style="font-size:12px;color:var(--tm);"><?php echo e(Str::limit($s->source_agency ?? '—', 30)); ?></td>
                    <td>
                        <span class="badge <?php echo e($s->source_type==='Government'?'b-p':($s->source_type==='Private'?'b-s':'b-i')); ?>" style="font-size:10px;">
                            <?php echo e($s->source_type ?? '—'); ?>

                        </span>
                    </td>
                    <td style="font-size:12px;max-width:150px;">
                        <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--tm);" title="<?php echo e($s->benefits); ?>">
                            <?php echo e(Str::limit($s->benefits ?? '—', 40)); ?>

                        </div>
                    </td>
                    <td class="mono" style="font-size:11px;color:var(--tm);">
                        <?php echo e($s->deadline ? $s->deadline->format('M d, Y') : '—'); ?>

                    </td>
                    <td class="mono fwb" style="color:var(--gm);"><?php echo e($s->slots ?? '—'); ?></td>
                    <td style="min-width:100px;">
                        <div style="display:flex;align-items:center;gap:5px;">
                            <div style="flex:1;"><div class="asb"><div class="asf <?php echo e($confCls); ?>" style="width:<?php echo e($conf); ?>%;"></div></div></div>
                            <span class="mono fwb" style="font-size:11px;color:<?php echo e($confCol); ?>;"><?php echo e($conf); ?>%</span>
                        </div>
                    </td>
                    <td>
                        <?php if($s->imported): ?>
                            <span class="badge b-s" style="font-size:10px;"><i class="fas fa-check" style="margin-right:3px;"></i>Imported</span>
                        <?php elseif($s->status === 'new'): ?>
                            <span class="badge b-y" style="font-size:10px;"><i class="fas fa-star" style="margin-right:3px;"></i>New</span>
                        <?php elseif($s->status === 'updated'): ?>
                            <span class="badge b-w" style="font-size:10px;"><i class="fas fa-sync" style="margin-right:3px;"></i>Updated</span>
                        <?php else: ?>
                            <span class="badge b-gray" style="font-size:10px;"><?php echo e(ucfirst($s->status)); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="mono" style="font-size:11px;color:var(--tm);">
                        <?php echo e($s->last_scraped_at?->diffForHumans() ?? '—'); ?>

                    </td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <?php if(!$s->imported): ?>
                            <form method="POST" action="<?php echo e(route('admin.scraper.import', $s->id)); ?>"><?php echo csrf_field(); ?>
                                <button class="btn btn-p btn-sm btn-ic" title="Import to Programs"><i class="fas fa-download"></i></button>
                            </form>
                            <form method="POST" action="<?php echo e(route('admin.scraper.dismiss', $s->id)); ?>"><?php echo csrf_field(); ?>
                                <button class="btn btn-o btn-sm btn-ic" title="Dismiss"><i class="fas fa-times"></i></button>
                            </form>
                            <?php else: ?>
                            <a href="<?php echo e(route('admin.scholarships.index')); ?>" class="btn btn-o btn-sm btn-ic" title="View in Programs"><i class="fas fa-eye"></i></a>
                            <?php endif; ?>
                            <form method="POST" action="<?php echo e(route('admin.scraper.destroy', $s->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-d btn-sm btn-ic" title="Delete" onclick="return confirm('Delete this entry?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="10" style="text-align:center;padding:32px;color:var(--tm);">
                        <div style="font-size:36px;margin-bottom:12px;opacity:.4;"><i class="fas fa-flag"></i></div>
                        <div class="fws" style="font-size:15px;margin-bottom:6px;">No Philippine scholarships synced yet</div>
                        <div style="font-size:13px;margin-bottom:14px;">Click "Sync All PH Sources" to fetch scholarships from <?php echo e(count($sources)); ?> official Philippine websites</div>
                        <form method="POST" action="<?php echo e(route('admin.scraper.run')); ?>"><?php echo csrf_field(); ?>
                            <button class="btn btn-ac"><i class="fas fa-sync-alt"></i> Sync Now</button>
                        </form>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($scraped->hasPages()): ?>
    <div style="padding:13px 18px;border-top:1px solid var(--bd);"><?php echo e($scraped->links()); ?></div>
    <?php endif; ?>
</div>


<div class="card an mt3">
    <div class="ch"><i class="fas fa-info-circle" style="color:var(--yd);"></i><h2>How PH Scholarship Sync Works</h2></div>
    <div class="cb">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
            <?php $__currentLoopData = [
                ['1','fas fa-flag','Philippine Sources Only','Only monitors official Philippine scholarship websites — CHED, DOST-SEI, DSWD, PVAO, SM Foundation, and more.','y'],
                ['2','fas fa-robot','AI Reads Content','AI reads each page and extracts scholarship names, benefits, requirements, and deadlines automatically.','g'],
                ['3','fas fa-check-circle','Confidence Score','Each result gets an AI confidence score (0–100) based on how reliably the data was extracted.','t'],
                ['4','fas fa-sync','Detects Updates','If a scholarship deadline, slot count, or benefit changes on the source site, it is flagged as Updated automatically.','o'],
                ['5','fas fa-download','One-Click Import','Admin reviews results and clicks Import to add it to Scholarship Programs with AI criteria auto-configured.','g'],
                ['6','fas fa-clock','Auto Schedule','Set up daily auto-sync via the artisan command: php artisan scamp:scrape --auto-import','y'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:13px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:7px;">
                    <div style="width:24px;height:24px;border-radius:50%;background:<?php echo e($step[4]==='y'?'var(--y)':($step[4]==='g'?'var(--gm)':($step[4]==='t'?'var(--info)':'var(--warn)'))); ?>;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#0d3318;flex-shrink:0;"><?php echo e($step[0]); ?></div>
                    <div class="fws" style="font-size:13px;"><?php echo e($step[2]); ?></div>
                </div>
                <div style="font-size:12px;color:var(--tm);line-height:1.5;"><?php echo e($step[3]); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="alert al-ai mt3" style="margin-bottom:0;font-size:12px;">
            <i class="fas fa-key"></i>
            <span><strong>Setup Required:</strong> Add your Anthropic API key to <code>.env</code> as <code>ANTHROPIC_API_KEY=sk-ant-...</code> for full AI extraction. Without it, the sync uses fallback placeholder data.</span>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/scholarships/scraper.blade.php ENDPATH**/ ?>