<?php
// sidebar.php — shared admin/user sidebar navigation
// Usage: include "includes/sidebar.php"; (after session_start and role check)
// $activePage must be set before including this file.
// Example: $activePage = "dashboard";
?>

<div class="de-sidebar" id="deSidebar">

    <div class="de-sidebar-brand">

        <span class="de-sidebar-brand-dot"></span>

        <div>

            <div class="de-sidebar-brand-name">DormEase</div>

            <div class="de-sidebar-role">

                <?php echo ($_SESSION['role'] == "admin") ? "Admin Panel" : "Resident Portal"; ?>

            </div>

        </div>

    </div>

    <nav class="de-sidebar-nav">

        <div class="de-nav-section">Overview</div>

        <a
            href="dashboard.php"
            class="de-nav-link <?php echo ($activePage == 'dashboard') ? 'active' : ''; ?>">

            <span class="nav-icon">&#9783;</span>

            Dashboard

        </a>

        <?php if ($_SESSION['role'] == "admin"): ?>

            <div class="de-nav-section">Management</div>

            <a
                href="account.php"
                class="de-nav-link <?php echo ($activePage == 'account') ? 'active' : ''; ?>">

                <span class="nav-icon">&#9965;</span>

                Accounts

            </a>

            <a
                href="manageroom.php"
                class="de-nav-link <?php echo ($activePage == 'manageroom') ? 'active' : ''; ?>">

                <span class="nav-icon">&#9638;</span>

                Manage Rooms

            </a>

            <a
                href="rooms.php"
                class="de-nav-link <?php echo ($activePage == 'rooms') ? 'active' : ''; ?>">

                <span class="nav-icon">&#9776;</span>

                Room Requests

            </a>

            <a
                href="billing.php"
                class="de-nav-link <?php echo ($activePage == 'billing') ? 'active' : ''; ?>">

                <span class="nav-icon">&#9783;</span>

                Billing

            </a>

            <a
                href="maintenance.php"
                class="de-nav-link <?php echo ($activePage == 'maintenance') ? 'active' : ''; ?>">

                <span class="nav-icon">&#9874;</span>

                Maintenance

            </a>

        <?php else: ?>

            <div class="de-nav-section">My Account</div>

            <a
                href="rooms.php"
                class="de-nav-link <?php echo ($activePage == 'rooms') ? 'active' : ''; ?>">

                <span class="nav-icon">&#9638;</span>

                Rooms

            </a>

            <a
                href="billing.php"
                class="de-nav-link <?php echo ($activePage == 'billing') ? 'active' : ''; ?>">

                <span class="nav-icon">&#9783;</span>

                My Billing

            </a>

            <a
                href="maintenance.php"
                class="de-nav-link <?php echo ($activePage == 'maintenance') ? 'active' : ''; ?>">

                <span class="nav-icon">&#9874;</span>

                Maintenance

            </a>

            <a
                href="account.php"
                class="de-nav-link <?php echo ($activePage == 'account') ? 'active' : ''; ?>">

                <span class="nav-icon">&#9965;</span>

                My Profile

            </a>

        <?php endif; ?>

    </nav>

    <div class="de-sidebar-footer">

        <div class="de-sidebar-user">

            <div class="de-sidebar-avatar">

                <?php echo strtoupper(substr($_SESSION['fullname'], 0, 1)); ?>

            </div>

            <div>

                <div class="de-sidebar-username">

                    <?php echo htmlspecialchars($_SESSION['fullname']); ?>

                </div>

                <a href="logout.php" class="de-sidebar-logout">Log out</a>

            </div>

        </div>

    </div>

</div>
