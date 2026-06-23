<?php

session_start();

if (!isset($_SESSION['loggedin']))
{
    header("Location: login.php");

    exit();
}

if ($_SESSION['role'] != "admin")
{
    header("Location: dashboard.php");

    exit();
}

include "includes/db.php";

$message = "";

if (isset($_POST['add_room']))
{
    $roomNumber = $_POST['room_number'];

    $capacity = $_POST['capacity'];

    $bill = $_POST['monthly_bill'];

    $conn->query("

        INSERT INTO rooms

        (

        room_number,

        capacity,

        occupied,

        monthly_bill,

        availability

        )

        VALUES

        (

        '$roomNumber',

        '$capacity',

        0,

        '$bill',

        'Available'

        )

    ");

    $message = "Room Added.";
}

if (isset($_POST['edit_room']))
{
    $roomID = $_POST['room_id'];

    $roomNumber = $_POST['room_number'];

    $capacity = $_POST['capacity'];

    $bill = $_POST['monthly_bill'];

    $conn->query("

        UPDATE rooms

        SET

        room_number='$roomNumber',

        capacity='$capacity',

        monthly_bill='$bill'

        WHERE room_id='$roomID'

    ");

    $message = "Room Updated.";
}

if (isset($_GET['delete']))
{
    $roomID = $_GET['delete'];

    $conn->query("

        DELETE FROM rooms

        WHERE room_id='$roomID'

    ");

    header("Location: manageroom.php");

    exit();
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Manage Rooms</title>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link
href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

font-family:'Plus Jakarta Sans',sans-serif;

background:

url('image/bg.png')

center center fixed;

background-size:cover;

min-height:100vh;

}

.navbar{

background:

rgba(10,10,15,.72)!important;

backdrop-filter:blur(28px);

padding:0;

position:sticky;

top:0;

z-index:200;

}

.navbar .container{

height:90px;

max-width:1000px;

display:flex;

align-items:center;

justify-content:space-between;

}

.navbar-brand{

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

.btn-glass{

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

.btn-glass:hover{

background:

rgba(255,255,255,.18);

color:#fff;

}

.page-wrap{

max-width:1000px;

margin:auto;

padding:50px 16px;

}

.welcome-eyebrow{

font-size:11px;

font-weight:700;

letter-spacing:.1em;

text-transform:uppercase;

color:#FF375F;

margin-bottom:8px;

}

.welcome-name{

font-size:38px;

font-weight:800;

letter-spacing:-1px;

margin-bottom:40px;

}

.glass-card{

background:

rgba(255,255,255,.92);

border-radius:26px;

padding:26px;

box-shadow:

0 10px 35px rgba(0,0,0,.15);

margin-bottom:30px;

}

.section-title{

font-size:14px;

font-weight:800;

letter-spacing:.08em;

text-transform:uppercase;

margin-bottom:20px;

color:#777;

}

label{

font-size:12px;

font-weight:700;

text-transform:uppercase;

letter-spacing:.08em;

color:#888;

margin-bottom:6px;

}

.table th{

background:

linear-gradient(

135deg,

#141d2e,

#1a2135

);

color:#fff;

font-size:12px;

font-weight:700;

letter-spacing:.08em;

text-transform:uppercase;

padding:18px;

}

.table td{

padding:18px;

vertical-align:middle;

}

.table tbody tr:hover{

background:#f8f9fb;

}

.btn-success,

.btn-primary,

.btn-danger{

border-radius:999px;

font-weight:700;

padding:8px 18px;

}

</style>

</head>

<body>

<nav class="navbar navbar-dark">

<div class="container">

<a class="navbar-brand">

<img

src="image/2.png"

class="navbar-logo"

>

DormEase

</a>

<div class="d-flex gap-2">



<a

href="dashboard.php"

class="btn-glass"

>

Dashboard

</a>

</div>

</div>

</nav>

<div class="page-wrap">

<div class="welcome-eyebrow">

Room Inventory

</div>

<div class="welcome-name">

Manage Rooms

</div>

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

<div class="glass-card">

<div class="section-title">

Add New Room

</div>

<form method="POST">

<div class="row">

<div class="col-md-4 mb-3">

<label>Room Number</label>

<input

type="text"

name="room_number"

placeholder="Example: A101"

class="form-control"

required>

</div>

<div class="col-md-4 mb-3">

<label>Capacity</label>

<input

type="number"

name="capacity"

placeholder="Number of beds"

class="form-control"

min="1"

required>

</div>

<div class="col-md-4 mb-3">

<label>Monthly Bill</label>

<input

type="number"

name="monthly_bill"

placeholder="Amount in ₱"

class="form-control"

min="0"

required>

</div>

</div>

<input

type="submit"

name="add_room"

value="Add Room"

class="btn btn-success">

</form>

</div>

<div class="glass-card">

<div class="section-title">

All Rooms

</div>

<table class="table table-bordered">

<tr>

<th>Room</th>

<th>Capacity</th>

<th>Occupied</th>

<th>Available</th>

<th>Monthly Bill</th>

<th>Action</th>

</tr>

<?php

$rooms = $conn->query("

SELECT *

FROM rooms

");

while($row = $rooms->fetch_assoc())

{

$available = $row['capacity'] - $row['occupied'];

$formID = "room_form_".$row['room_id'];

?>

<form

id="<?php echo $formID; ?>"

method="POST">

<input

type="hidden"

name="room_id"

value="<?php echo $row['room_id']; ?>">

</form>

<tr>

<td>

<input

type="text"

name="room_number"

form="<?php echo $formID; ?>"

value="<?php echo $row['room_number']; ?>"

class="form-control form-control-sm"

required>

</td>

<td>

<input

type="number"

name="capacity"

form="<?php echo $formID; ?>"

value="<?php echo $row['capacity']; ?>"

class="form-control form-control-sm"

required>

</td>

<td>

<?php echo $row['occupied']; ?>

</td>

<td>

<?php echo $available; ?>

</td>

<td>

<input

type="number"

name="monthly_bill"

form="<?php echo $formID; ?>"

value="<?php echo $row['monthly_bill']; ?>"

class="form-control form-control-sm"

required>

</td>

<td>

<input

type="submit"

name="edit_room"

value="Save"

form="<?php echo $formID; ?>"

class="btn btn-primary btn-sm">

<a

href="manageroom.php?delete=<?php echo $row['room_id']; ?>"

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

</body>

</html>

<?php