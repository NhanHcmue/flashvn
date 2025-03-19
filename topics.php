<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn Chủ Đề</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Chào bạn</h1>
    <p>Vui lòng chọn chủ đề</p>
    
    <div class="topics-container">
        <?php
        for ($i = 1; $i <= 6; $i++) {
            echo '<a href="questions.php?module_id=' . $i . '" class="topic-button">Mô-đun ' . $i . '</a>';
        }
        ?>
    </div>
</body>
</html>
