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
    ?? [];


$businessId =
    (int) (
        $businessId
        ?? 0
    );


$tenantRole =
    $tenantRole
    ?? Auth::tenantRole();


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
        ?? (
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

function dashboardMoney(float $amount): string
{
    return '₱' .
        number_format(
            $amount,
            2
        );
}


function dashboardStatusClass(
    string $status
): string {

    return match (
        strtolower($status)
    ) {

        'active' =>
            'status-active',

        'approved' =>
            'status-approved',

        'pending' =>
            'status-pending',

        'completed' =>
            'status-completed',

        'overdue' =>
            'status-overdue',

        'cancelled',
        'rejected' =>
            'status-danger',

        default =>
            'status-default'
    };
}


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
    'Administrator';

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
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        .dashboard-page {

            width: 100%;

        }


        /*
        |--------------------------------------------------------------------------
        | WELCOME
        |--------------------------------------------------------------------------
        */

        .dashboard-welcome {

            margin-bottom: 28px;

        }


        .dashboard-welcome h1 {

            margin: 0;

            font-size: 28px;

            font-weight: 700;

            color: #111827;

        }


        .dashboard-welcome p {

            margin: 7px 0 0;

            color: #6b7280;

            font-size: 14px;

        }


        /*
        |--------------------------------------------------------------------------
        | STAT CARDS
        |--------------------------------------------------------------------------
        */

        .dashboard-stats {

            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
                );

            gap: 18px;

            margin-bottom: 24px;

        }


        .dashboard-stat {

            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            padding: 21px;

            min-width: 0;

            box-shadow:
                0 2px 8px
                rgba(
                    0,
                    0,
                    0,
                    .04
                );

            transition:
                transform .2s ease,
                box-shadow .2s ease;

        }


        .dashboard-stat:hover {

            transform: translateY(-2px);

            box-shadow:
                0 8px 22px
                rgba(
                    0,
                    0,
                    0,
                    .08
                );

        }


        .dashboard-stat-top {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 12px;

            margin-bottom: 15px;

        }


        .dashboard-stat-icon {

            width: 42px;

            height: 42px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #f3f4f6;

            color: #374151;

            font-size: 18px;

            font-weight: 700;

        }


        .dashboard-stat-label {

            color: #6b7280;

            font-size: 13px;

            font-weight: 600;

        }


        .dashboard-stat-value {

            color: #111827;

            font-size: 26px;

            font-weight: 750;

            line-height: 1.2;

            word-break: break-word;

        }


        .dashboard-stat-description {

            color: #9ca3af;

            font-size: 12px;

            margin-top: 7px;

        }


        /*
        |--------------------------------------------------------------------------
        | PANELS
        |--------------------------------------------------------------------------
        */

        .dashboard-grid {

            display: grid;

            grid-template-columns:
                minmax(0, 1.35fr)
                minmax(300px, .9fr);

            gap: 20px;

            margin-bottom: 20px;

        }


        .dashboard-panel {

            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            overflow: hidden;

            box-shadow:
                0 2px 8px
                rgba(
                    0,
                    0,
                    0,
                    .04
                );

        }


        .dashboard-panel-header {

            padding: 19px 20px;

            border-bottom:
                1px solid #f0f0f0;

        }


        .dashboard-panel-header h2 {

            margin: 0;

            color: #111827;

            font-size: 18px;

            font-weight: 700;

        }


        .dashboard-panel-header p {

            margin: 5px 0 0;

            color: #6b7280;

            font-size: 13px;

        }


        /*
        |--------------------------------------------------------------------------
        | ACCOUNTS
        |--------------------------------------------------------------------------
        */

        .account-list {

            display: flex;

            flex-direction: column;

        }


        .account-item {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 16px 20px;

            border-bottom:
                1px solid #f3f4f6;

        }


        .account-item:last-child {

            border-bottom: none;

        }


        .account-left {

            display: flex;

            align-items: center;

            gap: 12px;

            min-width: 0;

        }


        .account-icon {

            width: 42px;

            height: 42px;

            min-width: 42px;

            border-radius: 11px;

            background: #f3f4f6;

            color: #374151;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: 700;

        }


        .account-info {

            min-width: 0;

        }


        .account-name {

            color: #111827;

            font-size: 14px;

            font-weight: 600;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }


        .account-type {

            margin-top: 3px;

            color: #9ca3af;

            font-size: 12px;

            text-transform: capitalize;

        }


        .account-balance {

            color: #111827;

            font-size: 14px;

            font-weight: 700;

            white-space: nowrap;

        }


        .account-total {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 17px 20px;

            background: #f9fafb;

            border-top:
                1px solid #e5e7eb;

        }


        .account-total-label {

            color: #6b7280;

            font-size: 13px;

        }


        .account-total-value {

            color: #111827;

            font-weight: 700;

        }


        /*
        |--------------------------------------------------------------------------
        | FINANCIAL SUMMARY
        |--------------------------------------------------------------------------
        */

        .financial-summary {

            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(0, 1fr)
                );

            gap: 12px;

            padding: 20px;

        }


        .financial-item {

            padding: 16px;

            border:
                1px solid #e5e7eb;

            border-radius: 12px;

        }


        .financial-label {

            color: #6b7280;

            font-size: 12px;

            margin-bottom: 7px;

        }


        .financial-value {

            color: #111827;

            font-size: 18px;

            font-weight: 700;

            word-break: break-word;

        }


        /*
        |--------------------------------------------------------------------------
        | LOAN TABLE
        |--------------------------------------------------------------------------
        */

        .loan-table-wrapper {

            width: 100%;

            overflow-x: auto;

            -webkit-overflow-scrolling: touch;

        }


        .loan-table {

            width: 100%;

            min-width: 760px;

            border-collapse: collapse;

        }


        .loan-table th {

            padding: 13px 20px;

            background: #f9fafb;

            color: #6b7280;

            text-align: left;

            font-size: 11px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .04em;

            white-space: nowrap;

        }


        .loan-table td {

            padding: 15px 20px;

            border-top:
                1px solid #f3f4f6;

            color: #374151;

            font-size: 13px;

            white-space: nowrap;

        }


        .loan-number {

            color: #111827;

            font-weight: 700;

        }


        .borrower-name {

            color: #111827;

            font-weight: 600;

        }


        .loan-amount {

            color: #111827;

            font-weight: 600;

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        .loan-status {

            display: inline-flex;

            align-items: center;

            padding: 5px 9px;

            border-radius: 999px;

            font-size: 11px;

            font-weight: 700;

            text-transform: capitalize;

        }


        .status-active {

            background: #ecfdf5;

            color: #047857;

        }


        .status-approved {

            background: #eff6ff;

            color: #1d4ed8;

        }


        .status-pending {

            background: #fffbeb;

            color: #b45309;

        }


        .status-completed {

            background: #f0fdf4;

            color: #15803d;

        }


        .status-overdue {

            background: #fff7ed;

            color: #c2410c;

        }


        .status-danger {

            background: #fef2f2;

            color: #b91c1c;

        }


        .status-default {

            background: #f3f4f6;

            color: #4b5563;

        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .dashboard-empty {

            padding: 35px 20px;

            text-align: center;

            color: #9ca3af;

            font-size: 14px;

        }


        /*
        |--------------------------------------------------------------------------
        | BUSINESS INFORMATION
        |--------------------------------------------------------------------------
        */

        .business-info {

            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );

            gap: 14px;

            padding: 20px;

        }


        .business-info-item {

            padding: 16px;

            background: #f9fafb;

            border-radius: 12px;

        }


        .business-info-label {

            color: #9ca3af;

            font-size: 10px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .05em;

            margin-bottom: 6px;

        }


        .business-info-value {

            color: #111827;

            font-size: 14px;

            font-weight: 600;

            word-break: break-word;

        }


        /*
        |--------------------------------------------------------------------------
        | TABLET
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1100px) {

            .dashboard-stats {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );

            }


            .dashboard-grid {

                grid-template-columns: 1fr;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 650px) {

            .dashboard-welcome {

                margin-bottom: 20px;

            }


            .dashboard-welcome h1 {

                font-size: 23px;

            }


            .dashboard-stats {

                grid-template-columns: 1fr;

                gap: 12px;

                margin-bottom: 16px;

            }


            .dashboard-stat {

                padding: 17px;

                border-radius: 13px;

            }


            .dashboard-stat-value {

                font-size: 23px;

            }


            .dashboard-grid {

                gap: 15px;

                margin-bottom: 15px;

            }


            .dashboard-panel {

                border-radius: 13px;

            }


            .dashboard-panel-header {

                padding: 16px;

            }


            .dashboard-panel-header h2 {

                font-size: 16px;

            }


            .account-item {

                padding: 14px 16px;

            }


            .account-icon {

                width: 38px;

                height: 38px;

                min-width: 38px;

            }


            .account-name {

                max-width: 150px;

            }


            .account-balance {

                font-size: 13px;

            }


            .financial-summary {

                grid-template-columns: 1fr;

                padding: 15px;

            }


            .business-info {

                grid-template-columns: 1fr;

                padding: 15px;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | VERY SMALL PHONES
        |--------------------------------------------------------------------------
        */

        @media (max-width: 400px) {

            .dashboard-welcome h1 {

                font-size: 21px;

            }


            .dashboard-stat-value {

                font-size: 21px;

            }


            .account-item {

                align-items: flex-start;

            }


            .account-balance {

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
         CONTENT
    ====================================================== -->

    <div class="container dashboard-page">


        <!-- =================================================
             WELCOME
        ================================================== -->

        <div class="dashboard-welcome">

            <h1>

                Welcome,
                <?= htmlspecialchars(
                    $userName
                ) ?>

            </h1>


            <p>

                <?= htmlspecialchars(
                    $businessName
                ) ?>

            </p>

        </div>


        <!-- =================================================
             MAIN STATISTICS
        ================================================== -->

        <div class="dashboard-stats">


            <!-- BORROWERS -->

            <div class="dashboard-stat">

                <div class="dashboard-stat-top">

                    <div class="dashboard-stat-label">

                        Total Borrowers

                    </div>


                    <div class="dashboard-stat-icon">

                        B

                    </div>

                </div>


                <div class="dashboard-stat-value">

                    <?= number_format(
                        $totalBorrowers
                    ) ?>

                </div>


                <div class="dashboard-stat-description">

                    Registered borrowers

                </div>

            </div>


            <!-- ACTIVE LOANS -->

            <div class="dashboard-stat">

                <div class="dashboard-stat-top">

                    <div class="dashboard-stat-label">

                        Active Loans

                    </div>


                    <div class="dashboard-stat-icon">

                        L

                    </div>

                </div>


                <div class="dashboard-stat-value">

                    <?= number_format(
                        $activeLoans
                    ) ?>

                </div>


                <div class="dashboard-stat-description">

                    Currently active loans

                </div>

            </div>


            <!-- OUTSTANDING -->

            <div class="dashboard-stat">

                <div class="dashboard-stat-top">

                    <div class="dashboard-stat-label">

                        Outstanding Loans

                    </div>


                    <div class="dashboard-stat-icon">

                        ₱

                    </div>

                </div>


                <div class="dashboard-stat-value">

                    <?= dashboardMoney(
                        $outstandingBalance
                    ) ?>

                </div>


                <div class="dashboard-stat-description">

                    Total payable of active loans

                </div>

            </div>


            <!-- AVAILABLE FUNDS -->

            <div class="dashboard-stat">

                <div class="dashboard-stat-top">

                    <div class="dashboard-stat-label">

                        Available Funds

                    </div>


                    <div class="dashboard-stat-icon">

                        ₱

                    </div>

                </div>


                <div class="dashboard-stat-value">

                    <?= dashboardMoney(
                        $totalFunds
                    ) ?>

                </div>


                <div class="dashboard-stat-description">

                    Active asset account balances

                </div>

            </div>


        </div>


        <!-- =================================================
             ACCOUNTS + FINANCIAL SUMMARY
        ================================================== -->

        <div class="dashboard-grid">


            <!-- =================================================
                 BUSINESS ACCOUNTS
            ================================================== -->

            <div class="dashboard-panel">


                <div class="dashboard-panel-header">

                    <h2>

                        Business Accounts

                    </h2>


                    <p>

                        Current balances of your active accounts

                    </p>

                </div>


                <?php if (
                    !empty($accounts)
                ): ?>


                    <div class="account-list">


                        <?php foreach (
                            $accounts
                            as $account
                        ): ?>


                            <div class="account-item">


                                <div class="account-left">


                                    <div class="account-icon">

                                        ₱

                                    </div>


                                    <div class="account-info">

                                        <div class="account-name">

                                            <?= htmlspecialchars(
                                                $account['account_name']
                                                ??
                                                'Unnamed Account'
                                            ) ?>

                                        </div>


                                        <div class="account-type">

                                            <?= htmlspecialchars(
                                                $account['account_type']
                                                ??
                                                'Account'
                                            ) ?>

                                        </div>

                                    </div>

                                </div>


                                <div class="account-balance">

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


                    <div class="account-total">

                        <span class="account-total-label">

                            Available Funds

                        </span>


                        <span class="account-total-value">

                            <?= dashboardMoney(
                                $totalFunds
                            ) ?>

                        </span>

                    </div>


                <?php else: ?>


                    <div class="dashboard-empty">

                        No active accounts found.

                    </div>


                <?php endif; ?>


            </div>


            <!-- =================================================
                 FINANCIAL SUMMARY
            ================================================== -->

            <div class="dashboard-panel">


                <div class="dashboard-panel-header">

                    <h2>

                        Financial Summary

                    </h2>


                    <p>

                        Current business position

                    </p>

                </div>


                <div class="financial-summary">


                    <div class="financial-item">

                        <div class="financial-label">

                            Total Accounts

                        </div>


                        <div class="financial-value">

                            <?= number_format(
                                $totalAccounts
                            ) ?>

                        </div>

                    </div>


                    <div class="financial-item">

                        <div class="financial-label">

                            Total Assets

                        </div>


                        <div class="financial-value">

                            <?= dashboardMoney(
                                $totalAssets
                            ) ?>

                        </div>

                    </div>


                    <div class="financial-item">

                        <div class="financial-label">

                            Liabilities

                        </div>


                        <div class="financial-value">

                            <?= dashboardMoney(
                                $totalLiabilities
                            ) ?>

                        </div>

                    </div>


                    <div class="financial-item">

                        <div class="financial-label">

                            Net Balance

                        </div>


                        <div class="financial-value">

                            <?= dashboardMoney(
                                $netBalance
                            ) ?>

                        </div>

                    </div>


                </div>


            </div>


        </div>


        <!-- =================================================
             RECENT LOANS
        ================================================== -->

        <div class="dashboard-panel">


            <div class="dashboard-panel-header">

                <h2>

                    Recent Loans

                </h2>


                <p>

                    Latest loan records

                </p>

            </div>


            <?php if (
                !empty($recentLoans)
            ): ?>


                <div class="loan-table-wrapper">


                    <table class="loan-table">


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


                                <tr>


                                    <td>

                                        <span class="loan-number">

                                            <?= htmlspecialchars(
                                                $loan['loan_number']
                                                ??
                                                'N/A'
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span class="borrower-name">

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

                                        <span class="loan-amount">

                                            <?= dashboardMoney(
                                                (float) (
                                                    $loan['principal_amount']
                                                    ??
                                                    0
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span class="loan-amount">

                                            <?= dashboardMoney(
                                                (float) (
                                                    $loan['total_payable']
                                                    ??
                                                    0
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="loan-status <?= htmlspecialchars(
                                                dashboardStatusClass(
                                                    $loan['status']
                                                    ??
                                                    ''
                                                )
                                            ) ?>"
                                        >

                                            <?= htmlspecialchars(
                                                $loan['status']
                                                ??
                                                'unknown'
                                            ) ?>

                                        </span>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            <?php else: ?>


                <div class="dashboard-empty">

                    No loans have been created yet.

                </div>


            <?php endif; ?>


        </div>


        <!-- =================================================
             BUSINESS INFORMATION
        ================================================== -->

        <div
            class="dashboard-panel"
            style="margin-top:20px;"
        >


            <div class="dashboard-panel-header">

                <h2>

                    Business Information

                </h2>


                <p>

                    Current business information

                </p>

            </div>


            <div class="business-info">


                <div class="business-info-item">

                    <div class="business-info-label">

                        Business

                    </div>


                    <div class="business-info-value">

                        <?= htmlspecialchars(
                            $businessName
                        ) ?>

                    </div>

                </div>


                <div class="business-info-item">

                    <div class="business-info-label">

                        Business ID

                    </div>


                    <div class="business-info-value">

                        <?= htmlspecialchars(
                            (string) $businessId
                        ) ?>

                    </div>

                </div>


                <div class="business-info-item">

                    <div class="business-info-label">

                        Your Role

                    </div>


                    <div class="business-info-value">

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
