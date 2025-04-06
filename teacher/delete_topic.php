<?php
session_start();
require_once '../config/db.php';
require "../helpers/session_helper.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    redirectWithError('../login.php', 'Vui lòng đăng nhập với tư cách giáo viên');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['topic_id'])) {
    redirectWithError('dashboard.php', 'Yêu cầu không hợp lệ!');
    exit();
}

$topic_id = (int)$_POST['topic_id'];
$teacher_id = $_SESSION['user_id'];

try {
    $conn->begin_transaction();
    $stmt = $conn->prepare("SELECT id FROM topics WHERE id = ? AND create_by = ?");
    $stmt->bind_param("ii", $topic_id, $teacher_id);
    $stmt->execute();
    
    if (!$stmt->get_result()->num_rows) {
        throw new Exception("Bạn không có quyền xóa chủ đề này");
    }
    $stmt->close();
    $stmt = $conn->prepare("DELETE a FROM answers a 
                          JOIN questions q ON a.question_id = q.id 
                          WHERE q.topic_id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM questions WHERE topic_id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception("Không tìm thấy chủ đề để xóa");
    }
    $stmt->close();

    $conn->commit();
    redirectWithSuccess('dashboard.php', 'Xóa chủ đề thành công!');

} catch (Exception $e) {
    $conn->rollback();
    redirectWithError('dashboard.php', 'Lỗi khi xóa chủ đề: ' . $e->getMessage());
}