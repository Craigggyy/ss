<?php

session_start();

if (!isset($_SESSION['loggedin']))
{
    header("Location: login.php");

    exit();
}

include "includes/db.php";

$activePage = "dashboard";

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

// Admin dashboard stats
if ($_SESSION['role'] == "admin")
{
    $totalResidents = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role='user'")->fetch_assoc()['cnt'];

    $occupiedRooms = $conn->query("SELECT COUNT(*) as cnt FROM rooms WHERE occupied > 0")->fetch_assoc()['cnt'];

    $totalRooms = $conn->query("SELECT COUNT(*) as cnt FROM rooms")->fetch_assoc()['cnt'];

    $unpaidBills = $conn->query("SELECT COUNT(*) as cnt FROM billing WHERE status='Unpaid'")->fetch_assoc()['cnt'];

    $openTickets = $conn->query("SELECT COUNT(*) as cnt FROM maintenance_requests WHERE status='Pending'")->fetch_assoc()['cnt'];
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard — DormEase</title>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link
    href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap"
    rel="stylesheet">

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

<link rel="stylesheet" href="assets/css/style.css">

</head>

<body class="has-sidebar">

<div class="de-app">

    <?php include "includes/sidebar.php"; ?>

    <div class="de-page">

        <div class="de-page-topbar">

            <button
                class="de-sidebar-toggle"
                onclick="document.getElementById('deSidebar').classList.toggle('open')"
                aria-label="Toggle sidebar">

                &#9776;

            </button>

            <div>

                <div class="de-page-eyebrow">Front Desk</div>

                <h1 class="de-page-heading">Dashboard</h1>

            </div>

        </div>

        <div class="de-page-content">

            <?php if ($_SESSION['role'] == "admin"): ?>

                <!-- Admin Stats -->
                <div class="de-stat-row">

                    <div class="de-stat-card">

                        <div class="de-stat-label">Total Residents</div>

                        <div class="de-stat-value"><?php echo $totalResidents; ?></div>

                        <div class="de-stat-sub">Active accounts</div>

                    </div>

                    <div class="de-stat-card">

                        <div class="de-stat-label">Rooms Occupied</div>

                        <div class="de-stat-value"><?php echo $occupiedRooms; ?></div>

                        <div class="de-stat-sub">of <?php echo $totalRooms; ?> total rooms</div>

                    </div>

                    <div class="de-stat-card">

                        <div class="de-stat-label">Unpaid Bills</div>

                        <div class="de-stat-value"><?php echo $unpaidBills; ?></div>

                        <div class="de-stat-sub">This month</div>

                    </div>

                    <div class="de-stat-card">

                        <div class="de-stat-label">Open Tickets</div>

                        <div class="de-stat-value"><?php echo $openTickets; ?></div>

                        <div class="de-stat-sub">Pending maintenance</div>

                    </div>

                </div>

                <!-- Quick Links -->
                <span class="page-eyebrow">Quick Actions</span>

                <div class="row g-3 mt-1">

                    <div class="col-md-4 col-6">

                        <a href="account.php" class="dashboard-card d-block text-decoration-none">

                            <div class="tile-icon">&#9965;</div>

                            <h4>Accounts</h4>

                            <span class="tile-tag">Resident Management</span>

                        </a>

                    </div>

                    <div class="col-md-4 col-6">

                        <a href="manageroom.php" class="dashboard-card d-block text-decoration-none">

                            <div class="tile-icon">&#9638;</div>

                            <h4>Manage Rooms</h4>

                            <span class="tile-tag">Add / Edit Rooms</span>

                        </a>

                    </div>

                    <div class="col-md-4 col-6">

                        <a href="rooms.php" class="dashboard-card d-block text-decoration-none">

                            <div class="tile-icon">&#9776;</div>

                            <h4>Room Requests</h4>

                            <span class="tile-tag">Approve or Reject</span>

                        </a>

                    </div>

                    <div class="col-md-6 col-6">

                        <a href="billing.php" class="dashboard-card d-block text-decoration-none">

                            <div class="tile-icon">&#9783;</div>

                            <h4>Billing</h4>

                            <span class="tile-tag">Monthly Dues Ledger</span>

                        </a>

                    </div>

                    <div class="col-md-6 col-12">

                        <a href="maintenance.php" class="dashboard-card d-block text-decoration-none">

                            <div class="tile-icon">&#9874;</div>

                            <h4>Maintenance</h4>

                            <span class="tile-tag">Repair Tickets</span>

                        </a>

                    </div>

                </div>

            <?php else: ?>

                <!-- Resident View -->
                <div class="alert alert-primary mb-4">

                    <span>&#128273; Current Room</span>

                    <strong class="room-tag"><?php echo htmlspecialchars($currentRoom); ?></strong>

                </div>

                <span class="page-eyebrow">Resident Menu</span>

                <div class="row g-3 mt-1">

                    <div class="col-md-4 col-6">

                        <a href="rooms.php" class="dashboard-card d-block text-decoration-none">

                            <div class="tile-icon">&#9638;</div>

                            <h4>Rooms</h4>

                            <span class="tile-tag">Browse Vacancies</span>

                        </a>

                    </div>

                    <div class="col-md-4 col-6">

                        <a href="billing.php" class="dashboard-card d-block text-decoration-none">

                            <div class="tile-icon">&#9783;</div>

                            <h4>My Billing</h4>

                            <span class="tile-tag">Dues and Receipts</span>

                        </a>

                    </div>

                    <div class="col-md-4 col-6">

                        <a href="maintenance.php" class="dashboard-card d-block text-decoration-none">

                            <div class="tile-icon">&#9874;</div>

                            <h4>Maintenance</h4>

                            <span class="tile-tag">Report an Issue</span>

                        </a>

                    </div>

                    <div class="col-12">

                        <a href="rooms.php" class="dashboard-card d-block text-decoration-none">

                            <div class="tile-icon">&#9776;</div>

                            <h4>My Request Status</h4>

                            <span class="tile-tag">Track Your Application</span>

                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
