<?php
require "config/db.php";

// Kiểm tra tham số user_id và age
if (!isset($_GET['user_id']) || !isset($_GET['age'])) {
    die("Lỗi: Dữ liệu không hợp lệ!");
}

$user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
$age = $_GET['age'];

// Xác định teacher_id dựa trên kiểu của user_id
$teacher_id = 0;
if (is_numeric($user_id)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($teacher_id);
        $stmt->fetch();
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT id FROM users WHERE qr_code = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($teacher_id);
        $stmt->fetch();
    }
    $stmt->close();
}

if ($teacher_id === 0) {
    die("Lỗi: Giáo viên không tồn tại!");
}

// Truy vấn danh sách chủ đề của giáo viên theo độ tuổi
$stmt = $conn->prepare("SELECT id, title FROM topics WHERE create_by = ? AND level = ?");
$stmt->bind_param("is", $teacher_id, $age);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách chủ đề</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="div-container">
        <h1>Chào bạn</h1>
        <p>Vui lòng chọn chủ đề</p>
        <div class="topic-list">
            <?php if ($result->num_rows == 0): ?>
                <p>Không có chủ đề nào cho cấp độ này!</p>
            <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <a href="question.php?user_id=<?= $teacher_id ?>&topic_id=<?= $row['id'] ?>" class="topic-button">
                        <?= htmlspecialchars($row['title']) ?>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>