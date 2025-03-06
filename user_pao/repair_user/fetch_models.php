<?php
require_once '../../db_pao.php';

if (isset($_POST['id_device'])) {
    $id_device = $_POST['id_device'];
    $stmt = $pdo->prepare("SELECT id_model, model_name FROM models WHERE id_device = :id_device");
    $stmt->execute(['id_device' => $id_device]);
    $models = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($models);
}
?>
