<?php
require_once '../../db_pao.php';

if (isset($_POST['id_device'])) {
    $id_device = $_POST['id_device'];

    try {
        $stmt = $pdo->prepare("SELECT id_model, model_name FROM models WHERE id_device = :id_device");
        $stmt->bindParam(':id_device', $id_device, PDO::PARAM_INT);
        $stmt->execute();
        $models = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($models);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>
