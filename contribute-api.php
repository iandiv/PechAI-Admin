<?php
include 'sys/ini.php';
$pdo = pdo_init();

// if (isset($_POST['submit'])) {
//     if (isset($_POST['image'])) {
//         $imageData = $_POST['image'];

//         // Generate a unique file name with a timestamp
//         $timestamp = time();
//         $target_dir = "uploads/";
//         $file_name = $timestamp . ".jpg";
//         $target_file = $target_dir . $file_name;

//         if (file_put_contents($target_file, base64_decode($imageData))) {
//             // Successfully saved the image

//             $classname = $_POST['classname']; // Assuming you have this variable set

//             // Insert the data into the database
//             $add = $pdo->prepare("INSERT INTO `contributed`(`con_img`, `con_classname`) VALUES (:img, :classname)");
//             $add->bindParam(":img", $file_name);
//             $add->bindParam(":classname", $classname);

//             if ($add->execute()) {
//                 echo json_encode([
//                     "Message" => "Uploaded Successfully!",
//                 ], JSON_PRETTY_PRINT);
//             } else {
//                 echo json_encode([
//                     "Message" => "Error uploading the image!",
//                 ], JSON_PRETTY_PRINT);
//             }
//         } else {
//             echo json_encode([
//                 "Message" => "Error saving the image!",
//             ], JSON_PRETTY_PRINT);
//         }
//     } else {
//         echo json_encode([
//             "Message" => "Image data not provided",
//         ], JSON_PRETTY_PRINT);
//     }
// }


if (isset($_POST['submit'])) {
    if (isset($_POST['image'])) {
        $imageData = $_POST['image'];

        // Decode the base64 encoded image data
        $decodedImage = base64_decode($imageData);

        // Create an image resource from the decoded image
        $image = imagecreatefromstring($decodedImage);

        // Get original image dimensions
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Calculate new dimensions while maintaining aspect ratio
        $maxSize = 300;
        if ($originalWidth > $originalHeight) {
            $newWidth = $maxSize;
            $newHeight = $originalHeight * ($maxSize / $originalWidth);
        } else {
            $newHeight = $maxSize;
            $newWidth = $originalWidth * ($maxSize / $originalHeight);
        }

        // Create a new blank image with the resized dimensions
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Resize the original image to the new dimensions
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Output the resized image to a buffer
        ob_start();
        imagejpeg($resizedImage, NULL, 100);
        $resizedImageData = ob_get_clean();

        // Generate a unique file name with a timestamp
        $timestamp = time();
        $target_dir = "uploads/";
        $file_name = $timestamp . ".jpg";
        $target_file = $target_dir . $file_name;

        // Save the resized image
        if (file_put_contents($target_file, $resizedImageData)) {
            // Successfully saved the image

            $classname = $_POST['classname']; // Assuming you have this variable set

            // Insert the data into the database
            $add = $pdo->prepare("INSERT INTO `contributed`(`con_img`, `con_classname`) VALUES (:img, :classname)");
            $add->bindParam(":img", $file_name);
            $add->bindParam(":classname", $classname);

            if ($add->execute()) {
                echo json_encode([
                    "Message" => "Uploaded Successfully!",
                ], JSON_PRETTY_PRINT);
            } else {
                echo json_encode([
                    "Message" => "Error uploading the image!",
                ], JSON_PRETTY_PRINT);
            }
        } else {
            echo json_encode([
                "Message" => "Error saving the image!",
            ], JSON_PRETTY_PRINT);
        }

        // Free up memory
        imagedestroy($image);
        imagedestroy($resizedImage);
    } else {
        echo json_encode([
            "Message" => "Image data not provided",
        ], JSON_PRETTY_PRINT);
    }
}

?>