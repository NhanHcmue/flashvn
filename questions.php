<?php
session_start();
if (!isset($_SESSION['teacher_id']) || !isset($_GET['topic_id'])) {
    die("Lỗi: Không tìm thấy dữ liệu.");
}
$teacher_id = $_SESSION['teacher_id'];
$topic_id = $_GET['topic_id'];
echo "Hiển thị câu hỏi của giáo viên $teacher_id, chủ đề $topic_id";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Câu Hỏi</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function rollDice() {
            const dice = document.getElementById('dice');
            const randomRotation = Math.floor(Math.random() * 360);
            dice.style.transform = `rotate(${randomRotation}deg)`;
        }
    </script>
    <style>
        label { font-size: 1.2em; font-weight: bold; display: block; text-align: left; margin: 10px 0; }
        .answer-container { width: 100%; margin-bottom: 20px; }
        .answer-input-container { display: flex; align-items: center; margin-bottom: 5px; }
        .answer-input { flex-grow: 1; padding: 10px; border: 2px solid black; box-sizing: border-box; }
        .correct-radio { margin-right: 10px; }
    </style>
</head>
<body>
    <div class="question-container">
        <div class="dice-container">
            <img id="dice" src="image/dice6.png" alt="Xúc xắc" class="dice-img" onclick="rollDice()">
        </div>
        <label for="question_text">"Thao túng thông tin" (Information Manipulation) có thể gây ra điều gì?</label>
        
        <div class="answer-container">
            <div class="answer-input-container">
                <input type="radio" name="correct_answer" value="1" class="correct-radio">
                A. Nhiều mạng xã hội hơn
            </div>
            <div class="answer-input-container">
                <input type="radio" name="correct_answer" value="2" class="correct-radio">
                B. Mất tiếp cận thông tin
            </div>
            <div class="answer-input-container">
                <input type="radio" name="correct_answer" value="3" class="correct-radio">
                C. Nhận thức sai lệch về sự thật
            </div>
            <div class="answer-input-container">
                <input type="radio" name="correct_answer" value="4" class="correct-radio">
                D. Nhận thức sai lệch
            </div>
        </div>


        <div class="button-container">
            <button class="answer-button">Đáp án</button>
            <button class="next-button">Câu kế tiếp</button>
        </div>
    </div>
</body>
</html>

