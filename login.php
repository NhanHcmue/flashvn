<?php
// login.php
session_start();
require "config/db.php";
require "helpers/session_helper.php";

// Kiểm tra nếu đã đăng nhập thì chuyển hướng
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        redirectWithSuccess('admin/manage_users.php', 'Bạn đã đăng nhập');
    } else {
        redirectWithSuccess('teacher/dashboard.php', 'Bạn đã đăng nhập');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    redirectWithSuccess('admin/manage_users.php', 'Đăng nhập thành công');
                } else {
                    redirectWithSuccess('teacher/dashboard.php', 'Đăng nhập thành công');
                }
            } else {
                $error = 'Mật khẩu không chính xác';
            }
        } else {
            $error = 'Tài khoản không tồn tại';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --danger-color: #e63946;
            --success-color: #4cc9f0;
            --border-radius: 8px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('https://source.unsplash.com/random/1920x1080/?school,education');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container h1 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 2rem;
            font-weight: 700;
        }

        .logo {
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-size: 2.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(72, 149, 239, 0.2);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 0.5rem;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .alert {
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-weight: 500;
            text-align: center;
        }

        .alert.error {
            background-color: rgba(230, 57, 70, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .footer-links {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--gray-color);
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 1.5rem;
                margin: 0 15px;
            }
        }
        .btn-guest {
            width: 100%;
            padding: 12px;
            background-color: var(--light-color);
            color: var(--dark-color);
            border: 2px solid var(--primary-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-guest:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h1>Đăng nhập hệ thống</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" name="username" placeholder="Tên đăng nhập" required>
            </div>
            
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Đăng nhập
            </button>
        </form>
        
        <!-- Thêm nút Guest ở đây -->
        <button class="btn-guest" onclick="window.location.href='enter_code.php'">
            <i class="fas fa-user-clock"></i> Tiếp tục với tư cách khách
        </button>
        
        <div class="footer-links">
            <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                <i class="fas fa-question-circle"></i> Quên mật khẩu?
            </a>
        </div>
<!-- 
        <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Quên mật khẩu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="forgotPasswordForm" action="send_reset_password.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email đăng ký</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="form-text">Chúng tôi sẽ gửi link đổi mật khẩu đến email này</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</body>
</html>