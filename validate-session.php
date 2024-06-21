<?php
include 'sys/ini.php';
$pdo = pdo_init();
header('Content-Type: application/json');

//Example
$_POST['read'] = true;

// Fetch information for Classname

if (isset($_POST['read'])) {

    $query = $pdo->prepare("SELECT * FROM `admin` ");
    $query->execute();
    $results = array();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $results[] = $row;
    }

    if (!empty($results)) {
        echo json_encode($results, JSON_PRETTY_PRINT);
    } else {
        echo json_encode(['error' => 'No classes found'], JSON_PRETTY_PRINT);
    }


    exit();
}

// Rest of your existing code...
