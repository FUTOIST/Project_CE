<?php
session_start();
require_once 'db_pao.php'; // เชื่อมต่อฐานข้อมูล

if (isset($_POST['signin'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // กรองอีเมล
    $password = $_POST['password']; // รับค่ารหัสผ่าน

    if (empty($email)) {
        $_SESSION['error'] = 'กรุณากรอกอีเมล';
        header("location: index.php");
        exit;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
        header("location: index.php");
        exit;
    } else if (empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
        header("location: index.php");
        exit;
    } else if (strlen($password) > 20 || strlen($password) < 5) {
        $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร';
        header("location: index.php");
        exit;
    } else {
        try {
            // ตรวจสอบข้อมูลผู้ใช้จากฐานข้อมูล
            $check_data = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $check_data->bindParam(":email", $email, PDO::PARAM_STR);
            $check_data->execute();
            $row = $check_data->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // ตรวจสอบรหัสผ่าน
                if ($password === $row['password']) {
                    if ($row['urole'] == 'admin') {
                        $_SESSION['admin_login'] = $row['id_user'];
                        header("location: admin_pao/admin_home.php");
                        exit;
                    } else {
                        $_SESSION['user_login'] = $row['id_user'];
                        header("location: user_pao/user_home.php");
                        exit;
                    }
                } else {
                    $_SESSION['error'] = 'รหัสผ่านผิด';
                    header("location: index.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = "ไม่มีข้อมูลในระบบ";
                header("location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            // บันทึกข้อผิดพลาดไว้ใน log
            error_log($e->getMessage());
            $_SESSION['error'] = "เกิดข้อผิดพลาดบางประการ กรุณาลองใหม่อีกครั้ง";
            header("location: index.php");
            exit;
        }
    }
}
