<?php
session_start();
require "../config/db.php";

$create_by = $_SESSION['user_id'] ?? null;
if (!$create_by) {
    die("Lỗi: Người dùng chưa đăng nhập!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if (empty($title) || empty($description)) {
        die("Lỗi: Tiêu đề và mô tả không được để trống!");
    }
    $stmt = $conn->prepare("CALL AddTopic(?, ?, ?)");
    $stmt->bind_param("ssi", $title, $description, $create_by);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $topic_id = $row['topic_id'];
    $stmt->close();

    if (!$topic_id) {
        die("Lỗi: Không thể tạo chủ đề!");
    }
    foreach ($_POST as $key => $value) {
        if (strpos($key, "question_") === 0) {
            $question_text = trim($value);
            if (empty($question_text)) {
                continue;
            }

            $question_number = str_replace("question_", "", $key);
            $correct_answer = $_POST["correct_answer_{$question_number}"] ?? null;
            $answer1 = trim($_POST["answer_{$question_number}_1"] ?? '');
            $answer2 = trim($_POST["answer_{$question_number}_2"] ?? '');
            $answer3 = trim($_POST["answer_{$question_number}_3"] ?? '');
            $answer4 = trim($_POST["answer_{$question_number}_4"] ?? '');
            if (empty($answer1) || empty($answer2) || empty($answer3) || empty($answer4)) {
                die("Lỗi: Tất cả đáp án phải được nhập!");
            }
            $stmt = $conn->prepare("CALL AddQuestionWithAnswers(?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssi", $topic_id, $question_text, $answer1, $answer2, $answer3, $answer4, $correct_answer);          
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: dashboard.php?message=add_success");
    exit;
}
