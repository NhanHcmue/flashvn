<?php
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_GET['topic_id'])) {
    echo json_encode(['error' => 'Thiếu tham số topic_id']);
    exit;
}

$topic_id = (int)$_GET['topic_id'];

try {
    $sql = "SELECT q.id AS question_id, q.content AS question_content, 
                   a.id AS answer_id, a.content AS answer_content, a.is_correct
            FROM questions q
            LEFT JOIN answers a ON q.id = a.question_id
            WHERE q.topic_id = ?
            ORDER BY q.id, a.id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $q_id = $row['question_id'];
        if (!isset($questions[$q_id])) {
            $questions[$q_id] = [
                'content' => $row['question_content'],
                'answers' => []
            ];
        }
        $questions[$q_id]['answers'][] = [
            'id' => $row['answer_id'],
            'content' => $row['answer_content'],
            'is_correct' => (bool)$row['is_correct']
        ];
    }

    echo json_encode([
        'success' => true,
        'questions' => array_values($questions)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Lỗi khi lấy dữ liệu: ' . $e->getMessage()
    ]);
}