<?php
require_once 'pdo.php';
session_start();
if (isset($_POST['fn']) && isset($_POST['ln']) && isset($_POST['em']) && isset($_POST['he']) && isset($_POST['su'])) {
  if (
    strlen($_POST['fn']) < 1 || strlen($_POST['ln']) < 1 || strlen($_POST['em']) < 1 ||
    strlen($_POST['he']) < 1 || strlen($_POST['su']) < 1
  ) {
    $_SESSION['error'] = 'Some field is empty.';
    header("Location: add.php");
    return;
  };

  if (strpos($_POST['em'], '@') === false) {
    $_SESSION['error'] = 'Invalid email!';
    header("Location: add.php");
    return;
  }
  $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
  VALUES ( :uid, :f, :l, :e, :h, :s)');
  $stmt->execute(
    array(
      ':uid' => $_SESSION['user_id'],
      ':f' => $_POST['fn'],
      ':l' => $_POST['ln'],
      ':e' => $_POST['em'],
      ':h' => $_POST['he'],
      ':s' => $_POST['su']
    )
  );
  $_SESSION['success'] = 'Profile added!';
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
    <labe for="fn">First name:</labe>
    <input id="fn" type="text" name="fn"><br><br>
    <label for="ln">Last name:</label>
    <input id="ln" type="text" name="ln"><br><br>
    <label for="em">Email:</label>
    <input id="em"type="text" name="em"><br><br>
    <label for="he">Headline:</label>
    <input id="he" type="text" name="he"><br><br>
    <label for="su">Summary:</label>
    <input id="su" type="text" name="su"><br><br>
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
                alert("All fields must be filled!");
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