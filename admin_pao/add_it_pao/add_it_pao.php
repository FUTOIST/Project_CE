<?php

session_start();
require_once '../../db_pao.php';
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: \project_ce\index.php');
    exit();
}

// ลบอุปกรณ์
if (isset($_GET['delete_device'])) {
    $delete_id = $_GET['delete_device'];
    $deletestmt = $pdo->prepare("DELETE FROM devices WHERE id_device = :delete_id");
    $deletestmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
    $result = $deletestmt->execute();

    if ($result) {
        echo "<script>alert('ลบข้อมูลสำเร็จ');</script>";
        $_SESSION['success'] = "ลบข้อมูลสำเร็จ";
        header("refresh:1; url=add_it_pao.php");
        exit();
    } else {
        $_SESSION['error'] = "ไม่สามารถลบข้อมูลได้";
        header("location: add_it_pao.php");
        exit();
    }
}

// ลบรุ่นอุปกรณ์
if (isset($_GET['delete_model'])) {
    $delete_id = $_GET['delete_model'];

    try {
        $deleteStmt = $pdo->prepare("DELETE FROM models WHERE id_model = :delete_id");
        $deleteStmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
        $result = $deleteStmt->execute();

        if ($result) {
            $_SESSION['success'] = "ลบข้อมูลสำเร็จ";
        } else {
            $_SESSION['error'] = "ไม่สามารถลบข้อมูลได้";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    header("location: add_it_pao.php"); // รีเฟรชหน้าเพื่ออัปเดตข้อมูล
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-การเพิ่มอุปกรณ์</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../../css/button_pao.css">


</head>

<body>

    <ol class="breadcrumb" style="margin-left: 12%; padding-top: 20px;">
        <li class="breadcrumb-item"><a href="../admin_home.php" class="text-success">หน้าหลัก</a></li>
        <li class="breadcrumb-item active" aria-current="page">เพิ่มข้อมูล IT</li>
    </ol>
    <!--------------- แสดง Pop up เพื่อ Insert ข้อมูลของ devices -------------->
    <div class="modal fade" id="itModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-weight: bold;">เพิ่มอุปกรณ์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="insert_it_pao.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="device_name" class="col-form-label" style="font-weight: bold;">ชื่ออุปกรณ์:</label>
                            <input type="text" required class="form-control" placeholder="กรอกชื่ออุปกรณ์" name="device_name">
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h2>เพิ่มอุปกรณ์</h2>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#itModal" data-bs-whatever="@mdo"><i class="bi bi-patch-plus-fill"></i> เพิ่มอุปกรณ์</button>
            </div>
        </div>
        <hr>

        <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-light alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>


        <!-- ตารางแสดงข้อมูลของ devices -->
        <table class="table">
            <thead class="text-center align-middle">
                <tr>
                    <th scope="col">รหัสอุปกรณ์</th>
                    <th scope="col">ชื่ออุปกรณ์</th>
                    <th scope="col">แก้ไข</th>
                </tr>
            </thead>
            <tbody class="text-center align-middle">
                <?php

                $devices = []; // กำหนดค่าเริ่มต้นให้ตัวแปรเพื่อลด Warning

                try {
                    $stmt = $pdo->query("SELECT * FROM devices");
                    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $_SESSION['error'] = "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage();
                }

                if (empty($devices)) {
                    echo "<tr><td colspan='3' class='text-center'>ไม่มีข้อมูล</td></tr>";
                } else {
                    foreach ($devices as $device) {
                ?>
                        <tr>
                            <td class="align-middle"><?php echo $device['id_device']; ?></td>
                            <td class="align-middle"><?php echo $device['device_name']; ?></td>
                            <td class="align-middle">
                                <a href="edit_it_pao.php?id_device=<?php echo $device['id_device']; ?>" class="btn btn-success">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a onclick="return confirm('Are you sure you want to delete?');"
                                    href="?delete_device=<?php echo $device['id_device']; ?>" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Delete
                                </a>
                            </td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>


    <!--------------- แสดง Pop up เพื่อ Insert ข้อมูลของ models -------------->
    <div class="modal fade" id="modelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-weight: bold;">เพิ่มรุ่นอุปกรณ์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="insert_model_it_pao.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="id_device" class="col-form-label" style="font-weight: bold;">ชื่ออุปกรณ์:</label>
                            <select required class="form-control" name="id_device">
                                <option value="">เลือกอุปกรณ์ (ชื่ออุปกรณ์)</option>
                                <?php
                                $stmt = $pdo->query("SELECT id_device, device_name FROM devices");
                                $stmt->execute();
                                $devices = $stmt->fetchAll();

                                foreach ($devices as $device) {
                                    echo "<option value='{$device['id_device']}'>{$device['device_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="model_name" class="col-form-label" style="font-weight: bold;">ชื่อรุ่นอุปกรณ์:</label>
                            <input type="text" required class="form-control" placeholder="กรอกชื่ออุปกรณ์" name="model_name">
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!---------------------- ส่วนของตาราง models ---------------------->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h2>ตารางข้อมูลรุ่นอุปกรณ์</h2>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modelModal" data-bs-whatever="@mdo"><i class="bi bi-patch-plus-fill"></i> เพิ่มรุ่นอุปกรณ์</button>
            </div>
        </div>
        <hr>

        <!-- ตารางแสดงข้อมูลของ models -->
        <table class="table">
            <thead class="text-center align-middle">
                <tr>
                    <th scope="col">รหัสรุ่นอุปกรณ์</th>
                    <th scope="col">ชื่อรุ่นอุปกรณ์</th>
                    <th scope="col">รหัสอุปกรณ์</th>
                    <th scope="col">แก้ไข</th>
                </tr>
            </thead>
            <tbody class="text-center align-middle">
                <?php
                // ดึงข้อมูลจากฐานข้อมูล
                $models = [];
                try {
                    $stmt = $pdo->query("SELECT * FROM models");
                    $models = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $_SESSION['error'] = "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage();
                }

                if (empty($models)) {
                    echo "<tr><td colspan='4' class='text-center'>ไม่มีข้อมูลในฐานข้อมูล</td></tr>";
                } else {
                    foreach ($models as $model) {
                ?>
                        <tr>
                            <td class="align-middle"><?php echo htmlspecialchars($model['id_model']); ?></td>
                            <td class="align-middle"><?php echo htmlspecialchars($model['model_name']); ?></td>
                            <td class="align-middle"><?php echo htmlspecialchars($model['id_device']); ?></td>
                            <td class="align-middle">
                                <a href="edit_model_it_pao.php?id_model=<?php echo $model['id_model']; ?>" class="btn btn-success">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a onclick="return confirm('Are you sure you want to delete?');"
                                    href="?delete_model=<?php echo $model['id_model']; ?>" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Delete
                                </a>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>