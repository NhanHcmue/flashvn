<?php
include '../config/db.php';
require "../helpers/session_helper.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php?error=invalid_topic");
    exit;
}

$topic_id = (int)$_GET['id'];
$sql = "SELECT * FROM topics WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$topic_result = $stmt->get_result();

if ($topic_result->num_rows === 0) {
    header("Location: dashboard.php?error=topic_not_found");
    exit;
}

$topic = $topic_result->fetch_assoc();
$stmt->close();

$sql = "SELECT q.id AS question_id, q.content AS question_content, 
               a.id AS answer_id, a.content AS answer_content, a.is_correct
        FROM questions q
        LEFT JOIN answers a ON q.id = a.question_id
        WHERE q.topic_id = ?
        ORDER BY q.id, a.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$questions_result = $stmt->get_result();
$stmt->close();
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
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Chủ Đề: <?= htmlspecialchars($topic['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Chỉnh sửa Chủ Đề: <?= htmlspecialchars($topic['title']) ?></h1>
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-section">
                    <form id="topicForm" method="POST" action="update_topic_handler.php">
                        <input type="hidden" name="topic_id" value="<?= $topic_id ?>">
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Tiêu đề chủ đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= htmlspecialchars($topic['title']) ?>" required>
                                <div class="form-text">Tối đa 255 ký tự</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Mô tả chủ đề <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="2" required><?= htmlspecialchars($topic['description']) ?></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Độ tuổi <span class="text-danger">*</span></label>
                                <select class="form-select" id="level" name="level" required>
                                    <option value="">Vui lòng chọn độ tuổi</option>
                                    <option value="1-2" <?= $topic['level'] === '1-2' ? 'selected' : '' ?>>Lớp 1-2</option>
                                    <option value="3-5" <?= $topic['level'] === '3-5' ? 'selected' : '' ?>>Lớp 3-5</option>
                                    <option value="6-8" <?= $topic['level'] === '6-8' ? 'selected' : '' ?>>Lớp 6-8</option>
                                    <option value="9-12" <?= $topic['level'] === '9-12' ? 'selected' : '' ?>>Lớp 9-12</option>
                                </select>
                            </div>
                        </div>
                        
                        <h4 class="mb-3">Câu hỏi</h4>
                        <div id="questionsContainer">
                            <?php foreach ($questions as $q_id => $question): ?>
                                <div class="question-container" data-question-id="<?= $q_id ?>">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 remove-question" 
                                            title="Xóa câu hỏi">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Câu hỏi #<?= $q_id ?></label>
                                        <textarea class="form-control question-content" name="question[<?= $q_id ?>][content]" required><?= htmlspecialchars($question['content']) ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                                        <select class="form-select correct-answer-select" name="question[<?= $q_id ?>][correct_answer]" required>
                                            <?php foreach ($question['answers'] as $index => $answer): ?>
                                                <option value="<?= $answer['id'] ?>" <?= $answer['is_correct'] ? 'selected' : '' ?>>
                                                    Đáp án <?= $index + 1 ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="answers-container">
                                        <?php foreach ($question['answers'] as $index => $answer): ?>
                                            <div class="answer-container <?= $answer['is_correct'] ? 'correct-answer' : '' ?>">
                                                <label class="form-label">Đáp án <?= $index + 1 ?> <?= $answer['is_correct'] ? '(Đúng)' : '' ?></label>
                                                <input type="text" class="form-control answer-input" 
                                                       name="question[<?= $q_id ?>][answers][<?= $answer['id'] ?>]" 
                                                       value="<?= htmlspecialchars($answer['content']) ?>" 
                                                       required>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-primary" id="addQuestionBtn">
                                <i class="bi bi-plus-circle"></i> Thêm câu hỏi
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Template cho câu hỏi mới -->
    <template id="newQuestionTemplate">
        <div class="question-container" data-new-question="true">
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 remove-question" 
                    title="Xóa câu hỏi">
                <i class="bi bi-trash"></i>
            </button>
            
            <div class="mb-3">
                <label class="form-label">Câu hỏi mới</label>
                <textarea class="form-control question-content" name="new_questions[][content]" required></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                <select class="form-select correct-answer-select" name="new_questions[][correct_answer]" required>
                    <option value="0">Đáp án 1</option>
                    <option value="1">Đáp án 2</option>
                    <option value="2">Đáp án 3</option>
                    <option value="3">Đáp án 4</option>
                </select>
            </div>
            
            <div class="answers-container">
                <div class="answer-container">
                    <label class="form-label">Đáp án 1 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control answer-input" name="new_questions[][answers][0]" required>
                </div>
                <div class="answer-container">
                    <label class="form-label">Đáp án 2 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control answer-input" name="new_questions[][answers][1]" required>
                </div>
                <div class="answer-container">
                    <label class="form-label">Đáp án 3</label>
                    <input type="text" class="form-control answer-input" name="new_questions[][answers][2]">
                </div>
                <div class="answer-container">
                    <label class="form-label">Đáp án 4</label>
                    <input type="text" class="form-control answer-input" name="new_questions[][answers][3]">
                </div>
            </div>
        </div>
    </template>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questionsContainer = document.getElementById('questionsContainer');
            const addQuestionBtn = document.getElementById('addQuestionBtn');
            const newQuestionTemplate = document.getElementById('newQuestionTemplate');
            
            // Xử lý xóa câu hỏi
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-question') || 
                    e.target.parentElement.classList.contains('remove-question')) {
                    const questionBlock = e.target.closest('.question-container');
                    if (questionBlock && confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
                        const questionId = questionBlock.dataset.questionId;
                        if (questionId) {
                            // Nếu là câu hỏi đã có trong DB, thêm input ẩn để đánh dấu xóa
                            const deleteInput = document.createElement('input');
                            deleteInput.type = 'hidden';
                            deleteInput.name = `deleted_questions[]`;
                            deleteInput.value = questionId;
                            document.getElementById('topicForm').appendChild(deleteInput);
                        }
                        questionBlock.remove();
                    }
                }
            });
            
            // Thêm câu hỏi mới
            addQuestionBtn.addEventListener('click', function() {
                const questionClone = newQuestionTemplate.content.cloneNode(true);
                const questionIndex = document.querySelectorAll('.question-container[data-new-question="true"]').length;
                const questionDiv = questionClone.querySelector('.question-container');
                
                // Cập nhật name attributes với index mới
                questionDiv.querySelectorAll('[name]').forEach(input => {
                    const name = input.name.replace(/\[\]/g, `[${questionIndex}]`);
                    input.name = name;
                });
                
                // Highlight đáp án đúng
                const correctAnswerSelect = questionDiv.querySelector('.correct-answer-select');
                const answerInputs = questionDiv.querySelectorAll('.answer-input');
                
                function highlightCorrectAnswer() {
                    answerInputs.forEach((input, index) => {
                        input.parentElement.classList.toggle('correct-answer', index == correctAnswerSelect.value);
                    });
                }
                
                correctAnswerSelect.addEventListener('change', highlightCorrectAnswer);
                highlightCorrectAnswer();
                
                questionsContainer.appendChild(questionClone);
            });
            
            // Highlight đáp án đúng cho các câu hỏi hiện có
            document.querySelectorAll('.correct-answer-select').forEach(select => {
                const answerInputs = select.closest('.question-container').querySelectorAll('.answer-input');
                
                select.addEventListener('change', function() {
                    answerInputs.forEach((input, index) => {
                        input.parentElement.classList.toggle('correct-answer', index == this.value);
                    });
                });
            });
            
            // Validate form
            document.getElementById('topicForm').addEventListener('submit', function(e) {
                let isValid = true;
                const questionBlocks = document.querySelectorAll('.question-container');
                
                if (questionBlocks.length === 0) {
                    alert('Vui lòng thêm ít nhất một câu hỏi!');
                    isValid = false;
                }
                
                questionBlocks.forEach(block => {
                    const textarea = block.querySelector('textarea');
                    if (!textarea.value.trim()) {
                        alert('Vui lòng nhập nội dung câu hỏi!');
                        textarea.focus();
                        isValid = false;
                        return;
                    }
                    
                    const answerInputs = block.querySelectorAll('.answer-input[required]');
                    answerInputs.forEach(input => {
                        if (!input.value.trim()) {
                            alert('Vui lòng nhập đầy đủ các đáp án bắt buộc!');
                            input.focus();
                            isValid = false;
                            return;
                        }
                    });
                });
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>