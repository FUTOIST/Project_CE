<?php

session_start();
require_once '../../db_pao.php';
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
    <title>การแจ้งซ่อมอุปกรณ์</title>

    <link rel="stylesheet" href="../../css/navbar_user.css">

</head>

<body>

</body>

</html>