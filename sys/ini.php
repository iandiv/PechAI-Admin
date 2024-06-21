<?php
session_start();
$DB_HOST = "localhost";
$DB_NAME = "pechai";
$DB_USER = "root";
$DB_PASS = "";
define("BASE_URL", "http://localhost/PechAI/"); // Eg. http://yourwebsite.com

function pdo_init() {
    global $DB_HOST,
        $DB_NAME,
        $DB_USER,

        $DB_PASS;



    try {
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
        $pdo->exec("set names utf8");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage();
    }
}
