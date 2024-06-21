<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_COOKIE['a_id']) && empty($_SESSION['aid'])) {
    header("Location: index.php");
}

include('class/userClass.php');
$userClass = new userClass();
$adminDetails = $userClass->adminDetails($_COOKIE['a_id']);


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


    <style>
        img:hover {
            background-color: #f5f5f5;
            border-color: #176e43;
            border-width: 2px;
        }

        .datasets-name:hover {
            background-color: #f5f5f5;
            border-color: #176e43 !important;
            border-width: 2px !important;

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
        <div style="z-index: 2000;" class="modal fade " id="viewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content round_md border-0 shadow-lg">
                    <div class="modal-header border-0 ">
                        <h5 class="modal-title" id="viewModalLabel">Delete </h5>
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="modal-body mb-2 mt-1 ps-sm-5 pe-sm-5">

                        <input type="hidden" class="form-control" id="img-id">
                        <div class="row">
                            <div class="col-sm-6">
                                <img class="round" alt="" width="100%" src="img/pechai.png" id="viewImage">
                                <br>
                            </div>
                            <div class="col-sm-6">

                                <h3 id="classname"></h3>

                                <p id="desc"></p>
                            </div>

                        </div>
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

        <div class="container " style="height:100vh">



            <div class=" mb-5">

                <div class="row ps-lg-5 ps-md-3 pe-lg-5 pe-md-3">

                    <div class="col-md-12 col-lg-12 bg-white round_md p-sm-4 ">

                        <table class="table  " id="diseaseTable">

                            <div class="row">

                                <div class="col-sm-8">
                                    <h3 class="text-capitalize"><?= $_GET['page'] ?></h3>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group ">

                                        <input type="text" onkeyup="onDiseaseSearch()" class="bg-white form-control border round" id="diseaseBox" placeholder="Search...">
                                        <span class="input-group-text  round" onclick="onDiseaseSearch()">
                                            <i class="bi bi-search"></i>
                                        </span>

                                        <!-- <button type="button" style="margin-top:-10px" class=" pb-1 pt-1 ps-4 pe-4 border-0 text-white btn-danger  round" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash"></i>
</button>
<button type="button" style="margin-top:-10px" class="  pb-1 pt-1 ps-4 pe-4 border-0 text-white btn-primary  round" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i></button>
-->

                                    </div>
                                </div>
                            </div>
                                <!-- <input type="text" onkeyup="onDiseaseSearch()" class="form-control border round" id="diseaseBox" placeholder="Search disease..."> -->

                                <hr>


                                <tbody>
                                <?php
                                $query = $pdo->prepare("SELECT * FROM classes WHERE classname IN ('Healthy', 'Invalid') ORDER BY classname ASC;");
                                $query->execute(array());
                                $list = $query->fetchAll(PDO::FETCH_OBJ);
                                foreach ($list as $bd) {
                                    ?>

                                    <tr>

                                        <td class="text-capitalize  round border-bottom "
                                            onclick="goTo('<?php echo $bd->classname; ?>');">
                                            <h6 class="pt-2"> <i class="bi bi-hdd-fill"></i>  &nbsp;
                                                <?php echo $bd->classname ?>
                                            </h6>
                                        </td>

                                        <!-- <td class="round" onclick="highlightRow(this)"><?php echo $bd->classname ?></td> -->
                                    </tr>
                                    
                                    <?php
                                }
                                ?>
                            
                                <?php
                                $query = $pdo->prepare("SELECT * FROM classes WHERE classname NOT IN ('Healthy', 'Invalid') ORDER BY classname ASC;");
                                $query->execute(array());
                                $list = $query->fetchAll(PDO::FETCH_OBJ);
                                foreach ($list as $bd) {
                                    ?>

                                    <tr>

                                        <td class="text-capitalize round border-bottom"
                                            onclick="goTo('<?php echo $bd->classname; ?>');">
                                            <h6 class="pt-2">
                                                <?php echo $bd->classname ?>
                                            </h6>
                                        </td>

                                        <!-- <td class="round" onclick="highlightRow(this)"><?php echo $bd->classname ?></td> -->
                                    </tr>

                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>


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
    function onDiseaseSearch() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("diseaseBox");
        filter = input.value.toUpperCase();
        table = document.getElementById("diseaseTable");
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

    function goTo(value) {
        var currentURL = window.location.href.split("?")[0];
        var newURL = "contributed-view.php?page=contributed images&classname=" + encodeURIComponent(value)+"&img=18";
        window.location.href = newURL;
    }

    function getID(id) {
        // alert(id);
        document.getElementById('img-id').value = id;
        var viewModal = new bootstrap.Modal(document.getElementById('viewModal'), {
            keyboard: false
        });
        viewModal.show();

        var sourceImage = document.getElementById("img-" + id);
        var imgClassname = document.getElementById("classname-" + id);
        var imgDesc = document.getElementById("desc-" + id);

        var targetImage = document.getElementById("viewImage");
        var viewModalLabel = document.getElementById("viewModalLabel");
        var classname = document.getElementById("classname");
        var desc = document.getElementById("desc");

        classname.textContent = imgClassname.value;
        desc.textContent = imgDesc.value;


        targetImage.src = sourceImage.src;
        var parts = sourceImage.src.split('/');
        var fileName = parts[parts.length - 1];
        viewModalLabel.textContent = fileName;

    }
</script>

</html>