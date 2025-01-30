<?php

session_start();
require_once '../db_pao.php';
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: \project_ce\index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบสถานะ-องค์การบริหารส่วนจังหวัดปัตตานี</title>

    <link rel="stylesheet" href="../css/navbar_user.css">

</head>

<body>
    <div class="navbar">
        <div class="logo" style="text-align: center;">
            <img src="../img_usage/logo.png" alt="" style="width: 180px; height: auto; ">
        </div>
        <ul>
            <li><a href="http://localhost/project_ce/user_pao/user_home.php">แจ้งซ่อมอุปกรณ์</a></li>
            <hr>
            <li><a href="http://localhost/project_ce/user_pao/user_check.php">ตรวจสอบสถานะการแจ้งซ่อม</a></li>
        </ul>
    </div>
    <hr>
    <div class="content">
        <h1>ตรวจสอบสถานะ</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Iusto quod molestiae itaque mollitia, nihil suscipit?</p>
    </div>
</body>

</html>