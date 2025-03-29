<?php
session_start();
// if (!isset($_SESSION['users_id'])) {
//     $_SESSION['users_id'] = 3; // Tạm thời đặt ID giáo viên là 3 để test giả lập
// }
// echo "ID Giáo viên: " . $_SESSION['users_id'];
include '../config/db.php';

$teacher_id = $_SESSION['users_id']; // Lấy ID giáo viên từ session
$age = $_GET['age']; // Lấy độ tuổi từ URL

$stmt = $conn->prepare("SELECT * FROM topics WHERE teacher_id = ? AND age_group = ?");
$stmt->bind_param("is", $teacher_id, $age);
$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Chọn Chủ Đề</h1>";
echo "<p>Vui lòng chọn chủ đề</p>";
echo "<div class='topics-container'>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<a href="questions.php?topic_id=' . $row['id'] . '" class="topic-button">' . $row['topic_name'] . '</a>';
    }
} else {
    echo "<p>Không có chủ đề nào!</p>";
}

echo "</div>";

$stmt->close();
$conn->close();
?>
