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
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirmpass = trim($_POST["confirmpass"]);
    $role = trim($_POST["role"]);

    if ($password !== $confirmpass) {
        $_SESSION["error"] = "Mật khẩu xác nhận không khớp!";
        header("Location: edit_account.php?id=" . $user_id);
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
    <title>Chỉnh sửa tài khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="edit-container">
        <div class="edit-header">
            <h1><i class="fas fa-user-edit"></i> Chỉnh sửa tài khoản</h1>
            <p>Cập nhật thông tin tài khoản người dùng</p>
        </div>

        <?php if (isset($_SESSION["error"])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Tên tài khoản</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mật khẩu mới (để trống nếu không đổi)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>

            <div class="form-group">
                <label for="confirmpass"><i class="fas fa-lock"></i> Xác nhận mật khẩu</label>
                <input type="password" class="form-control" id="confirmpass" name="confirmpass">
            </div>

            <div class="form-group">
                <label for="role"><i class="fas fa-user-tag"></i> Vai trò</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">-- Chọn vai trò --</option>
                    <option value="teacher" <?php if ($user["role"] == "teacher") echo "selected"; ?>>Giáo viên</option>
                    <option value="admin" <?php if ($user["role"] == "admin") echo "selected"; ?>>Quản trị viên</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="window.location.href='manage_users.php'">
                    <i class="fas fa-times"></i> Hủy bỏ
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmpass = document.getElementById('confirmpass').value;
            
            if (password !== confirmpass) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }
            
            if (password && password.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>