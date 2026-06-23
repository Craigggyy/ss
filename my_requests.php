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

if(isset($_POST['transfer_room']))
{
    $newRoom = $_POST['room_id'];

    $user = $conn->query("

    SELECT *

    FROM users

    WHERE id='$id'

    ");

    $userData = $user->fetch_assoc();

    $oldRoom = $userData['assigned_room'];

    if($oldRoom == $newRoom)
    {
        $message = "You are already assigned to this room.";
    }

    else

    {
        $check = $conn->query("

        SELECT *

        FROM rooms

        WHERE room_id='$newRoom'

        ");

        $room = $check->fetch_assoc();

        if($room['occupied'] >= $room['capacity'])
        {
            $message = "Room is already full.";
        }

        else

        {
            $conn->query("

            INSERT INTO room_requests

            (

            user_id,

            room_id,

            monthly_bill,

            status,

            request_type

            )

            VALUES

            (

            '$id',

            '$newRoom',

            '".$room['monthly_bill']."',

            'Pending',

            'Transfer'

            )

            ");

            $message = "Transfer request submitted.";
        }
    }
}

$history = $conn->query("

SELECT

room_requests.*,

rooms.room_number

FROM room_requests

INNER JOIN rooms

ON room_requests.room_id=rooms.room_id

WHERE room_requests.user_id='$id'

ORDER BY request_id DESC

");

?>

<!DOCTYPE html>

<html>

<head>

<title>

My Requests

</title>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

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

<div class="card shadow">

<div class="card-header bg-primary text-white">

Transfer Room Request

</div>

<div class="card-body">

<form method="POST">

<select

name="room_id"

class="form-select mb-3"

required>

<option value="">

Select Room

</option>

<?php

$rooms = $conn->query("

SELECT *

FROM rooms

");

while($row = $rooms->fetch_assoc())

{

?>

<option value="<?php echo $row['room_id']; ?>">

<?php echo $row['room_number']; ?>

</option>

<?php

}

?>

</select>

<input

type="submit"

name="transfer_room"

value="Submit Transfer Request"

class="btn btn-primary">

</form>

</div>

</div>

<div class="card shadow mt-4">

<div class="card-header bg-success text-white">

Request History

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>Room</th>

<th>Type</th>

<th>Status</th>

</tr>

<?php

while($row = $history->fetch_assoc())

{

?>

<tr>

<td>

<?php echo $row['room_number']; ?>

</td>

<td>

<?php echo $row['request_type']; ?>

</td>

<td>

<?php echo $row['status']; ?>

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

