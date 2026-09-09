<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Board Game</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Font Sarabun -->
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
            color: #e0e0e0;
        }

        .login-card {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: -40px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
        }

        .login-header {
            background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
            padding: 40px 20px;
            color: white;
            text-align: center;
        }

        .login-header i {
            font-size: 2.8rem;
            margin-bottom: 10px;
            opacity: 0.95;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .login-header h4 {
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.85;
        }

        .card-body {
            padding: 40px !important;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.75);
            margin-left: 2px;
        }

        .input-group-text {
            background-color: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-right: none !important;
            color: #a5b4fc;
            border-radius: 12px 0 0 12px !important;
        }

        .form-control {
            border-radius: 0 12px 12px 0 !important;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            background-color: rgba(255, 255, 255, 0.06) !important;
            font-size: 0.95rem;
            color: #fff !important;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.09) !important;
            border-color: var(--theme-color) !important;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.25);
            color: #fff !important;
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
            color: white;
        }

        .register-link {
            color: #a5b4fc;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .register-link:hover {
            text-decoration: underline;
            color: #c7d2fe;
        }
    </style>
</head>
<body>
<?php $this->load->view('theme/notify'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card login-card shadow-sm">
                <div class="login-header">
                    <i class="fa-solid fa-gamepad"></i>
                    <h4>ยินดีต้อนรับ</h4>
                    <p class="mb-0">เข้าสู่ระบบ Board Game Community</p>
                </div>

                <div class="card-body">
                    <form method="post" action="<?= base_url('auth/login') ?>">
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้ใช้งาน</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" name="username" class="form-control" placeholder="ระบุชื่อผู้ใช้งาน" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">รหัสผ่าน</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control" placeholder="ระบุรหัสผ่าน" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-login w-100">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>เข้าสู่ระบบ
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <span style="font-size: 0.9rem; color: #666;">ยังไม่มีบัญชีสำหรับเล่นเกม?</span>
                        <a href="<?= base_url('auth/register_player') ?>" class="register-link ms-1">
                            สมัครสมาชิกผู้เล่น
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. ตรวจสอบการเชื่อมต่อ Firebase อัตโนมัติเมื่อเปิดหน้าเว็บ
    console.log("%c🔥 [Firebase Diagnostic] กำลังทดสอบเชื่อมต่อ Firebase...", "color: #6366f1; font-weight: bold; font-size: 13px;");
    
    fetch("<?= base_url('auth/test_firebase') ?>")
        .then(function(res) { return res.json(); })
        .then(function(data) {
            console.group("%c🔥 [Firebase Connection Result]", "color: " + (data.connected ? "#10b981" : "#ef4444") + "; font-weight: bold; font-size: 13px;");
            console.log("📍 Database URL:", data.database_url);
            console.log("📡 HTTP Status Code:", data.http_code);
            console.log("🔌 Connected Status:", data.connected ? "✅ สำเร็จ (Online)" : "❌ ล้มเหลว (Offline / Error)");
            
            if (data.curl_error) {
                console.error("⚠️ cURL Error:", data.curl_error);
            }
            if (data.raw_response) {
                console.log("📄 Raw Response from Firebase:", data.raw_response);
            }
            
            if (data.connected) {
                console.log("👥 จำนวน User ในฐานข้อมูล:", data.user_count, "คน");
                console.log("📋 รายชื่อ User ทั้งหมดที่มีในระบบ:", data.user_list);
            } else {
                console.warn("💡 ข้อแนะนำ: ตรวจสอบ Firebase Realtime Database Rules ว่าอนุญาต .read: true และ .write: true หรือไม่ หรือตรวจ URL ใน Render Environment Variables");
            }
            console.groupEnd();
        })
        .catch(function(err) {
            console.error("❌ ไม่สามารถเรียก API ทดสอบ Firebase ได้:", err);
        });

    // 2. ถ้าเพิ่งกดล็อกอินแล้วล้มเหลว ให้แสดงผลลัพธ์อย่างละเอียดใน Console
    <?php if ($debug = $this->session->flashdata('login_debug')): ?>
    console.group("%c🚨 [Login Failure Detail - ข้อมูลการล็อกอินล้มเหลว]", "color: #f43f5e; font-weight: bold; font-size: 14px;");
    console.error("❌ สาเหตุ:", <?= json_encode($debug['reason'], JSON_UNESCAPED_UNICODE) ?>);
    console.log("👤 Username ที่พยายามล็อกอิน:", <?= json_encode($debug['username']) ?>);
    console.log("🔍 มีชื่อผู้ใช้นี้ใน Firebase หรือไม่:", <?= $debug['user_exists'] ? '"✅ มีอยู่ในระบบ"' : '"❌ ไม่พบในระบบ"' ?>);
    console.log("🔑 รหัสผ่านตรงกันหรือไม่:", <?= $debug['password_match'] ? '"✅ ตรงกัน"' : '"❌ ไม่ตรงกัน"' ?>);
    console.log("👥 รายชื่อ User ทั้งหมดที่มีในระบบขณะนี้:", <?= json_encode($debug['existing_users'], JSON_UNESCAPED_UNICODE) ?>);
    console.log("📡 Firebase HTTP Status:", <?= json_encode($debug['http_code']) ?>);
    if (<?= json_encode($debug['curl_error']) ?>) {
        console.error("⚠️ cURL Error:", <?= json_encode($debug['curl_error']) ?>);
    }
    console.groupEnd();
    <?php endif; ?>
});
</script>

</body>
</html>
