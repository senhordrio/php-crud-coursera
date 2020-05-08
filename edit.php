<?php
require_once 'pdo.php';
session_start();

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {

    if (
        strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 ||
        strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1
    ) {
        $_SESSION['error'] = 'Some field is empty.';
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    };

    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = 'Invalid email!';
        header("Location: edit.php?profile_id=" . $_POST['profile_id']);
        return;
    }

    $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
    WHERE profile_id = :pid');
    $stmt->execute(
        array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
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

$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
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
        <label for="first_name">First name:</label>
        <input id="fn" type="text" name="first_name" value="<?= $first_name ?>"><br><br>
        <label for="last_name">Last name:</label>
        <input id="ln" type="text" name="last_name" value="<?= $last_name ?>"><br><br>
        <label for="email">Email:</label>
        <input id="em" type="text" name="email" value="<?= $email ?>"><br><br>
        <label for="headline">Headline:</label>
        <input id="he" type="text" name="headline" value="<?= $headline ?>"><br><br>
        <label for="summary">Summary:</label>
        <input id="su" type="text" name="summary" value="<?= $summary ?>"><br><br>
        <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
        <input type="submit" onclick="return dataValidate()" value="Save">
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