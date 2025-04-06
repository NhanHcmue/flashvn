<?php
require "../config/db.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    
    try {
        $conn->begin_transaction();
        
        $sql = "DELETE a FROM answers a 
                JOIN questions q ON a.question_id = q.id 
                JOIN topics t ON q.topic_id = t.id 
                WHERE t.create_by = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        $sql = "DELETE q FROM questions q 
                JOIN topics t ON q.topic_id = t.id 
                WHERE t.create_by = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        $sql = "DELETE FROM topics WHERE create_by = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $conn->commit();
        header("Location: manage_users.php?success=1");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: manage_users.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}

$conn->close();
?>