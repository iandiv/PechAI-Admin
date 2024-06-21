<?php
include 'sys/ini.php';
$pdo = pdo_init();
header('Content-Type: application/json');


$_POST['api_key']  = "MtMmLomDuTNSOYvgVVVHDnaf17zsQ0Av";
// Check if the API key is provided in the POST request
if (isset($_POST['api_key'])) {
    $providedApiKey = $_POST['api_key'];
    // SQL query to check if the provided API key exists in the 'apikey' table

    
    try {
        // Prepare the SQL statement
        $apiKeyStmt = $pdo->prepare("SELECT id FROM apikey WHERE `apikey` = :apiKey" );
        $apiKeyStmt->bindParam(':apiKey', $providedApiKey, PDO::PARAM_STR);

        // Execute the query
        $apiKeyStmt->execute();

        // Check if the API key is valid
        if ($apiKeyStmt->rowCount() > 0) {
            // API key is valid; proceed to fetch data

            // Dynamically detect the base URL
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $domain = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);


            try {
                // Connect to the database

                // SQL query to fetch ds_img URLs categorized by ds_classname
                $sql = "SELECT ds_classname, ds_img FROM datasets ";

                // Prepare the SQL statement
                $stmt = $pdo->prepare($sql);

                // Execute the query
                $stmt->execute();

                // Fetch the results into an associative array
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Organize the data into a structure with full URLs
                $data = array();
                foreach ($results as $row) {
                    $ds_classname = ucwords($row['ds_classname']);
                    $ds_img = $domain . '/datasets/' . $ds_classname . '/' . $row['ds_img']; // Construct the URL dynamically

                    if (!isset($data[$ds_classname])) {
                        $data[$ds_classname] = array();
                    }

                    $data[$ds_classname][] = $ds_img;
                }

                // Convert the PHP array to JSON
                $jsonResult = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                // Output the JSON
                echo $jsonResult;
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        } else {
            // API key is invalid
            echo json_encode(array("error" => "Invalid API key"));
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    echo json_encode(array("error" => "API key not provided"));
}
