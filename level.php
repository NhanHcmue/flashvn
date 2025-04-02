<?php
require "../config/db.php";

// Kiểm tra xem có `user_id` từ mã QR không
if (!isset($_GET['user_id'])) {
    die("Lỗi: Không tìm thấy ID giáo viên!");
}

$teacher_qr = $_GET['user_id'];

// Tìm ID của giáo viên dựa trên mã QR trong database
$stmt = $conn->prepare("SELECT id FROM users WHERE qr_code = ?");
$stmt->bind_param("s", $teacher_qr);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Lỗi: Mã QR không hợp lệ!");
}

$teacher_id = $row['id']; // Lưu ID giáo viên

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
        <a href="topics.php?user_id=<?= $teacher_id ?>&age=1-2" class="button box-1">Lớp 1-2</a>
        <a href="topics.php?user_id=<?= $teacher_id ?>&age=3-5" class="button box-2">Lớp 3-5</a>
        <a href="topics.php?user_id=<?= $teacher_id ?>&age=6-8" class="button box-3">Lớp 6-8</a>
        <a href="topics.php?user_id=<?= $teacher_id ?>&age=9-12" class="button box-4">Lớp 9-12</a>
    </div>
</body>
</html>
