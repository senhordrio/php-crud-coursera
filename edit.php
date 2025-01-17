<?php
require_once 'pdo.php';
require_once 'util.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED!");
    return;
}

if (!isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pid AND user_id = :uid");
$stmt->execute(array(":pid" => $_REQUEST['profile_id'], ':uid' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for user_id';
    header('Location: index.php');
    return;
}

if (
    isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
    && isset($_POST['headline']) && isset($_POST['summary'])
) {

    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
        return;
    }

    $msg = validateEdu();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
        return;
    }

    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
        return;
    }

    $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
    WHERE profile_id = :pid AND user_id = :uid');
    $stmt->execute(
        array(
            ':pid' => $_REQUEST['profile_id'],
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']
        )
    );

    $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    insertPositions($pdo, $_REQUEST['profile_id']);

    $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    insertEducations($pdo, $_REQUEST['profile_id']);


    $_SESSION['success'] = 'Profile edited!';
    header('Location: index.php');
    return;
}



if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Not logged in!";
    header('Location: index.php');
    return;
}

$positions = loadPos($pdo, $_REQUEST['profile_id']);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);
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
    flashMessages();
    ?>
    <form method="POST" action="edit.php">
        <input type="hidden" name="profile_id" value="<?= htmlentities($profile_id) ?>" />
        <label for="first_name">First name:</label>
        <input id="fn" type="text" name="first_name" value="<?= $first_name ?>"><br><br>
        <label for="last_name">Last name:</label>
        <input id="ln" type="text" name="last_name" value="<?= $last_name ?>"><br><br>
        <label for="email">Email:</label>
        <input id="em" type="text" name="email" value="<?= $email ?>"><br><br>
        <label for="headline">Headline:</label>
        <input id="he" type="text" name="headline" value="<?= $headline ?>"><br><br>
        <label for="summary">Summary:</label>
        <input id="su" type="text" name="summary" value="<?= $summary ?>"></input>
        <?php

        $countEdu = 0;

        echo ('<p> Education: <input type="submit" id="addEdu" value="+">' . "\n");
        echo ('<div id="edu_fields">' . "\n");
        if (count($schools) > 0) {
            foreach ($schools as $school) {
                $countEdu++;
                echo ('<div id="edu'.$countEdu.'">');
                echo ('<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.$school['year'].'"/>
                <input type="button" value="-" onclick="$(\'#edu'.$countEdu.'\').remove();return false;"></p>
                <p>School: <input type="text" size="80" name="edu_school'.$countEdu.'" class="school"
                value="'.htmlentities($school['name']).'"/>');
                echo ("\n</div>\n");
            }
        }
        echo ('</div><p>');

        $pos = 0;
        echo ('<p>Position: <input type="submit" id="addPos" value="+">' . "\n");
        echo ('<div id="position_fields">' . "\n");
        foreach ($positions as $position) {
            $pos++;
            echo ('<div id="position' . $pos . '">' . "\n");
            echo ('<p>Year: <input type="text" name="year' . $pos . '"');
            echo ('value="' . $position['year'] . '"/>' . "\n");
            echo ('<input type="button" value="-"');
            echo ('onclick="$(\'#position' . $pos . '\').remove();return false;">' . "\n");
            echo ("<p>\n");
            echo ('<textarea name="desc' . $pos . '" rows="8">' . "\n");
            echo (htmlentities($position['description']) . "\n");
            echo ("\n</textarea>\n</div>\n");
        }
        echo ("</div></p>\n");
        ?>
        <input type="submit" onclick="return dataValidate()" value="Save">
        <a href="index.php">Cancel</a>
    </form>
    <script src="jquery-3.5.1.js"></script>
    <script src="jquery-ui.js"></script>
    <script>
        countPos = <?= $pos ?>;
        countEdu = <?= $countEdu ?>;

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
                    onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
                    <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
                    </div>');
            });

            $('#addEdu').click(function(event) {
                event.preventDefault();
                if (countPos >= 9) {
                    alert('Maximum of nine position entries exceeded');
                    return;
                }
                countEdu++;
                var source = $("#edu_template").html();
                $('#edu_fields').append(source.replace(/@COUNT@/g, countEdu));
                $('.school').autocomplete({
                    source: "school.php"
                });
            });
            $('.school').autocomplete({
                source: "school.php"
            });
        });

        function dataValidate() {
            var fn = document.getElementById('fn').value;
            var ln = document.getElementById('ln').value;
            var em = document.getElementById('em').value;
            var he = document.getElementById('he').value;
            var su = document.getElementById('su').value;
            if (fn == "" || ln == "" || em == "" || he == "" || su == "") {
                alert("All fields must be filled!");
                header("Location: edit.php?profile_id=".$_POST['profile_id']);
                return false;
            }
            return true;
        }
    </script>
    <script id="edu_template" type="text">
        <div id="edu@COUNT@">
            <p>Year: <input type="text" name="edu_year@COUNT@" value=""/>
            <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
            <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value=""/>
            </p>
        </div>
    </script>
</body>
</html>