<?php
session_start();
require_once '../../db_pao.php';
if (!isset($_SESSION['user_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: \project_ce\index.php');
    exit();
}

if (isset($_POST['update'])) {
    $id_repair = $_POST['id_repair'];
    $id_device = $_POST['id_device'];
    $id_model = $_POST['id_model'];
    $id_khong = $_POST['id_khong'];

    if (!empty($id_repair)) {
        try {
            // แก้ไขข้อมูลในฐานข้อมูล
            $sql = $pdo->prepare("UPDATE repairs SET id_device = :id_device, id_model = :id_model, id_khong = :id_khong WHERE id_repair = :id_repair");
            $sql->bindParam(":id_device", $id_device, PDO::PARAM_INT);
            $sql->bindParam(":id_model", $id_model, PDO::PARAM_INT);
            $sql->bindParam(":id_khong", $id_khong, PDO::PARAM_INT);
            $sql->bindParam(":id_repair", $id_repair, PDO::PARAM_INT);
            $sql->execute();  // Execute การอัปเดต

            $_SESSION['success'] = "ได้มีการอัพเดตข้อมูลแล้ว";
            header("location: ../user_home.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "ข้อผิดพลาด: " . $e->getMessage();
            header("location: ../user_home.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "ไม่พบรหัสของอุปกรณ์";
        header("location: ../user_home.php");
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

    <div class="container">
        <div class="container-fluid mt-5">
            <h1 class="mb-4">แก้ไขการแจ้งซ่อม</h1>
            <div class="card">
                <div class="card-body">
                    <form action="edit_rep_user.php" method="post" enctype="multipart/form-data"> <!-- แก้ไขที่ action -->
                        <?php
                        if (isset($_GET['id_repair'])) {
                            $id_repair = $_GET['id_repair'];  // รับค่า id_repair จาก URL

                            try {
                                $stmt = $pdo->prepare("SELECT * FROM repairs WHERE id_repair = :id_repair");
                                $stmt->bindParam(':id_repair', $id_repair, PDO::PARAM_INT);
                                $stmt->execute();
                                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit();
                            }
                        }
                        ?>
                        <div class="row md-3">
                            <div class="col-md-6">
                                <label for="id_repair" class="form-label"></label>
                                <input type="hidden" value="<?php echo htmlspecialchars($data['id_repair']); ?>" required class="form-control" placeholder="กรอกชื่ออุปกรณ์" name="id_repair">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label for="id_device" class="col-form-label" style="font-weight: bold;">ชื่ออุปกรณ์:</label>
                                <select required class="form-control" name="id_device" id="id_device">
                                    <option value="">เลือกอุปกรณ์ (ชื่ออุปกรณ์)</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT id_device, device_name FROM devices");
                                    $devices = $stmt->fetchAll();
                                    foreach ($devices as $device) {
                                        echo "<option value='{$device['id_device']}'>{$device['device_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label for="id_model" class="col-form-label" style="font-weight: bold;">รุ่นอุปกรณ์:</label>
                                <select required class="form-control" name="id_model" id="id_model">
                                    <option value="">เลือกอุปกรณ์ (รุ่นอุปกรณ์)</option>
                                    <?php
                                    if (isset($data['id_device'])) {
                                        $stmt = $pdo->prepare("SELECT id_model, model_name FROM models WHERE id_device = :id_device");
                                        $stmt->bindParam(':id_device', $data['id_device'], PDO::PARAM_INT);
                                        $stmt->execute();
                                        $models = $stmt->fetchAll();

                                        foreach ($models as $model) {
                                            $selected = ($model['id_model'] == $data['id_model']) ? "selected" : "";
                                            echo "<option value='{$model['id_model']}' $selected>{$model['model_name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="id_khong" class="col-form-label" style="font-weight: bold;">กองที่สังกัด :</label>
                                <select class="form-control" name="id_khong">
                                    <option value="">เลือกกอง (ที่ประจำการอยู่)</option>
                                    <?php
                                    // ตรวจสอบข้อมูลจากฐานข้อมูลเพื่อแสดงกอง
                                    $stmt = $pdo->query("SELECT id_khong, khong_name FROM khongs");
                                    $stmt->execute();
                                    $khongs = $stmt->fetchAll();

                                    // ตรวจสอบว่าค่า id_khong ที่เลือกมีค่าในฐานข้อมูลหรือไม่
                                    foreach ($khongs as $khong) {
                                        // ถ้า id_khong ของการซ่อมตรงกับค่า id_khong ในฐานข้อมูล ให้เลือกเป็น selected
                                        $selected = (isset($repair['id_khong']) && $repair['id_khong'] == $khong['id_khong']) ? 'selected' : '';
                                        echo "<option value='{$khong['id_khong']}' $selected>{$khong['khong_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mt-4">
                                <a href="../user_home.php" class="btn btn-secondary">Go Back</a>
                                <button type="submit" name="update" class="btn btn-primary">Update</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#id_device").change(function() {
                var id_device = $(this).val();
                var selectedModel = "<?php echo $data['id_model'] ?? ''; ?>"; // ดึง id_model ปัจจุบัน

                $("#id_model").html('<option value="">กำลังโหลด...</option>');

                if (id_device !== "") {
                    $.ajax({
                        url: "fetch2_models.php",
                        type: "POST",
                        data: {
                            id_device: id_device
                        },
                        dataType: "json",
                        success: function(data) {
                            var options = '<option value="">เลือกอุปกรณ์ (รุ่นอุปกรณ์)</option>';
                            $.each(data, function(index, model) {
                                var selected = (model.id_model == selectedModel) ? 'selected' : '';
                                options += '<option value="' + model.id_model + '" ' + selected + '>' + model.model_name + '</option>';
                            });
                            $("#id_model").html(options);
                        },
                        error: function() {
                            $("#id_model").html('<option value="">เกิดข้อผิดพลาด</option>');
                        }
                    });
                } else {
                    $("#id_model").html('<option value="">เลือกอุปกรณ์ (รุ่นอุปกรณ์)</option>');
                }
            });

            // Trigger change เพื่อโหลดค่า id_model เมื่อเปิดหน้า
            $("#id_device").trigger("change");
        });
    </script>

</body>

</html>