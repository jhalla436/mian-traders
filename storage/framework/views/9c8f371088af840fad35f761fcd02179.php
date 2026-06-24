<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'Mian Traders')); ?> — Login</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
                min-height: 100vh;
                display: flex; align-items: center; justify-content: center;
                background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
                color: #0f172a; -webkit-font-smoothing: antialiased;
                position: relative; overflow: hidden;
            }
            body::before {
                content: ''; position: absolute; top: -50%; left: -50%;
                width: 200%; height: 200%;
                background: radial-gradient(circle at 30% 40%, rgba(99,102,241,0.15) 0%, transparent 50%),
                            radial-gradient(circle at 70% 60%, rgba(168,85,247,0.1) 0%, transparent 50%);
                animation: bgFloat 20s ease-in-out infinite alternate;
            }
            @keyframes bgFloat {
                0% { transform: translate(0, 0) rotate(0deg); }
                100% { transform: translate(-2%, 2%) rotate(3deg); }
            }
            .login-container { position: relative; z-index: 1; width: 100%; max-width: 420px; padding: 24px; }
            .login-brand { display: flex; flex-direction: column; align-items: center; margin-bottom: 32px; }
            .login-brand-icon {
                width: 64px; height: 64px;
                background: linear-gradient(135deg, #6366f1, #a855f7);
                border-radius: 18px; display: flex; align-items: center; justify-content: center;
                font-weight: 900; font-size: 26px; color: #fff;
                box-shadow: 0 8px 32px rgba(99,102,241,0.4); margin-bottom: 16px;
            }
            .login-brand-name { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
            .login-brand-sub { font-size: 13px; color: #94a3b8; margin-top: 4px; font-weight: 500; }
            .login-card {
                background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
                border-radius: 20px; padding: 36px 32px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.15);
            }
            .login-title { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 24px; text-align: center; }
            .form-group { margin-bottom: 18px; }
            .form-label { display: block; font-size: 12px; font-weight: 600; color: #475569; letter-spacing: 0.3px; margin-bottom: 6px; text-transform: uppercase; }
            .form-input {
                width: 100%; padding: 12px 16px; border: 1.5px solid #e2e8f0; border-radius: 12px;
                font-family: inherit; font-size: 14px; font-weight: 500; color: #0f172a;
                background: #fff; outline: none; transition: border-color 0.2s, box-shadow 0.2s;
            }
            .form-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
            .form-input::placeholder { color: #94a3b8; font-weight: 400; }
            .form-error { color: #dc2626; font-size: 12px; margin-top: 4px; font-weight: 500; }
            .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
            .remember-row input[type="checkbox"] { width: 18px; height: 18px; border-radius: 5px; accent-color: #6366f1; cursor: pointer; }
            .remember-row label { font-size: 13px; color: #64748b; cursor: pointer; }
            .login-btn {
                width: 100%; padding: 14px; border: none; border-radius: 12px;
                background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff;
                font-family: inherit; font-size: 15px; font-weight: 700; cursor: pointer;
                transition: all 0.2s; letter-spacing: 0.2px;
            }
            .login-btn:hover { background: linear-gradient(135deg, #818cf8, #6366f1); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(99,102,241,0.4); }
            .login-btn:active { transform: translateY(0); }
            .forgot-link { display: block; text-align: center; margin-top: 16px; font-size: 13px; color: #6366f1; text-decoration: none; font-weight: 600; transition: color 0.2s; }
            .forgot-link:hover { color: #4f46e5; }
            .session-status { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #6ee7b7; color: #065f46; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 500; text-align: center; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-brand">
                <div class="login-brand-icon">MT</div>
                <div class="login-brand-name">Mian Traders</div>
                <div class="login-brand-sub">Business Management System</div>
            </div>
            <div class="login-card">
                <?php echo e($slot); ?>

            </div>
        </div>
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views\layouts\guest.blade.php ENDPATH**/ ?>