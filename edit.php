<?php
require_once 'pdo.php';
session_start();

if (isset($_POST['fn']) && isset($_POST['ln']) && isset($_POST['em']) && isset($_POST['he']) && isset($_POST['su'])) {

    if (
        strlen($_POST['fn']) < 1 || strlen($_POST['ln']) < 1 || strlen($_POST['em']) < 1 ||
        strlen($_POST['he']) < 1 || strlen($_POST['su']) < 1
    ) {
        $_SESSION['error'] = 'Some field is empty.';
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    };

    if (strpos($_POST['em'], '@') === false) {
        $_SESSION['error'] = 'Invalid email!';
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    }

    $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
    WHERE profile_id = :pid');
    $stmt->execute(
        array(
            ':fn' => $_POST['fn'],
            ':ln' => $_POST['ln'],
            ':em' => $_POST['em'],
            ':he' => $_POST['he'],
            ':su' => $_POST['su'],
            ':pid' => $_POST['profile_id']
        )
    );
    $_SESSION['success'] = 'Profile edited!';
    header('Location: index.php');
    return;
}

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Not logged in!";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for user_id';
    header('Location: index.php');
    return;
}

// Flash pattern

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
    <h1>Edit registry:</h1>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
        unset($_SESSION['error']);
    }
    ?>
    <form method="POST">
        <label for="fn">First name:</label>
        <input id="fn" type="text" name="fn" value="<?= $fn ?>"><br><br>
        <label for="ln">Last name:</label>
        <input id="ln" type="text" name="ln" value="<?= $ln ?>"><br><br>
        <label for="em">Email:</label>
        <input id="em" type="text" name="em" value="<?= $em ?>"><br><br>
        <label for="he">Headline:</label>
        <input id="he" type="text" name="he" value="<?= $he ?>"><br><br>
        <label for="su">Summary:</label>
        <input id="su" type="text" name="su" value="<?= $su ?>"><br><br>
        <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
        <input type="submit" onclick="return dataValidate()" value="Edit">
        <a href="index.php">Cancel</a>
    </form>
</body>
<script>
    function dataValidate() {
        var fn = document.getElementById('fn').value;
        var ln = document.getElementById('ln').value;
        var em = document.getElementById('em').value;
        var he = document.getElementById('he').value;
        var su = document.getElementById('su').value;
        if (fn == "" || ln == "" || em == "" || he == "" || su == "") {
            alert("All fields must be filled!");
            header("Location: edit.php?profile_id=" . $_POST['profile_id']);
            return false;
        }
        return true;
    }
</script>

</html>