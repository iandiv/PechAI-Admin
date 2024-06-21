<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_COOKIE['a_id']) && empty($_SESSION['aid'])) {
    header("Location: index.php");
}

include('class/userClass.php');
$userClass = new userClass();
$adminDetails = $userClass->adminDetails($_COOKIE['a_id']);




// if (isset($_POST['upload'])) {



//     $images = $_FILES['images'];

//     # Number of images
//     $num_of_imgs = count($images['name']);

//     for ($i = 0; $i < $num_of_imgs; $i++) {

//         # get the image info and store them in var
//         $image_name = $images['name'][$i];
//         $tmp_name   = $images['tmp_name'][$i];
//         $error      = $images['error'][$i];

//         # if there is not error occurred while uploading
//         if ($error === 0) {

//             # get image extension store it in var
//             $img_ex = pathinfo($image_name, PATHINFO_EXTENSION);

//             // /** 
//             // convert the image extension into lower case 
//             // and store it in var 
//             //  **/
//             $img_ex_lc = strtolower($img_ex);

//             // /** 
//             // crating array that stores allowed
//             // to upload image extensions.
//             //  **/
//             $allowed_exs = array('jpg', 'jpeg', 'png');


//             // /** 
//             // check if the the image extension 
//             // is present in $allowed_exs array
//             //  **/

//             if (in_array($img_ex_lc, $allowed_exs)) {
//                 // //** 
//                 //  renaming the image name with 
//                 //  with random string
//                 //  **//


//                 $new_img_name = uniqid('IMG-', true) . '.' . $img_ex_lc;
//                 # crating upload path on root directory
//                 $classname = $_GET['classname'];
//                 $target_dir =  'datasets/' . ucwords($classname);
//                 if (!file_exists($target_dir)) {

//                     mkdir($target_dir, 0777, true);
//                 }
//                 $img_upload_path = $target_dir . '/' . $new_img_name;

//                 # inserting imge name into database

//                 $sql  = "INSERT INTO `datasets`( `ds_img`, `ds_classname`) VALUES (:img,:classname)";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindParam(":img", $new_img_name);
//                 $stmt->bindParam(":classname", $classname);
//                 $ok = $stmt->execute();
//                 if ($ok) {
//                     move_uploaded_file($tmp_name, $img_upload_path);
//                     // header("Location: index.php");

//                     $_SESSION['status'] = '<div class="successMsg border  round">
//                     Uploaded Successfully!&nbsp;<i class="bi bi-check-lg"></i>
//                     </div><br>';

//                     // header("Location: datasets-view.php?page=datasets+images&classname=".$_GET['classname']."&im")
//                     echo '<script>
//                     if ( window.history.replaceState ) {
//                         window.history.replaceState( null, null, window.location.href );
//                     }
//                     </script>';
//                 } else {


//                     $_SESSION['status'] = '<div class="errorMsg border  round">Error uploading the image!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
//                 }
//                 # move uploaded image to 'uploads' folder

//             } else {
//                 # error message


//                 $_SESSION['status'] = '<div class="errorMsg border  round">You can\'t upload files of this type&nbsp;<i class="bi bi-x-lg"></i></div><br>';
//             }
//         } else {
//             # error message
//             $_SESSION['status'] = '<div class="errorMsg border  round">Unknown Error Occurred while uploading&nbsp;<i class="bi bi-x-lg"></i></div><br>';
//         }
//     }
// }
if (isset($_POST['upload'])) {
    $images = $_FILES['images'];
    $num_of_imgs = count($images['name']);

    for ($i = 0; $i < $num_of_imgs; $i++) {
        $image_name = $images['name'][$i];
        $tmp_name = $images['tmp_name'][$i];
        $error = $images['error'][$i];

        if ($error === 0) {
            $img_ex = pathinfo($image_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $allowed_exs = array('jpg', 'jpeg', 'png');

            if (in_array($img_ex_lc, $allowed_exs)) {
                $new_img_name = uniqid('IMG-', true) . '.' . $img_ex_lc;
                $classname = $_GET['classname'];
                $target_dir = 'datasets/' . ucwords($classname);
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $img_upload_path = $target_dir . '/' . $new_img_name;

                // Resize image to a maximum width or height of 300 pixels while maintaining aspect ratio
                list($width, $height) = getimagesize($tmp_name);
                $max_size = 300;

                $new_width = $width;
                $new_height = $height;

                if ($width > $max_size || $height > $max_size) {
                    $ratio = $width / $height;

                    if ($width > $height) {
                        $new_width = $max_size;
                        $new_height = $max_size / $ratio;
                    } else {
                        $new_height = $max_size;
                        $new_width = $max_size * $ratio;
                    }
                }

                $resized_img = imagecreatetruecolor($new_width, $new_height);
                $source_img = imagecreatefromstring(file_get_contents($tmp_name));

                imagecopyresampled($resized_img, $source_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                // Save resized image
                imagejpeg($resized_img, $img_upload_path);

                imagedestroy($resized_img);
                imagedestroy($source_img);

                // Insert image name into the database
                $sql = "INSERT INTO `datasets`( `ds_img`, `ds_classname`) VALUES (:img,:classname)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":img", $new_img_name);
                $stmt->bindParam(":classname", $classname);
                $ok = $stmt->execute();

                if ($ok) {
                    $_SESSION['status'] = '<div class="successMsg border round">
                    Uploaded Successfully!&nbsp;<i class="bi bi-check-lg"></i>
                    </div><br>';
                    echo '<script>
                    if ( window.history.replaceState ) {
                        window.history.replaceState( null, null, window.location.href );
                    }
                    </script>';
                } else {
                    $_SESSION['status'] = '<div class="errorMsg border round">Error uploading the image!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
                }
            } else {
                $_SESSION['status'] = '<div class="errorMsg border round">You can\'t upload files of this type&nbsp;<i class="bi bi-x-lg"></i></div><br>';
            }
        } else {
            $_SESSION['status'] = '<div class="errorMsg border round">Unknown Error Occurred while uploading&nbsp;<i class="bi bi-x-lg"></i></div><br>';
        }
    }
}

if (isset($_POST['delete'])) {
    $id = $_POST['img-id'];

    $classname = $_GET['classname'];
    $target_dir = 'datasets/' . ucwords($classname) . '/';


    $stmt = $pdo->prepare('SELECT ds_img FROM `datasets` WHERE ds_id=:id');
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $image_path = $target_dir . $result['ds_img'];



    // echo "<script>
    // alert('" . $image_path ."');
    //         </script>";
    $del = $pdo->prepare("DELETE FROM `datasets` WHERE ds_id =:id");
    $del->bindParam(":id", $id);

    $ok = $del->execute();
    if ($ok) {
        if (file_exists($image_path)) {
            unlink($image_path);
        }


        echo '<script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
        </script>';
        $_SESSION['status'] = '<div class="successMsg border  round">Deleted Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
    } else {

        $_SESSION['status'] = '<div class="errorMsg border  round">Failed to Delete!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
    }
}
if (isset($_POST['delCheck']) && is_array($_POST['delCheck'])) {
    $classname = $_GET['classname'];
    $target_dir = 'datasets/' . ucwords($classname) . '/';


    foreach ($_POST['delCheck'] as $ds_id) {
        $stmt = $pdo->prepare('SELECT ds_img FROM `datasets` WHERE ds_id=:id');
        $stmt->bindParam(":id", $ds_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $imagePath = $target_dir . $result['ds_img'];
        // echo $imagePath;
        $deleteStatement = $pdo->prepare('DELETE FROM `datasets` WHERE ds_id = :ds_id');
        $deleteStatement->bindParam(':ds_id', $ds_id, PDO::PARAM_INT);
        $ok = $deleteStatement->execute();

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        if ($ok) {



            echo '<script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
        </script>';
            $_SESSION['status'] = '<div class="successMsg border  round">Deleted Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
        } else {

            $_SESSION['status'] = '<div class="errorMsg border  round">Failed to Delete!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
        }
    }



} else {
    echo '<script>

    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    </script>';

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0">

    <title>
        <?= ucwords($_GET['page']) ?> | PechAI
    </title>
    <link rel=" icon" href="img/pechai-nobg.png">
    <!-- CDN -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- <script src="https://cdn.jsdelivr.net/npm/lozad@1.16.0/dist/lozad.min.js"></script> -->

    <!-- <script src="https://cdn.jsdelivr.net/npm/lozad@1.16.0/dist/lozad.min.js"></script> -->
    <style>
        .back-btn {

            border-width: 2px;


        }

        .back-btn:hover {
            /* border-color: #176e43; */
            border-width: 2px;

            background-color: #f5f5f5;
        }

        img.img-thumbnail:hover {
            background-color: #eee;
            border-color: #176e43;
            border-width: 3px;

        }

        img.img-thumbnail {
            background-color: #eee;

        }

        /* 
        

        img.img-thumbnail {
            background-color: #eee;
            border-color: #eee;
            border-width: 0px;
            display: none;

        }

        .img-thumbnail:nth-child(n+1):nth-child(-n+<?= $_GET['img'] ?>) {
            background-color: #eee;
            border-color: #eee;
            border-width: 0px;
            display: inline-block;

        }



        img.img-thumbnail:hover:nth-child(n+1):nth-child(-n+<?= $_GET['img'] ?>) {
            background-color: #eee;
            border-color: #176e43;
            border-width: 3px;

        } */
        /*         
        div.image-container:hover {
            background-color: #eee;
            border-color: #176e43;
            border-width: 3px;

        } */

        div.image-container {


            border-width: 0px;
            display: none;

        }

        div.image-container:nth-child(n+1):nth-child(-n+<?= $_GET['img'] ?>) {
            border-width: 0px;
            display: inline-block;

        }



        div.image-container:hover:nth-child(n+1):nth-child(-n+<?= $_GET['img'] ?>) {
            border-color: #176e43;
            border-width: 3px;

        }

        .image-container {
            position: relative;
            display: inline-block;
            /* Ensure inline-block so that checkbox and image are on the same line */
        }

        .form-check-input {
            position: absolute;
            margin-left: -20px;

            z-index: 1;
            /* Ensure checkbox appears above the image */
        }



        /* Define the loading pulse animation */
    </style>
    <link rel="stylesheet" href="style.css">
    <!-- <script src="javascript/javascript.js"></script> -->
</head>

<body class="white-f5">

    <?php
    include 'sys/sidebar.php'
        ?>

    <!-- Upload Modal -->
    <form method="post" enctype="multipart/form-data">

        <div style="z-index: 2000;" class="modal fade " id="uploadModal" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content round_md border-0 shadow-lg">
                    <div class="modal-header border-0 ">
                        <h5 class="modal-title text-truncate" id="viewModalLabel">Upload </h5>
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="modal-body mb-2 mt-1 ps-sm-5 pe-sm-5">



                        <input id="photo-upload" type="file" class="form-control" name="images[]" multiple />
                        <p class="text-center fw-bold text-danger" id="photo-upload-status"></p>

                    </div>
                    <div class="modal-footer border-0 ">

                        <button type="button" class="btn btn-secondary round" data-bs-dismiss="modal">Close</button>
                        <!-- <button type="submit" name="accept" class="btn bg-primary text-white round">Accept</button> -->
                        <button class="btn text-light bg-primary" type="submit" name="upload">
                            Upload</button>
                    </div>
                </div>

            </div>

        </div>

    </form>
    <!--VIEW Modal -->
    <form method="post" action="">
        <div style="z-index: 2000;" class="modal fade " id="viewModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content round_md border-0 shadow-lg">
                    <div class="modal-header border-0 ">
                        <h5 class="modal-title text-truncate" id="filename">Title </h5>
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="modal-body mb-2 mt-1 ps-sm-5 pe-sm-5 text-center">

                        <input type="hidden" class="form-control" id="img-id" name="img-id">
                        <img loading="lazy" decoding="async" class=" round" alt="" src="" id="viewImage"
                            style="height:60vh;width:100%; object-fit: cover;">

                    </div>
                    <div class="modal-footer border-0 ">

                        <button type="button" class="btn btn-secondary round" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="delete" class="btn btn-danger text-white round">Delete</button>

                    </div>

                </div>

            </div>

        </div>

    </form>
    <div id="main">
        <?php
        include 'sys/admin_navbar.php'
            ?>

        <div class="container ">



            <div class=" mb-5">

                <div class="row ps-lg-5 ps-md-3 pe-lg-5 pe-md-3">

                    <?php
                    //  echo $status; 
                    
                    if (isset($_SESSION['status'])) {
                        $status = $_SESSION['status'];

                        echo $status . "    <small>&nbsp;</small>";
                        unset($_SESSION['status']);
                    }

                    ?>



                    <div class="col-md-12 col-lg-12  p-sm-4 ">


                        <form method="post">
                            <div class="row">
                                <div class="col-10 ">

                                    <div class="">
                                        <!-- <a class="back-btn  round text-decoration-none text-dark"
                                href="datasets.php?page=datasets images"> -->
                                        <h3 class="text-capitalize mb-0">

                                            <?= $_GET['classname'] ?>
                                        </h3>
                                        <!-- </a> -->
                                        <!-- i class="bi bi-chevron-right"></i> -->
                                        <span class="text-capitalize ">
                                            <?php echo $_GET['page'] ?>
                                        </span>


                                    </div>
                                </div>

                                <div class="col-2  ">
                                    
                                    <div class="float-end btn-group">
                                        <?php
                                        $stmt = $pdo->query('SELECT * FROM `datasets` WHERE ds_classname="' . $_GET['classname'] . '" ORDER BY ds_id DESC ');
                                        $rowCount = $stmt->rowCount();

                                        if ($rowCount > 0) { ?>
                                            <button class=" btn bg-secondary  round text-white" type="submit"><i
                                                    class="bi bi-trash"></i></button>
                                        <?php }
                                        ?>
                                        <button class=" btn bg-primary  text-light round" data-bs-toggle="modal"
                                            type="button" data-bs-target="#uploadModal"><i
                                                class="bi bi-plus-lg"></i></button>
                                    </div>
                                </div>

                            </div>




                            <div class="row gap-2 p-sm-2 ">
                                <hr>
                                <div class="ms-1 ">
                                    <!-- Your HTML Form -->

                                    <?php
                                    $stmt = $pdo->query('SELECT * FROM `datasets` WHERE ds_classname="' . $_GET['classname'] . '" ORDER BY ds_id DESC ');
                                    $rowCount = $stmt->rowCount();

                                    if ($rowCount > 0) {
                                        while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                            <div class="image-container">
                                                <img loading="lazy" decoding="async" id="img-<?php echo $rows['ds_id'] ?>"
                                                    onclick="getID('<?php echo $rows['ds_id'] ?>')"
                                                    class=" p-0  img-thumbnail round mb-1 "
                                                    src="datasets/<?php echo $_GET['classname'] ?>/<?php echo $rows["ds_img"] ?>"
                                                    style=" width:  135px;height: 135px;object-fit: cover;">

                                                <input class="form-check-input" type="checkbox" name="delCheck[]"
                                                    value="<?php echo $rows['ds_id'] ?>"
                                                    id="delCheck-<?php echo $rows['ds_id'] ?>">


                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="text-center p-5">
                                            <h6>No images found!</h6>
                                        </div>
                                        <?php
                                    }
                                    ?>


                                </div>



                            </div>


                            <div class="row">
                                <div class="col-8">

                                    <button class="ms-sm-3 loadmore btn bg-primary text-white">Load More</button>



                                </div>
                                <div class="col-4 align-items-center d-flex justify-content-end">
                                    <?php
                                    $stmt = $pdo->query('SELECT * FROM `datasets` WHERE ds_classname="' . $_GET['classname'] . '"');
                                    // $query = $pdo->prepare("SELECT * FROM `books` WHERE 1");
                                    $rowCount = $stmt->rowCount();
                                    $stmt->fetch(PDO::FETCH_ASSOC);

                                    ?>
                                    <p class=" text-end  pe-sm-4 m-0 p-0 small ">
                                        <span class="" id="loadedIMG"></span> of <span class="" id="loadedMAX">
                                            <?= $rowCount; ?>
                                        </span>
                                    </p>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <?php
        include 'sys/footer.php'
            ?>
    </div>

</body>
<script type="text/javascript">

    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById("photo-upload");
        const fileStatus = document.getElementById("photo-upload-status");
        fileInput.addEventListener("change", function () {
            if (fileInput.files.length > 80) {
                fileStatus.textContent = "You can only upload a maximum of 80 files.";
                // Clear the selected files
                fileInput.value = "";
            }
        });
    });

    var img = document.querySelectorAll('div.image-container');
    var currentimg = <?= $_GET['img'] ?>;

    if (currentimg >= img.length) {
        document.querySelector('.loadmore').style.display = 'none';
    }
    var btn = document.querySelector('.loadmore');



    const loadedIMG = document.getElementById('loadedIMG');

    const loadedMAX = document.getElementById('loadedMAX');
    if (!(parseInt(loadedMAX.textContent) > 0)) {


        setTimeout(function () {
            loadedIMG.textContent = '0';
        }, 1);


    }

    var loadmore = 18;

    if (img.length < loadmore) {
        loadedIMG.textContent = loadedMAX.textContent;
    } else {
        loadedIMG.textContent = <?= $_GET['img'] ?>;
    }

    btn.addEventListener('click', function () {

        for (var i = currentimg; i < currentimg + loadmore; i++) {
            if (img[i]) {
                img[i].style.display = 'inline-block';
                const newURL = new URL(window.location.href);
                newURL.searchParams.set('img', i + 1);
                loadedIMG.textContent = i + 1;
                history.pushState(null, null, newURL.toString());
            }
        }
        currentimg += loadmore;


        if (currentimg >= img.length) {
            event.target.style.display = 'none';
        }

    })

    function getID(id) {
        // alert(id);
        document.getElementById('img-id').value = id;
        var viewModal = new bootstrap.Modal(document.getElementById('viewModal'), {
            keyboard: false
        });
        viewModal.show();

        var sourceImage = document.getElementById("img-" + id);



        var targetImage = document.getElementById("viewImage");
        var filename = document.getElementById("filename");










        targetImage.src = sourceImage.src;
        var parts = sourceImage.src.split('/');
        var fileName = parts[parts.length - 1];
        filename.textContent = fileName;

    }
</script>

</html>