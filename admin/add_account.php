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
        .btn-add { background-color: yellowgreen; }
        .btn-cancel { background-color: orange; color: white; }
        .btn-add:hover, .btn-cancel:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <h1>Thêm tài khoản</h1>
    <form method="POST">
        <label for="username">Tên tài khoản</label>
        <input id="username" name="username" placeholder="Nhập tên tài khoản" required>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="Nhập email" required>

        <label for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" placeholder="Nhập mật khẩu" required>

        <label for="confirmpass">Xác nhận mật khẩu</label>
        <input id="confirmpass" name="confirmpass" type="password" placeholder="Nhập lại mật khẩu" required>

        <button type="submit" class="btn-add">Thêm</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='manage_users.php'">Hủy</button>
    </form>
</body>
</html>
<?php
include '../config/db.php';
include '../libs/phpqrcode/qrlib.php'; // Kiểm tra đường dẫn 

// Hàm tạo mã QR dựa trên email
function generateQRCode($email) {
    return md5($email); // Mã hóa email thành chuỗi duy nhất
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmpass = $_POST["confirmpass"];
    $role = "teacher";

    // Kiểm tra xác nhận mật khẩu
    if ($password !== $confirmpass) {
        echo "Mật khẩu xác nhận không khớp!";
        exit;
    }

    // Mã hóa mật khẩu
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Kiểm tra email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email đã tồn tại!";
    } else {
        // Tạo mã QR
        $qr_code = generateQRCode($email);

        // Thêm tài khoản vào cơ sở dữ liệu
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, qr_code) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $password_hash, $role, $qr_code);

        if ($stmt->execute()) {
            // Tạo thư mục lưu QR nếu chưa tồn tại
            $qr_dir = "../image/qrcodes/";
            if (!file_exists($qr_dir)) {
                mkdir($qr_dir, 0777, true);
            }

            // Tạo ảnh QR
            $qr_image = $qr_dir . $qr_code . ".png";
            QRcode::png("https://flashvn.org/level.php?teacher_id=$users_id", $qr_image, QR_ECLEVEL_L, 5);

            echo "<script>
                alert('Thêm tài khoản thành công!');
                window.location.href = 'manage_users.php';
            </script>";
        } else {
            echo "Lỗi: " . $conn->error;
        }
    }
    $stmt->close();
}

$conn->close();
?>