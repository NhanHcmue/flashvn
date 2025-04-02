<?php
require '../config/db.php';

$teacher_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$topic_id = isset($_GET['topic_id']) ? intval($_GET['topic_id']) : 0;

if ($teacher_id === 0 || $topic_id === 0) {
    die("Thiếu thông tin giáo viên hoặc chủ đề!");
}

// Lấy câu hỏi ngẫu nhiên từ topic của giáo viên
$stmt = $conn->prepare("SELECT id, question_text, option_1, option_2, option_3, option_4, correct_option FROM questions WHERE teacher_id = ? AND topic_id = ? ORDER BY RAND() LIMIT 1");
$stmt->bind_param("ii", $teacher_id, $topic_id);
$stmt->execute();
$result = $stmt->get_result();
$question = $result->fetch_assoc();
$stmt->close();

if (!$question) {
    die("Không tìm thấy câu hỏi!");
}

$options = [
    1 => $question['option_1'],
    2 => $question['option_2'],
    3 => $question['option_3'],
    4 => $question['option_4']
];
shuffle($options); // Tráo ngẫu nhiên câu trả lời
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Câu Hỏi</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="style2.css">
    <style>
        .dice-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .dice {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            background-color: white;
            border: 2px solid black;
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            animation: none;
        }
        @keyframes roll {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        let totalScore = 0;
        let correctCount = 0;
        let currentDiceValue = 1;
        
        function rollDice() {
            const dice = document.getElementById('dice');
            dice.style.animation = 'roll 0.6s ease-out';
            setTimeout(() => {
                currentDiceValue = Math.floor(Math.random() * 6) + 1;
                dice.textContent = currentDiceValue;
                dice.style.animation = 'none';
            }, 600);
        }
        
        function checkAnswer(correctAnswer) {
            let selected = document.querySelector('input[name="answer"]:checked');
            if (!selected) {
                alert("Vui lòng chọn một đáp án!");
                return;
            }
            let isCorrect = parseInt(selected.value) === correctAnswer;
            let options = document.querySelectorAll('.correct-radio');
            options.forEach(option => {
                option.parentElement.classList.remove('correct', 'incorrect');
                if (parseInt(option.value) === correctAnswer) {
                    option.parentElement.classList.add('correct');
                }
            });
            if (isCorrect) {
                let points = 2 * currentDiceValue;
                totalScore += points;
                correctCount++;
                document.getElementById("score").textContent = totalScore;
            }
        }
        
        function nextQuestion() {
            rollDice();
            setTimeout(() => {
                window.location.href = `question.php?teacher_id=<?= $teacher_id ?>&topic_id=<?= $topic_id ?>`;
            }, 800);
        }
    </script>
</head>
<body onload="rollDice()">
    <div class="question-container">
        <div class="dice-container">
            <div id="dice" class="dice">6</div>
        </div>
        <label><?= htmlspecialchars($question['question_text']) ?></label>
        <div class="answer-container">
            <?php foreach ($options as $key => $option): ?>
                <div class="answer-input-container">
                    <input type="radio" name="answer" value="<?= $key ?>" class="correct-radio">
                    <?= htmlspecialchars($option) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="button-container">
            <button onclick="checkAnswer(<?= $question['correct_option'] ?>)" class="answer-button">Đáp án</button>
            <button onclick="nextQuestion()" class="next-button">Câu kế tiếp</button>
        </div>
    </div>
    <div id="scoreboard">Tổng điểm: <span id="score">0</span></div>
</body>
</html>