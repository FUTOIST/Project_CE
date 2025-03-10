<?php

session_start();
require_once '../../db_pao.php';
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: \project_ce\index.php');
    exit();
}

function getStatusBadge($status)
{
    $statusColors = [
        "รอดำเนินการซ่อม" => "warning text-dark", // เหลือง
        "กำลังดำเนินการซ่อม" => "primary", // น้ำเงิน
        "การซ่อมเสร็จสิ้น" => "success", // เขียว
        "ยกเลิกการซ่อม" => "danger" // แดง
    ];

    $color = $statusColors[$status] ?? "secondary"; // สีเทาสำหรับสถานะที่ไม่รู้จัก
    return "<span class='badge bg-$color px-3 py-2'>$status</span>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-การแจ้งซ่อมอุปกรณ์</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../../css/button_pao.css">
</head>

<body>

    <ol class="breadcrumb" style="margin-left: 12%; padding-top: 20px;">
        <li class="breadcrumb-item"><a href="../admin_home.php">หน้าหลัก</a></li>
        <li class="breadcrumb-item active" aria-current="page">การแจ้งซ่อมอุปกรณ์</li>
    </ol>

    <div class="container mt-5 text-center">
        <ul class="nav nav-tabs justify-content-center border-bottom border-2 mb-4" id="repairTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-dark bg-warning border border-dark mx-2" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">รอดำเนินการซ่อม</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-white bg-primary border border-dark mx-2" id="inprogress-tab" data-bs-toggle="tab" data-bs-target="#inprogress" type="button" role="tab" aria-controls="inprogress" aria-selected="false">กำลังดำเนินการซ่อม</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-white bg-success border border-dark mx-2" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">การซ่อมเสร็จสิ้น</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-white bg-danger border border-dark mx-2" id="canceled-tab" data-bs-toggle="tab" data-bs-target="#canceled" type="button" role="tab" aria-controls="canceled" aria-selected="false">ยกเลิกการซ่อม</button>
            </li>
        </ul>

        <div class="tab-content mt-4 border border-2 p-4 rounded" id="repairTabsContent">
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <p>รายการซ่อมที่รอดำเนินการ...</p>
            </div>
            <div class="tab-pane fade" id="inprogress" role="tabpanel" aria-labelledby="inprogress-tab">
                <p>รายการที่กำลังซ่อมอยู่...</p>
            </div>
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <p>รายการซ่อมที่เสร็จสิ้นแล้ว...</p>
            </div>
            <div class="tab-pane fade" id="canceled" role="tabpanel" aria-labelledby="canceled-tab">
                <p>รายการที่ถูกยกเลิก...</p>
            </div>
        </div>

        <!-- ช่องแสดงข้อมูลของ repair -->
        <div class="row">
            <?php
            // ดึงข้อมูลจากฐานข้อมูล
            $repairs = [];
            try {
                $stmt = $pdo->query("SELECT r.*, d.device_name, m.model_name FROM repairs r JOIN devices d ON r.id_device = d.id_device JOIN models m ON r.id_model = m.id_model");
                $repairs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $_SESSION['error'] = "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage();
            }

            if (empty($repairs)) {
                echo "<div class='col-12 text-center'><p class='text-muted'>ไม่มีข้อมูลในฐานข้อมูล</p></div>";
            } else {
                foreach ($repairs as $repair) {
            ?>
                    <div class="col-md-4 mb-4">
                        <div class="card model-card shadow-lg">
                            <div class="card-header bg-dark-green text-white">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($repair['device_name']); ?></h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text text-start">
                                    <strong>ชื่อรุ่นอุปกรณ์ :</strong> <?php echo htmlspecialchars($repair['model_name']); ?><br>
                                    <strong>วันที่ :</strong> <?php echo date('d/m/Y', strtotime($repair['date_time'])); ?>
                                    <br>
                                    <strong class="me-2">สถานะ :</strong>
                                    <?= getStatusBadge($repair['repair_status']); ?><br>
                                </p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="repair_user/edit_rep_user.php?id_repair=<?php echo $repair['id_repair']; ?>"
                                        class="btn btn-sm btn-edit">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a onclick="return confirm('Are you sure you want to delete?');"
                                        href="?delete_repair=<?php echo $repair['id_repair']; ?>"
                                        class="btn btn-sm btn-delete">
                                        <i class="bi bi-x-circle"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>



    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>