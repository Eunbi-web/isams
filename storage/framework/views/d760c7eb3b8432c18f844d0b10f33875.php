<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('favicon-32x32.png')); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('favicon-16x16.png')); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('favicon-180x180.png')); ?>">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"><title>ISAMS Admin — <?php echo $__env->yieldContent('title','Dashboard'); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{--g:#1a6b2f;--gm:#2d9e4f;--gp:#e8f5ec;--y:#f0c020;--yd:#c9a010;--yp:#fef9e0;--danger:#c0392b;--warn:#d68910;--info:#1a6b8a;--bg:#f2f7f3;--card:#fff;--sb:#0d3318;--st:#90c8a0;--sh:#1a6b2f;--tx:#1a2e1a;--tm:#5a7a60;--bd:#cde0d0;--shadow:0 2px 12px rgba(26,107,47,.09);--r:12px;--rs:8px;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--tx);min-height:100vh;display:flex;font-size:15px;}
.sidebar{width:262px;min-width:262px;background:var(--sb);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100;overflow-y:auto;transition:transform .3s;}
.sb-brand{padding:22px 18px 16px;border-bottom:1px solid rgba(255,255,255,.07);}
.sb-logo{display:flex;align-items:center;gap:11px;}
.sb-icon{width:52px;height:52px;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
.sb-icon img{width:100%;height:100%;object-fit:contain;}
.sb-name{font-family:'Sora',sans-serif;font-size:15px;font-weight:800;color:#fff;line-height:1.1;}
.sb-sub{font-size:10px;color:var(--st);text-transform:uppercase;letter-spacing:1px;}
.ai-pill-sb{display:flex;align-items:center;gap:6px;margin:12px 12px 0;background:rgba(240,192,32,.1);border:1px solid rgba(240,192,32,.25);border-radius:8px;padding:8px 12px;}
.ai-dot{width:6px;height:6px;border-radius:50%;background:var(--y);animation:pulse 2s infinite;flex-shrink:0;}
.ai-pill-sb span{font-size:11px;color:var(--y);font-weight:600;}
.ai-v{margin-left:auto;font-size:10px;color:rgba(144,200,160,.5);}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(1.4)}}
.sb-sec{padding:14px 10px 4px;}
.sb-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(144,200,160,.4);padding:0 8px 7px;}
.nav-a{display:flex;align-items:center;gap:11px;padding:9px 10px;border-radius:var(--rs);color:var(--st);text-decoration:none;font-size:13.5px;font-weight:500;transition:all .2s;margin-bottom:2px;}
.nav-a i{width:17px;text-align:center;font-size:14px;}
.nav-a:hover{background:var(--sh);color:#fff;}
.nav-a.active{background:var(--sh);color:var(--y);}
.nav-badge{margin-left:auto;background:var(--y);color:#0d3318;font-size:10px;font-weight:800;padding:2px 7px;border-radius:20px;line-height:1.4;}
.nav-badge.g{background:var(--gm);color:#fff;}.nav-badge.b{background:var(--info);color:#fff;}.nav-badge.ph{background:#0038a8;color:#fff;}
.sb-footer{margin-top:auto;padding:13px 10px;border-top:1px solid rgba(255,255,255,.07);}
.user-card{display:flex;align-items:center;gap:9px;padding:9px 10px;background:rgba(255,255,255,.05);border-radius:var(--rs);}
.u-av{width:34px;height:34px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;color:#0d3318;}
.u-name{font-size:13px;font-weight:600;color:#fff;}
.u-role{font-size:10px;color:var(--st);}
.u-out{margin-left:auto;color:var(--st);text-decoration:none;font-size:14px;transition:color .2s;}
.u-out:hover{color:var(--danger);}
.main{margin-left:262px;flex:1;display:flex;flex-direction:column;min-height:100vh;}
.ai-bar{background:linear-gradient(135deg,#0d3318,#1a6b2f);padding:8px 26px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;}
.ai-bar-label{display:flex;align-items:center;gap:7px;font-size:12px;color:rgba(255,255,255,.8);font-weight:600;}
.ai-bar-stats{display:flex;gap:14px;flex-wrap:wrap;}
.ai-stat{font-size:12px;color:rgba(255,255,255,.65);}
.ai-stat strong{color:var(--y);}
.topbar{background:var(--card);border-bottom:2.5px solid var(--y);padding:0 26px;height:62px;display:flex;align-items:center;gap:14px;position:sticky;top:0;z-index:90;box-shadow:0 2px 10px rgba(26,107,47,.07);}
.tp-title{font-family:'Sora',sans-serif;font-size:17px;font-weight:700;color:var(--g);}
.tp-sub{font-size:11px;color:var(--tm);}
.tp-right{margin-left:auto;display:flex;align-items:center;gap:10px;}
.tp-btn{width:37px;height:37px;background:var(--bg);border:1.5px solid var(--bd);border-radius:var(--rs);display:flex;align-items:center;justify-content:center;color:var(--tm);cursor:pointer;text-decoration:none;transition:all .2s;}
.tp-btn:hover{background:var(--g);color:#fff;border-color:var(--g);}
.school-chip{background:linear-gradient(135deg,var(--g),var(--gm));color:#fff;padding:5px 13px;border-radius:20px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:6px;}
.school-chip img{width:16px;height:16px;object-fit:contain;}
.mob-toggle{display:none;background:none;border:1.5px solid var(--bd);border-radius:var(--rs);width:37px;height:37px;align-items:center;justify-content:center;color:var(--tm);cursor:pointer;font-size:16px;}
.page{padding:24px 26px;flex:1;}
.card{background:var(--card);border-radius:var(--r);border:1px solid var(--bd);box-shadow:var(--shadow);overflow:hidden;}
.ch{padding:15px 20px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:10px;}
.ch h2{font-family:'Sora',sans-serif;font-size:14px;font-weight:700;color:var(--g);}
.ch-acts{margin-left:auto;display:flex;gap:8px;}
.cb{padding:20px;}
.sg{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:20px;}
.sc{background:var(--card);border-radius:var(--r);border:1px solid var(--bd);padding:16px 18px;box-shadow:var(--shadow);display:flex;align-items:center;gap:13px;transition:transform .2s,box-shadow .2s;}
.sc:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(26,107,47,.12);}
.si{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0;}
.si.g{background:var(--gp);color:var(--gm);}.si.y{background:var(--yp);color:var(--yd);}.si.r{background:#fde8e6;color:var(--danger);}.si.t{background:#e0f3f8;color:var(--info);}.si.o{background:#fef3cd;color:var(--warn);}.si.dg{background:#d0f0d8;color:#0d6624;}
.sv .lbl{font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
.sv .val{font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:var(--g);line-height:1.1;}
.sv .chg{font-size:11px;color:#1a7a4a;margin-top:1px;}.sv .chg.dn{color:var(--danger);}
.asb{height:7px;border-radius:10px;background:#eee;overflow:hidden;}
.asf{height:100%;border-radius:10px;transition:width .8s;}
.ash{background:linear-gradient(90deg,var(--gm),var(--y));}.asm{background:linear-gradient(90deg,var(--warn),var(--y));}.asl{background:linear-gradient(90deg,var(--danger),#e87070);}
.slot-bar{height:9px;background:#e8f0ea;border-radius:9px;overflow:hidden;margin:8px 0;}
.slot-fill{height:100%;border-radius:9px;}
.tw{overflow-x:auto;}table{width:100%;border-collapse:collapse;}
thead th{background:#f3f8f4;padding:9px 13px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--tm);text-align:left;border-bottom:2px solid var(--bd);}
tbody tr{transition:background .15s;}tbody tr:hover{background:#f8fdf8;}
tbody td{padding:11px 13px;font-size:13px;border-bottom:1px solid var(--bd);}tbody tr:last-child td{border-bottom:none;}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.b-s{background:#d0f0d8;color:#0d6624;}.b-d{background:#fde8e6;color:var(--danger);}.b-w{background:#fef3cd;color:#a07c00;}.b-i{background:#e0f3f8;color:var(--info);}.b-p{background:var(--gp);color:var(--g);}.b-y{background:var(--yp);color:var(--yd);}.b-gray{background:#eef1f5;color:#5a7a60;}
.elig-el{background:#d0f0d8;color:#0d6624;border:1px solid #a0d8b0;}.elig-rv{background:var(--yp);color:var(--yd);border:1px solid #f0d060;}.elig-no{background:#fde8e6;color:var(--danger);border:1px solid #f0b0a0;}
.tag{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;}
.tag-renewal{background:#d0f0d8;color:#0d6624;}.tag-needs{background:var(--yp);color:var(--yd);}.tag-disq{background:#fde8e6;color:var(--danger);}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--rs);font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all .2s;font-family:inherit;}
.btn-p{background:var(--g);color:#fff;}.btn-p:hover{background:var(--gm);}
.btn-ac{background:var(--y);color:#0d3318;}.btn-ac:hover{background:#f7d84a;}
.btn-s{background:#1a7a4a;color:#fff;}.btn-d{background:var(--danger);color:#fff;}
.btn-o{background:transparent;border:1.5px solid var(--bd);color:var(--tm);}.btn-o:hover{border-color:var(--g);color:var(--g);background:var(--gp);}
.btn-ai{background:linear-gradient(135deg,var(--g),var(--gm));color:var(--y);border:1px solid rgba(240,192,32,.4);font-weight:700;}
.btn-sm{padding:5px 12px;font-size:12px;}.btn-ic{padding:6px 9px;}
.fg{margin-bottom:15px;}.fl{display:block;font-size:13px;font-weight:600;color:var(--tx);margin-bottom:5px;}
.fc{width:100%;padding:9px 13px;border:1.5px solid var(--bd);border-radius:var(--rs);font-size:13px;color:var(--tx);background:#fff;transition:border-color .2s;font-family:inherit;}
.fc:focus{outline:none;border-color:var(--gm);box-shadow:0 0 0 3px rgba(45,158,79,.1);}
.alert{padding:11px 15px;border-radius:var(--rs);margin-bottom:14px;font-size:13px;display:flex;align-items:center;gap:8px;}
.al-s{background:#d0f0d8;border-left:4px solid #1a7a4a;color:#0d4a1e;}.al-d{background:#fde8e6;border-left:4px solid var(--danger);color:#7a1a14;}.al-w{background:#fef3cd;border-left:4px solid var(--warn);color:#6b4a00;}.al-i{background:var(--gp);border-left:4px solid var(--gm);color:var(--g);}.al-ai{background:var(--yp);border-left:4px solid var(--y);color:var(--yd);}
.mo{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;display:none;align-items:center;justify-content:center;backdrop-filter:blur(3px);}
.mo.open{display:flex;}
.mb{background:#fff;border-radius:16px;max-width:640px;width:95%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.25);animation:min .25s ease;}
@keyframes min{from{opacity:0;transform:scale(.94) translateY(16px)}to{opacity:1;transform:scale(1) translateY(0)}}
.mh{padding:18px 22px 14px;border-bottom:2px solid var(--y);display:flex;align-items:center;gap:11px;position:sticky;top:0;background:#fff;z-index:1;}
.mh h3{font-family:'Sora',sans-serif;font-size:17px;font-weight:700;color:var(--g);}
.mc{margin-left:auto;background:none;border:none;font-size:18px;color:var(--tm);cursor:pointer;}.mc:hover{color:var(--danger);}
.mbody{padding:20px 22px;}.mfoot{padding:13px 22px;border-top:1px solid var(--bd);display:flex;gap:8px;justify-content:flex-end;}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:15px;}.g3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px;}
.mt3{margin-top:18px;}.mb3{margin-bottom:18px;}.mb2{margin-bottom:12px;}
.tm{color:var(--tm);}.fwb{font-weight:700;}.fws{font-weight:600;}.mono{font-family:'JetBrains Mono',monospace;}
.av{border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:700;color:var(--g);flex-shrink:0;}
.av-s{width:30px;height:30px;font-size:12px;background:var(--gp);}.av-m{width:38px;height:38px;font-size:14px;background:var(--gp);}
.an{animation:fadeUp .35s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(13px)}to{opacity:1;transform:translateY(0)}}
.d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}
::-webkit-scrollbar{width:6px;height:6px;}::-webkit-scrollbar-thumb{background:#b0d0b8;border-radius:10px;}
@media(max-width:900px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}.main{margin-left:0}.mob-toggle{display:flex}.g2,.g3{grid-template-columns:1fr}}
/* Theme overrides (light/dark) */
body[data-theme="dark"]{--bg:#0b1220;--card:#0f1a2d;--sb:#081022;--st:#9fb3c8;--tm:#dbe8f5;--tm2:#6f8aa7;--bd:rgba(160,190,220,0.22);--shadow:0 8px 30px rgba(0,0,0,0.35);}
body[data-theme="light"]{--bg:#f2f7f3;--card:#fff;--sb:#0d3318;--st:#90c8a0;--tx:#1a2e1a;--tm:#5a7a60;--bd:#cde0d0;--shadow:0 2px 12px rgba(26,107,47,.09);}

/* ── Notification Dropdown ── */
.notif-wrapper{position:relative;}
.notif-item{padding:13px 18px;border-bottom:1px solid var(--bd);display:flex;align-items:flex-start;gap:11px;cursor:pointer;transition:background .15s;}
.notif-item:hover{background:var(--bg);}
.notif-item.unread{background:#f0faf2;}
.notif-item.unread:hover{background:#e4f5e8;}
.notif-item:last-child{border-bottom:none;}
.notif-icon{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}
@keyframes notifSpin{to{transform:rotate(360deg);}}
</style><?php echo $__env->yieldPushContent('styles'); ?></head>
<body data-theme="light">
<aside class="sidebar" id="sidebar">
<div class="sb-brand"><div class="sb-logo"><div class="sb-icon"><img src="<?php echo e(asset('images/DSA_logo.png')); ?>" alt="Logo"></div><div><div class="sb-name">ISAMS</div><div class="sb-sub">Admin Portal</div></div></div></div>
<div class="ai-pill-sb"><div class="ai-dot"></div><span>AI Engine Active</span><span class="ai-v">v2.1</span></div>
<div class="sb-sec"><div class="sb-lbl">Main</div>
<a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.dashboard')?'active':''); ?>"><i class="fas fa-th-large"></i> Dashboard</a></div>
<div class="sb-sec"><div class="sb-lbl">Scholarship</div>
<a href="<?php echo e(route('admin.scholarships.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.scholarships*')?'active':''); ?>"><i class="fas fa-award"></i> Programs</a>
<a href="<?php echo e(route('admin.scraper.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.scraper*')?'active':''); ?>"><i class="fas fa-flag"></i> PH Scholarship Sync<span class="nav-badge ph">PH</span></a>
<a href="<?php echo e(route('admin.applications.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.applications*')?'active':''); ?>"><i class="fas fa-file-alt"></i> Applications</a>
<a href="<?php echo e(route('admin.ai.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.ai*')?'active':''); ?>"><i class="fas fa-robot"></i> AI Filter<span class="nav-badge">AI</span></a>
<a href="<?php echo e(route('admin.students.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.students*')?'active':''); ?>"><i class="fas fa-users"></i> Students</a>
<a href="<?php echo e(route('admin.reports.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.reports*')?'active':''); ?>"><i class="fas fa-chart-bar"></i> Reports</a></div>
<div class="sb-sec"><div class="sb-lbl">Services</div>
<a href="<?php echo e(route('admin.counseling.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.counseling*')?'active':''); ?>"><i class="fas fa-heart"></i> Counseling</a>
<a href="<?php echo e(route('admin.discipline.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.discipline*')?'active':''); ?>"><i class="fas fa-gavel"></i> Discipline Records</a>
<a href="<?php echo e(route('admin.announcements.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.announcements*')?'active':''); ?>"><i class="fas fa-bullhorn"></i> Announcements</a></div>
<div class="sb-sec"><div class="sb-lbl">Account</div>

<a href="<?php echo e(route('admin.settings.index')); ?>" class="nav-a <?php echo e(request()->routeIs('admin.settings*')?'active':''); ?>"><i class="fas fa-cog"></i> Settings</a></div>
<div class="sb-footer"><div class="user-card"><div class="u-av"><?php echo e(strtoupper(substr(auth()->user()->name??'A',0,1))); ?></div><div><div class="u-name"><?php echo e(explode(' ',auth()->user()->name??'Admin')[0]); ?></div><div class="u-role"><?php echo e(ucfirst(auth()->user()->role??'admin')); ?></div></div><a href="<?php echo e(route('logout')); ?>" class="u-out" onclick="event.preventDefault();document.getElementById('alf').submit();" title="Logout"><i class="fas fa-sign-out-alt"></i></a></div><form id="alf" action="<?php echo e(route('logout')); ?>" method="POST" style="display:none;"><?php echo csrf_field(); ?></form></div>
</aside>
<div class="main">
<?php echo $__env->yieldContent('ai-bar'); ?>
<header class="topbar">
<button class="mob-toggle" id="sbToggle"><i class="fas fa-bars"></i></button>
<div><div class="tp-title"><?php echo $__env->yieldContent('page-title','Dashboard'); ?></div><div class="tp-sub"><?php echo $__env->yieldContent('page-sub','ISAMS — Integrated Student Affairs Management System'); ?></div></div>
<div class="tp-right">
<span class="school-chip"><img src="<?php echo e(asset('images/DSA_logo.png')); ?>" alt="Logo">ISAMS</span>


<?php echo $__env->make('components.theme-toggle', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="notif-wrapper" id="adminNotifWrapper" style="position:relative;">

    <button type="button" id="adminNotifBtn" onclick="toggleAdminNotif()"
        style="position:relative;background:var(--bg);border:1.5px solid var(--bd);border-radius:var(--rs);width:37px;height:37px;display:flex;align-items:center;justify-content:center;color:var(--tm);cursor:pointer;transition:all .2s;"
        onmouseover="this.style.background='var(--g)';this.style.color='#fff';this.style.borderColor='var(--g)'"
        onmouseout="this.style.background='var(--bg)';this.style.color='var(--tm)';this.style.borderColor='var(--bd)'">
        <i class="fas fa-bell" style="font-size:15px;"></i>
        <span id="adminNotifBadge" style="display:none;position:absolute;top:3px;right:3px;width:16px;height:16px;background:var(--danger);color:#fff;border-radius:50%;font-size:9px;font-weight:800;align-items:center;justify-content:center;border:2px solid #fff;line-height:1;"></span>
    </button>
    <div id="adminNotifDropdown" style="display:none;position:absolute;top:calc(100% + 10px);right:0;width:360px;background:#fff;border:1.5px solid var(--bd);border-radius:14px;box-shadow:0 12px 40px rgba(0,0,0,.14);z-index:9990;overflow:hidden;">
        <div style="padding:13px 18px;border-bottom:1px solid var(--bd);display:flex;align-items:center;justify-content:space-between;background:var(--bg);">
            <div style="font-size:14px;font-weight:700;color:var(--tx);">Notifications</div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span id="adminNotifUnread" style="font-size:11px;color:var(--tm);"></span>
                <button type="button" onclick="adminMarkAll()" id="adminMarkAllBtn" style="display:none;background:none;border:1px solid var(--bd);border-radius:7px;padding:3px 10px;font-size:11px;color:var(--gm);cursor:pointer;font-weight:600;">Mark all read</button>
            </div>
        </div>
        <div id="adminNotifLoading" style="padding:24px;text-align:center;color:var(--tm);font-size:13px;">
            <div style="display:inline-flex;align-items:center;gap:8px;"><div style="width:14px;height:14px;border:2px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:notifSpin .7s linear infinite;"></div>Loading...</div>
        </div>
        <div id="adminNotifList" style="display:none;max-height:380px;overflow-y:auto;"></div>
        <div id="adminNotifEmpty" style="display:none;padding:30px 20px;text-align:center;">
            <i class="fas fa-bell-slash" style="font-size:28px;color:var(--bd);margin-bottom:10px;display:block;"></i>
            <div style="font-size:13px;color:var(--tm);">No notifications yet</div>
        </div>
        <div style="padding:11px 18px;border-top:1px solid var(--bd);background:var(--bg);text-align:center;">
            <a href="<?php echo e(route('admin.notifications.index')); ?>" style="font-size:12px;color:var(--gm);font-weight:600;text-decoration:none;">View all notifications <i class="fas fa-arrow-right" style="margin-left:3px;font-size:10px;"></i></a>
        </div>
    </div>
</div>

<a href="<?php echo e(route('admin.settings.index')); ?>" class="tp-btn"><i class="fas fa-cog"></i></a>
</div>
</header>
<div class="page">
<?php if(session('success')): ?><div class="alert al-s an"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="alert al-d an"><i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?></div><?php endif; ?>
<?php if(session('ai')): ?><div class="alert al-ai an"><i class="fas fa-robot"></i> <?php echo e(session('ai')); ?></div><?php endif; ?>
<?php echo $__env->yieldContent('content'); ?>
</div></div>
<script>
const sbToggle=document.getElementById('sbToggle'),sidebar=document.getElementById('sidebar');
sbToggle.addEventListener('click',()=>sidebar.classList.toggle('open'));
function chk(){if(window.innerWidth<=900)sbToggle.style.display='flex';else{sbToggle.style.display='none';sidebar.classList.remove('open');}}
chk();window.addEventListener('resize',chk);
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
document.addEventListener('click',e=>{if(e.target.classList.contains('mo'))e.target.classList.remove('open');});

// ── Admin Notification Dropdown ──────────────────────────────────────────────
(function(){
    var DROPDOWN_URL = '<?php echo e(route("admin.notifications.dropdown")); ?>';
    var MARK_ALL_URL = '<?php echo e(route("admin.notifications.mark-all")); ?>';
    var MARK_READ_BASE = '<?php echo e(url("admin/notifications")); ?>';
    var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var open = false, loaded = false;

    function iconCfg(type){
        var m={scholarship:{i:'fas fa-award',bg:'#d0f0d8',c:'#1a6b2f'},application:{i:'fas fa-file-alt',bg:'#dce8ff',c:'#0038a8'},counseling:{i:'fas fa-comments',bg:'#fef3cd',c:'#a07c00'},announcement:{i:'fas fa-bullhorn',bg:'#fde8e6',c:'#c0392b'},system:{i:'fas fa-cog',bg:'#f0f0f0',c:'#5a5a5a'}};
        return m[type]||{i:'fas fa-bell',bg:'#e8f5ec',c:'#2d9e4f'};
    }
    function timeAgo(d){var s=Math.floor((Date.now()-new Date(d))/1000);if(s<60)return'just now';if(s<3600)return Math.floor(s/60)+'m ago';if(s<86400)return Math.floor(s/3600)+'h ago';return Math.floor(s/86400)+'d ago';}
    function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}

    function load(){
        fetch(DROPDOWN_URL,{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
        .then(function(r){return r.json();})
        .then(function(data){
            document.getElementById('adminNotifLoading').style.display='none';
            var notifs=data.notifications||[], unread=data.unread||0;
            var badge=document.getElementById('adminNotifBadge');
            var ul=document.getElementById('adminNotifUnread');
            var mb=document.getElementById('adminMarkAllBtn');
            var list=document.getElementById('adminNotifList');
            var empty=document.getElementById('adminNotifEmpty');
            if(unread>0){badge.style.display='flex';badge.textContent=unread>99?'99+':unread;ul.textContent=unread+' unread';mb.style.display='block';}
            else{badge.style.display='none';ul.textContent='All read';mb.style.display='none';}
            if(notifs.length===0){empty.style.display='block';list.style.display='none';loaded=true;return;}
            list.style.display='block';empty.style.display='none';list.innerHTML='';
            notifs.forEach(function(n){
                var cfg=iconCfg(n.type), isNew=!n.read;
                var el=document.createElement('div');
                el.className='notif-item'+(isNew?' unread':'');
                el.onclick=function(){markOne(n.id,this);};
                el.innerHTML='<div class="notif-icon" style="background:'+cfg.bg+';color:'+cfg.c+';"><i class="'+cfg.i+'"></i></div>'
                +'<div style="flex:1;min-width:0;">'
                +'<div style="font-size:13px;font-weight:'+(isNew?'700':'500')+';color:var(--tx);line-height:1.4;margin-bottom:2px;">'+esc(n.title||'Notification')+'</div>'
                +'<div style="font-size:12px;color:var(--tm);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:260px;">'+esc(n.message||n.body||'')+'</div>'
                +'<div style="font-size:10px;color:var(--tm);margin-top:4px;">'+(isNew?'<span style="width:6px;height:6px;background:var(--gm);border-radius:50%;display:inline-block;margin-right:4px;"></span>':'')+timeAgo(n.created_at)+'</div>'
                +'</div>';
                list.appendChild(el);
            });
            loaded=true;
        }).catch(function(){document.getElementById('adminNotifLoading').style.display='none';});
    }

    function markOne(id, el){
        fetch(MARK_READ_BASE+'/'+id,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},body:JSON.stringify({_method:'PATCH'})})
        .then(function(){
            if(el){
                el.classList.remove('unread');
                var title=el.querySelector('[style*="font-weight"]');
                if(title)title.style.fontWeight='500';
                var dot=el.querySelector('[style*="border-radius:50%;display:inline-block"]');
                if(dot)dot.remove();
                var badge=document.getElementById('adminNotifBadge');
                var cur=parseInt(badge.textContent)||0;
                if(cur>1){badge.textContent=cur-1;}
                else{badge.style.display='none';document.getElementById('adminMarkAllBtn').style.display='none';document.getElementById('adminNotifUnread').textContent='All read';}
            }
        });
    }

    window.adminMarkAll=function(){
        fetch(MARK_ALL_URL,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},body:JSON.stringify({})})
        .then(function(){loaded=false;document.getElementById('adminNotifLoading').style.display='block';document.getElementById('adminNotifList').style.display='none';load();});
    };

    window.toggleAdminNotif=function(){
        var dd=document.getElementById('adminNotifDropdown');
        open=!open;dd.style.display=open?'block':'none';
        if(open&&!loaded)load();
    };

    document.addEventListener('click',function(e){
        if(!open)return;
        var w=document.getElementById('adminNotifWrapper');
        if(w&&!w.contains(e.target)){document.getElementById('adminNotifDropdown').style.display='none';open=false;}
    });

    setInterval(function(){
        if(!DROPDOWN_URL)return;
        fetch(DROPDOWN_URL,{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
        .then(function(r){return r.json();})
        .then(function(data){
            var badge=document.getElementById('adminNotifBadge');
            var u=data.unread||0;
            if(u>0){badge.style.display='flex';badge.textContent=u>99?'99+':u;}
            else{badge.style.display='none';}
            if(open){loaded=false;load();}
        }).catch(function(){});
    },60000);
})();
</script>

<script>
    (function(){
        // Apply saved theme (system/light/dark) and resolve "system" using OS preference.
        var body=document.body;
        if(!body) return;

        var savedTheme=<?php echo json_encode(auth()->user()->theme ?? 'system', 15, 512) ?>;
        var resolved=savedTheme;
        if(savedTheme==='system'){
            var prefersDark=window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches;
            resolved=prefersDark?'dark':'light';
        }
        body.setAttribute('data-theme',resolved);

        // Theme toggle button state
        var btn=document.getElementById('themeToggleBtn');
        var icon=document.getElementById('themeToggleIcon');
        if(btn){
            var cycle=['system','light','dark'];
            var idx=Math.max(0,cycle.indexOf(savedTheme));

            function setIcon(){
                if(!icon) return;
                var map={system:'fas fa-laptop', light:'fas fa-sun', dark:'fas fa-moon'};
                icon.className=map[savedTheme]||'fas fa-adjust';
                btn.title='Theme: '+savedTheme.charAt(0).toUpperCase()+savedTheme.slice(1);
            }

            setIcon();

            btn.addEventListener('click',function(){
                var next=cycle[(idx+1)%cycle.length];
                idx=(idx+1)%cycle.length;
                savedTheme=next;

                var nextResolved=next;
                if(next==='system'){
                    var pDark=window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches;
                    nextResolved=pDark?'dark':'light';
                }
                body.setAttribute('data-theme',nextResolved);
                setIcon();

                var csrf=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('<?php echo e(route('settings.theme.update')); ?>',{
                    method:'POST',
                    headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':csrf,
                        'X-Requested-With':'XMLHttpRequest'
                    },
                    body:JSON.stringify({theme:next})
                }).catch(function(){});
            });
        }
    })();
</script>


<?php echo $__env->yieldPushContent('scripts'); ?>
<script src="<?php echo e(asset('js/isams-ajax.js')); ?>"></script>
</body></html>
<?php /**PATH C:\Users\Acer\Herd\isams\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>