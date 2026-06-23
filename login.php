<?php

session_start();

include "includes/db.php";

$message = "";

if(isset($_COOKIE['remember']))
{
    $_SESSION['loggedin']=true;

    $_SESSION['user_id']=$_COOKIE['user_id'];

    $_SESSION['fullname']=$_COOKIE['fullname'];

    $_SESSION['email']=$_COOKIE['email'];

    $_SESSION['role']=$_COOKIE['role'];

    header("Location: dashboard.php");

    exit();
}

if (isset($_POST['register']))
{
    $fullname = $_POST['fullname'];

    $email = $_POST['email'];

    $username = $_POST['username'];

    $password = $_POST['password'];

    $studentid = "";

if(!empty($_FILES['studentid']['name']))
{
    $studentid = time().$_FILES['studentid']['name'];

    move_uploaded_file(

        $_FILES['studentid']['tmp_name'],

        "uploads/studentid/".$studentid

    );
}

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

status,

student_id

)

            VALUES

(

'$fullname',

'$email',

'$username',

'$password',

'user',

'active',

'$studentid'

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

                if(isset($_POST['remember']))
{
    setcookie(

    "remember",

    true,

    time()+604800,

    "/"

    );

    setcookie(

    "user_id",

    $row['id'],

    time()+604800,

    "/"

    );

    setcookie(

    "fullname",

    $row['fullname'],

    time()+604800,

    "/"

    );

    setcookie(

    "email",

    $row['email'],

    time()+604800,

    "/"

    );

    setcookie(

    "role",

    $row['role'],

    time()+604800,

    "/"

    );
}

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
href="https://fonts.googleapis.com/css2?family=Circular+Std:wght@400;500;700&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="assets/css/style.css">

<style>
  /* ── Airbnb-style Design System ── */
  *, *::before, *::after { box-sizing: border-box; }

  body {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;

    background:
        linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
        url('image/1.png') no-repeat center center fixed;

    background-size: cover;

    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #222222;
    -webkit-font-smoothing: antialiased;
}

  /* ── Wrapper & Card ── */
  .auth-wrapper {
    width: 100%;
    max-width: 480px;
    padding: 1.5rem 1rem;
  }

  .auth-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #DDDDDD;
    padding: 2.5rem 2.5rem 2rem;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  }

  /* ── Brand Header ── */
  .brand-eyebrow {
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.02em;
    color: #717171;
    margin-bottom: 6px;
  }

  .brand-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 26px;
    font-weight: 700;
    color: #FF385C;
    margin-bottom: 4px;
    letter-spacing: -0.5px;
  }

  .brand-sub {
    font-size: 13px;
    color: #717171;
    margin-bottom: 0;
  }

  /* ── Section Heading ── */
  .section-label {
    font-size: 22px;
    font-weight: 600;
    color: #222222;
    margin-bottom: 20px;
    letter-spacing: -0.3px;
  }

  /* ── Divider ── */
  .airbnb-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 1.5rem 0;
    color: #717171;
    font-size: 13px;
  }
  .airbnb-divider::before,
  .airbnb-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #DDDDDD;
  }

  /* ── Inputs ── */
  .form-control {
    border-radius: 10px;
    border: 1px solid #DDDDDD;
    font-size: 15px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    padding: 14px 16px;
    background: #ffffff;
    color: #222222;
    width: 100%;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
  }

  .form-control::placeholder {
    color: #B0B0B0;
    font-size: 15px;
  }

  .form-control:hover {
    border-color: #222222;
  }

  .form-control:focus {
    border-color: #222222;
    background: #fff;
    box-shadow: 0 0 0 2px rgba(34,34,34,0.15);
  }

  /* ── Primary Button (Airbnb gradient red) ── */
  .btn-login,
  .btn-login:visited {
    display: block;
    width: 100%;
    background: linear-gradient(to right, #E61E4D, #E31C5F, #D70466);
    background: #FF385C;
    color: #fff !important;
    border: none;
    border-radius: 10px;
    padding: 14px 24px;
    font-size: 16px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    text-align: center;
    letter-spacing: -0.1px;
  }

  .btn-login:hover {
    background: #E61E4D;
    color: #fff;
  }

  .btn-login:active {
    transform: scale(0.99);
  }

  /* ── Outline Button (Register) ── */
  .btn-register {
    display: block;
    width: 100%;
    background: #fff;
    color: #222222 !important;
    border: 1px solid #DDDDDD;
    border-radius: 10px;
    padding: 14px 24px;
    font-size: 16px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s, transform 0.1s;
    text-align: center;
  }

  .btn-register:hover {
    border-color: #222222;
    background: #F7F7F7;
  }

  .btn-register:active {
    transform: scale(0.99);
  }

  /* ── Forgot Link ── */
  .forgot-link {
    font-size: 14px;
    font-weight: 500;
    color: #222222;
    text-decoration: underline;
    text-underline-offset: 2px;
    cursor: pointer;
  }

  .forgot-link:hover {
    color: #FF385C;
    text-decoration: underline;
  }

  /* ── Back Link ── */
  .back-link {
    font-size: 15px;
    font-weight: 600;
    color: #222222;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 1.5rem;
    background: none;
    border: none;
    padding: 0;
    letter-spacing: -0.1px;
  }

  .back-link:hover {
    color: #FF385C;
  }

  /* ── Alert ── */
  .alert {
    border-radius: 10px;
    font-size: 14px;
    border: 1px solid transparent;
    padding: 14px 16px;
  }

  .alert-info {
    background-color: #FFF0F3;
    border-color: #FFBBC8;
    color: #C13B4F;
  }

  /* ── Panel visibility ── */
  .register-panel,
  .forgot-panel { display: none; }

  .register-panel.active,
  .forgot-panel.active { display: block; }

  .login-panel { display: block; }
  .login-panel.hidden { display: none; }

  /* ── Step Badge ── */
  .step-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #FFF0F3;
    color: #C13B4F;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 99px;
    margin-bottom: 16px;
    letter-spacing: 0.02em;
  }

  /* ── OTP / Verify / Change buttons — all use Airbnb red ── */
  .btn-otp,
  .btn-verify,
  .btn-change {
    display: block;
    width: 100%;
    background: #FF385C;
    color: #fff !important;
    border: none;
    border-radius: 10px;
    padding: 14px 24px;
    font-size: 16px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    text-align: center;
  }

  .btn-otp:hover,
  .btn-verify:hover,
  .btn-change:hover {
    background: #E61E4D;
    color: #fff;
  }

  .btn-otp:active,
  .btn-verify:active,
  .btn-change:active {
    transform: scale(0.99);
  }

  /* ── Logo mark ── */
  .logo-mark {
    width: 150px;
    height: 150px;
    margin: 0 auto 12px;
}

.logo-mark img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

  /* ── mb spacing helpers ── */
  .mb-airbnb { margin-bottom: 12px; }
</style>

</head>

<body>

<div class="auth-wrapper">

  <?php if ($message != ""): ?>
  <div class="alert alert-info mb-3">
    <?php echo $message; ?>
  </div>
  <?php endif; ?>

  <div class="auth-card">

    <div class="text-center mb-4">
      <div class="logo-mark">
    <img src="image/2.png" alt="DormEase Logo">
</div>
      
    </div>

    <?php
      $showForgot = isset($_SESSION['otp_sent']) || isset($_SESSION['otp_verified']);
      $showRegister = ($message == "Registration Successful." || (isset($_POST['register']) && $message != ""));
    ?>

    <!-- LOGIN PANEL -->
    <div class="login-panel" id="loginPanel">

      <p class="section-label">Login</p>

      <form method="POST">
        <div class="mb-3">
          <input
            type="text"
            name="username"
            placeholder="Username"
            class="form-control"
            required>
        </div>
        <div class="mb-3">
          <input
            type="password"
            name="password"
            placeholder="Password"
            class="form-control"
            required>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <a href="#" class="forgot-link" onclick="showPanel('forgot'); return false;">Forgot password?</a>
          <div class="form-check mb-3">

<input

type="checkbox"

name="remember"

class="form-check-input"

id="remember">

<label

for="remember"

class="form-check-label">

Remember Me

</label>

</div>
        </div>

        <input
          type="submit"
          name="login"
          value="Continue"
          class="btn-login mb-3">

        <div class="airbnb-divider">or</div>

        <input
          type="button"
          value="Create an account"
          class="btn-register"
          onclick="showPanel('register')">
      </form>

    </div>

    <!-- REGISTER PANEL -->
    <div class="register-panel <?php echo ($showRegister ? 'active' : ''); ?>" id="registerPanel">

      <button type="button" class="back-link" onclick="showPanel('login')">&#8592; Back to login</button>

      <p class="section-label">Register</p>

      <form

method="POST"

enctype="multipart/form-data"

>
        <div class="mb-3">
          <input
            type="text"
            name="fullname"
            placeholder="Full Name"
            class="form-control"
            required>
        </div>
        <div class="mb-3">
          <input
            type="email"
            name="email"
            placeholder="Email"
            class="form-control"
            required>
        </div>
        <div class="mb-3">
          <input
            type="text"
            name="username"
            placeholder="Username"
            class="form-control"
            required>
        </div>
        <div class="mb-3">
          <input
            type="password"
            name="password"
            placeholder="Password"
            class="form-control"
            required>

            <div class="mb-3">

<input

type="file"

name="studentid"

class="form-control"

required>

</div>
        </div>
        <input
          type="submit"
          name="register"
          value="Register"
          class="btn-login">
      </form>

    </div>

    <!-- FORGOT PASSWORD PANEL -->
    <div class="forgot-panel <?php echo ($showForgot ? 'active' : ''); ?>" id="forgotPanel">

      <button type="button" class="back-link" onclick="showPanel('login')">&#8592; Back to login</button>

      <p class="section-label">Forgot Password</p>

      <?php if (!isset($_SESSION['otp_sent'])): ?>

        <span class="step-badge">Step 1 of 3 &mdash; Enter Email</span>

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
            class="btn-otp">
        </form>

      <?php elseif ($_SESSION['otp_verified'] == false): ?>

        <span class="step-badge">Step 2 of 3 &mdash; Verify OTP</span>

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
            class="btn-verify">
        </form>

      <?php else: ?>

        <span class="step-badge">Step 3 of 3 &mdash; Change Password</span>

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
            class="btn-change">
        </form>

      <?php endif; ?>

    </div>

  </div>

</div>

<script>
function showPanel(panel) {
  document.getElementById('loginPanel').style.display    = 'none';
  document.getElementById('registerPanel').style.display = 'none';
  document.getElementById('forgotPanel').style.display   = 'none';

  if (panel === 'login')    document.getElementById('loginPanel').style.display    = 'block';
  if (panel === 'register') document.getElementById('registerPanel').style.display = 'block';
  if (panel === 'forgot')   document.getElementById('forgotPanel').style.display   = 'block';
}

(function() {
  <?php if ($showForgot): ?>
    showPanel('forgot');
  <?php elseif ($showRegister): ?>
    showPanel('register');
  <?php else: ?>
    showPanel('login');
  <?php endif; ?>
})();
</script>

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>