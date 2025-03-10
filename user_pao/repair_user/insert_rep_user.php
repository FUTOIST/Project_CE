<?php
session_start();
require_once '../../db_pao.php';

if (isset($_POST['submit'])) {
    $id_device = intval($_POST['id_device']);
    $id_khong = intval($_POST['id_khong']);
    $id_user = intval($_POST['id_user']);
    $id_model = intval($_POST['id_model']);  // เพิ่มการรับค่า id_model
    $date_time = trim($_POST['date_time']);

    // แปลงวันที่เป็นรูปแบบที่ฐานข้อมูลรองรับ
    $date_time = DateTime::createFromFormat('d/m/Y', $date_time);
    if ($date_time) {
        $date_time = $date_time->format('Y-m-d');  // แปลงเป็นรูปแบบที่ฐานข้อมูลต้องการ
    } else {
        $_SESSION['error'] = "รูปแบบวันที่ไม่ถูกต้อง";
        header("Location: /Project_CE/user_pao/user_home.php");
        exit();
    }

    // กำหนดสถานะเริ่มต้นเป็น "รอดำเนินการซ่อม"
    $repair_status = "รอดำเนินการซ่อม";

    try {
        // ตรวจสอบและบันทึกข้อมูล
        $sql = $pdo->prepare("INSERT INTO repairs (id_device, id_khong, id_user, id_model, date_time, repair_status) 
VALUES (:id_device, :id_khong, :id_user, :id_model, :date_time, :repair_status)");
        $sql->bindParam(":id_device", $id_device, PDO::PARAM_INT);
        $sql->bindParam(":id_khong", $id_khong, PDO::PARAM_INT);
        $sql->bindParam(":id_user", $id_user, PDO::PARAM_INT);
        $sql->bindParam(":id_model", $id_model, PDO::PARAM_INT);
        $sql->bindParam(":date_time", $date_time, PDO::PARAM_STR);
        $sql->bindParam(":repair_status", $repair_status, PDO::PARAM_STR);

        if ($sql->execute()) {
            $_SESSION['success'] = "เพิ่มข้อมูลสำเร็จ";
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในฐานข้อมูล: " . $e->getMessage();
    }

    header("Location: /Project_CE/user_pao/user_home.php");
    exit();
}

?>