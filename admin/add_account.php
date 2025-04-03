<?php
require "../config/db.php";
include "../libs/phpqrcode/qrlib.php"; // Thư viện tạo mã QR

// Hàm tạo mã QR từ email
function generateQRCode($email) {
    return md5($email); // Tạo chuỗi duy nhất từ email
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirmpass = trim($_POST["confirmpass"]);
    $role = $_POST["role"];

    // Kiểm tra email hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email không hợp lệ!');</script>";
        exit;
    }

    // Kiểm tra mật khẩu trùng khớp
    if ($password !== $confirmpass) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
        exit;
    }

    // Mã hóa mật khẩu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Kiểm tra email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email đã tồn tại!');</script>";
        exit;
    }

    // Tạo mã QR
    $qr_code = generateQRCode($email);

    // Thêm tài khoản vào database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, qr_code) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $hashedPassword, $role, $qr_code);

    if ($stmt->execute()) {
        // Tạo thư mục lưu QR nếu chưa có
        $qr_dir = "../image/qrcodes/";
        if (!file_exists($qr_dir)) {
            mkdir($qr_dir, 0777, true);
        }

        // Sinh ảnh QR
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
    <title>Thêm tài khoản</title>
    <style>
        body { text-align: center; padding: 20px; }
        h1 { background-color: green; color: white; padding: 10px; border-radius: 5px; }
        form { width: 60%; margin: auto; }
        label { font-size: 1.2em; font-weight: bold; display: block; text-align: left; margin: 10px 0; }
        input, select { width: 100%; padding: 10px; border: 2px solid black; font-size: 1.1em; }
        button { padding: 10px 20px; font-size: 1.2em; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; margin-top: 15px; }
        .btn-add { background-color: yellow; }
        .btn-cancel { background-color: orange; color: white; }
        .btn-add:hover, .btn-cancel:hover { opacity: 0.8; }
    </style>
</head>
<body>

    <h1>Thêm tài khoản</h1> 
    <form method="POST">
        <label for="username">Tên tài khoản</label>
        <input id="username" name="username" required>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>

        <label for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" required>

        <label for="confirmpass">Xác nhận mật khẩu</label>
        <input id="confirmpass" name="confirmpass" type="password" required>

        <label for="role">Vai trò</label>
        <select id="role" name="role" required>
            <option value="">Vui lòng chọn vai trò</option>
            <option value="teacher">Giáo viên</option>
            <option value="admin">Admin</option>
        </select>   

        <button type="submit" name="submit" class="btn-add">Thêm</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='manage_users.php'">Hủy</button>
    </form>

</body>
</html>
