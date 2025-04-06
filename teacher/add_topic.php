<?php
session_start();
require_once '../config/db.php';
require "../helpers/session_helper.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    redirectWithError('../login.php', 'Vui lòng đăng nhập với tư cách giáo viên');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $teacher_id = $_SESSION['user_id'];
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Tiêu đề chủ đề không được để trống';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Tiêu đề chủ đề quá dài (tối đa 255 ký tự)';
    }
    
    if (empty($description)) {
        $errors[] = 'Mô tả chủ đề không được để trống';
    }
    
    if (empty($level)) {
        $errors[] = 'Vui lòng chọn độ tuổi';
    }
    
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: add_topic.php');
        exit();
    }

    try {
        $conn->begin_transaction();
        $stmt = $conn->prepare("INSERT INTO topics (title, description, level, create_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $description, $level, $teacher_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi thêm chủ đề: " . $conn->error);
        }

        $topic_id = $conn->insert_id;
        $stmt->close();

        if (!empty($_POST['questions'])) {
            foreach ($_POST['questions'] as $question) {
                $content = trim($question['content'] ?? '');
                $correct_answer = (int)($question['correct_answer'] ?? 0);
                
                if (empty($content)) {
                    throw new Exception("Nội dung câu hỏi không được để trống!");
                }

                $stmt = $conn->prepare("INSERT INTO questions (content, topic_id) VALUES (?, ?)");
                $stmt->bind_param("si", $content, $topic_id);
                
                if (!$stmt->execute()) {
                    throw new Exception("Lỗi khi thêm câu hỏi: " . $conn->error);
                }
                
                $question_id = $conn->insert_id;
                $stmt->close();

                if (!empty($question['answers'])) {
                    foreach ($question['answers'] as $index => $answer_content) {
                        $answer_content = trim($answer_content ?? '');
                        $is_correct = ($index == $correct_answer) ? 1 : 0;
                        
                        if (empty($answer_content)) {
                            throw new Exception("Nội dung đáp án không được để trống!");
                        }
                        
                        $stmt = $conn->prepare("INSERT INTO answers (question_id, content, is_correct) VALUES (?, ?, ?)");
                        $stmt->bind_param("isi", $question_id, $answer_content, $is_correct);
                        
                        if (!$stmt->execute()) {
                            throw new Exception("Lỗi khi thêm đáp án: " . $conn->error);
                        }
                        $stmt->close();
                    }
                }
            }
        }

        $conn->commit();
        redirectWithSuccess('dashboard.php', 'Thêm chủ đề mới thành công!');
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        redirectWithError('add_topic.php', 'Lỗi: ' . $e->getMessage());
        exit();
    }
}
displayAddTopicForm();
exit();

function displayAddTopicForm() {
    $old_input = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);
    
    $errors = $_SESSION['form_errors'] ?? [];
    unset($_SESSION['form_errors']);
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thêm chủ đề mới</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="styles.css">
        <style>
            .question-container {
                border: 1px solid #dee2e6;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                border-radius: 0.375rem;
                background-color: #f8f9fa;
                position: relative;
            }
            .answer-container {
                margin-bottom: 1rem;
                padding: 0.75rem;
                border-radius: 0.25rem;
            }
            .correct-answer {
                background-color: #e6f7e6;
                border-left: 4px solid #28a745;
            }
            .form-section {
                background-color: white;
                border-radius: 0.5rem;
                padding: 2rem;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                margin-bottom: 2rem;
            }
            .error-list {
                list-style-type: none;
                padding-left: 0;
            }
        </style>
    </head>
    <body class="bg-light">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Thêm chủ đề mới</h1>
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="error-list mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-section">
                        <form action="add_topic.php" method="POST" id="topicForm">
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Tiêu đề chủ đề <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= htmlspecialchars($old_input['title'] ?? '') ?>" required>
                                    <div class="form-text">Tối đa 255 ký tự</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Mô tả chủ đề <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="2" required><?= htmlspecialchars($old_input['description'] ?? '') ?></textarea>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="level" class="form-label">Độ tuổi <span class="text-danger">*</span></label>
                                    <select class="form-select" id="level" name="level" required>
                                        <option value="">Vui lòng chọn độ tuổi</option>
                                        <option value="1-2" <?= isset($old_input['level']) && $old_input['level'] === '1-2' ? 'selected' : '' ?>>Lớp 1-2</option>
                                        <option value="3-5" <?= isset($old_input['level']) && $old_input['level'] === '3-5' ? 'selected' : '' ?>>Lớp 3-5</option>
                                        <option value="6-8" <?= isset($old_input['level']) && $old_input['level'] === '6-8' ? 'selected' : '' ?>>Lớp 6-8</option>
                                        <option value="9-12" <?= isset($old_input['level']) && $old_input['level'] === '9-12' ? 'selected' : '' ?>>Lớp 9-12</option>
                                    </select>
                                </div>
                            </div>
                            
                            <h4 class="mb-3">Câu hỏi</h4>
                            <div id="questionsContainer">
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-primary" id="addQuestionBtn">
                                    <i class="bi bi-plus-circle"></i> Thêm câu hỏi
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Lưu chủ đề
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template cho câu hỏi -->
        <template id="questionTemplate">
            <div class="question-container">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 remove-question" 
                        title="Xóa câu hỏi">
                    <i class="bi bi-trash"></i>
                </button>
                
                <div class="mb-3">
                    <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control question-content" name="questions[][content]" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                    <select class="form-select correct-answer-select" name="questions[][correct_answer]" required>
                        <option value="0">Đáp án 1</option>
                        <option value="1">Đáp án 2</option>
                        <option value="2">Đáp án 3</option>
                        <option value="3">Đáp án 4</option>
                    </select>
                </div>
                
                <div class="answers-container">
                    <div class="answer-container">
                        <label class="form-label">Đáp án 1 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control answer-input" name="questions[][answers][]" required>
                    </div>
                    <div class="answer-container">
                        <label class="form-label">Đáp án 2 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control answer-input" name="questions[][answers][]" required>
                    </div>
                    <div class="answer-container">
                        <label class="form-label">Đáp án 3</label>
                        <input type="text" class="form-control answer-input" name="questions[][answers][]">
                    </div>
                    <div class="answer-container">
                        <label class="form-label">Đáp án 4</label>
                        <input type="text" class="form-control answer-input" name="questions[][answers][]">
                    </div>
                </div>
            </div>
        </template>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const questionsContainer = document.getElementById('questionsContainer');
                const addQuestionBtn = document.getElementById('addQuestionBtn');
                const questionTemplate = document.getElementById('questionTemplate');
                
                function addQuestion() {
                    const questionClone = questionTemplate.content.cloneNode(true);
                    const questionIndex = document.querySelectorAll('.question-container').length;
                    const questionDiv = questionClone.querySelector('.question-container');
                    
                    questionDiv.querySelectorAll('[name]').forEach(input => {
                        const name = input.name.replace(/\[\d*\]/g, `[${questionIndex}]`);
                        input.name = name;
                    });
                    
                    questionDiv.querySelector('.remove-question').addEventListener('click', function() {
                        if (confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
                            questionDiv.remove();
                        }
                    });
                    
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
                }
                
                addQuestionBtn.addEventListener('click', addQuestion);
                addQuestion();
            });
        </script>
    </body>
    </html>
    <?php
}