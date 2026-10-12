<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Health Risk Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background-color: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .login-body {
            padding: 30px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25 red rgba(118, 75, 162, 0.25);
            border-color: #764ba2;
        }
        .btn-login {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            color: white;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            color: white;
            opacity: 0.9;
        }
        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-home a {
            color: #764ba2;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="fas fa-user-shield"></i>
        <h4 class="mb-0">Admin Login</h4>
        <p class="small opacity-75">จัดการระบบประเมินความเสี่ยงสุขภาพ</p>
    </div>
    
    <div class="login-body">
        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                อีเมลหรือรหัสผ่านไม่ถูกต้อง
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
    <label for="login" class="form-label small fw-bold">ชื่อผู้ใช้งาน หรือ อีเมล</label>
    <div class="input-group">
        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
        <input type="text" class="form-control border-start-0" id="login" name="login" value="{{ old('login') }}" required autofocus placeholder="Username or Email">
    </div>
</div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-bold">รหัสผ่าน</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0" id="password" name="password" required placeholder="••••••••">
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label small" for="remember">จดจำการใช้งาน</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> เข้าสู่ระบบ
                </button>
            </div>
        </form>

        <div class="back-to-home">
            <a href="{{ url('http://10.121.111.233:9992/risk-score/') }}">
                <i class="fas fa-arrow-left me-1"></i> กลับหน้าหลัก
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>