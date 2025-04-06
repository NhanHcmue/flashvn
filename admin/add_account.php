<?php
require "../config/db.php";
include "../libs/phpqrcode/qrlib.php";

function generateQRCode($email) {
    return md5($email);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirmpass = trim($_POST["confirmpass"]);
    $role = $_POST["role"];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email không hợp lệ!');</script>";
        exit;
    }
    if ($password !== $confirmpass) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
        exit;
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email đã tồn tại!');</script>";
        exit;
    }
    $qr_code = generateQRCode($email);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, qr_code) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $hashedPassword, $role, $qr_code);

    if ($stmt->execute()) {
        $qr_dir = "../image/qrcodes/";
        if (!file_exists($qr_dir)) {
            mkdir($qr_dir, 0777, true);
        }
        $qr_image = $qr_dir . $qr_code . ".png";
        QRcode::png("https://localhost/flashvn/level.php?user_id=$qr_code", $qr_image, QR_ECLEVEL_L, 5);

        echo "<script>alert('Thêm tài khoản thành công!'); window.location.href = 'manage_users.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi thêm tài khoản!');</script>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm tài khoản mới</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h1><i class="fas fa-user-plus"></i> Thêm tài khoản mới</h1>
            <p>Điền đầy đủ thông tin để tạo tài khoản mới</p>
        </div>

        <div id="errorAlert" class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <span id="errorMessage"></span>
        </div>

        <form method="POST" id="accountForm">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Tên tài khoản</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirmpass"><i class="fas fa-lock"></i> Xác nhận mật khẩu</label>
                <input type="password" class="form-control" id="confirmpass" name="confirmpass" required>
            </div>

            <div class="form-group">
                <label for="role"><i class="fas fa-user-tag"></i> Vai trò</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">-- Chọn vai trò --</option>
                    <option value="teacher">Giáo viên</option>
                    <option value="admin">Quản trị viên</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="window.location.href='manage_users.php'">
                    <i class="fas fa-times"></i> Hủy bỏ
                </button>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu tài khoản
                </button>
            </div>
        </form>
    </div>

    <script>
        // Xử lý hiển thị thông báo lỗi từ PHP
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        
        if (error) {
            document.getElementById('errorMessage').textContent = decodeURIComponent(error);
            document.getElementById('errorAlert').style.display = 'block';
        }

        // Validate form trước khi submit
        document.getElementById('accountForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmpass = document.getElementById('confirmpass').value;
            const email = document.getElementById('email').value;
            const errorAlert = document.getElementById('errorAlert');
            
            // Kiểm tra email hợp lệ
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                document.getElementById('errorMessage').textContent = 'Email không hợp lệ!';
                errorAlert.style.display = 'block';
                return;
            }
            
            // Kiểm tra mật khẩu trùng khớp
            if (password !== confirmpass) {
                e.preventDefault();
                document.getElementById('errorMessage').textContent = 'Mật khẩu xác nhận không khớp!';
                errorAlert.style.display = 'block';
                return;
            }
            
            // Kiểm tra độ dài mật khẩu
            if (password.length < 6) {
                e.preventDefault();
                document.getElementById('errorMessage').textContent = 'Mật khẩu phải có ít nhất 6 ký tự!';
                errorAlert.style.display = 'block';
            }
        });
    </script>
</body>
</html>