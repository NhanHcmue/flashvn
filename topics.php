<!DOCTYPE html>
<html>
<head>
    <title>Danh sách chủ đề</title>
    <style>
        body {font-family: sans-serif;display: flex;justify-content: center;align-items: center;height: 100vh;margin: 0;background-color: #f4f4f4;}
        .div-container {text-align: center;background-color: white;padding: 40px;border-radius: 8px;box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);}
        h1 {margin-bottom: 20px;}
        button {padding: 10px 20px;margin: 5px;width: 200px;border: none;border-radius: 5px;background-color: #007bff;color: white;cursor: pointer;}
        button:hover {background-color: #0056b3;}
    </style>
</head>
<body>
    <div class="div-container">
        <h1>Chào bạn</h1>
        <p>Vui lòng chọn chủ đề</p>
        <div id="topics-list">
            </div>
    </div>

    <script>
        fetch('get_topics_data.php')
            .then(response => response.json())
            .then(data => {
                const topicsList = document.getElementById('topics-list');
                data.forEach(topic => {
                    const button = document.createElement('button');
                    button.textContent = topic.title;
                    button.addEventListener('click', () => {
                        window.location.href = `questions.php?topic_id=${topic.id}`;
                    });
                    topicsList.appendChild(button);
                });
            });
    </script>
</body>
</html>