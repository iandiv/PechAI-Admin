<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_COOKIE['a_id']) && empty($_SESSION['aid'])) {
    header("Location: index.php");
}

include('class/userClass.php');
$userClass = new userClass();
$adminDetails = $userClass->adminDetails($_COOKIE['a_id']);
$lang = isset($_GET['lang']) ? $_GET['lang'] : '';





$status = '';



if (isset($_POST['update'])) {

    // Assuming $disease is the condition you want to use to filter the rows to update



    $query = $pdo->prepare("SELECT * FROM `classes`");
    $query->execute(array());
    $list = $query->fetchAll(PDO::FETCH_OBJ);
    
    foreach ($list as $bd) {
        if($bd->classname != "Healthy" && $bd->classname != "Invalid"){
            $disease = $_GET['disease'];
            $Info = $_POST[$bd->id . 'info'];
            $Cause = $_POST[$bd->id . 'cause'];
            $Rec = $_POST[$bd->id . 'rec'];
            $Info_ceb = $_POST[$bd->id . 'info_ceb'];
            $Cause_ceb = $_POST[$bd->id . 'cause_ceb'];
            $Rec_ceb = $_POST[$bd->id . 'rec_ceb'];
    
            // Update only the row where `classname` matches $disease
            try {
                if ($bd->classname == $disease) {
                    $update = $pdo->prepare("UPDATE `classes` SET `information` =:info, `causes` =:cause, `recommendation` =:rec,`information_ceb` =:info_ceb, `causes_ceb` =:cause_ceb, `recommendation_ceb` =:rec_ceb WHERE classname =:disease");
    
                    $update->bindParam(":info", $Info);
                    $update->bindParam(":cause", $Cause);
                    $update->bindParam(":rec", $Rec);
                    $update->bindParam(":info_ceb", $Info_ceb);
                    $update->bindParam(":cause_ceb", $Cause_ceb);
                    $update->bindParam(":rec_ceb", $Rec_ceb);
                    $update->bindParam(":disease", $disease);
    
                    $ok = $update->execute();
    
                    if ($ok) {
                        $_SESSION['status'] = '<div class="successMsg border round">Saved Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
                    } else {
                        $_SESSION['status'] = '<div class="errorMsg border  round">Failed to Save!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
                    }
                }
            } catch (PDOException $e) {
                // Handle the error here
                $_SESSION['status'] = 'Error: ' . $e->getMessage();
            }
        }
        
    }
}
$isRenamed = false;
$newValue = '';
if (isset($_POST['add'])) {
    $diseaseName = $_POST['diseaseName'];

    $add = $pdo->prepare("INSERT INTO `classes`(`classname`) VALUES (:diseaseName)");
    $add->bindParam(":diseaseName", $diseaseName);

    $ok = $add->execute();
    if ($ok) {

        $_SESSION['status'] = '<div class="successMsg border  round">Added Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
        // header("Location: inventory.php?page=inventory&disease=$diseaseName");

        $isRenamed = true;
        $newValue = $diseaseName;
    } else {
        $_SESSION['status'] = '<div class="errorMsg border  round">Failed to Add!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
    }
}

if (isset($_POST['edit'])) {
    $diseaseRename = $_POST['diseaseRename'];
    $disease = $_GET['disease'];
    $target_dir = 'datasets/' . ucwords($disease);
    $new_dir = 'datasets/' . ucwords($diseaseRename);

    $edit = $pdo->prepare("UPDATE `classes` SET `classname` =:diseaseRename WHERE classname =:disease");
    $edit->bindParam(":diseaseRename", $diseaseRename);
    $edit->bindParam(":disease", $disease);

    $ok = $edit->execute();
    if ($ok) {

        $_SESSION['status'] = '<div  class="successMsg border  round">Edited Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';

        if (file_exists($target_dir)) {

            renameDirectory($target_dir, $new_dir);

        }
        $isRenamed = true;
        $newValue = $diseaseRename;
    } else {
        $_SESSION['status'] = '<div class="errorMsg border  round">Failed to Edit!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
    }
}

if (isset($_POST['delete'])) {
    $disease = $_GET['disease'];
    // Check if the disease exists in the classes table
    $checkExistenceQuery = $pdo->prepare("SELECT * FROM `classes` WHERE classname = :disease");
    $checkExistenceQuery->bindParam(":disease", $disease);
    $checkExistenceQuery->execute();
    $existingEntry = $checkExistenceQuery->fetch(PDO::FETCH_OBJ);

    if ($existingEntry) {
        // Disease exists, proceed with deletion

        // Delete entries from classes and datasets tables
        $delClasses = $pdo->prepare("DELETE FROM `classes` WHERE classname = :disease");
        $delClasses->bindParam(":disease", $disease);

        $delDatasets = $pdo->prepare("DELETE FROM `datasets` WHERE ds_classname = :disease");
        $delDatasets->bindParam(":disease", $disease);

        $okClasses = $delClasses->execute();
        $okDatasets = $delDatasets->execute();

        if ($okClasses && $okDatasets) {
            // Delete related folder on success
            $target_dir = 'datasets/' . ucwords($disease);
            if (file_exists($target_dir)) {
                // Check if the folder exists before attempting to remove it
                // Recursively remove the directory and its contents
                removeDirectory($target_dir);
            }
            $_SESSION['status'] = '<div class="successMsg border round">Deleted Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
            $isRenamed = true;
            $newValue = "";
        } else {
            $_SESSION['status'] = '<div class="errorMsg border round">Failed to Delete!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
        }
    } else {
        // Disease doesn't exist, handle accordingly
        $_SESSION['status'] = '<div class="errorMsg border round">Disease not found!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
    }
    // $del = $pdo->prepare("DELETE FROM `classes` WHERE classname =:disease");
    // $del->bindParam(":disease", $disease);
    // $delD = $pdo->prepare("DELETE FROM `datasets` WHERE ds_classname =:disease");
    // $delD->bindParam(":disease", $disease);
    // $ok = $del->execute();

    // if ($ok) {
    //     $delD->execute();
    //     $target_dir = 'datasets/' . ucwords($disease);
    //     if (file_exists($target_dir)) {
    //         // Check if the folder exists before attempting to remove it
    //         // Recursively remove the directory and its contents
    //         removeDirectory($target_dir);
    //     }
    //     $_SESSION['status'] = '<div class="successMsg border  round">Deleted Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
    // } else {
    //     $_SESSION['status'] = '<div class="errorMsg border  round">Failed to Delete!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
    // }


}

// Function to recursively remove a directory and its contents
function removeDirectory($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    removeDirectory($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
}


function renameDirectory($oldDir, $newDir)
{
    if (is_dir($oldDir)) {
        if (!is_dir($newDir)) {
            // Rename the directory
            if (rename($oldDir, $newDir)) {
                return true; // Directory renamed successfully
            } else {
                return false; // Failed to rename directory
            }
        } else {
            return false; // New directory already exists
        }
    } else {
        return false; // Old directory does not exist
    }
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
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />



    <link rel="stylesheet" href="style.css">
    <!-- <script src="javascript/javascript.js"></script> -->
    <style>
        
        .image-container {
            position: relative;
            display: inline-block;
            /* Ensure inline-block so that checkbox and image are on the same line */
        }
    </style>
</head>

<body class="white-f5 ">

    <?php
    include 'sys/sidebar.php'
        ?>

    <div id="main">
        <?php
        include 'sys/admin_navbar.php'
            ?>
        <!--ADD Modal -->
        <form method="post" action="">
            <div class="modal fade " id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content round_md border-0 shadow-lg">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="exampleModalLabel">Add </h5>
                            <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i
                                    class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" class="form-control border round" placeholder="disease name..."
                                name="diseaseName" required>

                        </div>
                        <div class="modal-footer border-0">

                            <button type="button" class="btn text-secondary fw-bold round" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add" class="btn text-primary fw-bold round">Add</button>

                        </div>
                    </div>

                </div>

            </div>

        </form>
        <!--DELETE Modal -->
        <form method="post" action="">
            <div style="z-index: 2000;" class="modal fade " id="deleteModal" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content round_md border-0 shadow-lg">
                        <div class="modal-header border-0 ">
                            <h5 class="modal-title" id="exampleModalLabel">Delete </h5>
                            <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i
                                    class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="modal-body mb-2 mt-1 text-center">

                            <h6>Are you sure you want to delete this?</h6>

                        </div>
                        <div class="modal-footer border-0 ">

                            <button type="button" class="btn text-secondary fw-bold round" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="delete" class="btn text-danger fw-bold round">Delete</button>

                        </div>
                    </div>

                </div>

            </div>

        </form>
        <!--RENAME Modal -->
        <form method="post" action="">
            <div class="modal fade " id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content round_md border-0 shadow-lg">
                        <div class="modal-header border-0 ">
                            <h5 class="modal-title" id="exampleModalLabel">Edit </h5>
                            <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i
                                    class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="modal-body ">


                            <input type="text" class="text-capitalize form-control border round" placeholder="..."
                                name="diseaseRename" id="diseaseRename" required>


                        </div>
                        <div class="d-flex gap-2  p-3">
                            <div class="w-100">

                                <button data-bs-toggle="modal" data-bs-target="#deleteModal" type="button" name="delete"
                                    class="  btn text-danger fw-bold round">Delete</button>
                            </div>
                            <button type="button" class="btn text-secondary fw-bold round" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="edit" class="btn text-primary fw-bold round">Save</button>
                        </div>
                        <!-- <div class="modal-footer">
                        
                    </div> -->
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
                    <div class="modal-body  mt-1 ps-sm-5 pe-sm-5  text-center">

                        <input type="hidden" class="form-control" id="img-id" name="img-id">
                        <img loading="lazy" decoding="async" class=" round" alt="" src="" id="viewImage"
                            style="height:60vh;width:100%;margin-bottom:-15px; object-fit: cover;">

                    </div>
                    
                    <div class="modal-footer border-0  justify-content-center">
                
                        <a id="onSeeMore"  onclick="onSeeMore()" >
                    <button type="button" class="btn fw-bold   text-primary">See more</button></a>
                        <!-- <button type="button" class="btn btn-secondary round" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="delete" class="btn btn-danger text-white round">Delete</button> -->

                    </div>

                </div>

            </div>

        </div>

    </form>

        <div class="container">


            <form method="post" action="">
                <div class=" mb-5">

                    <div class="row ps-lg-5 ps-md-3 pe-lg-5 pe-md-3 ">


                        <div class="col-md-7 col-lg-8 bg-white  round_md p-sm-4 ">
                            <?php
                            //  echo $status; 
                            
                            if ($isRenamed) {
                                $isRenamed = false;

                                // Output JavaScript code
                                echo '<script>
                                    localStorage.setItem("selectedItem", "' . $newValue . '");
                                    console.log("' . $newValue . '");
                                    // Redirect to a different page
                                    window.location.href = "disease.php?page=pest and diseases&disease=' . $newValue . '";
                                    </script>';

                                // Redirect after JavaScript code
                                // header("Location: disease.php?page=pest and diseases&disease=$newRenameValue");
                            
                                // // Ensure no further output is sent
                                // exit();
                            }

                            if (isset($_SESSION['status'])) {
                                $status = $_SESSION['status'];

                                echo $status;
                                unset($_SESSION['status']);
                            }
                            ?>

                            <table id="table" class="w-100">
                            <div class="pt-4 gap-2 align-items-center diseaseSelectContainer">
                                <?php
                                
                                    if ($_GET["disease"]!="") { ?>
                                    

                                            <div class=" w-100  ">

                                                <select class="diseaseSelect form-select mb-2  white-f5  round border-0 "
                                                    onchange="onSelectItem()" id="selectedClass"
                                                    aria-label=".form-select-lg example">
                                                    <?php
                                                    $query = $pdo->prepare("SELECT * FROM classes WHERE classname NOT IN ('Healthy', 'Invalid') ORDER BY classname ASC");
                                                    $query->execute(array());
                                                    $list = $query->fetchAll(PDO::FETCH_OBJ);
                                                    foreach ($list as $bd) {
                                                        ?>
                                                        <option value="<?php echo $bd->classname ?>">
                                                            <?php echo $bd->classname ?>
                                                        </option>

                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <button type="button" onclick="onGetName();" style="margin-top:-10px"
                                                class=" btn text-white btn-secondary  round" data-bs-toggle="modal"
                                                data-bs-target="#editModal"><i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" style="margin-top:-10px"
                                                class="  btn text-white bg-primary  round" data-bs-toggle="modal"
                                                data-bs-target="#addModal"><i class="bi bi-plus-lg"></i>
                                            </button>

                                        
                                        <?php
                                    } else  { ?>
                                            
                                                    <div class=" w-100 mt-2">
                                                        <h3 class=" " style="text-transform: capitalize;">
                                                        <?php echo "Empty"; ?>

                                                        </h3>
                                                    </div>
                                                    <!-- <button type="button" onclick="onGetName();" style="margin-top:-10px"
                                                        class=" btn text-white btn-secondary  round" data-bs-toggle="modal"
                                                        data-bs-target="#editModal"><i class="bi bi-pencil-square"></i>
                                                    </button> -->
                                                    <button type="button" style="margin-top:0px"
                                                        class="  btn text-white bg-primary  round" data-bs-toggle="modal"
                                                        data-bs-target="#addModal"><i class="bi bi-plus-lg"></i></button>


                                                
                                        
                                    <?php }
                            
                                ?>
    </div>
                                <?php
                                $query = $pdo->prepare("SELECT * FROM `classes`  WHERE 1");
                                $query->execute(array());
                                $list = $query->fetchAll(PDO::FETCH_OBJ);
                                foreach ($list as $bd) {
                                    if ($bd->classname != "Healthy" && $bd->classname != "Invalid") { ?>

                                        <tr>
                                            <td class="d-none">
                                                <?php echo $bd->classname; ?>
                                            </td>
                                            <td class="">
                                                
                                                <div>


                                                    <div class="diseaseNameContainer gap-2 mb-3 align-items-center">
                                                        <div class=" w-100 ">
                                                            <h3 class="" style="text-transform: capitalize;">
                                                                <?php echo $bd->classname; ?>

                                                            </h3>
                                                        </div>
                                                        <button type="button" onclick="onGetName();" style="margin-top:-10px"
                                                            class=" btn text-white btn-secondary  round" data-bs-toggle="modal"
                                                            data-bs-target="#editModal"><i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" style="margin-top:-10px"
                                                            class="  btn text-white bg-primary  round" data-bs-toggle="modal"
                                                            data-bs-target="#addModal"><i class="bi bi-plus-lg"></i></button>


                                                    </div>


                                                </div>
                                                <div class="ms-1 ">
                                    <!-- Your HTML Form -->
                                    <div class="d-flex gap-2 justify-content-center">
                                    <?php
                                    $stmt = $pdo->query('SELECT * FROM `datasets` WHERE ds_classname="' . $bd->classname . '" ORDER BY RAND(42) DESC LIMIT 3');
                                    $rowCount = $stmt->rowCount();

                                    if ($rowCount > 0) {
                                        while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                            
                                            <div class="image-container w-100">
                                                <img loading="lazy" decoding="async" id="img-<?php echo $rows['ds_id'] ?>"
                                                    onclick="getID('<?php echo $rows['ds_id'] ?>')"
                                                    class=" p-0  img-thumbnail round mb-1 "
                                                    src="datasets/<?php echo  $bd->classname ?>/<?php echo $rows["ds_img"] ?>"
                                                    style="width:100%; height: 135px;object-fit: cover;">



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
                                <br><h5>Language </h5>
                                                <div class="d-flex mt-2b white-dark round border">
                                                    
                                                    <span class=" white-dark border-0  input-group-text round pt-3 pb-3 ps-3" for="" style="margin-right:-40px">
                                                        <i class="bi bi-translate"></i>

                                                    </span>
                                                    <select class="languageSelect form-select ps-5 pe-4 border-0 round bg-transparent"
                                                        onchange="onSelectLanguage(this)">
                                                        <option value="cebuano"></i>Cebuano</option>
                                                        <option value="english"></i>English</option>
                                                    </select>
                                                </div>

                                                <br>

                                                <div id="english" class="collapse show">


                                                    <div>
                                                        <h5>Information </h5>
                                                        <div>
                                                            <textarea type="text " rows="6" class="border white-dark p-3 round "
                                                                name="<?php echo $bd->id; ?>info"><?php echo $bd->information; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div>
                                                        <h5>Causes </h5>
                                                        <div>
                                                            <textarea type="text " rows="6" class="border white-dark p-3 round "
                                                                name="<?php echo $bd->id; ?>cause"><?php echo $bd->causes; ?></textarea>
                                                        </div>
                                                    </div>

                                                    <br>
                                                    <div>
                                                        <h5>Recommendation </h5>
                                                        <div>
                                                            <textarea type="text " rows="6" class="border white-dark p-3 round "
                                                                name="<?php echo $bd->id; ?>rec"><?php echo $bd->recommendation; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <br>
                                                </div>
                                                <div id="cebuano" class="collapse show">


                                                    <div>
                                                        <h5>Information </h5>
                                                        <div>
                                                            <textarea type="text " rows="6" class="border white-dark p-3 round "
                                                                name="<?php echo $bd->id; ?>info_ceb"><?php echo $bd->information_ceb; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div>
                                                        <h5>Causes </h5>
                                                        <div>
                                                            <textarea type="text " rows="6" class="border white-dark p-3 round "
                                                                name="<?php echo $bd->id; ?>cause_ceb"><?php echo $bd->causes_ceb; ?></textarea>
                                                        </div>
                                                    </div>

                                                    <br>
                                                    <div>
                                                        <h5>Recommendation </h5>
                                                        <div>
                                                            <textarea type="text " rows="6" class="border white-dark p-3 round "
                                                                name="<?php echo $bd->id; ?>rec_ceb"><?php echo $bd->recommendation_ceb; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <br>
                                                </div>
                            
                                        <button type="button" style="margin-top:-10px" class=" btn text-white bg-primary  round"
                                            data-bs-toggle="modal" data-bs-target="#saveModal">
                                            Save
                                        </button>
                                    
                            </div>
                                            </td>
                                        </tr>
                                        <?php
                                    } else if ($bd->classname == "Healthy") { ?>
                                            <td class="">
                                                <div class="diseaseNameContainer gap-2 mb-3 align-items-center">
                                                    <div class=" w-100 ">
                                                        <h3 class="" style="text-transform: capitalize;">
                                                        <?php echo "Empty"; ?>

                                                        </h3>
                                                    </div>
                                                    <!-- <button type="button" onclick="onGetName();" style="margin-top:-10px"
                                                        class=" btn text-white btn-secondary  round" data-bs-toggle="modal"
                                                        data-bs-target="#editModal"><i class="bi bi-pencil-square"></i>
                                                    </button> -->
                                                    <button type="button" style="margin-top:-10px"
                                                        class="  btn text-white bg-primary  round" data-bs-toggle="modal"
                                                        data-bs-target="#addModal"><i class="bi bi-plus-lg"></i></button>


                                                </div>
                                                <div class="text-center p-5">
                                                    no disease class found!
                                                </div>
                                            </td>
                                    <?php }
                                }
                                ?>
                            </table>
                            
                        </div>
                        <div class="col-md-5 col-lg-4 ps-5 pt-4 ">


                            <div class="diseaseTable ">

                                <table class="table  " id="diseaseTable">


                                    <div class="input-group ">
                                        <!-- <h5 class="w-75">Diseases</h5> -->

                                        <input type="text" onkeyup="onDiseaseSearch()"
                                            class="bg-white ps-3  form-control border round" id="diseaseBox"
                                            placeholder="Search disease...">
                                        <span class="input-group-text  round" onclick="onDiseaseSearch()">
                                            <i class="bi bi-search"></i>
                                        </span>

                                        <!-- <button type="button" style="margin-top:-10px" class=" pb-1 pt-1 ps-4 pe-4 border-0 text-white btn-danger  round" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash"></i>
                            </button>
                            <button type="button" style="margin-top:-10px" class="  pb-1 pt-1 ps-4 pe-4 border-0 text-white btn-primary  round" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i></button>
 -->

                                    </div>
                                    <!-- <input type="text" onkeyup="onDiseaseSearch()" class="form-control border round" id="diseaseBox" placeholder="Search disease..."> -->

                                    <hr>

                                    <tbody>
                                        <?php
                                        $query = $pdo->prepare("SELECT * FROM classes WHERE classname NOT IN ('Healthy', 'Invalid') ORDER BY classname ASC");
                                        $query->execute(array());
                                        $list = $query->fetchAll(PDO::FETCH_OBJ);
                                        foreach ($list as $bd) {

                                            ?>

                                            <tr>
                                                <td class="round ps-3"
                                                    onclick="highlightRow(this, '<?php echo $bd->classname ?>')">
                                                    <?php echo $bd->classname ?>
                                                </td>

                                                <!-- <td class="round" onclick="highlightRow(this)"><?php echo $bd->classname ?></td> -->
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>




                            <br>

                        </div>
                    </div>
                </div>
                <!--SAVE Modal -->
                <div class="modal fade " id="saveModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered ">
                        <div class="modal-content round_md border-0 shadow-lg">
                            <div class="modal-header border-0">
                                <h5 class="modal-title" id="exampleModalLabel">Save</h5>
                                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i
                                        class="bi bi-x-lg"></i></button>
                            </div>
                            <div class="modal-body text-center">
                                <h6>Do you want to save?</h6>
                            </div>
                            <div class="modal-footer border-0">

                                <button type="button" class="btn btn-secondary round"
                                    data-bs-dismiss="modal">Cancel</button>
                                <!-- <button type="button" class="btn bg-primary text-white round">Add</button> -->
                                <button type="submit" name="update" id="update" class="btn text-white bg-primary round">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        include 'sys/footer.php'
            ?>
    </div>
</body>


<script>

</script>
<script>
function onSeeMore(){
    
    location.href = "datasets-view.php?page=datasets%20images&classname="+localStorage.getItem('selectedItem')+"&img=18";
}

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
    document.addEventListener('DOMContentLoaded', function () {
        // Check if a language preference is stored in LocalStorage
        const preferredLanguage = localStorage.getItem('preferredLanguage');

        // Set initial visibility based on the stored preference

        if (preferredLanguage === 'cebuano') {
            showLanguage('cebuano');
        } else {
            showLanguage('english');
        }
        // // Set the initial selected value for the <select> element
        // document.querySelectorAll('#languageSelect').value = preferredLanguage || 'english';

        const selectDropdowns = document.querySelectorAll('.languageSelect');
        selectDropdowns.forEach(function (select) {
            select.value = preferredLanguage || 'english';;

        });
    });
    // Add event listener for language selection change
    function onSelectLanguage(e) {
        const selectedLanguage = e.value;


        showLanguage(selectedLanguage);

        localStorage.setItem('preferredLanguage', selectedLanguage);
        // Store the selected language in LocalStorage

    };


    function showLanguage(language) {
        const selectDropdowns = document.querySelectorAll('.languageSelect');
        selectDropdowns.forEach(function (select) {
            select.value = language;
            console.log(language);
        });
        const languageElements = document.querySelectorAll('#' + language);
        languageElements.forEach(function (element) {
            element.classList.add('show');
        });

        const otherLanguage = language === 'cebuano' ? 'english' : 'cebuano';
        const otherLanguageElements = document.querySelectorAll('#' + otherLanguage);
        otherLanguageElements.forEach(function (element) {
            element.classList.remove('show');
        });

    }
    // Function to handle select item change

    function onSelectItem() {

        var selectElement = document.getElementById("selectedClass");
        var selectedClass = selectElement.value;

        // Store the selected item in localStorage
        localStorage.setItem('selectedItem', selectedClass);
        console.log(selectedClass)
        // Find the corresponding <td> and highlight it
        var tdElements = document.querySelectorAll('#diseaseTable td');

        tdElements.forEach(function (td) {

            if (td.innerText.includes(selectedClass)) {


                highlightRow(td, selectedClass);

            } else {

            }
        });
    }

    // Check if there is a selected class in localStorage and highlight it
    function highlightRow(row, classname) {
        var selectedRow = row.parentElement;
        // console.log(classname);



        // Remove highlighting from previously highlighted row(s)
        var highlightedRows = document.querySelectorAll('.highlighted');
        highlightedRows.forEach(function (highlightedRow) {
            highlightedRow.classList.remove('highlighted');
        });

        // Add highlighting to the selected row
        selectedRow.classList.add('highlighted');

        // Set the selected item in the <select> element
        var selectElement = document.getElementById("selectedClass");
        selectElement.value = classname;
        var selectedClass = selectElement.value;
        localStorage.setItem('selectedItem', selectedClass);
        diseaseItemClick(row, classname);
    }
    // Highlight the first td by default

    document.addEventListener('DOMContentLoaded', function () {

        var currentURL = window.location.href;

        // Create a URLSearchParams object from the query string
        var urlParams = new URLSearchParams(window.location.search);

        // Get a specific parameter by name
        var diseaseParam = urlParams.get('disease');

        var firstTd = document.querySelector('#diseaseTable td:first-child');
        var classname = firstTd.innerText;
        var tdElements = document.querySelectorAll('#diseaseTable td');
        var found = false;

        tdElements.forEach(function (td) {
            var textContent = td.innerText;
            if (diseaseParam == textContent) {
                found = true;

                onSelectItem()
                // highlightRow(td, diseaseParam);
                return;

            }
        });
        if (!found) {
            //console.log("F"+diseaseParam);
            onSelectItem()
            // highlightRow(firstTd, classname);
        }






    });


    var selectedItem = localStorage.getItem('selectedItem');
    if (selectedItem) {
        // Set the selected item in the <select> element
        var selectElement = document.getElementById("selectedClass");
        selectElement.value = selectedItem;

        // Find the corresponding <td> and highlight it
        var tdElements = document.querySelectorAll('#diseaseTable td');
        tdElements.forEach(function (td) {
            if (td.innerText === selectedItem) {
                highlightRow(td, selectedItem);
            }
        });
    }



    function diseaseItemClick(row, selectedClass) {
        var input, filter, table, tr, td, i, txtValue;
        var currentURL = window.location.href.split("?")[0];
        var newURL = currentURL + "?page=pest and diseases&disease=" + encodeURIComponent(selectedClass) + "";
        window.history.pushState(null, null, newURL);

        filter = selectedClass.toUpperCase();
        table = document.getElementById("table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    function onGetName() {
        var diseaseRename = document.getElementById('diseaseRename');
        var currentURL = window.location.href;
        var urlParams = new URLSearchParams(window.location.search);
        var diseaseName = urlParams.get('disease');
        diseaseRename.value = diseaseName;
    }

    function onDiseaseSearch() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("diseaseBox");
        filter = input.value.toUpperCase();
        table = document.getElementById("diseaseTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }




</script>


<!-- <script>
        function onSelectItem() {
            var selectElement = document.getElementById("selectedClass");
            var selectedClass = selectElement.value;
            // Store the selected item in localStorage
            localStorage.setItem('selectedItem', selectedClass);

            // Find the corresponding <td> and highlight it
            var tdElements = document.querySelectorAll('#diseaseTable td');
            tdElements.forEach(function(td) {
                if (td.textContent === selectedClass) {
                    highlightRow(td, selectedClass);
                }
            });
        }
        // Check if there is a selected class in localStorage and highlight it
        function highlightRow(row, classname) {
            var selectedRow = row.parentElement;
            // Remove highlighting from previously highlighted row(s)
            var highlightedRows = document.querySelectorAll('.highlighted');
            highlightedRows.forEach(function(highlightedRow) {
                highlightedRow.classList.remove('highlighted');
            });

            // Add highlighting to the selected row
            selectedRow.classList.add('highlighted');


            diseaseItemClick(row, classname);
        }

        var selectedItem = localStorage.getItem('selectedItem');
        if (selectedItem) {
            // Set the selected item in the <select> element
            var selectElement = document.getElementById("selectedClass");
            selectElement.value = selectedItem;

            // Find the corresponding <td> and highlight it
            var tdElements = document.querySelectorAll('#diseaseTable td');
            tdElements.forEach(function(td) {
                if (td.textContent === selectedItem) {
                    highlightRow(td, selectedItem);
                }
            });
        }


        function diseaseItemClick(row, selectedClass) {


            var input, filter, table, tr, td, i, txtValue;
            // input = document.getElementById("selectedClass");
            // alert(selectedClass)
            var currentURL = window.location.href.split("?")[0];
            // Construct the new URL
            var newURL = currentURL + "?disease=" + encodeURIComponent(selectedClass);
            // Update the URL without a page reload

            window.history.pushState(null, null, newURL);

            filter = selectedClass.toUpperCase();
            table = document.getElementById("table");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // Check if there's a previously clicked class name in localStorage
        // var selectedClass = localStorage.getItem('selectedClass');
        // if (selectedClass) {
        //     // Find the corresponding <td> and highlight it
        //     var tdElements = document.querySelectorAll('#diseaseTable td');
        //     tdElements.forEach(function(td) {
        //         if (td.textContent === selectedClass) {
        //             highlightRow(td, selectedClass);

        //         }
        //     });
        // }
    </script> -->


</body>

</html>