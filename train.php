<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_COOKIE['a_id']) && empty($_SESSION['aid'])) {
    header("Location: index.php");
}

include ('class/userClass.php');
$userClass = new userClass();
$adminDetails = $userClass->adminDetails($_COOKIE['a_id']);


$aid = $_SESSION['aid'];

// Prepare the SQL query
$query = $pdo->prepare("SELECT ausername, apassword FROM `admin` WHERE aid=:aid");;
$query->bindParam(":aid", $aid);
$query->execute();

// Fetch the result
$result = $query->fetch(PDO::FETCH_ASSOC);

// $ausername = $result['ausername'];
// $apassword = $result['apassword'];

$queryToken = $pdo->prepare("SELECT * FROM `apikey` WHERE 1");
$queryToken->execute();

// Fetch the result
$resultToken = $queryToken->fetch(PDO::FETCH_ASSOC);

$token = $resultToken['apikey'];

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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

<body class="white-f5 overflow-hidden " >

    <?php
    include 'sys/sidebar.php'
        ?>


    <div id="main" class="overflow-hidden">
        <?php include 'sys/admin_navbar.php'; ?>
        <div id="spinner" class="justify-content-center align-items-center" style="height:90vh;display: none;">

            <div class="spinner-grow" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <iframe id="myFrame" class=" w-100" style="height: 90vh; display: block;"
            src="http://127.0.0.1:5000/?embed=true&username=admin&password=admin&token=<?=$token?>"
            frameborder="0"></iframe>
    </div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // After 1000 milliseconds (1 second), hide the spinner and show the iframe
        setTimeout(function () {
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('myFrame').style.display = 'block';
        }, 1500); // 1000 milliseconds = 1 second
    });
</script>


</html>