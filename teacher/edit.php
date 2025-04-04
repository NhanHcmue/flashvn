<?php
include '../config/db.php';

if (!isset($_GET['id'])) {
    echo "Không tìm thấy chủ đề!";
    exit;
}

$topic_id = $_GET['id'];

$sql = "SELECT * FROM topics WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$topic_result = $stmt->get_result();
$topic = $topic_result->fetch_assoc();
$stmt->close();

$sql = "
    SELECT q.id AS question_id, q.content AS question_content, 
           a.id AS answer_id, a.content AS answer_content, a.is_correct
    FROM questions q
    LEFT JOIN answers a ON q.id = a.question_id
    WHERE q.topic_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$questions_result = $stmt->get_result();
$stmt->close();
$conn->close();

$questions = [];
while ($row = $questions_result->fetch_assoc()) {
    $q_id = $row['question_id'];
    if (!isset($questions[$q_id])) {
        $questions[$q_id] = [
            'content' => $row['question_content'],
            'answers' => []
        ];
    }
    $questions[$q_id]['answers'][] = [
        'id' => $row['answer_id'],
        'content' => $row['answer_content'],
        'is_correct' => $row['is_correct']
    ];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Chủ Đề</title>
    <style>
        body { text-align: center; font-family: Arial, sans-serif; padding: 20px; }
        h1 { background-color: green; color: white; padding: 10px; border-radius: 5px; }
        form { width: 60%; margin: auto; }
        label { display: block; font-weight: bold; margin: 10px 0; text-align: left;}
        textarea, select, input[type="text"] {
            width: 100%; padding: 10px; border: 2px solid black; font-size: 1.1em;
        }
        button { padding: 10px 20px; font-size: 1.2em; border: none; border-radius: 8px; cursor: pointer; margin-top: 15px; }
        .btn-add { background-color: yellowgreen; }
        .btn-cancel { background-color: orange; color: white; }
        .btn-remove { 
            background-color: red; 
        }
        .answer-container { margin-bottom: 20px; }
        .answer-input-container { display: flex; align-items: center; margin-bottom: 5px; }
        .answer-input { flex-grow: 1; padding: 10px; border: 2px solid black; }
        .correct-radio { margin-right: 10px; }
    </style>
    <script>
        function addQuestion() {
            let container = document.querySelector(".question-container");
            let questionCount = document.querySelectorAll(".question-block").length + 1;
            
            let div = document.createElement("div");
            div.classList.add("question-block");
            div.setAttribute("id", `question_block_${questionCount}`);

            div.innerHTML = `
                <label for="question_${questionCount}">Câu hỏi ${questionCount}</label>
                <textarea id="question_${questionCount}" name="question_${questionCount}" rows="2" placeholder="Nhập câu hỏi vào đây" required></textarea>
                <button type="button" class="btn-remove" onclick="removeQuestion(${questionCount})">-</button>
                <div class="answer-container">
                    <label>Đáp án</label>
                    ${[1,2,3,4].map(i => `
                        <div class="answer-input-container">
                            <input type="radio" name="correct_answer_${questionCount}" value="${i}" class="correct-radio">
                            <input type="text" name="answer_${questionCount}_${i}" class="answer-input" placeholder="Đáp án ${i}" required>
                        </div>
                    `).join('')}
                </div>
            `;

            container.appendChild(div);
        }

        function removeQuestion(questionNumber) {
            let questionBlock = document.getElementById(`question_block_${questionNumber}`);
            if (questionBlock) {
                questionBlock.remove();
            }
        }

        document.querySelector("form").addEventListener("submit", function(event) {
            let questionBlocks = document.querySelectorAll(".question-block");
            let valid = true;

            questionBlocks.forEach((block, index) => {
                let questionNumber = index + 1;
                let radioButtons = block.querySelectorAll(`input[name="correct_answer_${questionNumber}"]`);
                let isChecked = Array.from(radioButtons).some(radio => radio.checked);

                if (!isChecked) {
                    alert(`Vui lòng chọn một đáp án đúng cho câu hỏi ${questionNumber}!`);
                    valid = false;
                }

                let answerInputs = block.querySelectorAll(".answer-input");
                answerInputs.forEach((input, idx) => {
                    if (input.value.trim() === "") {
                        alert(`Vui lòng nhập đầy đủ nội dung cho Đáp án ${idx + 1} của Câu hỏi ${questionNumber}!`);
                        valid = false;
                    }
                });
            });

            if (!valid) {
                event.preventDefault();
            }
        });
    </script>
</head>
<body>
    <h1>Chỉnh sửa Chủ Đề</h1>
    <form method="POST" action="update_topic.php">
        <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
        
        <label for="title">Chủ đề</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($topic['title']); ?>" required>

        <label for="description">Mô Tả Chủ đề</label>
        <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($topic['description']); ?>" required>

        <div class="question-container">
            <?php foreach ($questions as $q_id => $question): ?>
                <div class="question-block">
                    <label for="question_<?php echo $q_id; ?>">Câu hỏi</label>
                    <textarea id="question_<?php echo $q_id; ?>" name="question_<?php echo $q_id; ?>" rows="2" required><?php echo htmlspecialchars($question['content']); ?></textarea>
                    <div class="answer-container">
                        <label>Đáp án</label>
                        <?php foreach ($question['answers'] as $index => $answer): ?>
                            <div class="answer-input-container">
                                <input type="radio" name="correct_answer_<?php echo $q_id; ?>" value="<?php echo $answer['id']; ?>" <?php echo ($answer['is_correct'] ? 'checked' : ''); ?>>
                                <input type="text" name="answer_<?php echo $answer['id']; ?>" value="<?php echo htmlspecialchars($answer['content']); ?>" required>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn-add" onclick="addQuestion()">+</button>

        <button type="submit" class="btn-save">Lưu</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='dashboard.php'">Hủy</button>
    </form>
</body>
</html>
