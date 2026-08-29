<?php

$user = Auth::user();

?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        SaaS Administration | Loan Management
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f7fb;

            color: #1f2937;

        }


        /* ============================================================
           SIDEBAR
        ============================================================ */

        .superadmin-sidebar {

            position: fixed;

            top: 0;

            left: 0;

            width: 250px;

            height: 100vh;

            background: #111827;

            color: #ffffff;

            display: flex;

            flex-direction: column;

            z-index: 1000;

        }


        .sidebar-brand {

            height: 64px;

            display: flex;

            align-items: center;

            padding: 0 22px;

            border-bottom:
                1px solid
                rgba(255,255,255,0.08);

        }


        .sidebar-brand-title {

            font-size: 18px;

            font-weight: 700;

            color: #ffffff;

            white-space: nowrap;

        }


        .sidebar-brand-subtitle {

            margin-top: 2px;

            font-size: 11px;

            color: #9ca3af;

        }


        .sidebar-content {

            flex: 1;

            overflow-y: auto;

            padding: 18px 12px;

        }


        .sidebar-section {

            margin-bottom: 24px;

        }


        .sidebar-section-title {

            padding:
                0 12px
                8px;

            font-size: 10px;

            font-weight: 700;

            color: #6b7280;

            text-transform: uppercase;

            letter-spacing: 0.08em;

        }


        .sidebar-menu {

            display: flex;

            flex-direction: column;

            gap: 3px;

        }


        .sidebar-link {

            display: flex;

            align-items: center;

            gap: 11px;

            width: 100%;

            padding:
                10px
                12px;

            border-radius: 7px;

            color: #d1d5db;

            text-decoration: none;

            font-size: 13px;

            font-weight: 500;

            transition:
                background 0.2s ease,
                color 0.2s ease;

        }


        .sidebar-link:hover {

            background: #1f2937;

            color: #ffffff;

        }


        .sidebar-link.active {

            background: #374151;

            color: #ffffff;

            font-weight: 600;

        }


        .sidebar-icon {

            width: 20px;

            min-width: 20px;

            height: 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 15px;

        }


        .sidebar-footer {

            padding: 14px 12px;

            border-top:
                1px solid
                rgba(255,255,255,0.08);

        }


        .sidebar-user {

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 10px;

            margin-bottom: 5px;

        }


        .sidebar-avatar {

            width: 34px;

            height: 34px;

            border-radius: 50%;

            background: #374151;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 13px;

            font-weight: 700;

            color: #ffffff;

        }


        .sidebar-user-info {

            min-width: 0;

            flex: 1;

        }


        .sidebar-user-name {

            color: #ffffff;

            font-size: 12px;

            font-weight: 600;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

        }


        .sidebar-user-role {

            margin-top: 2px;

            color: #9ca3af;

            font-size: 10px;

            text-transform: uppercase;

        }


        .sidebar-logout {

            display: flex;

            align-items: center;

            gap: 10px;

            width: 100%;

            padding:
                10px
                12px;

            border-radius: 7px;

            color: #d1d5db;

            text-decoration: none;

            font-size: 13px;

            font-weight: 500;

        }


        .sidebar-logout:hover {

            background: #1f2937;

            color: #ffffff;

        }


        /* ============================================================
           MAIN CONTENT
        ============================================================ */

        .main-content {

            margin-left: 250px;

            min-height: 100vh;

        }


        /* ============================================================
           TOP NAVBAR
        ============================================================ */

        .navbar {

            height: 64px;

            background: #ffffff;

            border-bottom:
                1px solid
                #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 30px;

        }


        .navbar-title {

            font-size: 16px;

            font-weight: 600;

            color: #111827;

        }


        .user-area {

            display: flex;

            align-items: center;

            gap: 14px;

        }


        .user-name {

            font-size: 13px;

            color: #374151;

        }


        .role {

            background: #f3f4f6;

            color: #374151;

            padding:
                6px
                11px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 600;

            text-transform: uppercase;

        }


        .logout {

            color: #374151;

            text-decoration: none;

            font-size: 13px;

            font-weight: 600;

        }


        .logout:hover {

            color: #111827;

            text-decoration: underline;

        }


        /* ============================================================
           CONTAINER
        ============================================================ */

        .container {

            max-width: 1400px;

            margin: 0 auto;

            padding: 30px;

        }


        /* ============================================================
           WELCOME
        ============================================================ */

        .welcome {

            background: #ffffff;

            border:
                1px solid
                #e5e7eb;

            border-radius: 12px;

            padding: 30px;

            margin-bottom: 25px;

            box-shadow:
                0 1px 2px
                rgba(0, 0, 0, 0.04);

        }


        .welcome h1 {

            margin:
                0 0 8px 0;

            font-size: 28px;

            color: #111827;

        }


        .welcome p {

            margin: 0;

            color: #6b7280;

            font-size: 14px;

        }


        /* ============================================================
           CARDS
        ============================================================ */

        .cards {

            display: grid;

            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(220px, 1fr)
                );

            gap: 20px;

        }


        .card {

            background: #ffffff;

            border:
                1px solid
                #e5e7eb;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 1px 2px
                rgba(0, 0, 0, 0.04);

        }


        .card-title {

            color: #6b7280;

            font-size: 13px;

            margin-bottom: 12px;

        }


        .card-value {

            font-size: 30px;

            font-weight: 700;

            color: #111827;

        }


        /* ============================================================
           SECTION
        ============================================================ */

        .section {

            margin-top: 30px;

            background: #ffffff;

            border:
                1px solid
                #e5e7eb;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 1px 2px
                rgba(0, 0, 0, 0.04);

        }


        .section h2 {

            margin:
                0 0 10px;

            font-size: 18px;

            color: #111827;

        }


        .section p {

            margin: 0;

            color: #6b7280;

            font-size: 14px;

            line-height: 1.6;

        }


        /* ============================================================
           MOBILE BUTTON
        ============================================================ */

        .mobile-menu-button {

            display: none;

            width: 36px;

            height: 36px;

            border: 1px solid #e5e7eb;

            background: #ffffff;

            border-radius: 7px;

            cursor: pointer;

            font-size: 18px;

            color: #374151;

        }


        /* ============================================================
           SIDEBAR OVERLAY
        ============================================================ */

        .sidebar-overlay {

            display: none;

            position: fixed;

            inset: 0;

            background:
                rgba(0,0,0,0.4);

            z-index: 999;

        }


        /* ============================================================
           RESPONSIVE
        ============================================================ */

        @media (max-width: 900px) {

            .superadmin-sidebar {

                transform:
                    translateX(-100%);

                transition:
                    transform 0.25s ease;

            }


            .superadmin-sidebar.open {

                transform:
                    translateX(0);

            }


            .sidebar-overlay.active {

                display: block;

            }


            .main-content {

                margin-left: 0;

            }


            .mobile-menu-button {

                display: flex;

                align-items: center;

                justify-content: center;

            }


            .navbar {

                padding:
                    0 18px;

            }


            .navbar-title {

                display: none;

            }


            .user-name {

                display: none;

            }


            .container {

                padding: 18px;

            }


            .welcome {

                padding: 22px;

            }


            .welcome h1 {

                font-size: 24px;

            }

        }


        @media (max-width: 600px) {

            .cards {

                grid-template-columns: 1fr;

            }


            .card {

                padding: 20px;

            }


            .section {

                padding: 20px;

            }


            .role {

                display: none;

            }

        }

    </style>

</head>


<body>


<!-- ================================================================
     SIDEBAR
================================================================ -->

<aside
    class="superadmin-sidebar"
    id="superadminSidebar"
>


    <div class="sidebar-brand">

        <div>

            <div class="sidebar-brand-title">
                Loan Management
            </div>

            <div class="sidebar-brand-subtitle">
                SaaS Administration
            </div>

        </div>

    </div>


    <div class="sidebar-content">


        <!-- DASHBOARD -->

        <div class="sidebar-section">

            <div class="sidebar-section-title">
                Overview
            </div>


            <div class="sidebar-menu">

                <a
                    href="index.php?url=dashboard/super_admin"
                    class="sidebar-link active"
                >

                    <span class="sidebar-icon">
                        ▣
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>

            </div>

        </div>


        <!-- PLATFORM -->

        <div class="sidebar-section">

            <div class="sidebar-section-title">
                Platform
            </div>


            <div class="sidebar-menu">


                <a
                    href="index.php?url=super_admin/businesses"
                    class="sidebar-link"
                >

                    <span class="sidebar-icon">
                        ◫
                    </span>

                    <span>
                        Businesses
                    </span>

                </a>


                <a
                    href="index.php?url=superadmin/users"
                    class="sidebar-link"
                >

                    <span class="sidebar-icon">
                        ◉
                    </span>

                    <span>
                        Users
                    </span>

                </a>


                <a
                    href="index.php?url=superadmin/subscriptions"
                    class="sidebar-link"
                >

                    <span class="sidebar-icon">
                        ◈
                    </span>

                    <span>
                        Subscriptions
                    </span>

                </a>


                <a
                    href="index.php?url=superadmin/plans"
                    class="sidebar-link"
                >

                    <span class="sidebar-icon">
                        ◇
                    </span>

                    <span>
                        Plans
                    </span>

                </a>

            </div>

        </div>


        <!-- SYSTEM -->

        <div class="sidebar-section">

            <div class="sidebar-section-title">
                System
            </div>


            <div class="sidebar-menu">


                <a
                    href="index.php?url=superadmin/settings"
                    class="sidebar-link"
                >

                    <span class="sidebar-icon">
                        ⚙
                    </span>

                    <span>
                        Settings
                    </span>

                </a>


                <a
                    href="index.php?url=superadmin/logs"
                    class="sidebar-link"
                >

                    <span class="sidebar-icon">
                        ≡
                    </span>

                    <span>
                        System Logs
                    </span>

                </a>

            </div>

        </div>


    </div>


    <!-- SIDEBAR FOOTER -->

    <div class="sidebar-footer">


        <div class="sidebar-user">

            <div class="sidebar-avatar">

                <?php

                $displayName =
                    $user['full_name']
                    ?? $user['username']
                    ?? 'Administrator';

                echo htmlspecialchars(
                    strtoupper(
                        substr(
                            $displayName,
                            0,
                            1
                        )
                    )
                );

                ?>

            </div>


            <div class="sidebar-user-info">

                <div class="sidebar-user-name">

                    <?= htmlspecialchars(
                        $displayName
                    ) ?>

                </div>


                <div class="sidebar-user-role">

                    <?= htmlspecialchars(
                        $user['role']
                        ?? 'super_admin'
                    ) ?>

                </div>

            </div>

        </div>


        <a
            href="index.php?url=auth/logout"
            class="sidebar-logout"
        >

            <span class="sidebar-icon">
                ↪
            </span>

            <span>
                Logout
            </span>

        </a>


    </div>


</aside>


<div
    class="sidebar-overlay"
    id="sidebarOverlay"
    onclick="closeSuperAdminSidebar()"
></div>


<!-- ================================================================
     MAIN CONTENT
================================================================ -->

<div class="main-content">


    <!-- ============================================================
         NAVBAR
    ============================================================= -->

    <nav class="navbar">


        <div
            style="
                display:flex;
                align-items:center;
                gap:12px;
            "
        >

            <button
                type="button"
                class="mobile-menu-button"
                onclick="openSuperAdminSidebar()"
            >
                ☰
            </button>


            <div class="navbar-title">

                SaaS Administration

            </div>

        </div>


        <div class="user-area">


            <span class="user-name">

                <?= htmlspecialchars(
                    $user['full_name']
                    ?? $user['username']
                    ?? 'Administrator'
                ) ?>

            </span>


            <span class="role">

                <?= htmlspecialchars(
                    $user['role']
                    ?? 'super_admin'
                ) ?>

            </span>


            <a
                href="index.php?url=auth/logout"
                class="logout"
            >
                Logout
            </a>


        </div>


    </nav>


    <!-- ============================================================
         PAGE
    ============================================================= -->

    <main class="container">


        <!-- WELCOME -->

        <div class="welcome">


            <h1>
                SaaS Administration
            </h1>


            <p>

                Welcome back,

                <?= htmlspecialchars(
                    $user['full_name']
                    ?? $user['username']
                    ?? 'Administrator'
                ) ?>.

            </p>


        </div>


        <!-- STATISTICS -->

        <div class="cards">


            <div class="card">

                <div class="card-title">
                    Total Businesses
                </div>


                <div class="card-value">
                    0
                </div>

            </div>


            <div class="card">

                <div class="card-title">
                    Active Subscriptions
                </div>


                <div class="card-value">
                    0
                </div>

            </div>


            <div class="card">

                <div class="card-title">
                    Total Users
                </div>


                <div class="card-value">
                    1
                </div>

            </div>


            <div class="card">

                <div class="card-title">
                    Active Loans
                </div>


                <div class="card-value">
                    0
                </div>

            </div>


        </div>


        <!-- PLATFORM ADMINISTRATION -->

        <div class="section">


            <h2>
                Platform Administration
            </h2>


            <p>

                From this dashboard you will manage
                businesses, subscriptions, users, plans,
                and the entire Loan Management SaaS
                platform.

            </p>


        </div>


    </main>


</div>


<script>


/* ================================================================
   OPEN SIDEBAR
================================================================ */

function openSuperAdminSidebar()
{

    const sidebar =
        document.getElementById(
            'superadminSidebar'
        );

    const overlay =
        document.getElementById(
            'sidebarOverlay'
        );


    if (sidebar) {

        sidebar.classList.add(
            'open'
        );

    }


    if (overlay) {

        overlay.classList.add(
            'active'
        );

    }

}


/* ================================================================
   CLOSE SIDEBAR
================================================================ */

function closeSuperAdminSidebar()
{

    const sidebar =
        document.getElementById(
            'superadminSidebar'
        );

    const overlay =
        document.getElementById(
            'sidebarOverlay'
        );


    if (sidebar) {

        sidebar.classList.remove(
            'open'
        );

    }


    if (overlay) {

        overlay.classList.remove(
            'active'
        );

    }

}


/* ================================================================
   ESC KEY
================================================================ */

document.addEventListener(
    'keydown',
    function(event)
    {

        if (
            event.key === 'Escape'
        ) {

            closeSuperAdminSidebar();

        }

    }
);


</script>


</body>

</html>
