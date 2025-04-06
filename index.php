<?php
session_start();
require "config/db.php";
require "helpers/session_helper.php";

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        redirectWithSuccess('admin/manage_users.php', 'Chào mừng trở lại');
    } else {
        redirectWithSuccess('teacher/dashboard.php', 'Chào mừng trở lại');
    }
}
redirectWithError('login.php', 'Vui lòng đăng nhập');