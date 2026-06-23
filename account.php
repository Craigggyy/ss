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

if(isset($_POST['update_profile']))
{
    $fullname = $_POST['fullname'];

    $email = $_POST['email'];

    $conn->query("

        UPDATE users

        SET

        fullname='$fullname',

        email='$email'

        WHERE id='$id'

    ");

    $_SESSION['fullname'] = $fullname;

    $_SESSION['email'] = $email;

    $message = "Profile Updated.";
}

if(isset($_POST['upload_id']))
{
    $filename = $_FILES['studentid']['name'];

    $tmp = $_FILES['studentid']['tmp_name'];

    move_uploaded_file(

        $tmp,

        "uploads/studentid/".$filename

    );

    $conn->query("

        UPDATE users

        SET student_id='$filename'

        WHERE id='$id'

    ");

    $message = "Student ID Uploaded.";
}

if(isset($_GET['delete']))
{
    if($_SESSION['role'] == "admin")
    {
        $deleteID = $_GET['delete'];

        $conn->query("

            DELETE FROM users

            WHERE id='$deleteID'

            AND role='user'

        ");

        header("Location: account.php");

        exit();
    }
}

if(isset($_POST['edit_resident']))
{
    if($_SESSION['role'] == "admin")
    {
        $residentID = $_POST['resident_id'];

        $fullname = $_POST['fullname'];

        $email = $_POST['email'];

        $conn->query("

            UPDATE users

            SET

            fullname='$fullname',

            email='$email'

            WHERE id='$residentID'

        ");

        $message = "Resident Updated.";
    }
}

$user = $conn->query("

    SELECT *

    FROM users

    WHERE id='$id'

");

$userData = $user->fetch_assoc();

$residents = $conn->query("

    SELECT *

    FROM users

    WHERE role='user'

");

?>

<!DOCTYPE html>
<html>
<head>

<title>Account — DormEase</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --red:        #FF375F;
    --red-dark:   #D70040;
    --bg:         #F8F9FB;
    --surface:    rgba(255,255,255,0.92);
    --surface-2:  rgba(255,255,255,1);
    --border:     rgba(0,0,0,0.10);
    --border-2:   rgba(0,0,0,0.08);
    --text-1:     #222222;
    --text-2:     #555555;
    --text-3:     #888888;
    --green:      #30D158;
    --blue:       #0A84FF;
    --orange:     #FF9F0A;
    --purple:     #BF5AF2;
    --teal:       #5AC8FA;
    --r:          26px;
  }

  body {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: url('image/bg.png') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    color: var(--text-1);
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
  }

  /* ── Navbar ── */
  .navbar {
    background: rgba(10,10,15,0.72) !important;
    backdrop-filter: blur(28px) saturate(180%);
    -webkit-backdrop-filter: blur(28px) saturate(180%);
    border-bottom: 1px solid rgba(0,0,0,0.08);
    padding: 0;
    position: sticky;
    top: 0;
    z-index: 200;
  }

  .navbar .container {
    height: 90px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1000px;
  }

  .navbar-brand {
    font-size: 30px;
    font-weight: 800;
    color: #fff !important;
    letter-spacing: -0.3px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 9px;
  }

  .navbar-logo {
    width: 70px;
    height: 70px;
    object-fit: contain;
    border-radius: 8px;
  }

  /* Glass buttons */
  .btn-glass {
    font-size: 16px;
    font-weight: 600;
    border-radius: 999px;
    padding: 8px 18px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.18s ease;
    white-space: nowrap;
    border: none;
  }

  .btn-glass-outline {
    color: #fff;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.14);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.12), 0 1px 4px rgba(0,0,0,0.3);
  }

  .btn-glass-outline:hover {
    color: #fff;
    background: rgba(255,255,255,0.13);
    border-color: rgba(255,255,255,0.22);
  }

  .btn-glass-red {
    color: #fff;
    background: linear-gradient(135deg, rgba(255,55,95,0.85), rgba(215,0,64,0.85));
    border: 1px solid rgba(255,55,95,0.4) !important;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.2), 0 2px 14px rgba(255,55,95,0.35);
  }

  .btn-glass-red:hover {
    color: #fff;
    background: linear-gradient(135deg, #FF375F, #D70040);
    transform: translateY(-1px);
  }

  /* ── Page wrapper ── */
  .page-wrap {
    position: relative;
    z-index: 1;
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 1rem 4rem;
  }

  /* ── Welcome Hero ── */
  .welcome-hero {
    padding: 3rem 0 2.4rem;
  }

  .welcome-eyebrow {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--red);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .welcome-eyebrow::before {
    content: '';
    display: inline-block;
    width: 18px;
    height: 2px;
    background: var(--red);
    border-radius: 1px;
  }

  .welcome-name {
    font-size: 38px;
    font-weight: 800;
    color: #222222;
    letter-spacing: -1px;
    line-height: 1.1;
  }

  .section-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: var(--text-3);
    margin-bottom: 1rem;
  }

  /* ── Glass Card ── */
  .glass-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r);
    backdrop-filter: blur(24px) saturate(160%);
    -webkit-backdrop-filter: blur(24px) saturate(160%);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.10),
      0 4px 24px rgba(0,0,0,0.28),
      0 1px 4px rgba(0,0,0,0.18);
    padding: 2rem;
    position: relative;
    overflow: hidden;
  }

  .glass-card::before {
    content: '';
    position: absolute;
    top: 0; left: 12px; right: 12px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
  }

  /* ── Alert ── */
  .alert-glass {
    background: rgba(48,209,88,0.12);
    border: 1px solid rgba(48,209,88,0.28);
    border-radius: 14px;
    color: #1a6b2a;
    padding: 14px 20px;
    font-size: 15px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1.5rem;
  }

  /* ── Profile Form ── */
  .form-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-3);
    margin-bottom: 6px;
  }

  .form-control {
    background: rgba(255,255,255,0.7);
    border: 1px solid rgba(0,0,0,0.12);
    border-radius: 12px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px;
    font-weight: 500;
    color: var(--text-1);
    padding: 12px 16px;
    transition: border-color 0.15s, box-shadow 0.15s;
  }

  .form-control:focus {
    background: rgba(255,255,255,0.95);
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(10,132,255,0.14);
    outline: none;
  }

  .form-control:disabled {
    background: rgba(0,0,0,0.04);
    color: var(--text-3);
  }

  .btn-primary-glass {
    background: linear-gradient(135deg, var(--blue), #0060CC);
    color: #fff;
    border: none;
    border-radius: 999px;
    font-size: 15px;
    font-weight: 700;
    padding: 11px 28px;
    cursor: pointer;
    transition: all 0.18s ease;
    box-shadow: 0 4px 14px rgba(10,132,255,0.35);
    display: inline-flex;
    align-items: center;
    gap: 7px;
  }

  .btn-primary-glass:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(10,132,255,0.45);
  }

  .btn-success-glass {
    background: linear-gradient(135deg, var(--green), #1BAA40);
    color: #fff;
    border: none;
    border-radius: 999px;
    font-size: 15px;
    font-weight: 700;
    padding: 11px 28px;
    cursor: pointer;
    transition: all 0.18s ease;
    box-shadow: 0 4px 14px rgba(48,209,88,0.35);
    display: inline-flex;
    align-items: center;
    gap: 7px;
  }

  .btn-success-glass:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(48,209,88,0.45);
  }

  /* ── Divider ── */
  .glass-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(0,0,0,0.08), transparent);
    margin: 1.8rem 0;
  }

  /* ── Student ID Preview ── */
  .id-preview {
    display: flex;
    justify-content: center;
    margin-bottom: 1.5rem;
  }

  .id-preview img {
    width: 240px;
    height: 152px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid var(--border);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  }

  /* ── Table ── */
  .residents-table-wrap {
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid var(--border);
  }

  .residents-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    font-size: 14px;
  }

  .residents-table thead tr {
    background: linear-gradient(135deg, #141d2e, #1a2135);
  }

  .residents-table thead th {
    padding: 16px 18px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: rgba(255,255,255,0.7);
    text-align: left;
    white-space: nowrap;
  }

  .residents-table thead th:first-child {
    color: var(--red);
  }

  .residents-table tbody tr {
    border-bottom: 1px solid rgba(0,0,0,0.06);
    transition: background 0.14s;
  }

  .residents-table tbody tr:last-child {
    border-bottom: none;
  }

  .residents-table tbody tr:hover {
    background: rgba(10,132,255,0.04);
  }

  .residents-table td {
    padding: 16px 18px;
    color: var(--text-1);
    font-size: 14px;
    vertical-align: middle;
  }

  .ledger-id {
    font-weight: 800;
    color: var(--red);
    font-size: 14px;
  }

  .badge-active {
    background: rgba(48,209,88,0.14);
    color: #198d29;
    border: 1px solid rgba(48,209,88,0.25);
    padding: 5px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
  }

  .badge-inactive {
    background: rgba(255,55,95,0.12);
    color: var(--red-dark);
    border: 1px solid rgba(255,55,95,0.22);
    padding: 5px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
  }

  .btn-delete {
    background: linear-gradient(135deg, var(--red), var(--red-dark));
    color: #fff;
    border: none;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    padding: 7px 18px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s ease;
    box-shadow: 0 3px 10px rgba(255,55,95,0.3);
    white-space: nowrap;
  }

  .btn-delete:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 5px 16px rgba(255,55,95,0.45);
  }

  .view-id-link {
    color: var(--blue);
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: opacity 0.15s;
  }

  .view-id-link:hover {
    opacity: 0.75;
  }

  .text-muted-dash {
    color: var(--text-3);
    font-size: 18px;
  }

  /* ── User pill ── */
  .user-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 999px;
    padding: 5px 14px 5px 5px;
    backdrop-filter: blur(12px);
  }

  .user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--red), var(--red-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
  }

  .user-pill-name {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-2);
    white-space: nowrap;
  }

  /* ── File input ── */
  .file-input-wrap {
    background: rgba(0,0,0,0.03);
    border: 1.5px dashed rgba(0,0,0,0.14);
    border-radius: 14px;
    padding: 16px;
    transition: border-color 0.15s;
  }

  .file-input-wrap:focus-within {
    border-color: var(--blue);
  }

  .file-input-wrap .form-control {
    background: transparent;
    border: none;
    padding: 0;
    box-shadow: none;
  }

  /* Responsive table scroll */
  .table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
</style>

</head>
<body>

<!-- ── Navbar ── -->
<nav class="navbar navbar-dark">
  <div class="container">
    <a class="navbar-brand">
      <img src="image/2.png" class="navbar-logo" alt="DormEase Logo">
      DormEase
    </a>
    <div class="nav-right d-flex align-items-center gap-2">
      
      <a href="dashboard.php" class="btn-glass btn-glass-outline">Dashboard</a>
    </div>
  </div>
</nav>

<!-- ── Page ── -->
<div class="page-wrap">

  <!-- Welcome Hero -->
  <div class="welcome-hero">
  
    <div class="welcome-name">
      <?php echo $_SESSION['role'] == "admin" ? "Resident Management" : "My Profile"; ?>
    </div>
  </div>

  <?php if($message != ""): ?>
  <div class="alert-glass">
    ✓ <?php echo $message; ?>
  </div>
  <?php endif; ?>

  <?php if($_SESSION['role'] == "user"): ?>

    <!-- ── User Profile ── -->
    <p class="section-label">Profile Information</p>

    <div class="glass-card mb-4">

      <form method="POST">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" value="<?php echo $userData['fullname']; ?>" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?php echo $userData['email']; ?>" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" value="<?php echo $userData['username']; ?>" class="form-control" disabled>
          </div>
          <div class="col-12">
            <button type="submit" name="update_profile" class="btn-primary-glass">
              Save Changes
            </button>
          </div>
        </div>
      </form>

      <div class="glass-divider"></div>

      <p class="section-label">Student ID</p>

      <?php if(!empty($userData['student_id'])): ?>
      <div class="id-preview">
        <img src="uploads/studentid/<?php echo $userData['student_id']; ?>" alt="Student ID">
      </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="file-input-wrap mb-3">
          <input type="file" name="studentid" class="form-control" required>
        </div>
        <button type="submit" name="upload_id" class="btn-success-glass">
          Upload ID
        </button>
      </form>

    </div>

  <?php else: ?>

    <!-- ── Admin Residents Table ── -->
    <p class="section-label">All Residents</p>

    <div class="glass-card">
      <div class="table-scroll">
        <table class="residents-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Username</th>
              <th>Student ID</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $residents->fetch_assoc()): ?>
            <tr>
              <td><span class="ledger-id">#<?php echo $row['id']; ?></span></td>
              <td style="font-weight:600;"><?php echo $row['fullname']; ?></td>
              <td style="color:var(--text-2);"><?php echo $row['email']; ?></td>
              <td style="color:var(--text-3);font-family:monospace;"><?php echo $row['username']; ?></td>
              <td>
                <?php if(!empty($row['student_id'])): ?>
                  <a href="uploads/studentid/<?php echo $row['student_id']; ?>" target="_blank" class="view-id-link">
                    View ID ↗
                  </a>
                <?php else: ?>
                  <span class="text-muted-dash">—</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if($row['status'] == "active"): ?>
                  <span class="badge-active">Active</span>
                <?php else: ?>
                  <span class="badge-inactive"><?php echo $row['status']; ?></span>
                <?php endif; ?>
              </td>
              <td>
                <a href="account.php?delete=<?php echo $row['id']; ?>" class="btn-delete">
                  Delete
                </a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>