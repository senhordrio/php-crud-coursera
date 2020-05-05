<?php

$username = "drio";
$password = "gg7elSaMclWaoRga";
$servername = "https://jsjsoncoursera.herokuapp.com/";

try{
    $pdo = new PDO("mysql:host=$servername;port=3306;dbname=resumeDB", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    echo "Connection Failed:" . $e->getMessage();
    }
?>
