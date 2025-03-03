<?php

session_start();
require_once '../../db_pao.php';
if (!isset($_SESSION['user_login'])) {
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../../css/navbar_user.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <img src="../../img_usage/logo.png" alt="โลโก้">
        </div>
        <ul class="menu">
            <li><a href="http://localhost/project_ce/user_pao/user_home.php">แจ้งซ่อมอุปกรณ์</a></li>
            <hr>
            <li><a href="http://localhost/project_ce/user_pao/check_user/user_check.php">ตรวจสอบสถานะการแจ้งซ่อม</a></li>
            <hr>
        </ul>
        <ul class="logout">
            <li>
                <a href="../../logout_pao.php" onclick="return confirm('ต้องการออกจากระบบ ใช่หรือไม่?');">
                    ออกจากระบบ
                </a>
            </li>
        </ul>
    </div>

    <hr>
    <div class="content">
        <div class="container-box">
            <?php
            if (isset($_SESSION['user_login'])) {
                $admin_id = $_SESSION['user_login'];
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = :admin_id");
                $stmt->execute(['admin_id' => $admin_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
                <div class="user-info">
                    <p><strong>USER :</strong> <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            <?php } ?>
        </div>
        <h1>ตรวจสอบสถานะ</h1>
        <p1>สามารถดูและตรวจสอบสถานะการแจ้งซ่อมอุปกรณ์ของท่าน</p1>
        <hr>


    </div>
</body>
<!-- JavaScript Bundle with Popper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>