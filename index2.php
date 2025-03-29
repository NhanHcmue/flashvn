<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Chọn Vai Trò</title>
    
</head>
<body>
    <div class="role-container">
        <h1>Chào bạn</h1>
        <p>Vui lòng chọn vai trò</p>
        <div class="role-buttons">
            <!-- <button class="role-button student" onclick="chooseRole('student')">Học Sinh</button> -->
            <button class="role-button teacher" onclick="chooseRole('teacher')">Giáo viên</button>
            <button class="role-button admin" onclick="chooseRole('admin')">Admin</button>
        </div>
    </div>

    <script>
        function chooseRole(role) {
            if (role === "student") {
                window.location.href = "level.php"; // Chọn độ tuổi
            } else if (role === "teacher") {
                window.location.href = "teacher/dashboard.php"; // Giáo viên
            } else if (role === "admin") {
                window.location.href = "admin/manage_users.php"; // Admin
            }
        }
    </script>
</body>
</html>