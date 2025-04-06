<?php
require 'config/db.php';

// Bắt đầu session để lưu số câu đúng và sai
session_start();

// Lấy tham số user_id và topic_id
$user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
$topic_id = isset($_GET['topic_id']) ? trim($_GET['topic_id']) : 0;

// Kiểm tra tham số
if (empty($user_id) || empty($topic_id)) {
    die("Thiếu thông tin giáo viên hoặc chủ đề!");
}

// Xác định teacher_id dựa trên kiểu của user_id
$teacher_id = 0;
if (is_numeric($user_id)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($teacher_id);
        $stmt->fetch();
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT id FROM users WHERE qr_code = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($teacher_id);
        $stmt->fetch();
    }
    $stmt->close();
}

// Kiểm tra giáo viên có tồn tại không
if ($teacher_id === 0) {
    die("Giáo viên không tồn tại!");
}

// Kiểm tra topic_id là số
if (!is_numeric($topic_id)) {
    die("ID chủ đề không hợp lệ!");
}
$topic_id = (int)$topic_id;

// Kiểm tra chủ đề có thuộc về giáo viên không
$stmt = $conn->prepare("SELECT id FROM topics WHERE id = ? AND create_by = ?");
$stmt->bind_param("ii", $topic_id, $teacher_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    die("Chủ đề không thuộc về giáo viên này!");
}
$stmt->close();

// Lấy tất cả câu hỏi trong topic để kiểm tra số lượng
$stmt = $conn->prepare("SELECT id FROM questions WHERE topic_id = ?");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$result = $stmt->get_result();
$all_questions = [];
while ($row = $result->fetch_assoc()) {
    $all_questions[] = $row['id'];
}
$stmt->close();

$total_questions = count($all_questions);

// Khởi tạo session để lưu danh sách câu hỏi đã trả lời
if (!isset($_SESSION['answered_questions'])) {
    $_SESSION['answered_questions'] = [];
}
if (!isset($_SESSION['correct_count'])) {
    $_SESSION['correct_count'] = 0;
}
if (!isset($_SESSION['wrong_count'])) {
    $_SESSION['wrong_count'] = 0;
}

// Kiểm tra xem đã trả lời hết câu hỏi chưa
if (count($_SESSION['answered_questions']) >= $total_questions) {
    // Hiển thị màn hình kết thúc
    $final_score = isset($_SESSION['total_score']) ? $_SESSION['total_score'] : 0;
    $correct_count = $_SESSION['correct_count'];
    $wrong_count = $_SESSION['wrong_count'];
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kết Thúc</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="end-container">
            <img src="image/champ.avif" alt="Cúp vô địch">
            <h1>Bạn dành được <?php echo $final_score; ?> điểm</h1>
            <p>Số câu đúng: <?php echo $correct_count; ?></p>
            <p>Số câu sai: <?php echo $wrong_count; ?></p>
            <button class="exit-button" onclick="window.location.href='level.php?user_id=<?= htmlspecialchars($user_id) ?>'">Thoát</button>
        </div>
    </body>
    </html>
    <?php
    // Reset session sau khi hiển thị kết quả
    unset($_SESSION['answered_questions']);
    unset($_SESSION['total_score']);
    unset($_SESSION['correct_count']);
    unset($_SESSION['wrong_count']);
    exit;
}

// Lấy câu hỏi ngẫu nhiên chưa trả lời
$available_questions = array_diff($all_questions, $_SESSION['answered_questions']);
if (empty($available_questions)) {
    die("Bạn đã hoàn thành tất cả câu hỏi!");
}
$random_question_id = $available_questions[array_rand($available_questions)];

$stmt = $conn->prepare("SELECT id, content FROM questions WHERE id = ?");
$stmt->bind_param("i", $random_question_id);
$stmt->execute();
$result = $stmt->get_result();
$question = $result->fetch_assoc();
$stmt->close();

// Lưu câu hỏi đã trả lời vào session
$_SESSION['answered_questions'][] = $question['id'];

// Lấy đáp án
$question_id = $question['id'];
$stmt = $conn->prepare("SELECT content, is_correct FROM answers WHERE question_id = ?");
$stmt->bind_param("i", $question_id);
$stmt->execute();
$result = $stmt->get_result();
$options = [];
$correct_option = 0;
$index = 1;
while ($answer = $result->fetch_assoc()) {
    $options[$index] = $answer['content'];
    if ($answer['is_correct']) {
        $correct_option = $index;
    }
    $index++;
}
$stmt->close();

if (empty($options)) {
    die("Câu hỏi không có đáp án! Vui lòng thêm đáp án cho câu hỏi này.");
}

shuffle($options); // Tráo ngẫu nhiên câu trả lời
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Câu Hỏi</title>
    <link rel="stylesheet" href="style.css">
    <script>
        let totalScore = <?php echo isset($_SESSION['total_score']) ? $_SESSION['total_score'] : 0; ?>;
        let correctCount = <?php echo $_SESSION['correct_count']; ?>;
        let wrongCount = <?php echo $_SESSION['wrong_count']; ?>;
        let currentDiceValue = 1;

        function rollDice() {
            const dice = document.getElementById('dice');
            dice.className = 'dice';
            setTimeout(() => {
                currentDiceValue = Math.floor(Math.random() * 6) + 1;
                dice.className = `dice show-${currentDiceValue}`;
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
            let correctMessage = document.getElementById('correct-message');
            options.forEach(option => {
                option.parentElement.classList.remove('correct', 'incorrect');
                if (parseInt(option.value) === correctAnswer) {
                    option.parentElement.classList.add('correct');
                    correctMessage.style.display = 'block';
                    correctMessage.textContent = `Câu trả lời đúng là: ${option.parentElement.textContent.trim()}`;
                } else if (option === selected && !isCorrect) {
                    option.parentElement.classList.add('incorrect');
                }
            });
            if (isCorrect) {
                let points = 2 * currentDiceValue;
                totalScore += points;
                correctCount++;
                fetch('update_score.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `total_score=${totalScore}&correct_count=${correctCount}&wrong_count=${wrongCount}`
                });
            } else {
                wrongCount++;
                fetch('update_score.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `total_score=${totalScore}&correct_count=${correctCount}&wrong_count=${wrongCount}`
                });
            }
            document.getElementById("score").textContent = totalScore;
            document.querySelector('.answer-button').disabled = true;
        }

        function nextQuestion() {
            setTimeout(() => {
                window.location.href = `question.php?user_id=<?= htmlspecialchars($user_id) ?>&topic_id=<?= $topic_id ?>`;
            }, 800);
            rollDice();
        }
    </script>
</head>
<body onload="rollDice()">
    <div class="question-container">
        <div id="scoreboard">Tổng điểm: <span id="score"><?php echo isset($_SESSION['total_score']) ? $_SESSION['total_score'] : 0; ?></span></div>
        <div class="dice-container">
            <div id="dice" class="dice">
                <div class="dice-face face-1"><span class="dot"></span></div>
                <div class="dice-face face-2"><span class="dot"></span><span class="dot"></span></div>
                <div class="dice-face face-3"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
                <div class="dice-face face-4"><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
                <div class="dice-face face-5"><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
                <div class="dice-face face-6"><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
            </div>
        </div>
        <label><?= htmlspecialchars($question['content']) ?></label>
        <div class="answer-container">
            <?php foreach ($options as $key => $option): ?>
                <div class="answer-input-container">
                    <input type="radio" name="answer" value="<?= $key ?>" class="correct-radio">
                    <?= htmlspecialchars($option) ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="correct-message" class="correct-message"></div>
        <div class="button-container">
            <button onclick="checkAnswer(<?= $correct_option ?>)" class="answer-button">Đáp án</button>
            <button onclick="nextQuestion()" class="next-button">Câu kế tiếp</button>
        </div>
    </div>
</body>
</html>