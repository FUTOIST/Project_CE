<?php
session_start();
require_once '../../db_pao.php';

//---------------------------- models ---------------------------//
if (isset($_POST['submit'])) {
    $model_name = trim($_POST['model_name']);
    $id_device = trim($_POST['id_device']);

    // ตรวจสอบว่ามี model_name ซ้ำกับ id_device หรือไม่
    $mod_check = $pdo->prepare("SELECT COUNT(*) FROM models WHERE model_name = :model_name AND id_device = :id_device");
    $mod_check->bindParam(":model_name", $model_name, PDO::PARAM_STR);
    $mod_check->bindParam(":id_device", $id_device, PDO::PARAM_INT);
    $mod_check->execute();
    $mod_exists = $mod_check->fetchColumn();

    if ($mod_exists > 0) {
        $_SESSION['error'] = "มีชื่อรุ่นนี้สำหรับอุปกรณ์นี้อยู่แล้ว!";
        header("location: add_it_pao.php");
        exit();
    }

    // เพิ่มข้อมูลลงในฐานข้อมูล models
    try {
        $sql = $pdo->prepare("INSERT INTO models (model_name, id_device) VALUES (:model_name, :id_device)");
        $sql->bindParam(":model_name", $model_name, PDO::PARAM_STR);
        $sql->bindParam(":id_device", $id_device, PDO::PARAM_INT);
        $result = $sql->execute();

        if ($result) {
            $_SESSION['success'] = "เพิ่มข้อมูลรุ่นอุปกรณ์สำเร็จ";
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูลรุ่นอุปกรณ์";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในฐานข้อมูล: " . $e->getMessage();
    }

    // รีเฟรชหน้าเพื่อแสดงข้อมูลที่เพิ่มใหม่
    header("location: add_it_pao.php");
    exit();
}
