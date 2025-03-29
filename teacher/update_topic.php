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
    $sql = "UPDATE topics SET title = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $description, $topic_id);
    $stmt->execute();
    $stmt->close();

    foreach ($_POST as $key => $value) {
        if (preg_match('/^question_(\d+)$/', $key, $matches)) {
            $question_id = $matches[1];
            $question_content = trim($value);
            $sql = "UPDATE questions SET content = ? WHERE id = ? AND topic_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $question_content, $question_id, $topic_id);
            $stmt->execute();
            $stmt->close();
            if (!isset($_POST["correct_answer_{$question_id}"])) {
                echo "Vui lòng chọn đáp án đúng cho câu hỏi: " . htmlspecialchars($question_content);
                exit;
            }
            foreach ($_POST as $answer_key => $answer_value) {
                if (preg_match("/^answer_(\d+)$/", $answer_key, $answer_match)) {
                    $answer_id = $answer_match[1];
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
        elseif (strpos($key, "new_question_") === 0) {
            $question_text = trim($value);
            $stmt = $conn->prepare("INSERT INTO questions (content, topic_id) VALUES (?, ?)");
            $stmt->bind_param("si", $question_text, $topic_id);
            $stmt->execute();
            $new_question_id = $stmt->insert_id;
            $stmt->close();
            $question_number = str_replace("new_question_", "", $key);
            $correct_answer = $_POST["new_correct_answer_" . $question_number] ?? null;
            for ($i = 1; $i <= 4; $i++) {
                $answer_key = "new_answer_{$question_number}_{$i}";
                if (!empty($_POST[$answer_key])) {
                    $answer_text = trim($_POST[$answer_key]);
                    $is_correct = ($correct_answer == $i) ? 1 : 0;

                    $stmt = $conn->prepare("INSERT INTO answers (question_id, content, is_correct) VALUES (?, ?, ?)");
                    $stmt->bind_param("isi", $new_question_id, $answer_text, $is_correct);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    exit;


    $conn->close();
    header("Location: dashboard.php?message=update_success");
    exit;
} else {
    echo "Yêu cầu không hợp lệ!";
}


