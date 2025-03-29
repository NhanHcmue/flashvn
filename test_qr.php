<?php
include 'libs/phpqrcode/qrlib.php'; // thư viện qr

// Tạo QR Code và lưu ảnh vào thư mục
QRcode::png("test, QR Code!", "image/qrcode_test.png", QR_ECLEVEL_L, 5);

echo "QR Code đã được tạo! <br>";
echo "<img src='image/qrcode_test.png'>";
?>
