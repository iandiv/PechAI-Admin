<?php include 'sys/ini.php';


if (!empty($_COOKIE['a_id']) && !empty($_SESSION['aid'])) {

    // echo "<script>alert('true');</script>";
    header("Location: dashboard.php?page=dashboard");

    exit;
} else {
    // echo "<script>alert('false');</script>" ;
}
//&& !empty($_SESSION['aid'])
include('class/userClass.php');
$userClass = new userClass();

require_once 'googleLib/GoogleAuthenticator.php';
$ga = new GoogleAuthenticator();
$secret = $ga->createSecret();

$errorMsgReg = '';
$errorMsgLogin = '';
if (!empty($_POST['loginSubmit'])) {
    $usernameEmail = $_POST['usernameEmail'];
    $password = $_POST['password'];
    if (strlen(trim($usernameEmail)) > 1 && strlen(trim($password)) > 1) {
        $aid = $userClass->adminLogin($usernameEmail, $password, $secret);
        if ($aid) {


            header("Location: dashboard.php?page=dashboard");
            exit;
        } else {
            $errorMsgLogin = '<div class="errorMsg  round">Please check login details.</div>';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0">

    <title>Log In | PechAI</title>

    <link rel=" icon" href="img/pechai-nobg.png">

    <!-- CDN -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />



    <link rel="stylesheet" href="style.css">
    <script src="javascript/javascript.js"></script>

    <script>
        set = false;

        function onNav() {
            if (set === false) {
                var element = document.getElementById('nav');
                element.classList.add("mybg");
                set = true;
            } else {
                var element = document.getElementById('nav');
                element.classList.remove("mybg");
                set = false;
            }

        }
        $(document).ready(function() {
            $('#togglePassword').click(function() {
                const eyeIcon = $(this);
                const pwdInput = $('.show-hide-password');

                pwdInput.off('blur');

                if (eyeIcon.hasClass('fa-eye')) {
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                    pwdInput.attr('type', 'password').focus();


                } else {
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                    pwdInput.attr('type', 'text').focus();

                    // pwdInput.one('blur', function(e) {
                    //     if ($(e.relatedTarget).attr('id') !== 'togglePassword') {
                    //         pwdInput.attr('type', 'password');
                    //         eyeIcon.removeClass("fa-eye").addClass('fa-eye-slash');
                    //     }
                    // });

                }
            });
        })
    </script>

</head>

<body class="white-dark">




    <div class="container " style="height:100vh">

        <div class="row ">
            <div class="col-md-6 left">
                <div class="vertical-center-100 justify-content-center d-flex ">

                    <div class="title ">

                        <div class="d-flex align-items-end">
                            <h1> <b>Pech<span style="color:#26a83d;">AI</span> </b></h1>
                            <img width="80" height="80" style="margin-bottom:10px;margin-left:-10px;transform:rotate(35deg)" src="img/pechai-nobg.png" alt="">

                        </div>
                        <p class=" round border me-4">An AI-powered Cabbage Disease detection app.</p>

                        <br>

                        <?php
                        // if (empty($userDetails->username)) {
                        //     echo ' <a href="register.php"> <button type="button" class="btn mybtn blue  btn-primary border-0" style="padding-left:30px;padding-right: 30px;"> <b>Login&emsp;</b><i class="bi bi-arrow-right"></i></button></a>';
                        // } else {
                        //     echo '<div class="btn-group" >  <a href="carshop.php"> <button type="button" class="btn mybtn blue  btn-primary border-0" style="padding-left:50px;padding-right: 60px;"> <b>Car&emsp;</b><i class="bi bi-car-front-fill"></i></button></a>';

                        //     echo '<a href="smartphoneshop.php"> <button type="button" class="btn mybtn text-light green  btn-primary border-0" style="margin-left:-30px;padding-left:30px;padding-right: 20px;"> <b>Smartphone&emsp;</b><i class="bi bi-phone"></i></button></a></div>';
                        // }

                        ?>

                    </div>



                </div>
            </div>
            <div class="col-md-6 right">

                <div class="title mob">


                    <div class="d-flex align-items-end">
                        <h1> <b>Pech<span style="color:#26a83d;">AI</span> </b></h1>
                        <img width="50" height="50" style="margin-bottom:10px;margin-left:-5px;transform:rotate(35deg)" src="img/pechai-nobg.png" alt="">

                    </div>
                    <p class="round border">An AI-powered Cabbage Disease detection app.</p>
                </div>
                <div class="vertical-center-100 justify-content-center d-flex ">

                    <div id="login" class="p-sm-5 p-4 shadow-sm border bg-white round">


                        <h2 class="pb-2 text-secondary"><b>Log In</b></h2>
                        <br>
                        <form method="post" action="" name="login">
                            <input type="text" class="border round" id="usernameEmail" name="usernameEmail" autocomplete="off" placeholder="Username" required /><br>

                            <div class="d-flex align-items-center " style="margin-top:15px">
                                <input type="password" id="password" class="border show-hide-password round" name="password" autocomplete="off" placeholder="Password" required />
                                <span class="fas fa-eye-slash" id="togglePassword" style="margin-left:-40px"></span>
                            </div>


                            <br><?php echo $errorMsgLogin; ?>

                            <hr id="hr">
                            <input type="submit" id="submit-btn" class="w-100 btn  round  " name="loginSubmit" value="LOGIN">
                        </form>


                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- <div class="one container-fluid"> 

        <div class="back">
            
        </div>


        <div class="row">

            <div class="col-md-12 ">
                <div class="vertical-center-100">
                    <div class="title ">
                        <br>
                        <br>

                        <br>
                        <center>
                        <?php
                        if (!empty($userDetails->username)) {
                            echo '
                            <h5 >Feel the Quality</h5>
                            <h1 style="font-size:80px;text-transform: uppercase;"> <b>' . $userDetails->username . ' </b></h1> 
                            ';
                        } else {
                            echo ' <h5 ></h5>
                            <h1 style="font-size:80px"> <b>PechAI </b></h1> 
                            ';
                        }

                        ?>



                        <p>"An AI-powered Cabbage Disease detection app."</p>
                    </center>
                        <br>

                        <?php
                        // if (empty($userDetails->username)) {
                        //     echo ' <a href="register.php"> <button type="button" class="btn mybtn blue  btn-primary border-0" style="padding-left:30px;padding-right: 30px;"> <b>Login&emsp;</b><i class="bi bi-arrow-right"></i></button></a>';
                        // } else {
                        //     echo '<div class="btn-group" >  <a href="carshop.php"> <button type="button" class="btn mybtn blue  btn-primary border-0" style="padding-left:50px;padding-right: 60px;"> <b>Car&emsp;</b><i class="bi bi-car-front-fill"></i></button></a>';

                        //     echo '<a href="smartphoneshop.php"> <button type="button" class="btn mybtn text-light green  btn-primary border-0" style="margin-left:-30px;padding-left:30px;padding-right: 20px;"> <b>Smartphone&emsp;</b><i class="bi bi-phone"></i></button></a></div>';
                        // }

                        ?>

                    </div>

                </div>
            </div>

        </div>
-->



    <?php
    include 'sys/footer.php'
    ?>

</body>

</html>