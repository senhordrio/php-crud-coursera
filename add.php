<?php
require_once 'pdo.php';
session_start();
if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
  if (
    strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 ||
    strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1
  ) {
    $_SESSION['error'] = 'All values are required';
    header("Location: add.php");
    return;
  };

  if (strpos($_POST['email'], '@') === false) {
    $_SESSION['error'] = 'Invalid email!';
    header("Location: add.php");
    return;
  }
  $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
  VALUES ( :uid, :f, :l, :e, :h, :s)');
  $stmt->execute(
    array(
      ':uid' => $_SESSION['user_id'],
      ':f' => $_POST['first_name'],
      ':l' => $_POST['last_name'],
      ':e' => $_POST['email'],
      ':h' => $_POST['headline'],
      ':s' => $_POST['summary']
    )
  );
  $_SESSION['success'] = 'added';
  header('Location: index.php');
  return;
}

if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = 'Not logged in!';
  header('Location: index.php');
  return;
}

?>

<html>

<head>
  <title>Adriano Oliveira Silva</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1>Add new registry:</h1>
  <?php
  if (isset($_SESSION['error'])) {
    echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
  }
  ?>
  <form method="POST">
    <labe for="first_name">First name:</labe>
    <input id="fn" type="text" name="first_name"><br><br>
    <label for="last_name">Last name:</label>
    <input id="ln" type="text" name="last_name"><br><br>
    <label for="email">Email:</label>
    <input id="em"type="text" name="email"><br><br>
    <label for="headline">Headline:</label>
    <input id="he" type="text" name="headline"><br><br>
    <label for="summary">Summary:</label>
    <input id="su" type="text" name="summary"><br><br>
    <input type="submit" onclick="return dataValidate()" value="Add">
    <a href="index.php">Cancel</a>
  </form>
</body>
<script>
    function dataValidate() {
        try{
            var fn = document.getElementById('fn').value;
            var ln = document.getElementById('ln').value;
            var em = document.getElementById('em').value;
            var he = document.getElementById('he').value;
            var su = document.getElementById('su').value;
            if(fn == "" || ln == "" || em == "" || he == "" || su == ""){
                alert("All values are required");
                header("Location: add.php")
                return false;
            }
            return true;
        }catch(e){
            return false;
        }
        return false;
        }
</script>
</html>