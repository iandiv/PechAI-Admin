<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_COOKIE['a_id']) && empty($_SESSION['aid'])) {
    header("Location: index.php");
}

include ('class/userClass.php');
$userClass = new userClass();
$adminDetails = $userClass->adminDetails($_COOKIE['a_id']);





if (isset($_POST['submit'])) {
    $classname = $_POST['classname'];
    $description = $_POST['description'];

    if (!empty($_FILES["image-file"]["name"])) {
        $image = basename($_FILES["image-file"]["name"]);
        $target_dir = "uploads/";
        $target_file = $target_dir . $image;

        if (move_uploaded_file($_FILES['image-file']['tmp_name'], $target_file)) {
            $add = $pdo->prepare("INSERT INTO `contributed`(`con_img`, `con_classname`, `con_description`) VALUES (:img, :classname, :descr)");
            $add->bindParam(":img", $image);
            $add->bindParam(":classname", $classname);
            $add->bindParam(":descr", $description);

            if ($add->execute()) {

                $_SESSION['status'] = '<div class="successMsg border  round">Uploaded Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
            } else {

                $_SESSION['status'] = '<div class="errorMsg border  round">Error adding the image!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
            }
        } else {

            $_SESSION['status'] = '<div class="errorMsg border  round">Error uploading the image!<i class="bi bi-x-lg"></i></div><br>';
        }
    } else {
        echo '<script>
            alert("no uploading the image!");
            </script>';
        $_SESSION['status'] = '<div class="errorMsg border  round">No uploading the image!i class="bi bi-x-lg"></i></div><br>';
    }
}



if (isset($_POST['delete'])) {
    $id = $_POST['img-id'];


    $target_dir = 'uploads/';


    $stmt = $pdo->prepare('SELECT con_img FROM `contributed` WHERE con_id=:id');
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $image_path = $target_dir . $result['con_img'];



    // echo "<script>
    // alert('" . $image_path ."');
    //         </script>";
    $del = $pdo->prepare("DELETE FROM `contributed` WHERE con_id =:id");
    $del->bindParam(":id", $id);

    $ok = $del->execute();
    if ($ok) {
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        // echo "<script>
        // alert('Deleted Successfully');
        // </script>";
        echo '<script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
        </script>';
        $_SESSION['status'] = '<div class="successMsg border  round">Deleted Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
    } else {
        // echo "<script>
        // alert('Failed');
        // </script>";
        $_SESSION['status'] = '<div class="errorMsg border  round">Failed to Delete!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
    }
}



if (isset($_POST['accept'])) {
    // Step 1: Retrieve image information and physical file path
    $imageId = $_POST['img-id']; // Replace with the actual image ID
    $imageClassname = $_POST['img-new-classname'];
    $stmt = $pdo->prepare("SELECT * FROM contributed WHERE con_id = :imageId");
    $stmt->bindParam(":imageId", $imageId, PDO::PARAM_INT);
    $stmt->execute();
    $imageData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Step 2: Insert image information into the "datasets" table
    $targetDir = "datasets/" . $imageClassname . "/"; // The target directory for the datasets
    $newFileName = $imageData['con_img']; // Assuming "con_img" is the column containing the file name
    $newConFileName = 'con_' . $newFileName;
    $newFilePath = $targetDir . $newConFileName;

    $fileClassname = $imageData['con_classname'];
    // Prepare and execute an INSERT statement in the "datasets" table
    $insertStmt = $pdo->prepare("INSERT INTO datasets (ds_img, ds_classname) VALUES (:img, :classname)");
    $insertStmt->bindParam(":img", $newConFileName, PDO::PARAM_STR);
    $insertStmt->bindParam(":classname", $imageClassname, PDO::PARAM_STR); // Assuming you have a description column
    $insertStmt->execute();

    // Step 3: Move the physical file
    $sourceFilePath = "uploads/" . $newFileName; // Assuming the source folder is "uploads"
    if (file_exists($sourceFilePath)) {
        if (rename($sourceFilePath, $newFilePath)) {

            $_SESSION['status'] = '<div class="successMsg border  round">Successfully added an image to datasets!<i class="bi bi-check-lg"></i></div><br>';
        } else {


            $_SESSION['status'] = '<div class="errorMsg border  round">Failed to add an image to datasets!i class="bi bi-x-lg"></i></div><br>';
        }
    } else {

        $_SESSION['status'] = '<div class="errorMsg border  round">Source file not found!i class="bi bi-x-lg"></i></div><br>';
    }

    // Step 4: Delete the image from the "images" table (if needed)
    $deleteStmt = $pdo->prepare("DELETE FROM contributed WHERE con_id = :imageId");
    $deleteStmt->bindParam(":imageId", $imageId, PDO::PARAM_INT);
    $deleteStmt->execute();
}
if (isset($_POST['delCheck']) && is_array($_POST['delCheck'])) {
    $classname = $_GET['classname'];
    $target_dir = 'uploads/';


    foreach ($_POST['delCheck'] as $con_id) {
        $stmt = $pdo->prepare('SELECT con_img FROM `contributed` WHERE con_id=:id');
        $stmt->bindParam(":id", $con_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $imagePath = $target_dir . $result['con_img'];
        // echo $imagePath;
        $deleteStatement = $pdo->prepare('DELETE FROM `contributed` WHERE con_id = :con_id');
        $deleteStatement->bindParam(':con_id', $con_id, PDO::PARAM_INT);

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
        <?= ucwords($_GET['classname']) ?> | PechAI
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


    <style>
        /* img:hover {
            background-color: #f5f5f5;
            border-color: #176e43;
            border-width: 2px;
        } */

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
            margin-left: -30px;

            z-index: 1;
            /* Ensure checkbox appears above the image */
        }
    </style>
    <link rel="stylesheet" href="style.css">
    <script src="javascript/javascript.js"></script>

</head>

<body class="white-f5">

    <?php
    include 'sys/sidebar.php'
        ?>


    <!--VIEW Modal -->
    <form method="post" action="">
        <div style="z-index: 2000;" class="modal fade " id="viewModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content round_md border-0 shadow-lg">
                    <div class="modal-header border-0 ">
                        <h5 class="modal-title text-truncate" id="viewModalLabel">Title </h5>
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="modal-body mb-2 mt-1 ps-sm-5 pe-sm-5">
                        <input type="hidden" name="img-classname" class="d-none" id="img-classname">

                        <input type="hidden" class="form-control d-none" id="img-id" clas name="img-id">
                        <!-- <img loading="lazy" decoding="async" class=" round" alt="" width="100%" src="" id="viewImage"> -->
                        <img loading="lazy" decoding="async" class=" round" alt="" src="" id="viewImage"
                            style="height:60vh;width:100%; object-fit: cover;">

                        <label for="">Disease name:</label>
                        <select class="form-select mt-2" id="img-class" name="img-new-classname">
                            <?php
                            $query = $pdo->prepare("SELECT * FROM classes  ORDER BY classname ASC");
                            $query->execute(array());
                            $list = $query->fetchAll(PDO::FETCH_OBJ);

                            foreach ($list as $bd) {

                                ?>
                            
                                <option value="<?php echo $bd->classname ?>" <?php echo ($_GET['classname'] == $bd->classname) ? "selected" : ""; ?>>
                                
                                <?php echo ($_GET['classname'] == $bd->classname) ? " • " : ""; ?><?php echo $bd->classname ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                        
                    </div>
                    <div class=" border-0 p-4">

                        <button type="submit" name="delete" class=" btn btn-danger text-white round">Delete</button>
                        <div class="float-end">
                            <!-- <button type="button" class="  btn btn-secondary round" data-bs-dismiss="modal">Close</button> -->
                            <button type="submit" name="accept" class="  btn bg-primary text-white round">Add to
                                datasets</button>
                        </div>

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

                        echo $status . "<small>&nbsp;</small>";
                        unset($_SESSION['status']);
                    }

                    ?>



                    <div class="col-md-12 col-lg-12 bg-white round_md p-sm-4 ">
                        <form method="post">


                            <div class="row">
                                <div class="col-sm-10">
                                    <div class="">
                                        <!-- <a class="back-btn  round text-decoration-none text-dark"
                                            href="datasets.php?page=datasets images"> -->
                                        <h3 class="text-capitalize mb-0" style="font-size: calc(1.2rem + 0.64vw);">
                                            <?= $_GET['classname'] ?>
                                        </h3>
                                        <span class="text-capitalize ">
                                            <?php echo $_GET['page'] ?>
                                        </span>


                                    </div>
                                </div>
                                <div class="col-sm-2  ">
                                    <div class="float-end btn-group ">
                                        <?php
                                        $stmt = $pdo->query('SELECT * FROM `contributed` WHERE con_classname="' . $_GET['classname'] . '" ORDER BY con_id DESC ');
                                        $rowCount = $stmt->rowCount();

                                        if ($rowCount > 0) { ?>
                                            <button class=" btn bg-secondary mb-2 round text-white" type="submit"><i
                                                    class="bi bi-trash"></i></button>
                                        <?php }
                                        ?>
                                    </div>
                                </div>

                            </div>



                            <!-- <form method="post" enctype="multipart/form-data">



                            <input type="file" name="images[]" multiple>

                            <button type="submit" name="upload">
                                Upload</button>
                        </form> -->
                            <div class="row gap-2 p-sm-2 ">
                                <hr>
                                <div class="ms-2 ">
                                    <!-- Your HTML Form -->

                                    <?php
                                    $stmt = $pdo->query('SELECT * FROM `contributed` WHERE con_classname="' . $_GET['classname'] . '" ORDER BY con_id DESC ');
                                    $rowCount = $stmt->rowCount();


                                    if ($rowCount > 0) {
                                        while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                            <div class="image-container">
                                                <img loading="lazy" decoding="async" id="img-<?php echo $rows['con_id'] ?>"
                                                    onclick="getID('<?php echo $rows['con_id'] ?>')"
                                                    class="  p-0  img-thumbnail round mb-1 "
                                                    src="uploads/<?php echo $rows["con_img"] ?>"
                                                    style=" width:  135px;height: 135px;object-fit: cover;">

                                                <input class="form-check-input rounded-circle h4 " type="checkbox" name="delCheck[]"
                                                    value="<?php echo $rows['con_id'] ?>"
                                                    id="delCheck-<?php echo $rows['con_id'] ?>">

                                                <input type="hidden" id="classname-<?php echo $rows['con_id'] ?>"
                                                    value="<?php echo $rows['con_classname'] ?>">
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

                            <!--<div class="row gap-2 p-sm-2">
                            <hr>
                            <div class="ms-2">
                                <?php
                                $stmt = $pdo->query('SELECT * FROM `contributed` WHERE con_classname="' . $_GET['classname'] . '"ORDER BY con_id DESC ');
                                $rowCount = $stmt->rowCount();

                                // Output the row count
                                if ($rowCount > 0) {

                                    while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>

                                        <div class="image-container">
                                        <img loading="lazy" decoding="async" id="img-<?php echo $rows['con_id'] ?>"
                                            onclick="getID('<?php echo $rows['con_id'] ?>')"
                                            class="  p-0  img-thumbnail round mb-1 "
                                            src="uploads/<?php echo $rows["con_img"] ?>"
                                            style=" width:  135px;height: 135px;object-fit: cover;">

                                            <input class="form-check-input" type="checkbox" name="delCheck[]"
                                                value="<?php echo $rows['con_id'] ?>" id="delCheck-<?php echo $rows['con_id'] ?>">


                                        </div>
                                    

                                        <input type="hidden" id="classname-<?php echo $rows['con_id'] ?>"
                                            value="<?php echo $rows['con_classname'] ?>">

                                    <?php }
                                } else {

                                    // echo "  <script>
                                    // const newURL = new URL(window.location.href);
                                    // newURL.searchParams.set('img',0);
                                    // history.pushState(null, null, newURL.toString());
                                
                                    // </script>";
                                    ?>
                                    <div class="text-center  p-5 ">
                                        <h6>No images found!</h6>
                                    </div>
                                <?php }
                                ?>
                            </div>
                        </div>-->
                        </form>
                        <div class="row">
                            <div class="col-8">
                                <button class="ms-sm-3 loadmore btn bg-primary text-white">Load More</button>



                            </div>
                            <div class="col-4 align-items-center d-flex justify-content-end">
                                <?php
                                $stmt = $pdo->query('SELECT * FROM `contributed` WHERE con_classname="' . $_GET['classname'] . '"');
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
                    </div>
                </div>

            </div>
        </div>

        <?php
        include 'sys/footer.php'
            ?>
    </div>

</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // const observer = lozad('.lozad', {
        //     loaded: function(el) {
        //         // Custom callback when the element is loaded
        //         el.classList.add('loaded');
        //     },
        //     threshold: 0.1, // Load elements when they are 10% within the viewport
        // });
        // observer.observe();
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
        var imgClassname = document.getElementById("classname-" + id);


        var targetImage = document.getElementById("viewImage");
        var viewModalLabel = document.getElementById("viewModalLabel");
        var ImgClassname = document.getElementById("img-classname");




        ImgClassname.value = imgClassname.value;




        targetImage.src = sourceImage.src;
        var parts = sourceImage.src.split('/');
        var fileName = parts[parts.length - 1];
        viewModalLabel.textContent = fileName;

    }
</script>

</html>