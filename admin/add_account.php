<?php
require "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirmpass = trim($_POST["confirmpass"]);
    $role = $_POST["role"];

    if ($password !== $confirmpass) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>alert('Email đã tồn tại!');</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

            if ($stmt->execute()) {
                echo "<script>alert('Thêm tài khoản thành công!'); window.location.href = 'manage_users.php';</script>";
            } else {
                echo "<script>alert('Lỗi khi thêm tài khoản!');</script>";
            }
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
    <title>Thêm tài khoản</title>
    <style>
        body { text-align: center; padding: 20px; font-family: Arial, sans-serif; }
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

    <form method="POST" action="">
        <label for="username">Tên tài khoản</label>
        <input type="text" id="username" name="username" placeholder="Nhập tên tài khoản" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Nhập email" required>

        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>

        <label for="confirmpass">Xác nhận mật khẩu</label>
        <input type="password" id="confirmpass" name="confirmpass" placeholder="Nhập lại mật khẩu" required>

        <label for="role">Vai trò</label>
        <select id="role" name="role" required>
            <option value="">Vui lòng chọn vai trò</option>
            <option value="teacher">Giáo viên</option>
            <option value="admin">Admin</option>
        </select>  

        <button type="submit" class="btn-add">Thêm</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='manage_users.php'">Hủy</button>
    </form>

</body>
</html>
