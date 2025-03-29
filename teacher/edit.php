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
        .btn-save { background-color: yellow; }
        .btn-cancel { background-color: orange; color: white; }
        .btn-save:hover, .btn-cancel:hover { opacity: 0.8; }
        .answer-container { width: 100%; margin-bottom: 20px; }
        .answer-input-container { display: flex; align-items: center; margin-bottom: 5px; }
        .answer-input { flex-grow: 1; padding: 10px; border: 2px solid black; box-sizing: border-box; }
        .correct-radio { margin-right: 10px; }
    </style>
</head>
<body>

    <h1>Sửa câu hỏi</h1>

    <form method="POST">
        <label for="question_text">Câu hỏi</label>
        <textarea id="question_text" name="question_text" rows="2" placeholder="Nhập câu hỏi vào đây" required></textarea>

        <div class="answer-container">
        <label for="answer">Đáp án</label>
            <div class="answer-input-container">
                <input type="radio" name="correct_answer" value="1" class="correct-radio">
                <input type="text" name="answer_1" class="answer-input" placeholder="Đáp án 1">
            </div>
            <div class="answer-input-container">
                <input type="radio" name="correct_answer" value="2" class="correct-radio">
                <input type="text" name="answer_2" class="answer-input" placeholder="Đáp án 2">
            </div>
            <div class="answer-input-container">
                <input type="radio" name="correct_answer" value="3" class="correct-radio">
                <input type="text" name="answer_3" class="answer-input" placeholder="Đáp án 3">
            </div>
            <div class="answer-input-container">
                <input type="radio" name="correct_answer" value="4" class="correct-radio">
                <input type="text" name="answer_4" class="answer-input" placeholder="Đáp án 4">
            </div>
        </div>

        <label for="topic">Chủ đề</label>
        <select id="topic" name="topic" required>
            <option value="">Vui lòng chọn chủ đề</option>
            <option value="Module_1">Module_1</option>
            <option value="Module_2">Module_2</option>
            <option value="Module_3">Module_3</option>
        </select>

        <label for="topic">Độ tuổi</label>
        <select id="topic" name="topic" required>
            <option value="">Vui lòng chọn độ tuổi</option>
            <option value="age_12">Lớp 1-2</option>
            <option value="age_35">Lớp 3-5</option>
            <option value="age_68">Lớp 6-8</option>
            <option value="age_912">Lớp 9-12</option>           
        </select>

        <button type="submit" class="btn-save">Lưu</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='dashboard.php'">Hủy</button>
    </form>

</body>
</html>