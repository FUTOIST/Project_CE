<?php

session_start();
require_once '../../db_pao.php';
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: \project_ce\index.php');
    exit();
}

if (!isset($_GET['id_repair'])) {
    $_SESSION['error'] = 'ไม่พบข้อมูลที่ต้องการแก้ไข!';
    header('location: repair_pao.php');
    exit();
}

$id_repair = $_GET['id_repair'];

try {
    $stmt = $pdo->prepare("SELECT r.*, d.device_name, m.model_name, u.firstname, u.lastname, k.khong_name 
        FROM repairs r 
        LEFT JOIN devices d ON r.id_device = d.id_device 
        LEFT JOIN models m ON r.id_model = m.id_model 
        LEFT JOIN users u ON r.id_user = u.id_user 
        LEFT JOIN khongs k ON r.id_khong = k.id_khong WHERE r.id_repair = ?");
    $stmt->execute([$id_repair]);
    $repair = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$repair) {
        $_SESSION['error'] = 'ไม่พบข้อมูลการซ่อมที่เลือก';
        header('location: repair_pao.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    header('location: repair_pao.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['repair_status'];

    try {
        $updateStmt = $pdo->prepare("UPDATE repairs SET repair_status = ? WHERE id_repair = ?");
        $updateStmt->execute([$new_status, $id_repair]);
        $_SESSION['success'] = 'อัปเดตสถานะการซ่อมเรียบร้อยแล้ว';
        header('location: repair_pao.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

$statuses = [
    "รอดำเนินการซ่อม",
    "กำลังดำเนินการซ่อม",
    "การซ่อมเสร็จสิ้น",
    "ยกเลิกการซ่อม"
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลการซ่อม</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../../css/button_pao.css">

</head>

<body>
    <div class="container">
        <div class="container-fluid mt-5">
            <h1 class="mb-4">แก้ไขสถานะการซ่อม</h1>
            <div class="card">
                <div class="card-body">
                    <form method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: bold;">ชื่ออุปกรณ์ :</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($repair['device_name']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: bold;">ชื่อรุ่น :</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($repair['model_name']) ?>" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: bold;">ชื่อผู้แจ้ง :</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']); ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: bold;">กอง :</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($repair['khong_name']); ?>" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight: bold;">สถานะ:</label>
                                <select name="repair_status" class="form-select">
                                    <?php foreach ($statuses as $status) : ?>
                                        <option value="<?= $status ?>" <?= $repair['repair_status'] === $status ? 'selected' : '' ?>>
                                            <?= $status ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="repair_pao.php" class="btn btn-secondary">ย้อนกลับ</a>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>