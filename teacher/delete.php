<?php
include '../config/db.php';

if (isset($_GET['id'])) {
    $topic_id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();
    header("Location: dashboard.php?message=delete_success");
    exit;
} else {
    echo "Yêu cầu không hợp lệ!";
}
