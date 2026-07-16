<?php $__env->startSection('title','AI Scholarship Passport'); ?>
<?php $__env->startSection('page-title','AI Scholarship Passport'); ?>
<?php $__env->startSection('page-sub','Your personalized scholarship readiness report — bring this when applying physically'); ?>
<?php $__env->startSection('content'); ?>

<?php
// Read Gemini key
$gk = '';
try { $gk = config('services.groq.key',''); } catch(\Exception $e){}
if(!$gk){ $gk = getenv('GROQ_API_KEY') ?: ''; }
if(!$gk && file_exists(base_path('.env'))){
    foreach(file(base_path('.env'),FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $ln){
        $ln=trim($ln); if(strpos($ln,'#')===0) continue;
        if(strpos($ln,'GROQ_API_KEY=')===0){ $gk=trim(substr($ln,15)," \t\"'"); break; }
    }
}
$hasKey = !empty(trim($gk));

$gwa        = (float)($student?->gwa ?? 5.0);
$isRegular  = strtolower($student?->enrollment_type ?? '') === 'regular';
$bracket    = $student?->income_bracket ?? '';
$bannerClass= $overallEligibility==='Eligible'?'eligible':($overallEligibility==='For Review'?'review':'not');
$bannerIcon = $overallEligibility==='Eligible'?'check-circle':($overallEligibility==='For Review'?'exclamation-circle':'times-circle');

// Sort scholarships by score
$mapped = [];
foreach($scholarships as $s){
    $d = $eligibilityMap[$s->id] ?? [];
    $mapped[] = [
        'id'          => $s->id,
        'name'        => $s->name,
        'type'        => $s->type ?? '',
        'benefits'    => \Illuminate\Support\Str::limit($s->benefits ?? '',80),
        'score'       => (int)($d['score'] ?? 0),
        'eligibility' => $d['eligibility'] ?? 'N/A',
        'tag'         => $d['tag'] ?? '',
        'reasoning'   => $d['reasoning'] ?? '',
        'applied'     => (bool)($d['applied'] ?? false),
        'status'      => $d['status'] ?? null,
        'source'      => $s->source ?? '',
        'slots'       => $s->slots ?? 0,
        'end_date'    => $s->end_date ? $s->end_date->format('M d, Y') : 'Open',
        'requirements'=> $s->requirements ?? '',
    ];
}
usort($mapped, function($a,$b){ return $b['score'] - $a['score']; });

$top = count($mapped) > 0 ? $mapped[0] : null;
$eligible_count = count(array_filter($mapped, fn($x) => $x['eligibility']==='Eligible'));
$review_count   = count(array_filter($mapped, fn($x) => $x['eligibility']==='For Review'));
?>

<input type="hidden" id="gk" value="<?php echo e(htmlspecialchars($gk, ENT_QUOTES,'UTF-8')); ?>">


<div class="elig-banner <?php echo e($bannerClass); ?> an" style="margin-bottom:20px;">
    <div class="elig-icon"><i class="fas fa-<?php echo e($bannerIcon); ?>" style="font-size:28px;color:#fff;"></i></div>
    <div style="flex:1;">
        <div class="elig-title">
            <?php if($overallEligibility==='Eligible'): ?> Scholarship-Ready! Visit the SAO Office to Apply.
            <?php elseif($overallEligibility==='For Review'): ?> Partially Qualified — See Improvement Tips Below
            <?php else: ?> Not Yet Qualified — Follow Your Action Plan Below
            <?php endif; ?>
        </div>
        <div class="elig-sub">AI evaluated your profile against <?php echo e(count($mapped)); ?> active scholarship programs. Applications are done physically at the Student Affairs Office.</div>
        <div style="font-size:12px;color:rgba(255,255,255,.65);margin-top:5px;">
            GWA: <strong style="color:#fff;"><?php echo e(number_format($gwa,2)); ?></strong> &nbsp;·&nbsp;
            <?php echo e($student?->enrollment_type ?? 'N/A'); ?> &nbsp;·&nbsp;
            <?php echo e(\Illuminate\Support\Str::limit($student?->course ?? 'N/A',30)); ?>

        </div>
    </div>
    <div class="chance-circle" style="flex-shrink:0;">
        <div class="chance-val"><?php echo e($overallScore); ?>%</div>
        <div class="chance-lbl">Best Score</div>
    </div>
</div>


<div class="sg" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
    <div class="sc an d1"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Eligible For</div><div class="val"><?php echo e($eligible_count); ?></div><div class="chg">scholarships</div></div></div>
    <div class="sc an d2"><div class="si o"><i class="fas fa-exclamation-circle"></i></div><div class="sv"><div class="lbl">For Review</div><div class="val"><?php echo e($review_count); ?></div><div class="chg">need verification</div></div></div>
    <div class="sc an d3"><div class="si y"><i class="fas fa-star"></i></div><div class="sv"><div class="lbl">Best AI Score</div><div class="val"><?php echo e($overallScore); ?>%</div><div class="chg">out of 100</div></div></div>
    <div class="sc an d4"><div class="si t"><i class="fas fa-id-card"></i></div><div class="sv"><div class="lbl">Passport</div><div class="val">Ready</div><div class="chg">to print</div></div></div>
</div>


<div class="card an mb3" id="passportCard" style="border:2px solid var(--g);">
    <div class="ch" style="background:linear-gradient(135deg,#0d3318,#1a6b2f);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:42px;height:42px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:19px;color:#0d3318;"><i class="fas fa-id-card"></i></div>
            <div>
                <h2 style="color:#fff;font-size:16px;">AI Scholarship Passport</h2>
                <div style="font-size:11px;color:rgba(255,255,255,.6);">Print and bring this to the Student Affairs Office when applying physically</div>
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="button" onclick="generatePassport()" id="genBtn" class="btn btn-ac" style="font-size:13px;">
                <i class="fas fa-magic"></i> Generate AI Passport
            </button>
            <button type="button" onclick="printPassport()" class="btn btn-o" style="border-color:rgba(255,255,255,.3);color:#fff;font-size:13px;">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
    <div style="padding:22px 24px;">

        
        <div id="passportStatic" style="background:#f8fdf8;border:2px dashed var(--gm);border-radius:12px;padding:20px;">
            <div style="display:flex;align-items:flex-start;gap:18px;flex-wrap:wrap;">
                <div style="width:70px;height:70px;background:linear-gradient(135deg,var(--g),var(--gm));border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:28px;color:var(--y);flex-shrink:0;">
                    <?php echo e(strtoupper(substr(auth()->user()->name ?? 'S', 0, 1))); ?>

                </div>
                <div style="flex:1;">
                    <div style="font-family:'Sora',sans-serif;font-size:18px;font-weight:800;color:var(--g);"><?php echo e(auth()->user()->name ?? 'Student'); ?></div>
                    <div style="font-size:13px;color:var(--tm);margin-top:2px;"><?php echo e($student?->course ?? 'N/A'); ?> — <?php echo e($student?->year_level ?? 'N/A'); ?></div>
                    <div style="font-size:12px;color:var(--tm);margin-top:1px;"><?php echo e(auth()->user()->email ?? ''); ?></div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                        <span style="background:var(--gp);color:var(--g);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">GWA: <?php echo e(number_format($gwa,2)); ?></span>
                        <span style="background:<?php echo e($isRegular?'#d0f0d8':'#fef3cd'); ?>;color:<?php echo e($isRegular?'#0d6624':'#a07c00'); ?>;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;"><?php echo e($student?->enrollment_type ?? 'N/A'); ?></span>
                        <span style="background:<?php echo e($overallScore>=75?'#d0f0d8':($overallScore>=50?'#fef3cd':'#fde8e6')); ?>;color:<?php echo e($overallScore>=75?'#0d6624':($overallScore>=50?'#a07c00':'#c0392b')); ?>;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">AI Score: <?php echo e($overallScore); ?>%</span>
                        <span style="background:<?php echo e($eligible_count>0?'#d0f0d8':'#fde8e6'); ?>;color:<?php echo e($eligible_count>0?'#0d6624':'#c0392b'); ?>;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">Eligible for <?php echo e($eligible_count); ?> scholarship<?php echo e($eligible_count!=1?'s':''); ?></span>
                    </div>
                </div>
                <div style="text-align:center;flex-shrink:0;">
                    <div style="font-size:10px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Generated</div>
                    <div style="font-size:12px;font-weight:700;color:var(--g);"><?php echo e(now()->format('M d, Y')); ?></div>
                    <div style="font-size:10px;color:var(--tm);"><?php echo e(now()->format('h:i A')); ?></div>
                </div>
            </div>

            
            <div style="margin-top:18px;border-top:1px solid var(--bd);padding-top:14px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tm);margin-bottom:10px;"><i class="fas fa-chart-bar" style="margin-right:5px;"></i>Scholarship Eligibility Summary</div>
                <div style="display:flex;flex-direction:column;gap:7px;">
                <?php $__currentLoopData = $mapped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $sfC = $s['score']>=75?'var(--gm)':($s['score']>=50?'var(--warn)':'var(--danger)'); ?>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="flex:1;min-width:0;font-size:12px;font-weight:600;color:var(--tx);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($s['name']); ?></div>
                    <div style="width:120px;height:8px;background:#e0e0e0;border-radius:20px;overflow:hidden;flex-shrink:0;">
                        <div style="width:<?php echo e($s['score']); ?>%;height:100%;background:<?php echo e($sfC); ?>;border-radius:20px;"></div>
                    </div>
                    <div style="font-weight:800;font-size:12px;color:<?php echo e($sfC); ?>;width:34px;text-align:right;flex-shrink:0;"><?php echo e($s['score']); ?>%</div>
                    <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;flex-shrink:0;background:<?php echo e($s['eligibility']==='Eligible'?'#d0f0d8':($s['eligibility']==='For Review'?'#fef3cd':'#fde8e6')); ?>;color:<?php echo e($s['eligibility']==='Eligible'?'#0d6624':($s['eligibility']==='For Review'?'#a07c00':'#c0392b')); ?>;"><?php echo e($s['eligibility']); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div style="margin-top:14px;background:linear-gradient(135deg,#0d3318,#1a6b2f);border-radius:10px;padding:13px 16px;display:flex;align-items:center;gap:12px;">
                <i class="fas fa-map-marker-alt" style="color:var(--y);font-size:20px;flex-shrink:0;"></i>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#fff;">Apply Physically at the Student Affairs Office</div>
                    <div style="font-size:12px;color:rgba(255,255,255,.7);margin-top:2px;">Saint Columban College — Pagadian City, Zamboanga del Sur &nbsp;·&nbsp; Present this passport and your original documents</div>
                </div>
            </div>
        </div>

        
        <div id="passportLoading" style="display:none;text-align:center;padding:28px;background:var(--bg);border-radius:var(--rs);margin-top:16px;">
            <div style="display:inline-flex;align-items:center;gap:12px;">
                <div style="width:20px;height:20px;border:3px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:aiSpin .7s linear infinite;"></div>
                <span style="font-size:13px;color:var(--tm);">Groq AI is generating your personalized Scholarship Passport...</span>
            </div>
        </div>

        <div id="passportAI" style="display:none;margin-top:16px;border:1.5px solid var(--y);border-radius:12px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#0d3318,#1a6b2f);padding:12px 18px;display:flex;align-items:center;gap:10px;">
                <div style="width:28px;height:28px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#0d3318;flex-shrink:0;"><i class="fas fa-robot"></i></div>
                <span style="font-size:13px;font-weight:700;color:#fff;">AI-Generated Scholarship Passport — Powered by Groq AI</span>
                <button type="button" onclick="document.getElementById('passportAI').style.display='none'" style="margin-left:auto;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:4px 10px;font-size:11px;color:rgba(255,255,255,.7);cursor:pointer;">Close</button>
            </div>
            <div id="passportAIContent" style="padding:20px 24px;font-size:13.5px;color:var(--tx);line-height:1.9;white-space:pre-wrap;background:#fff;"></div>
        </div>

        <div id="passportError" style="display:none;background:#fde8e6;border:1.5px solid var(--danger);border-radius:var(--rs);padding:12px 14px;font-size:13px;color:#7a1a14;margin-top:12px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i><span id="passportErrorMsg"></span>
        </div>
    </div>
</div>


<div class="card an mb3">
    <div class="ch" style="background:linear-gradient(135deg,#1a3a6b,#0038a8);">
        <div style="width:42px;height:42px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:19px;color:#fff;flex-shrink:0;"><i class="fas fa-chart-line"></i></div>
        <div><h2 style="color:#fff;font-size:16px;">Gap Analysis — What You Need to Qualify</h2>
        <div style="font-size:11px;color:rgba(255,255,255,.6);">Exact requirements vs your current profile for each scholarship</div></div>
        <div class="ch-acts">
            <button type="button" onclick="generateGapAnalysis()" id="gapBtn" class="btn btn-ac" style="font-size:12px;">
                <i class="fas fa-search"></i> Run AI Gap Analysis
            </button>
        </div>
    </div>
    <div style="padding:20px;">

        
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;margin-bottom:16px;">
        <?php $__currentLoopData = $mapped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $sc  = $s['score'];
            $gap = 100 - $sc;
            $scC = $sc>=75?'#0d6624':($sc>=50?'#a07c00':'#c0392b');
            $scB = $sc>=75?'#f0faf2':($sc>=50?'#fffcf0':'#fff8f8');
            $brd = $sc>=75?'#a0d8b0':($sc>=50?'#f0d060':'#f0a0a0');
        ?>
        <div style="background:<?php echo e($scB); ?>;border:1.5px solid <?php echo e($brd); ?>;border-radius:10px;padding:14px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <div style="font-size:13px;font-weight:700;color:var(--tx);"><?php echo e(\Illuminate\Support\Str::limit($s['name'],35)); ?></div>
                <div style="font-family:'Sora',sans-serif;font-size:18px;font-weight:800;color:<?php echo e($scC); ?>;"><?php echo e($sc); ?>%</div>
            </div>
            <div style="background:#e0e0e0;border-radius:20px;overflow:hidden;height:7px;margin-bottom:8px;">
                <div style="width:<?php echo e($sc); ?>%;height:100%;background:<?php echo e($scC); ?>;border-radius:20px;"></div>
            </div>
            <div style="font-size:11px;color:var(--tm);margin-bottom:8px;line-height:1.5;">
                <?php echo e(\Illuminate\Support\Str::limit($s['reasoning'],100)); ?>

            </div>
            <div style="font-size:11px;">
                <?php if($sc >= 75): ?>
                <span style="color:#0d6624;font-weight:700;"><i class="fas fa-check-circle" style="margin-right:4px;"></i>You meet the requirements. Visit SAO to apply.</span>
                <?php elseif($sc >= 50): ?>
                <span style="color:#a07c00;font-weight:700;"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i><?php echo e($gap); ?> pts needed. Submit supporting documents at SAO.</span>
                <?php else: ?>
                <span style="color:#c0392b;font-weight:700;"><i class="fas fa-times-circle" style="margin-right:4px;"></i><?php echo e($gap); ?> pts gap. Focus on GWA and enrollment this semester.</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div id="gapLoading" style="display:none;text-align:center;padding:20px;background:var(--bg);border-radius:var(--rs);">
            <div style="display:inline-flex;align-items:center;gap:10px;">
                <div style="width:16px;height:16px;border:3px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:aiSpin .7s linear infinite;"></div>
                <span style="font-size:13px;color:var(--tm);">AI is running your gap analysis...</span>
            </div>
        </div>

        <div id="gapResult" style="display:none;border:1.5px solid var(--y);border-radius:var(--r);overflow:hidden;margin-top:4px;">
            <div style="background:linear-gradient(135deg,#0d3318,#1a6b2f);padding:11px 16px;display:flex;align-items:center;gap:10px;">
                <div style="width:26px;height:26px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#0d3318;flex-shrink:0;"><i class="fas fa-search"></i></div>
                <span style="font-size:13px;font-weight:700;color:#fff;">AI Gap Analysis Report</span>
                <button type="button" onclick="document.getElementById('gapResult').style.display='none'" style="margin-left:auto;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:4px 10px;font-size:11px;color:rgba(255,255,255,.7);cursor:pointer;">Close</button>
            </div>
            <div id="gapResultText" style="padding:18px 20px;font-size:13.5px;color:var(--tx);line-height:1.9;white-space:pre-wrap;background:#fff;"></div>
        </div>

        <div id="gapError" style="display:none;background:#fde8e6;border:1.5px solid var(--danger);border-radius:var(--rs);padding:12px 14px;font-size:13px;color:#7a1a14;margin-top:10px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i><span id="gapErrorMsg"></span>
        </div>
    </div>
</div>


<div class="card an mb3">
    <div class="ch">
        <div style="width:38px;height:38px;background:linear-gradient(135deg,var(--g),var(--gm));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--y);flex-shrink:0;"><i class="fas fa-tasks"></i></div>
        <div><h2 style="font-size:15px;">Your Priority Action Plan This Semester</h2>
        <div style="font-size:11px;color:var(--tm);">Follow these steps to qualify for more scholarships</div></div>
        <div class="ch-acts">
            <button type="button" onclick="generateActionPlan()" id="planBtn" class="btn btn-p btn-sm">
                <i class="fas fa-robot"></i> Generate AI Plan
            </button>
        </div>
    </div>
    <div style="padding:18px 20px;">

        
        <div style="display:flex;flex-direction:column;gap:10px;" id="staticPlan">
        <?php
            $actions = [];
            if($gwa > 1.75) $actions[] = ['high','Improve GWA to 1.75 or better','Your GWA of '.number_format($gwa,2).' is above the cutoff for most scholarships. Focus on your academic performance this semester.','fas fa-star'];
            if(!$isRegular) $actions[] = ['high','Shift to Regular Enrollment','Irregular students lose 10 points on every scholarship score. Shifting to Regular doubles your enrollment score.','fas fa-id-badge'];
            if($bracket==='above_400'||$bracket==='') $actions[] = ['medium','Update your income bracket in your profile','Income bracket affects up to 15 points of your AI score. Make sure it is accurate.','fas fa-hand-holding-usd'];
            if($gwa > 1.25 && $gwa <= 1.75) $actions[] = ['low','Push GWA to 1.25 for Excellence Bonus','You are close to qualifying for the +5 point Excellence Bonus on all scholarships.','fas fa-trophy'];
            $actions[] = ['high','Visit SAO Office to get the official scholarship application form','Physical applications are required. Bring your Scholarship Passport, transcript, and proof of income.','fas fa-map-marker-alt'];
            $actions[] = ['medium','Prepare supporting documents','Requirements typically include: transcript of records, certificate of enrollment, income tax return or certificate of indigency, and 2x2 ID photos.','fas fa-file-alt'];
            $actions[] = ['low','Monitor scholarship deadlines','Check with the SAO office regularly for opening and closing dates of each scholarship program.','fas fa-calendar-check'];
        ?>
        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $pCol = $action[0]==='high'?'#c0392b':($action[0]==='medium'?'#d68910':'#1a6b2f');
            $pBg  = $action[0]==='high'?'#fde8e6':($action[0]==='medium'?'#fef3cd':'#d0f0d8');
            $pLbl = $action[0]==='high'?'Priority':($action[0]==='medium'?'Important':'Recommended');
        ?>
        <div style="display:flex;align-items:flex-start;gap:13px;padding:13px 15px;background:var(--bg);border:1px solid var(--bd);border-radius:10px;">
            <div style="width:34px;height:34px;background:linear-gradient(135deg,var(--g),var(--gm));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;color:var(--y);flex-shrink:0;"><?php echo e($idx+1); ?></div>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                    <span style="font-size:13px;font-weight:700;color:var(--tx);"><i class="fas <?php echo e($action[3]); ?>" style="color:var(--gm);margin-right:5px;"></i><?php echo e($action[1]); ?></span>
                    <span style="background:<?php echo e($pBg); ?>;color:<?php echo e($pCol); ?>;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;"><?php echo e($pLbl); ?></span>
                </div>
                <div style="font-size:12px;color:var(--tm);line-height:1.6;"><?php echo e($action[2]); ?></div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div id="planLoading" style="display:none;text-align:center;padding:18px;background:var(--bg);border-radius:var(--rs);margin-top:12px;">
            <div style="display:inline-flex;align-items:center;gap:10px;">
                <div style="width:16px;height:16px;border:3px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:aiSpin .7s linear infinite;"></div>
                <span style="font-size:13px;color:var(--tm);">AI is generating your action plan...</span>
            </div>
        </div>

        <div id="planResult" style="display:none;border:1.5px solid var(--gm);border-radius:var(--r);overflow:hidden;margin-top:12px;">
            <div style="background:linear-gradient(135deg,var(--g),var(--gm));padding:11px 16px;display:flex;align-items:center;gap:10px;">
                <div style="width:26px;height:26px;background:rgba(255,255,255,.2);border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff;flex-shrink:0;"><i class="fas fa-robot"></i></div>
                <span style="font-size:13px;font-weight:700;color:#fff;">AI Priority Action Plan</span>
                <button type="button" onclick="document.getElementById('planResult').style.display='none'" style="margin-left:auto;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:4px 10px;font-size:11px;color:rgba(255,255,255,.7);cursor:pointer;">Close</button>
            </div>
            <div id="planResultText" style="padding:18px 20px;font-size:13.5px;color:var(--tx);line-height:1.9;white-space:pre-wrap;background:#fff;"></div>
        </div>

        <div id="planError" style="display:none;background:#fde8e6;border:1.5px solid var(--danger);border-radius:var(--rs);padding:12px 14px;font-size:13px;color:#7a1a14;margin-top:10px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i><span id="planErrorMsg"></span>
        </div>
    </div>
</div>


<div class="card an mb3" style="border:2px solid var(--g);">
    <div class="ch"><i class="fas fa-map-marker-alt" style="color:var(--gm);font-size:18px;"></i><h2>How to Apply — Physical Application Process</h2></div>
    <div style="padding:18px 20px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
        <?php $__currentLoopData = [
            ['1','fas fa-robot','Check AI Score Here','Review your AI eligibility score on this page to know which scholarships you qualify for.','t'],
            ['2','fas fa-print','Print Your Passport','Click "Generate AI Passport" then print the full passport card to bring to the office.','y'],
            ['3','fas fa-file-alt','Prepare Documents','Gather your transcript of records, COE, income proof, birth certificate, and 2x2 ID photos.','g'],
            ['4','fas fa-map-marker-alt','Visit SAO Office','Go to the Student Affairs Office at Saint Columban College during office hours.','o'],
            ['5','fas fa-clipboard-list','Submit Application','Fill out the official application form, attach your documents, and submit to the SAO officer.','g'],
            ['6','fas fa-bell','Wait for Result','The SAO office will notify you of the result. AI eligibility scores assist but final decision rests with the committee.','t'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background:var(--bg);border:1px solid var(--bd);border-radius:10px;padding:13px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:7px;">
                <div style="width:28px;height:28px;background:linear-gradient(135deg,var(--g),var(--gm));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;color:var(--y);flex-shrink:0;"><?php echo e($step[0]); ?></div>
                <div class="si <?php echo e($step[4]); ?>" style="width:28px;height:28px;border-radius:7px;font-size:12px;flex-shrink:0;"><i class="<?php echo e($step[1]); ?>"></i></div>
                <div class="fws" style="font-size:13px;"><?php echo e($step[2]); ?></div>
            </div>
            <div style="font-size:12px;color:var(--tm);line-height:1.5;"><?php echo e($step[3]); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>


<div id="jsSchData" style="display:none;"><?php echo e(json_encode(array_values($mapped))); ?></div>
<div id="jsProfileData" style="display:none;"><?php echo e(json_encode([
    'name'       => auth()->user()->name ?? '',
    'gwa'        => (string)($student?->gwa ?? 'N/A'),
    'course'     => $student?->course ?? 'N/A',
    'yearLevel'  => $student?->year_level ?? 'N/A',
    'enrollment' => $student?->enrollment_type ?? 'N/A',
    'income'     => $student?->income_bracket ?? 'N/A',
    'bestScore'  => $overallScore,
    'eligibility'=> $overallEligibility,
    'email'      => auth()->user()->email ?? '',
    'eligible_count' => $eligible_count,
    'review_count'   => $review_count,
])); ?></div>

<style>
@keyframes aiSpin{to{transform:rotate(360deg);}}
@media print{
    .sidebar,.topbar,.ai-bar,.btn,#passportLoading,#passportAI .btn{display:none!important;}
    #passportCard{border:2px solid #1a6b2f!important;box-shadow:none!important;}
    .main{margin-left:0!important;}
}
</style>

<script>
(function(){
    var GK   = document.getElementById('gk').value;
    var SCH  = JSON.parse(document.getElementById('jsSchData').textContent || document.getElementById('jsSchData').innerText || '[]');
    var PRF  = JSON.parse(document.getElementById('jsProfileData').textContent || document.getElementById('jsProfileData').innerText || '{}');

    function callGemini(prompt, onSuccess, onError, maxTokens){
        if(!GK||!GK.trim()){ onError('GROQ_API_KEY is missing. Add it to .env and run: php artisan config:clear'); return; }
        fetch('https://api.groq.com/openai/v1/chat/completions', {
            method:'POST',
            headers:{'Content-Type':'application/json','Authorization':'Bearer '+GK},
            body: JSON.stringify({
                model:'llama-3.1-8b-instant',messages:[{role:'user',content:prompt}],max_tokens:maxTokens||400,temperature:0.7
            })
        })
        .then(function(r){ return r.json().then(function(d){ return {ok:r.ok,status:r.status,data:d}; }); })
        .then(function(r){
            if(!r.ok){ onError('Groq AI Error: '+(r.data&&r.data.error?r.data.error.message:'HTTP '+r.status)); return; }
            var t = r.data&&r.data.choices&&r.data.choices[0]&&r.data.choices[0].message?r.data.choices[0].message.content:'';
            if(!t){ onError('Empty response from Groq. Please try again.'); return; }
            onSuccess(t);
        })
        .catch(function(e){ onError('Network error: '+e.message); });
    }

    function schSummary(){
        return SCH.map(function(s,i){
            return (i+1)+'. '+s.name+' ('+s.type+') | Score:'+s.score+'% | '+s.eligibility+(s.tag?' | '+s.tag:'')+(s.reasoning?' | '+s.reasoning.substring(0,80):'');
        }).join('\n');
    }

    window.generatePassport = function(){
        var btn = document.getElementById('genBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        document.getElementById('passportLoading').style.display='block';
        document.getElementById('passportAI').style.display='none';
        document.getElementById('passportError').style.display='none';

        var p = 'You are ISAMS AI at Saint Columban College, Pagadian City, Philippines. Generate a formal SCHOLARSHIP PASSPORT document for this student.\n\n'
            +'STUDENT PROFILE:\n'
            +'Name: '+PRF.name+'\nCourse: '+PRF.course+' '+PRF.yearLevel+'\nGWA: '+PRF.gwa+'\nEnrollment: '+PRF.enrollment+'\nIncome Bracket: '+PRF.income+'\nBest AI Score: '+PRF.bestScore+'% ('+PRF.eligibility+')\nEligible for: '+PRF.eligible_count+' scholarship(s)\n\n'
            +'SCHOLARSHIP SCORES:\n'+schSummary()+'\n\n'
            +'Generate a formal scholarship passport with these sections:\n'
            +'1. ELIGIBILITY SUMMARY: Overall status and best matches in 2 sentences.\n'
            +'2. TOP RECOMMENDED SCHOLARSHIPS: List the top 1-3 with score and why they qualify. Say "Visit SAO Office to apply physically."\n'
            +'3. GAP ANALYSIS: For each scholarship they don\'t fully qualify for, what exactly is missing (e.g., "Need GWA of 1.75, currently 1.90 — need 0.15 improvement").\n'
            +'4. DOCUMENTS TO PREPARE: List standard physical application documents needed.\n'
            +'5. NEXT STEPS: 3 concrete actions for this semester.\n\n'
            +'Write formally. Plain text only. No asterisks or hashtags. This will be printed.';

        callGemini(p, function(text){
            document.getElementById('passportLoading').style.display='none';
            document.getElementById('passportAIContent').textContent = text;
            document.getElementById('passportAI').style.display='block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic"></i> Regenerate Passport';
        }, function(err){
            document.getElementById('passportLoading').style.display='none';
            document.getElementById('passportErrorMsg').textContent = err;
            document.getElementById('passportError').style.display='block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic"></i> Generate AI Passport';
        }, 600);
    };

    window.generateGapAnalysis = function(){
        var btn = document.getElementById('gapBtn');
        btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Analyzing...';
        document.getElementById('gapLoading').style.display='block';
        document.getElementById('gapResult').style.display='none';
        document.getElementById('gapError').style.display='none';

        var p = 'You are ISAMS AI at Saint Columban College, Pagadian City, Philippines. Generate a detailed GAP ANALYSIS for this student.\n\n'
            +'STUDENT: '+PRF.name+' | GWA:'+PRF.gwa+' | '+PRF.enrollment+' | Income:'+PRF.income+'\n\n'
            +'SCHOLARSHIP SCORES:\n'+schSummary()+'\n\n'
            +'For each scholarship, provide:\n'
            +'- Current score vs needed score (75% to be eligible)\n'
            +'- Exact gap in points\n'
            +'- Specific actions to close the gap (e.g., "Improve GWA by 0.10 to gain 8 more points")\n'
            +'- Timeline estimate (e.g., "Achievable next semester if GWA improves")\n\n'
            +'Be specific with numbers. Plain text only. No asterisks. Helpful and encouraging tone.';

        callGemini(p, function(text){
            document.getElementById('gapLoading').style.display='none';
            document.getElementById('gapResultText').textContent=text;
            document.getElementById('gapResult').style.display='block';
            btn.disabled=false; btn.innerHTML='<i class="fas fa-search"></i> Re-run Gap Analysis';
        }, function(err){
            document.getElementById('gapLoading').style.display='none';
            document.getElementById('gapErrorMsg').textContent=err;
            document.getElementById('gapError').style.display='block';
            btn.disabled=false; btn.innerHTML='<i class="fas fa-search"></i> Run AI Gap Analysis';
        }, 500);
    };

    window.generateActionPlan = function(){
        var btn = document.getElementById('planBtn');
        btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Generating...';
        document.getElementById('planLoading').style.display='block';
        document.getElementById('planResult').style.display='none';
        document.getElementById('planError').style.display='none';

        var p = 'You are ISAMS AI at Saint Columban College, Pagadian City, Philippines. Generate a PRIORITY ACTION PLAN for this student to maximize scholarship eligibility.\n\n'
            +'STUDENT: '+PRF.name+' | GWA:'+PRF.gwa+' | '+PRF.enrollment+' | Income:'+PRF.income+' | Best Score:'+PRF.bestScore+'%\n\n'
            +'SCHOLARSHIPS:\n'+schSummary()+'\n\n'
            +'Create a numbered action plan with:\n'
            +'1. Immediate actions (this week) — e.g., visit SAO for forms, update profile\n'
            +'2. Short-term actions (this semester) — e.g., academic improvements needed\n'
            +'3. Documents to prepare before applying physically at SAO\n'
            +'4. Specific GWA target needed to unlock more scholarships\n'
            +'5. Which scholarship to prioritize applying for first and why\n\n'
            +'Note: All applications are done physically at the Student Affairs Office. Do not say apply online.\n'
            +'Be specific, encouraging, and practical. Plain text only. No asterisks.';

        callGemini(p, function(text){
            document.getElementById('planLoading').style.display='none';
            document.getElementById('planResultText').textContent=text;
            document.getElementById('planResult').style.display='block';
            btn.disabled=false; btn.innerHTML='<i class="fas fa-robot"></i> Regenerate Plan';
        }, function(err){
            document.getElementById('planLoading').style.display='none';
            document.getElementById('planErrorMsg').textContent=err;
            document.getElementById('planError').style.display='block';
            btn.disabled=false; btn.innerHTML='<i class="fas fa-robot"></i> Generate AI Plan';
        }, 500);
    };

    window.printPassport = function(){
        window.print();
    };
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/student/eligibility/index.blade.php ENDPATH**/ ?>