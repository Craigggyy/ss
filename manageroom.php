<?php

session_start();

if (!isset($_SESSION['loggedin']))
{
    header("Location: index.php");

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
    $capacity   = $_POST['capacity'];
    $bill       = $_POST['monthly_bill'];

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
    $roomID     = $_POST['room_id'];
    $roomNumber = $_POST['room_number'];
    $capacity   = $_POST['capacity'];
    $bill       = $_POST['monthly_bill'];

    $conn->query("
        UPDATE rooms
        SET
            room_number  = '$roomNumber',
            capacity     = '$capacity',
            monthly_bill = '$bill'
        WHERE room_id = '$roomID'
    ");

    $message = "Room Updated.";
}

if (isset($_GET['delete']))
{
    $roomID = $_GET['delete'];

    $conn->query("
        DELETE FROM rooms
        WHERE room_id = '$roomID'
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
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">

</head>
<body>

<nav class="navbar navbar-dark">
    <div class="container">

        <a class="navbar-brand">
            DormEase
        </a>

        <div>
            <a href="rooms.php" class="btn btn-light me-2">Room Requests</a>
            <a href="dashboard.php" class="btn btn-light">Dashboard</a>
        </div>

    </div>
</nav>

<div class="container mt-5">

    <span class="page-eyebrow">Key Rack — Room Inventory</span>

    <?php if ($message != ""): ?>
    <div class="alert alert-success">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">

        <div class="card-header bg-primary text-white">
            Add New Room
        </div>

        <div class="card-body">

            <form method="POST">
                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label>Room Number</label>
                        <input type="text" name="room_number" placeholder="Example: A101" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Capacity</label>
                        <input type="number" name="capacity" placeholder="Number of beds" class="form-control" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Monthly Bill</label>
                        <input type="number" name="monthly_bill" placeholder="Amount in ₱" class="form-control" min="0" required>
                    </div>

                </div>

                <input type="submit" name="add_room" value="Add Room" class="btn btn-success">
            </form>

        </div>

    </div>

    <div class="card shadow">

        <div class="card-header bg-danger text-white">
            All Rooms
        </div>

        <div class="card-body">

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

                while ($row = $rooms->fetch_assoc())
                {
                    $available = $row['capacity'] - $row['occupied'];
                    $formID    = "room_form_" . $row['room_id'];

                ?>

                <form id="<?php echo $formID; ?>" method="POST">
                    <input type="hidden" name="room_id" value="<?php echo $row['room_id']; ?>">
                </form>

                <tr>

                    <td>
                        <input type="text" name="room_number" form="<?php echo $formID; ?>" value="<?php echo $row['room_number']; ?>" class="form-control form-control-sm" required>
                    </td>

                    <td>
                        <input type="number" name="capacity" form="<?php echo $formID; ?>" value="<?php echo $row['capacity']; ?>" class="form-control form-control-sm" min="1" required>
                    </td>

                    <td>
                        <?php echo $row['occupied']; ?>
                    </td>

                    <td>
                        <?php echo $available; ?>
                    </td>

                    <td>
                        <input type="number" name="monthly_bill" form="<?php echo $formID; ?>" value="<?php echo $row['monthly_bill']; ?>" class="form-control form-control-sm" min="0" required>
                    </td>

                    <td>
                        <input type="submit" name="edit_room" value="Save" form="<?php echo $formID; ?>" class="btn btn-primary btn-sm">
                        <a href="manageroom.php?delete=<?php echo $row['room_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>

                </tr>

                <?php
                }
                ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>