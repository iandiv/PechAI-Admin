<?php
include 'sys/ini.php';

$_SESSION['googleCode'] = '';
$_SESSION['aid'] = '';
setcookie("a_id", "", time() - 3600, "/");

if (empty($_SESSION['aid'])) {
    $url = BASE_URL . 'index.php';
    header("Location: index.php");
    exit();
}
