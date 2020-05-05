<?php
require_once 'pdo.php';
session_start();
if (isset($_POST['em']) && isset($_POST['pw'])) {
    $salt =  'XyZzy12*_';
    $check = hash('md5', $salt . $_POST['pw']);
    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute(array(':em' => $_POST['em'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
        unset($_SESSION['user_id']);
        $_SESSION['em'] = $row['em'];
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['success'] = "You're logged in.";
        header("Location: index.php");
        return;
    } else {
        $_SESSION['error'] = "Wrong credentials.";
        header('Location: login.php');
        return;
    }
}
?>

<html>
<head>
    <title>Adriano Oliveira Silva</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Please, log in:</h1>
    <?php
    if (isset($_SESSION['error'])) {
        echo ('<p style="color:red">' . $_SESSION['error'] . "</p>\n");
        unset($_SESSION['error']);
    }
    ?>
    <form method="post">
        <input id="email" type="text" name="em" value="" placeholder="Enter your email">
        <input id="password" type="password" name="pw" value="" placeholder="Enter your password">
        <input type="submit" onclick="return dataValidate()" value="Enter">
        <span><a href="index.php">Cancel</a></span>
    </form>
</body>
<script>
    function dataValidate() {
        var password = document.getElementById('password').value;
        var email = document.getElementById('email').value;
        if(password == "" || password == null){
            alert("Invalid password!");
            header('Location: login.php');
            return false;
        }
        if(!email.includes('@')){
            alert("Invalid email!");
            header('Location: login.php');
            return false;
        }
        return true;
        }
</script>

</html>