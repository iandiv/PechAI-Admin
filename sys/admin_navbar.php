<nav class="navbar navbar-expand-md navbar-light fw-bold sticky-top  white-f5   " id="nav">
    <div class="container-fluid">
        <div class="d-flex w-100 align-items-center ">
            <button class="btn   " id="opnbtn" type="button" onclick="toggleNav()"><i class="bi h5 bi-list"></i></button>

            <a href="#" class="navbar-brand ms-3 ">
                <div class="d-flex align-items-end">

                    <h2> <span style="color:#176e43;"><b>Pech</span><span style="color:#26a83d;">AI</span> </b></h2>
                    <img width="40" height="40" style="margin-bottom:5px;margin-left:0px;transform:rotate(35deg)" src="img/pechai-nobg.png" alt="">
                </div>

            </a>

            <?php
            if (!empty($adminDetails->ausername)) {
                ?>

                            <div class="dropdown w-100 "> 
                        
                                <button class="btn  round float-end ps-2  p-1"type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <small> <?= $adminDetails->ausername ?></small>  
                              <img class="rounded-circle border ms-2 p-2" src="./img/user.png" alt="" style="width:30px">
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end round" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item small" href="logout.php">Logout</a></li>
                                </ul>
                                
                            </div>
                    
        <?php    }
            ?>
<?php
        if (empty($adminDetails->ausername)) {
             ?>

            <div class="dropdown  w-100 d-flex justify-content-end p-2 ps-3 pe-4">
                <!-- 
                <button class="btn text-secondary bg-light border float-end ms-2  p-1   align-items-center d-flex toggle" type="button"
                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="border-radius:100px">
                
                        <i class="bi bi-bell-fill  ms-1 me-1   p-1 rounded-circle"></i>

                </button> -->

                <button
                    class="btn text-secondary bg-white border float-end ms-2  p-1 fw-bold rounded-pill align-items-center d-flex toggle"
                    type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" >
                    <small> &nbsp; <?= $adminDetails->ausername ?>
                        <i class="bi  bi-person-fill ms-1 me-1   p-1 rounded-pill"></i>
                    </small>
                </button>

                <ul class="dropdown-menu dropdown-menu-end border round_sm shadow me-2 "
                    aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item small" href="admin_logout.php">Logout</a></li>
                </ul>

            </div>

        <?php }
        ?>

        </div>

    </div>
</nav>