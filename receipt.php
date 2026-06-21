<?php

session_start();

include "includes/db.php";

if (!isset($_SESSION['loggedin']))
{
    header("Location: login.php");

    exit();
}

$id = $_SESSION['user_id'];

$list = $conn->query("
    SELECT *

    FROM billing

    WHERE user_id='$id'

    ORDER BY billing_id DESC
");

$total = 0;

?>

<!DOCTYPE html>

<html>

<head>

<title>

Receipt

</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link href="assets/css/style.css" rel="stylesheet">

</head>

<body class="de-shell">

<div class="de-topbar no-print">

<a class="de-brand" href="dashboard.php">

<span class="de-brand-mark">⌗</span>

DormEase

</a>

<div class="de-topbar-right">

<a href="dashboard.php" class="de-btn de-btn-ghost" style="color:#F5F2EC; border-color:rgba(245,242,236,0.35);">&larr; Dashboard</a>

</div>

</div>

<div class="de-main">

<div class="de-print-sheet">

<div class="de-print-head">

<div>

<h2 style="margin:0;">⌗ DormEase</h2>

<p class="de-muted" style="margin:4px 0 0;">Official Statement of Account</p>

</div>

<div style="text-align:right;">

<p class="de-muted" style="margin:0;">Resident</p>

<strong><?php echo htmlspecialchars($_SESSION['fullname']); ?></strong>

</div>

</div>

<div class="de-table-wrap">

<table class="de-table">

<tr>

<th>Month</th>

<th>Amount</th>

<th>Status</th>

</tr>

<?php

while ($row = $list->fetch_assoc())
{

$total = $total + $row['amount'];

?>

<tr>

<td><?php echo htmlspecialchars($row['month']); ?></td>

<td><span class="ledger-amount">&#8369;<?php echo number_format($row['amount'], 2); ?></span></td>

<td>

<?php

if ($row['status'] == 'Paid')
{
?>

<span class="de-badge de-badge-good">Paid</span>

<?php
}
elseif ($row['status'] == 'Pending')
{
?>

<span class="de-badge de-badge-pending">Pending</span>

<?php
}
else
{
?>

<span class="de-badge de-badge-bad"><?php echo htmlspecialchars($row['status']); ?></span>

<?php
}
?>

</td>

</tr>

<?php

}

?>

</table>

</div>

<div class="de-flex-between" style="margin-top:18px; border-top:2px dashed var(--line); padding-top:14px;">

<span class="de-muted">Total recorded payments</span>

<h3 style="margin:0;">&#8369;<?php echo number_format($total, 2); ?></h3>

</div>

<p class="de-muted" style="margin-top:24px; font-size:0.78rem;">Generated on <?php echo date("F j, Y"); ?> &bull; DormEase Dormitory Management System</p>

<div class="de-actions no-print">

<button onclick="window.print()" class="de-btn de-btn-clay">🖨️ Print Receipt</button>

<a href="billing.php" class="de-btn de-btn-ghost">Back to Billing</a>

</div>

</div>

</div>

</body>

</html>
