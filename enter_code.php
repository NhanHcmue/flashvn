<?php
require "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pin_code = trim($_POST["pin_code"] ?? '');

    // Kiểm tra mã phòng có rỗng không
    if (empty($pin_code)) {
        echo "<script>alert('Vui lòng nhập mã phòng!');</script>";
    } elseif (strlen($pin_code) !== 4) {
        echo "<script>alert('Mã phòng phải là 4 ký tự!');</script>";
    } else {
        // Tìm qr_code có 4 ký tự cuối khớp với mã phòng
        $stmt = $conn->prepare("SELECT id, qr_code FROM users WHERE SUBSTRING(qr_code, -4) = ?");
        $stmt->bind_param("s", $pin_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $qr_code = $row['qr_code'];
            // Chuyển hướng đến level.php với user_id là qr_code
            header("Location: level.php?user_id=$qr_code");
            exit;
        } else {
            echo "<script>alert('Mã phòng không hợp lệ!');</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tham gia trò chơi</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {font-family: Arial, sans-serif; text-align: center; padding: 20px; background-color: #f0f0f0;}
        h1 {color: #333;}
        .container {max-width: 600px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);}
        .option {margin: 20px 0;}
        .option h2 {color: #555;}
        .qr-option, .code-option {padding: 20px; border-radius: 5px; margin: 10px 0;}
        input[type="text"] {width: 100%; padding: 10px; margin: 10px 0; border: 2px solid #ccc; border-radius: 5px; font-size: 1.1em;}
        button {padding: 10px 20px;font-size: 1.1em;font-weight: bold;border: none;border-radius: 5px;cursor: pointer;margin: 5px;}
        .btn-scan {background-color: #4CAF50;color: white;}
        .btn-enter {background-color: #2196F3;color: white;}
        .btn-scan:hover, .btn-enter:hover { opacity: 0.8;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Tham gia trò chơi</h1>
        <div class="option qr-option">
            <h2>Lựa chọn 1: Quét mã QR</h2>
            <label>Quét mã QR từ giáo viên để tham gia trò chơi.</label>
            <button class="btn-scan" onclick="alert('Vui lòng sử dụng thiết bị quét mã QR để quét mã từ giáo viên.')">Quét mã QR</button>
        </div>
        <hr>
        <div class="option code-option">
            <h2>Lựa chọn 2: Nhập mã</h2>
            <form method="POST">
                <label for="pin_code">Nhập mã từ giáo viên:</label>
                <input type="text" id="pin_code" name="pin_code" placeholder="Ví dụ: 12a2" required maxlength="4" minlength="4">
                <button type="submit" class="btn-enter">Tham gia</button>
            </form>
        </div>
    </div>
</body>
</html>