<?php

/*
|--------------------------------------------------------------------------
| LOANS DASHBOARD
|--------------------------------------------------------------------------
| Complete replacement view.
|--------------------------------------------------------------------------
*/

$user = $user ?? Auth::user();

$business = $business ?? Auth::business();

$tenantRole = $tenantRole ?? Auth::tenantRole();

$currentUrl = 'loans';

$loans = $loans ?? [];

$borrowers = $borrowers ?? [];

$categories = $categories ?? [];

$accounts = $accounts ?? [];

$success = $success
    ?? ($_SESSION['loan_success'] ?? '');

$error = $error
    ?? ($_SESSION['loan_error'] ?? '');


/*
|--------------------------------------------------------------------------
| PHP HELPERS
|--------------------------------------------------------------------------
*/

if (!function_exists('loanFormatPaymentMethod')) {

    function loanFormatPaymentMethod($value): string
    {
        $value = strtolower(
            trim(
                (string) $value
            )
        );

        if ($value === 'full_payment') {
            return 'Full Payment';
        }

        if ($value === 'installment') {
            return 'Installment';
        }

        if ($value === '') {
            return 'Installment';
        }

        return ucwords(
            str_replace(
                '_',
                ' ',
                $value
            )
        );
    }
}


/*
|--------------------------------------------------------------------------
| SUMMARY
|--------------------------------------------------------------------------
*/

$totalLoans = count($loans);

$activeLoans = 0;

$pendingLoans = 0;

$approvedLoans = 0;

$completedLoans = 0;

$totalPrincipal = 0;

$totalInterest = 0;

$totalPayable = 0;


foreach ($loans as $loan) {

    $status = strtolower(
        trim(
            (string) (
                $loan['status']
                ?? 'pending'
            )
        )
    );

    switch ($status) {

        case 'active':
            $activeLoans++;
            break;

        case 'pending':
            $pendingLoans++;
            break;

        case 'approved':
            $approvedLoans++;
            break;

        case 'completed':
            $completedLoans++;
            break;

    }

    $totalPrincipal += (float) (
        $loan['principal_amount']
        ?? 0
    );

    $totalInterest += (float) (
        $loan['total_interest']
        ?? $loan['interest_amount']
        ?? 0
    );

    $totalPayable += (float) (
        $loan['total_payable']
        ?? 0
    );
}


/*
|--------------------------------------------------------------------------
| CLEAR FLASH MESSAGES
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['loan_success'])) {
    unset($_SESSION['loan_success']);
}

if (isset($_SESSION['loan_error'])) {
    unset($_SESSION['loan_error']);
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
        Loans | Loan Management
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

/* ==========================================================================
   LOANS PAGE
   ========================================================================== */

.loans-page {
    width: 100%;
}


/* ==========================================================================
   SUMMARY CARDS
   ========================================================================== */

.loan-summary-grid {
    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 18px;

    margin-bottom: 24px;
}

.loan-summary-card {
    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 12px;

    padding: 20px;

    min-width: 0;

    box-shadow:
        0 2px 8px
        rgba(0, 0, 0, 0.04);

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.loan-summary-card:hover {
    transform: translateY(-2px);

    box-shadow:
        0 7px 20px
        rgba(0, 0, 0, 0.08);
}

.loan-summary-label {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;

    margin-bottom: 9px;

    color: #6b7280;

    font-size: 13px;

    font-weight: 600;
}

.loan-summary-value {
    color: #111827;

    font-size: 25px;

    line-height: 1.2;

    font-weight: 700;

    word-break: break-word;
}

.loan-summary-subtext {
    margin-top: 7px;

    color: #9ca3af;

    font-size: 12px;
}


/* ==========================================================================
   ALERTS
   ========================================================================== */

.loan-alert {
    display: flex;

    align-items: flex-start;

    gap: 10px;

    padding: 13px 15px;

    margin-bottom: 20px;

    border-radius: 9px;

    font-size: 14px;

    line-height: 1.5;
}

.loan-alert-success {
    color: #166534;

    background: #f0fdf4;

    border: 1px solid #bbf7d0;
}

.loan-alert-error {
    color: #991b1b;

    background: #fef2f2;

    border: 1px solid #fecaca;
}


/* ==========================================================================
   TABLE WRAPPER
   ========================================================================== */

.loan-table-card {
    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 12px;

    overflow: visible;

    box-shadow:
        0 2px 8px
        rgba(0, 0, 0, 0.04);
}

.loan-table-header {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    padding: 18px 20px;

    border-bottom: 1px solid #e5e7eb;
}

.loan-table-header-title {
    margin: 0;

    color: #111827;

    font-size: 16px;

    font-weight: 700;
}

.loan-table-header-count {
    color: #6b7280;

    font-size: 13px;
}

.loan-table-scroll {
    width: 100%;

    overflow-x: auto;

    overflow-y: visible;

    -webkit-overflow-scrolling: touch;
}

.loan-table-scroll table {
    min-width: 1050px;
}


/* ==========================================================================
   LOAN NUMBER
   ========================================================================== */

.loan-number {
    color: #111827;

    font-weight: 700;

    white-space: nowrap;
}

.loan-number-subtext {
    display: block;

    margin-top: 3px;

    color: #9ca3af;

    font-size: 11px;

    font-weight: 400;
}


/* ==========================================================================
   BORROWER
   ========================================================================== */

.loan-borrower-name {
    color: #111827;

    font-weight: 600;
}

.loan-borrower-code {
    display: block;

    margin-top: 3px;

    color: #9ca3af;

    font-size: 11px;
}


/* ==========================================================================
   MONEY
   ========================================================================== */

.loan-money {
    white-space: nowrap;

    color: #111827;
}

.loan-money-strong {
    font-weight: 700;
}


/* ==========================================================================
   STATUS
   ========================================================================== */

.loan-status {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 27px;

    padding: 4px 10px;

    border-radius: 999px;

    font-size: 11px;

    line-height: 1;

    font-weight: 700;

    text-transform: capitalize;

    white-space: nowrap;
}

.loan-status-pending {
    background: #fef3c7;

    color: #92400e;
}

.loan-status-approved {
    background: #dbeafe;

    color: #1e40af;
}

.loan-status-active {
    background: #dcfce7;

    color: #166534;
}

.loan-status-completed {
    background: #e0e7ff;

    color: #3730a3;
}

.loan-status-overdue {
    background: #fee2e2;

    color: #991b1b;
}

.loan-status-cancelled,
.loan-status-rejected {
    background: #f3f4f6;

    color: #374151;
}


/* ==========================================================================
   PAYMENT METHOD
   ========================================================================== */

.payment-method {
    display: inline-flex;

    align-items: center;

    padding: 5px 9px;

    border-radius: 6px;

    background: #f3f4f6;

    color: #374151;

    font-size: 11px;

    font-weight: 600;

    white-space: nowrap;
}


/* ==========================================================================
   ACTION MENU
   ========================================================================== */

.loan-action-cell {
    width: 60px;

    text-align: center;
}

.loan-action-menu {
    position: relative;

    display: inline-block;
}

.loan-action-button {
    width: 36px;

    height: 36px;

    padding: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    border: 1px solid #e5e7eb;

    border-radius: 8px;

    background: #ffffff;

    color: #374151;

    font-size: 21px;

    line-height: 1;

    cursor: pointer;

    transition:
        background 0.2s ease,
        border-color 0.2s ease;
}

.loan-action-button:hover,
.loan-action-button[aria-expanded="true"] {
    background: #f3f4f6;

    border-color: #d1d5db;
}

.loan-action-dropdown {
    position: absolute;

    top: calc(100% + 6px);

    right: 0;

    width: 190px;

    padding: 6px;

    display: none;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    box-shadow:
        0 12px 30px
        rgba(0, 0, 0, 0.14);

    z-index: 5000;
}

.loan-action-dropdown.active {
    display: block;
}

.loan-action-item {
    width: 100%;

    min-height: 40px;

    padding: 9px 11px;

    display: flex;

    align-items: center;

    gap: 10px;

    box-sizing: border-box;

    border: none;

    border-radius: 7px;

    background: transparent;

    color: #374151;

    font-size: 13px;

    font-weight: 500;

    line-height: 1.2;

    text-align: left;

    text-decoration: none;

    cursor: pointer;

    transition:
        background 0.15s ease,
        color 0.15s ease;
}

.loan-action-item:hover {
    background: #f3f4f6;
}

.loan-action-icon {
    width: 20px;

    flex: 0 0 20px;

    text-align: center;
}

.loan-action-approve {
    color: #166534;
}

.loan-action-approve:hover {
    background: #dcfce7;
}

.loan-action-danger {
    color: #991b1b;
}

.loan-action-danger:hover {
    background: #fee2e2;
}

.loan-action-form {
    margin: 0;

    padding: 0;
}


/* ==========================================================================
   EMPTY STATE
   ========================================================================== */

.loan-empty-state {
    padding: 55px 25px;

    text-align: center;
}

.loan-empty-icon {
    width: 52px;

    height: 52px;

    margin: 0 auto 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 12px;

    background: #f3f4f6;

    color: #6b7280;

    font-size: 24px;
}

.loan-empty-state h3 {
    margin: 0 0 7px;

    color: #111827;

    font-size: 17px;
}

.loan-empty-state p {
    margin: 0 0 20px;

    color: #6b7280;

    font-size: 14px;
}


/* ==========================================================================
   MODALS
   ========================================================================== */

.loan-modal-overlay {
    position: fixed;

    inset: 0;

    width: 100%;

    height: 100%;

    padding: 20px;

    box-sizing: border-box;

    display: none;

    align-items: center;

    justify-content: center;

    background: rgba(0, 0, 0, 0.55);

    z-index: 10000;
}

.loan-modal-overlay.active {
    display: flex;
}

.loan-modal {
    width: 100%;

    max-width: 720px;

    max-height: 92vh;

    overflow-y: auto;

    padding: 25px;

    box-sizing: border-box;

    background: #ffffff;

    border-radius: 13px;

    box-shadow:
        0 25px 70px
        rgba(0, 0, 0, 0.28);

    animation: loanModalIn 0.18s ease;
}

@keyframes loanModalIn {

    from {
        opacity: 0;

        transform:
            translateY(10px)
            scale(0.985);
    }

    to {
        opacity: 1;

        transform:
            translateY(0)
            scale(1);
    }

}

.loan-modal-header {
    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 24px;
}

.loan-modal-header h2 {
    margin: 0 0 5px;

    color: #111827;

    font-size: 20px;
}

.loan-modal-header p {
    margin: 0;

    color: #6b7280;

    font-size: 13px;

    line-height: 1.5;
}

.loan-modal-close {
    width: 34px;

    height: 34px;

    flex: 0 0 34px;

    display: flex;

    align-items: center;

    justify-content: center;

    border: none;

    border-radius: 7px;

    background: transparent;

    color: #6b7280;

    font-size: 25px;

    line-height: 1;

    cursor: pointer;
}

.loan-modal-close:hover {
    background: #f3f4f6;

    color: #111827;
}


/* ==========================================================================
   CREATE LOAN FORM
   ========================================================================== */

.loan-form-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 17px;
}

.loan-form-full {
    grid-column: 1 / -1;
}

.loan-form-group {
    min-width: 0;
}

.loan-form-group label {
    display: block;

    margin-bottom: 7px;

    color: #374151;

    font-size: 13px;

    font-weight: 600;
}

.loan-form-group input,
.loan-form-group select,
.loan-form-group textarea {
    width: 100%;

    box-sizing: border-box;
}

.loan-form-group textarea {
    resize: vertical;

    min-height: 100px;
}

.loan-form-help {
    display: block;

    margin-top: 5px;

    color: #6b7280;

    font-size: 11px;

    line-height: 1.45;
}

.loan-account-hint {
    display: block;

    margin-top: 6px;

    color: #6b7280;

    font-size: 11px;

    line-height: 1.4;
}

.loan-account-hint.warning {
    color: #b45309;
}

.loan-account-hint.success {
    color: #166534;
}


/* ==========================================================================
   MODAL FOOTER
   ========================================================================== */

.loan-modal-footer {
    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 10px;

    flex-wrap: wrap;

    margin-top: 25px;

    padding-top: 18px;

    border-top: 1px solid #e5e7eb;
}


/* ==========================================================================
   DECISION BUTTONS
   ========================================================================== */

.loan-decision-button {
    min-width: 145px;

    min-height: 40px;

    padding: 9px 15px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    border-radius: 8px;

    font-size: 13px;

    font-weight: 700;

    cursor: pointer;

    transition:
        background 0.2s ease,
        box-shadow 0.2s ease,
        transform 0.2s ease;
}

.loan-decision-approve {
    background: #dcfce7;

    color: #166534;

    border: 1px solid #86efac;

    box-shadow:
        0 0 10px
        rgba(34, 197, 94, 0.25);
}

.loan-decision-approve:hover {
    background: #bbf7d0;

    box-shadow:
        0 0 18px
        rgba(34, 197, 94, 0.42);

    transform: translateY(-1px);
}

.loan-decision-reject {
    background: #fee2e2;

    color: #991b1b;

    border: 1px solid #fca5a5;

    box-shadow:
        0 0 10px
        rgba(239, 68, 68, 0.25);
}

.loan-decision-reject:hover {
    background: #fecaca;

    box-shadow:
        0 0 18px
        rgba(239, 68, 68, 0.42);

    transform: translateY(-1px);
}

.loan-decision-button:disabled,
.loan-decision-button.disabled {
    background: #e5e7eb;

    color: #9ca3af;

    border-color: #d1d5db;

    box-shadow: none;

    cursor: not-allowed;

    transform: none;

    opacity: 0.8;
}


/* ==========================================================================
   DETAILS
   ========================================================================== */

.loan-details-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 12px;
}

.loan-detail-item {
    min-width: 0;

    padding: 13px 14px;

    border: 1px solid #e5e7eb;

    border-radius: 8px;

    background: #f9fafb;
}

.loan-detail-full {
    grid-column: 1 / -1;
}

.loan-detail-label {
    display: block;

    margin-bottom: 5px;

    color: #6b7280;

    font-size: 11px;

    line-height: 1.3;
}

.loan-detail-value {
    display: block;

    color: #111827;

    font-size: 13px;

    font-weight: 600;

    line-height: 1.45;

    overflow-wrap: anywhere;
}


/* ==========================================================================
   DUE DATE
   ========================================================================== */

.loan-due-date {
    display: inline-flex;

    align-items: center;

    padding: 4px 8px;

    border-radius: 6px;

    background: #fef3c7;

    color: #92400e;

    font-size: 11px;

    font-weight: 700;
}


/* ==========================================================================
   PAYMENT SCHEDULE
   ========================================================================== */

.loan-payment-schedule {
    margin-top: 5px;
}

.loan-payment-schedule-title {
    margin-bottom: 9px;

    color: #374151;

    font-size: 12px;

    font-weight: 700;
}

.loan-payment-schedule-list {
    margin: 0;

    padding-left: 20px;

    color: #374151;

    font-size: 12px;
}

.loan-payment-schedule-list li {
    margin-bottom: 6px;

    line-height: 1.45;
}

.loan-payment-schedule-empty {
    color: #9ca3af;

    font-size: 12px;
}


/* ==========================================================================
   MOBILE CARD VIEW
   ========================================================================== */

@media (max-width: 1100px) {

    .loan-summary-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

}


@media (max-width: 760px) {

    .loan-summary-grid {
        grid-template-columns: 1fr;

        gap: 12px;
    }

    .loan-summary-card {
        padding: 16px;
    }

    .loan-summary-value {
        font-size: 22px;
    }

    .loan-table-header {
        align-items: flex-start;

        flex-direction: column;

        padding: 15px;
    }

    .loan-modal-overlay {
        padding: 10px;

        align-items: flex-start;
    }

    .loan-modal {
        max-height: calc(100vh - 20px);

        margin-top: 0;

        padding: 18px;

        border-radius: 11px;
    }

    .loan-form-grid,
    .loan-details-grid {
        grid-template-columns: 1fr;
    }

    .loan-form-full,
    .loan-detail-full {
        grid-column: auto;
    }

    .loan-modal-header {
        margin-bottom: 18px;
    }

    .loan-modal-header h2 {
        font-size: 18px;
    }

    .loan-modal-footer {
        flex-direction: column-reverse;

        align-items: stretch;
    }

    .loan-modal-footer > button,
    .loan-modal-footer > form,
    .loan-modal-footer > form > button {
        width: 100%;
    }

    .loan-decision-button {
        width: 100%;

        min-width: 0;
    }

}


@media (max-width: 480px) {

    .loan-modal {
        padding: 15px;
    }

    .loan-form-grid {
        gap: 13px;
    }

    .loan-detail-item {
        padding: 11px 12px;
    }

    .loan-action-dropdown {
        position: fixed;

        top: auto;

        right: 10px;

        width: min(
            190px,
            calc(100vw - 20px)
        );
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


    <!-- =========================================================
         NAVBAR
    ========================================================== -->

    <nav class="navbar">

        <div class="page-title">
            Loans
        </div>


        <div class="user-info">

            <span class="user-name">

                <?= htmlspecialchars(
                    $user['full_name']
                    ?? $user['username']
                    ?? 'User'
                ) ?>

            </span>


            <span class="badge">

                <?= htmlspecialchars(
                    $tenantRole
                    ?? 'User'
                ) ?>

            </span>

        </div>

    </nav>


    <main class="container loans-page">


        <!-- =====================================================
             PAGE HEADER
        ====================================================== -->

        <div class="page-header">

            <div>

                <h1>
                    Loans
                </h1>

                <p>
                    Manage borrower loans, payments and loan status.
                </p>

            </div>


            <div>

                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="openCreateLoanModal()"
                >
                    + Create Loan
                </button>

            </div>

        </div>


        <!-- =====================================================
             ALERTS
        ====================================================== -->

        <?php if (!empty($success)): ?>

            <div class="loan-alert loan-alert-success">

                <span>✓</span>

                <span>
                    <?= htmlspecialchars($success) ?>
                </span>

            </div>

        <?php endif; ?>


        <?php if (!empty($error)): ?>

            <div class="loan-alert loan-alert-error">

                <span>!</span>

                <span>
                    <?= htmlspecialchars($error) ?>
                </span>

            </div>

        <?php endif; ?>


        <!-- =====================================================
             SUMMARY
        ====================================================== -->

        <section class="loan-summary-grid">


            <div class="loan-summary-card">

                <div class="loan-summary-label">

                    <span>
                        Total Loans
                    </span>

                </div>


                <div class="loan-summary-value">

                    <?= number_format($totalLoans) ?>

                </div>


                <div class="loan-summary-subtext">

                    All loan records

                </div>

            </div>


            <div class="loan-summary-card">

                <div class="loan-summary-label">

                    <span>
                        Active Loans
                    </span>

                </div>


                <div class="loan-summary-value">

                    <?= number_format($activeLoans) ?>

                </div>


                <div class="loan-summary-subtext">

                    Currently active

                </div>

            </div>


            <div class="loan-summary-card">

                <div class="loan-summary-label">

                    <span>
                        Total Principal
                    </span>

                </div>


                <div class="loan-summary-value">

                    ₱<?= number_format(
                        $totalPrincipal,
                        2
                    ) ?>

                </div>


                <div class="loan-summary-subtext">

                    Principal amount

                </div>

            </div>


            <div class="loan-summary-card">

                <div class="loan-summary-label">

                    <span>
                        Total Payable
                    </span>

                </div>


                <div class="loan-summary-value">

                    ₱<?= number_format(
                        $totalPayable,
                        2
                    ) ?>

                </div>


                <div class="loan-summary-subtext">

                    Principal + interest

                </div>

            </div>


        </section>


        <!-- =====================================================
             LOANS
        ====================================================== -->

        <?php if (empty($loans)): ?>


            <div class="loan-table-card">

                <div class="loan-empty-state">

                    <div class="loan-empty-icon">
                        ₱
                    </div>


                    <h3>
                        No Loans Found
                    </h3>


                    <p>
                        You haven't created any loans yet.
                    </p>


                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="openCreateLoanModal()"
                    >
                        Create Your First Loan
                    </button>

                </div>

            </div>


        <?php else: ?>


            <section class="loan-table-card">


                <div class="loan-table-header">

                    <div>

                        <h2 class="loan-table-header-title">
                            Loan Records
                        </h2>

                        <span class="loan-table-header-count">
                            <?= number_format($totalLoans) ?>
                            <?= $totalLoans === 1 ? 'loan' : 'loans' ?>
                        </span>

                    </div>

                </div>


                <div class="loan-table-scroll">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Loan Number
                                </th>

                                <th>
                                    Borrower
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
                                    Payment
                                </th>

                                <th>
                                    Term
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach ($loans as $loan): ?>


                            <?php

                            $loanId = (int) (
                                $loan['id']
                                ?? 0
                            );


                            $loanNumber =
                                $loan['loan_number']
                                ?? '-';


                            $borrowerName =
                                $loan['borrower_name']
                                ??
                                $loan['full_name']
                                ??
                                $loan['borrower']
                                ??
                                'Unknown Borrower';


                            $borrowerCode =
                                $loan['borrower_code']
                                ?? '';


                            $principal =
                                (float) (
                                    $loan['principal_amount']
                                    ?? 0
                                );


                            $interest =
                                (float) (
                                    $loan['total_interest']
                                    ??
                                    $loan['interest_amount']
                                    ??
                                    0
                                );


                            $payable =
                                (float) (
                                    $loan['total_payable']
                                    ?? 0
                                );


                            $interestRate =
                                (float) (
                                    $loan['interest_rate']
                                    ?? 0
                                );


                            $interestType =
                                $loan['interest_type']
                                ?? 'flat';


                            $term =
                                (int) (
                                    $loan['term']
                                    ?? 1
                                );


                            $termPeriod =
                                $loan['term_period']
                                ?? 'months';


                            $paymentType =
                                $loan['payment_type']
                                ?? 'installment';


                            $processingFee =
                                (float) (
                                    $loan['processing_fee']
                                    ?? 0
                                );


                            $releaseDate =
                                $loan['release_date']
                                ?? '';


                            $firstPaymentDate =
                                $loan['first_payment_date']
                                ?? '';


                            $status =
                                strtolower(
                                    trim(
                                        (string) (
                                            $loan['status']
                                            ?? 'pending'
                                        )
                                    )
                                );


                            $purpose =
                                $loan['purpose']
                                ?? '';


                            $notes =
                                $loan['notes']
                                ?? '';


                            $categoryName =
                                $loan['category_name']
                                ?? '';


                            $jsonFlags =
                                JSON_HEX_TAG |
                                JSON_HEX_APOS |
                                JSON_HEX_AMP |
                                JSON_HEX_QUOT;

                            ?>


                            <tr>


                                <!-- LOAN NUMBER -->

                                <td>

                                    <span class="loan-number">

                                        <?= htmlspecialchars(
                                            $loanNumber
                                        ) ?>

                                    </span>


                                    <?php if ($releaseDate): ?>

                                        <span class="loan-number-subtext">

                                            Released:
                                            <?= htmlspecialchars(
                                                $releaseDate
                                            ) ?>

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- BORROWER -->

                                <td>

                                    <span class="loan-borrower-name">

                                        <?= htmlspecialchars(
                                            $borrowerName
                                        ) ?>

                                    </span>


                                    <?php if ($borrowerCode): ?>

                                        <span class="loan-borrower-code">

                                            <?= htmlspecialchars(
                                                $borrowerCode
                                            ) ?>

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- PRINCIPAL -->

                                <td>

                                    <span class="loan-money loan-money-strong">

                                        ₱<?= number_format(
                                            $principal,
                                            2
                                        ) ?>

                                    </span>

                                </td>


                                <!-- INTEREST -->

                                <td>

                                    <span class="loan-money">

                                        ₱<?= number_format(
                                            $interest,
                                            2
                                        ) ?>

                                    </span>

                                </td>


                                <!-- PAYABLE -->

                                <td>

                                    <span class="loan-money loan-money-strong">

                                        ₱<?= number_format(
                                            $payable,
                                            2
                                        ) ?>

                                    </span>

                                </td>


                                <!-- PAYMENT -->

                                <td>

                                    <span class="payment-method">

                                        <?= htmlspecialchars(
                                            loanFormatPaymentMethod(
                                                $paymentType
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <!-- TERM -->

                                <td>

                                    <?= number_format(
                                        $term
                                    ) ?>

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $termPeriod
                                        )
                                    ) ?>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="loan-status loan-status-<?= htmlspecialchars(
                                            $status
                                        ) ?>"
                                    >

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $status
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <!-- ACTIONS -->

                                <td class="loan-action-cell">


                                    <div class="loan-action-menu">


                                        <button
                                            type="button"
                                            class="loan-action-button"
                                            onclick="toggleLoanActions(<?= $loanId ?>)"
                                            aria-label="Loan actions"
                                            aria-expanded="false"
                                            data-loan-id="<?= $loanId ?>"
                                        >

                                            ⋮

                                        </button>


                                        <div
                                            class="loan-action-dropdown"
                                            id="loan-actions-<?= $loanId ?>"
                                        >


                                            <!-- VIEW DETAILS -->

                                            <button
                                                type="button"
                                                class="loan-action-item"
                                                onclick="closeLoanActions(); openLoanDetails(
                                                    <?= $loanId ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $loanNumber,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $borrowerName,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $categoryName,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $principal,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $interestRate,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $interestType,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $term,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $termPeriod,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $paymentType,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $processingFee,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $interest,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $payable,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $releaseDate,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $firstPaymentDate,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $status,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $purpose,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $notes,
                                                            $jsonFlags
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                );"
                                            >

                                                <span class="loan-action-icon">
                                                    👁
                                                </span>

                                                <span>
                                                    View Details
                                                </span>

                                            </button>


                                            <!-- EDIT -->

                                            <a
                                                href="index.php?url=loans/edit&id=<?= $loanId ?>"
                                                class="loan-action-item"
                                                onclick="closeLoanActions();"
                                            >

                                                <span class="loan-action-icon">
                                                    ✏️
                                                </span>

                                                <span>
                                                    Edit
                                                </span>

                                            </a>


                                            <!-- PAYMENT -->

                                            <a
                                                href="index.php?url=loans/payment&id=<?= $loanId ?>"
                                                class="loan-action-item"
                                                onclick="closeLoanActions();"
                                            >

                                                <span class="loan-action-icon">
                                                    💵
                                                </span>

                                                <span>
                                                    Payment
                                                </span>

                                            </a>


                                            <!-- CANCEL -->

                                            <?php if (
                                                $status === 'pending'
                                                ||
                                                $status === 'approved'
                                            ): ?>

                                                <form
                                                    method="POST"
                                                    action="index.php?url=loans/delete"
                                                    class="loan-action-form"
                                                    onsubmit="return confirm('Are you sure you want to cancel this loan?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= $loanId ?>"
                                                    >


                                                    <button
                                                        type="submit"
                                                        class="loan-action-item loan-action-danger"
                                                    >

                                                        <span class="loan-action-icon">
                                                            ✕
                                                        </span>

                                                        <span>
                                                            Cancel
                                                        </span>

                                                    </button>

                                                </form>

                                            <?php endif; ?>


                                        </div>

                                    </div>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>

                    </table>

                </div>

            </section>


        <?php endif; ?>


    </main>

</div>


<!-- ==========================================================================
     CREATE LOAN MODAL
========================================================================== -->

<div
    class="loan-modal-overlay"
    id="createLoanModal"
    onclick="closeCreateLoanModal(event)"
>

    <div
        class="loan-modal"
        onclick="event.stopPropagation();"
    >


        <div class="loan-modal-header">

            <div>
            <div>
                <h2>
                    Create New Loan
                </h2>

                <p>
                    Create a new loan account for a borrower.
                </p>
            </div>

            <button
                type="button"
                class="loan-modal-close"
                onclick="closeCreateLoanModal()"
                aria-label="Close"
            >
                ×
            </button>
        </div>

        <form
            method="POST"
            action="index.php?url=loans/store"
            id="createLoanForm"
        >

            <div class="loan-form-grid">

                <!-- =====================================================
                     BORROWER
                ====================================================== -->

                <div class="loan-form-group loan-form-full">

                    <label for="borrower_id">
                        Borrower
                    </label>

                    <select
                        name="borrower_id"
                        id="borrower_id"
                        required
                    >
                        <option value="">
                            Select Borrower
                        </option>

                        <?php foreach ($borrowers as $borrower): ?>

                            <?php
                            $borrowerId =
                                (int) (
                                    $borrower['id']
                                    ?? 0
                                );

                            $borrowerFullName =
                                trim(
                                    (string) (
                                        $borrower['full_name']
                                        ??
                                        $borrower['name']
                                        ??
                                        ''
                                    )
                                );

                            $borrowerCode =
                                trim(
                                    (string) (
                                        $borrower['borrower_code']
                                        ??
                                        ''
                                    )
                                );
                            ?>

                            <option
                                value="<?= $borrowerId ?>"
                            >
                                <?= htmlspecialchars(
                                    $borrowerFullName
                                    ?: 'Unknown Borrower'
                                ) ?>

                                <?php if ($borrowerCode): ?>

                                    -
                                    <?= htmlspecialchars(
                                        $borrowerCode
                                    ) ?>

                                <?php endif; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <span class="loan-form-help">
                        Select the borrower who will receive this loan.
                    </span>

                </div>


                <!-- =====================================================
                     LOAN CATEGORY
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="category_id">
                        Loan Category
                    </label>

                    <select
                        name="category_id"
                        id="category_id"
                    >
                        <option value="">
                            Select Category
                        </option>

                        <?php foreach ($categories as $category): ?>

                            <?php
                            $categoryId =
                                (int) (
                                    $category['id']
                                    ?? 0
                                );

                            $categoryName =
                                $category['name']
                                ??
                                $category['category_name']
                                ??
                                '';
                            ?>

                            <option
                                value="<?= $categoryId ?>"
                            >
                                <?= htmlspecialchars(
                                    $categoryName
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- =====================================================
                     ACCOUNT
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="account_id">
                        Release Account
                    </label>

                    <select
                        name="account_id"
                        id="account_id"
                    >

                        <option value="">
                            Select Account
                        </option>

                        <?php foreach ($accounts as $account): ?>

                            <?php
                            $accountId =
                                (int) (
                                    $account['id']
                                    ?? 0
                                );

                            $accountName =
                                $account['name']
                                ??
                                $account['account_name']
                                ??
                                'Account';

                            $accountBalance =
                                (float) (
                                    $account['balance']
                                    ??
                                    $account['current_balance']
                                    ??
                                    0
                                );
                            ?>

                            <option
                                value="<?= $accountId ?>"
                            >
                                <?= htmlspecialchars(
                                    $accountName
                                ) ?>

                                -
                                ₱<?= number_format(
                                    $accountBalance,
                                    2
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <span
                        class="loan-account-hint"
                        id="loanAccountHint"
                    >
                        Select the account where the loan will be released.
                    </span>

                </div>


                <!-- =====================================================
                     PRINCIPAL
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="principal_amount">
                        Principal Amount
                    </label>

                    <input
                        type="number"
                        name="principal_amount"
                        id="principal_amount"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        required
                    >

                </div>


                <!-- =====================================================
                     INTEREST RATE
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="interest_rate">
                        Interest Rate (%)
                    </label>

                    <input
                        type="number"
                        name="interest_rate"
                        id="interest_rate"
                        min="0"
                        step="0.01"
                        value="0"
                        placeholder="0.00"
                        required
                    >

                </div>


                <!-- =====================================================
                     INTEREST TYPE
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="interest_type">
                        Interest Type
                    </label>

                    <select
                        name="interest_type"
                        id="interest_type"
                    >

                        <option value="flat">
                            Flat
                        </option>

                        <option value="reducing_balance">
                            Reducing Balance
                        </option>

                    </select>

                </div>


                <!-- =====================================================
                     TERM
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="term">
                        Loan Term
                    </label>

                    <input
                        type="number"
                        name="term"
                        id="term"
                        min="1"
                        step="1"
                        value="1"
                        required
                    >

                </div>


                <!-- =====================================================
                     TERM PERIOD
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="term_period">
                        Term Period
                    </label>

                    <select
                        name="term_period"
                        id="term_period"
                    >

                        <option value="days">
                            Days
                        </option>

                        <option value="weeks">
                            Weeks
                        </option>

                        <option
                            value="months"
                            selected
                        >
                            Months
                        </option>

                        <option value="years">
                            Years
                        </option>

                    </select>

                </div>


                <!-- =====================================================
                     PAYMENT TYPE
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="payment_type">
                        Payment Type
                    </label>

                    <select
                        name="payment_type"
                        id="payment_type"
                    >

                        <option
                            value="installment"
                            selected
                        >
                            Installment
                        </option>

                        <option value="full_payment">
                            Full Payment
                        </option>

                    </select>

                </div>


                <!-- =====================================================
                     PROCESSING FEE
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="processing_fee">
                        Processing Fee
                    </label>

                    <input
                        type="number"
                        name="processing_fee"
                        id="processing_fee"
                        min="0"
                        step="0.01"
                        value="0"
                        placeholder="0.00"
                    >

                </div>


                <!-- =====================================================
                     RELEASE DATE
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="release_date">
                        Release Date
                    </label>

                    <input
                        type="date"
                        name="release_date"
                        id="release_date"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>


                <!-- =====================================================
                     FIRST PAYMENT DATE
                ====================================================== -->

                <div class="loan-form-group">

                    <label for="first_payment_date">
                        First Payment Date
                    </label>

                    <input
                        type="date"
                        name="first_payment_date"
                        id="first_payment_date"
                        value="<?= date(
                            'Y-m-d',
                            strtotime('+1 month')
                        ) ?>"
                    >

                </div>


                <!-- =====================================================
                     PURPOSE
                ====================================================== -->

                <div class="loan-form-group loan-form-full">

                    <label for="purpose">
                        Purpose
                    </label>

                    <input
                        type="text"
                        name="purpose"
                        id="purpose"
                        placeholder="Enter loan purpose"
                    >

                </div>


                <!-- =====================================================
                     NOTES
                ====================================================== -->

                <div class="loan-form-group loan-form-full">

                    <label for="notes">
                        Notes
                    </label>

                    <textarea
                        name="notes"
                        id="notes"
                        placeholder="Additional notes..."
                    ></textarea>

                </div>

            </div>


            <!-- =====================================================
                 LOAN CALCULATION PREVIEW
            ====================================================== -->

            <div
                class="loan-detail-grid"
                id="loanCalculationPreview"
                style="
                    margin-top: 20px;
                    display: grid;
                    grid-template-columns:
                        repeat(3, minmax(0, 1fr));
                    gap: 10px;
                "
            >

                <div class="loan-detail-item">

                    <span class="loan-detail-label">
                        Interest
                    </span>

                    <span
                        class="loan-detail-value"
                        id="previewInterest"
                    >
                        ₱0.00
                    </span>

                </div>

                <div class="loan-detail-item">

                    <span class="loan-detail-label">
                        Total Payable
                    </span>

                    <span
                        class="loan-detail-value"
                        id="previewPayable"
                    >
                        ₱0.00
                    </span>

                </div>

                <div class="loan-detail-item">

                    <span class="loan-detail-label">
                        Estimated Installment
                    </span>

                    <span
                        class="loan-detail-value"
                        id="previewInstallment"
                    >
                        ₱0.00
                    </span>

                </div>

            </div>


            <!-- =====================================================
                 MODAL FOOTER
            ====================================================== -->

            <div class="loan-modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="closeCreateLoanModal()"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Create Loan
                </button>

            </div>

        </form>

    </div>
</div>


<!-- =====================================================================
     LOAN DETAILS MODAL
====================================================================== -->

<div
    class="loan-modal-overlay"
    id="loanDetailsModal"
    onclick="closeLoanDetails(event)"
>

    <div
        class="loan-modal"
        onclick="event.stopPropagation();"
    >

        <div class="loan-modal-header">

            <div>

                <h2>
                    Loan Details
                </h2>

                <p>
                    Complete information about this loan.
                </p>

            </div>

            <button
                type="button"
                class="loan-modal-close"
                onclick="closeLoanDetails()"
                aria-label="Close"
            >
                ×
            </button>

        </div>


        <div class="loan-details-grid">

            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Loan Number
                </span>

                <span
                    class="loan-detail-value"
                    id="detailLoanNumber"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Borrower
                </span>

                <span
                    class="loan-detail-value"
                    id="detailBorrower"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Category
                </span>

                <span
                    class="loan-detail-value"
                    id="detailCategory"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Status
                </span>

                <span
                    class="loan-detail-value"
                    id="detailStatus"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Principal
                </span>

                <span
                    class="loan-detail-value"
                    id="detailPrincipal"
                >
                    ₱0.00
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Interest Rate
                </span>

                <span
                    class="loan-detail-value"
                    id="detailInterestRate"
                >
                    0%
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Interest Type
                </span>

                <span
                    class="loan-detail-value"
                    id="detailInterestType"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Term
                </span>

                <span
                    class="loan-detail-value"
                    id="detailTerm"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Payment Type
                </span>

                <span
                    class="loan-detail-value"
                    id="detailPaymentType"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Processing Fee
                </span>

                <span
                    class="loan-detail-value"
                    id="detailProcessingFee"
                >
                    ₱0.00
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Total Interest
                </span>

                <span
                    class="loan-detail-value"
                    id="detailInterest"
                >
                    ₱0.00
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Total Payable
                </span>

                <span
                    class="loan-detail-value"
                    id="detailPayable"
                >
                    ₱0.00
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    Release Date
                </span>

                <span
                    class="loan-detail-value"
                    id="detailReleaseDate"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">
                    First Payment Date
                </span>

                <span
                    class="loan-detail-value"
                    id="detailFirstPaymentDate"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item loan-detail-full">

                <span class="loan-detail-label">
                    Purpose
                </span>

                <span
                    class="loan-detail-value"
                    id="detailPurpose"
                >
                    -
                </span>

            </div>


            <div class="loan-detail-item loan-detail-full">

                <span class="loan-detail-label">
                    Notes
                </span>

                <span
                    class="loan-detail-value"
                    id="detailNotes"
                >
                    -
                </span>

            </div>

        </div>


        <div class="loan-modal-footer">

            <button
                type="button"
                class="btn btn-secondary"
                onclick="closeLoanDetails()"
            >
                Close
            </button>

        </div>

    </div>

</div>


<!-- =====================================================================
     SEARCH + FILTER STYLES
====================================================================== -->

<style>

.loan-filter-wrapper {
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #ffffff;
}

.loan-filter-grid {
    display: grid;
    grid-template-columns:
        minmax(250px, 2fr)
        minmax(150px, 1fr)
        minmax(150px, 1fr)
        auto;
    gap: 10px;
    align-items: center;
}

.loan-search-box {
    position: relative;
    width: 100%;
}

.loan-search-box input {
    width: 100%;
    height: 40px;
    box-sizing: border-box;
    padding: 0 40px 0 38px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #ffffff;
    color: #111827;
    font-size: 13px;
    outline: none;
    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.loan-search-box input:focus {
    border-color: #2563eb;
    box-shadow:
        0 0 0 3px
        rgba(37, 99, 235, 0.10);
}

.loan-search-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 16px;
    pointer-events: none;
}

.loan-search-clear {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 27px;
    height: 27px;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: #6b7280;
    cursor: pointer;
    font-size: 16px;
}

.loan-search-clear:hover {
    background: #f3f4f6;
    color: #111827;
}

.loan-filter-select {
    width: 100%;
    height: 40px;
    padding: 0 32px 0 11px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #ffffff;
    color: #374151;
    font-size: 13px;
    outline: none;
    cursor: pointer;
}

.loan-filter-select:focus {
    border-color: #2563eb;
    box-shadow:
        0 0 0 3px
        rgba(37, 99, 235, 0.10);
}

.loan-filter-reset {
    height: 40px;
    padding: 0 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #ffffff;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
}

.loan-filter-reset:hover {
    background: #f3f4f6;
}

.loan-filter-result {
    margin-top: 10px;
    color: #6b7280;
    font-size: 12px;
}

.loan-no-search-results {
    display: none;
    padding: 45px 20px;
    text-align: center;
}

.loan-no-search-results-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #f3f4f6;
    color: #6b7280;
    font-size: 22px;
}

.loan-no-search-results h3 {
    margin: 0 0 6px;
    color: #111827;
    font-size: 16px;
}

.loan-no-search-results p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
}

@media (max-width: 900px) {

    .loan-filter-grid {
        grid-template-columns:
            1fr 1fr;
    }

    .loan-search-box {
        grid-column: 1 / -1;
    }

}

@media (max-width: 600px) {

    .loan-filter-wrapper {
        padding: 13px 15px;
    }

    .loan-filter-grid {
        grid-template-columns: 1fr;
    }

    .loan-search-box {
        grid-column: auto;
    }

    .loan-filter-reset {
        width: 100%;
    }

}

</style>


<!-- =====================================================================
     ADD SEARCH + FILTER UI INTO LOAN TABLE
====================================================================== -->

<script>

(function () {

    'use strict';

    /*
    |--------------------------------------------------------------------------
    | INSERT SEARCH / FILTER BAR
    |--------------------------------------------------------------------------
    */

    const tableCard =
        document.querySelector(
            '.loan-table-card'
        );

    const tableHeader =
        document.querySelector(
            '.loan-table-header'
        );

    const loanTable =
        document.querySelector(
            '.loan-table-card table'
        );

    if (
        !tableCard ||
        !tableHeader ||
        !loanTable
    ) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE FILTER BAR
    |--------------------------------------------------------------------------
    */

    const filterWrapper =
        document.createElement('div');

    filterWrapper.className =
        'loan-filter-wrapper';

    filterWrapper.innerHTML = `

        <div class="loan-filter-grid">

            <div class="loan-search-box">

                <span
                    class="loan-search-icon"
                >
                    🔍
                </span>

                <input
                    type="search"
                    id="loanSearchInput"
                    placeholder="Search loan number or borrower..."
                    autocomplete="off"
                >

                <button
                    type="button"
                    class="loan-search-clear"
                    id="loanSearchClear"
                    aria-label="Clear search"
                >
                    ×
                </button>

            </div>


            <select
                id="loanStatusFilter"
                class="loan-filter-select"
            >

                <option value="">
                    All Statuses
                </option>

                <option value="pending">
                    Pending
                </option>

                <option value="active">
                    Active
                </option>

                <option value="completed">
                    Completed
                </option>

                <option value="overdue">
                    Overdue
                </option>

                <option value="cancelled">
                    Cancelled
                </option>

                <option value="rejected">
                    Rejected
                </option>

            </select>


            <select
                id="loanPaymentFilter"
                class="loan-filter-select"
            >

                <option value="">
                    All Payment Types
                </option>

                <option value="installment">
                    Installment
                </option>

                <option value="full_payment">
                    Full Payment
                </option>

            </select>


            <button
                type="button"
                class="loan-filter-reset"
                id="loanFilterReset"
            >
                Reset
            </button>

        </div>


        <div
            class="loan-filter-result"
            id="loanFilterResult"
        ></div>

    `;

    tableHeader.insertAdjacentElement(
        'afterend',
        filterWrapper
    );


    /*
    |--------------------------------------------------------------------------
    | CREATE NO RESULTS MESSAGE
    |--------------------------------------------------------------------------
    */

    const noResults =
        document.createElement('div');

    noResults.className =
        'loan-no-search-results';

    noResults.id =
        'loanNoSearchResults';

    noResults.innerHTML = `

        <div class="loan-no-search-results-icon">
            🔍
        </div>

        <h3>
            No Matching Loans
        </h3>

        <p>
            Try changing your search or filter.
        </p>

    `;

    tableCard.appendChild(
        noResults
    );


    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const searchInput =
        document.getElementById(
            'loanSearchInput'
        );

    const statusFilter =
        document.getElementById(
            'loanStatusFilter'
        );

    const paymentFilter =
        document.getElementById(
            'loanPaymentFilter'
        );

    const resetButton =
        document.getElementById(
            'loanFilterReset'
        );

    const clearButton =
        document.getElementById(
            'loanSearchClear'
        );

    const resultText =
        document.getElementById(
            'loanFilterResult'
        );


    /*
    |--------------------------------------------------------------------------
    | TABLE ROWS
    |--------------------------------------------------------------------------
    */

    const rows = Array.from(
        loanTable.querySelectorAll(
            'tbody tr'
        )
    );


    /*
    |--------------------------------------------------------------------------
    | FILTER FUNCTION
    |--------------------------------------------------------------------------
    */

    function filterLoans() {

        const search =
            searchInput.value
                .trim()
                .toLowerCase();

        const selectedStatus =
            statusFilter.value
                .trim()
                .toLowerCase();

        const selectedPayment =
            paymentFilter.value
                .trim()
                .toLowerCase();

        let visibleCount = 0;


        rows.forEach(function (row) {

            /*
            |--------------------------------------------------------------------------
            | SEARCHABLE TEXT
            |--------------------------------------------------------------------------
            */

            const rowText =
                row.innerText
                    .toLowerCase();


            /*
            |--------------------------------------------------------------------------
            | LOAN STATUS
            |--------------------------------------------------------------------------
            */

            const statusElement =
                row.querySelector(
                    '.loan-status'
                );

            const rowStatus =
                statusElement
                    ? statusElement
                        .textContent
                        .trim()
                        .toLowerCase()
                    : '';


            /*
            |--------------------------------------------------------------------------
            | PAYMENT TYPE
            |--------------------------------------------------------------------------
            */

            const paymentElement =
                row.querySelector(
                    '.payment-method'
                );

            let rowPayment =
                paymentElement
                    ? paymentElement
                        .textContent
                        .trim()
                        .toLowerCase()
                    : '';


            if (
                rowPayment ===
                'full payment'
            ) {
                rowPayment =
                    'full_payment';
            }

            if (
                rowPayment ===
                'installment'
            ) {
                rowPayment =
                    'installment';
            }


            /*
            |--------------------------------------------------------------------------
            | SEARCH MATCH
            |--------------------------------------------------------------------------
            */

            const matchesSearch =
                search === ''
                ||
                rowText.includes(
                    search
                );


            /*
            |--------------------------------------------------------------------------
            | STATUS MATCH
            |--------------------------------------------------------------------------
            */

            const matchesStatus =
                selectedStatus === ''
                ||
                rowStatus ===
                    selectedStatus;


            /*
            |--------------------------------------------------------------------------
            | PAYMENT MATCH
            |--------------------------------------------------------------------------
            */

            const matchesPayment =
                selectedPayment === ''
                ||
                rowPayment ===
                    selectedPayment;


            /*
            |--------------------------------------------------------------------------
            | FINAL MATCH
            |--------------------------------------------------------------------------
            */

            const visible =
                matchesSearch &&
                matchesStatus &&
                matchesPayment;


            if (visible) {

                row.style.display = '';

                visibleCount++;

            } else {

                row.style.display =
                    'none';

            }

        });


        /*
        |--------------------------------------------------------------------------
        | CLEAR BUTTON
        |--------------------------------------------------------------------------
        */

        if (search !== '') {

            clearButton.style.display =
                'flex';

        } else {

            clearButton.style.display =
                'none';

        }


        /*
        |--------------------------------------------------------------------------
        | RESULT TEXT
        |--------------------------------------------------------------------------
        */

        const totalCount =
            rows.length;

        if (
            search !== ''
            ||
            selectedStatus !== ''
            ||
            selectedPayment !== ''
        ) {

            resultText.textContent =
                'Showing '
                +
                visibleCount
                +
                ' of '
                +
                totalCount
                +
                ' loans';

        } else {

            resultText.textContent =
                totalCount
                +
                (
                    totalCount === 1
                        ? ' loan'
                        : ' loans'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | NO RESULTS
        |--------------------------------------------------------------------------
        */

        if (
            visibleCount === 0 &&
            rows.length > 0
        ) {

            noResults.style.display =
                'block';

        } else {

            noResults.style.display =
                'none';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH EVENT
    |--------------------------------------------------------------------------
    */

    searchInput.addEventListener(
        'input',
        filterLoans
    );


    /*
    |--------------------------------------------------------------------------
    | STATUS EVENT
    |--------------------------------------------------------------------------
    */

    statusFilter.addEventListener(
        'change',
        filterLoans
    );


    /*
    |--------------------------------------------------------------------------
    | PAYMENT EVENT
    |--------------------------------------------------------------------------
    */

    paymentFilter.addEventListener(
        'change',
        filterLoans
    );


    /*
    |--------------------------------------------------------------------------
    | CLEAR SEARCH
    |--------------------------------------------------------------------------
    */

    clearButton.addEventListener(
        'click',
        function () {

            searchInput.value = '';

            searchInput.focus();

            filterLoans();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | RESET ALL FILTERS
    |--------------------------------------------------------------------------
    */

    resetButton.addEventListener(
        'click',
        function () {

            searchInput.value = '';

            statusFilter.value = '';

            paymentFilter.value = '';

            filterLoans();

            searchInput.focus();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE
    |--------------------------------------------------------------------------
    */

    filterLoans();

})();

</script>


<!-- =====================================================================
     LOAN ACTION MENU JAVASCRIPT
====================================================================== -->

<script>

function closeLoanActions()
{
    document
        .querySelectorAll(
            '.loan-action-dropdown.active'
        )
        .forEach(
            function(dropdown)
            {
                dropdown.classList.remove(
                    'active'
                );
            }
        );

    document
        .querySelectorAll(
            '.loan-action-button[aria-expanded="true"]'
        )
        .forEach(
            function(button)
            {
                button.setAttribute(
                    'aria-expanded',
                    'false'
                );
            }
        );
}


function toggleLoanActions(loanId)
{
    const dropdown =
        document.getElementById(
            'loan-actions-' + loanId
        );

    if (!dropdown) {
        return;
    }

    const button =
        document.querySelector(
            '.loan-action-button[data-loan-id="' +
            loanId +
            '"]'
        );

    const isOpen =
        dropdown.classList.contains(
            'active'
        );

    closeLoanActions();

    if (!isOpen) {

        dropdown.classList.add(
            'active'
        );

        if (button) {

            button.setAttribute(
                'aria-expanded',
                'true'
            );

        }

    }
}


document.addEventListener(
    'click',
    function(event)
    {

        if (
            !event.target.closest(
                '.loan-action-menu'
            )
        ) {

            closeLoanActions();

        }

    }
);


document.addEventListener(
    'keydown',
    function(event)
    {

        if (
            event.key ===
            'Escape'
        ) {

            closeLoanActions();

        }

    }
);

</script>


<!-- =====================================================================
     CREATE LOAN MODAL JAVASCRIPT
====================================================================== -->

<script>

function openCreateLoanModal()
{
    const modal =
        document.getElementById(
            'createLoanModal'
        );

    if (!modal) {
        return;
    }

    modal.classList.add(
        'active'
    );

    document.body.style.overflow =
        'hidden';

    setTimeout(
        function()
        {

            const borrower =
                document.getElementById(
                    'borrower_id'
                );

            if (borrower) {
                borrower.focus();
            }

        },
        100
    );
}


function closeCreateLoanModal(event)
{

    if (
        event &&
        event.target &&
        event.target.id !==
            'createLoanModal'
    ) {
        return;
    }

    const modal =
        document.getElementById(
            'createLoanModal'
        );

    if (!modal) {
        return;
    }

    modal.classList.remove(
        'active'
    );

    document.body.style.overflow =
        '';

}


document.addEventListener(
    'keydown',
    function(event)
    {

        if (
            event.key ===
            'Escape'
        ) {

            const modal =
                document.getElementById(
                    'createLoanModal'
                );

            if (
                modal &&
                modal.classList.contains(
                    'active'
                )
            ) {

                closeCreateLoanModal();

            }

            const detailsModal =
                document.getElementById(
                    'loanDetailsModal'
                );

            if (
                detailsModal &&
                detailsModal.classList.contains(
                    'active'
                )
            ) {

                closeLoanDetails();

            }

        }

    }
);

</script>


<!-- =====================================================================
     LOAN DETAILS JAVASCRIPT
====================================================================== -->

<script>

function openLoanDetails(
    loanId,
    loanNumber,
    borrowerName,
    categoryName,
    principal,
    interestRate,
    interestType,
    term,
    termPeriod,
    paymentType,
    processingFee,
    interest,
    payable,
    releaseDate,
    firstPaymentDate,
    status,
    purpose,
    notes
)
{

    const modal =
        document.getElementById(
            'loanDetailsModal'
        );

    if (!modal) {
        return;
    }


    document.getElementById(
        'detailLoanNumber'
    ).textContent =
        loanNumber || '-';


    document.getElementById(
        'detailBorrower'
    ).textContent =
        borrowerName || '-';


    document.getElementById(
        'detailCategory'
    ).textContent =
        categoryName || '-';


    document.getElementById(
        'detailStatus'
    ).textContent =
        status
            ? (
                status.charAt(0)
                    .toUpperCase()
                +
                status.slice(1)
            )
            : '-';


    document.getElementById(
        'detailPrincipal'
    ).textContent =
        '₱' +
        Number(
            principal || 0
        ).toLocaleString(
            'en-PH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );


    document.getElementById(
        'detailInterestRate'
    ).textContent =
        Number(
            interestRate || 0
        ).toLocaleString(
            'en-PH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        )
        +
        '%';


    document.getElementById(
        'detailInterestType'
    ).textContent =
        interestType
            ? interestType
                .replaceAll(
                    '_',
                    ' '
                )
                .replace(
                    /\b\w/g,
                    function(letter)
                    {
                        return letter
                            .toUpperCase();
                    }
                )
            : '-';


    document.getElementById(
        'detailTerm'
    ).textContent =
        Number(
            term || 0
        )
        +
        ' '
        +
        (
            termPeriod
                ? termPeriod
                    .replaceAll(
                        '_',
                        ' '
                    )
                : ''
        );


    document.getElementById(
        'detailPaymentType'
    ).textContent =
        paymentType ===
        'full_payment'
            ? 'Full Payment'
            : 'Installment';


    document.getElementById(
        'detailProcessingFee'
    ).textContent =
        '₱' +
        Number(
            processingFee || 0
        ).toLocaleString(
            'en-PH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );


    document.getElementById(
        'detailInterest'
    ).textContent =
        '₱' +
        Number(
            interest || 0
        ).toLocaleString(
            'en-PH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );


    document.getElementById(
        'detailPayable'
    ).textContent =
        '₱' +
        Number(
            payable || 0
        ).toLocaleString(
            'en-PH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );


    document.getElementById(
        'detailReleaseDate'
    ).textContent =
        releaseDate || '-';


    document.getElementById(
        'detailFirstPaymentDate'
    ).textContent =
        firstPaymentDate || '-';


    document.getElementById(
        'detailPurpose'
    ).textContent =
        purpose || '-';


    document.getElementById(
        'detailNotes'
    ).textContent =
        notes || '-';


    modal.classList.add(
        'active'
    );

    document.body.style.overflow =
        'hidden';

}


function closeLoanDetails(event)
{

    if (
        event &&
        event.target &&
        event.target.id !==
            'loanDetailsModal'
    ) {
        return;
    }

    const modal =
        document.getElementById(
            'loanDetailsModal'
        );

    if (!modal) {
        return;
    }

    modal.classList.remove(
        'active'
    );

    document.body.style.overflow =
        '';

}

</script>


<!-- =====================================================================
     LOAN CALCULATION PREVIEW
====================================================================== -->

<script>

(function ()
{

    const principalInput =
        document.getElementById(
            'principal_amount'
        );

    const interestRateInput =
        document.getElementById(
            'interest_rate'
        );

    const termInput =
        document.getElementById(
            'term'
        );

    const interestTypeInput =
        document.getElementById(
            'interest_type'
        );

    const paymentTypeInput =
        document.getElementById(
            'payment_type'
        );


    const previewInterest =
        document.getElementById(
            'previewInterest'
        );

    const previewPayable =
        document.getElementById(
            'previewPayable'
        );

    const previewInstallment =
        document.getElementById(
            'previewInstallment'
        );


    if (
        !principalInput ||
        !interestRateInput ||
        !termInput
    ) {
        return;
    }


    function money(value)
    {

        return '₱' +
            Number(
                value || 0
            ).toLocaleString(
                'en-PH',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );

    }


    function calculate()
    {

        const principal =
            parseFloat(
                principalInput.value
            ) || 0;


        const rate =
            parseFloat(
                interestRateInput.value
            ) || 0;


        const term =
            parseInt(
                termInput.value
            ) || 1;


        const interestType =
            interestTypeInput
                ? interestTypeInput.value
                : 'flat';


        const paymentType =
            paymentTypeInput
                ? paymentTypeInput.value
                : 'installment';


        let interest = 0;


        /*
        |--------------------------------------------------------------------------
        | FLAT INTEREST
        |--------------------------------------------------------------------------
        */

        if (
            interestType ===
            'flat'
        ) {

            interest =
                principal *
                (rate / 100) *
                term;

        }


        /*
        |--------------------------------------------------------------------------
        | REDUCING BALANCE
        |--------------------------------------------------------------------------
        */

        else {

            const monthlyRate =
                rate / 100;

            if (
                monthlyRate > 0 &&
                term > 0
            ) {

                const payment =
                    principal *
                    (
                        monthlyRate *
                        Math.pow(
                            1 +
                            monthlyRate,
                            term
                        )
                    )
                    /
                    (
                        Math.pow(
                            1 +
                            monthlyRate,
                            term
                        ) -
                        1
                    );

                interest =
                    (
                        payment *
                        term
                    ) -
                    principal;

            } else {

                interest = 0;

            }

        }


        const payable =
            principal +
            interest;


        let installment =
            payable;


        if (
            paymentType ===
            'installment' &&
            term > 0
        ) {

            installment =
                payable /
                term;

        }


        previewInterest.textContent =
            money(
                interest
            );


        previewPayable.textContent =
            money(
                payable
            );


        previewInstallment.textContent =
            money(
                installment
            );

    }


    principalInput.addEventListener(
        'input',
        calculate
    );

    interestRateInput.addEventListener(
        'input',
        calculate
    );

    termInput.addEventListener(
        'input',
        calculate
    );


    if (interestTypeInput) {

        interestTypeInput.addEventListener(
            'change',
            calculate
        );

    }


    if (paymentTypeInput) {

        paymentTypeInput.addEventListener(
            'change',
            calculate
        );

    }


    calculate();

})();

</script>


<!-- =====================================================================
     CREATE LOAN FORM VALIDATION
====================================================================== -->

<script>

(function ()
{

    const form =
        document.getElementById(
            'createLoanForm'
        );

    if (!form) {
        return;
    }


    form.addEventListener(
        'submit',
        function(event)
        {

            const borrower =
                document.getElementById(
                    'borrower_id'
                );

            const principal =
                parseFloat(
                    document.getElementById(
                        'principal_amount'
                    ).value
                ) || 0;


            const interestRate =
                parseFloat(
                    document.getElementById(
                        'interest_rate'
                    ).value
                ) || 0;


            const term =
                parseInt(
                    document.getElementById(
                        'term'
                    ).value
                ) || 0;


            if (
                !borrower ||
                !borrower.value
            ) {

                event.preventDefault();

                alert(
                    'Please select a borrower.'
                );

                borrower.focus();

                return;

            }


            if (
                principal <= 0
            ) {

                event.preventDefault();

                alert(
                    'Principal amount must be greater than zero.'
                );

                document
                    .getElementById(
                        'principal_amount'
                    )
                    .focus();

                return;

            }


            if (
                interestRate < 0
            ) {

                event.preventDefault();

                alert(
                    'Interest rate cannot be negative.'
                );

                document
                    .getElementById(
                        'interest_rate'
                    )
                    .focus();

                return;

            }


            if (
                term <= 0
            ) {

                event.preventDefault();

                alert(
                    'Loan term must be greater than zero.'
                );

                document
                    .getElementById(
                        'term'
                    )
                    .focus();

                return;

            }

        }
    );

})();

</script>


<!-- =====================================================================
     AUTO CLOSE FLASH ALERTS
====================================================================== -->

<script>

(function ()
{

    const alerts =
        document.querySelectorAll(
            '.loan-alert'
        );

    if (!alerts.length) {
        return;
    }


    setTimeout(
        function()
        {

            alerts.forEach(
                function(alert)
                {

                    alert.style.transition =
                        'opacity 0.3s ease';

                    alert.style.opacity =
                        '0';

                    setTimeout(
                        function()
                        {

                            if (
                                alert.parentNode
                            ) {

                                alert.parentNode
                                    .removeChild(
                                        alert
                                    );

                            }

                        },
                        350
                    );

                }
            );

        },
        5000
    );

})();

</script>


</body>
</html>