<?php

/*
|--------------------------------------------------------------------------
| REPORT DATA
|--------------------------------------------------------------------------
*/

$user =
    $user
    ??
    Auth::user();

$business =
    $business
    ??
    Auth::business();

$tenantRole =
    $tenantRole
    ??
    Auth::tenantRole();

$currentUrl =
    $currentUrl
    ??
    'reports/index';


$loanSummary =
    $loanSummary
    ??
    [];

$financialSummary =
    $financialSummary
    ??
    [];

$paymentSummary =
    $paymentSummary
    ??
    [];

$borrowerSummary =
    $borrowerSummary
    ??
    [];

$recentPayments =
    $recentPayments
    ??
    [];

$recentLoans =
    $recentLoans
    ??
    [];


/*
|--------------------------------------------------------------------------
| LOAN SUMMARY
|--------------------------------------------------------------------------
*/

$totalLoans =
    (int) (
        $loanSummary['total_loans']
        ??
        0
    );

$pendingLoans =
    (int) (
        $loanSummary['pending_loans']
        ??
        0
    );

$approvedLoans =
    (int) (
        $loanSummary['approved_loans']
        ??
        0
    );

$activeLoans =
    (int) (
        $loanSummary['active_loans']
        ??
        0
    );

$overdueLoans =
    (int) (
        $loanSummary['overdue_loans']
        ??
        0
    );

$completedLoans =
    (int) (
        $loanSummary['completed_loans']
        ??
        0
    );

$rejectedLoans =
    (int) (
        $loanSummary['rejected_loans']
        ??
        0
    );

$cancelledLoans =
    (int) (
        $loanSummary['cancelled_loans']
        ??
        0
    );


/*
|--------------------------------------------------------------------------
| FINANCIAL SUMMARY
|--------------------------------------------------------------------------
*/

$totalPrincipal =
    (float) (
        $financialSummary['total_principal']
        ??
        0
    );

$totalInterest =
    (float) (
        $financialSummary['total_interest']
        ??
        0
    );

$totalProcessingFee =
    (float) (
        $financialSummary['total_processing_fee']
        ??
        0
    );

$totalPayable =
    (float) (
        $financialSummary['total_payable']
        ??
        0
    );


/*
|--------------------------------------------------------------------------
| PAYMENT SUMMARY
|--------------------------------------------------------------------------
*/

$paymentCount =
    (int) (
        $paymentSummary['payment_count']
        ??
        0
    );

$totalCollected =
    (float) (
        $paymentSummary['total_collected']
        ??
        0
    );

$principalCollected =
    (float) (
        $paymentSummary['principal_collected']
        ??
        0
    );

$interestCollected =
    (float) (
        $paymentSummary['interest_collected']
        ??
        0
    );

$penaltyCollected =
    (float) (
        $paymentSummary['penalty_collected']
        ??
        0
    );


/*
|--------------------------------------------------------------------------
| OUTSTANDING
|--------------------------------------------------------------------------
*/

$outstandingBalance =
    (float) (
        $outstandingBalance
        ??
        0
    );


/*
|--------------------------------------------------------------------------
| BORROWERS
|--------------------------------------------------------------------------
*/

$totalBorrowers =
    (int) (
        $borrowerSummary['total_borrowers']
        ??
        0
    );

$activeBorrowers =
    (int) (
        $borrowerSummary['active_borrowers']
        ??
        0
    );

$inactiveBorrowers =
    (int) (
        $borrowerSummary['inactive_borrowers']
        ??
        0
    );


/*
|--------------------------------------------------------------------------
| COLLECTION RATE
|--------------------------------------------------------------------------
*/

$collectionRate = 0;

if ($totalPayable > 0) {

    $collectionRate =
        min(
            100,
            ($totalCollected / $totalPayable) * 100
        );
}


/*
|--------------------------------------------------------------------------
| BUSINESS NAME
|--------------------------------------------------------------------------
*/

$businessName =
    $business['name']
    ??
    'Loan Management';


/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function reportMoney(float $amount): string
{
    return '₱' . number_format(
        $amount,
        2
    );
}


function reportDate(?string $date): string
{
    if (
        empty($date)
    ) {
        return '—';
    }

    $timestamp =
        strtotime($date);

    if (
        $timestamp === false
    ) {
        return '—';
    }

    return date(
        'M d, Y',
        $timestamp
    );
}


function reportStatusClass(string $status): string
{
    return strtolower(
        trim($status)
    );
}

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
        Reports | <?= htmlspecialchars($businessName) ?>
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | REPORT PAGE
        |--------------------------------------------------------------------------
        */

        .reports-page {
            padding-bottom: 40px;
        }


        /*
        |--------------------------------------------------------------------------
        | REPORT HEADER
        |--------------------------------------------------------------------------
        */

        .report-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }


        .report-header-left h1 {
            margin: 0 0 6px;
            font-size: 26px;
            font-weight: 700;
        }


        .report-header-left p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }


        .report-date {
            padding: 9px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #ffffff;
            color: #6b7280;
            font-size: 13px;
            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | FINANCIAL CARDS
        |--------------------------------------------------------------------------
        */

        .report-financial-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
                );

            gap: 16px;
            margin-bottom: 22px;
        }


        .report-financial-card {
            position: relative;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            overflow: hidden;
        }


        .report-financial-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #2563eb;
        }


        .report-financial-card.collected::before {
            background: #16a34a;
        }


        .report-financial-card.outstanding::before {
            background: #dc2626;
        }


        .report-financial-card.payable::before {
            background: #7c3aed;
        }


        .report-financial-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 8px;
        }


        .report-financial-value {
            color: #111827;
            font-size: 23px;
            font-weight: 700;
            line-height: 1.2;
        }


        .report-financial-meta {
            margin-top: 8px;
            color: #9ca3af;
            font-size: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | TWO COLUMN LAYOUT
        |--------------------------------------------------------------------------
        */

        .report-grid {
            display: grid;
            grid-template-columns:
                minmax(0, 1.55fr)
                minmax(300px, 1fr);

            gap: 20px;
            margin-bottom: 20px;
        }


        /*
        |--------------------------------------------------------------------------
        | REPORT CARD
        |--------------------------------------------------------------------------
        */

        .report-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }


        .report-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 18px 20px;
            border-bottom: 1px solid #f0f1f3;
        }


        .report-card-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }


        .report-card-subtitle {
            margin: 4px 0 0;
            font-size: 12px;
            color: #9ca3af;
        }


        .report-card-body {
            padding: 20px;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAN STATUS GRID
        |--------------------------------------------------------------------------
        */

        .loan-status-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );

            gap: 12px;
        }


        .loan-status-item {
            border: 1px solid #eef0f3;
            border-radius: 8px;
            padding: 14px;
            background: #fafafa;
        }


        .loan-status-label {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 6px;
        }


        .loan-status-value {
            color: #111827;
            font-size: 21px;
            font-weight: 700;
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS COLORS
        |--------------------------------------------------------------------------
        */

        .loan-status-item.pending {
            border-left: 3px solid #f59e0b;
        }


        .loan-status-item.approved {
            border-left: 3px solid #2563eb;
        }


        .loan-status-item.active {
            border-left: 3px solid #16a34a;
        }


        .loan-status-item.overdue {
            border-left: 3px solid #dc2626;
        }


        .loan-status-item.completed {
            border-left: 3px solid #059669;
        }


        .loan-status-item.rejected {
            border-left: 3px solid #ef4444;
        }


        .loan-status-item.cancelled {
            border-left: 3px solid #6b7280;
        }


        /*
        |--------------------------------------------------------------------------
        | COLLECTION BREAKDOWN
        |--------------------------------------------------------------------------
        */

        .collection-list {
            display: flex;
            flex-direction: column;
            gap: 0;
        }


        .collection-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 13px 0;
            border-bottom: 1px solid #f1f2f4;
        }


        .collection-row:last-child {
            border-bottom: none;
        }


        .collection-label {
            color: #6b7280;
            font-size: 13px;
        }


        .collection-value {
            color: #111827;
            font-size: 14px;
            font-weight: 600;
        }


        .collection-total {
            margin-top: 4px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
        }


        .collection-total .collection-label,
        .collection-total .collection-value {
            color: #111827;
            font-weight: 700;
        }


        /*
        |--------------------------------------------------------------------------
        | PROGRESS BAR
        |--------------------------------------------------------------------------
        */

        .collection-progress {
            margin-top: 20px;
        }


        .collection-progress-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }


        .collection-progress-label {
            color: #6b7280;
            font-size: 12px;
        }


        .collection-progress-value {
            color: #111827;
            font-size: 12px;
            font-weight: 700;
        }


        .progress-track {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: #eef0f3;
            overflow: hidden;
        }


        .progress-fill {
            height: 100%;
            border-radius: inherit;
            background: #16a34a;
        }


        /*
        |--------------------------------------------------------------------------
        | BORROWER STATS
        |--------------------------------------------------------------------------
        */

        .borrower-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );

            gap: 12px;
        }


        .borrower-item {
            text-align: center;
            padding: 18px 10px;
            border: 1px solid #eef0f3;
            border-radius: 8px;
        }


        .borrower-number {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }


        .borrower-label {
            margin-top: 5px;
            color: #6b7280;
            font-size: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        .report-table-wrapper {
            overflow-x: auto;
        }


        .report-table {
            width: 100%;
            border-collapse: collapse;
        }


        .report-table th {
            padding: 11px 16px;
            text-align: left;
            background: #f9fafb;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }


        .report-table td {
            padding: 13px 16px;
            color: #374151;
            font-size: 13px;
            border-bottom: 1px solid #f1f2f4;
            white-space: nowrap;
        }


        .report-table tbody tr:last-child td {
            border-bottom: none;
        }


        .report-table tbody tr:hover {
            background: #fafafa;
        }


        .report-loan-number {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }


        .report-loan-number:hover {
            text-decoration: underline;
        }


        .report-amount {
            color: #111827;
            font-weight: 600;
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS BADGES
        |--------------------------------------------------------------------------
        */

        .report-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
        }


        .report-status.posted {
            background: #dcfce7;
            color: #166534;
        }


        .report-status.pending {
            background: #fef3c7;
            color: #92400e;
        }


        .report-status.active {
            background: #dcfce7;
            color: #166534;
        }


        .report-status.approved {
            background: #dbeafe;
            color: #1e40af;
        }


        .report-status.overdue {
            background: #fee2e2;
            color: #991b1b;
        }


        .report-status.completed {
            background: #d1fae5;
            color: #065f46;
        }


        .report-status.rejected,
        .report-status.cancelled {
            background: #f3f4f6;
            color: #4b5563;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .report-empty {
            text-align: center;
            padding: 35px 20px;
            color: #9ca3af;
        }


        .report-empty-title {
            margin: 0 0 5px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }


        .report-empty-text {
            margin: 0;
            font-size: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (
            max-width: 1100px
        ) {

            .report-financial-grid {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );
            }


            .report-grid {
                grid-template-columns:
                    1fr;
            }

        }


        @media (
            max-width: 700px
        ) {

            .report-header {
                flex-direction: column;
            }


            .report-financial-grid {
                grid-template-columns:
                    1fr;
            }


            .loan-status-grid {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );
            }


            .borrower-grid {
                grid-template-columns:
                    1fr;
            }

        }


        @media (
            max-width: 480px
        ) {

            .loan-status-grid {
                grid-template-columns:
                    1fr;
            }

        }

    </style>

</head>


<body>


<?php

require APP_PATH .
    '/views/layouts/sidebar.php';

?>


<div class="main-content reports-page">


    <!-- ==========================================================
         NAVBAR
    =========================================================== -->

    <nav class="navbar">

        <div class="page-title">

            Reports

        </div>


        <div class="user-info">

            <span class="user-name">

                <?= htmlspecialchars(
                    $user['full_name']
                    ??
                    $user['username']
                    ??
                    'User'
                ) ?>

            </span>


            <span class="badge">

                <?= htmlspecialchars(
                    $tenantRole
                    ??
                    'User'
                ) ?>

            </span>

        </div>

    </nav>


    <div class="container">


        <!-- ==========================================================
             REPORT HEADER
        =========================================================== -->

        <div class="report-header">

            <div class="report-header-left">

                <h1>
                    Financial Reports
                </h1>

                <p>
                    Overview of your loan portfolio,
                    collections and borrower activity.
                </p>

            </div>


            <div class="report-date">

                <?= date('F d, Y') ?>

            </div>

        </div>


        <!-- ==========================================================
             FINANCIAL SUMMARY
        =========================================================== -->

        <div class="report-financial-grid">


            <div class="report-financial-card">

                <div class="report-financial-label">

                    Principal Released

                </div>


                <div class="report-financial-value">

                    <?= reportMoney(
                        $totalPrincipal
                    ) ?>

                </div>


                <div class="report-financial-meta">

                    Total principal amount

                </div>

            </div>



            <div class="report-financial-card payable">

                <div class="report-financial-label">

                    Total Payable

                </div>


                <div class="report-financial-value">

                    <?= reportMoney(
                        $totalPayable
                    ) ?>

                </div>


                <div class="report-financial-meta">

                    Principal + interest + fees

                </div>

            </div>



            <div class="report-financial-card collected">

                <div class="report-financial-label">

                    Total Collected

                </div>


                <div class="report-financial-value">

                    <?= reportMoney(
                        $totalCollected
                    ) ?>

                </div>


                <div class="report-financial-meta">

                    <?= number_format(
                        $paymentCount
                    ) ?>

                    posted payments

                </div>

            </div>



            <div class="report-financial-card outstanding">

                <div class="report-financial-label">

                    Outstanding Balance

                </div>


                <div class="report-financial-value">

                    <?= reportMoney(
                        $outstandingBalance
                    ) ?>

                </div>


                <div class="report-financial-meta">

                    Remaining amount to collect

                </div>

            </div>


        </div>


        <!-- ==========================================================
             LOAN STATUS + COLLECTION
        =========================================================== -->

        <div class="report-grid">


            <!-- LOAN PORTFOLIO -->

            <div class="report-card">

                <div class="report-card-header">

                    <div>

                        <h2 class="report-card-title">

                            Loan Portfolio

                        </h2>


                        <p class="report-card-subtitle">

                            Current loan status breakdown

                        </p>

                    </div>


                    <strong>

                        <?= number_format(
                            $totalLoans
                        ) ?>

                    </strong>

                </div>


                <div class="report-card-body">

                    <div class="loan-status-grid">


                        <div class="loan-status-item pending">

                            <div class="loan-status-label">
                                Pending
                            </div>

                            <div class="loan-status-value">

                                <?= number_format(
                                    $pendingLoans
                                ) ?>

                            </div>

                        </div>



                        <div class="loan-status-item approved">

                            <div class="loan-status-label">
                                Approved
                            </div>

                            <div class="loan-status-value">

                                <?= number_format(
                                    $approvedLoans
                                ) ?>

                            </div>

                        </div>



                        <div class="loan-status-item active">

                            <div class="loan-status-label">
                                Active
                            </div>

                            <div class="loan-status-value">

                                <?= number_format(
                                    $activeLoans
                                ) ?>

                            </div>

                        </div>



                        <div class="loan-status-item overdue">

                            <div class="loan-status-label">
                                Overdue
                            </div>

                            <div class="loan-status-value">

                                <?= number_format(
                                    $overdueLoans
                                ) ?>

                            </div>

                        </div>



                        <div class="loan-status-item completed">

                            <div class="loan-status-label">
                                Completed
                            </div>

                            <div class="loan-status-value">

                                <?= number_format(
                                    $completedLoans
                                ) ?>

                            </div>

                        </div>



                        <div class="loan-status-item rejected">

                            <div class="loan-status-label">
                                Rejected
                            </div>

                            <div class="loan-status-value">

                                <?= number_format(
                                    $rejectedLoans
                                ) ?>

                            </div>

                        </div>



                        <div class="loan-status-item cancelled">

                            <div class="loan-status-label">
                                Cancelled
                            </div>

                            <div class="loan-status-value">

                                <?= number_format(
                                    $cancelledLoans
                                ) ?>

                            </div>

                        </div>


                    </div>

                </div>

            </div>



            <!-- COLLECTION -->

            <div class="report-card">

                <div class="report-card-header">

                    <div>

                        <h2 class="report-card-title">

                            Collection Summary

                        </h2>


                        <p class="report-card-subtitle">

                            Posted payment breakdown

                        </p>

                    </div>

                </div>


                <div class="report-card-body">


                    <div class="collection-list">


                        <div class="collection-row">

                            <span class="collection-label">
                                Principal Collected
                            </span>

                            <span class="collection-value">

                                <?= reportMoney(
                                    $principalCollected
                                ) ?>

                            </span>

                        </div>



                        <div class="collection-row">

                            <span class="collection-label">
                                Interest Collected
                            </span>

                            <span class="collection-value">

                                <?= reportMoney(
                                    $interestCollected
                                ) ?>

                            </span>

                        </div>



                        <div class="collection-row">

                            <span class="collection-label">
                                Penalties Collected
                            </span>

                            <span class="collection-value">

                                <?= reportMoney(
                                    $penaltyCollected
                                ) ?>

                            </span>

                        </div>



                        <div class="collection-row collection-total">

                            <span class="collection-label">
                                Total Collected
                            </span>

                            <span class="collection-value">

                                <?= reportMoney(
                                    $totalCollected
                                ) ?>

                            </span>

                        </div>


                    </div>


                    <div class="collection-progress">

                        <div class="collection-progress-top">

                            <span class="collection-progress-label">

                                Collection Rate

                            </span>


                            <span class="collection-progress-value">

                                <?= number_format(
                                    $collectionRate,
                                    1
                                ) ?>%

                            </span>

                        </div>


                        <div class="progress-track">

                            <div
                                class="progress-fill"
                                style="width: <?= $collectionRate ?>%;"
                            ></div>

                        </div>

                    </div>

                </div>

            </div>


        </div>


        <!-- ==========================================================
             BORROWER SUMMARY
        =========================================================== -->

        <div class="report-card" style="margin-bottom: 20px;">

            <div class="report-card-header">

                <div>

                    <h2 class="report-card-title">

                        Borrower Overview

                    </h2>


                    <p class="report-card-subtitle">

                        Borrower statistics for this business

                    </p>

                </div>

            </div>


            <div class="report-card-body">

                <div class="borrower-grid">


                    <div class="borrower-item">

                        <div class="borrower-number">

                            <?= number_format(
                                $totalBorrowers
                            ) ?>

                        </div>


                        <div class="borrower-label">

                            Total Borrowers

                        </div>

                    </div>



                    <div class="borrower-item">

                        <div class="borrower-number">

                            <?= number_format(
                                $activeBorrowers
                            ) ?>

                        </div>


                        <div class="borrower-label">

                            Active Borrowers

                        </div>

                    </div>



                    <div class="borrower-item">

                        <div class="borrower-number">

                            <?= number_format(
                                $inactiveBorrowers
                            ) ?>

                        </div>


                        <div class="borrower-label">

                            Inactive Borrowers

                        </div>

                    </div>


                </div>

            </div>

        </div>


        <!-- ==========================================================
             RECENT PAYMENTS
        =========================================================== -->

        <div class="report-card" style="margin-bottom: 20px;">

            <div class="report-card-header">

                <div>

                    <h2 class="report-card-title">

                        Recent Payments

                    </h2>


                    <p class="report-card-subtitle">

                        Latest posted payments

                    </p>

                </div>


                <a
                    href="index.php?url=payments"
                    class="btn btn-primary"
                >

                    View Payments

                </a>

            </div>


            <div class="report-table-wrapper">


                <?php if (
                    empty($recentPayments)
                ): ?>


                    <div class="report-empty">

                        <p class="report-empty-title">

                            No payments found

                        </p>


                        <p class="report-empty-text">

                            No posted payments have been recorded yet.

                        </p>

                    </div>


                <?php else: ?>


                    <table class="report-table">

                        <thead>

                            <tr>

                                <th>
                                    Payment #
                                </th>

                                <th>
                                    Loan #
                                </th>

                                <th>
                                    Borrower
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach (
                            $recentPayments
                            as $payment
                        ): ?>


                            <?php

                            $status =
                                $payment['status']
                                ??
                                'posted';

                            ?>


                            <tr>


                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $payment[
                                                'payment_number'
                                            ]
                                            ??
                                            '—'
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $payment[
                                            'loan_number'
                                        ]
                                        ??
                                        '—'
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        trim(
                                            $payment[
                                                'borrower_name'
                                            ]
                                            ??
                                            '—'
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <?= reportDate(
                                        $payment[
                                            'payment_date'
                                        ]
                                        ??
                                        null
                                    ) ?>

                                </td>


                                <td>

                                    <span class="report-amount">

                                        <?= reportMoney(
                                            (float)(
                                                $payment[
                                                    'amount'
                                                ]
                                                ??
                                                0
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="report-status <?= htmlspecialchars(
                                            reportStatusClass(
                                                $status
                                            )
                                        ) ?>"
                                    >

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $status
                                            )
                                        ) ?>

                                    </span>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>

                    </table>


                <?php endif; ?>


            </div>

        </div>


        <!-- ==========================================================
             RECENT LOANS
        =========================================================== -->

        <div class="report-card">

            <div class="report-card-header">

                <div>

                    <h2 class="report-card-title">

                        Recent Loans

                    </h2>


                    <p class="report-card-subtitle">

                        Latest loans created in the system

                    </p>

                </div>


                <a
                    href="index.php?url=loans"
                    class="btn btn-primary"
                >

                    View Loans

                </a>

            </div>


            <div class="report-table-wrapper">


                <?php if (
                    empty($recentLoans)
                ): ?>


                    <div class="report-empty">

                        <p class="report-empty-title">

                            No loans found

                        </p>


                        <p class="report-empty-text">

                            No loans have been recorded yet.

                        </p>

                    </div>


                <?php else: ?>


                    <table class="report-table">

                        <thead>

                            <tr>

                                <th>
                                    Loan #
                                </th>

                                <th>
                                    Borrower
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

                                <th>
                                    Created
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach (
                            $recentLoans
                            as $loan
                        ): ?>


                            <?php

                            $status =
                                $loan['status']
                                ??
                                'pending';

                            ?>


                            <tr>


                                <td>

                                    <a
                                        href="index.php?url=loans/show&id=<?= (int)(
                                            $loan['id']
                                            ??
                                            0
                                        ) ?>"
                                        class="report-loan-number"
                                    >

                                        <?= htmlspecialchars(
                                            $loan[
                                                'loan_number'
                                            ]
                                            ??
                                            '—'
                                        ) ?>

                                    </a>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        trim(
                                            $loan[
                                                'borrower_name'
                                            ]
                                            ??
                                            '—'
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <span class="report-amount">

                                        <?= reportMoney(
                                            (float)(
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

                                    <span class="report-amount">

                                        <?= reportMoney(
                                            (float)(
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
                                        class="report-status <?= htmlspecialchars(
                                            reportStatusClass(
                                                $status
                                            )
                                        ) ?>"
                                    >

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $status
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= reportDate(
                                        $loan[
                                            'created_at'
                                        ]
                                        ??
                                        null
                                    ) ?>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>

                    </table>


                <?php endif; ?>


            </div>

        </div>


    </div>

</div>


</body>

</html>