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
    <title>แจ้งซ่อมอุปกรณ์-องค์การบริหารส่วนจังหวัดปัตตานี</title>

    <link rel="stylesheet" href="../css/navbar_user.css">

</head>

<body>
    <div class="navbar">
        <h2>LOGO</h2>
        <ul>
            <li><a href="#home">หน้าหลัก</a></li>
            <hr>
            <li><a href="#report">แจ้งซ่อม</a></li>
            <hr>
            <li><a href="#history">ประวัติการซ่อม</a></li>
            <hr>
            <li><a href="#contact">ติดต่อเรา</a></li>
        </ul>
    </div>
    <hr>
    <div class="content">
        <h1>ยินดีต้อนรับ!</h1>
        <p>นี่คือหน้าเว็บไซต์สำหรับการแจ้งซ่อมอุปกรณ์.</p>
    </div>
</body>

</html>