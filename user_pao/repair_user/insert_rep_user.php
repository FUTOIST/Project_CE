<?php

session_start();
require_once '../../db_pao.php';

//---------------------------- devices ---------------------------//
if (isset($_POST['submit'])) {
    $device_name = trim($_POST['device_name']);

    // ตรวจสอบว่ามี device_name ซ้ำหรือไม่
    $add_check = $pdo->prepare("SELECT COUNT(*) FROM devices WHERE device_name = :device_name");
    $add_check->bindParam(":device_name", $device_name, PDO::PARAM_STR);
    $add_check->execute();
    $add_exists = $add_check->fetchColumn();

    if ($add_exists > 0) {
        $_SESSION['error'] = "มีชื่อุปกรณ์นี้อยู่แล้ว!";
        header("location: add_it_pao.php");
        exit();
    }

    // เพิ่มข้อมูลลงในฐานข้อมูล devices
    try {
        $sql = $pdo->prepare("INSERT INTO models (device_name) VALUES (:device_name)");
        $sql->bindParam(":device_name", $device_name, PDO::PARAM_STR);
        $result = $sql->execute();

        if ($result) {
            $_SESSION['success'] = "เพิ่มข้อมูลสำเร็จ";
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในฐานข้อมูล: " . $e->getMessage();
    }

    header("location: add_it_pao.php");
    exit();
}


?>
 