<?php
session_start();
require "../config/db.php";
require "../helpers/session_helper.php";


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    redirectWithError('../login.php', 'Vui lòng đăng nhập với tư cách giáo viên');
}
$teacher_username = $_SESSION['username'];
$teacher_id = $_SESSION['user_id'];
$qr_code = "";
$sql = "SELECT qr_code FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $teacher_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $qr_code = $row['qr_code'];
} else {
    $_SESSION['error'] = "Không tìm thấy thông tin giáo viên";
    header("Location: ../login.php");
    exit;
}

$stmt->close();
$pin_code = substr($qr_code, -4);
$sql = "
    SELECT t.id, t.title, t.description, t.level, 
           (SELECT COUNT(*) FROM questions q WHERE q.topic_id = t.id) AS total_questions 
    FROM topics t
    WHERE t.create_by = (SELECT id FROM users WHERE username = ?)
    ORDER BY t.id DESC
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
    <title>Dashboard Giáo Viên</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="welcome-message">Xin chào, <span><?php echo htmlspecialchars($teacher_username); ?></span></h1>
            <div class="action-buttons">
                <button class="btn btn-success" onclick="window.location.href='add_topic.php'">
                    <i class="fas fa-plus"></i> Thêm chủ đề
                </button>
                <button class="btn btn-info" onclick="showQRCode()">
                    <i class="fas fa-qrcode"></i> Mã QR
                </button>
                <button class="btn btn-danger" onclick="window.location.href='../logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </div>
        </header>

        <div class="card">
            <h2 class="card-title">Danh sách chủ đề</h2>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Chủ đề</th>
                        <th>Mô tả</th>
                        <th>Độ tuổi</th>
                        <th>Số câu hỏi</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  $stt = 1 ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stt); ?></td>
                             <?php  $stt++ ?>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><span class="badge badge-primary"><?php echo htmlspecialchars($row['level']); ?></span></td>
                            <td><span class="badge badge-success"><?php echo $row['total_questions']; ?></span></td>
                            <td class="action-cell">
                                <button class="btn btn-warning btn-sm" onclick="editTopic(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal QR Code -->
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="hideQRCode()">&times;</span>
            <h2>Mã QR điểm danh</h2>
            <p>Quét mã QR này để tham gia lớp học</p>
            
            <div class="qr-code">
                <?php
                $qr_image_path = "../image/qrcodes/{$qr_code}.png";
                if (file_exists($qr_image_path)) {
                    echo "<img src='{$qr_image_path}' alt='Mã QR'>";
                } else {
                    echo "<p>Không tìm thấy hình ảnh mã QR</p>";
                }
                ?>
            </div>
            
            <div class="pin-code">Mã PIN: <?php echo htmlspecialchars($pin_code); ?></div>
            
            <button class="btn btn-primary" onclick="hideQRCode()">
                <i class="fas fa-check"></i> Đã hiểu
            </button>
        </div>
    </div>

    <script>
        function showQRCode() {
            document.getElementById('qrModal').style.display = 'flex';
        }
        function hideQRCode() {
            document.getElementById('qrModal').style.display = 'none';
        }
        function editTopic(id) {
            window.location.href = 'edit_topic.php?id=' + id;
        }

        function confirmDelete(id) {
            if (confirm('Bạn có chắc chắn muốn xóa chủ đề này? Tất cả câu hỏi liên quan cũng sẽ bị xóa!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete_topic.php';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'topic_id';
                input.value = id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
        window.onclick = function(event) {
            const modal = document.getElementById('qrModal');
            if (event.target === modal) {
                hideQRCode();
            }
        }
    </script>
</body>
</html>