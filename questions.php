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
</head>
<body>
    <div class="question-container">
        <div class="dice-container">
            <img id="dice" src="image/dice6.png" alt="Xúc xắc" class="dice-img" onclick="rollDice()">
        </div>
        
        <p class="question-text">"Thao túng thông tin" (Information Manipulation) có thể gây ra điều gì?</p>
        
        <ul class="answer-list">
            <li>A. Nhiều mạng xã hội hơn</li>
            <li>B. Mất tiếp cận thông tin</li>
            <li>C. Nhận thức sai lệch về sự thật</li>
        </ul>

        <div class="button-container">
            <button class="answer-button">Đáp án</button>
            <button class="next-button">Câu kế tiếp</button>
        </div>
    </div>
</body>
</html>

