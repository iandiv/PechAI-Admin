<?php
include 'sys/ini.php';
$pdo = pdo_init();
header('Content-Type: application/json');

//Example
// $_POST['read'] = true;
// $_POST['disease'] = "Diamondback moth";  

// Fetch information for Classname

if (isset($_POST['read'])) {
    $disease = $_POST['disease']; // Set the specific classname you want to retrieve
    
    $query = $pdo->prepare("SELECT * FROM `classes` WHERE classname=:disease");
    $query->bindParam(":disease", $disease);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Output the information as JSON or perform other actions as needed
        echo json_encode($result, JSON_PRETTY_PRINT);
    } else {
        // Handle the case when no rows are found
        echo json_encode(['error' => 'No classes found'], JSON_PRETTY_PRINT);
    }

    // Exit to avoid executing the rest of the script
    exit();
}

// Rest of your existing code...
