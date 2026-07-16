<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('favicon-32x32.png')); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('favicon-16x16.png')); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('favicon-180x180.png')); ?>">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"><title>ISAMS Super Admin — <?php echo $__env->yieldContent('title','Dashboard'); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{--g:#1a6b2f;--gm:#2d9e4f;--y:#f0c020;--yd:#c9a010;--danger:#c0392b;--warn:#d68910;--info:#1a6b8a;--bg:#0a0f0a;--card:#111a11;--card2:#162016;--sb:#060d06;--st:#5a9a6a;--sh:#1a4020;--tx:#e0f0e0;--tm:#5a8a6a;--bd:#1e3a1e;--shadow:0 2px 16px rgba(0,0,0,.4);--r:12px;--rs:8px;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--tx);min-height:100vh;display:flex;font-size:15px;}
.sidebar{width:262px;min-width:262px;background:var(--sb);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100;overflow-y:auto;border-right:1px solid var(--bd);}
.sb-brand{padding:22px 18px 16px;border-bottom:1px solid var(--bd);}
.sb-logo{display:flex;align-items:center;gap:11px;}
.sb-icon{width:52px;height:52px;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
.sb-icon img{width:100%;height:100%;object-fit:contain;}
.sb-name{font-family:'Sora',sans-serif;font-size:15px;font-weight:800;color:#fff;line-height:1.1;}
.sb-sub{font-size:10px;color:var(--y);text-transform:uppercase;letter-spacing:1px;font-weight:700;}
.sa-badge{display:flex;align-items:center;gap:7px;margin:12px 12px 0;background:rgba(240,192,32,.08);border:1px solid rgba(240,192,32,.2);border-radius:8px;padding:8px 12px;}
.sa-dot{width:7px;height:7px;border-radius:50%;background:var(--y);box-shadow:0 0 8px var(--y);flex-shrink:0;}
.sa-badge span{font-size:11px;color:var(--y);font-weight:700;}
.sa-v{margin-left:auto;font-size:10px;color:rgba(240,192,32,.4);}
.sb-sec{padding:14px 10px 4px;}
.sb-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(90,154,106,.4);padding:0 8px 7px;}
.nav-a{display:flex;align-items:center;gap:11px;padding:9px 10px;border-radius:var(--rs);color:var(--st);text-decoration:none;font-size:13.5px;font-weight:500;transition:all .2s;margin-bottom:2px;}
.nav-a i{width:17px;text-align:center;font-size:14px;}
.nav-a:hover{background:var(--sh);color:#fff;}
.nav-a.active{background:var(--sh);color:var(--y);}
.nav-badge{margin-left:auto;background:var(--y);color:#0d3318;font-size:10px;font-weight:800;padding:2px 7px;border-radius:20px;line-height:1.4;}
.nav-badge.r{background:var(--danger);color:#fff;}
.sb-footer{margin-top:auto;padding:13px 10px;border-top:1px solid var(--bd);}
.user-card{display:flex;align-items:center;gap:9px;padding:9px 10px;background:rgba(255,255,255,.04);border-radius:var(--rs);}
.u-av{width:34px;height:34px;background:linear-gradient(135deg,var(--y),var(--yd));border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;color:#0d3318;}
.u-name{font-size:13px;font-weight:600;color:#fff;}
.u-role{font-size:10px;color:var(--y);font-weight:700;}
.u-out{margin-left:auto;color:var(--st);text-decoration:none;font-size:14px;transition:color .2s;}
.u-out:hover{color:var(--danger);}
.main{margin-left:262px;flex:1;display:flex;flex-direction:column;min-height:100vh;}
.topbar{background:var(--card);border-bottom:2px solid var(--y);padding:0 26px;height:62px;display:flex;align-items:center;gap:14px;position:sticky;top:0;z-index:90;box-shadow:0 2px 16px rgba(0,0,0,.3);}
.tp-title{font-family:'Sora',sans-serif;font-size:17px;font-weight:700;color:var(--y);}
.tp-sub{font-size:11px;color:var(--tm);}
.tp-right{margin-left:auto;display:flex;align-items:center;gap:10px;}
.sa-chip{background:linear-gradient(135deg,var(--y),var(--yd));color:#0d3318;padding:5px 13px;border-radius:20px;font-size:11px;font-weight:800;display:inline-flex;align-items:center;gap:6px;}
.sa-chip img{width:16px;height:16px;object-fit:contain;}
.tp-btn{width:37px;height:37px;background:var(--card2);border:1.5px solid var(--bd);border-radius:var(--rs);display:flex;align-items:center;justify-content:center;color:var(--tm);cursor:pointer;text-decoration:none;transition:all .2s;}
.tp-btn:hover{background:var(--g);color:#fff;border-color:var(--g);}
.mob-toggle{display:none;background:none;border:1.5px solid var(--bd);border-radius:var(--rs);width:37px;height:37px;align-items:center;justify-content:center;color:var(--tm);cursor:pointer;font-size:16px;}
.page{padding:24px 26px;flex:1;}
.card{background:var(--card);border-radius:var(--r);border:1px solid var(--bd);box-shadow:var(--shadow);overflow:hidden;}
.ch{padding:15px 20px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:10px;}
.ch h2{font-family:'Sora',sans-serif;font-size:14px;font-weight:700;color:var(--y);}
.ch-acts{margin-left:auto;display:flex;gap:8px;}
.cb{padding:20px;}
.sg{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:20px;}
.sc{background:var(--card);border-radius:var(--r);border:1px solid var(--bd);padding:16px 18px;box-shadow:var(--shadow);display:flex;align-items:center;gap:13px;transition:transform .2s;}
.sc:hover{transform:translateY(-2px);}
.si{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0;}
.si.y{background:rgba(240,192,32,.15);color:var(--y);}.si.g{background:rgba(45,158,79,.15);color:var(--gm);}.si.r{background:rgba(192,57,43,.15);color:var(--danger);}.si.t{background:rgba(26,107,138,.15);color:var(--info);}.si.o{background:rgba(214,137,16,.15);color:var(--warn);}.si.w{background:rgba(255,255,255,.08);color:#fff;}
.sv .lbl{font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
.sv .val{font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#fff;line-height:1.1;}
.sv .chg{font-size:11px;color:var(--gm);margin-top:1px;}.sv .chg.dn{color:var(--danger);}
.tw{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
thead th{background:var(--card2);padding:9px 13px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--tm);text-align:left;border-bottom:1px solid var(--bd);}
tbody tr{transition:background .15s;}tbody tr:hover{background:var(--card2);}
tbody td{padding:11px 13px;font-size:13px;border-bottom:1px solid var(--bd);color:var(--tx);}
tbody tr:last-child td{border-bottom:none;}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.b-s{background:rgba(45,158,79,.2);color:#6dd98e;}.b-d{background:rgba(192,57,43,.2);color:#e87070;}.b-w{background:rgba(214,137,16,.2);color:#f0b429;}.b-y{background:rgba(240,192,32,.2);color:var(--y);}.b-i{background:rgba(26,107,138,.2);color:#60b0d8;}.b-gray{background:rgba(255,255,255,.08);color:var(--tm);}.b-g{background:rgba(45,158,79,.2);color:#6dd98e;}.b-sa{background:linear-gradient(135deg,rgba(240,192,32,.25),rgba(240,192,32,.1));color:var(--y);border:1px solid rgba(240,192,32,.3);}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--rs);font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all .2s;font-family:inherit;}
.btn-p{background:var(--g);color:#fff;}.btn-p:hover{background:var(--gm);}
.btn-ac{background:var(--y);color:#0d3318;}.btn-ac:hover{background:#f7d84a;}
.btn-s{background:var(--gm);color:#fff;}.btn-d{background:var(--danger);color:#fff;}
.btn-o{background:transparent;border:1.5px solid var(--bd);color:var(--tm);}.btn-o:hover{border-color:var(--y);color:var(--y);}
.btn-w{background:rgba(255,255,255,.08);border:1px solid var(--bd);color:var(--tx);}.btn-w:hover{background:rgba(255,255,255,.15);}
.btn-sm{padding:5px 12px;font-size:12px;}.btn-ic{padding:6px 9px;}
.fg{margin-bottom:15px;}.fl{display:block;font-size:13px;font-weight:600;color:var(--tx);margin-bottom:5px;}
.fc{width:100%;padding:9px 13px;border:1.5px solid var(--bd);border-radius:var(--rs);font-size:13px;color:var(--tx);background:var(--card2);transition:border-color .2s;font-family:inherit;}
.fc:focus{outline:none;border-color:var(--y);box-shadow:0 0 0 3px rgba(240,192,32,.1);}
select.fc option{background:var(--card);}
.alert{padding:11px 15px;border-radius:var(--rs);margin-bottom:14px;font-size:13px;display:flex;align-items:center;gap:8px;}
.al-s{background:rgba(45,158,79,.15);border-left:4px solid var(--gm);color:#6dd98e;}.al-d{background:rgba(192,57,43,.15);border-left:4px solid var(--danger);color:#e87070;}.al-w{background:rgba(214,137,16,.15);border-left:4px solid var(--warn);color:#f0b429;}.al-y{background:rgba(240,192,32,.1);border-left:4px solid var(--y);color:var(--y);}
.mo{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px);}
.mo.open{display:flex;}
.mb{background:var(--card);border-radius:16px;max-width:580px;width:95%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.5);border:1px solid var(--bd);animation:min .25s ease;}
@keyframes min{from{opacity:0;transform:scale(.94) translateY(16px)}to{opacity:1;transform:scale(1) translateY(0)}}
.mh{padding:18px 22px 14px;border-bottom:2px solid var(--y);display:flex;align-items:center;gap:11px;position:sticky;top:0;background:var(--card);z-index:1;}
.mh h3{font-family:'Sora',sans-serif;font-size:17px;font-weight:700;color:var(--y);}
.mc{margin-left:auto;background:none;border:none;font-size:18px;color:var(--tm);cursor:pointer;}.mc:hover{color:var(--danger);}
.mbody{padding:20px 22px;}.mfoot{padding:13px 22px;border-top:1px solid var(--bd);display:flex;gap:8px;justify-content:flex-end;}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}.g3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
.mt3{margin-top:18px;}.mb3{margin-bottom:18px;}.mb2{margin-bottom:12px;}
.tm{color:var(--tm);}.fwb{font-weight:700;}.fws{font-weight:600;}.mono{font-family:'JetBrains Mono',monospace;}
.av{border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;}
.av-s{width:30px;height:30px;font-size:12px;background:rgba(240,192,32,.15);color:var(--y);}
.online-dot{width:8px;height:8px;border-radius:50%;background:var(--gm);box-shadow:0 0 6px var(--gm);display:inline-block;margin-right:5px;}
.offline-dot{width:8px;height:8px;border-radius:50%;background:var(--tm);display:inline-block;margin-right:5px;}
.an{animation:fadeUp .35s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(13px)}to{opacity:1;transform:translateY(0)}}
.d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}
::-webkit-scrollbar{width:5px;height:5px;}::-webkit-scrollbar-track{background:var(--card);}::-webkit-scrollbar-thumb{background:var(--bd);border-radius:10px;}
@media(max-width:900px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}.main{margin-left:0}.mob-toggle{display:flex}.g2,.g3{grid-template-columns:1fr}}
</style><?php echo $__env->yieldPushContent('styles'); ?></head>
<body data-theme="light">
<aside class="sidebar" id="sidebar">
<div class="sb-brand"><div class="sb-logo"><div class="sb-icon"><img src="<?php echo e(asset('images/SCC_NEW_LOGO.png')); ?>" alt="Logo"></div><div><div class="sb-name">ISAMS</div><div class="sb-sub">⚡ Super Admin</div></div></div></div>
<div class="sa-badge"><div class="sa-dot"></div><span>Super Admin Panel</span><span class="sa-v">v2.1</span></div>
<div class="sb-sec"><div class="sb-lbl">Overview</div>
<a href="<?php echo e(route('superadmin.dashboard')); ?>" class="nav-a <?php echo e(request()->routeIs('superadmin.dashboard')?'active':''); ?>"><i class="fas fa-th-large"></i> Dashboard</a>
<a href="<?php echo e(route('superadmin.monitoring')); ?>" class="nav-a <?php echo e(request()->routeIs('superadmin.monitoring')?'active':''); ?>"><i class="fas fa-chart-line"></i> Live Monitoring<span class="nav-badge">LIVE</span></a></div>
<div class="sb-sec"><div class="sb-lbl">User Management</div>
<a href="<?php echo e(route('superadmin.users.index')); ?>" class="nav-a <?php echo e(request()->routeIs('superadmin.users.*')?'active':''); ?>"><i class="fas fa-users"></i> All Users</a>
<a href="<?php echo e(route('superadmin.users.create')); ?>" class="nav-a <?php echo e(request()->routeIs('superadmin.users.create')?'active':''); ?>"><i class="fas fa-user-plus"></i> Create User</a></div>
<div class="sb-sec"><div class="sb-lbl">Logs & Audit</div>
<a href="<?php echo e(route('superadmin.logs')); ?>" class="nav-a <?php echo e(request()->routeIs('superadmin.logs')?'active':''); ?>"><i class="fas fa-history"></i> Login Logs</a>
<a href="<?php echo e(route('superadmin.logs')); ?>?status=failed" class="nav-a"><i class="fas fa-exclamation-triangle"></i> Failed Logins<span class="nav-badge r">!</span></a></div>
<div class="sb-sec"><div class="sb-lbl">System</div>
<a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-a" target="_blank"><i class="fas fa-external-link-alt"></i> Admin Panel</a>
<a href="<?php echo e(route('superadmin.settings')); ?>" class="nav-a <?php echo e(request()->routeIs('superadmin.settings')?'active':''); ?>"><i class="fas fa-cog"></i> Settings</a></div>
<div class="sb-footer"><div class="user-card"><div class="u-av"><?php echo e(strtoupper(substr(auth()->user()->name??'S',0,1))); ?></div><div><div class="u-name"><?php echo e(auth()->user()->name??'Super Admin'); ?></div><div class="u-role">⚡ SUPER ADMIN</div></div><a href="<?php echo e(route('logout')); ?>" class="u-out" onclick="event.preventDefault();document.getElementById('salf').submit();" title="Logout"><i class="fas fa-sign-out-alt"></i></a></div><form id="salf" action="<?php echo e(route('logout')); ?>" method="POST" style="display:none;"><?php echo csrf_field(); ?></form></div>
</aside>
<div class="main">
<div class="tp-right"><span class="sa-chip"><img src="<?php echo e(asset('images/SCC_NEW_LOGO.png')); ?>" alt="Logo">Super Admin</span>


<?php echo $__env->make('components.theme-toggle', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<a href="<?php echo e(route('superadmin.logs')); ?>" class="tp-btn"><i class="fas fa-history"></i></a><a href="<?php echo e(route('superadmin.settings')); ?>" class="tp-btn"><i class="fas fa-cog"></i></a></div></header>

<div class="page">
<?php if(session('success')): ?><div class="alert al-s an"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="alert al-d an"><i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?></div><?php endif; ?>
<?php if(session('info')): ?><div class="alert al-y an"><i class="fas fa-info-circle"></i> <?php echo e(session('info')); ?></div><?php endif; ?>
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

// Apply saved theme and wire up toggle (system/light/dark)
(function(){
    var body=document.body;
    if(!body) return;

    var savedTheme=<?php echo json_encode(auth()->user()->theme ?? 'system', 15, 512) ?>;
    var resolved=savedTheme;
    if(savedTheme==='system'){
        var pDark=window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches;
        resolved=pDark?'dark':'light';
    }
    body.setAttribute('data-theme',resolved);

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
                var pDark2=window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches;
                nextResolved=pDark2?'dark':'light';
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
</script><?php echo $__env->yieldPushContent('scripts'); ?>

<script src="<?php echo e(asset('js/isams-ajax.js')); ?>"></script>
</body></html><?php /**PATH C:\Users\Acer\Herd\isams\resources\views/superadmin/layouts/app.blade.php ENDPATH**/ ?>