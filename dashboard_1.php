<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_COOKIE['a_id']) && empty($_SESSION['aid'])) {
    header("Location: index.php");
}

include ('class/userClass.php');
$userClass = new userClass();
$adminDetails = $userClass->adminDetails($_COOKIE['a_id']);

$data = [
    'Red' => 500,
    'Blue' => 500,
    'Green' => 500,
    'q' => 500,
    'we' => 500,
    'e' => 500
];
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinycolor/1.4.1/tinycolor.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                                    <!-- <h6 class="text-secondary text-capitalize">Datasets Images:
                                        <?= $rows['total_rows'] ?>
                                    </h6> -->
                                    <?php

                                }
                                ?>

                                <div class="row ">
                                    <div class="col-md-6   ">
                                        <h6 class="text-secondary text-capitalize">Dataset Images:
                                            <?php
                                            $stmt = $pdo->query('SELECT SUM(`rows`) AS total_rows FROM ( SELECT classname AS classname, IFNULL(COUNT(ds_classname), 0) AS `rows` FROM classes LEFT JOIN datasets ON classname = ds_classname GROUP BY classname ) AS subquery;');
                                            while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo $rows['total_rows'];
                                            } ?>
                                        </h6>
                                        <div id="pieChartContainer" class="ps-4 pe-4 pt-1 pb-1 mt-2 mb-2 ">

                                            <canvas id="pieChart"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-6 ">
                                        <h6 class="text-secondary text-capitalize">Contribution Images:
                                            <?php $stmt = $pdo->query('SELECT SUM(`rows`) AS total_rows FROM ( SELECT classname AS classname, IFNULL(COUNT(con_classname), 0) AS `rows` FROM classes LEFT JOIN contributed ON classname = con_classname GROUP BY classname ) AS subquery;');
                                            while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo $rows['total_rows'];
                                            } ?>
                                        </h6>
                                        <div id="pieChartContainer" class="ps-4 pe-4 pt-1 pb-1 mt-2 mb-2">
                                            <canvas id="pieChartCon"></canvas>
                                        </div>
                                    </div>
                                </div>


                                <div class="row collapse">

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

                                                <div class="round p-4 mb-3 text-white border  dashboard-card"
                                                    style="background-color:<?= $shade; ?>">
                                                    <h1>
                                                        <?= $rows['rows']; ?>
                                                    </h1>
                                                    <h6 class="text-capitalize text-truncate pt-2">
                                                        <?= $rows['classname']; ?>
                                                    </h6>
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
                                    <!-- <h6 class="text-secondary  text-capitalize">Contributed Images:
                                        <?= $rows['total_rows'] ?>
                                    </h6> -->
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
                                                <div class="round p-4 mb-3 text-white border  dashboard-card"
                                                    style="background-color:<?= $shade; ?>">
                                                    <h1>
                                                        <?= $rows['rows']; ?>
                                                    </h1>
                                                    <h6 class="text-capitalize text-truncate pt-2">
                                                        <?= $rows['classname']; ?>
                                                    </h6>
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
<script>
// Function to generate shades of a given base color
function generateShades(baseColor) {
    var shades = [];
    var base = tinycolor(baseColor);
    for (var i = 0; i < 5; i++) {
        var shade = base.clone().lighten(i * 5).toHexString();
        shades.push(shade);
    }
    return shades;
}

// Define base colors
var baseColors = [
    '#a4de02',
    '#acdf87',
    '#68bc50',
    '#4c9d57',
    '#4c9a2a',
    '#76ba1b',
    '#176e43'
];

// Generate shades for each base color
var colors = [];
baseColors.forEach(function(baseColor) {
    colors = colors.concat(generateShades(baseColor));
});
    var ctx = document.getElementById('pieChart').getContext('2d');


    var pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_keys($data)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($data)); ?>,
                backgroundColor: colors,
                borderColor: 'rgba(255, 255, 255, 1)',
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: "right",
                    align: "center"
                }
            },
            onClick: function (event, elements) {
                if (elements && elements.length > 0) {
                    var index = elements[0].index;
                    var label = pieChart.data.labels[index];
                    var value = pieChart.data.datasets[0].data[index];
                    console.log("Clicked on slice: " + label + " (" + value + ")");
                }
            }
        }
    });
    var ctx = document.getElementById('pieChartCon').getContext('2d');
    var pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_keys($data)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($data)); ?>,
                backgroundColor: [
                    '#68bc50',
                    '#4c9d57',
                    '#176e43'
                ],
                borderColor: [
                    'rgba(255, 255, 255, 1)',
                    'rgba(255, 255, 255, 1)',
                    'rgba(255, 255, 255, 1)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: "right",
                    align: "center"
                }
            },
            onClick: function (event, elements) {
                if (elements && elements.length > 0) {
                    var index = elements[0].index;
                    var label = pieChart.data.labels[index];
                    var value = pieChart.data.datasets[0].data[index];
                    window.location.href = "contributed-view.php?page=contributed%20images&classname=" + value + "&img=18"
                    console.log("Clicked on slice: " + label + " (" + value + ")");
                }
            }
        }
    });
</script>

</html>