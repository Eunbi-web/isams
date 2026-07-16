<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ISAMS — @yield('title', 'Login')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a3a5c;
            --primary-light: #2a5298;
            --accent: #e8b84b;
            --bg: #f0f4f8;
            --border: #d0dce8;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #0d2137 0%, #1a3a5c 50%, #2a5298 100%);
        }
        .auth-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(232,184,75,0.12), transparent 70%);
            top: -100px; left: -100px;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(42,82,152,0.3), transparent 70%);
            bottom: -50px; right: 50px;
        }
        .auth-hero {
            position: relative; z-index: 1;
            text-align: center; color: #fff;
            max-width: 420px;
        }
        .auth-hero-icon {
            width: 90px; height: 90px;
            background: linear-gradient(135deg, var(--accent), #c8972b);
            border-radius: 24px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 40px; color: #fff;
            margin-bottom: 28px;
            box-shadow: 0 12px 40px rgba(232,184,75,0.4);
        }
        .auth-hero h1 {
            font-family: 'Sora', sans-serif;
            font-size: 42px; font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 10px;
        }
        .auth-hero h1 span { color: var(--accent); }
        .auth-hero p {
            font-size: 15px; opacity: 0.75;
            line-height: 1.6; margin-bottom: 36px;
        }
        .feature-list {
            display: flex; flex-direction: column; gap: 12px;
            text-align: left;
        }
        .feature-item {
            display: flex; align-items: center; gap: 12px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; padding: 12px 16px;
        }
        .feature-item i {
            color: var(--accent); font-size: 16px; width: 20px;
        }
        .feature-item span { font-size: 13px; opacity: 0.9; }

        .auth-right {
            width: 480px; min-width: 400px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 48px 52px;
        }
        .auth-form { width: 100%; }
        .auth-form .form-header { margin-bottom: 32px; }
        .auth-form .form-header h2 {
            font-family: 'Sora', sans-serif;
            font-size: 26px; font-weight: 700;
            color: var(--primary);
        }
        .auth-form .form-header p { font-size: 14px; color: #5a7a94; margin-top: 6px; }
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 13px; font-weight: 600;
            color: #1c2b3a; margin-bottom: 7px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #8a9db5; font-size: 14px;
        }
        .form-control {
            width: 100%; padding: 12px 14px 12px 42px;
            border: 1.5px solid var(--border);
            border-radius: 10px; font-size: 14px;
            font-family: inherit; color: #1c2b3a;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            outline: none; border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(42,82,152,0.1);
        }
        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700;
            cursor: pointer; font-family: 'Sora', sans-serif;
            transition: opacity .2s, transform .2s;
            margin-top: 8px;
        }
        .btn-submit:hover { opacity: .92; transform: translateY(-1px); }
        .form-footer { text-align: center; margin-top: 22px; font-size: 13px; color: #5a7a94; }
        .alert-danger {
            padding: 12px 16px; background: #fde8e6;
            border-left: 4px solid #c0392b; border-radius: 8px;
            color: #7a1a14; font-size: 13px; margin-bottom: 18px;
        }
        .divider { display: flex; align-items: center; gap: 14px; margin: 20px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .divider span { font-size: 12px; color: #8a9db5; white-space: nowrap; }
        .demo-badge {
            display: inline-block;
            background: #fef6e0; color: #8a6200;
            border: 1px solid #f5d07a; border-radius: 8px;
            padding: 8px 14px; font-size: 12px; font-weight: 500;
            width: 100%; text-align: center;
        }
        @media (max-width: 900px) {
            .auth-left { display: none; }
            .auth-right { width: 100%; min-width: unset; }
        }
    </style>
</head>
<body>
<div class="auth-left">
    <div class="auth-hero">
        <div class="auth-hero-icon"><i class="fas fa-graduation-cap"></i></div>
        <h1>I<span>SAMS</span></h1>
        <p>Integrated Student Affairs Management System — a unified platform for managing all aspects of student life and welfare.</p>
        <div class="feature-list">
            <div class="feature-item"><i class="fas fa-user-graduate"></i><span>Student Records & Enrollment Tracking</span></div>
            <div class="feature-item"><i class="fas fa-heart"></i><span>Counseling & Mental Wellness Services</span></div>
            <div class="feature-item"><i class="fas fa-gavel"></i><span>Discipline Case Management</span></div>
            <div class="feature-item"><i class="fas fa-award"></i><span>Scholarships & Financial Aid</span></div>
            <div class="feature-item"><i class="fas fa-users"></i><span>Student Organizations & Activities</span></div>
        </div>
    </div>
</div>
<div class="auth-right">
    @yield('content')
</div>
</body>
</html>
