<?php
require_once 'pdo.php';
require_once 'util.php';
session_start();

if(!isset($_SESSION['user_id'])){
  die("ACCESS DENIED!");
  return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
    && isset($_POST['headline']) && isset($_POST['summary'])) {
  $msg = validateProfile();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
    header("Location: add.php");
    return;
  }

  $msg = validatePos();
  if(is_string($msg)){
    $_SESSION['error'] = $msg;
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

  $profile_id = $pdo->lastInsertId();
  $rank = 1;
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];
    $stmt = $pdo->prepare('INSERT INTO position
      (profile_id, rank, year, description)
      VALUES ( :pid, :rank, :year, :desc)');
    $stmt->execute(array(
    ':pid' => $profile_id,
    ':rank' => $rank,
    ':year' => $year,
    ':desc' => $desc)
    );
    $rank++;
  }

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
    <input id="em" type="text" name="email"><br><br>
    <label for="headline">Headline:</label>
    <input id="he" type="text" name="headline"><br><br>
    <label for="summary">Summary:</label>
    <textarea id="su" type="text" name="summary" rows="8"></textarea>
    <p> Position:
      <input id="addPos" type="submit" value="+">
      <div id="position_fields"></div>
    </p>

    <input type="submit" onclick="return dataValidate()" value="Add">
    <a href="index.php">Cancel</a>
  </form>
</body>
<script src="jquery-3.5.1.js"></script>
<script>
  countPos = 0;
  $(document).ready(function() {
    $('#addPos').click(function(event) {
      event.preventDefault();
      if (countPos >= 9) {
        alert('Maximum of nine position entries exceeded');
        return;
      }
      countPos++;
      $('#position_fields').append(
        '<div id="position' + countPos + '"> \
        <p>Year: <input type="text" name="year' + countPos + '" value=""/> \
        <input type="button" value="-" \
        onclick="$(\'#position' + countPos + '\').remove(); return false;"></p> \
        <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
        </div>');
    });
  });

  function dataValidate() {
    try {
      var fn = document.getElementById('fn').value;
      var ln = document.getElementById('ln').value;
      var em = document.getElementById('em').value;
      var he = document.getElementById('he').value;
      var su = document.getElementById('su').value;
      if (fn == "" || ln == "" || em == "" || he == "" || su == "") {
        alert("All values are required");
        header("Location: add.php")
        return false;
      }
      return true;
    } catch (e) {
      return false;
    }
    return false;
  }
</script>

</html>