<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm câu hỏi</title>
    <style>
        body { text-align: center; padding: 20px; }
        h1 { background-color: green; color: white; padding: 10px; border-radius: 5px; }
        form { width: 60%; margin: auto; }
        label { font-size: 1.2em; font-weight: bold; display: block; text-align: left; margin: 10px 0; }
        textarea, select { width: 100%; padding: 10px; border: 2px solid black; font-size: 1.1em; }
        button { padding: 10px 20px; font-size: 1.2em; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; margin-top: 15px; }
        .btn-add { background-color: yellowgreen; }
        .btn-cancel { background-color: orange; color: white; }
        .btn-add:hover, .btn-cancel:hover { opacity: 0.8; }
    </style>
</head>
<body>

    <h1>Thêm câu hỏi</h1>

    <form method="POST">
        <label for="question_text">Câu hỏi</label>
        <textarea id="question_text" name="question_text" rows="2" placeholder="Nhập câu hỏi vào đây" required></textarea>

        <label for="answer">Đáp án</label>
        <textarea id="answer" name="answer" rows="2" placeholder="Nhập đáp án vào đây" required></textarea>

        <label for="topic">Chủ đề</label>
        <select id="topic" name="topic" required>
            <option value="">Vui lòng chọn chủ đề</option>
            <option value="Module_1">Module_1</option>
            <option value="Module_2">Module_2</option>
            <option value="Module_3">Module_3</option>
        </select>

        <button type="submit" class="btn-add">Thêm</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='dashboard.php'">Hủy</button>
    </form>

</body>
</html>
