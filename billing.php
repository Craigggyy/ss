<?php

session_start();

if (!isset($_SESSION['loggedin']))
{
    header("Location: login.php");

    exit();
}

include "includes/db.php";

$message = "";



if ($_SESSION['role'] == "user")
{
    if (isset($_POST['upload_payment']))
    {
        $billingID = $_POST['billing_id'];

        $filename = $_FILES['proof']['name'];

        $tmp = $_FILES['proof']['tmp_name'];

        move_uploaded_file(

            $tmp,

            "uploads/payment/".$filename

        );

        $conn->query("

            UPDATE billing

            SET proof_payment='$filename'

            WHERE billing_id='$billingID'

        ");

        $message = "Proof Uploaded.";
    }
}

if ($_SESSION['role'] == "admin")
{
    if (isset($_POST['mark_paid']))
    {
        $billingID = $_POST['billing_id'];

        $conn->query("

            UPDATE billing

            SET status='Paid'

            WHERE billing_id='$billingID'

        ");

        $message = "Payment Approved.";
    }

    if (isset($_GET['delete']))
    {
        $billingID = $_GET['delete'];

        $conn->query("

            DELETE FROM billing

            WHERE billing_id='$billingID'

        ");

        header("Location: billing.php");

        exit();
    }
}

?>

<!DOCTYPE html>

<html>

<head>

<title>

Billing

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

<style>

.dormease-navbar{

background:

rgba(10,10,15,.72)!important;

backdrop-filter:blur(28px);

padding:0;

height:90px;

}

.dormease-container{

max-width:1000px;

display:flex;

align-items:center;

justify-content:space-between;

height:100%;

}

.navbar-brand{

font-family:'Plus Jakarta Sans',sans-serif;

font-size:30px;

font-weight:800;

color:#fff!important;

display:flex;

align-items:center;

gap:10px;

text-decoration:none;

}

.navbar-logo{

width:70px;

height:70px;

object-fit:contain;

}

.header-btn{

display:inline-flex;

align-items:center;

justify-content:center;

padding:10px 24px;

border-radius:999px;

font-size:16px;

font-weight:700;

text-decoration:none;

color:#fff;

background:

rgba(255,255,255,.10);

border:1px solid rgba(255,255,255,.14);

}

.header-btn:hover{

background:

rgba(255,255,255,.18);

color:#fff;

}

</style>

</head>

<body>

<nav class="navbar navbar-dark dormease-navbar">

<div class="container dormease-container">

<a
href="dashboard.php"
class="navbar-brand">

<img

src="image/2.png"

class="navbar-logo"

>

DormEase

</a>

<div>

<a

href="dashboard.php"

class="header-btn"

>

Dashboard

</a>

</div>

</div>

</nav>

<div class="container mt-5">

<span class="page-eyebrow">Dues Ledger</span>

<?php

if ($message != "")
{

?>

<div class="alert alert-success">

<?php echo $message; ?>

</div>

<?php

}

?>

<?php

if ($_SESSION['role'] == "user")

{

?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

My Billing

</div>

<div class="card-body">

<div class="mb-3 text-end">

<a
href="receipt.php"
class="btn btn-warning btn-sm">

🧾 View / Print Receipt

</a>

</div>

<table class="table table-bordered">

<tr>

<th>Month</th>

<th>Amount</th>

<th>Proof</th>

<th>Status</th>

<th>Action</th>

</tr>

<?php

$id = $_SESSION['user_id'];

$bills = $conn->query("

SELECT *

FROM billing

WHERE user_id='$id'

");

while ($row = $bills->fetch_assoc())

{

?>

<tr>

<td>

<?php echo $row['month']; ?>

</td>

<td>

<span class="ledger-amount">₱<?php echo $row['amount']; ?></span>

</td>

<td>

<?php

if ($row['proof_payment'] != "")

{

?>

<a
href="uploads/payment/<?php echo $row['proof_payment']; ?>"
target="_blank">

View File

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

if ($row['status'] == "Unpaid")
{
echo "<span class='badge-status badge-unpaid'>Unpaid</span>";
}
elseif ($row['status'] == "Pending")
{
echo "<span class='badge-status badge-pending'>Pending</span>";
}
else
{
echo "<span class='badge-status badge-paid'>Paid</span>";
}

?>

</td>

<td>

<?php

if ($row['status'] == "Unpaid")

{

?>

<form
method="POST"
enctype="multipart/form-data">

<input
type="hidden"
name="billing_id"
value="<?php echo $row['billing_id']; ?>">

<input
type="file"
name="proof"
class="form-control mb-2"
required>

<input
type="submit"
name="upload_payment"
value="Upload"
class="btn btn-success btn-sm">

</form>

<?php

}

else

{

echo "<span class='text-muted-soft'>No action needed</span>";

}

?>

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

else

{

?>

<div class="card shadow">

<div class="card-header bg-danger text-white">

Billing Management

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>Resident</th>

<th>Month</th>

<th>Amount</th>

<th>Proof</th>

<th>Status</th>

<th>Action</th>

</tr>

<?php

$bills = $conn->query("

SELECT

billing.*,

users.fullname

FROM billing

INNER JOIN users

ON billing.user_id=users.id

");

while ($row = $bills->fetch_assoc())

{

?>

<tr>

<td>

<?php echo $row['fullname']; ?>

</td>

<td>

<?php echo $row['month']; ?>

</td>

<td>

<span class="ledger-amount">₱<?php echo $row['amount']; ?></span>

</td>

<td>

<?php

if ($row['proof_payment'] != "")

{

?>

<a
href="uploads/payment/<?php echo $row['proof_payment']; ?>"
target="_blank">

View File

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

if ($row['status'] == "Unpaid")
{
echo "<span class='badge-status badge-unpaid'>Unpaid</span>";
}
elseif ($row['status'] == "Pending")
{
echo "<span class='badge-status badge-pending'>Pending</span>";
}
else
{
echo "<span class='badge-status badge-paid'>Paid</span>";
}

?>

</td>

<td>

<form method="POST">

<input
type="hidden"
name="billing_id"
value="<?php echo $row['billing_id']; ?>">

<?php

if ($row['status'] == "Unpaid")

{

?>

<input
type="submit"
name="mark_paid"
value="Approve"
class="btn btn-success btn-sm">

<?php

}

?>

<a
href="billing.php?delete=<?php echo $row['billing_id']; ?>"
class="btn btn-danger btn-sm">

Delete

</a>

</form>

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