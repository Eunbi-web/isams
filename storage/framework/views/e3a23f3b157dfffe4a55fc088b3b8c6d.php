<!DOCTYPE html><html lang="en">
    <head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('favicon-32x32.png')); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('favicon-16x16.png')); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('favicon-180x180.png')); ?>">
<title>ISAMS — Sign In</title><link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{--g:#1a6b2f;--gm:#2d9e4f;--y:#f0c020;--yd:#c9a010;--danger:#c0392b;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;}
body{
    font-family:'DM Sans',sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
    background-image:url('<?php echo e(asset('images/SCC_College_building.jpg')); ?>');
    background-size:cover;
    background-position:center;
    padding:20px;
}
body::before{
    content:'';
    position:fixed;
    inset:0;
    background:rgba(20,30,20,.55);
    backdrop-filter:blur(2px);
    z-index:0;
}
.login-card{
    position:relative;
    z-index:1;
    background:#fff;
    border-radius:18px;
    width:100%;
    max-width:420px;
    padding:38px 40px 34px;
    box-shadow:0 24px 70px rgba(0,0,0,.35);
    border:1px solid rgba(255,255,255,.4);
    animation:fadeUp .4s ease both;
}
@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.logo-wrap{display:flex;flex-direction:column;align-items:center;margin-bottom:18px;}
.logo-circle{
    width:92px;height:92px;
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    margin-bottom:12px;
    overflow:hidden;
    background:#fff;
    box-shadow:0 6px 18px rgba(0,0,0,.15);
}
.logo-circle img{
    width:100%;
    height:100%;
    object-fit:contain;
}
.school-name{
    font-family:'Sora',sans-serif;
    font-size:19px;
    font-weight:800;
    color:#1a2e1a;
    text-align:center;
    line-height:1.3;
}
.school-sub{
    font-size:12px;
    color:#5a7a60;
    text-align:center;
    margin-top:2px;
    text-transform:uppercase;
    letter-spacing:.5px;
}
.alert-err{
    padding:10px 13px;
    background:#fde8e6;
    border-left:4px solid var(--danger);
    border-radius:8px;
    color:#7a1a14;
    font-size:13px;
    margin-bottom:16px;
    display:flex;
    align-items:center;
    gap:8px;
}
.fg{margin-bottom:16px;}
.fl{display:block;font-size:13px;font-weight:600;color:#1a2e1a;margin-bottom:6px;}
.iw{position:relative;}
.iw i.fa-envelope, .iw i.fa-lock{
    position:absolute;left:13px;top:50%;transform:translateY(-50%);
    color:#9ab8a0;font-size:14px;pointer-events:none;
}
.fc{
    width:100%;
    padding:11px 13px 11px 38px;
    border:1.5px solid #d8e8dc;
    border-radius:9px;
    font-size:14px;
    font-family:inherit;
    color:#1a2e1a;
    background:#f7faf8;
    transition:border-color .2s,box-shadow .2s,background .2s;
}
.fc:focus{outline:none;border-color:var(--gm);box-shadow:0 0 0 3px rgba(45,158,79,.12);background:#fff;}
.toggle-pw{
    position:absolute;right:13px;top:50%;transform:translateY(-50%);
    background:none;border:none;color:#9ab8a0;cursor:pointer;font-size:14px;padding:0;
}
.opts{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.opts label{display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;color:#5a7a60;}
.opts input[type=checkbox]{accent-color:var(--g);width:14px;height:14px;}
.opts a{font-size:13px;color:var(--gm);text-decoration:none;font-weight:500;}
.opts a:hover{text-decoration:underline;}
.btn-login{
    width:100%;
    padding:12px;
    background:linear-gradient(135deg,var(--g),var(--gm));
    color:#fff;
    border:none;
    border-radius:9px;
    font-size:15px;
    font-weight:700;
    font-family:'Sora',sans-serif;
    cursor:pointer;
    transition:all .2s;
}
.btn-login:hover{opacity:.93;transform:translateY(-1px);box-shadow:0 8px 20px rgba(26,107,47,.3);}
.footer-note{
    text-align:center;
    margin-top:20px;
    font-size:13px;
    color:#7a9a80;
}
</style></head>
<body>
<div class="login-card">

    <div class="logo-wrap">
        <div class="logo-circle">
            <img src="<?php echo e(asset('images/SCC_NEW_LOGO.png')); ?>" alt="Saint Columban College Logo">
        </div>
        <div class="school-name">Saint Columban College</div>
        <div class="school-sub">ISAMS Portal</div>
    </div>

    <?php if($errors->any()): ?>
    <div class="alert-err"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <?php echo csrf_field(); ?>

        <div class="fg">
            <label class="fl">Email</label>
            <div class="iw">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="fc" value="<?php echo e(old('email')); ?>" placeholder="Enter your email" required autofocus>
            </div>
        </div>

        <div class="fg">
            <label class="fl">Password</label>
            <div class="iw">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="passwordField" class="fc" placeholder="Enter your password" required>
                <button type="button" class="toggle-pw" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </button>
            </div>
        </div>

        <div class="opts">
            <label><input type="checkbox" name="remember"> Remember me?</label>
            <a href="<?php echo e(route('password.request')); ?>">Forgot password?</a>
        </div>

        <button type="submit" class="btn-login">Log in</button>
    </form>

    <div class="footer-note">
        Confidential - Do Not Distribute
    </div>

</div>

<script>
function togglePassword(){
    const field = document.getElementById('passwordField');
    const icon  = document.getElementById('toggleIcon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
</body></html>
<?php /**PATH C:\Users\Acer\Herd\isams\resources\views/auth/login.blade.php ENDPATH**/ ?>