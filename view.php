<?php
require_once 'pdo.php';
session_start();

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'No profile';
    header( 'Location: index.php' ) ;
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);

$stmt = $pdo->prepare("SELECT * FROM position WHERE profile_id = :pid");
$stmt->execute(array("pid" =>$_GET['profile_id']));
?>

<html>
<head>
    <title>Adriano Oliveira Silva</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<h1>Adriano Oliveira Awesome Resume Registry</h1>
<a href="index.php">Back</a>
<body>
    <h2>Profile details:</h2>
    <p>First name:<?= " $fn" ?></p>
    <p>Last name:<?= " $ln" ?></p>
    <p>E-mail:<?= " $em" ?></p>
    <p>Headline:<?= " $he" ?></p>
    <p>Summary:<?= " $su" ?></p>
    <p>Position:</p>
    <ul>
    <?php
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo('<li><span>');
        echo(htmlentities($row['year']));
        echo(': ');
        echo('</span><span>');
        echo(htmlentities($row['description']));
        echo('</span></li>');
    }
    ?>
    </ul>
</body>
</html>