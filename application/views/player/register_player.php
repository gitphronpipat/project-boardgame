<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก — Board Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --theme-color: #818cf8;
            --theme-hover: #6366f1;
        }
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-card {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.1);
            margin-top: -40px;
        }
        .register-header {
            background: var(--theme-color);
            padding: 40px 20px;
            color: white;
            text-align: center;
        }
        .register-header i { font-size: 2.5rem; margin-bottom: 12px; opacity: 0.95; }
        .register-header h4 { font-weight: 600; letter-spacing: 0.5px; margin-bottom: 5px; }
        .register-header p { font-size: 0.9rem; opacity: 0.8; }

        .card-body { padding: 40px !important; }

        .form-label { font-size: 0.9rem; font-weight: 500; color: rgba(255,255,255,0.7); margin-left: 2px; }

        .input-group-text {
            background-color: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            border-right: none !important;
            color: rgba(255,255,255,0.4);
            border-radius: 12px 0 0 12px !important;
        }
        .form-control {
            border-radius: 0 12px 12px 0 !important;
            padding: 12px;
            border: 1px solid rgba(255,255,255,0.1) !important;
            background-color: rgba(255,255,255,0.05) !important;
            color: #fff !important;
            font-size: 0.95rem;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.3); }
        .form-control:focus {
            background-color: rgba(255,255,255,0.08) !important;
            border-color: var(--theme-color) !important;
            box-shadow: none;
        }

        .btn-register {
            background-color: var(--theme-color);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: white;
            margin-top: 10px;
        }
        .btn-register:hover {
            background-color: var(--theme-hover);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(129, 140, 248, 0.25);
            color: white;
        }

        .login-link { color: var(--theme-color); text-decoration: none; font-weight: 600; }
        .login-link:hover { text-decoration: underline; color: var(--theme-hover); }
    </style>
</head>
<body>

<?php $this->load->view('theme/notify'); ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card register-card shadow-sm">
                <div class="register-header">
                    <i class="fa-solid fa-gamepad"></i>
                    <h4>สมัครสมาชิก</h4>
                    <p class="mb-0">สร้างบัญชีเพื่อเล่นเกมกับเพื่อน</p>
                </div>

                <div class="card-body">
                    <form method="post" action="<?= base_url('auth/register_player') ?>">
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้ใช้งาน</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="ตั้งชื่อผู้ใช้" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">รหัสผ่าน</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="ตั้งรหัสผ่าน" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-register w-100">
                            <i class="fa-solid fa-user-plus me-2"></i>สมัครสมาชิก
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <span style="color: rgba(255,255,255,0.5);">มีบัญชีแล้ว?</span>
                        <a href="<?= base_url('auth/login') ?>" class="login-link">เข้าสู่ระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
