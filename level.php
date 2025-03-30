<?php
// include '../config/db.php'; // Kết nối database

// // Nhận mã giáo viên từ QR
// $users_id = $_GET['users_id'] ?? null;

// // Kiểm tra mã hợp lệ
// if (!$users_id) {
//     die("Mã giáo viên không hợp lệ.");
// }

// // Truy vấn kiểm tra mã giáo viên
// $stmt = $conn->prepare("SELECT id, username FROM users WHERE qr_code = ?");
// $stmt->bind_param("s", $users_id);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows === 0) {
//     die("Mã giáo viên không tồn tại.");
// }

// $teacher = $result->fetch_assoc();
// $teacher_name = $teacher['username'];
// $teacher_id = $teacher['id']; // ID của giáo viên
// $stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    <title>Chọn Độ Tuổi</title>
</head>
<body>
    <h1>Chào bạn!</h1>
    <p>Vui lòng chọn độ tuổi để bắt đầu</p>
    <div class="container">
        <a href="topics.php?teacher_id=<?= $teacher_id ?>&age=1-2" class="button box-1">Lớp 1-2</a>
        <a href="topics.php?teacher_id=<?= $teacher_id ?>&age=3-5" class="button box-2">Lớp 3-5</a>
        <a href="topics.php?teacher_id=<?= $teacher_id ?>&age=6-8" class="button box-3">Lớp 6-8</a>
        <a href="topics.php?teacher_id=<?= $teacher_id ?>&age=9-12" class="button box-4">Lớp 9-12</a>
    </div>
</body>
</html>
