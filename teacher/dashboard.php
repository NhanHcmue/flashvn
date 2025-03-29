<?php
include '../config/db.php';
session_start();

$teacher_email = $_SESSION['email']; // Email của giáo viên đang đăng nhập
$qr_code = ""; 

// Lấy mã giáo viên từ database
$sql = "SELECT qr_code FROM users WHERE email = '$teacher_email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $qr_code = $row['qr_code']; 
}

$conn->close();
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
        .btn-qr, .close-button {background-color: gray; color: white;}
        .btn-edit {background-color: yellow; color: black;}
        .btn-delete {background-color: red; color: black;}
        .btn:hover { opacity: 0.8;}
        table {width: 80%; margin: 0 auto; border-collapse: collapse; font-size: 1.2em;}
        th, td {padding: 10px; text-align: center;}
        th {background-color: gray; color: white;}
        #qr-container {
            display: none;
            margin-top: 20px;
            text-align: center;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }
        .close-button {
            background-color: red;
            color: white;
            margin-top: 10px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <h1>Xin chào, Giáo viên!</h1>
    <div class="btn-container">
        <button class="btn btn-add" onclick="window.location.href='add.php'">Thêm</button>
        <button class="btn btn-qr" onclick="showQRCode()">QR</button>
        <div id="qr-container">
        <p>Quét mã QR để tiếp tục:</p>
        <div id="qrcode"></div>
        <button class="close-button" onclick="hideQRCode()">Đóng</button>
    </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Câu hỏi</th>
                <th>Chủ đề</th>
                <th>Thao tác</th>               
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tên_Câu_Hỏi</td>
                <td>Module_1</td>
                <td><button class="btn btn-edit" onclick="editQuestion(1)">Sửa</button>
                <button class="btn btn-delete" onclick="deleteQuestion(1)">Xóa</button></td>
            </tr>
            <tr>
                <td>Tên_Câu_Hỏi</td>
                <td>Module_2</td>
                <td><button class="btn btn-edit" onclick="editQuestion(2)">Sửa</button>
                <button class="btn btn-delete" onclick="deleteQuestion(2)">Xóa</button></td>
            </tr>
        </tbody>
    </table>   
</body>
</html>
<?php
include '../config/db.php';
session_start();

$teacher_email = $_SESSION['email']; // Email của giáo viên đang đăng nhập
$qr_code = ""; // Mặc định rỗng

// lấy mã QR từ database
$sql = "SELECT qr_code FROM users WHERE email = '$teacher_email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $qr_code = $row['qr_code']; // Lấy mã QR
}

$conn->close();
?>

<script>
    function editQuestion(id) {
        window.location.href = 'edit.php?id=' + id;
    }

    function deleteQuestion(id) {
        if (confirm("Bạn có chắc muốn xóa câu hỏi này?")) {
                window.location.href = 'delete.php?id=' + id;
        }
    }

    function showQRCode() {
    let qrContainer = document.getElementById("qr-container");
    qrContainer.style.display = "block"; // Hiển thị modal mã QR

    let qrDiv = document.getElementById("qrcode");
    qrDiv.innerHTML = ""; // Xóa QR cũ trước khi tạo mới

    let qrData = "https://yourwebsite.com/questions.php?teacher=<?php echo urlencode($qr_code); ?>";

    new QRCode(qrDiv, {
        text: qrData, 
        width: 200,
        height: 200
    });
    }

    function hideQRCode() {
    document.getElementById("qr-container").style.display = "none";
    }
</script>