<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_COOKIE['a_id']) && empty($_SESSION['aid'])) {
    header("Location: index.php");
}

include ('class/userClass.php');
$userClass = new userClass();
$adminDetails = $userClass->adminDetails($_COOKIE['a_id']);

if (isset($_POST['delete'])) {
    $mod_id = $_POST['mod_id'];
    $stmt = $pdo->prepare('SELECT * FROM `models` WHERE mod_id =:mod_id');
    $stmt->bindParam(":mod_id", $mod_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $resultModel = $result['model_file'];
    $resultLabel = $result['label_file'];

    $target_dir = 'model_uploads/';
    $delD = $pdo->prepare("DELETE FROM `models` WHERE mod_id =:mod_id");
    $delD->bindParam(":mod_id", $mod_id);
    $ok = $delD->execute();
    $modelFile = $target_dir . $resultModel;
    $labelFile = $target_dir . $resultLabel;

    if ($ok) {
        if (file_exists($modelFile)) {
            unlink($modelFile);
        }
        if (file_exists($labelFile)) {
            unlink($labelFile);
        }
        $_SESSION['status'] = '<div class="successMsg border  round">Deleted Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
    } else {
        $_SESSION['status'] = '<div class="errorMsg border  round">Failed to Delete!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
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



    <link rel="stylesheet" href="style.css">
    <script src="javascript/javascript.js"></script>
    <style>
        .dashboard-card:hover {


            opacity: 90%;
        }
    </style>
</head>

<body class="white-f5">
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
                        <input type="hidden" id="model-id" name="mod_id">
                        <h6>Are you sure you want to delete this?</h6>

                    </div>
                    <div class="modal-footer border-0 ">

                        <button type="button" class="btn btn-secondary round" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="delete" class="btn btn-danger text-white round">Delete</button>

                    </div>
                </div>

            </div>

        </div>

    </form>
    <?php
    include 'sys/sidebar.php'
        ?>

    <div id="main">
        <?php
        include 'sys/admin_navbar.php'
            ?>

        <div class="container ">


            <form method="post" action="">
                <div class=" mb-5">

                    <div class="row ps-lg-5 ps-md-3 pe-lg-5 pe-md-3">
                        <?php
                        //  echo $status; 
                        
                        if (isset($_SESSION['status'])) {
                            $status = $_SESSION['status'];

                            echo $status;
                            unset($_SESSION['status']);
                        }
                        ?>
                        <div class="col-12 p-sm-4  bg-white round_md">
                            <div class="row">

                                <div class="col-sm-8">
                                    <h3 class="text-capitalize">
                                        <?= $_GET['page'] ?>
                                    </h3>
                                </div>
                                <div class="col-sm-4 ">
                                    <a href="train.php?page=models">
                                        <button type="button"
                                            class="btn float-end btn-primary ps-3  pe-3  border-0 round">Train
                                            Model</button>
                                    </a>
                                </div>
                            </div>

                            <div class="mt-4   round_md">
                                <table class="table  ">

                                    <tr>
                                        <!-- <th>ID</th> -->
                                        <th class="col-5">Model</th>
                                        <th class="col-6">Labels</th>
                                        <th class="col-1">Actions</th>
                                        <!-- Add more columns as needed -->
                                    </tr>


                                    <?php

                                    $query = "SELECT mod_id,REPLACE(REPLACE(model_file, '.tflite', ''), '_', ' v') AS model_file, REPLACE(REPLACE(label_file, '.txt', ''), '_', ' v') AS label_file FROM models ORDER BY model_file DESC";
                                    $stmt = $pdo->prepare($query);
                                    $stmt->execute();
                                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($data as $row): ?>
                                        <tr>
                                            <!-- <td>
                                                    <?php echo $row['mod_id']; ?>
                                                </td> -->
                                            <td class="pt-3 pb-3">
                                                <?php echo $row['model_file']; ?>
                                            </td>
                                            <td class="pt-3 pb-3">
                                                <?php echo $row['label_file']; ?>
                                            </td>
                                            <td class="col-2">
                                                <button class="btn bg-primary round text-light" type="button"
                                                    onclick="getID(<?= $row['mod_id']; ?>)"><i
                                                        class="bi bi-trash-fill"></i></button>
                                            </td>
                                            <!-- Add more cells as needed -->
                                        </tr>
                                    <?php endforeach; ?>

                                </table>


                            </div>
                        </div>



                    </div>
                </div>
            </form>
        </div>
        <br><br><br>
        <?php
        include 'sys/footer.php'
            ?>
    </div>

</body>
<script>
    function getID(id) {
        // alert(id);
        document.getElementById('model-id').value = id;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'), {
            keyboard: false
        });
        deleteModal.show();
    }

</script>

</html>