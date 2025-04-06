<?php
require_once '../config/db.php';
require "../helpers/session_helper.php";
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('dashboard.php', 'Yêu cầu không hợp lệ!');
}
$topic_id = filter_input(INPUT_POST, 'topic_id', FILTER_VALIDATE_INT);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if (!$topic_id || empty($title) || empty($description)) {
    redirectWithError("edit_topic.php?id=$topic_id", 'Vui lòng điền đầy đủ thông tin chủ đề!');
}
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("UPDATE topics SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $description, $topic_id);
    $stmt->execute();
    $stmt->close();
    if (!empty($_POST['question'])) {
        foreach ($_POST['question'] as $question_id => $question_data) {
            $question_id = (int)$question_id;
            $content = trim($question_data['content']);
            
            if (empty($content)) {
                throw new Exception("Nội dung câu hỏi không được để trống!");
            }
            $stmt = $conn->prepare("UPDATE questions SET content = ? WHERE id = ? AND topic_id = ?");
            $stmt->bind_param("sii", $content, $question_id, $topic_id);
            $stmt->execute();
            $stmt->close();
            if (!empty($question_data['answers'])) {
                $correct_answer_id = (int)$question_data['correct_answer'];
                
                foreach ($question_data['answers'] as $answer_id => $answer_content) {
                    $answer_id = (int)$answer_id;
                    $answer_content = trim($answer_content);
                    $is_correct = ($answer_id === $correct_answer_id) ? 1 : 0;
                    
                    $stmt = $conn->prepare("UPDATE answers SET content = ?, is_correct = ? WHERE id = ? AND question_id = ?");
                    $stmt->bind_param("siii", $answer_content, $is_correct, $answer_id, $question_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
    if (!empty($_POST['deleted_questions'])) {
        foreach ($_POST['deleted_questions'] as $question_id) {
            $question_id = (int)$question_id;
            $stmt = $conn->prepare("DELETE FROM answers WHERE question_id = ?");
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $stmt->close();
            $stmt = $conn->prepare("DELETE FROM questions WHERE id = ? AND topic_id = ?");
            $stmt->bind_param("ii", $question_id, $topic_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    if (!empty($_POST['new_questions'])) {
        foreach ($_POST['new_questions'] as $new_question) {
            $content = trim($new_question['content']);
            $correct_answer = (int)$new_question['correct_answer'];
            
            if (empty($content)) {
                throw new Exception("Nội dung câu hỏi mới không được để trống!");
            }
            $stmt = $conn->prepare("INSERT INTO questions (content, topic_id) VALUES (?, ?)");
            $stmt->bind_param("si", $content, $topic_id);
            $stmt->execute();
            $question_id = $stmt->insert_id;
            $stmt->close();
            if (!empty($new_question['answers'])) {
                foreach ($new_question['answers'] as $index => $answer_content) {
                    $answer_content = trim($answer_content);
                    $is_correct = ($index == $correct_answer) ? 1 : 0;
                    
                    $stmt = $conn->prepare("INSERT INTO answers (question_id, content, is_correct) VALUES (?, ?, ?)");
                    $stmt->bind_param("isi", $question_id, $answer_content, $is_correct);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    $conn->commit();
    redirectWithSuccess("dashboard.php?id=$topic_id", 'Cập nhật chủ đề thành công!');

} catch (Exception $e) {
    $conn->rollback();
    redirectWithError("edit_topic.php?id=$topic_id", 'Lỗi: ' . $e->getMessage());
}