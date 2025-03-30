<?php
session_start();
require "../config/db.php";


if (!isset($_GET["id"])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET["id"];


$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION["error"] = "Không tìm thấy tài khoản!";
    header("Location: manage_users.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["question_text"]);
    $email = trim($_POST["answer"]);
    $password = trim($_POST["password"]);
    $confirmpass = trim($_POST["confirmpass"]);
    $role = trim($_POST["topic"]);

    if ($password !== $confirmpass) {
        $_SESSION["error"] = "Mật khẩu xác nhận không khớp!";
        header("Location: edit_user.php?id=" . $user_id);
        exit();
    }

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $email, $hashed_password, $role, $user_id);
    } else {
        $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $email, $role, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION["success"] = "Cập nhật tài khoản thành công!";
        header("Location: manage_users.php");
        exit();
    } else {
        $_SESSION["error"] = "Có lỗi xảy ra, vui lòng thử lại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa tài khoản</title>
    <style>
        body { text-align: center; padding: 20px; }
        h1 { background-color: green; color: white; padding: 10px; border-radius: 5px; }
        form { width: 60%; margin: auto; }
        label { font-size: 1.2em; font-weight: bold; display: block; text-align: left; margin: 10px 0; }
        input, select { width: 100%; padding: 10px; border: 2px solid black; font-size: 1.1em; }
        button { padding: 10px 20px; font-size: 1.2em; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; margin-top: 15px; }
        .btn-save { background-color: yellow; }
        .btn-cancel { background-color: orange; color: white; }
        .btn-save:hover, .btn-cancel:hover { opacity: 0.8; }
    </style>
</head>
<body>

    <h1>Sửa tài khoản</h1>

    <?php if (isset($_SESSION["error"])): ?>
        <p style="color: red;"><?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="question_text">Tên tài khoản</label>
        <input id="question_text" name="question_text" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="answer">Email</label>
        <input id="answer" name="answer" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="password">Mật khẩu (để trống nếu không đổi)</label>
        <input id="password" name="password" type="password">

        <label for="confirmpass">Xác nhận mật khẩu</label>
        <input id="confirmpass" name="confirmpass" type="password">

        <label for="topic">Vai trò</label>
        <select id="topic" name="topic" required>
            <option value="">Vui lòng chọn vai trò</option>
            <option value="teacher" <?php if ($user["role"] == "teacher") echo "selected"; ?>>Giáo viên</option>
            <option value="admin" <?php if ($user["role"] == "admin") echo "selected"; ?>>Admin</option>
        </select>   

        <button type="submit" class="btn-save">Lưu</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='manage_users.php'">Hủy</button>
    </form>

</body>
</html>
