<?php
include 'sys/ini.php';
$pdo = pdo_init();
header('Content-Type: application/json');

// Fetch the latest mod_id from the "models" table
$query = $pdo->prepare("SELECT MAX(model_file) AS latest_model,MAX(label_file) AS latest_label FROM models;");
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

if ($result !== false) {
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    echo json_encode(['error' => 'No models found'], JSON_PRETTY_PRINT);
}

exit();
?>
