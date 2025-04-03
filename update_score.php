<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_score = isset($_POST['total_score']) ? (int)$_POST['total_score'] : 0;
    $correct_count = isset($_POST['correct_count']) ? (int)$_POST['correct_count'] : 0;
    $wrong_count = isset($_POST['wrong_count']) ? (int)$_POST['wrong_count'] : 0;

    $_SESSION['total_score'] = $total_score;
    $_SESSION['correct_count'] = $correct_count;
    $_SESSION['wrong_count'] = $wrong_count;

    echo "Score updated successfully";
} else {
    echo "Invalid request";
}
?>