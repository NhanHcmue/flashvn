<?php
require "config/db.php";
include "libs/phpqrcode/qrlib.php";

// Thư mục lưu QR
$qr_dir = "image/qrcodes/";

// Đảm bảo thư mục tồn tại
if (!file_exists($qr_dir)) {
    mkdir($qr_dir, 0777, true);
}

// Truy vấn tất cả người dùng có qr_code có trong db
$sql = "SELECT id, qr_code FROM users WHERE qr_code IS NOT NULL";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $qr_code = $row['qr_code'];
        $user_id = $row['id'];

        // Đường dẫn file mã QR
        $qr_image_path = $qr_dir . $qr_code . ".png";

        // Xóa file mã QR cũ nếu tồn tại (vì URL đã thay đổi)
        if (file_exists($qr_image_path)) {
            unlink($qr_image_path);
        }

        // Tạo URL cho mã QR với tên miền mới
        $qr_url = "https://localhost/flashvn/level.php?user_id=" . urlencode($qr_code);
        // Tạo và lưu hình ảnh mã QR
        QRcode::png($qr_url, $qr_image_path, QR_ECLEVEL_L, 5);

        echo "Đã tạo mã QR cho user_id=$user_id, qr_code=$qr_code<br>";
    }
} else {
    echo "Không tìm thấy người dùng nào có qr_code!";
}

$conn->close();
?>