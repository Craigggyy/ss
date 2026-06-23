<?php

session_start();

if (!isset($_SESSION['loggedin']))
{
    header("Location: login.php");

    exit();
}

include "includes/db.php";

$activePage = "rooms";

$message = "";

if ($_SESSION['role'] == "user")
{
    if (isset($_POST['reserve_room']))
    {
        $userID = $_SESSION['user_id'];

        $roomID = $_POST['room_id'];

        $check = $conn->query("

            SELECT *

            FROM room_requests

            WHERE user_id='$userID'

            AND

            (

            status='Pending'

            OR

            status='Accepted'

            )

        ");

        if ($check->num_rows > 0)
        {
            $message = "You already have a pending request.";
        }
        else
        {
            $room = $conn->query("

                SELECT *

                FROM rooms

                WHERE room_id='$roomID'

            ");

            $roomData = $room->fetch_assoc();

            $bill = $roomData['monthly_bill'];

            $conn->query("

                INSERT INTO room_requests

                (

                user_id,

                room_id,

                monthly_bill,

                status

                )

                VALUES

                (

                '$userID',

                '$roomID',

                '$bill',

                'Pending'

                )

            ");

            $message = "Room request submitted.";
        }
    }
}

if ($_SESSION['role'] == "admin")
{
    if (isset($_POST['transfer']))
    {
        $userID = $_POST['user_id'];

        $oldRoom = $_POST['old_room'];

        $newRoom = $_POST['new_room'];

        if ($oldRoom != $newRoom)
        {
            $conn->query("

                UPDATE users

                SET assigned_room='$newRoom'

                WHERE id='$userID'

            ");

            $conn->query("

                UPDATE rooms

                SET occupied=occupied-1

                WHERE room_id='$oldRoom'

            ");

            $conn->query("

                UPDATE rooms

                SET occupied=occupied+1

                WHERE room_id='$newRoom'

            ");

            $message = "Resident transferred.";
        }
    }

    if (isset($_POST['remove']))
    {
        $userID = $_POST['user_id'];

        $roomID = $_POST['room_id'];

        $conn->query("

            UPDATE users

            SET assigned_room=NULL

            WHERE id='$userID'

        ");

        $conn->query("

            UPDATE rooms

            SET occupied=occupied-1

            WHERE room_id='$roomID'

        ");

        $message = "Resident removed.";
    }

    if (isset($_POST['delete_resident']))
    {
        $userID = $_POST['user_id'];

        $roomID = $_POST['room_id'];

        $conn->query("

            DELETE FROM users

            WHERE id='$userID'

        ");

        $conn->query("

            UPDATE rooms

            SET occupied=occupied-1

            WHERE room_id='$roomID'

        ");

        $message = "Resident deleted.";
    }

    if (isset($_POST['approve']))
    {
        $requestID = $_POST['request_id'];

        $userID = $_POST['user_id'];

        $roomID = $_POST['room_id'];

        $conn->query("

            UPDATE room_requests

            SET status='Accepted'

            WHERE request_id='$requestID'

        ");

        $conn->query("

            UPDATE users

            SET assigned_room='$roomID'

            WHERE id='$userID'

        ");

        $conn->query("

            UPDATE rooms

            SET occupied=occupied+1

            WHERE room_id='$roomID'

        ");

        $message = "Request Approved.";
    }

    if (isset($_POST['reject']))
    {
        $requestID = $_POST['request_id'];

        $conn->query("

            UPDATE room_requests

            SET status='Rejected'

            WHERE request_id='$requestID'

        ");

        $message = "Request Rejected.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Rooms — DormEase</title>

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

                <div class="de-page-eyebrow">Room Ledger</div>

                <h1 class="de-page-heading">

                    <?php echo ($_SESSION['role'] == "admin") ? "Room Requests" : "Available Rooms"; ?>

                </h1>

            </div>

            <?php if ($_SESSION['role'] == "admin"): ?>

                <a href="manageroom.php" class="btn btn-primary btn-sm">Manage Rooms</a>

            <?php endif; ?>

        </div>

        <div class="de-page-content">

            <?php if ($message != ""): ?>

                <div class="alert alert-success mb-4">

                    <?php echo $message; ?>

                </div>

            <?php endif; ?>

            <?php if ($_SESSION['role'] == "user"): ?>

                <div class="card shadow mb-4">

                    <div class="card-header bg-success">Available Rooms</div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>Room</th>

                                        <th>Capacity</th>

                                        <th>Occupied</th>

                                        <th>Available</th>

                                        <th>Monthly Bill</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $rooms = $conn->query("SELECT * FROM rooms");

                                    while ($row = $rooms->fetch_assoc()):

                                        $available = $row['capacity'] - $row['occupied'];

                                    ?>

                                        <tr>

                                            <td>

                                                <span class="room-tag"><?php echo $row['room_number']; ?></span>

                                            </td>

                                            <td><?php echo $row['capacity']; ?></td>

                                            <td><?php echo $row['occupied']; ?></td>

                                            <td><?php echo $available; ?></td>

                                            <td>

                                                <span class="ledger-amount">&#8369;<?php echo $row['monthly_bill']; ?></span>

                                            </td>

                                            <td>

                                                <?php if ($available > 0): ?>

                                                    <form method="POST">

                                                        <input
                                                            type="hidden"
                                                            name="room_id"
                                                            value="<?php echo $row['room_id']; ?>">

                                                        <input
                                                            type="submit"
                                                            name="reserve_room"
                                                            value="Reserve"
                                                            class="btn btn-success btn-sm">

                                                    </form>

                                                <?php else: ?>

                                                    <span class="badge-status badge-unpaid">Full</span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <div class="card shadow">

                    <div class="card-header bg-primary">My Request Status</div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>Room</th>

                                        <th>Bill</th>

                                        <th>Status</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $id = $_SESSION['user_id'];

                                    $status = $conn->query("

                                        SELECT

                                        room_requests.*,

                                        rooms.room_number

                                        FROM room_requests

                                        INNER JOIN rooms

                                        ON room_requests.room_id=rooms.room_id

                                        WHERE room_requests.user_id='$id'

                                    ");

                                    while ($row = $status->fetch_assoc()):

                                    ?>

                                        <tr>

                                            <td>

                                                <span class="room-tag"><?php echo $row['room_number']; ?></span>

                                            </td>

                                            <td>

                                                <span class="ledger-amount">&#8369;<?php echo $row['monthly_bill']; ?></span>

                                            </td>

                                            <td>

                                                <?php

                                                if ($row['status'] == "Pending")
                                                {
                                                    echo "<span class='badge-status badge-pending'>Pending</span>";
                                                }
                                                elseif ($row['status'] == "Accepted")
                                                {
                                                    echo "<span class='badge-status badge-accepted'>Accepted</span>";
                                                }
                                                else
                                                {
                                                    echo "<span class='badge-status badge-rejected'>".$row['status']."</span>";
                                                }

                                                ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            <?php else: ?>

                <div class="card shadow mb-4">

                    <div class="card-header bg-danger">Room Requests</div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>Resident</th>

                                        <th>Room</th>

                                        <th>Bill</th>

                                        <th>Status</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $requests = $conn->query("

                                        SELECT

                                        room_requests.*,

                                        users.fullname,

                                        rooms.room_number

                                        FROM room_requests

                                        INNER JOIN users

                                        ON room_requests.user_id=users.id

                                        INNER JOIN rooms

                                        ON room_requests.room_id=rooms.room_id

                                    ");

                                    while ($row = $requests->fetch_assoc()):

                                    ?>

                                        <tr>

                                            <td><?php echo $row['fullname']; ?></td>

                                            <td>

                                                <span class="room-tag"><?php echo $row['room_number']; ?></span>

                                            </td>

                                            <td>

                                                <span class="ledger-amount">&#8369;<?php echo $row['monthly_bill']; ?></span>

                                            </td>

                                            <td>

                                                <?php

                                                if ($row['status'] == "Pending")
                                                {
                                                    echo "<span class='badge-status badge-pending'>Pending</span>";
                                                }
                                                elseif ($row['status'] == "Accepted")
                                                {
                                                    echo "<span class='badge-status badge-accepted'>Accepted</span>";
                                                }
                                                else
                                                {
                                                    echo "<span class='badge-status badge-rejected'>".$row['status']."</span>";
                                                }

                                                ?>

                                            </td>

                                            <td>

                                                <?php if ($row['status'] == "Pending"): ?>

                                                    <form method="POST">

                                                        <input
                                                            type="hidden"
                                                            name="request_id"
                                                            value="<?php echo $row['request_id']; ?>">

                                                        <input
                                                            type="hidden"
                                                            name="user_id"
                                                            value="<?php echo $row['user_id']; ?>">

                                                        <input
                                                            type="hidden"
                                                            name="room_id"
                                                            value="<?php echo $row['room_id']; ?>">

                                                        <input
                                                            type="submit"
                                                            name="approve"
                                                            value="Approve"
                                                            class="btn btn-success btn-sm">

                                                        <input
                                                            type="submit"
                                                            name="reject"
                                                            value="Reject"
                                                            class="btn btn-danger btn-sm">

                                                    </form>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <div class="card shadow">

                    <div class="card-header bg-primary">Current Residents</div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>Resident</th>

                                        <th>Current Room</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $residents = $conn->query("

                                        SELECT

                                        users.*,

                                        rooms.room_number

                                        FROM users

                                        INNER JOIN rooms

                                        ON users.assigned_room=rooms.room_id

                                        WHERE users.role='user'

                                        AND users.assigned_room IS NOT NULL

                                    ");

                                    while ($row = $residents->fetch_assoc()):

                                    ?>

                                        <tr>

                                            <td><?php echo $row['fullname']; ?></td>

                                            <td>

                                                <span class="room-tag"><?php echo $row['room_number']; ?></span>

                                            </td>

                                            <td>

                                                <form method="POST">

                                                    <input
                                                        type="hidden"
                                                        name="user_id"
                                                        value="<?php echo $row['id']; ?>">

                                                    <input
                                                        type="hidden"
                                                        name="room_id"
                                                        value="<?php echo $row['assigned_room']; ?>">

                                                    <input
                                                        type="hidden"
                                                        name="old_room"
                                                        value="<?php echo $row['assigned_room']; ?>">

                                                    <select name="new_room" class="form-select mb-2">

                                                        <?php

                                                        $roomList = $conn->query("SELECT * FROM rooms");

                                                        while ($room = $roomList->fetch_assoc()):

                                                        ?>

                                                            <option value="<?php echo $room['room_id']; ?>">

                                                                <?php echo $room['room_number']; ?>

                                                            </option>

                                                        <?php endwhile; ?>

                                                    </select>

                                                    <input
                                                        type="submit"
                                                        name="transfer"
                                                        value="Transfer"
                                                        class="btn btn-primary btn-sm">

                                                    <input
                                                        type="submit"
                                                        name="remove"
                                                        value="Remove"
                                                        class="btn btn-warning btn-sm">

                                                    <input
                                                        type="submit"
                                                        name="delete_resident"
                                                        value="Delete"
                                                        class="btn btn-danger btn-sm">

                                                </form>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
