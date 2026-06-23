<?php

session_start();

if (!isset($_SESSION['loggedin']))
{
    header("Location: login.php");

    exit();
}

include "includes/db.php";

$activePage = "billing";

$message = "";

if ($_SESSION['role'] == "admin")
{
    $approved = $conn->query("

        SELECT *

        FROM room_requests

        WHERE status='Accepted'

    ");

    while ($row = $approved->fetch_assoc())
    {
        $userID = $row['user_id'];

        $month = date("F Y");

        $check = $conn->query("

            SELECT *

            FROM billing

            WHERE user_id='$userID'

            AND month='$month'

        ");

        if ($check->num_rows == 0)
        {
            $amount = $row['monthly_bill'];

            $conn->query("

                INSERT INTO billing

                (

                user_id,

                month,

                amount,

                status

                )

                VALUES

                (

                '$userID',

                '$month',

                '$amount',

                'Unpaid'

                )

            ");
        }
    }
}

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

<title>Billing — DormEase</title>

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

                <div class="de-page-eyebrow">Dues Ledger</div>

                <h1 class="de-page-heading">Billing</h1>

            </div>

        </div>

        <div class="de-page-content">

            <?php if ($message != ""): ?>

                <div class="alert alert-success mb-4">

                    <?php echo $message; ?>

                </div>

            <?php endif; ?>

            <?php if ($_SESSION['role'] == "user"): ?>

                <div class="card shadow">

                    <div class="card-header bg-primary">

                        My Billing

                        <a
                            href="receipt.php"
                            class="btn btn-warning btn-sm float-end">

                            &#9783; View / Print Receipt

                        </a>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>Month</th>

                                        <th>Amount</th>

                                        <th>Proof</th>

                                        <th>Status</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $id = $_SESSION['user_id'];

                                    $bills = $conn->query("

                                        SELECT *

                                        FROM billing

                                        WHERE user_id='$id'

                                    ");

                                    while ($row = $bills->fetch_assoc()):

                                    ?>

                                        <tr>

                                            <td><?php echo $row['month']; ?></td>

                                            <td>

                                                <span class="ledger-amount">&#8369;<?php echo $row['amount']; ?></span>

                                            </td>

                                            <td>

                                                <?php if ($row['proof_payment'] != ""): ?>

                                                    <a
                                                        href="uploads/payment/<?php echo $row['proof_payment']; ?>"
                                                        target="_blank">

                                                        View File

                                                    </a>

                                                <?php else: ?>

                                                    &mdash;

                                                <?php endif; ?>

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

                                                <?php if ($row['status'] == "Unpaid"): ?>

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

                                                <?php else: ?>

                                                    <span class="text-muted-soft">No action needed</span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            <?php else: ?>

                <div class="card shadow">

                    <div class="card-header bg-danger">Billing Management</div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>Resident</th>

                                        <th>Month</th>

                                        <th>Amount</th>

                                        <th>Proof</th>

                                        <th>Status</th>

                                        <th>Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $bills = $conn->query("

                                        SELECT

                                        billing.*,

                                        users.fullname

                                        FROM billing

                                        INNER JOIN users

                                        ON billing.user_id=users.id

                                    ");

                                    while ($row = $bills->fetch_assoc()):

                                    ?>

                                        <tr>

                                            <td><?php echo $row['fullname']; ?></td>

                                            <td><?php echo $row['month']; ?></td>

                                            <td>

                                                <span class="ledger-amount">&#8369;<?php echo $row['amount']; ?></span>

                                            </td>

                                            <td>

                                                <?php if ($row['proof_payment'] != ""): ?>

                                                    <a
                                                        href="uploads/payment/<?php echo $row['proof_payment']; ?>"
                                                        target="_blank">

                                                        View File

                                                    </a>

                                                <?php else: ?>

                                                    &mdash;

                                                <?php endif; ?>

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

                                                    <?php if ($row['status'] == "Unpaid"): ?>

                                                        <input
                                                            type="submit"
                                                            name="mark_paid"
                                                            value="Approve"
                                                            class="btn btn-success btn-sm">

                                                    <?php endif; ?>

                                                    <a
                                                        href="billing.php?delete=<?php echo $row['billing_id']; ?>"
                                                        class="btn btn-danger btn-sm">

                                                        Delete

                                                    </a>

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
