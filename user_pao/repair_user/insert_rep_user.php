<?php
session_start();
require_once '../../db_pao.php';

if (isset($_POST['submit'])) {
    $id_device = intval($_POST['id_device']);
    $id_khong = intval($_POST['id_khong']);
    $id_user = intval($_POST['id_user']);  // แก้ไขจาก 'id_user' เป็น 'id_user' เพื่อรับค่าจาก hidden input
    $date_time = trim($_POST['date_time']);
    $repair_status = trim($_POST['repair_status']);

    try {
        // ตรวจสอบข้อมูลซ้ำ
        $rep_check = $pdo->prepare("SELECT 1 FROM repairs WHERE id_device = :id_device AND id_khong = :id_khong AND id_user = :id_user LIMIT 1");
        $rep_check->bindParam(":id_device", $id_device, PDO::PARAM_INT);
        $rep_check->bindParam(":id_khong", $id_khong, PDO::PARAM_INT);
        $rep_check->bindParam(":id_user", $id_user, PDO::PARAM_INT);
        $rep_check->execute();

        if ($rep_check->fetch()) {
            $_SESSION['error'] = "มีการแจ้งซ่อมนี้อยู่แล้ว!";
            header("Location: /Project_CE/user_pao/user_home.php");
            exit();
        }

        // เพิ่มข้อมูลลงในฐานข้อมูล
        $sql = $pdo->prepare("INSERT INTO repairs (id_device, id_khong, id_user, date_time, name_inform, repair_status) 
                              VALUES (:id_device, :id_khong, :id_user, :date_time, :name_inform, :repair_status)");
        $sql->bindParam(":id_device", $id_device, PDO::PARAM_INT);
        $sql->bindParam(":id_khong", $id_khong, PDO::PARAM_INT);
        $sql->bindParam(":id_user", $id_user, PDO::PARAM_INT);
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
