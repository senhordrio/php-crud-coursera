<?php
    //Adriano Oliveira Silva
    session_start();
    session_destroy();
    header('Location: index.php');
?>