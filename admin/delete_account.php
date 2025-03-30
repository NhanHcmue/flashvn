<?php
require "../config/db.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "❌ Lỗi khi xóa tài khoản!";
    }
    $stmt->close();
}
$conn->close();
?>
