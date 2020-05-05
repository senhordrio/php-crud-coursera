<?php
require_once 'pdo.php';
session_start();

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':pid' => $_POST['profile_id']));
    $_SESSION['success'] = 'Profile deleted!';
    header( 'Location: index.php' ) ;
    return;
}

if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Not logged in!";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'No profile';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

?>

<html>

<head>
    <title>Adriano Oliveira Silva</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Are you sure you want to Delete the following?</h1>
    <p>First name:<?= " $fn" ?></p>
    <p>Last name:<?= " $ln" ?></p>
    <p>E-mail:<?= " $em" ?></p>
    <p>Headline:<?= " $he" ?></p>
    <p>Summary:<?= " $su" ?></p>
    <form method="POST">
        <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
        <input type="submit" value="Delete" name="delete">
        <a href="index.php">Cancel</a>
    </form>
</body>

</html>