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
        "รอดำเนินการซ่อม" => "warning text-dark",
        "กำลังดำเนินการซ่อม" => "primary",
        "การซ่อมเสร็จสิ้น" => "success",
        "ยกเลิกการซ่อม" => "danger"
    ];

    $color = $statusColors[$status] ?? "secondary";
    return "<span class='badge bg-$color px-3 py-2'>$status</span>";
}

// ดึงข้อมูลจากฐานข้อมูล
$repairLists = [
    "รอดำเนินการซ่อม" => [],
    "กำลังดำเนินการซ่อม" => [],
    "การซ่อมเสร็จสิ้น" => [],
    "ยกเลิกการซ่อม" => []
];

try {
    $stmt = $pdo->query("SELECT 
        r.*, d.device_name, m.model_name, u.firstname, u.lastname, k.khong_name 
        FROM repairs r 
        LEFT JOIN devices d ON r.id_device = d.id_device 
        LEFT JOIN models m ON r.id_model = m.id_model 
        LEFT JOIN users u ON r.id_user = u.id_user 
        LEFT JOIN khongs k ON r.id_khong = k.id_khong");

    while ($repair = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($repairLists[$repair['repair_status']])) {
            $repairLists[$repair['repair_status']][] = $repair;
        }
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-การแจ้งซ่อมอุปกรณ์</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../../css/button_pao.css">

</head>

<body>
    <div class="container mt-5 text-center">
        <ul class="nav nav-tabs justify-content-center border-bottom border-2 mb-4" id="repairTabs" role="tablist">
            <?php foreach ($repairLists as $status => $list) { ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark bg-<?= getStatusBadge($status) ?> border border-dark mx-2"
                        id="<?= $status ?>-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#<?= $status ?>"
                        type="button" role="tab"
                        aria-controls="<?= $status ?>"
                        aria-selected="false">
                        <?= $status ?>
                    </button>
                </li>
            <?php } ?>
        </ul>

        <div class="tab-content mt-4 border border-2 p-4 rounded" id="repairTabsContent">
            <?php foreach ($repairLists as $status => $repairs) { ?>
                <div class="tab-pane fade show <?= $status === "รอดำเนินการซ่อม" ? 'active' : '' ?>"
                    id="<?= $status ?>" role="tabpanel"
                    aria-labelledby="<?= $status ?>-tab">
                    <div class="row">
                        <?php if (empty($repairs)) { ?>
                            <div class='col-12 text-center'>
                                <p class='text-muted'>ไม่มีข้อมูลในฐานข้อมูล</p>
                            </div>
                        <?php } else { ?>
                            <?php foreach ($repairs as $repair) { ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card model-card shadow-lg">
                                        <div class="card-header bg-dark-green text-white">
                                            <h5 class="card-title mb-0"><?= htmlspecialchars($repair['device_name']); ?></h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text text-start">
                                                <strong>ชื่อรุ่นอุปกรณ์ :</strong> <?= htmlspecialchars($repair['model_name']); ?><br>
                                                <strong>วันที่ :</strong> <?= date('d/m/Y', strtotime($repair['date_time'])); ?><br>
                                                <strong>ชื่อผู้แจ้ง :</strong> <?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']); ?><br>
                                                <strong>กอง :</strong> <?= htmlspecialchars($repair['khong_name']); ?><br>
                                                <strong class="me-2">สถานะ :</strong> <?= getStatusBadge($repair['repair_status']); ?><br>
                                            </p>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="edit_rep_pao.php?id_repair=<?= $repair['id_repair']; ?>"
                                                    class="btn btn-lg btn-edit">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>