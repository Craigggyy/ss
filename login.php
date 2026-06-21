<?php

session_start();

include "includes/db.php";

$message = "";

if (isset($_POST['register']))
{
    $fullname = $_POST['fullname'];

    $email = $_POST['email'];

    $username = $_POST['username'];

    $password = $_POST['password'];

    $check = $conn->query("

        SELECT *

        FROM users

        WHERE email='$email'

        OR username='$username'

    ");

    if ($check->num_rows > 0)
    {
        $message = "Account already exists.";
    }
    else
    {
        $password = password_hash(

            $password,

            PASSWORD_DEFAULT

        );

        $conn->query("

            INSERT INTO users

            (

            fullname,

            email,

            username,

            password,

            role,

            status

            )

            VALUES

            (

            '$fullname',

            '$email',

            '$username',

            '$password',

            'user',

            'active'

            )

        ");

        $message = "Registration Successful.";
    }
}

if (isset($_POST['login']))
{
    $username = $_POST['username'];

    $password = $_POST['password'];

    $check = $conn->query("

        SELECT *

        FROM users

        WHERE username='$username'

    ");

    if ($check->num_rows > 0)
    {
        $row = $check->fetch_assoc();

        if (password_verify(

            $password,

            $row['password']

        ))
        {
            $id = $row['id'];

            $active = $conn->query("

                SELECT *

                FROM login_logs

                WHERE user_id='$id'

                AND is_logged_in=1

            ");

            if ($active->num_rows > 0)
            {
                $message = "Account is already active on another device.";
            }
            else
            {
                $_SESSION['loggedin'] = true;

                $_SESSION['user_id'] = $row['id'];

                $_SESSION['fullname'] = $row['fullname'];

                $_SESSION['email'] = $row['email'];

                $_SESSION['role'] = $row['role'];

                $token = session_id();

                $conn->query("

                    INSERT INTO login_logs

                    (

                    user_id,

                    session_token,

                    is_logged_in

                    )

                    VALUES

                    (

                    '$id',

                    '$token',

                    1

                    )

                ");

                header("Location: dashboard.php");

                exit();
            }
        }
        else
        {
            $message = "Wrong Password.";
        }
    }
    else
    {
        $message = "User Not Found.";
    }
}

if (isset($_POST['sendotp']))
{
    $email = $_POST['email'];

    $check = $conn->query("

        SELECT *

        FROM users

        WHERE email='$email'

    ");

    if ($check->num_rows > 0)
    {
        $otp = rand(

            100000,

            999999

        );

        $_SESSION['otp'] = $otp;

        $_SESSION['reset_email'] = $email;

        $_SESSION['otp_sent'] = true;

        $_SESSION['otp_verified'] = false;

        $subject = "DormEase OTP Verification";

        $body = "

Your OTP Code is:

".$otp."

Do not share this code.

";

        $headers = "From: pasiadelashley@gmail.com";

        if (

            mail(

                $email,

                $subject,

                $body,

                $headers

            )

        )
        {
            $message = "OTP Sent Successfully.";
        }
        else
        {
            $message = "Failed to send OTP.";

            unset($_SESSION['otp_sent']);
        }
    }
    else
    {
        $message = "Email does not exist.";
    }
}

if (isset($_POST['verifyotp']))
{
    if (!isset($_SESSION['otp_sent']))
    {
        $message = "Please send OTP first.";
    }
    else
    {
        if (

            $_POST['otp']

            ==

            $_SESSION['otp']

        )
        {
            $_SESSION['otp_verified'] = true;

            $message = "OTP Verified Successfully.";
        }
        else
        {
            $message = "Invalid OTP.";
        }
    }
}

if (isset($_POST['changepassword']))
{
    if (!isset($_SESSION['otp_verified']))
    {
        $message = "Verify OTP first.";
    }
    else
    {
        if ($_SESSION['otp_verified'] == false)
        {
            $message = "Verify OTP first.";
        }
        else
        {
            $password = password_hash(

                $_POST['newpassword'],

                PASSWORD_DEFAULT

            );

            $email = $_SESSION['reset_email'];

            $conn->query("

                UPDATE users

                SET password='$password'

                WHERE email='$email'

            ");

            unset($_SESSION['otp']);

            unset($_SESSION['otp_sent']);

            unset($_SESSION['otp_verified']);

            unset($_SESSION['reset_email']);

            $message = "Password Updated Successfully.";

unset($_SESSION['otp']);

unset($_SESSION['otp_sent']);

unset($_SESSION['otp_verified']);

unset($_SESSION['reset_email']);

header("Refresh:2; url=login.php");

exit();
        }
    }
}

?>

<!DOCTYPE html>

<html>

<head>

<title>DormEase</title>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link
href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="assets/css/style.css">

</head>

<body>

<div class="container mt-5">

<div class="text-center mb-4">

<span class="page-eyebrow">Resident Sign-In Desk</span>

<h2 class="page-title">⌗ DormEase</h2>

<p class="text-muted-soft">Check the log, claim your key.</p>

</div>

<?php

if ($message != "")
{

?>

<div class="alert alert-info">

<?php echo $message; ?>

</div>

<?php

}

?>

<div class="row">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

Login

</div>

<div class="card-body">

<form method="POST">

<input
type="text"
name="username"
placeholder="Username"
class="form-control mb-3"
required>

<input
type="password"
name="password"
placeholder="Password"
class="form-control mb-3"
required>

<input
type="submit"
name="login"
value="Login"
class="btn btn-primary w-100">

</form>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-success text-white">

Register

</div>

<div class="card-body">

<form method="POST">

<input
type="text"
name="fullname"
placeholder="Full Name"
class="form-control mb-3"
required>

<input
type="email"
name="email"
placeholder="Email"
class="form-control mb-3"
required>

<input
type="text"
name="username"
placeholder="Username"
class="form-control mb-3"
required>

<input
type="password"
name="password"
placeholder="Password"
class="form-control mb-3"
required>

<input
type="submit"
name="register"
value="Register"
class="btn btn-success w-100">

</form>

</div>

</div>

</div>

</div>

<div class="card shadow mt-4">

<div class="card-header bg-warning text-dark">

Forgot Password

</div>

<div class="card-body">

<?php

if (!isset($_SESSION['otp_sent']))

{

?>

<h5 class="mb-3">

Step 1 : Enter Email

</h5>

<form method="POST">

<div class="mb-3">

<input
type="email"
name="email"
placeholder="Enter your registered email"
class="form-control"
required>

</div>

<input
type="submit"
name="sendotp"
value="Send OTP"
class="btn btn-warning w-100">

</form>

<?php

}

elseif ($_SESSION['otp_verified'] == false)

{

?>

<h5 class="mb-3">

Step 2 : Verify OTP

</h5>

<form method="POST">

<div class="mb-3">

<input
type="number"
name="otp"
placeholder="Enter OTP"
class="form-control"
required>

</div>

<input
type="submit"
name="verifyotp"
value="Verify OTP"
class="btn btn-info w-100">

</form>

<?php

}

else

{

?>

<h5 class="mb-3">

Step 3 : Change Password

</h5>

<form method="POST">

<div class="mb-3">

<input
type="password"
name="newpassword"
placeholder="Enter New Password"
class="form-control"
required>

</div>

<input
type="submit"
name="changepassword"
value="Change Password"
class="btn btn-danger w-100">

</form>

<?php

}

?>

</div>

</div>

</div>

</body>

</html>