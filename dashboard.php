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

<title>Dashboard</title>
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

  --r: 26px;
}

  body {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    min-height: 100vh;
    color: var(--text-1);
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
  }

  /* ── Ambient background orbs ── */
  body::before {
    content: '';
    position: fixed;
    top: -20vh; left: -10vw;
    width: 60vw; height: 60vh;
    background: radial-gradient(ellipse, rgba(255,55,95,0.18) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
  }

  body::after {
    content: '';
    position: fixed;
    bottom: -20vh; right: -10vw;
    width: 55vw; height: 55vh;
    background: radial-gradient(ellipse, rgba(10,132,255,0.10) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
  }

  /* ── Navbar ── */
  .navbar {
    background: rgba(10,10,15,0.72) !important;
    backdrop-filter: blur(28px) saturate(180%);
    -webkit-backdrop-filter: blur(28px) saturate(180%);
    border-bottom: 1px solid var(--border-2);
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

  .brand-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: var(--red);
    box-shadow: 0 0 8px rgba(255,55,95,0.7);
    display: inline-block;
  }

  .navbar-logo {
    width: 70px; height: 70px;
    object-fit: contain;
    border-radius: 8px;
  }

  .nav-right {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* Glass user pill */
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
    width: 35px; height: 35px;
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

  .billing-card {
  background:
    linear-gradient(
      rgba(0,0,0,0.25),
      rgba(0,0,0,0.25)
    ),
    url('image/a2.png') center center no-repeat !important;

  background-size: cover !important;

  position: relative;
}

.billing-card:hover {
  background:
    linear-gradient(
      rgba(255,255,255,0.20),
      rgba(255,255,255,0.20)
    ),
    url('image/a2.png') center center no-repeat !important;

  background-size: cover !important;

  transform: translateY(-5px) scale(1.01);

  box-shadow:
    0 12px 40px rgba(0,0,0,0.25),
    0 2px 8px rgba(0,0,0,0.15);
}

.billing-card::before,
.billing-card:hover::before,
.billing-card:hover::after {
  display: none;
}

.billing-card .tile-title {
  font-size: 34px;
  font-weight: 800;

  color: #FFFFFF;

  text-shadow:
    0 2px 8px rgba(0,0,0,0.7);
}

.billing-card .tile-tag {
  font-size: 18px;
  font-weight: 600;

  color: #FFFFFF;

  text-shadow:
    0 2px 8px rgba(0,0,0,0.7);
}

.billing-card .tile-cta {
  font-size: 20px;
  font-weight: 800;

  color: #5AC8FA;

  text-shadow:
    0 2px 8px rgba(0,0,0,0.7);
}

  /* Apple-style glass buttons */
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
    position: relative;
    white-space: nowrap;
  }

  .maintenance-card {
  background:
    linear-gradient(
      rgba(0,0,0,0.25),
      rgba(0,0,0,0.25)
    ),
    url('image/a3.png') center center no-repeat !important;

  background-size: cover !important;

  position: relative;
}

.maintenance-card:hover {
  background:
    linear-gradient(
      rgba(255,255,255,0.20),
      rgba(255,255,255,0.20)
    ),
    url('image/a3.png') center center no-repeat !important;

  background-size: cover !important;

  transform: translateY(-5px) scale(1.01);

  box-shadow:
    0 12px 40px rgba(0,0,0,0.25),
    0 2px 8px rgba(0,0,0,0.15);
}

.maintenance-card::before,
.maintenance-card:hover::before,
.maintenance-card:hover::after {
  display: none;
}

.maintenance-card .tile-title {
  font-size: 34px;
  font-weight: 800;

  color: #FFFFFF;

  text-shadow:
    0 2px 8px rgba(0,0,0,0.7);
}

.maintenance-card .tile-tag {
  font-size: 18px;
  font-weight: 600;

  color: #FFFFFF;

  text-shadow:
    0 2px 8px rgba(0,0,0,0.7);
}

.maintenance-card .tile-cta {
  font-size: 20px;
  font-weight: 800;

  color: #BF5AF2;

  text-shadow:
    0 2px 8px rgba(0,0,0,0.7);
}

  .btn-glass-outline {
    color: #FFFFFF;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.14);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.12),
      0 1px 4px rgba(0,0,0,0.3);
  }

  .btn-glass-outline:hover {
    color: #fff;
    background: rgba(255,255,255,0.13);
    border-color: rgba(255,255,255,0.22);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.18),
      0 2px 12px rgba(0,0,0,0.4);
  }

  .btn-glass-red {
    color: #fff;
    background: linear-gradient(135deg, rgba(255,55,95,0.85), rgba(215,0,64,0.85));
    border: 1px solid rgba(255,55,95,0.4);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.2),
      0 2px 14px rgba(255,55,95,0.35),
      0 1px 4px rgba(0,0,0,0.3);
    backdrop-filter: blur(8px);
  }

  .btn-glass-red:hover {
    color: #fff;
    background: linear-gradient(135deg, rgba(255,55,95,1), rgba(215,0,64,1));
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.25),
      0 4px 20px rgba(255,55,95,0.5),
      0 1px 6px rgba(0,0,0,0.4);
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
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
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
    width: 18px; height: 2px;
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

  .welcome-name span {
  color: #222222;
  background: none;

  -webkit-background-clip: unset;
  -webkit-text-fill-color: #222222;
  background-clip: unset;
}

  .welcome-time {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-3);
    margin-top: 8px;
  }

  /* ── Room Banner ── */
  .room-banner {
    background: rgba(255,55,95,0.10);
    border: 1px solid rgba(255,55,95,0.22);
    border-radius: var(--r);
    padding: 1.2rem 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 14px;
    backdrop-filter: blur(16px);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.08),
      0 2px 16px rgba(255,55,95,0.10);
  }

  .room-icon-wrap {
    width: 46px; height: 46px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--red), var(--red-dark));
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(255,55,95,0.4);
  }

  .room-banner-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255,55,95,0.8);
    margin-bottom: 3px;
  }

  .room-banner-value {
  font-size: 19px;
  font-weight: 800;
  color: #222222;
  letter-spacing: -0.3px;
}

  /* ── Section label ── */
  .section-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: var(--text-3);
    margin-bottom: 1rem;
  }

  /* ── Glass Tiles ── */
  .tile {
    position: relative;
    border-radius: var(--r);
    padding: 1.6rem 1.5rem;
    text-decoration: none !important;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    height: 100%;
    cursor: pointer;
    

    /* Apple glassmorphism */
    background: var(--surface);
    border: 1px solid var(--border);
    backdrop-filter: blur(24px) saturate(160%);
    -webkit-backdrop-filter: blur(24px) saturate(160%);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.10),
      inset 0 -1px 0 rgba(0,0,0,0.2),
      0 4px 24px rgba(0,0,0,0.28),
      0 1px 4px rgba(0,0,0,0.18);

    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
  }

.tile.wide .tile-cta {
  margin-top: 0;
  margin-left: auto;
  white-space: nowrap;
}
.top-card .tile {
  height: 380px;
}


  /* Top gloss line */
  .tile::before {
    content: '';
    position: absolute;
    top: 0; left: 12px; right: 12px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
    border-radius: 1px;
  }

  .tile:hover {
  transform: translateY(-5px) scale(1.01);

  box-shadow:
    0 12px 40px rgba(0,0,0,0.25),
    0 2px 8px rgba(0,0,0,0.15);
}


  
  .tile:active {
    transform: translateY(-2px) scale(1.005);
  }

  /* Colored glow strip bottom-left */
  .tile::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    border-radius: 0 0 var(--r) var(--r);
    opacity: 0;
    transition: opacity 0.2s;
  }

  .tile:hover::after { opacity: 1; }

  .tile.c-blue::after    { background: var(--blue); box-shadow: 0 0 14px var(--blue); }
  .tile.c-green::after   { background: var(--green); box-shadow: 0 0 14px var(--green); }
  .tile.c-orange::after  { background: var(--orange); box-shadow: 0 0 14px var(--orange); }
  .tile.c-teal::after    { background: var(--teal); box-shadow: 0 0 14px var(--teal); }
  .tile.c-purple::after  { background: var(--purple); box-shadow: 0 0 14px var(--purple); }

  .tile-icon-wrap {
    width: 50px; height: 50px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
    margin-bottom: 1.1rem;
    flex-shrink: 0;
    position: relative;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.15);
  }

  .tile.c-blue   .tile-icon-wrap { background: rgba(10,132,255,0.18); border: 1px solid rgba(10,132,255,0.25); }
  .tile.c-green  .tile-icon-wrap { background: rgba(48,209,88,0.15);  border: 1px solid rgba(48,209,88,0.22); }
  .tile.c-orange .tile-icon-wrap { background: rgba(255,159,10,0.16); border: 1px solid rgba(255,159,10,0.24); }
  .tile.c-teal   .tile-icon-wrap { background: rgba(90,200,250,0.15); border: 1px solid rgba(90,200,250,0.22); }
  .tile.c-purple .tile-icon-wrap { background: rgba(191,90,242,0.16); border: 1px solid rgba(191,90,242,0.24); }

  .choose-room-card .tile-title {
  font-size: 34px;
  font-weight: 800;
  color: #FFFFFF;

  text-shadow:
    0 2px 8px rgba(0,0,0,0.7);

  margin-top: auto;
}

.choose-room-card .tile-tag {
  font-size: 18px;
  font-weight: 600;

  color: #FFFFFF;

  text-shadow:
    0 2px 8px rgba(0,0,0,0.7);
}

.choose-room-card .tile-cta {
  font-size: 20px;
  font-weight: 800;

  color: #00ff55;

  text-shadow:
    0 2px 8px rgba(0,0,0,0.7);
}

  .tile-tag {
    font-size: 12.5px;
    font-weight: 500;
    color: var(--text-3);
    display: block;
    flex: 1;
    line-height: 1.4;
  }

  .choose-room-card {
  background:
    linear-gradient(
      rgba(0,0,0,0.25),
      rgba(0,0,0,0.25)
    ),
    url('image/a1.png') center center no-repeat !important;

  background-size: cover !important;

  position: relative;
}

.choose-room-card:hover {
  background:
    url('image/a1.png') center center no-repeat !important;

  background-size: cover !important;
}

.choose-room-card::before,
.choose-room-card:hover::before,
.choose-room-card:hover::after {
  display: none;
}

  .tile-cta {
    margin-top: 1.2rem;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.02em;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .tile.c-blue   .tile-cta { color: var(--blue); }
  .tile.c-green  .tile-cta { color: var(--green); }
  .tile.c-orange .tile-cta { color: var(--orange); }
  .tile.c-teal   .tile-cta { color: var(--teal); }
  .tile.c-purple .tile-cta { color: var(--purple); }

  /* ── Wide tile ── */
  .tile.wide {
    flex-direction: row;
    align-items: center;
    gap: 16px;
    padding: 1.3rem 1.6rem;
  }

  .tile.wide .tile-icon-wrap { margin-bottom: 0; flex-shrink: 0; }
  .tile.wide .tile-text { flex: 1; }
  .tile.wide .tile-cta { margin-top: 0; margin-left: auto; white-space: nowrap; }

  /* ── Divider ── */
  .glass-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--border), transparent);
    margin: 2rem 0;
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
    <div class="nav-right">
      <div class="user-pill d-none d-sm-flex">
        <div class="user-avatar">
          <?php echo strtoupper(substr($_SESSION['fullname'], 0, 1)); ?>
        </div>
        <span class="user-pill-name"><?php echo $_SESSION['fullname']; ?></span>
      </div>
      <a href="account.php" class="btn-glass btn-glass-outline">My Account</a>
      <a href="logout.php" class="btn-glass btn-glass-red">Logout</a>
    </div>
  </div>
</nav>

<!-- ── Page ── -->
<div class="page-wrap">

  <!-- Welcome Hero -->
  <div class="welcome-hero">
    <div>
      <div class="welcome-eyebrow">Front Desk — Dashboard</div>
      <div class="welcome-name">Welcome back,<br><span><?php echo $_SESSION['fullname']; ?></span></div>
      <div class="welcome-time" id="live-time"></div>
    </div>
  </div>

  <?php if ($_SESSION['role'] == "admin"): ?>

    <p class="section-label">Admin Controls</p>

    <div class="row g-3">

      <div class="col-md-4 col-6 top-card">
        <a href="account.php" class="tile c-blue">
          <div class="tile-icon-wrap">👥</div>
          <div class="tile-title">Manage Accounts</div>
          <span class="tile-tag">Residents on file</span>
          <span class="tile-cta">Open →</span>
        </a>
      </div>

      <div class="col-md-4 col-6 top-card">
        <a href="manageroom.php" class="tile c-green">
          <div class="tile-title">Manage Rooms</div>
          <span class="tile-tag">Add, edit, retire rooms</span>
          <span class="tile-cta">Open →</span>
        </a>
      </div>

      <div class="col-md-4 col-6 top-card">
        <a href="rooms.php" class="tile c-orange">
          <div class="tile-icon-wrap">📋</div>
          <div class="tile-title">Room Requests</div>
          <span class="tile-tag">Approve or reject</span>
          <span class="tile-cta">Open →</span>
        </a>
      </div>

      <div class="col-md-6 col-6">
        <a href="billing.php" class="tile c-teal">
          <div class="tile-icon-wrap">🧾</div>
          <div class="tile-title">Billing</div>
          <span class="tile-tag">Track monthly dues</span>
          <span class="tile-cta">Open →</span>
        </a>
      </div>

      <div class="col-md-6 col-12">
        <a href="maintenance.php" class="tile c-purple maintenance-card">
          <div class="tile-title">Maintenance</div>
          <span class="tile-tag">Open repair tickets</span>
        </a>
      </div>

    </div>

  <?php else: ?>

    <!-- Room Banner -->
    <div class="room-banner">
      <div class="room-icon-wrap">🔑</div>
      <div>
        <div class="room-banner-label">Current Room</div>
        <div class="room-banner-value"><?php echo $currentRoom; ?></div>
      </div>
    </div>

    <p class="section-label">Resident Menu</p>

    <div class="row g-3">

      <div class="col-md-4 col-6 top-card">
  <a href="rooms.php" class="tile c-green choose-room-card">
          <div class="tile-title">Choose Room</div>
          <span class="tile-tag">Browse vacancies</span>
        </a>
      </div>

      <div class="col-md-4 col-6 top-card">
  <a href="billing.php" class="tile c-teal billing-card">
          <div class="tile-title">My Billing</div>
          <span class="tile-tag">Dues and receipts</span>
        </a>
      </div>

      <div class="col-md-4 col-6 top-card">
  <a href="maintenance.php" class="tile c-purple maintenance-card">
          <div class="tile-title">Maintenance</div>
          <span class="tile-tag">Report an issue</span>
        </a>
      </div>

      <div class="col-12">
        <a href="rooms.php" class="tile c-orange wide">
          <div class="tile-icon-wrap">📋</div>
          <div class="tile-text">
            <div class="tile-title">My Request Status</div>
            <span class="tile-tag">Track your application</span>
          </div>
          <span class="tile-cta">Track →</span>
        </a>
      </div>

    </div>

  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  function updateTime() {
    const now = new Date();
    const opts = { weekday: 'long', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    document.getElementById('live-time').textContent = now.toLocaleDateString('en-US', opts);
  }
  updateTime();
  setInterval(updateTime, 30000);
</script>

</body> 
</html>