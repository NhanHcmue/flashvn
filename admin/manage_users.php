<?php
include '../config/db.php'; // Kết nối database

// Truy vấn danh sách tài khoản từ bảng users
$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <style>
        body {padding: 20px; font-family: Arial, sans-serif;}
        h1 {font-size: 1.8em; text-align: left; margin-left: 10%;}
        .btn {width: 100px; height: 40px; font-size: 1.2em; font-weight: bold; text-align: center; border: none; border-radius: 10px; cursor: pointer;}
        .btn-add {background-color: green; color: white; margin-left: 10%; margin-bottom: 10px;}
        .btn-edit {background-color: yellow; color: black;}
        .btn-delete {background-color: red; color: black;}
        .btn:hover { opacity: 0.8;}
        table {width: 80%; margin: 0 auto; border-collapse: collapse; font-size: 1.2em;}
        th, td {padding: 10px; text-align: center;}
        th {background-color: gray; color: white;}
    </style>
</head>
<body>

    <h1>Xin chào, Admin!</h1>
    <button class="btn btn-add" onclick="window.location.href='add_account.php'">Thêm</button>

    <table>
        <thead>
            <tr>
                <th>Tên tài khoản</th>
                <th>Email</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['username']}</td>
                        <td>{$row['email']}</td>
                        <td>
                            <button class='btn btn-edit' onclick='editUser({$row['id']})'>Sửa</button>
                            <button class='btn btn-delete' onclick='deleteUser({$row['id']})'>Xóa</button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Không có tài khoản nào</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>

    <script>
        function editUser(id) {
            window.location.href = 'edit_account.php?id=' + id;
        }

        function deleteUser(id) {
            if (confirm("Bạn có chắc muốn xóa tài khoản này?")) {
                window.location.href = 'delete_account.php?id=' + id;
            }
        }
    </script>

</body>
</html>
