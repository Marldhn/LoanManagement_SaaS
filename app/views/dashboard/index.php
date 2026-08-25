<?php

/*
|--------------------------------------------------------------------------
| SAFE DEFAULTS
|--------------------------------------------------------------------------
*/

$user =
    $user
    ?? Auth::user();


$business =
    $business
    ?? Auth::business()
    ?? [];


$businessId =
    (int) (
        $businessId
        ?? Auth::businessId()
        ?? 0
    );


$tenantRole =
    $tenantRole
    ?? Auth::tenantRole()
    ?? 'User';


$totalBorrowers =
    (int) (
        $totalBorrowers
        ?? 0
    );


$activeLoans =
    (int) (
        $activeLoans
        ?? 0
    );


$outstandingBalance =
    (float) (
        $outstandingBalance
        ?? 0
    );


$totalAccounts =
    (int) (
        $totalAccounts
        ?? 0
    );


$totalFunds =
    (float) (
        $totalFunds
        ?? 0
    );


$totalAssets =
    (float) (
        $totalAssets
        ?? 0
    );


$totalLiabilities =
    (float) (
        $totalLiabilities
        ?? 0
    );


$netBalance =
    (float) (
        $netBalance
        ??
        (
            $totalAssets
            -
            $totalLiabilities
        )
    );


$accounts =
    $accounts
    ?? [];


$recentLoans =
    $recentLoans
    ?? [];


/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

if (!function_exists('dashboardMoney')) {

    function dashboardMoney(
        float $amount
    ): string {

        return '₱' .
            number_format(
                $amount,
                2
            );
    }

}


if (!function_exists('dashboardStatusClass')) {

    function dashboardStatusClass(
        string $status
    ): string {

        return match (
            strtolower(trim($status))
        ) {

            'active' =>
                'dashboard-status-active',

            'approved' =>
                'dashboard-status-approved',

            'pending' =>
                'dashboard-status-pending',

            'completed' =>
                'dashboard-status-completed',

            'overdue' =>
                'dashboard-status-overdue',

            'cancelled',
            'rejected' =>
                'dashboard-status-danger',

            default =>
                'dashboard-status-default'
        };
    }

}


/*
|--------------------------------------------------------------------------
| DISPLAY DATA
|--------------------------------------------------------------------------
*/

$userName =
    $user['full_name']
    ??
    $user['username']
    ??
    'Administrator';


$businessName =
    $business['name']
    ??
    'Loan Management';


$role =
    $tenantRole
    ??
    $user['role']
    ??
    'User';

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
        Dashboard | <?= htmlspecialchars($businessName) ?>
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD PAGE
        |--------------------------------------------------------------------------
        */

        .lm-dashboard {

            width: 100%;

        }


        /*
        |--------------------------------------------------------------------------
        | WELCOME HEADER
        |--------------------------------------------------------------------------
        */

        .lm-dashboard-header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 28px;

        }


        .lm-dashboard-heading h1 {

            margin: 0;

            color: #111827;

            font-size: 28px;

            font-weight: 750;

            letter-spacing: -.025em;

        }


        .lm-dashboard-heading p {

            margin: 7px 0 0;

            color: #6b7280;

            font-size: 14px;

        }


        .lm-dashboard-date {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            padding: 9px 13px;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            background: #ffffff;

            color: #6b7280;

            font-size: 12px;

            font-weight: 600;

            white-space: nowrap;

        }


        /*
        |--------------------------------------------------------------------------
        | KPI GRID
        |--------------------------------------------------------------------------
        */

        .lm-kpi-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
                );

            gap: 16px;

            margin-bottom: 22px;

        }


        .lm-kpi-card {

            position: relative;

            overflow: hidden;

            padding: 20px;

            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 15px;

            box-shadow:
                0 2px 7px
                rgba(
                    0,
                    0,
                    0,
                    .035
                );

            transition:
                transform .18s ease,
                box-shadow .18s ease;

        }


        .lm-kpi-card:hover {

            transform: translateY(-2px);

            box-shadow:
                0 8px 20px
                rgba(
                    0,
                    0,
                    0,
                    .07
                );

        }


        .lm-kpi-top {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 17px;

        }


        .lm-kpi-icon {

            width: 42px;

            height: 42px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 11px;

            background: #f3f4f6;

            color: #374151;

            font-size: 16px;

            font-weight: 750;

        }


        .lm-kpi-label {

            color: #6b7280;

            font-size: 12px;

            font-weight: 650;

        }


        .lm-kpi-value {

            color: #111827;

            font-size: 25px;

            line-height: 1.2;

            font-weight: 750;

            letter-spacing: -.02em;

            word-break: break-word;

        }


        .lm-kpi-description {

            margin-top: 6px;

            color: #9ca3af;

            font-size: 11px;

        }


        /*
        |--------------------------------------------------------------------------
        | MAIN GRID
        |--------------------------------------------------------------------------
        */

        .lm-main-grid {

            display: grid;

            grid-template-columns:
                minmax(0, 1.35fr)
                minmax(300px, .85fr);

            gap: 20px;

            margin-bottom: 20px;

        }


        /*
        |--------------------------------------------------------------------------
        | PANEL
        |--------------------------------------------------------------------------
        */

        .lm-panel {

            background: #ffffff;

            border:
                1px solid #e5e7eb;

            border-radius: 15px;

            overflow: hidden;

            box-shadow:
                0 2px 7px
                rgba(
                    0,
                    0,
                    0,
                    .035
                );

        }


        .lm-panel-header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 15px;

            padding: 19px 20px;

            border-bottom:
                1px solid #f0f1f3;

        }


        .lm-panel-title {

            margin: 0;

            color: #111827;

            font-size: 17px;

            font-weight: 700;

        }


        .lm-panel-subtitle {

            margin: 5px 0 0;

            color: #9ca3af;

            font-size: 12px;

        }


        /*
        |--------------------------------------------------------------------------
        | LOAN PORTFOLIO
        |--------------------------------------------------------------------------
        */

        .lm-portfolio {

            padding: 20px;

        }


        .lm-portfolio-main {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            padding-bottom: 20px;

            border-bottom:
                1px solid #f0f1f3;

        }


        .lm-portfolio-label {

            color: #6b7280;

            font-size: 12px;

            font-weight: 600;

        }


        .lm-portfolio-value {

            margin-top: 5px;

            color: #111827;

            font-size: 27px;

            font-weight: 750;

        }


        .lm-portfolio-description {

            margin-top: 4px;

            color: #9ca3af;

            font-size: 11px;

        }


        .lm-portfolio-icon {

            width: 52px;

            height: 52px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 14px;

            background: #f3f4f6;

            color: #374151;

            font-size: 19px;

            font-weight: 750;

        }


        .lm-loan-breakdown {

            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );

            gap: 10px;

            padding-top: 16px;

        }


        .lm-loan-breakdown-item {

            padding: 13px;

            background: #f9fafb;

            border-radius: 11px;

        }


        .lm-loan-breakdown-label {

            color: #9ca3af;

            font-size: 10px;

            font-weight: 650;

            text-transform: uppercase;

            letter-spacing: .03em;

        }


        .lm-loan-breakdown-value {

            margin-top: 5px;

            color: #111827;

            font-size: 17px;

            font-weight: 750;

        }


        /*
        |--------------------------------------------------------------------------
        | FINANCIAL OVERVIEW
        |--------------------------------------------------------------------------
        */

        .lm-financial-body {

            padding: 20px;

        }


        .lm-financial-item {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 14px 0;

            border-bottom:
                1px solid #f0f1f3;

        }


        .lm-financial-item:first-child {

            padding-top: 0;

        }


        .lm-financial-item:last-child {

            padding-bottom: 0;

            border-bottom: none;

        }


        .lm-financial-label {

            color: #6b7280;

            font-size: 12px;

        }


        .lm-financial-value {

            color: #111827;

            font-size: 14px;

            font-weight: 700;

            text-align: right;

        }


        .lm-financial-value.net {

            font-size: 16px;

        }


        /*
        |--------------------------------------------------------------------------
        | ACCOUNT SECTION
        |--------------------------------------------------------------------------
        */

        .lm-account-list {

            display: flex;

            flex-direction: column;

        }


        .lm-account {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 15px 20px;

            border-bottom:
                1px solid #f3f4f6;

        }


        .lm-account:last-child {

            border-bottom: none;

        }


        .lm-account-left {

            display: flex;

            align-items: center;

            gap: 12px;

            min-width: 0;

        }


        .lm-account-icon {

            width: 39px;

            height: 39px;

            min-width: 39px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 10px;

            background: #f3f4f6;

            color: #374151;

            font-size: 14px;

            font-weight: 750;

        }


        .lm-account-details {

            min-width: 0;

        }


        .lm-account-name {

            color: #111827;

            font-size: 13px;

            font-weight: 650;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }


        .lm-account-type {

            margin-top: 3px;

            color: #9ca3af;

            font-size: 11px;

            text-transform: capitalize;

        }


        .lm-account-balance {

            color: #111827;

            font-size: 13px;

            font-weight: 700;

            white-space: nowrap;

        }


        .lm-account-footer {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 15px 20px;

            background: #f9fafb;

            border-top:
                1px solid #e5e7eb;

        }


        .lm-account-footer-label {

            color: #6b7280;

            font-size: 11px;

        }


        .lm-account-footer-value {

            color: #111827;

            font-size: 13px;

            font-weight: 750;

        }


        /*
        |--------------------------------------------------------------------------
        | RECENT LOANS TABLE
        |--------------------------------------------------------------------------
        */

        .lm-table-wrapper {

            width: 100%;

            overflow-x: auto;

            -webkit-overflow-scrolling: touch;

        }


        .lm-table {

            width: 100%;

            min-width: 760px;

            border-collapse: collapse;

        }


        .lm-table th {

            padding:
                12px 20px;

            background: #f9fafb;

            color: #6b7280;

            font-size: 10px;

            font-weight: 750;

            text-align: left;

            text-transform: uppercase;

            letter-spacing: .045em;

            white-space: nowrap;

        }


        .lm-table td {

            padding:
                15px 20px;

            color: #4b5563;

            font-size: 12px;

            border-top:
                1px solid #f3f4f6;

            white-space: nowrap;

        }


        .lm-table tbody tr {

            transition:
                background .15s ease;

        }


        .lm-table tbody tr:hover {

            background: #fafafa;

        }


        .lm-loan-number {

            color: #111827;

            font-weight: 700;

        }


        .lm-borrower {

            color: #111827;

            font-weight: 600;

        }


        .lm-amount {

            color: #111827;

            font-weight: 650;

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS BADGES
        |--------------------------------------------------------------------------
        */

        .lm-status {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding:
                5px 9px;

            border-radius: 999px;

            font-size: 10px;

            font-weight: 750;

            text-transform: capitalize;

        }


        .dashboard-status-active {

            background: #ecfdf5;

            color: #047857;

        }


        .dashboard-status-approved {

            background: #eff6ff;

            color: #1d4ed8;

        }


        .dashboard-status-pending {

            background: #fffbeb;

            color: #b45309;

        }


        .dashboard-status-completed {

            background: #f0fdf4;

            color: #15803d;

        }


        .dashboard-status-overdue {

            background: #fff7ed;

            color: #c2410c;

        }


        .dashboard-status-danger {

            background: #fef2f2;

            color: #b91c1c;

        }


        .dashboard-status-default {

            background: #f3f4f6;

            color: #4b5563;

        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .lm-empty {

            padding: 35px 20px;

            text-align: center;

            color: #9ca3af;

            font-size: 12px;

        }


        /*
        |--------------------------------------------------------------------------
        | BUSINESS INFORMATION
        |--------------------------------------------------------------------------
        */

        .lm-business-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );

            gap: 12px;

            padding: 20px;

        }


        .lm-business-item {

            padding: 15px;

            background: #f9fafb;

            border:
                1px solid #f0f1f3;

            border-radius: 11px;

        }


        .lm-business-label {

            margin-bottom: 6px;

            color: #9ca3af;

            font-size: 9px;

            font-weight: 750;

            text-transform: uppercase;

            letter-spacing: .05em;

        }


        .lm-business-value {

            color: #111827;

            font-size: 13px;

            font-weight: 650;

            word-break: break-word;

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE - TABLET
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1100px) {

            .lm-kpi-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );

            }


            .lm-main-grid {

                grid-template-columns: 1fr;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE - MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 650px) {

            .lm-dashboard-header {

                flex-direction: column;

                margin-bottom: 20px;

            }


            .lm-dashboard-heading h1 {

                font-size: 23px;

            }


            .lm-dashboard-date {

                display: none;

            }


            .lm-kpi-grid {

                grid-template-columns: 1fr;

                gap: 12px;

                margin-bottom: 15px;

            }


            .lm-kpi-card {

                padding: 17px;

                border-radius: 13px;

            }


            .lm-kpi-value {

                font-size: 23px;

            }


            .lm-main-grid {

                gap: 15px;

                margin-bottom: 15px;

            }


            .lm-panel {

                border-radius: 13px;

            }


            .lm-panel-header {

                padding: 16px;

            }


            .lm-panel-title {

                font-size: 16px;

            }


            .lm-portfolio {

                padding: 16px;

            }


            .lm-portfolio-value {

                font-size: 23px;

            }


            .lm-loan-breakdown {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );

            }


            .lm-account {

                padding:
                    14px 16px;

            }


            .lm-account-footer {

                padding:
                    14px 16px;

            }


            .lm-business-grid {

                grid-template-columns: 1fr;

                padding: 15px;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | VERY SMALL DEVICES
        |--------------------------------------------------------------------------
        */

        @media (max-width: 400px) {

            .lm-loan-breakdown {

                grid-template-columns: 1fr;

            }


            .lm-kpi-value {

                font-size: 21px;

            }


            .lm-account-balance {

                font-size: 12px;

            }

        }

    </style>

</head>


<body>


<?php

require APP_PATH .
    '/views/layouts/sidebar.php';

?>


<div class="main-content">


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <nav class="navbar">

        <div class="page-title">

            Dashboard

        </div>


        <div class="user-info">

            <span class="user-name">

                <?= htmlspecialchars(
                    $userName
                ) ?>

            </span>


            <span class="badge">

                <?= htmlspecialchars(
                    $role
                ) ?>

            </span>

        </div>

    </nav>


    <!-- =====================================================
         DASHBOARD CONTENT
    ====================================================== -->

    <div class="container lm-dashboard">


        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="lm-dashboard-header">

            <div class="lm-dashboard-heading">

                <h1>

                    Welcome back,
                    <?= htmlspecialchars(
                        $userName
                    ) ?>

                </h1>


                <p>

                    Here's an overview of
                    <?= htmlspecialchars(
                        $businessName
                    ) ?>

                </p>

            </div>


            <div class="lm-dashboard-date">

                <?= date('M d, Y') ?>

            </div>

        </div>


        <!-- =================================================
             KPI CARDS
        ================================================== -->

        <div class="lm-kpi-grid">


            <!-- BORROWERS -->

            <div class="lm-kpi-card">

                <div class="lm-kpi-top">

                    <div class="lm-kpi-label">

                        Total Borrowers

                    </div>


                    <div class="lm-kpi-icon">

                        B

                    </div>

                </div>


                <div class="lm-kpi-value">

                    <?= number_format(
                        $totalBorrowers
                    ) ?>

                </div>


                <div class="lm-kpi-description">

                    Registered borrowers

                </div>

            </div>


            <!-- ACTIVE LOANS -->

            <div class="lm-kpi-card">

                <div class="lm-kpi-top">

                    <div class="lm-kpi-label">

                        Active Loans

                    </div>


                    <div class="lm-kpi-icon">

                        L

                    </div>

                </div>


                <div class="lm-kpi-value">

                    <?= number_format(
                        $activeLoans
                    ) ?>

                </div>


                <div class="lm-kpi-description">

                    Currently active loans

                </div>

            </div>


            <!-- OUTSTANDING -->

            <div class="lm-kpi-card">

                <div class="lm-kpi-top">

                    <div class="lm-kpi-label">

                        Outstanding Balance

                    </div>


                    <div class="lm-kpi-icon">

                        ₱

                    </div>

                </div>


                <div class="lm-kpi-value">

                    <?= dashboardMoney(
                        $outstandingBalance
                    ) ?>

                </div>


                <div class="lm-kpi-description">

                    Remaining loan balance

                </div>

            </div>


            <!-- AVAILABLE FUNDS -->

            <div class="lm-kpi-card">

                <div class="lm-kpi-top">

                    <div class="lm-kpi-label">

                        Available Funds

                    </div>


                    <div class="lm-kpi-icon">

                        ₱

                    </div>

                </div>


                <div class="lm-kpi-value">

                    <?= dashboardMoney(
                        $totalFunds
                    ) ?>

                </div>


                <div class="lm-kpi-description">

                    Active account balances

                </div>

            </div>


        </div>


        <!-- =================================================
             PORTFOLIO + FINANCIAL
        ================================================== -->

        <div class="lm-main-grid">


            <!-- =================================================
                 LOAN PORTFOLIO
            ================================================== -->

            <div class="lm-panel">


                <div class="lm-panel-header">

                    <div>

                        <h2 class="lm-panel-title">

                            Loan Portfolio

                        </h2>


                        <p class="lm-panel-subtitle">

                            Current lending activity

                        </p>

                    </div>

                </div>


                <div class="lm-portfolio">


                    <div class="lm-portfolio-main">


                        <div>

                            <div class="lm-portfolio-label">

                                Outstanding Balance

                            </div>


                            <div class="lm-portfolio-value">

                                <?= dashboardMoney(
                                    $outstandingBalance
                                ) ?>

                            </div>


                            <div class="lm-portfolio-description">

                                Total amount currently outstanding

                            </div>

                        </div>


                        <div class="lm-portfolio-icon">

                            ₱

                        </div>

                    </div>


                    <div class="lm-loan-breakdown">


                        <div class="lm-loan-breakdown-item">

                            <div class="lm-loan-breakdown-label">

                                Active Loans

                            </div>


                            <div class="lm-loan-breakdown-value">

                                <?= number_format(
                                    $activeLoans
                                ) ?>

                            </div>

                        </div>


                        <div class="lm-loan-breakdown-item">

                            <div class="lm-loan-breakdown-label">

                                Borrowers

                            </div>


                            <div class="lm-loan-breakdown-value">

                                <?= number_format(
                                    $totalBorrowers
                                ) ?>

                            </div>

                        </div>


                        <div class="lm-loan-breakdown-item">

                            <div class="lm-loan-breakdown-label">

                                Accounts

                            </div>


                            <div class="lm-loan-breakdown-value">

                                <?= number_format(
                                    $totalAccounts
                                ) ?>

                            </div>

                        </div>


                    </div>


                </div>

            </div>


            <!-- =================================================
                 FINANCIAL OVERVIEW
            ================================================== -->

            <div class="lm-panel">


                <div class="lm-panel-header">

                    <div>

                        <h2 class="lm-panel-title">

                            Financial Overview

                        </h2>


                        <p class="lm-panel-subtitle">

                            Current business position

                        </p>

                    </div>

                </div>


                <div class="lm-financial-body">


                    <div class="lm-financial-item">

                        <span class="lm-financial-label">

                            Total Assets

                        </span>


                        <span class="lm-financial-value">

                            <?= dashboardMoney(
                                $totalAssets
                            ) ?>

                        </span>

                    </div>


                    <div class="lm-financial-item">

                        <span class="lm-financial-label">

                            Total Liabilities

                        </span>


                        <span class="lm-financial-value">

                            <?= dashboardMoney(
                                $totalLiabilities
                            ) ?>

                        </span>

                    </div>


                    <div class="lm-financial-item">

                        <span class="lm-financial-label">

                            Available Funds

                        </span>


                        <span class="lm-financial-value">

                            <?= dashboardMoney(
                                $totalFunds
                            ) ?>

                        </span>

                    </div>


                    <div class="lm-financial-item">

                        <span class="lm-financial-label">

                            Net Balance

                        </span>


                        <span class="lm-financial-value net">

                            <?= dashboardMoney(
                                $netBalance
                            ) ?>

                        </span>

                    </div>


                </div>

            </div>


        </div>


        <!-- =================================================
             BUSINESS ACCOUNTS
        ================================================== -->

        <div
            class="lm-panel"
            style="margin-bottom:20px;"
        >


            <div class="lm-panel-header">

                <div>

                    <h2 class="lm-panel-title">

                        Business Accounts

                    </h2>


                    <p class="lm-panel-subtitle">

                        Current balances of active accounts

                    </p>

                </div>


                <div class="lm-panel-subtitle">

                    <?= number_format(
                        $totalAccounts
                    ) ?>
                    accounts

                </div>

            </div>


            <?php if (!empty($accounts)): ?>


                <div class="lm-account-list">


                    <?php foreach (
                        $accounts
                        as $account
                    ): ?>


                        <div class="lm-account">


                            <div class="lm-account-left">


                                <div class="lm-account-icon">

                                    ₱

                                </div>


                                <div class="lm-account-details">

                                    <div class="lm-account-name">

                                        <?= htmlspecialchars(
                                            $account['account_name']
                                            ??
                                            'Unnamed Account'
                                        ) ?>

                                    </div>


                                    <div class="lm-account-type">

                                        <?= htmlspecialchars(
                                            $account['account_type']
                                            ??
                                            'Account'
                                        ) ?>

                                    </div>

                                </div>

                            </div>


                            <div class="lm-account-balance">

                                <?= dashboardMoney(
                                    (float) (
                                        $account['balance']
                                        ??
                                        0
                                    )
                                ) ?>

                            </div>


                        </div>


                    <?php endforeach; ?>


                </div>


                <div class="lm-account-footer">

                    <span class="lm-account-footer-label">

                        Total Available Funds

                    </span>


                    <span class="lm-account-footer-value">

                        <?= dashboardMoney(
                            $totalFunds
                        ) ?>

                    </span>

                </div>


            <?php else: ?>


                <div class="lm-empty">

                    No active business accounts found.

                </div>


            <?php endif; ?>


        </div>


        <!-- =================================================
             RECENT LOANS
        ================================================== -->

        <div class="lm-panel">


            <div class="lm-panel-header">

                <div>

                    <h2 class="lm-panel-title">

                        Recent Loans

                    </h2>


                    <p class="lm-panel-subtitle">

                        Latest loan records in the system

                    </p>

                </div>


                <a
                    href="index.php?url=loans/index"
                    class="btn btn-primary"
                >

                    View Loans

                </a>

            </div>


            <?php if (!empty($recentLoans)): ?>


                <div class="lm-table-wrapper">


                    <table class="lm-table">


                        <thead>

                            <tr>

                                <th>
                                    Loan Number
                                </th>

                                <th>
                                    Borrower
                                </th>

                                <th>
                                    Account
                                </th>

                                <th>
                                    Principal
                                </th>

                                <th>
                                    Total Payable
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php foreach (
                                $recentLoans
                                as $loan
                            ): ?>


                                <?php

                                $loanStatus =
                                    $loan['status']
                                    ??
                                    'pending';

                                ?>


                                <tr>


                                    <td>

                                        <span
                                            class="lm-loan-number"
                                        >

                                            <?= htmlspecialchars(
                                                $loan['loan_number']
                                                ??
                                                'N/A'
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="lm-borrower"
                                        >

                                            <?= htmlspecialchars(
                                                $loan['borrower_name']
                                                ??
                                                'Unknown Borrower'
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $loan['account_name']
                                            ??
                                            'N/A'
                                        ) ?>

                                    </td>


                                    <td>

                                        <span
                                            class="lm-amount"
                                        >

                                            <?= dashboardMoney(
                                                (float) (
                                                    $loan[
                                                        'principal_amount'
                                                    ]
                                                    ??
                                                    0
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="lm-amount"
                                        >

                                            <?= dashboardMoney(
                                                (float) (
                                                    $loan[
                                                        'total_payable'
                                                    ]
                                                    ??
                                                    0
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="lm-status <?= htmlspecialchars(
                                                dashboardStatusClass(
                                                    $loanStatus
                                                )
                                            ) ?>"
                                        >

                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $loanStatus
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            <?php else: ?>


                <div class="lm-empty">

                    No loans have been created yet.

                </div>


            <?php endif; ?>


        </div>


        <!-- =================================================
             BUSINESS INFORMATION
        ================================================== -->

        <div
            class="lm-panel"
            style="margin-top:20px;"
        >


            <div class="lm-panel-header">

                <div>

                    <h2 class="lm-panel-title">

                        Business Information

                    </h2>


                    <p class="lm-panel-subtitle">

                        Current account and business details

                    </p>

                </div>

            </div>


            <div class="lm-business-grid">


                <div class="lm-business-item">

                    <div class="lm-business-label">

                        Business

                    </div>


                    <div class="lm-business-value">

                        <?= htmlspecialchars(
                            $businessName
                        ) ?>

                    </div>

                </div>


                <div class="lm-business-item">

                    <div class="lm-business-label">

                        Business ID

                    </div>


                    <div class="lm-business-value">

                        <?= htmlspecialchars(
                            (string) $businessId
                        ) ?>

                    </div>

                </div>


                <div class="lm-business-item">

                    <div class="lm-business-label">

                        Your Role

                    </div>


                    <div class="lm-business-value">

                        <?= htmlspecialchars(
                            $role
                        ) ?>

                    </div>

                </div>


            </div>


        </div>


    </div>

</div>


</body>

</html>