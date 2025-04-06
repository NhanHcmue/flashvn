<?php
require "config/db.php";

if (!isset($_GET['user_id'])) {
    die("Lỗi: Thiếu thông tin giáo viên!");
}

$user_id = trim($_GET['user_id']);
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
$levels = [
    ['name' => 'Lớp 1-2', 'value' => '1-2', 'color' => '#ffe082'], // Vàng nhạt
    ['name' => 'Lớp 3-5', 'value' => '3-5', 'color' => '#81c784'], // Xanh lá
    ['name' => 'Lớp 6-8', 'value' => '6-8', 'color' => '#64b5f6'], // Xanh dương
    ['name' => 'Lớp 9-12', 'value' => '9-12', 'color' => '#ef5350'] // Đỏ
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn Cấp Độ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="level-container">
        <h1>Chào bạn</h1>
        <p>Vui lòng chọn độ tuổi</p>
        <div class="level-grid">
            <?php foreach ($levels as $level): ?>
                <a href="topics.php?user_id=<?= $teacher_id ?>&age=<?= $level['value'] ?>" 
                   class="level-button" 
                   style="background-color: <?= $level['color'] ?>;">
                    <?= htmlspecialchars($level['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>