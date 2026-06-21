<?php

session_start();

include "includes/db.php";

if (isset($_SESSION['user_id']))
{
    $id = $_SESSION['user_id'];

    $conn->query("

        UPDATE login_logs

        SET is_logged_in=0

        WHERE user_id='$id'

    ");
}

session_unset();

session_destroy();

header("Location: login.php");

exit();

?>