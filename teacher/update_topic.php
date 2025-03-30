<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $topic_id = $_POST['topic_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$topic_id || empty($title) || empty($description)) {
        die("Lỗi: Chủ đề, tiêu đề và mô tả không được để trống!");
    }
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("CALL UpdateTopic(?, ?, ?)");
        $stmt->bind_param("iss", $topic_id, $title, $description);
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi cập nhật chủ đề!");
        }
        $stmt->close();

        foreach ($_POST as $key => $value) {
            if (preg_match('/^question_(\d+)$/', $key, $matches)) {
                $question_id = (int) $matches[1];
                $question_content = trim($value);
                
                $correct_answer = $_POST["correct_answer_{$question_id}"] ?? null;
                if (!$correct_answer) {
                    throw new Exception("Vui lòng chọn đáp án đúng cho câu hỏi: " . htmlspecialchars($question_content));
                }
                
                $answers = [];
                for ($i = 1; $i <= 4; $i++) {
                    $answers[] = trim($_POST["answer_{$question_id}_{$i}"] ?? '');
                }
                $stmt = $conn->prepare("CALL UpdateQuestionAndAnswers(?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssssi", $question_id, $question_content, ...$answers, $correct_answer);

                if (!$stmt->execute()) {
                    throw new Exception("Lỗi khi cập nhật câu hỏi ID: $question_id");
                }
                $stmt->close();
            }
        }

        $conn->commit();
        echo "<script>alert('Cập nhật thành công!'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        die("Lỗi: " . $e->getMessage());
    }
} else {
    die("Yêu cầu không hợp lệ!");
}
?>