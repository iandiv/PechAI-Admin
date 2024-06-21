<?php
include 'sys/ini.php';
$pdo = pdo_init();


if (isset($_POST['model']) && isset($_POST['model'])) {
    $modelData = $_POST['model'];
    $labelsData = $_POST['labels'];

    // Generate unique file names with a timestamp
    $timestamp = time();
    $target_dir = "model_uploads/";
    $model_file_name = "model.tflite";
    $labels_file_name = "labels.txt";

    $model_target_file = $target_dir . $model_file_name;
    $labels_target_file = $target_dir . $labels_file_name;


    // Check if the file already exists
    $model_count = 1;
    while (file_exists($model_target_file)) {
        $model_file_name = "model_" . $model_count . ".tflite";
        $model_target_file = $target_dir . $model_file_name;
        $model_count++;
    }

    // Check if the labels file already exists
    $labels_count = 1;
    while (file_exists($labels_target_file)) {
        $labels_file_name = "labels_" . $labels_count . ".txt";
        $labels_target_file = $target_dir . $labels_file_name;
        $labels_count++;
    }


    if (
        file_put_contents($model_target_file, base64_decode($modelData)) &&
        file_put_contents($labels_target_file, base64_decode($labelsData))
    ) {
        // Successfully saved both model and labels files

        // Insert the data into the database
        $add = $pdo->prepare("INSERT INTO `models`(`model_file`, `label_file`) VALUES (:model_file, :label_file)");
        $add->bindParam(":model_file", $model_file_name);
        $add->bindParam(":label_file", $labels_file_name);

        if ($add->execute()) {
            echo json_encode([
                "Message" => "Model and Labels Uploaded Successfully!",
            ], JSON_PRETTY_PRINT);
        } else {
            echo json_encode([
                "Message" => "Error uploading the model and labels!",
            ], JSON_PRETTY_PRINT);
        }
    } else {
        echo json_encode([
            "Message" => "Error saving the model or labels file!",
        ], JSON_PRETTY_PRINT);
    }
} else {
    echo json_encode([
        "Message" => "Model or labels data not provided",
    ], JSON_PRETTY_PRINT);
}

?>