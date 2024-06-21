<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_COOKIE['a_id']) && empty($_SESSION['aid'])) {
    header("Location: index.php");
}

include('class/userClass.php');
$userClass = new userClass();
$adminDetails = $userClass->adminDetails($_COOKIE['a_id']);




if (isset($_POST['save'])) {

    $stmt = $pdo->query('SELECT * FROM `apikey`');
    // $query = $pdo->prepare("SELECT * FROM `books` WHERE 1");
    $rowCount = $stmt->rowCount();

    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    $oldapi = $rows['apikey'];
    
    $apikey = $_POST['apikey'];


    $add = $pdo->prepare("UPDATE `apikey` SET `apikey`=:apikey WHERE `apikey`=:oldapi");
    $add->bindParam(":apikey", $apikey);
    $add->bindParam(":oldapi", $oldapi);


    $ok = $add->execute();
    if ($ok) {

        $_SESSION['status'] = '<div class="successMsg border  round">Saved Successfully!&nbsp;<i class="bi bi-check-lg"></i></div><br>';
        // header("Location: inventory.php?page=inventory&disease=$diseaseName");
        echo '<script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
        </script>';
    } else {
        $_SESSION['status'] = '<div class="errorMsg border  round">Failed to Add!&nbsp;<i class="bi bi-x-lg"></i></div><br>';
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0">

    <title><?= ucwords($_GET['page']) ?> | PechAI</title>
    <link rel=" icon" href="img/pechai-nobg.png">
    <!-- CDN -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

        }


        /* Define the loading pulse animation */
    </style>
    <link rel="stylesheet" href="style.css">
    <!-- <script src="javascript/javascript.js"></script> -->

</head>

<body class="white-f5" >

    <?php
    include 'sys/sidebar.php'
    ?>


    <div id="main" >
        <?php
        include 'sys/admin_navbar.php'
        ?>

        <div class="container " >



            <div class=" mb-5">

                <div class="row ps-lg-5 ps-md-3 pe-lg-5 pe-md-3">
                    <?php
                    //  echo $status; 

                    if (isset($_SESSION['status'])) {
                        $status = $_SESSION['status'];

                        echo  $status;
                        unset($_SESSION['status']);
                    }
                    ?>
                    <div class="col-12 p-sm-4  bg-white round_md">
                        <h3 class="text-capitalize"><?= $_GET['page'] ?></h3>

                        <form action="" method="POST">
                            <div class="col-md-8 col-lg-8  p-sm-4 ">
                                <div class="">
                                    <div class="input-group">
                                        <?php
                                        $stmt = $pdo->query('SELECT * FROM `apikey`');
                                        // $query = $pdo->prepare("SELECT * FROM `books` WHERE 1");
                                        $rowCount = $stmt->rowCount();



                                        if ($rowCount > 0) {
                                            while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                                <input type="text" id="apikey" name="apikey" class="border  ps-3 round form-control" value="<?= $rows['apikey'] ?>">

                                        <?php }
                                        }
                                        ?>
                                        <button onclick="ApiKey()" type="button" class="btn text-white btn-primary border-0 round pe-4 ps-4 pt-3 pb-3"><i class="bi fs-5 bi-arrow-clockwise"></i></button>
                                    </div> <br>
                                    <!-- <button class="btn text-white bg-primary ">Generate</button> -->
                                    <button type="submit" name="save" class="btn  round text-white bg-primary ">Save</button>
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
    function ApiKey() {
        var apikey = document.getElementById('apikey');
        const apiKey = generateRandomApiKey(32); // You can specify the desired length
        apikey.value = apiKey;
    }

    function generateRandomApiKey(length) {
        const charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        let apiKey = "";

        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            apiKey += charset.charAt(randomIndex);
        }

        return apiKey;
    }
</script>

</html>