<?php
session_start();
require_once '../../db_pao.php';
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: \project_ce\index.php');
    exit();
}

if (isset($_POST['update'])) {
    $device_name = $_POST['device_name'];
    $id_device = $_POST['id_device'];  // รับค่า id_device จากฟอร์ม

    if (!empty($id_device)) {
        try {
            // แก้ไขข้อมูลในฐานข้อมูล
            $sql = $pdo->prepare("UPDATE devices SET device_name = :device_name WHERE id_device = :id_device");
            $sql->bindParam(":device_name", $device_name, PDO::PARAM_STR);
            $sql->bindParam(":id_device", $id_device, PDO::PARAM_INT);
            $sql->execute();  // Execute การอัปเดต

            $_SESSION['success'] = "ได้มีการอัพเดตข้อมูลแล้ว";
            header("location: add_it_pao.php");  // กลับไปหน้าเพิ่มข้อมูล IT
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "ข้อผิดพลาด: " . $e->getMessage();
            header("location: add_it_pao.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "ไม่พบรหัสของอุปกรณ์";
        header("location: add_it_pao.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-แก้ไขข้อมูลอุปกรณ์ IT</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../css/button_pao.css">
</head>

<body>

    <ol class="breadcrumb" style="margin-left: 12%; padding-top: 20px;">
        <li class="breadcrumb-item"><a href="../admin_home.php" class="text-success">หน้าหลัก</a></li>
        <li class="breadcrumb-item"><a href="add_it_pao.php" class="text-success">เพิ่มข้อมูล IT</a></li>
        <li class="breadcrumb-item active" aria-current="page">แก้ไขชื่ออุปกรณ์ IT</li>
    </ol>

    <div class="container">
        <div class="container-fluid mt-5">
            <h1 class="mb-4">แก้ไขชื่ออุปกรณ์</h1>
            <div class="card">
                <div class="card-body">
                    <form action="edit_it_pao.php" method="post" enctype="multipart/form-data"> <!-- แก้ไขที่ action -->
                        <?php
                        if (isset($_GET['id_device'])) {
                            $id_device = $_GET['id_device'];  // รับค่า id_device จาก URL

                            try {
                                $stmt = $pdo->prepare("SELECT * FROM devices WHERE id_device = :id_device");
                                $stmt->bindParam(':id_device', $id_device, PDO::PARAM_INT);
                                $stmt->execute();
                                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit();
                            }
                        }
                        ?>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="id_device" class="form-label">รหัสอุปกรณ์:</label>
                                <input type="text" value="<?php echo htmlspecialchars($data['id_device']); ?>" required class="form-control" name="id_device" readonly>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label for="device_name" class="form-label">ชื่ออุปกรณ์:</label>
                                <input type="text" value="<?php echo htmlspecialchars($data['device_name']); ?>" required class="form-control" placeholder="กรอกชื่ออุปกรณ์" name="device_name">
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="add_it_pao.php" class="btn btn-secondary">Go Back</a>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>