<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Dashboard - Giáo viên</title>
    <style>
        body {
            text-align: center;
            padding: 20px;
        }

        h1 {
            font-size: 1.8em;
        }

        .btn-add {
            background-color: green;
            color: white;
            font-size: 1.2em;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin: 10px 0;
        }

        .btn-add:hover {
            opacity: 0.8;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            font-size: 1.2em;
        }

        th, td {
            border: 2px solid black;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: gray;
            color: white;
        }

        .btn-edit {
            background-color: yellow;
            color: black;
            font-weight: bold;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .btn-delete {
            background-color: red;
            color: white;
            font-weight: bold;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

    <h1>Xin chào, Giáo viên!</h1>

    <button class="btn-add" onclick="window.location.href='add.php'">Thêm</button>

    <table>
        <thead>
            <tr>
                <th>Câu hỏi</th>
                <th>Chủ đề</th>
                <th>Sửa</th>
                <th>Xóa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tên_Câu_Hỏi</td>
                <td>Module_1</td>
                <td><button class="btn-edit" onclick="editQuestion(1)">Sửa</button></td>
                <td><button class="btn-delete" onclick="deleteQuestion(1)">X</button></td>
            </tr>
            <tr>
                <td>Tên_Câu_Hỏi</td>
                <td>Module_2</td>
                <td><button class="btn-edit" onclick="editQuestion(2)">Sửa</button></td>
                <td><button class="btn-delete" onclick="deleteQuestion(2)">X</button></td>
            </tr>
        </tbody>
    </table>

    <script>
        function editQuestion(id) {
            window.location.href = 'edit.php?id=' + id;
        }

        function deleteQuestion(id) {
            if (confirm("Bạn có chắc muốn xóa câu hỏi này?")) {
                window.location.href = 'delete.php?id=' + id;
            }
        }
    </script>

</body>
</html>
