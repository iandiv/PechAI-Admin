<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_COOKIE['a_id']) && empty($_SESSION['aid'])) {
    header("Location: index.php");
}

include ('class/userClass.php');
$userClass = new userClass();
$adminDetails = $userClass->adminDetails($_COOKIE['a_id']);


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

                        <div class="col-12 p-sm-4  bg-white round_md">
                            <h3 class="text-capitalize">
                                <?= $_GET['page'] ?>
                            </h3>

                            <div class="mt-4 ps-sm-3">


                                <!-- DATASET TOTAL -->
                                <?php
                                $stmt = $pdo->query('SELECT SUM(`rows`) AS total_rows FROM ( SELECT classname AS classname, IFNULL(COUNT(ds_classname), 0) AS `rows` FROM classes LEFT JOIN datasets ON classname = ds_classname GROUP BY classname ) AS subquery;');
                                while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                    ?>
                                    <h6 class="text-secondary text-capitalize">Datasets Images:
                                        <?= $rows['total_rows'] ?>
                                    </h6>
                                    <?php

                                }
                                ?>

                                <div class="row ">

                                    <?php
                                    $stmt = $pdo->query('SELECT classname AS classname, IFNULL(COUNT(ds_classname), 0) AS `rows` FROM classes LEFT JOIN datasets ON classname = ds_classname GROUP BY classname;');
                                    $rowCount = $stmt->rowCount();
                                    $shades = ['#176e43', '#4C9D57', '#56b263', '#68bc50'];
                                    $index = 0;
                                    while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $shade = $shades[$index % count($shades)]; // Get the shade from the array
                                    
                                        ?>


                                        <div class=" col-md-4">
                                            <a class="text-decoration-none"
                                                href="datasets-view.php?page=datasets%20images&classname=<?= $rows['classname']; ?>&img=18">

                                                <div class="row align-items-center me-sm-1  round   mb-sm-3 m-1 text-white border  dashboard-card"
                                                    style="background-color:<?= $shade; ?>">

                                                    <div class="col-8 p-4">
                                                        <h1 class="ps-1" style="font-weight:550">
                                                            <?= $rows['rows']; ?>
                                                        </h1>
                                                        <h6 class="ps-1 text-capitalize text-truncate pt-2">
                                                            <?= $rows['classname']; ?>
                                                        </h6>
                                                    </div>
                                                    <div class="col-4 pe-sm-3">
                                                        <?php
                                                        $stmtImg = $pdo->query('SELECT * FROM `datasets` WHERE ds_classname="' . $rows['classname'] . '" ORDER BY ds_id DESC LIMIT 1');
                                                        $rowCountImg = $stmtImg->rowCount();

                                                        if ($rowCountImg > 0) {
                                                            while ($rowsImg = $stmtImg->fetch(PDO::FETCH_ASSOC)) {
                                                                ?>

                                                                <img loading="lazy" decoding="async"
                                                                    id="img-<?php echo $rowsImg['ds_id'] ?>"
                                                                    onclick="getID('<?php echo $rowsImg['ds_id'] ?>')"
                                                                    class="   round  "
                                                                    src="datasets/<?php echo $rows['classname']; ?>/<?php echo $rowsImg["ds_img"] ?>"
                                                                    style="width:100%; height: 105px;object-fit: cover;
  display: block;
  margin: 0 auto">



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
                                            </a>
                                        </div>
                                        <?php
                                        $index++;

                                    }
                                    ?>


                                </div>
                                <br>
                                <!-- CONTRIBUTED TOTAL -->
                                <?php
                                $stmt = $pdo->query('SELECT SUM(`rows`) AS total_rows FROM ( SELECT classname AS classname, IFNULL(COUNT(con_classname), 0) AS `rows` FROM classes LEFT JOIN contributed ON classname = con_classname GROUP BY classname ) AS subquery;');
                                while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    ?>
                                    <h6 class="text-secondary  text-capitalize">Contributed Images:
                                        <?= $rows['total_rows'] ?>
                                    </h6>
                                    <?php
                                }
                                ?>
                                <div class="row ">

                                    <?php
                                    $stmt = $pdo->query('SELECT classname AS classname, IFNULL(COUNT(con_classname), 0) AS `rows` FROM classes LEFT JOIN contributed ON classname = con_classname GROUP BY classname;');
                                    $rowCount = $stmt->rowCount();
                                    $shades = ['#176e43', '#4C9D57', '#56b263', '#68bc50'];
                                    $index = 0;
                                    while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $shade = $shades[$index % count($shades)]; // Get the shade from the array
                                    
                                        ?>


                                        <div class=" col-md-4">
                                            <a class="text-decoration-none"
                                                href="contributed-view.php?page=contributed%20images&classname=<?= $rows['classname']; ?>&img=18">

                                                <div class="row align-items-center me-sm-1  round   mb-sm-3 m-1 text-white border  dashboard-card"
                                                    style="background-color:<?= $shade; ?>">

                                                    <div class="col-8 p-4">
                                                        <h1 class="ps-1" style="font-weight:550">
                                                            <?= $rows['rows']; ?>
                                                        </h1>
                                                        <h6 class="ps-1 text-capitalize text-truncate pt-2">
                                                            <?= $rows['classname']; ?>
                                                        </h6>
                                                    </div>
                                                    <div class="col-4 pe-sm-3">
                                                        <?php
                                                        $stmtImg = $pdo->query('SELECT * FROM `contributed` WHERE con_classname="' . $rows['classname'] . '" ORDER BY con_id DESC LIMIT 1');
                                                        $rowCountImg = $stmtImg->rowCount();

                                                        if ($rowCountImg > 0) {
                                                            while ($rowsImg = $stmtImg->fetch(PDO::FETCH_ASSOC)) {
                                                                ?>

                                                                <img loading="lazy" decoding="async"
                                                                    id="img-<?php echo $rowsImg['con_id'] ?>"
                                                                    onclick="getID('<?php echo $rowsImg['con_id'] ?>')"
                                                                    class="   round  "
                                                                    src="uploads/<?php echo $rowsImg["con_img"] ?>" style="width:100%; height: 105px;object-fit: cover;
  display: block;
  margin: 0 auto">



                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <div class="text-center  bg-dark round" style="opacity:20%;width:100%; height: 105px;">
                                                                <!-- <h6>No images found!</h6> -->
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <?php
                                        $index++;
                                    }
                                    ?>

                                </div>
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

</html>