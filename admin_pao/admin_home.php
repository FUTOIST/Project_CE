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
    <title>ADMIN-องค์การบริหารส่วนจังหวัดปัตตานี</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../css/button_pao.css">


</head>

<body>
    <div class="container">
        <div class="d-flex align-items-center justify-content-between" style="padding-top: 20px;">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active" aria-current="page">หน้าหลัก</li>
            </ol>

            <div class="dropdown ms-auto">
                <a href="#" class="btn btn-warning dropdown-toggle px-3 py-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php
                    if (isset($_SESSION['admin_login'])) {
                        $admin_id = $_SESSION['admin_login'];
                        $stmt = $pdo->query("SELECT * FROM users WHERE id_user = $admin_id");
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                    ?>
                    ADMIN : <?php echo $row['firstname'] . ' ' . $row['lastname'] ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end text-small shadow" style="min-width: 322px;">
                    <li><a class="dropdown-item text-danger" href="../logout_pao.php"><i class="bi bi-box-arrow-right"></i> Log out</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container">

        <h2 class="mt-4">ตรวจสอบการแจ้งซ่อม
            <br>
            <hr>

            <a href="http://localhost/project_ce/admin_pao/repair_pao/repair_pao.php" class="button_icon">
                <i class="bi bi-pc-display"></i>
                การแจ้งซ่อมอุปกรณ์
            </a>
            <a href="#" class="button_icon">
                #
            </a>
    </div>
    <div class="container">

        <h2 class="mt-4">เพิ่มข้อมูล
            <br>
            <hr>

            <a href="http://localhost/project_ce/admin_pao/add_it_pao/add_it_pao.php" class="button_icon">
                <i class="bi bi-patch-plus"></i>
                เพิ่มุอปกรณ์ IT
            </a>

    </div>


</body>
<!-- JavaScript Bundle with Popper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>