<?php

session_start();
require_once '../db_pao.php';
if (!isset($_SESSION['user_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: \project_ce\index.php');
    exit();
}

// ตั้งค่าโซนเวลาเป็นเวลาประเทศไทย (UTC+7)
date_default_timezone_set('Asia/Bangkok');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งซ่อมอุปกรณ์-องค์การบริหารส่วนจังหวัดปัตตานี</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../css/navbar_user.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <img src="../img_usage/logo.png" alt="โลโก้">
        </div>
        <ul class="menu">
            <li><a href="http://localhost/project_ce/user_pao/user_home.php">แจ้งซ่อมอุปกรณ์</a></li>
            <hr>
            <li><a href="http://localhost/project_ce/user_pao/check_user/user_check.php">ตรวจสอบสถานะการแจ้งซ่อม</a></li>
            <hr>
        </ul>
        <ul class="logout">
            <li>
                <a href="../logout_pao.php" onclick="return confirm('ต้องการออกจากระบบ ใช่หรือไม่?');">
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
        <h1>แจ้งซ่อมอุปกรณ์</h1>
        <p1>ท่านสามารถแจ้งซ่อมอุปกรณ์คอมพิวเตอร์ ปริ้นเตอร์ และครุภัณฑ์ต่างๆ</p1>
        <hr>

        <!--------------- แสดง Pop up เพื่อ Insert ข้อมูลของ repairs -------------->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <<div class="modal-dialog modal-lg modal-dialog-centered"> <!-- เพิ่ม modal-dialog-centered -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel" style="font-weight: bold;">เพิ่มรุ่นอุปกรณ์</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="repair_user/insert_rep_user.php" method="post" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="id_user" class="col-form-label" style="font-weight: bold;">ชื่อผู้แจ้ง :</label>
                                    <input type="text" required class="form-control" name="id_user" value="<?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?>" readonly>
                                    <input type="hidden" name="id_user" value="<?= htmlspecialchars($row['id_user']) ?>">
                                </div>

                                <div class="col-md-6">
                                    <label for="id_khong" class="col-form-label" style="font-weight: bold;">กองที่สังกัด :</label>
                                    <select required class="form-control" name="id_khong">
                                        <option value="">เลือกกอง (ที่ประจำการอยู่)</option>
                                        <?php
                                        $stmt = $pdo->query("SELECT id_khong, khong_name FROM khongs");
                                        $stmt->execute();
                                        $khongs = $stmt->fetchAll();

                                        foreach ($khongs as $khong) {
                                            echo "<option value='{$khong['id_khong']}'>{$khong['khong_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="date_time" class="col-form-label" style="font-weight: bold;">วันและเวลา :</label>
                                    <input type="text" required class="form-control" name="date_time" value="<?= date('d-m-Y H:i:s') ?>" readonly>
                                </div>
                            </div>

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
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-success btn-lg px-4 py-2" data-bs-toggle="modal" data-bs-target="#userModal" data-bs-whatever="@mdo">
                    <i class="bi bi-device-ssd"></i> แจ้งซ่อมอุปกรณ์
                </button>
            </div>
        </div>
        <br>

        <!-- ช่องแสดงข้อมูลของ repair -->
        <div class="row">
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
                echo "<div class='col-12 text-center'><p class='text-muted'>ไม่มีข้อมูลในฐานข้อมูล</p></div>";
            } else {
                foreach ($models as $model) {
            ?>
                    <div class="col-md-4 mb-4">
                        <div class="card model-card shadow-lg">
                            <div class="card-header bg-dark-green text-white">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($model['model_name']); ?></h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text text-start">
                                    <strong>รหัสรุ่นอุปกรณ์:</strong> <?php echo htmlspecialchars($model['id_model']); ?><br>
                                    <strong>รหัสอุปกรณ์:</strong> <?php echo htmlspecialchars($model['id_device']); ?>
                                </p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit_model_it_pao.php?id_model=<?php echo $model['id_model']; ?>" class="btn btn-sm btn-edit">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a onclick="return confirm('Are you sure you want to delete?');"
                                        href="?delete_model=<?php echo $model['id_model']; ?>"
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
    </div>
</body>
<!-- JavaScript Bundle with Popper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>