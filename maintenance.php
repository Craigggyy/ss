<?php

session_start();

if (!isset($_SESSION['loggedin']))
{
    header("Location: login.php");

    exit();
}

include "includes/db.php";

$message = "";

if (isset($_POST['submit_request']))
{
    $id = $_SESSION['user_id'];

    $type = $_POST['maintenance_type'];

    $description = $_POST['description'];

   $filename = "";

if(!empty($_FILES['image']['name']))
{
    if(!is_dir("uploads/maintenance"))
    {
        mkdir(

        "uploads/maintenance",

        0777,

        true

        );
    }

    $filename = time()."_".$_FILES['image']['name'];

    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file(

    $tmp,

    "uploads/maintenance/".$filename

    );
}

    $conn->query("

        INSERT INTO maintenance_requests

        (

        user_id,

        maintenance_type,

        description,

        image,

        status

        )

        VALUES

        (

        '$id',

        '$type',

        '$description',

        '$filename',

        'Pending'

        )

    ");

    $message = "Request Submitted.";
}

if ($_SESSION['role'] == "admin")
{
    if (isset($_POST['update_status']))
    {
        $requestID = $_POST['request_id'];

        $status = $_POST['status'];

        $conn->query("

            UPDATE maintenance_requests

            SET status='$status'

            WHERE request_id='$requestID'

        ");

        $message = "Status Updated.";
    }

    if (isset($_GET['delete']))
    {
        $requestID = $_GET['delete'];

        $conn->query("

            DELETE FROM maintenance_requests

            WHERE request_id='$requestID'

        ");

        header("Location: maintenance.php");

        exit();
    }
}

?>

<!DOCTYPE html>

<html>

<head>

<title>

Maintenance

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

<span class="page-eyebrow">Repair Tickets</span>

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

<div class="card shadow mb-4">

<div class="card-header bg-danger text-white">

Submit Maintenance Request

</div>

<div class="card-body">

<form
method="POST"
enctype="multipart/form-data">

<div class="mb-3">

<label>

Maintenance Type

</label>

<select
name="maintenance_type"
class="form-select"
required>

<option value="">

Select Type

</option>

<option>

Electrical

</option>

<option>

Plumbing

</option>

<option>

Air Conditioning

</option>

<option>

Door Repair

</option>

<option>

Window Repair

</option>

<option>

Furniture

</option>

<option>

Internet

</option>

<option>

Others

</option>

</select>

</div>

<div class="mb-3">

<label>

Describe The Problem

</label>

<textarea

name="description"

class="form-control"

rows="5"

placeholder="Example:

The air conditioner in Room A101 is not cooling properly."

required>

</textarea>

</div>

<div class="mb-3">

<label>

Upload Image

</label>

<input
type="file"
name="image"
class="form-control">

</div>

<input
type="submit"
name="submit_request"
value="Submit Request"
class="btn btn-danger">

</form>

</div>

</div>

<?php

if ($_SESSION['role'] == "admin")

{

?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

Maintenance Management

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>ID</th>

<th>Resident</th>

<th>Type</th>

<th>Description</th>

<th>Image</th>

<th>Status</th>

<th>Action</th>

</tr>

<?php

$requests = $conn->query("

    SELECT

    maintenance_requests.*,

    users.fullname

    FROM maintenance_requests

    INNER JOIN users

    ON maintenance_requests.user_id = users.id

");

while ($row = $requests->fetch_assoc())

{

?>

<tr>

<td>

<span class="ledger-id">#<?php echo $row['request_id']; ?></span>

</td>

<td>

<?php echo $row['fullname']; ?>

</td>

<td>

<?php echo $row['maintenance_type']; ?>

</td>

<td>

<?php echo $row['description']; ?>

</td>

<td>

<?php

if ($row['image'] != "")

{

?>

<a
href="uploads/maintenance/<?php echo $row['image']; ?>"
target="_blank">

View Image

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

if ($row['status'] == "Pending")
{
echo "<span class='badge-status badge-pending'>Pending</span>";
}
elseif ($row['status'] == "In Progress")
{
echo "<span class='badge-status badge-progress'>In Progress</span>";
}
else
{
echo "<span class='badge-status badge-completed'>Completed</span>";
}

?>

</td>

<td>

<form method="POST">

<input
type="hidden"
name="request_id"
value="<?php echo $row['request_id']; ?>">

<select
name="status"
class="form-select mb-2">

<option>

Pending

</option>

<option>

In Progress

</option>

<option>

Completed

</option>

</select>

<input
type="submit"
name="update_status"
value="Update"
class="btn btn-success btn-sm">

<a
href="maintenance.php?delete=<?php echo $row['request_id']; ?>"
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

else

{

?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

My Requests

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>ID</th>

<th>Type</th>

<th>Description</th>

<th>Status</th>

</tr>

<?php

$id = $_SESSION['user_id'];

$requests = $conn->query("

    SELECT *

    FROM maintenance_requests

    WHERE user_id='$id'

");

while ($row = $requests->fetch_assoc())

{

?>

<tr>

<td>

<span class="ledger-id">#<?php echo $row['request_id']; ?></span>

</td>

<td>

<?php echo $row['maintenance_type']; ?>

</td>

<td>

<?php echo $row['description']; ?>

</td>

<td>

<?php

if ($row['status'] == "Pending")
{
echo "<span class='badge-status badge-pending'>Pending</span>";
}
elseif ($row['status'] == "In Progress")
{
echo "<span class='badge-status badge-progress'>In Progress</span>";
}
else
{
echo "<span class='badge-status badge-completed'>Completed</span>";
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

?>

</div>

</body>

</html>