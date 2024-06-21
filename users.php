<?php
include 'sys/ini.php';
$pdo = pdo_init();
if (empty($_SESSION['aid'])) {
    header("Location: index.php");
    
}else if($_SESSION['arole']=="admin"){
    header("Location: dashboard.php");
    
}
if (basename($_SERVER['PHP_SELF']) === 'users.php' && empty($_GET['page'])) {
    header('Location: users.php?page=users');
    exit();
}

include ('class/userClass.php');
$userClass = new userClass();
if (!empty($_SESSION['aid'])) {
    $adminDetails = $userClass->adminDetails($_SESSION['aid']);
}


if (isset($_POST['editUser'])) {
    $id = $_POST['edtId'];
    $username = $_POST['edtUsername'];
    $password = $_POST['edtPassword'];

 
    // Prepare the SQL update statement
    $update = $pdo->prepare("UPDATE `admin` SET `ausername` = :username, `apassword` = :password WHERE `aid` = :id");
    $update->bindParam(":username", $username);
    $update->bindParam(":password", $password);

    $update->bindParam(":id", $id, PDO::PARAM_INT);

    // Execute the update statement
    $ok = $update->execute();

    if ($ok) {
        $_SESSION['status'] = '<div class="successMsg border round">Updated Successfully!&nbsp;<i class="bi bi-check-lg"></i></div>';
    } else {
        $_SESSION['status'] = '<div class="errorMsg border round">Update Failed!&nbsp;<i class="bi bi-check-lg"></i></div>';
    }
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['addUser'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'admin';

    // Prepare the SQL statement
    $add = $pdo->prepare("INSERT INTO `admin`(`ausername`, `apassword`, `arole`) VALUES (:username, :password, :role)");
    
    // Bind parameters
    $add->bindParam(":username", $username);
    $add->bindParam(":password", $password);
    $add->bindParam(":role", $role);

    // Execute the statement
    $ok = $add->execute();

    if ($ok) {
        // Set the success message in the session
        $_SESSION['status'] = '<div class="successMsg border round">Added Successfully!&nbsp;<i class="bi bi-check-lg"></i></div>';

    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
if (isset($_POST['delUser'])) {
    if (isset($_POST['delCheck']) && is_array($_POST['delCheck']) && count($_POST['delCheck']) > 0) {
        // If there are selected checkboxes
        foreach ($_POST['delCheck'] as $user_id) {
            // Check the role of the user
            $roleCheckStatement = $pdo->prepare('SELECT arole FROM `admin` WHERE aid = :user_id');
            $roleCheckStatement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $roleCheckStatement->execute();
            $userRole = $roleCheckStatement->fetchColumn();

            if ($userRole === 'superadmin') {
                $_SESSION['status'] = '<div class="errorMsg border round">Cannot delete superadmin user!&nbsp;<i class="bi bi-check-lg"></i></div>';

                continue; 
            }

        
            $deleteStatement = $pdo->prepare('DELETE FROM `admin` WHERE aid = :user_id');
            $deleteStatement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $ok = $deleteStatement->execute();

            if ($ok) {
                $_SESSION['status'] = '<div class="successMsg border round">Employee deleted Successfully!&nbsp;<i class="bi bi-check-lg"></i></div>';

            }
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['status'] = '<div class="errorMsg border round">No user selected!&nbsp;<i class="bi bi-check-lg"></i></div>';


        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
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
        /* .dashboard-card:hover {


            opacity: 90%;
        }

        .dashboard-card {
            border-left: solid;
            border-color: var(--bg-primary);
            border-width: 5px;
        } */

        /* select::before {
            content: "";
            float: left;
            height: 21px;
            margin-top: 0px;
            margin-left: -11px;
            margin-right: 6px;
            border-left: 4px solid var(--bg-primary);
            border-radius: 6px;
            transition: background-color 0.5s ease;

        } */





        /* Base class for the notification card */
        .notif-card {
            position: relative;
            padding: 10px;
            background-color: #f3f3f3;

            color: inherit;
        }

        /* Base class for the colored border-left */
        .notif-card::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 2px;
            margin-top: 15px;
            margin-bottom: 15px;

            width: 4px;
            border-left: 5px solid transparent;
            /* Default to transparent */
            border-radius: 5px;
            transition: border-color 0.5s ease;
            /* Corrected property */
        }

        .notif-card-error {
            color: #dc3545;
        }

        /* Specific classes for different border colors */
        .notif-card-error::before {
            border-left-color: #dc3545;

        }

        .notif-card-success {
            color: #28a745;
        }

        .notif-card-success::before {
            border-left-color: #28a745;
            /* Green */
        }
    </style>

</head>

<body class="bg-nav ">

    <?php
    include 'sys/sidebar.php'
        ?>
    <?php
    include 'modal/users.php'
        ?>


    <div id="main" class="">
        <!-- <div id="main" class=""> THEME 2 -->
        <?php
    include 'sys/admin_navbar.php'
        ?>
        <div class="container"> 
            <div class="row  mb-0 align-items-center ps-lg-5 ps-md-3 pe-lg-5 pe-md-3">
                <div class="col-sm-9  ">
                    <div class="row  pt-sm-4 ps-sm-4 pb-sm-4  align-items-center">
                        <div class="col-6">
                            <h3 class="pt-2 pb-2 m-0 p-0 text-capitalize">
                                <?= $_GET['page'] ?>
                            </h3>
                            <?php


                            $stmt = $pdo->query('SELECT COUNT(*) AS total_rows
    FROM admin');
                            while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>

                                <p class="small  text-secondary " style="margin-top:-10px"><?= $rows['total_rows']; ?>
                                    &nbsp;items</p>


                            <?php } ?>

                        </div>
                        <div class="col-6 d-flex justify-content-end align-items-center">
                            <?php
                            if ($_SESSION['arole'] != "admin") { ?>
                                <button data-bs-toggle="modal" data-bs-target="#addModal"
                                    class="btn round text-secondary bg-light border   p-1   align-items-center d-flex ">
                                    <i class="bi bi-plus-lg  ms-1 me-1   p-1 "></i>
                                </button>
                            <?php } ?>
                            
                        </div>
                    </div>
                </div>


                <div class="col-sm-3">
                    <div class="input-group  round">

                        <input type="text" onkeyup="onSearch()"
                            class="bg-light fw-normal fs-6 form-control border round" id="searchBox"
                            placeholder="username...">
                        <span class="input-group-text  round" onclick="onSearch()">
                            <i class="bi bi-search"></i>
                        </span>

                    </div>
                </div>

            </div>

        </div>
        <div class="container-fluid overflow-auto  round-top-left  ">
            <div class="container ">
                <?php
                //  echo $status; 
                
                if (isset($_SESSION['status'])) {
                    $status = $_SESSION['status'];

                    echo $status . "    <small>&nbsp;</small>";
                    unset($_SESSION['status']);
                }

                ?>
                <form method="post" action="">

                    <!--DELETE Modal -->
                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered ">

                            <div class="modal-content  round_sm border ">
                                <div class="modal-header  border-0">
                                    <h1 class="modal-title fs-5 w-100" id="exampleModalLabel">Delete</h1>
                                    <button class="btn rounded-circle" type="button" data-bs-dismiss="modal"><i
                                            class="bi bi-x-lg"></i></button>


                                </div>
                                <div class="modal-body text-center">
                                    <input type="text" id="deleteID" hidden>
                                    Do you want to <span class="text-danger fw-bold"> delete </span> the selected
                                    user?
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn  fw-bold text-secondary"
                                        data-bs-dismiss="modal">No</button>
                                    <button type="submit" name="delUser"
                                        class="btn  fw-bold text-danger">Yes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" mb-5">

                        <div class="row  ps-sm-4 pe-sm-4">

                            <div class="col-12 p-sm-4 ">

                                <form method="POST">
                                    <div class="row  ">
                                        <div class="col-12 " id="tableList">
                                            <div class="mt-2  overflow-auto " style="max-height:70vh">
                                                <table class="table table-hover  table-borderless round_sm" id="table">
                                                    <?php


                                                    // Execute the query
                                                    $stmt = $pdo->query('SELECT * FROM `admin`' );

                                                    if ($stmt->rowCount() > 0) { ?>
                                                        <thead class="sticky-top bg-white shadow-sm">

                                                            <th hidden>ID</th>
                                                            <th style="width:10px">&nbsp;</th>

                                                        


                                                            <th class="col-4 " >Username</th>
                                                            <th class="col-4 ">Password</th>
                                                        
                                                            <th class="col-4 ">Role</th>
                                                            <?php 
                                                            if ($_SESSION['arole'] != "employee") { ?>
                                                            <th class="col-4 ">Edit</th>
<?php }?>
                                                        </thead>
                                                        <tbody class="overflow-auto">
                                                            <?php
                                                            while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                                                <tr class="border-bottom ">
                                                                    <td id="id" hidden><?php echo $rows['aid']; ?></td>

                                                                    <td class="pt-3 pb-3">

                                                                        
                                                                        <input class="form-check-input h4 p-0 m-0 border round" type="checkbox"
                                                                            name="delCheck[]" id="check<?= $rows['aid']; ?>"
                                                                            value="<?= $rows['aid']; ?>" <?php echo ($rows['arole']=='superadmin')?'hidden':'' ?>>
                                                                    </td>
                                                                    
                                                                    <td class="pt-3 pb-3"id="brand"><?php echo $rows['ausername']; ?></td>
                                                                    <td class="pt-3 pb-3" id="name"> <?php echo $rows['apassword']; ?></td>
                                                                    <td class="pt-3 pb-3" id="quantity"><?php echo $rows['arole']; ?></td>
                                                                
                                                                    <?php
                                                                    if ($_SESSION['arole'] != "admin") { ?>
                                                                        <td>
                                                                            <button
                                                                                class="btn p-2 round  bg-primary border  text-white ps-2 pe-2"
                                                                                type="button" id="editBtn"><i
                                                                                    class="bi small bi-pencil-fill ms-1 me-1"></i></button>

                                                                        </td>
                                                                    <?php } ?>
                                                                </tr>
                                                            </tbody>
                                                        <?php }
                                                    } else { ?>

                                                        <div class="text-center p-4">
                                                            No products found!
                                                        </div>
                                                    <?php }
                                                    ?>

                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                    <?php


                                    // Execute the query
                                    $stmt = $pdo->query('SELECT * FROM `admin`');

                                    if ($stmt->rowCount() > 0) { ?>
                                        <div class="  round_sm bg-light ">
                                            <?php
                                            if ($_SESSION['arole'] != "admin") { ?>
                                                <button type="button"
                                                    class="float-start mt-2 bg-light round_sm border btn text-danger fw-bold"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                    <i class="bi bi-trash-fill me-3"></i>Delete&emsp;
                                                </button>
                                            <?php } ?>
                                            
                                        </div>
                                    <?php } ?>
                                </form>



                            </div>
                        </div>
                </form>
            </div>
        </div>
        <br><br><br>
        <?php
        // include 'sys/footer.php'
        ?>
    </div>
    <script>




        // Initialize a global object to hold references to table rows
        const checkboxReferences = {};

        // Initialize the total at a higher scope
        let total = 0;
        const result = [];
        // Event handler for row clicks
        function onRowClick(event) {
            // Ensure the event and the clicked row are valid
            if (!event || !event.currentTarget) return;

            const row = event.currentTarget; // The clicked row
            const itemId = row.querySelectorAll('td')[0].textContent.trim(); // Extract itemId from the first cell
            const cb = document.getElementById("check" + itemId); // Corresponding checkbox
                console.log(itemId)

            if (!cb ) return;
            
            const userName = row.querySelectorAll('td')[2].innerText;

            const userPassword = row.querySelectorAll('td')[3].innerText;
            const userRole = row.querySelectorAll('td')[4].innerText; // Extract item name

            console.log(userPassword)
            console.log(userRole)
            if(userRole!='superadmin'){

            if (cb.checked) {
                
                cb.checked = false;
                

            } else {

                cb.checked = true;

                
            }

        }
        


        }

        function allId() {
            const table = document.getElementById("selectedItemTable");

            const tbody = table.getElementsByTagName("tbody")[0];

            // Get all rows in the tbody
            const rows = tbody.getElementsByTagName("tr");

            const result = new Set(); // Using a Set to avoid duplicates

            for (let i = 0; i < rows.length; i++) {
                // Get the ID from the first column
                const idCell = rows[i].getElementsByTagName("td")[0];
                const id = idCell.textContent.trim();

                // Construct the ID of the input field
                const inputId = "item" + id;

                const quant = document.getElementById(inputId);
                if (quant) {
                    const entry = `[${id},${quant.innerText}]`;

                    result.add(entry);

                    console.log("ID: " + id);
                    console.log("Quantity: " + quant.innerText);
                } else {
                    console.warn(`Input field with ID ${inputId} not found.`);
                }
            }

            // Convert Set to array and join with commas
            const resultArray = Array.from(result);
            console.log(resultArray.join(','));
            const dataString = resultArray.join(',');

            // Construct the URL with the data parameter
            const url = `purchase.php?data=[${encodeURIComponent(dataString)}]`;

            // Redirect to the purchase.php page
            window.location.href = url;
        }

        function sumColumn() {
            // Get the table
            const table = document.getElementById("selectedItemTable");

            const tbody = table.getElementsByTagName("tbody")[0];

            // Get all rows in the tbody
            const rows = tbody.getElementsByTagName("tr");

            // Initialize sum
            let sum = 0;

            // Iterate over the rows
            for (let i = 0; i < rows.length; i++) {
                // Get the cell from the third column (index 2)
                const priceCell = rows[i].getElementsByTagName("td")[3];

                // Remove non-numeric characters and convert to a floating-point number
                const cleanText = priceCell.textContent.replace(/[^\d.-]/g, ''); // Remove currency symbol, etc.
                const value = parseFloat(cleanText);

                if (!isNaN(value)) {
                    sum += value;
                }
            }

            // Display the sum in console
            totalSelectedItem.textContent = sum.toFixed(2);
            console.log("Total Sum: ₱" + sum.toFixed(2));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tableRows = document.querySelectorAll('table tr'); // Select all rows in the table body
            tableRows.forEach(row => {
                row.addEventListener('click', onRowClick); // Attach the click event listener
            });
        });

        function onSearch() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchBox"); 
            filter = input.value.toUpperCase();
            table = document.getElementById("table");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[2];
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
        const viewBtns = document.querySelectorAll('#delete-btn');
        viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {

                const row = btn.parentNode.parentNode;

                const id = row.querySelector('td:first-child').textContent;

                // alert(id);
                $("#deleteModal").modal("show");
                document.getElementById("deleteID").value = id;

            });
        });

        const editBtns = document.querySelectorAll('#editBtn');
        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                console.log("TEST");
                const row = btn.parentNode.parentNode;
                const itemId = row.querySelectorAll('td')[0].textContent.trim(); // Extract itemId from the first cell
                const cb = document.getElementById("check" + itemId); // Corresponding checkbox

                const id = row.querySelector('td:first-child').innerText;
                const name = row.querySelector('td:nth-child(3)').innerText;
                const password = row.querySelector('td:nth-child(4)').innerText;

                if (cb.checked) {
                    cb.checked = false;
                } else {
                    cb.checked = true;

                }
                // alert(id);

                document.getElementById("editID").value = id;
                document.getElementById("editBrand").value = name;
                document.getElementById("editName").value = password;
            
                $("#editModal").modal("show");

            });
        });
    </script>
</body>

</html>