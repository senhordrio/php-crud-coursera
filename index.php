<?php
require_once "pdo.php";
session_start();
$stmt = $pdo->query("SELECT user_id, profile_id, first_name, headline FROM profile");

?>

<html>

<head>
    <title>Adriano Oliveira Silva</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Adriano Oliveira Awesome Resume Registry</h1>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }
    ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Headline</th>
        </tr>
        <?php
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>";
            echo(htmlentities($row['first_name']));
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td>");
            echo("<td>");
            echo('<a href="view.php?profile_id='.$row['profile_id'].'"><button>View</button></a>');
            echo("</td>");
            if (isset($_SESSION["user_id"])){
                echo("<td>");
                echo('<a href="edit.php?profile_id='.$row['profile_id'].'"><button>Edit</button></a>');
                echo('<a href="delete.php?profile_id='.$row['profile_id'].'"><button>Delete</button></a>');
                echo("</td>");
            }
            echo("</tr>");
        }
        ?>
    </table>
    <?php
    if (!isset($_SESSION["user_id"])) {
        echo '<br><a href="login.php">Please log in</a>';
    } else {
        echo '<br><a href="add.php">Add New Entry</a><br><br>';
        echo '<a href="logout.php">Log out</a>';
    }
    ?>
</body>

</html>