<?php

$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'staff';

$currentPage = $_GET['page'] ?? 'dashboard';

?>

<style>

    .sidebar {
        width: 250px;
        min-height: 100vh;
        background: #212529;
        color: #fff;
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        z-index: 1000;
        overflow-y: auto;
    }

    .sidebar-brand {
        padding: 22px 20px;
        font-size: 20px;
        font-weight: 700;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .sidebar-brand small {
        display: block;
        font-size: 11px;
        font-weight: 400;
        color: #adb5bd;
        margin-top: 3px;
    }

    .sidebar-user {
        padding: 18px 20px;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .sidebar-user-name {
        font-weight: 600;
        font-size: 14px;
    }

    .sidebar-user-role {
        color: #adb5bd;
        font-size: 12px;
        text-transform: capitalize;
        margin-top: 3px;
    }

    .sidebar-section {
        padding: 18px 20px 7px;
        color: #6c757d;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
    }

    .sidebar-menu {
        padding: 5px 10px;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 12px;
        margin-bottom: 3px;
        color: #adb5bd;
        text-decoration: none;
        border-radius: 7px;
        font-size: 14px;
        transition: .15s ease;
    }

    .sidebar-menu a:hover {
        background: #343a40;
        color: #fff;
    }

    .sidebar-menu a.active {
        background: #0d6efd;
        color: #fff;
    }

    .sidebar-icon {
        width: 20px;
        text-align: center;
        font-size: 15px;
    }

    .sidebar-bottom {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 10px;
        border-top: 1px solid rgba(255,255,255,.08);
        background: #212529;
    }

    .sidebar-logout {
        color: #ff6b6b !important;
    }

    .sidebar-logout:hover {
        background: rgba(220,53,69,.15) !important;
        color: #ff8787 !important;
    }

    @media (max-width: 767px) {

        .sidebar {
            width: 220px;
        }

    }

</style>


<aside class="sidebar">

    <!-- BRAND -->

    <div class="sidebar-brand">

        LoanSaaS

        <small>
            Loan Management System
        </small>

    </div>


    <!-- USER -->

    <div class="sidebar-user">

        <div class="sidebar-user-name">

            <?= htmlspecialchars($userName) ?>

        </div>

        <div class="sidebar-user-role">

            <?= htmlspecialchars(
                str_replace('_', ' ', $userRole)
            ) ?>

        </div>

    </div>


    <!-- ==========================================================
         SUPER ADMIN SIDEBAR
         ========================================================== -->

    <?php if ($userRole === 'super_admin'): ?>

        <div class="sidebar-section">
            System
        </div>

        <div class="sidebar-menu">

            <a
                href="<?= BASE_URL ?>/index.php?page=dashboard"
                class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▦</span>
                Dashboard
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=registration_requests"
                class="<?= $currentPage === 'registration_requests' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">✓</span>
                Registration Requests
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=system_users"
                class="<?= $currentPage === 'system_users' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">♟</span>
                System Users
            </a>

        </div>


        <div class="sidebar-section">
            Management
        </div>

        <div class="sidebar-menu">

            <a
                href="<?= BASE_URL ?>/index.php?page=businesses"
                class="<?= $currentPage === 'businesses' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▤</span>
                Businesses
            </a>

        </div>


    <!-- ==========================================================
         ADMIN SIDEBAR
         ========================================================== -->

    <?php elseif ($userRole === 'admin'): ?>

        <div class="sidebar-section">
            Main
        </div>

        <div class="sidebar-menu">

            <a
                href="<?= BASE_URL ?>/index.php?page=dashboard"
                class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▦</span>
                Dashboard
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=borrowers"
                class="<?= in_array(
                    $currentPage,
                    [
                        'borrowers',
                        'borrower_create',
                        'borrower_view',
                        'borrower_edit'
                    ],
                    true
                ) ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">♙</span>
                Borrowers
            </a>

            

            <a
                href="<?= BASE_URL ?>/index.php?page=loans"
                class="<?= $currentPage === 'loans' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▣</span>
                Loans
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=payments"
                class="<?= $currentPage === 'payments' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">₱</span>
                Payments
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=accounts"
                class="<?= $currentPage === 'accounts' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▤</span>
                Accounts
            </a>


              <a
                href="<?= BASE_URL ?>/index.php?page=expenses"
                class="<?= $currentPage === 'expenses' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▤</span>
                Expenses
            </a>

        </div>


        <div class="sidebar-section">
            Reports
        </div>

        <div class="sidebar-menu">

            <a
                href="<?= BASE_URL ?>/index.php?page=reports"
                class="<?= $currentPage === 'reports' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▥</span>
                Reports
            </a>

        </div>


        <div class="sidebar-section">
            Administration
        </div>

        <div class="sidebar-menu">

            <a
                href="<?= BASE_URL ?>/index.php?page=staff"
                class="<?= $currentPage === 'staff' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">♟</span>
                Staff
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=settings"
                class="<?= $currentPage === 'settings' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">⚙</span>
                Settings
            </a>

        </div>


    <!-- ==========================================================
         STAFF SIDEBAR
         ========================================================== -->

    <?php elseif ($userRole === 'staff'): ?>

        <div class="sidebar-section">
            Main
        </div>

        <div class="sidebar-menu">

            <a
                href="<?= BASE_URL ?>/index.php?page=dashboard"
                class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▦</span>
                Dashboard
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=borrowers"
                class="<?= in_array(
                    $currentPage,
                    [
                        'borrowers',
                        'borrower_create',
                        'borrower_view',
                        'borrower_edit'
                    ],
                    true
                ) ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">♙</span>
                Borrowers
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=loans"
                class="<?= $currentPage === 'loans' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▣</span>
                Loans
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=payments"
                class="<?= $currentPage === 'payments' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">₱</span>
                Payments
            </a>

            <a
                href="<?= BASE_URL ?>/index.php?page=accounts"
                class="<?= $currentPage === 'accounts' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▤</span>
                Accounts
            </a>

        </div>


        <div class="sidebar-section">
            Reports
        </div>

        <div class="sidebar-menu">

            <a
                href="<?= BASE_URL ?>/index.php?page=reports"
                class="<?= $currentPage === 'reports' ? 'active' : '' ?>"
            >
                <span class="sidebar-icon">▥</span>
                Reports
            </a>

        </div>

    <?php endif; ?>


    <!-- ==========================================================
         LOGOUT
         ========================================================== -->

    <div class="sidebar-bottom">

        <div class="sidebar-menu p-0">

            <a
                href="<?= BASE_URL ?>/index.php?page=logout"
                class="sidebar-logout"
            >

                <span class="sidebar-icon">↪</span>

                Logout

            </a>

        </div>

    </div>

</aside>