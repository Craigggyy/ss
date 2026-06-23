<?php

session_start();

if(!isset($_SESSION['loggedin']))
{
    header("Location: login.php");

    exit();
}

include "includes/db.php";

$message = "";

$id = $_SESSION['user_id'];

if(isset($_POST['update_profile']))
{
    $fullname = $_POST['fullname'];

    $email = $_POST['email'];

    $conn->query("

        UPDATE users

        SET

        fullname='$fullname',

        email='$email'

        WHERE id='$id'

    ");

    $_SESSION['fullname'] = $fullname;

    $_SESSION['email'] = $email;

    $message = "Profile Updated.";
}

if(isset($_POST['upload_id']))
{
    $filename = $_FILES['studentid']['name'];

    $tmp = $_FILES['studentid']['tmp_name'];

    move_uploaded_file(

        $tmp,

        "uploads/studentid/".$filename

    );

    $conn->query("

        UPDATE users

        SET student_id='$filename'

        WHERE id='$id'

    ");

    $message = "Student ID Uploaded.";
}

if(isset($_GET['delete']))
{
    if($_SESSION['role'] == "admin")
    {
        $deleteID = $_GET['delete'];

        $conn->query("

            DELETE FROM users

            WHERE id='$deleteID'

            AND role='user'

        ");

        header("Location: account.php");

        exit();
    }
}

if(isset($_POST['edit_resident']))
{
    if($_SESSION['role'] == "admin")
    {
        $residentID = $_POST['resident_id'];

        $fullname = $_POST['fullname'];

        $email = $_POST['email'];

        $conn->query("

            UPDATE users

            SET

            fullname='$fullname',

            email='$email'

            WHERE id='$residentID'

        ");

        $message = "Resident Updated.";
    }
}

$user = $conn->query("

    SELECT *

    FROM users

    WHERE id='$id'

");

$userData = $user->fetch_assoc();

$residents = $conn->query("

    SELECT *

    FROM users

    WHERE role='user'

");

?>

<!DOCTYPE html>

<html>

<head>

<title>

Account

</title>

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

<nav class="navbar navbar-dark">

<div class="container">

<a class="navbar-brand">

DormEase

</a>

<div>

<a
href="dashboard.php"
class="btn btn-light">

Dashboard

</a>

</div>

</div>

</nav>

<div class="container mt-5">

<span class="page-eyebrow">Resident File</span>

<?php

if($message != "")
{

?>

<div class="alert alert-success">

<?php echo $message; ?>

</div>

<?php

}

?>

<?php

if($_SESSION['role'] == "user")

{

?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

My Profile

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label>

Full Name

</label>

<input
type="text"
name="fullname"
value="<?php echo $userData['fullname']; ?>"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Email

</label>

<input
type="email"
name="email"
value="<?php echo $userData['email']; ?>"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Username

</label>

<input
type="text"
value="<?php echo $userData['username']; ?>"
class="form-control"
disabled>

</div>

<input
type="submit"
name="update_profile"
value="Update Profile"
class="btn btn-primary">

</form>

<hr>
<?php

if(!empty($userData['student_id']))

{

?>

<div class="text-center mb-4">

<img

src="uploads/studentid/<?php echo $userData['student_id']; ?>"

style="

width:250px;

height:160px;

object-fit:cover;

border-radius:10px;

border:1px solid #ccc;

">

</div>

<?php

}

?>
<h5>

Upload Student ID

</h5>

<form
method="POST"
enctype="multipart/form-data">

<input
type="file"
name="studentid"
class="form-control mb-3"
required>

<input
type="submit"
name="upload_id"
value="Upload"
class="btn btn-success">

</form>

</div>

</div>

<?php

}

else

{

?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

Resident Management

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>ID</th>

<th>Full Name</th>

<th>Email</th>

<th>Username</th>

<th>Student ID</th>

<th>Status</th>

<th>Action</th>

</tr>

<?php

while($row = $residents->fetch_assoc())

{

?>

<tr>

<td>

<span class="ledger-id">#<?php echo $row['id']; ?></span>

</td>

<td>

<?php echo $row['fullname']; ?>

</td>

<td>

<?php echo $row['email']; ?>

</td>

<td>

<?php echo $row['username']; ?>

</td>

<td>

<?php

if (!empty($row['student_id']))

{

?>

<a
href="uploads/studentid/<?php echo $row['student_id']; ?>"
target="_blank">

View ID

</a>

<?php

}

else

{

echo "—";

}

?>

</td>

<td>

<?php

if ($row['status'] == "active")
{
echo "<span class='badge-status badge-paid'>Active</span>";
}
else
{
echo "<span class='badge-status badge-unpaid'>".$row['status']."</span>";
}

?>

</td>

<td>

<a
href="account.php?delete=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm">

Delete

</a>

</td>

</tr>

<?php

}

?>

</table>

</div>

</div>

<?php

}

?>

</div>

</body>

</html>