<?php

session_start();

if (!isset($_SESSION['loggedin']))
{
    header("Location: login.php");

    exit();
}

include "includes/db.php";

$currentRoom = "No Room Assigned";

if ($_SESSION['role'] == "user")
{
    $id = $_SESSION['user_id'];

    $room = $conn->query("

        SELECT rooms.room_number

        FROM users

        LEFT JOIN rooms

        ON users.assigned_room=rooms.room_id

        WHERE users.id='$id'

    ");

    $data = $room->fetch_assoc();

    if ($data && $data['room_number'] != "")
    {
        $currentRoom = $data['room_number'];
    }
}

?>

<!DOCTYPE html>

<html>

<head>

<title>

Dashboard

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

<span class="text-white me-3">

<?php echo $_SESSION['fullname']; ?>

</span>

<a
href="account.php"
class="btn btn-light me-2">

My Account

</a>

<a
href="logout.php"
class="btn btn-danger">

Logout

</a>

</div>

</div>

</nav>

<div class="container mt-5">

<span class="page-eyebrow">Front Desk — Dashboard</span>

<h2 class="page-title">

Welcome, <?php echo $_SESSION['fullname']; ?>

</h2>

<?php

if ($_SESSION['role'] == "admin")

{

?>

<div class="row">

<div class="col-md-4 mb-4">

<a
href="account.php"
class="text-decoration-none">

<div class="card shadow dashboard-card">

<div class="tile-icon">👥</div>

<h4>

Manage Accounts

</h4>

<span class="tile-tag">Residents on file</span>

</div>

</a>

</div>

<div class="col-md-4 mb-4">

<a
href="manageroom.php"
class="text-decoration-none">

<div class="card shadow dashboard-card">

<div class="tile-icon">🚪</div>

<h4>

Manage Rooms

</h4>

<span class="tile-tag">Add, edit, retire rooms</span>

</div>

</a>

</div>

<div class="col-md-4 mb-4">

<a
href="rooms.php"
class="text-decoration-none">

<div class="card shadow dashboard-card">

<div class="tile-icon">📋</div>

<h4>

Room Requests

</h4>

<span class="tile-tag">Approve or reject</span>

</div>

</a>

</div>

<div class="col-md-6 mb-4">

<a
href="billing.php"
class="text-decoration-none">

<div class="card shadow dashboard-card">

<div class="tile-icon">🧾</div>

<h4>

Billing

</h4>

<span class="tile-tag">Track monthly dues</span>

</div>

</a>

</div>

<div class="col-md-6 mb-4">

<a
href="maintenance.php"
class="text-decoration-none">

<div class="card shadow dashboard-card">

<div class="tile-icon">🛠️</div>

<h4>

Maintenance

</h4>

<span class="tile-tag">Open repair tickets</span>

</div>

</a>

</div>

</div>

<?php

}

else

{

?>

<div class="alert alert-primary">

<h5 class="mb-0">

🔑 Current Room

</h5>

<span class="room-tag"><?php echo $currentRoom; ?></span>

</div>

<div class="row">

<div class="col-md-4 mb-4">

<a
href="rooms.php"
class="text-decoration-none">

<div class="card shadow dashboard-card">

<div class="tile-icon">🚪</div>

<h4>

Choose Room

</h4>

<span class="tile-tag">Browse vacancies</span>

</div>

</a>

</div>

<div class="col-md-4 mb-4">

<a
href="billing.php"
class="text-decoration-none">

<div class="card shadow dashboard-card">

<div class="tile-icon">🧾</div>

<h4>

My Billing

</h4>

<span class="tile-tag">Dues and receipts</span>

</div>

</a>

</div>

<div class="col-md-4 mb-4">

<a
href="maintenance.php"
class="text-decoration-none">

<div class="card shadow dashboard-card">

<div class="tile-icon">🛠️</div>

<h4>

Maintenance

</h4>

<span class="tile-tag">Report an issue</span>

</div>

</a>

</div>

<div class="col-md-12">

<a
href="rooms.php"
class="text-decoration-none">

<div class="card shadow dashboard-card">

<div class="tile-icon">📋</div>

<h4>

My Request Status

</h4>

<span class="tile-tag">Track your application</span>

</div>

</a>

</div>

</div>

<?php

}

?>

</div>

</body>

</html>