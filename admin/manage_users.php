<?php
include '../config/db.php';
$sql = "SELECT id, username, email, role, qr_code FROM users ORDER BY id ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Lỗi truy vấn: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="vi">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">
                <i class="fas fa-users-cog"></i> Quản lý tài khoản
            </h1>
            <div class="admin-actions">
                <button class="btn btn-success" onclick="window.location.href='add_account.php'">
                    <i class="fas fa-plus"></i> Thêm mới
                </button>
                <button class="btn btn-info" onclick="window.location.href='../generate_missing_qr.php'">
                    <i class="fas fa-qrcode"></i> Tạo QR
                </button>
                <button class="btn btn-danger" onclick="window.location.href='../logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Danh sách tài khoản</div>
            
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Mã QR</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $stt = 1;                    ?>
                    <?php if ($result->num_rows > 0): ?>
                        
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stt); ?></td>
                                <?php $stt++; ?>  
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($row['role']); ?>">
                                        <?php echo htmlspecialchars($row['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($row['qr_code'])): ?>
                                        <img src="../image/qrcodes/<?php echo htmlspecialchars($row['qr_code']); ?>.png" class="qr-code" alt="Mã QR">
                                    <?php else: ?>
                                        <span class="badge" style="background-color: rgba(255, 193, 7, 0.1); color: #d39e00;">
                                            Chưa có
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-warning btn-sm" onclick="editUser(<?php echo $row['id']; ?>)">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <p>Không có tài khoản nào</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editUser(id) {
            window.location.href = 'edit_account.php?id=' + id;
        }

        function confirmDelete(id) {
            if (confirm("Bạn có chắc chắn muốn xóa tài khoản này?\nHành động này không thể hoàn tác!")) {
                window.location.href = 'delete_account.php?id=' + id;
            }
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>