<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $topic_id = $_POST['topic_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    
    if (empty($title) || empty($description)) {
        echo "Chủ đề và mô tả không được để trống!";
        exit;
    }

    // Cập nhật chủ đề
    $sql = "UPDATE topics SET title = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $description, $topic_id);
    $stmt->execute();
    $stmt->close();

    foreach ($_POST as $key => $value) {
        // Xử lý cập nhật câu hỏi cũ
        if (strpos($key, "question_") === 0 && is_numeric(str_replace("question_", "", $key))) {
            $question_id = str_replace('question_', '', $key);
            $question_content = trim($value);
            
            // Cập nhật nội dung câu hỏi
            $sql = "UPDATE questions SET content = ? WHERE id = ? AND topic_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $question_content, $question_id, $topic_id);
            $stmt->execute();
            $stmt->close();

            // Kiểm tra xem có chọn đáp án đúng không
            if (!isset($_POST["correct_answer_{$question_id}"])) {
                echo "Vui lòng chọn một đáp án đúng cho câu hỏi: " . htmlspecialchars($question_content);
                exit;
            }

            // Cập nhật đáp án cũ
            foreach ($_POST as $answer_key => $answer_value) {
                if (strpos($answer_key, "answer_{$question_id}_") === 0) {
                    $answer_id = str_replace("answer_{$question_id}_", '', $answer_key);
                    $answer_content = trim($answer_value);
                    $is_correct = ($_POST["correct_answer_{$question_id}"] == $answer_id) ? 1 : 0;

                    $sql = "UPDATE answers SET content = ?, is_correct = ? WHERE id = ? AND question_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("siii", $answer_content, $is_correct, $answer_id, $question_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        // Xử lý thêm câu hỏi mới (nếu không có ID)
        elseif (strpos($key, "question_") === 0) {
            $question_text = trim($value);
            $stmt = $conn->prepare("INSERT INTO questions (content, topic_id) VALUES (?, ?)");
            $stmt->bind_param("si", $question_text, $topic_id);
            $stmt->execute();
            $question_id = $stmt->insert_id; // Lấy ID của câu hỏi mới
            $stmt->close();

            // Lấy số thứ tự của câu hỏi
            $question_number = str_replace("question_", "", $key);
            $correct_answer = $_POST["correct_answer_" . $question_number] ?? null;

            // Thêm đáp án mới
            for ($i = 1; $i <= 4; $i++) {
                $answer_key = "answer_{$question_number}_{$i}";
                if (!empty($_POST[$answer_key])) {
                    $answer_text = trim($_POST[$answer_key]);
                    $is_correct = ($correct_answer == $i) ? 1 : 0;

                    $stmt = $conn->prepare("INSERT INTO answers (question_id, content, is_correct) VALUES (?, ?, ?)");
                    $stmt->bind_param("isi", $question_id, $answer_text, $is_correct);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    $conn->close();
    header("Location: dashboard.php?message=update_success");
    exit;
} else {
    echo "Yêu cầu không hợp lệ!";
}
