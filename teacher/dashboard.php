<?php
include '../config/db.php';

session_start();
if (!isset($_SESSION['username'])) {
    echo "Bạn chưa đăng nhập.";
    exit;
}

$teacher_username = $_SESSION['username'];
$teacher_id = $_SESSION['user_id'];
$qr_code = "";

// Lấy qr_code từ cơ sở dữ liệu
$sql = "SELECT qr_code FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $teacher_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $qr_code = $row['qr_code'];
} else {
    echo "Không tìm thấy thông tin giáo viên.";
    exit;
}

$stmt->close();

// Lấy 4 ký tự cuối của qr_code làm mã phòng
$pin_code = substr($qr_code, -4); // Lấy 4 ký tự cuối

// Lấy danh sách chủ đề
$sql = "
    SELECT t.id, t.title, t.description, t.level, 
           (SELECT COUNT(*) FROM questions q WHERE q.topic_id = t.id) AS total_questions 
    FROM topics t
    WHERE t.create_by = (SELECT id FROM users WHERE username = ?)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $teacher_username);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Giáo viên</title>
    <style>
        body {padding: 20px; font-family: Arial, sans-serif;}
        h1 {font-size: 1.8em; text-align: left; margin-left: 10%;}
        .btn-container {display: flex; gap: 10px; margin-bottom: 15px; margin-left: 10%;}
        .btn {width: 100px; height: 40px; font-size: 1.2em; font-weight: bold; text-align: center; border: none; border-radius: 10px; cursor: pointer;}
        .btn-add {background-color: green; color: white;}
        .btn-logout {background-color: burlywood; color: white;}
        .btn-qr, .close-button {background-color: gray; color: white;}
        .btn-edit {background-color: yellow; color: black;}
        .btn-delete {background-color: red; color: black;}
        .btn:hover { opacity: 0.8;}
        table {width: 80%; margin: 0 auto; border-collapse: collapse; font-size: 1.2em;}
        th, td {padding: 10px; text-align: center;}
        th {background-color: gray; color: white;}
        #qr-container {display: none; margin-top: 20px; text-align: center; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3); border-radius: 10px;}
        .close-button {background-color: red; color: white; margin-top: 10px; border-radius: 10px;}
        .pin-code {margin-top: 10px; font-size: 1.2em; font-weight: bold; color: #333;}
    </style>
</head>
<body>
    <h1>Xin chào, Giáo viên!</h1>
    <div class="btn-container">
        <button class="btn btn-add" onclick="window.location.href='add.html'">Thêm</button>
        <button class="btn btn-qr" onclick="showQRCode()">QR</button>
        <div id="qr-container">
            <p>Quét mã QR để tiếp tục:</p>
            <?php
            $qr_image_path = "../image/qrcodes/{$qr_code}.png";
            if (file_exists($qr_image_path)) {
                echo "<img src='{$qr_image_path}' alt='Mã QR' style='width: 400px; height: 400px;'>";
            } else {
                echo "<p>Không tìm thấy hình ảnh mã QR!</p>";
            }
            ?>
            <div class="pin-code">Mã: <?php echo htmlspecialchars($pin_code); ?></div>
            <button class="close-button" onclick="hideQRCode()">Đóng</button>
        </div>
        <button class="btn btn-logout" onclick="window.location.href='../logout.php'">Thoát</button>
    </div>


    <table>
        <thead>
            <tr>
                <th>Chủ đề</th>
                <th>Mô tả</th>
                <th>Độ tuổi</th>
                <th>Số câu hỏi</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['level']); ?></td>
                    <td><?php echo $row['total_questions']; ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="editTopic(<?php echo $row['id']; ?>)">Sửa</button>
                        <button class="btn btn-delete" onclick="deleteTopic(<?php echo $row['id']; ?>)">Xóa</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <script>
        function hideQRCode() {
            document.getElementById("qr-container").style.display = "none";
        }
        function editTopic(id) {
            window.location.href = 'edit.php?id=' + id;
        }

        function deleteTopic(id) {
            if (confirm("Bạn có chắc muốn xóa câu hỏi này?")) {
                window.location.href = 'delete.php?id=' + id;
            }
        }

        function showQRCode() {
            document.getElementById("qr-container").style.display = "block";
        }
    </script>
</body>
</html>
