<?php

session_start();

include "includes/db.php";

if(isset($_SESSION['user_id']))
{
    $id = $_SESSION['user_id'];

    $conn->query("

        UPDATE login_logs

        SET is_logged_in = 0

        WHERE user_id = '$id'

    ");
}

/* Remove Remember Me cookies */

setcookie(

"remember",

"",

time()-3600,

"/"

);

setcookie(

"user_id",

"",

time()-3600,

"/"

);

setcookie(

"fullname",

"",

time()-3600,

"/"

);

setcookie(

"email",

"",

time()-3600,

"/"

);

setcookie(

"role",

"",

time()-3600,

"/"

);

/* Remove all session data */

$_SESSION = array();

session_unset();

session_destroy();

/* Start a fresh session */

session_start();

session_destroy();

header("Location: login.php");

exit();

?>