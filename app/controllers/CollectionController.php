<?php

/*
|--------------------------------------------------------------------------
| AUTH / BUSINESS DATA
|--------------------------------------------------------------------------
*/

$user =
    $user ?? Auth::user();

$business =
    $business ?? Auth::business();

$tenantRole =
    $tenantRole ?? Auth::tenantRole();

$currentUrl =
    $currentUrl ?? 'borrowers';


/*
|--------------------------------------------------------------------------
| BORROWER INFORMATION
|--------------------------------------------------------------------------
*/

$borrowerId =
    (int)($borrower['id'] ?? 0);

$borrowerCode =
    $borrower['borrower_code'] ?? '-';

$firstName =
    $borrower['first_name'] ?? '';

$middleName =
    $borrower['middle_name'] ?? '';

$lastName =
    $borrower['last_name'] ?? '';

$fullName =
    trim(
        $firstName . ' ' .
        $middleName . ' ' .
        $lastName
    );

if ($fullName === '') {
    $fullName = 'Unnamed Borrower';
}


$status =
    $borrower['status'] ?? 'active';

$statusClass =
    'status-' . $status;


/*
|--------------------------------------------------------------------------
| SUMMARY VALUES
|--------------------------------------------------------------------------
*/

$totalLoans =
    (int)($totalLoans ?? 0);

$totalPrincipal =
    (float)($totalPrincipal ?? 0);

$totalPaid =
    (float)($totalPaid ?? 0);

$remainingBalance =
    max(
        0,
        (float)($remainingBalance ?? 0)
    );

$totalPayable =
    (float)($totalPayable ?? 0);

$activeLoans =
    (int)($activeLoans ?? 0);

$pendingLoans =
    (int)($pendingLoans ?? 0);

$completedLoans =
    (int)($completedLoans ?? 0);

$overdueLoans =
    (int)($overdueLoans ?? 0);


/*
|--------------------------------------------------------------------------
| LOANS
|--------------------------------------------------------------------------
*/

$loans =
    is_array($loans ?? null)
        ? $loans
        : [];


/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

function borrowerValue($value, $fallback = '-')
{
    if (
        $value === null ||
        $value === ''
    ) {
        return $fallback;
    }

    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
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
        <?= borrowerValue($fullName) ?>
        | Borrower Details
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | PAGE
        |--------------------------------------------------------------------------
        */

        .borrower-details-page {
            width: 100%;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .borrower-details-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 25px;
        }


        .borrower-details-title {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }


        .borrower-avatar {
            width: 58px;
            height: 58px;

            border-radius: 14px;

            background: #eff6ff;

            color: #2563eb;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 22px;
            font-weight: 700;

            flex-shrink: 0;
        }


        .borrower-details-title h1 {
            margin: 0 0 5px;

            font-size: 27px;
            font-weight: 700;
        }


        .borrower-details-title p {
            margin: 0;

            color: #6b7280;

            font-size: 14px;
        }


        .borrower-header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY CARDS
        |--------------------------------------------------------------------------
        */

        .borrower-summary-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;

            margin-bottom: 22px;
        }


        .borrower-summary-card {
            background: #fff;

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            padding: 20px;

            box-shadow:
                0 2px 8px
                rgba(0, 0, 0, 0.04);
        }


        .borrower-summary-card-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            margin-bottom: 12px;
        }


        .borrower-summary-title {
            color: #6b7280;

            font-size: 13px;

            font-weight: 500;
        }


        .borrower-summary-icon {
            width: 34px;
            height: 34px;

            border-radius: 9px;

            background: #f8fafc;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 16px;
        }


        .borrower-summary-value {
            font-size: 23px;

            font-weight: 700;

            color: #111827;

            line-height: 1.2;
        }


        /*
        |--------------------------------------------------------------------------
        | CONTENT CARDS
        |--------------------------------------------------------------------------
        */

        .borrower-details-card {
            background: #fff;

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            margin-bottom: 22px;

            overflow: hidden;

            box-shadow:
                0 2px 8px
                rgba(0, 0, 0, 0.04);
        }


        .borrower-details-card-header {
            padding: 18px 22px;

            border-bottom:
                1px solid #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;
        }


        .borrower-details-card-header h2 {
            margin: 0;

            font-size: 17px;

            font-weight: 700;

            color: #111827;
        }


        .borrower-details-card-header p {
            margin: 4px 0 0;

            color: #6b7280;

            font-size: 13px;
        }


        .borrower-details-card-body {
            padding: 22px;
        }


        /*
        |--------------------------------------------------------------------------
        | INFORMATION GRID
        |--------------------------------------------------------------------------
        */

        .borrower-info-grid {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 30px;
        }


        .borrower-info-column {
            min-width: 0;
        }


        .borrower-info-row {
            display: grid;

            grid-template-columns:
                160px
                minmax(0, 1fr);

            gap: 15px;

            padding: 13px 0;

            border-bottom:
                1px solid #f1f5f9;
        }


        .borrower-info-row:first-child {
            padding-top: 0;
        }


        .borrower-info-row:last-child {
            border-bottom: none;
        }


        .borrower-info-label {
            color: #64748b;

            font-size: 13px;

            font-weight: 600;
        }


        .borrower-info-value {
            color: #111827;

            font-size: 14px;

            word-break: break-word;
        }


        /*
        |--------------------------------------------------------------------------
        | NOTES
        |--------------------------------------------------------------------------
        */

        .borrower-notes {
            margin-top: 20px;

            padding: 16px;

            background: #f8fafc;

            border: 1px solid #e2e8f0;

            border-radius: 10px;
        }


        .borrower-notes-title {
            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 700;

            color: #475569;
        }


        .borrower-notes-content {
            color: #334155;

            font-size: 14px;

            line-height: 1.6;

            white-space: normal;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAN STATUS GRID
        |--------------------------------------------------------------------------
        */

        .loan-status-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 12px;
        }


        .loan-status-box {
            padding: 16px;

            border-radius: 10px;

            background: #f8fafc;

            border: 1px solid #e2e8f0;
        }


        .loan-status-box-label {
            display: block;

            margin-bottom: 6px;

            color: #64748b;

            font-size: 12px;
        }


        .loan-status-box-value {
            display: block;

            font-size: 20px;

            font-weight: 700;

            color: #111827;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        .borrower-loan-table-wrapper {
            width: 100%;

            overflow-x: auto;
        }


        .borrower-loan-table {
            width: 100%;

            min-width: 1100px;

            border-collapse: collapse;
        }


        .borrower-loan-table th {
            padding: 13px 14px;

            background: #f8fafc;

            border-bottom:
                1px solid #e5e7eb;

            color: #64748b;

            font-size: 12px;

            font-weight: 700;

            text-align: left;

            white-space: nowrap;
        }


        .borrower-loan-table td {
            padding: 14px;

            border-bottom:
                1px solid #f1f5f9;

            color: #374151;

            font-size: 13px;

            white-space: nowrap;
        }


        .borrower-loan-table tbody tr:hover {
            background: #fafafa;
        }


        .borrower-loan-number {
            font-weight: 700;

            color: #2563eb;
        }


        .borrower-loan-balance {
            font-weight: 700;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .borrower-empty-state {
            text-align: center;

            padding: 55px 25px;
        }


        .borrower-empty-icon {
            width: 55px;
            height: 55px;

            margin: 0 auto 15px;

            border-radius: 50%;

            background: #f1f5f9;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 22px;
        }


        .borrower-empty-state h3 {
            margin: 0 0 8px;

            font-size: 17px;
        }


        .borrower-empty-state p {
            margin: 0 0 20px;

            color: #6b7280;

            font-size: 14px;
        }


        /*
        |--------------------------------------------------------------------------
        | ACTIONS
        |--------------------------------------------------------------------------
        */

        .borrower-card-actions {
            display: flex;

            gap: 8px;

            align-items: center;
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1100px) {

            .borrower-summary-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }


            .loan-status-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

        }


        @media (max-width: 850px) {

            .borrower-info-grid {
                grid-template-columns: 1fr;
            }


            .borrower-details-header {
                flex-direction: column;
            }


            .borrower-header-actions {
                width: 100%;
            }

        }


        @media (max-width: 600px) {

            .borrower-summary-grid {
                grid-template-columns: 1fr;
            }


            .loan-status-grid {
                grid-template-columns: 1fr;
            }


            .borrower-details-title h1 {
                font-size: 22px;
            }


            .borrower-info-row {
                grid-template-columns: 1fr;

                gap: 5px;
            }


            .borrower-header-actions {
                flex-direction: column;
            }


            .borrower-header-actions .btn {
                width: 100%;

                text-align: center;
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
*/

require APP_PATH .
    '/views/layouts/sidebar.php';

?>


<div class="main-content">


    <!--
    |--------------------------------------------------------------------------
    | NAVBAR
    |--------------------------------------------------------------------------
    -->

    <nav class="navbar">

        <div class="page-title">

            Borrower Details

        </div>


        <div class="user-info">

            <span class="user-name">

                <?= borrowerValue(
                    $user['full_name']
                    ?? $user['username']
                    ?? 'User'
                ) ?>

            </span>


            <span class="badge">

                <?= borrowerValue(
                    $tenantRole
                    ?? 'User'
                ) ?>

            </span>

        </div>

    </nav>


    <div class="container borrower-details-page">


        <!--
        |--------------------------------------------------------------------------
        | PAGE HEADER
        |--------------------------------------------------------------------------
        -->

        <div class="borrower-details-header">


            <div class="borrower-details-title">


                <div class="borrower-avatar">

                    <?= htmlspecialchars(
                        strtoupper(
                            substr(
                                $fullName,
                                0,
                                1
                            )
                        )
                    ) ?>

                </div>


                <div>

                    <h1>

                        <?= borrowerValue(
                            $fullName
                        ) ?>

                    </h1>


                    <p>

                        Borrower Code:

                        <strong>

                            <?= borrowerValue(
                                $borrowerCode
                            ) ?>

                        </strong>

                    </p>

                </div>


            </div>


            <div class="borrower-header-actions">


                <a
                    href="index.php?url=borrowers"
                    class="btn btn-secondary"
                >

                    ← Back

                </a>


                <a
                    href="index.php?url=borrowers/edit&id=<?= $borrowerId ?>"
                    class="btn btn-primary"
                >

                    ✏️ Edit Borrower

                </a>


            </div>


        </div>


        <!--
        |--------------------------------------------------------------------------
        | SUMMARY CARDS
        |--------------------------------------------------------------------------
        -->

        <div class="borrower-summary-grid">


            <!-- TOTAL LOANS -->

            <div class="borrower-summary-card">

                <div class="borrower-summary-card-header">

                    <span class="borrower-summary-title">

                        Total Loans

                    </span>

                    <span class="borrower-summary-icon">

                        📄

                    </span>

                </div>


                <div class="borrower-summary-value">

                    <?= number_format(
                        $totalLoans
                    ) ?>

                </div>

            </div>


            <!-- PRINCIPAL -->

            <div class="borrower-summary-card">

                <div class="borrower-summary-card-header">

                    <span class="borrower-summary-title">

                        Total Principal

                    </span>

                    <span class="borrower-summary-icon">

                        ₱

                    </span>

                </div>


                <div class="borrower-summary-value">

                    ₱<?= number_format(
                        $totalPrincipal,
                        2
                    ) ?>

                </div>

            </div>


            <!-- PAID -->

            <div class="borrower-summary-card">

                <div class="borrower-summary-card-header">

                    <span class="borrower-summary-title">

                        Total Paid

                    </span>

                    <span class="borrower-summary-icon">

                        ✓

                    </span>

                </div>


                <div class="borrower-summary-value">

                    ₱<?= number_format(
                        $totalPaid,
                        2
                    ) ?>

                </div>

            </div>


            <!-- BALANCE -->

            <div class="borrower-summary-card">

                <div class="borrower-summary-card-header">

                    <span class="borrower-summary-title">

                        Remaining Balance

                    </span>

                    <span class="borrower-summary-icon">

                        ◷

                    </span>

                </div>


                <div class="borrower-summary-value">

                    ₱<?= number_format(
                        $remainingBalance,
                        2
                    ) ?>

                </div>

            </div>


        </div>


        <!--
        |--------------------------------------------------------------------------
        | BORROWER INFORMATION
        |--------------------------------------------------------------------------
        -->

        <div class="borrower-details-card">


            <div class="borrower-details-card-header">

                <div>

                    <h2>
                        Borrower Information
                    </h2>

                    <p>
                        Personal and employment information
                    </p>

                </div>


                <span
                    class="status <?= htmlspecialchars(
                        $statusClass
                    ) ?>"
                >

                    <?= borrowerValue(
                        ucfirst($status)
                    ) ?>

                </span>

            </div>


            <div class="borrower-details-card-body">


                <div class="borrower-info-grid">


                    <!-- LEFT COLUMN -->

                    <div class="borrower-info-column">


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Borrower Code
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrowerCode
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                First Name
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $firstName
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Middle Name
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $middleName
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Last Name
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $lastName
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Email
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrower['email']
                                    ?? null
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Phone
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrower['phone']
                                    ?? null
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Date of Birth
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrower['date_of_birth']
                                    ?? null
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Gender
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    !empty(
                                        $borrower['gender']
                                    )
                                        ? ucfirst(
                                            $borrower['gender']
                                        )
                                        : null
                                ) ?>

                            </div>

                        </div>


                    </div>


                    <!-- RIGHT COLUMN -->

                    <div class="borrower-info-column">


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Address
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrower['address']
                                    ?? null
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                City
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrower['city']
                                    ?? null
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Province
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrower['province']
                                    ?? null
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Postal Code
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrower['postal_code']
                                    ?? null
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Occupation
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrower['occupation']
                                    ?? null
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Employer
                            </div>

                            <div class="borrower-info-value">

                                <?= borrowerValue(
                                    $borrower['employer']
                                    ?? null
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Monthly Income
                            </div>

                            <div class="borrower-info-value">

                                ₱<?= number_format(
                                    (float)(
                                        $borrower[
                                            'monthly_income'
                                        ]
                                        ?? 0
                                    ),
                                    2
                                ) ?>

                            </div>

                        </div>


                        <div class="borrower-info-row">

                            <div class="borrower-info-label">
                                Status
                            </div>

                            <div class="borrower-info-value">

                                <span
                                    class="status <?= htmlspecialchars(
                                        $statusClass
                                    ) ?>"
                                >

                                    <?= borrowerValue(
                                        ucfirst($status)
                                    ) ?>

                                </span>

                            </div>

                        </div>


                    </div>


                </div>


                <?php if (
                    !empty(
                        $borrower['notes']
                    )
                ): ?>

                    <div class="borrower-notes">

                        <div class="borrower-notes-title">

                            Notes

                        </div>


                        <div class="borrower-notes-content">

                            <?= nl2br(
                                htmlspecialchars(
                                    $borrower['notes'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>

                        </div>

                    </div>

                <?php endif; ?>


            </div>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | LOAN STATUS
        |--------------------------------------------------------------------------
        -->

        <div class="borrower-details-card">


            <div class="borrower-details-card-header">

                <div>

                    <h2>
                        Loan Overview
                    </h2>

                    <p>
                        Current loan portfolio status
                    </p>

                </div>

            </div>


            <div class="borrower-details-card-body">


                <div class="loan-status-grid">


                    <div class="loan-status-box">

                        <span class="loan-status-box-label">
                            Active Loans
                        </span>

                        <span class="loan-status-box-value">

                            <?= number_format(
                                $activeLoans
                            ) ?>

                        </span>

                    </div>


                    <div class="loan-status-box">

                        <span class="loan-status-box-label">
                            Pending Loans
                        </span>

                        <span class="loan-status-box-value">

                            <?= number_format(
                                $pendingLoans
                            ) ?>

                        </span>

                    </div>


                    <div class="loan-status-box">

                        <span class="loan-status-box-label">
                            Completed Loans
                        </span>

                        <span class="loan-status-box-value">

                            <?= number_format(
                                $completedLoans
                            ) ?>

                        </span>

                    </div>


                    <div class="loan-status-box">

                        <span class="loan-status-box-label">
                            Overdue Loans
                        </span>

                        <span class="loan-status-box-value">

                            <?= number_format(
                                $overdueLoans
                            ) ?>

                        </span>

                    </div>


                </div>


                <div
                    class="borrower-info-grid"
                    style="margin-top:25px;"
                >


                    <div class="borrower-info-row">

                        <div class="borrower-info-label">
                            Total Payable
                        </div>

                        <div class="borrower-info-value">

                            ₱<?= number_format(
                                $totalPayable,
                                2
                            ) ?>

                        </div>

                    </div>


                    <div class="borrower-info-row">

                        <div class="borrower-info-label">
                            Total Paid
                        </div>

                        <div class="borrower-info-value">

                            ₱<?= number_format(
                                $totalPaid,
                                2
                            ) ?>

                        </div>

                    </div>


                </div>


            </div>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | ALL LOANS
        |--------------------------------------------------------------------------
        -->

        <div class="borrower-details-card">


            <div class="borrower-details-card-header">

                <div>

                    <h2>
                        All Loans
                    </h2>

                    <p>
                        Complete loan history for this borrower
                    </p>

                </div>


                <div class="borrower-card-actions">

                    <a
                        href="index.php?url=loans"
                        class="btn btn-secondary"
                    >

                        View Loans

                    </a>

                </div>

            </div>


            <?php if (empty($loans)): ?>


                <div class="borrower-empty-state">


                    <div class="borrower-empty-icon">

                        📄

                    </div>


                    <h3>
                        No Loans Found
                    </h3>


                    <p>
                        This borrower does not have any loans yet.
                    </p>


                    <a
                        href="index.php?url=loans/create&borrower_id=<?= $borrowerId ?>"
                        class="btn btn-primary"
                    >

                        + Create Loan

                    </a>


                </div>


            <?php else: ?>


                <div class="borrower-loan-table-wrapper">


                    <table class="borrower-loan-table">


                        <thead>

                            <tr>

                                <th>
                                    Loan #
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Principal
                                </th>

                                <th>
                                    Interest
                                </th>

                                <th>
                                    Total Payable
                                </th>

                                <th>
                                    Paid
                                </th>

                                <th>
                                    Balance
                                </th>

                                <th>
                                    Payment Type
                                </th>

                                <th>
                                    Term
                                </th>

                                <th>
                                    Release Date
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach (
                            $loans
                            as $loan
                        ): ?>


                            <?php

                            $loanId =
                                (int)(
                                    $loan['id']
                                    ?? 0
                                );


                            $loanPayable =
                                (float)(
                                    $loan[
                                        'total_payable'
                                    ]
                                    ?? 0
                                );


                            $loanPaid =
                                (float)(
                                    $loan[
                                        'total_paid'
                                    ]
                                    ?? 0
                                );


                            $loanBalance =
                                max(
                                    0,
                                    $loanPayable
                                    - $loanPaid
                                );


                            $loanStatus =
                                $loan['status']
                                ?? 'pending';


                            $paymentType =
                                $loan[
                                    'payment_type'
                                ]
                                ?? '-';


                            $paymentType =
                                ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $paymentType
                                    )
                                );

                            ?>


                            <tr>


                                <!-- LOAN NUMBER -->

                                <td>

                                    <span
                                        class="borrower-loan-number"
                                    >

                                        <?= borrowerValue(
                                            $loan[
                                                'loan_number'
                                            ]
                                            ?? '-'
                                        ) ?>

                                    </span>

                                </td>


                                <!-- CATEGORY -->

                                <td>

                                    <?= borrowerValue(
                                        $loan[
                                            'category_name'
                                        ]
                                        ?? null
                                    ) ?>

                                </td>


                                <!-- PRINCIPAL -->

                                <td>

                                    ₱<?= number_format(
                                        (float)(
                                            $loan[
                                                'principal_amount'
                                            ]
                                            ?? 0
                                        ),
                                        2
                                    ) ?>

                                </td>


                                <!-- INTEREST -->

                                <td>

                                    ₱<?= number_format(
                                        (float)(
                                            $loan[
                                                'total_interest'
                                            ]
                                            ?? 0
                                        ),
                                        2
                                    ) ?>

                                </td>


                                <!-- PAYABLE -->

                                <td>

                                    ₱<?= number_format(
                                        $loanPayable,
                                        2
                                    ) ?>

                                </td>


                                <!-- PAID -->

                                <td>

                                    ₱<?= number_format(
                                        $loanPaid,
                                        2
                                    ) ?>

                                </td>


                                <!-- BALANCE -->

                                <td>

                                    <span
                                        class="borrower-loan-balance"
                                    >

                                        ₱<?= number_format(
                                            $loanBalance,
                                            2
                                        ) ?>

                                    </span>

                                </td>


                                <!-- PAYMENT TYPE -->

                                <td>

                                    <?= borrowerValue(
                                        $paymentType
                                    ) ?>

                                </td>


                                <!-- TERM -->

                                <td>

                                    <?= borrowerValue(
                                        $loan['term']
                                        ?? null
                                    ) ?>

                                    <?= borrowerValue(
                                        $loan[
                                            'term_period'
                                        ]
                                        ?? null,
                                        ''
                                    ) ?>

                                </td>


                                <!-- RELEASE DATE -->

                                <td>

                                    <?= borrowerValue(
                                        $loan[
                                            'release_date'
                                        ]
                                        ?? null
                                    ) ?>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="status status-<?= htmlspecialchars(
                                            $loanStatus
                                        ) ?>"
                                    >

                                        <?= borrowerValue(
                                            ucfirst(
                                                $loanStatus
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <!-- ACTION -->

                                <td>

                                    <a
                                        href="index.php?url=loans/show&id=<?= $loanId ?>"
                                        class="btn btn-secondary"
                                    >

                                        View

                                    </a>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            <?php endif; ?>


        </div>


    </div>

</div>


</body>

</html>