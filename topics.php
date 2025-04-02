<?php
require "../config/db.php";

// Kiểm tra tham số teacher_id và age
if (!isset($_GET['user_id']) || !isset($_GET['age'])) {
    die("Lỗi: Dữ liệu không hợp lệ!");
}

$teacher_id = $_GET['user_id'];
$age = $_GET['age'];

// Truy vấn danh sách chủ đề của giáo viên theo độ tuổi
$stmt = $conn->prepare("SELECT id, topic_name FROM topics WHERE teacher_id = ? AND age_group = ?");
$stmt->bind_param("is", $teacher_id, $age);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Danh sách chủ đề</title>
    <style>
        body {font-family: sans-serif;display: flex;justify-content: center;align-items: center;height: 100vh;margin: 0;background-color: #f4f4f4;}
        .div-container {text-align: center;background-color: white;padding: 40px;border-radius: 8px;box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);}
        h1 {margin-bottom: 20px;}
        button {padding: 10px 20px;margin: 5px;width: 200px;border: none;border-radius: 5px;background-color: #007bff;color: white;cursor: pointer;}
        button:hover {background-color: #0056b3;}
    </style>
</head>
<body>
    <div class="div-container">
        <h1>Chào bạn</h1>
        <p>Vui lòng chọn chủ đề</p>
        <div>
            <?php while ($row = $result->fetch_assoc()): ?>
                <a href="question.php?teacher_id=<?= $teacher_id ?>&topic_id=<?= $row['id'] ?>">
                    <button><?= htmlspecialchars($row['topic_name']) ?></button>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>