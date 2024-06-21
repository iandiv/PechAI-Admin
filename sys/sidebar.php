<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<link rel="stylesheet" href="sidebar-style.css">
<div id="mySidebar" class="sidebar  ">
    <div id="sidebarlogo" class=" d-flex  align-items-center ps-2 pt-2 ">
        <button class="btn ms-1 me-2  text-light" type="button" onclick="toggleNav()"><i
                class="bi h5 bi-x-lg"></i></button>

    </div>

    <div class="">
        <a href="dashboard.php?page=dashboard" class="sidebar-item"><i class="bi bi-grid"></i>&emsp;Dashboard</a>
        <?php
        $query = $pdo->prepare("SELECT * FROM `classes` WHERE 1");
        $query->execute();
        $list = $query->fetchAll(PDO::FETCH_OBJ);

        if (!empty($list)) {
            $firstClassName = $list[0]->classname;


            ?>
            <a href="disease.php?disease=<?= $firstClassName ?>&page=pest and diseases"
                class="text-truncate sidebar-item"><i class="bi bi-justify-left"></i>&emsp;Pest and Diseases</a>

        <?php } ?>
        <a href="model.php?page=models" class="sidebar-item mb-2"><i class="bi bi-cpu"></i>&emsp;Models</a>

        <span class="   ms-3 text-opacity-75" style="font-size:9pt">Images</span>



        <a href="datasets.php?page=datasets images" class="text-truncate  sidebar-item"><i
                class="bi bi-images"></i>&emsp;Datasets </a>
        <a href="contributed.php?page=contributed images" class="text-truncate  sidebar-item"><i
                class="bi bi-images"></i>&emsp;Contributed </a>
        <!-- <div class="bg-darkGreen p-1 round ms-3 me-3 mb-2">
        </div> -->
        <span class="   ms-3 text-opacity-75" style="font-size:9pt">Other</span>
        <!-- <a href="sample.php?page=sample&classname=tet&img=18" class="text-truncate m-0 sidebar-item"><i class="bi bi-images"></i>&emsp;Sample </a> -->

        <a href="api-gen.php?page=api key" class="sidebar-item"><i class="bi bi-key"></i>&emsp;Api Key</a>

        <?php
        if ($_SESSION['arole'] != "admin") { ?>
            <a href="users.php?page=users" class="sidebar-item"><i class="me-3  bi bi-person-fill"></i><span
                    class="sidebar-item-text collapse show">Users</span></a>
        <?php } ?>
    </div>
</div>

<script>

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.body.classList.add('show');
        }, 2000);
        if (window.innerWidth >= 867 || window.innerWidth >= 1090) {

            if (localStorage.getItem('isClosed') === 'true') {
                closeNav();
            } else {
                openNav();
            }
        } else {
            closeNav();
        }


    });
    function getUrlParameter(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }


    const currentPage = getUrlParameter('page');

    const sidebarItems = document.querySelectorAll('.sidebar-item');

    sidebarItems.forEach(item => {
        const itemPage = item.getAttribute('href').split('page=')[1];
        if (itemPage === currentPage) {
            item.classList.add('selected');
        }
    });

    function handleScroll() {
        var navbar = document.getElementById("nav");
        var sidebar = document.getElementById("mySidebar");

        if (window.scrollY > 0) {
            navbar.classList.remove("border-bottom");

        } else {

            navbar.classList.remove("border-bottom");
        }
    }


    window.addEventListener("scroll", handleScroll);



    function openNav() {
        localStorage.setItem('isClosed', false);
        var opnbtn = document.getElementById("opnbtn");

        var sidebar = document.getElementById("mySidebar");
        var main = document.getElementById("main");
        var sidebarlogo = document.getElementById("sidebarlogo");
        // var nav = document.getElementById("nav");



        if (window.innerWidth >= 1090) {
            opnbtn.style.display = "none"
            sidebar.style.width = "250px";
            sidebar.style.marginLeft = "0px";
            main.style.marginLeft = "250px";
            // nav.style.marginLeft = "250px";

            sidebar.classList.add('shadow');
            // sidebar.classList.add('border-top');




            sidebar.style.zIndex = "1";
        } else if (window.innerWidth >= 867) {
            sidebar.style.width = "200px";
            sidebar.style.marginLeft = "0px";
            opnbtn.style.display = "none"
            main.style.marginLeft = "200px";
            // nav.style.marginLeft = "200px";

            sidebar.classList.add('shadow');
            // sidebar.classList.add('border-top');





            sidebar.style.zIndex = "1";
        } else {
            sidebar.style.marginLeft = "0px";
            sidebar.style.width = "250px";
            opnbtn.style.display = "block"

            sidebar.style.marginTop = "0px";
            main.style.marginLeft = "0";
            // nav.style.marginLeft = "0";

            sidebar.classList.add('shadow');
            // sidebar.classList.remove('border-top');


            sidebar.style.zIndex = "4000";
        }

    }
    largeScreen = false;

    function closeNav() {
        localStorage.setItem('isClosed', true);
        var opnbtn = document.getElementById("opnbtn");
        var sidebar = document.getElementById("mySidebar");
        var main = document.getElementById("main");
        sidebar.classList.remove('shadow');


        sidebar.style.marginLeft = "-250px";
        main.style.marginLeft = "0";

        opnbtn.style.display = "block"

    }

    function toggleNav() {

        var sidebar = document.getElementById("mySidebar");
        if (window.innerWidth >= 867) {


            if (sidebar.style.marginLeft === "-250px") {
                if (window.innerWidth >= 867) {
                    largeScreen = false;
                }
                openNav();

            } else {

                closeNav();
                if (window.innerWidth >= 867) {
                    largeScreen = true;
                }
            }
        } else {
            if (sidebar.style.marginLeft === "0px") {
                closeNav();
                if (window.innerWidth >= 867) {
                    largeScreen = true;
                }
            } else {

                if (window.innerWidth >= 867) {
                    largeScreen = false;
                }
                openNav();

            }
        }
    }








    // Automatically open the sidebar on larger devices (width >= 768px)

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 867) {
            if (!largeScreen) {
                openNav();
            }


        } else if (window.innerWidth >= 1090) {
            if (!largeScreen) {
                openNav();
            }


        } else {

            closeNav();


        }
    });



</script>