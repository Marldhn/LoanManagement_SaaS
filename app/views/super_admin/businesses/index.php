<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $user ?? ($_SESSION['user'] ?? []);

$businesses = $businesses ?? [];

$totalBusinesses = (int) ($totalBusinesses ?? count($businesses));
$activeBusinesses = (int) ($activeBusinesses ?? 0);
$inactiveBusinesses = (int) ($inactiveBusinesses ?? 0);
$totalUsers = (int) ($totalUsers ?? 0);
$totalLoans = (int) ($totalLoans ?? 0);

$currentUrl = $currentUrl ?? ($_GET['url'] ?? 'super_admin/businesses');

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
    Businesses | Loan Management SaaS
</title>

<link
    rel="stylesheet"
    href="assets/css/style.css"
>

<style>

    /* =====================================================
       BUSINESS PAGE
    ====================================================== */

    .business-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 30px;
    }


    /* =====================================================
       PAGE HEADER
    ====================================================== */

    .business-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .business-page-header h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
        color: #111827;
    }

    .business-page-header p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 14px;
    }


    /* =====================================================
       PRIMARY BUTTON
    ====================================================== */

    .business-primary-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 16px;
        border: none;
        border-radius: 8px;
        background: #111827;
        color: #ffffff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition:
            background 0.2s ease,
            transform 0.2s ease;
    }

    .business-primary-btn:hover {
        background: #000000;
        color: #ffffff;
        transform: translateY(-1px);
    }


    /* =====================================================
       STATISTICS
    ====================================================== */

    .business-stats {
        display: grid;
        grid-template-columns:
            repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .business-stat-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        box-shadow:
            0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .business-stat-label {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .business-stat-value {
        font-size: 25px;
        font-weight: 700;
        color: #111827;
    }


    /* =====================================================
       TABLE CARD
    ====================================================== */

    .business-table-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow:
            0 1px 2px rgba(0, 0, 0, 0.04);
    }


    /* =====================================================
       TABLE HEADER
    ====================================================== */

    .business-table-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;
    }

    .business-table-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
    }


    /* =====================================================
       TABLE WRAPPER
    ====================================================== */

    .business-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }


    /* =====================================================
       TABLE
    ====================================================== */

    .business-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
    }

    .business-table th {
        background: #f9fafb;
        color: #6b7280;

        font-size: 11px;
        font-weight: 600;

        text-transform: uppercase;
        letter-spacing: 0.03em;

        padding: 13px 16px;

        text-align: left;
        white-space: nowrap;

        border-bottom: 1px solid #e5e7eb;
    }

    .business-table td {
        padding: 15px 16px;

        font-size: 13px;
        color: #374151;

        border-bottom: 1px solid #f1f5f9;

        vertical-align: middle;
    }

    .business-table tbody tr:hover {
        background: #fafafa;
    }

    .business-table tbody tr:last-child td {
        border-bottom: none;
    }


    /* =====================================================
       BUSINESS NAME
    ====================================================== */

    .business-name {
        font-weight: 700;
        color: #111827;
    }

    .business-slug {
        margin-top: 3px;
        font-size: 11px;
        color: #9ca3af;
    }


    /* =====================================================
       CONTACT
    ====================================================== */

    .business-phone {
        color: #374151;
    }

    .business-email {
        margin-top: 3px;
        font-size: 11px;
        color: #9ca3af;
    }


    /* =====================================================
       ADDRESS
    ====================================================== */

    .business-address {
        max-width: 220px;
        color: #4b5563;
        line-height: 1.4;
    }


    /* =====================================================
       STATUS
    ====================================================== */

    .business-status {
        display: inline-flex;
        align-items: center;

        padding: 5px 9px;

        border-radius: 999px;

        font-size: 11px;
        font-weight: 600;

        white-space: nowrap;
    }

    .status-active {
        background: #ecfdf5;
        color: #047857;
    }

    .status-inactive {
        background: #f3f4f6;
        color: #4b5563;
    }

    .status-suspended {
        background: #fef2f2;
        color: #b91c1c;
    }

    .status-pending {
        background: #fffbeb;
        color: #b45309;
    }


    /* =====================================================
       COUNTS
    ====================================================== */

    .business-count {
        font-weight: 600;
        color: #111827;
    }


    /* =====================================================
       ACTIONS
    ====================================================== */

    .business-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .business-action {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        width: 34px;
        height: 34px;

        border: 1px solid #e5e7eb;
        border-radius: 7px;

        background: #ffffff;
        color: #374151;

        text-decoration: none;

        font-size: 14px;

        transition:
            background 0.2s ease,
            border-color 0.2s ease;
    }

    .business-action:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        color: #111827;
    }


    /* =====================================================
       EMPTY STATE
    ====================================================== */

    .business-empty {
        padding: 60px 20px;
        text-align: center;
    }

    .business-empty-icon {
        width: 50px;
        height: 50px;

        margin: 0 auto 14px;

        border-radius: 50%;

        background: #f3f4f6;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 22px;
        color: #6b7280;
    }

    .business-empty h3 {
        margin: 0 0 6px;

        font-size: 16px;
        color: #111827;
    }

    .business-empty p {
        margin: 0;

        font-size: 13px;
        color: #9ca3af;
    }


    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 1100px) {

        .business-stats {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

    }


    @media (max-width: 900px) {

        .business-container {
            padding: 20px;
        }

        .business-page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .business-primary-btn {
            width: 100%;
        }

    }


    @media (max-width: 600px) {

        .business-container {
            padding: 15px;
        }

        .business-stats {
            grid-template-columns: 1fr;
        }

        .business-page-header h1 {
            font-size: 23px;
        }

    }

</style>

</head>

<body>

<?php

/*
|--------------------------------------------------------------------------
| SIDEBAR
|--------------------------------------------------------------------------
|
| Use the SAME sidebar used by Categories.
|
*/

require BASE_PATH .
    '/app/views/layouts/sidebar.php';

?>

<!-- =====================================================
     MAIN CONTENT
====================================================== -->

<div class="main-content">

```
<!-- =================================================
     NAVBAR
================================================== -->

<div class="navbar">

    <div class="page-title">
        Businesses
    </div>


    <div class="user-info">

        <span class="user-name">

            <?= htmlspecialchars(
                $user['full_name']
                ?? $user['username']
                ?? 'Administrator'
            ) ?>

        </span>


        <span class="badge">

            <?= htmlspecialchars(
                $user['role']
                ?? 'super_admin'
            ) ?>

        </span>

    </div>

</div>


<!-- =================================================
     PAGE
================================================== -->

<main class="business-container">


    <!-- =================================================
         PAGE HEADER
    ================================================== -->

    <div class="business-page-header">


        <div>

            <h1>
                Businesses
            </h1>

            <p>
                Manage all businesses registered
                on the Loan Management SaaS platform.
            </p>

        </div>


        <div>

            <a
                href="index.php?url=super_admin/businesses/create"
                class="business-primary-btn"
            >

                <span>
                    +
                </span>

                Add Business

            </a>

        </div>


    </div>


    <!-- =================================================
         STATISTICS
    ================================================== -->

    <div class="business-stats">


        <!-- TOTAL -->

        <div class="business-stat-card">

            <div class="business-stat-label">
                Total Businesses
            </div>

            <div class="business-stat-value">

                <?= number_format(
                    $totalBusinesses
                ) ?>

            </div>

        </div>


        <!-- ACTIVE -->

        <div class="business-stat-card">

            <div class="business-stat-label">
                Active Businesses
            </div>

            <div class="business-stat-value">

                <?= number_format(
                    $activeBusinesses
                ) ?>

            </div>

        </div>


        <!-- INACTIVE -->

        <div class="business-stat-card">

            <div class="business-stat-label">
                Inactive / Suspended
            </div>

            <div class="business-stat-value">

                <?= number_format(
                    $inactiveBusinesses
                ) ?>

            </div>

        </div>


        <!-- USERS -->

        <div class="business-stat-card">

            <div class="business-stat-label">
                Total Users
            </div>

            <div class="business-stat-value">

                <?= number_format(
                    $totalUsers
                ) ?>

            </div>

        </div>


    </div>


    <!-- =================================================
         TABLE
    ================================================== -->

    <div class="business-table-card">


        <!-- TABLE HEADER -->

        <div class="business-table-header">

            <div class="business-table-title">
                All Businesses
            </div>

        </div>


        <?php if (empty($businesses)): ?>


            <!-- =================================================
                 EMPTY
            ================================================== -->

            <div class="business-empty">


                <div class="business-empty-icon">
                    ◫
                </div>


                <h3>
                    No businesses found
                </h3>


                <p>
                    There are currently no businesses
                    registered on the platform.
                </p>


            </div>


        <?php else: ?>


            <!-- =================================================
                 TABLE
            ================================================== -->

            <div class="business-table-wrapper">


                <table class="business-table">


                    <thead>

                        <tr>

                            <th>
                                Business
                            </th>

                            <th>
                                Contact
                            </th>

                            <th>
                                Address
                            </th>

                            <th>
                                Users
                            </th>

                            <th>
                                Loans
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Created
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach (
                        $businesses
                        as $business
                    ): ?>


                        <?php

                        $businessId =
                            (int) (
                                $business['id']
                                ?? 0
                            );


                        $businessName =
                            $business['name']
                            ?? 'Unnamed Business';


                        $slug =
                            $business['slug']
                            ?? '';


                        $email =
                            $business['email']
                            ?? '-';


                        $phone =
                            $business['phone']
                            ?? '-';


                        $address =
                            $business['address']
                            ?? '-';


                        $userCount =
                            (int) (
                                $business['user_count']
                                ?? 0
                            );


                        $loanCount =
                            (int) (
                                $business['loan_count']
                                ?? 0
                            );


                        $status =
                            strtolower(
                                $business['status']
                                ?? 'inactive'
                            );


                        $createdAt =
                            $business['created_at']
                            ?? null;


                        $statusClass =
                            match ($status) {

                                'active'
                                    => 'status-active',

                                'inactive'
                                    => 'status-inactive',

                                'suspended'
                                    => 'status-suspended',

                                'pending'
                                    => 'status-pending',

                                default
                                    => 'status-inactive'
                            };


                        ?>


                        <tr>


                            <!-- BUSINESS -->

                            <td>

                                <div
                                    class="business-name"
                                >

                                    <?= htmlspecialchars(
                                        $businessName
                                    ) ?>

                                </div>


                                <?php if (
                                    $slug !== ''
                                ): ?>

                                    <div
                                        class="business-slug"
                                    >

                                        <?= htmlspecialchars(
                                            $slug
                                        ) ?>

                                    </div>

                                <?php endif; ?>


                            </td>


                            <!-- CONTACT -->

                            <td>


                                <div
                                    class="business-phone"
                                >

                                    <?= htmlspecialchars(
                                        $phone
                                    ) ?>

                                </div>


                                <div
                                    class="business-email"
                                >

                                    <?= htmlspecialchars(
                                        $email
                                    ) ?>

                                </div>


                            </td>


                            <!-- ADDRESS -->

                            <td>

                                <div
                                    class="business-address"
                                >

                                    <?= htmlspecialchars(
                                        $address
                                    ) ?>

                                </div>

                            </td>


                            <!-- USERS -->

                            <td>

                                <span
                                    class="business-count"
                                >

                                    <?= number_format(
                                        $userCount
                                    ) ?>

                                </span>

                            </td>


                            <!-- LOANS -->

                            <td>

                                <span
                                    class="business-count"
                                >

                                    <?= number_format(
                                        $loanCount
                                    ) ?>

                                </span>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="
                                        business-status
                                        <?= $statusClass ?>
                                    "
                                >

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $status
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <!-- CREATED -->

                            <td>


                                <?php if (
                                    !empty($createdAt)
                                ): ?>

                                    <?= htmlspecialchars(
                                        date(
                                            'M d, Y',
                                            strtotime(
                                                $createdAt
                                            )
                                        )
                                    ) ?>

                                <?php else: ?>

                                    <span
                                        style="
                                            color:#9ca3af;
                                        "
                                    >
                                        —
                                    </span>

                                <?php endif; ?>


                            </td>


                            <!-- ACTION -->

                            <td>


                                <div
                                    class="business-actions"
                                >


                                    <?php if (
                                        $businessId > 0
                                    ): ?>


                                        <a
                                            href="index.php?url=super_admin/businesses/view&id=<?= $businessId ?>"
                                            class="business-action"
                                            title="View Business"
                                        >
                                            👁
                                        </a>


                                    <?php endif; ?>


                                </div>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>


                </table>


            </div>


        <?php endif; ?>


    </div>


</main>
```

</div>

</body>

</html>
