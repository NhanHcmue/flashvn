<?php
function redirectWithError($url, $error) {
    if (!headers_sent()) {
        $_SESSION['error'] = $error;
        header("Location: " . filter_var($url, FILTER_SANITIZE_URL));
        exit();
    } else {
        die("Cannot redirect, headers already sent");
    }
}

function redirectWithSuccess($url, $message) {
    if (!headers_sent()) {
        $_SESSION['success'] = $message;
        header("Location: " . filter_var($url, FILTER_SANITIZE_URL));
        exit();
    } else {
        die("Cannot redirect, headers already sent");
    }
}

function getFlashMessage() {
    $message = '';
    if (!empty($_SESSION['error'])) {
        $message = '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    } elseif (!empty($_SESSION['success'])) {
        $message = '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    return $message;
}