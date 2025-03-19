<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Sửa tài khoản</title>
    <style>
        body { text-align: center; padding: 20px; }
        h1 { background-color: green; color: white; padding: 10px; border-radius: 5px; }
        form { width: 60%; margin: auto; }
        label { font-size: 1.2em; font-weight: bold; display: block; text-align: left; margin: 10px 0; }
        input, select { width: 100%; padding: 10px; border: 2px solid black; font-size: 1.1em; }
        button { padding: 10px 20px; font-size: 1.2em; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; margin-top: 15px; }
        .btn-save { background-color: yellow; }
        .btn-cancel { background-color: orange; color: white; }
        .btn-save:hover, .btn-cancel:hover { opacity: 0.8; }
    </style>
</head>
<body>

    <h1>Sửa tài khoản</h1>

    <form method="POST">
        <label for="question_text">Tên tài khoản</label>
        <input id="question_text" name="question_text" rows="2" placeholder="Nhập tên tài khoản" required></input>

        <label for="answer">Email</label>
        <input id="answer" name="answer" rows="2" placeholder="Nhập email" required></input>
        
        <label for="answer">Mật khẩu</label>
        <input id="answer" name="answer" rows="2" placeholder="Nhập mật khẩu" required></i>

        <label for="topic">Vai trò</label>
        <select id="topic" name="topic" required>
            <option value="">Vui lòng chọn vai trò</option>
            <option value="Module_1">Giáo viên</option>
            <option value="Module_2">Admin</option>
        </select>      

        <button type="submit" class="btn-save">Lưu</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='manage_users.php'">Hủy</button>
    </form>

</body>
</html>