<?php

$user =
    $user
    ?? Auth::user();

$business =
    $business
    ?? Auth::business();

$tenantRole =
    $tenantRole
    ?? Auth::tenantRole();

$currentUrl = 'loans';

$loans =
    $loans
    ?? [];

$borrowers =
    $borrowers
    ?? [];

$categories =
    $categories
    ?? [];

$accounts =
    $accounts
    ?? [];

$success =
    $success
    ?? ($_SESSION['loan_success'] ?? '');

$error =
    $error
    ?? ($_SESSION['loan_error'] ?? '');

if (!function_exists('formatPaymentMethod')) {

    function formatPaymentMethod($value): string
    {
        $value =
            strtolower(
                trim(
                    (string)$value
                )
            );

        switch ($value) {

            case 'full_payment':

                return 'Full Payment';

            case 'installment':

                return 'Installment';

            default:

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
}

$totalLoans = count($loans);

$activeLoans = 0;

$pendingLoans = 0;

$totalPrincipal = 0;

$totalPayable = 0;


foreach ($loans as $loan) {

    $status =
        strtolower(
            $loan['status']
            ?? 'pending'
        );


    if ($status === 'active') {

        $activeLoans++;

    }


    if ($status === 'pending') {

        $pendingLoans++;

    }


    $totalPrincipal +=
        (float)(
            $loan['principal_amount']
            ?? 0
        );


    $totalPayable +=
        (float)(
            $loan['total_payable']
            ?? 0
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
        Loans | Loan Management
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

<style>.loan-decision-section { margin-top: 25px; padding: 18px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; } .loan-decision-title { font-size: 14px; font-weight: 700; color: #374151; margin-bottom: 12px; } .loan-decision-actions { display: flex; gap: 12px; align-items: center; } .loan-decision-actions form { margin: 0; padding: 0; } .loan-decision-button { min-width: 155px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 18px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; } .loan-decision-approve { background: #dcfce7; color: #166534; border: 1px solid #86efac; box-shadow: 0 0 12px rgba(34, 197, 94, 0.45); } .loan-decision-approve:hover { background: #bbf7d0; box-shadow: 0 0 20px rgba(34, 197, 94, 0.65); transform: translateY(-1px); } .loan-decision-reject { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; box-shadow: 0 0 12px rgba(239, 68, 68, 0.45); } .loan-decision-reject:hover { background: #fecaca; box-shadow: 0 0 20px rgba(239, 68, 68, 0.65); transform: translateY(-1px); } .loan-decision-button.disabled, .loan-decision-button:disabled { background: #e5e7eb; color: #9ca3af; border-color: #d1d5db; box-shadow: none; cursor: not-allowed; opacity: 0.8; transform: none; } .loan-decision-button.disabled:hover, .loan-decision-button:disabled:hover { background: #e5e7eb; color: #9ca3af; box-shadow: none; transform: none; } .modal-overlay { position: fixed; inset: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.55); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px; box-sizing: border-box; } .modal-overlay.active { display: flex; } .modal { width: 100%; max-width: 700px; max-height: 92vh; overflow-y: auto; background: #ffffff; border-radius: 12px; padding: 25px; box-sizing: border-box; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25); } .modal-small { max-width: 550px; } .modal-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 25px; } .modal-header h2 { margin: 0 0 5px 0; } .modal-header p { margin: 0; color: #6b7280; } .modal-close { border: none; background: transparent; font-size: 28px; line-height: 1; cursor: pointer; color: #6b7280; } .modal-close:hover { color: #111827; } .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; flex-wrap: wrap; } .loan-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; } .loan-form-full { grid-column: 1 / -1; } .form-group { margin-bottom: 0; } .form-group label { display: block; margin-bottom: 7px; font-weight: 600; } .form-group input, .form-group select, .form-group textarea { width: 100%; box-sizing: border-box; } .account-balance-hint { display: block; margin-top: 5px; font-size: 12px; color: #6b7280; } .loan-summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; margin-bottom: 25px; } .loan-summary-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); } .loan-summary-title { font-size: 14px; color: #6b7280; margin-bottom: 8px; } .loan-summary-value { font-size: 25px; font-weight: 700; color: #111827; } .loan-details-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; } .loan-detail-item { padding: 14px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; } .loan-detail-label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 5px; } .loan-detail-value { font-weight: 600; color: #111827; } .loan-detail-full { grid-column: 1 / -1; } .loan-due-date { display: inline-block; padding: 5px 10px; border-radius: 6px; background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 600; } .loan-payment-schedule { margin-top: 5px; } .loan-payment-schedule-title { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 10px; } .loan-payment-schedule-list { margin: 0; padding-left: 20px; color: #374151; } .loan-payment-schedule-list li { margin-bottom: 7px; line-height: 1.5; } .loan-payment-schedule-empty { color: #9ca3af; font-weight: 400; } .loan-status { display: inline-block; padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: capitalize; } .loan-status-pending { background: #fef3c7; color: #92400e; } .loan-status-approved { background: #dbeafe; color: #1e40af; } .loan-status-active { background: #dcfce7; color: #166534; } .loan-status-completed { background: #e0e7ff; color: #3730a3; } .loan-status-overdue { background: #fee2e2; color: #991b1b; } .loan-status-cancelled, .loan-status-rejected { background: #f3f4f6; color: #374151; } .payment-method { display: inline-block; padding: 5px 9px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #f3f4f6; color: #374151; } .loan-number { font-weight: 700; } .loan-actions { display: flex; gap: 6px; flex-wrap: wrap; } .loan-action-menu { position: relative; display: inline-block; } .loan-action-button { width: 36px; height: 36px; border: 1px solid #e5e7eb; background: #ffffff; border-radius: 8px; font-size: 22px; line-height: 1; cursor: pointer; color: #374151; display: flex; align-items: center; justify-content: center; padding: 0; position: relative; z-index: 2; } .loan-action-button:hover { background: #f3f4f6; } .loan-action-dropdown { position: absolute; right: 0; top: calc(100% + 6px); min-width: 180px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12); padding: 6px; z-index: 10000; display: none; box-sizing: border-box; } .loan-action-dropdown.active { display: block; } .loan-action-item { width: 100%; display: flex; align-items: center; gap: 10px; padding: 10px 12px; border: none; background: transparent; color: #374151; text-decoration: none; font-size: 14px; font-weight: 500; border-radius: 7px; cursor: pointer; box-sizing: border-box; text-align: left; } .loan-action-item:hover { background: #f3f4f6; } .loan-action-approve { color: #166534; } .loan-action-approve:hover { background: #dcfce7; } .loan-action-danger { color: #991b1b; } .loan-action-danger:hover { background: #fee2e2; } .loan-action-form { margin: 0; padding: 0; } @media (max-width: 1100px) { .loan-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } } @media (max-width: 700px) { .loan-form-grid, .loan-details-grid { grid-template-columns: 1fr; } .loan-form-full, .loan-detail-full { grid-column: auto; } .loan-summary-grid { grid-template-columns: 1fr; } .loan-decision-actions { flex-direction: column; align-items: stretch; } .loan-decision-actions form { width: 100%; } .loan-decision-button { width: 100%; } .modal-footer { justify-content: stretch; } }</style>

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


    <!-- =====================================================
        MAIN CONTAINER
    ====================================================== -->

    <div class="container">


        <!-- =================================================
            PAGE HEADER
        ================================================== -->

        <div class="page-header">

            <div>

                <h1>
                    Loans
                </h1>


                <p>

                    Manage borrower loans,
                    payments and loan status.

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


        <!-- =================================================
            SUCCESS
        ================================================== -->

        <?php if (!empty($success)): ?>

            <div
                class="alert alert-success"
                style="margin-bottom:20px;"
            >

                <?= htmlspecialchars(
                    $success
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
            ERROR
        ================================================== -->

        <?php if (!empty($error)): ?>

            <div
                class="alert alert-danger"
                style="margin-bottom:20px;"
            >

                <?= htmlspecialchars(
                    $error
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
            SUMMARY CARDS
        ================================================== -->

        <div class="loan-summary-grid">


            <div class="loan-summary-card">

                <div class="loan-summary-title">

                    Total Loans

                </div>


                <div class="loan-summary-value">

                    <?= number_format(
                        $totalLoans
                    ) ?>

                </div>

            </div>


            <div class="loan-summary-card">

                <div class="loan-summary-title">

                    Active Loans

                </div>


                <div class="loan-summary-value">

                    <?= number_format(
                        $activeLoans
                    ) ?>

                </div>

            </div>


            <div class="loan-summary-card">

                <div class="loan-summary-title">

                    Total Principal

                </div>


                <div class="loan-summary-value">

                    ₱<?= number_format(
                        $totalPrincipal,
                        2
                    ) ?>

                </div>

            </div>


            <div class="loan-summary-card">

                <div class="loan-summary-title">

                    Total Payable

                </div>


                <div class="loan-summary-value">

                    ₱<?= number_format(
                        $totalPayable,
                        2
                    ) ?>

                </div>

            </div>

        </div>


        <!-- =================================================
            LOANS TABLE
        ================================================== -->

        <?php if (empty($loans)): ?>


            <div
                class="form-card"
                style="margin-top:20px;"
            >

                <div class="empty-state">

                    <h3>

                        No Loans Found

                    </h3>


                    <p>

                        You haven't created
                        any loans yet.

                    </p>


                    <br>


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


            <div
                class="table-container"
                style="margin-top:20px;"
            >

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
                                Payment Method
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


                    <?php foreach (
                        $loans as $loan
                    ): ?>


                        <?php

                        $loanId =
                            (int)(
                                $loan['id']
                                ?? 0
                            );


                        $loanNumber =
                            $loan['loan_number']
                            ?? '';


                        $borrowerName =
                            $loan['borrower_name']
                            ?? $loan['full_name']
                            ?? $loan['borrower']
                            ?? 'Unknown Borrower';


                        $principal =
                            (float)(
                                $loan['principal_amount']
                                ?? 0
                            );


                        $interest =
                            (float)(
                                $loan['total_interest']
                                ?? 0
                            );


                        $payable =
                            (float)(
                                $loan['total_payable']
                                ?? 0
                            );


                        $interestRate =
                            (float)(
                                $loan['interest_rate']
                                ?? 0
                            );


                        $interestType =
                            $loan['interest_type']
                            ?? 'flat';


                        $term =
                            (int)(
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
                            (float)(
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
                                $loan['status']
                                ?? 'pending'
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

                        ?>


                        <tr>


                            <!-- LOAN NUMBER -->

                            <td>

                                <span class="loan-number">

                                    <?= htmlspecialchars(
                                        $loanNumber
                                    ) ?>

                                </span>

                            </td>


                            <!-- BORROWER -->

                            <td>

                                <?= htmlspecialchars(
                                    $borrowerName
                                ) ?>

                            </td>


                            <!-- PRINCIPAL -->

                            <td>

                                <strong>

                                    ₱<?= number_format(
                                        $principal,
                                        2
                                    ) ?>

                                </strong>

                            </td>


                            <!-- INTEREST -->

                            <td>

                                ₱<?= number_format(
                                    $interest,
                                    2
                                ) ?>

                            </td>


                            <!-- TOTAL PAYABLE -->

                            <td>

                                <strong>

                                    ₱<?= number_format(
                                        $payable,
                                        2
                                    ) ?>

                                </strong>

                            </td>


                            <!-- PAYMENT TYPE -->

                            <td>

                                <span class="payment-method">

                                    <?= htmlspecialchars(
                                        formatPaymentMethod(
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

                            <td>

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


                                        <!-- DETAILS -->

                                        <button
                                            type="button"
                                            class="loan-action-item"
                                            onclick="
                                                closeLoanActions();
                                                openLoanDetails(
                                                    <?= $loanId ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $loanNumber,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $borrowerName,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $categoryName,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $principal,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $interestRate,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $interestType,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $term,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $termPeriod,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $paymentType,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $processingFee,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $interest,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $payable,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $releaseDate,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $firstPaymentDate,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $status,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $purpose,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $notes,
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                );
                                            "
                                        >

                                            <span>👁</span>

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

                                            <span>✏️</span>

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

    <span>💵</span>

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
                                                onsubmit="
                                                    return confirm(
                                                        'Are you sure you want to cancel this loan?'
                                                    );
                                                "
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

                                                    <span>
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


        <?php endif; ?>


    </div>


</div>


<!-- =========================================================
    CREATE LOAN MODAL
========================================================== -->

<div
    class="modal-overlay"
    id="createLoanModal"
    onclick="closeCreateLoanModal(event)"
>


    <div
        class="modal"
        onclick="event.stopPropagation();"
    >


        <div class="modal-header">

            <div>

                <h2>
                    Create Loan
                </h2>


                <p>
                    Create a new borrower loan.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                onclick="closeCreateLoanModal()"
            >

                &times;

            </button>

        </div>


        <form
            method="POST"
            action="index.php?url=loans/store"
            id="createLoanForm"
        >


            <div class="loan-form-grid">


                <!-- BORROWER -->

                <div class="form-group">

                    <label for="borrower_id">

                        Borrower

                    </label>


                    <select
                        id="borrower_id"
                        name="borrower_id"
                        required
                    >

                        <option value="">

                            Select Borrower

                        </option>


                        <?php foreach (
                            $borrowers
                            as $borrower
                        ): ?>


                            <?php

                            $borrowerId =
                                (int)(
                                    $borrower['id']
                                    ?? 0
                                );


                            $borrowerFullName =
                                $borrower['full_name']
                                ??
                                trim(
                                    ($borrower['first_name'] ?? '')
                                    . ' '
                                    . ($borrower['middle_name'] ?? '')
                                    . ' '
                                    . ($borrower['last_name'] ?? '')
                                );


                            if (
                                $borrowerFullName === ''
                            ) {

                                $borrowerFullName =
                                    $borrower['name']
                                    ?? 'Borrower';

                            }

                            ?>


                            <option
                                value="<?= $borrowerId ?>"
                            >

                                <?= htmlspecialchars(
                                    $borrowerFullName
                                ) ?>

                            </option>


                        <?php endforeach; ?>


                    </select>

                </div>


                <!-- CATEGORY -->

                <div class="form-group">

                    <label for="category_id">

                        Loan Category

                    </label>


                    <select
                        id="category_id"
                        name="category_id"
                    >

                        <option value="">

                            Select Category

                        </option>


                        <?php foreach (
                            $categories
                            as $category
                        ): ?>


                            <?php

                            $categoryId =
                                (int)(
                                    $category['id']
                                    ?? 0
                                );


                            $categoryNameOption =
                                $category['name']
                                ??
                                $category['category_name']
                                ??
                                'Category';

                            ?>


                            <option
                                value="<?= $categoryId ?>"
                            >

                                <?= htmlspecialchars(
                                    $categoryNameOption
                                ) ?>

                            </option>


                        <?php endforeach; ?>


                    </select>

                </div>


                <!-- ACCOUNT -->

                <div class="form-group">

                    <label for="account_id">

                        Account

                    </label>


                    <select
                        id="account_id"
                        name="account_id"
                        required
                    >

                        <option value="">

                            Select Account

                        </option>


                        <?php foreach (
                            $accounts
                            as $account
                        ): ?>


                            <?php

                            $accountId =
                                (int)(
                                    $account['id']
                                    ?? 0
                                );


                            $accountName =
                                $account['account_name']
                                ?? 'Account';


                            $accountBalance =
                                (float)(
                                    $account['balance']
                                    ?? 0
                                );

                            ?>


                            <option
                                value="<?= $accountId ?>"
                                data-balance="<?= htmlspecialchars(
                                    (string)$accountBalance,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
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
                        class="account-balance-hint"
                        id="accountBalanceHint"
                    >

                        Select an account.

                    </span>

                </div>


                <!-- PRINCIPAL -->

                <div class="form-group">

                    <label for="principal_amount">

                        Principal Amount

                    </label>


                    <input
                        type="number"
                        id="principal_amount"
                        name="principal_amount"
                        min="0.01"
                        step="0.01"
                        placeholder="0.00"
                        required
                    >

                </div>


                <!-- INTEREST RATE -->

                <div class="form-group">

                    <label for="interest_rate">

                        Interest Rate (%)

                    </label>


                    <input
                        type="number"
                        id="interest_rate"
                        name="interest_rate"
                        min="0"
                        step="0.01"
                        value="0.00"
                        placeholder="0.00"
                        required
                    >

                </div>


                <!-- INTEREST TYPE -->

                <div class="form-group">

                    <label for="interest_type">

                        Interest Type

                    </label>


                    <select
                        id="interest_type"
                        name="interest_type"
                        required
                    >

                        <option value="flat">

                            Flat

                        </option>


                        <option value="reducing_balance">

                            Reducing Balance

                        </option>

                    </select>

                </div>


                <!-- PAYMENT TYPE -->

                <div class="form-group">

                    <label for="payment_type">

                        Payment Type

                    </label>


                    <select
                        id="payment_type"
                        name="payment_type"
                        required
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


                <!-- TERM -->

                <div class="form-group">

                    <label for="term">

                        Term

                    </label>


                    <input
                        type="number"
                        id="term"
                        name="term"
                        min="1"
                        step="1"
                        value="1"
                        required
                    >

                </div>


                <!-- TERM PERIOD -->

                <div class="form-group">

                    <label for="term_period">

                        Term Period

                    </label>


                    <select
                        id="term_period"
                        name="term_period"
                        required
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


                <!-- PROCESSING FEE -->

                <div class="form-group">

                    <label for="processing_fee">

                        Processing Fee

                    </label>


                    <input
                        type="number"
                        id="processing_fee"
                        name="processing_fee"
                        min="0"
                        step="0.01"
                        value="0.00"
                        placeholder="0.00"
                    >

                </div>


                <!-- RELEASE DATE -->

                <div class="form-group">

                    <label for="release_date">

                        Release Date

                    </label>


                    <input
                        type="date"
                        id="release_date"
                        name="release_date"
                    >

                </div>


                <!-- FIRST PAYMENT DATE -->

                <div class="form-group">

                    <label for="first_payment_date">

                        First Payment Date

                    </label>


                    <input
                        type="date"
                        id="first_payment_date"
                        name="first_payment_date"
                    >


                    <span
                        style="
                            display:block;
                            margin-top:5px;
                            font-size:12px;
                            color:#6b7280;
                        "
                    >

                        Leave blank to automatically calculate
                        the first due date from the release date.

                    </span>

                </div>


                <!-- PURPOSE -->

                <div class="form-group loan-form-full">

                    <label for="purpose">

                        Purpose

                    </label>


                    <input
                        type="text"
                        id="purpose"
                        name="purpose"
                        maxlength="255"
                        placeholder="Example: Business capital"
                    >

                </div>


                <!-- NOTES -->

                <div class="form-group loan-form-full">

                    <label for="notes">

                        Notes

                    </label>


                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        placeholder="Additional notes..."
                    ></textarea>

                </div>


            </div>


            <div class="modal-footer">


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


<!-- =========================================================
    LOAN DETAILS MODAL
========================================================== -->

<div
    class="modal-overlay"
    id="loanDetailsModal"
    onclick="closeLoanDetails(event)"
>


    <div
        class="modal"
        onclick="event.stopPropagation();"
    >


        <div class="modal-header">

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
                class="modal-close"
                onclick="closeLoanDetails()"
            >

                &times;

            </button>

        </div>


        <div class="loan-details-grid">


            <div class="loan-detail-item">

                <span class="loan-detail-label">

                    Loan Number

                </span>


                <span
                    class="loan-detail-value"
                    id="detail_loan_number"
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
                    id="detail_borrower"
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
                    id="detail_category"
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
                    id="detail_payment_method"
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
                    id="detail_status"
                >

                    -

                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">

                    Principal Amount

                </span>


                <span
                    class="loan-detail-value"
                    id="detail_principal"
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
                    id="detail_interest_rate"
                >

                    0.00%

                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">

                    Interest Type

                </span>


                <span
                    class="loan-detail-value"
                    id="detail_interest_type"
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
                    id="detail_term"
                >

                    -

                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">

                    Due Date

                </span>


                <span
                    class="loan-detail-value"
                    id="detail_due_date"
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
                    id="detail_processing_fee"
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
                    id="detail_total_interest"
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
                    id="detail_total_payable"
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
                    id="detail_release_date"
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
                    id="detail_first_payment_date"
                >

                    -

                </span>

            </div>


            <div class="loan-detail-item">

                <span class="loan-detail-label">

                    Purpose

                </span>


                <span
                    class="loan-detail-value"
                    id="detail_purpose"
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
                    id="detail_notes"
                >

                    -

                </span>

            </div>


            <!-- =================================================
                INSTALLMENT SCHEDULE
            ================================================== -->

            <div class="loan-detail-item loan-detail-full">

                <span class="loan-detail-label">

                    Payment Schedule

                </span>


                <div
                    class="loan-payment-schedule"
                    id="detail_payment_schedule"
                >

                    <span class="loan-payment-schedule-empty">

                        No payment schedule available.

                    </span>

                </div>

            </div>


        </div>


        <!-- =====================================================
            APPROVE / REJECT / CLOSE
        ====================================================== -->

        <div class="modal-footer">


            <!-- APPROVE -->

            <form
                method="POST"
                action="index.php?url=loans/approve"
                id="approveLoanForm"
                style="margin:0;"
            >

                <input
                    type="hidden"
                    name="id"
                    id="approve_loan_id"
                    value=""
                >


                <button
                    type="submit"
                    id="approveLoanButton"
                    class="loan-decision-button loan-decision-approve"
                >

                    ✓ Approve Loan

                </button>

            </form>


            <!-- REJECT -->

            <form
                method="POST"
                action="index.php?url=loans/reject"
                id="rejectLoanForm"
                style="margin:0;"
            >

                <input
                    type="hidden"
                    name="id"
                    id="reject_loan_id"
                    value=""
                >


                <button
                    type="submit"
                    id="rejectLoanButton"
                    class="loan-decision-button loan-decision-reject"
                >

                    ✕ Reject Loan

                </button>

            </form>


            <!-- CLOSE -->

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
<script>
function formatPaymentMethod(value) {
    value = String(value || 'installment').toLowerCase();
    if (value === 'full_payment') return 'Full Payment';
    if (value === 'installment') return 'Installment';
    return formatText(value);
}

function openCreateLoanModal() {
    const modal = document.getElementById('createLoanModal');
    if (modal) modal.classList.add('active');
}

function closeCreateLoanModal(event) {
    if (event && event.target !== event.currentTarget) return;
    const modal = document.getElementById('createLoanModal');
    if (modal) modal.classList.remove('active');
}

function formatMoney(value) {
    return '₱' + Number(value || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function formatText(value) {
    if (!value) return '-';
    return String(value)
        .replace(/_/g, ' ')
        .replace(/\b\w/g, letter => letter.toUpperCase());
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function createStatusBadge(status) {
    const cleanStatus = String(status || 'pending').toLowerCase().trim();

    return `
        <span class="loan-status loan-status-${escapeHtml(cleanStatus)}">
            ${escapeHtml(formatText(cleanStatus))}
        </span>
    `;
}


/* ---------------------------------------------------------
   CREATE LOAN VALIDATION
--------------------------------------------------------- */

const accountSelect = document.getElementById('account_id');
const principalInput = document.getElementById('principal_amount');
const accountBalanceHint = document.getElementById('accountBalanceHint');
const createLoanForm = document.getElementById('createLoanForm');

function updateAccountBalanceHint() {
    if (!accountSelect || !accountBalanceHint) return;

    const option = accountSelect.options[accountSelect.selectedIndex];

    if (!option || !option.value) {
        accountBalanceHint.textContent =
            'Select an account to see its available balance.';
        return;
    }

    const balance = Number(option.dataset.balance || 0);
    accountBalanceHint.textContent =
        'Available balance: ' + formatMoney(balance);
}

if (accountSelect) {
    accountSelect.addEventListener('change', updateAccountBalanceHint);
}

if (createLoanForm && accountSelect && principalInput) {
    createLoanForm.addEventListener('submit', function(event) {
        const option = accountSelect.options[accountSelect.selectedIndex];

        if (!option || !option.value) {
            event.preventDefault();
            alert('Please select a disbursement account.');
            accountSelect.focus();
            return;
        }

        const balance = Number(option.dataset.balance || 0);
        const principal = Number(principalInput.value || 0);

        if (principal <= 0) {
            event.preventDefault();
            alert('Principal amount must be greater than zero.');
            principalInput.focus();
            return;
        }

        if (principal > balance) {
            event.preventDefault();
            alert(
                'Insufficient balance in the selected account.\n\n' +
                'Available balance: ' + formatMoney(balance) + '\n' +
                'Principal amount: ' + formatMoney(principal)
            );
            principalInput.focus();
        }
    });
}


/* ---------------------------------------------------------
   DATE FUNCTIONS
--------------------------------------------------------- */

const releaseDateInput = document.getElementById('release_date');
const firstPaymentInput = document.getElementById('first_payment_date');
const termInput = document.getElementById('term');
const termPeriodInput = document.getElementById('term_period');

function formatDateForInput(date) {
    return [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2, '0'),
        String(date.getDate()).padStart(2, '0')
    ].join('-');
}

function parseLocalDate(value) {
    if (!value) return null;

    const parts = String(value).split('-');
    if (parts.length !== 3) return null;

    const date = new Date(
        Number(parts[0]),
        Number(parts[1]) - 1,
        Number(parts[2])
    );

    return Number.isNaN(date.getTime()) ? null : date;
}

function addPaymentPeriod(date, period, amount = 1) {
    const result = new Date(date.getTime());

    period = String(period || 'months').toLowerCase();
    amount = Number(amount || 1);

    if (amount <= 0) amount = 1;

    switch (period) {
        case 'days':
            result.setDate(result.getDate() + amount);
            break;

        case 'weeks':
            result.setDate(result.getDate() + amount * 7);
            break;

        case 'years':
            result.setFullYear(result.getFullYear() + amount);
            break;

        case 'months':
        default:
            const originalDay = result.getDate();

            result.setDate(1);
            result.setMonth(result.getMonth() + amount);

            const lastDay = new Date(
                result.getFullYear(),
                result.getMonth() + 1,
                0
            ).getDate();

            result.setDate(Math.min(originalDay, lastDay));
            break;
    }

    return result;
}

function formatDisplayDate(date) {
    if (!date) return '-';

    return date.toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function updateAutomaticFirstPaymentDate() {
    if (!releaseDateInput || !firstPaymentInput || !termPeriodInput) return;
    if (!releaseDateInput.value || firstPaymentInput.value) return;

    const releaseDate = parseLocalDate(releaseDateInput.value);
    if (!releaseDate) return;

    firstPaymentInput.value = formatDateForInput(
        addPaymentPeriod(
            releaseDate,
            termPeriodInput.value || 'months',
            1
        )
    );
}

if (releaseDateInput) {
    releaseDateInput.addEventListener(
        'change',
        updateAutomaticFirstPaymentDate
    );
}

if (termPeriodInput) {
    termPeriodInput.addEventListener('change', function() {
        if (firstPaymentInput && !firstPaymentInput.value) {
            updateAutomaticFirstPaymentDate();
        }
    });
}


/* ---------------------------------------------------------
   PAYMENT SCHEDULE
--------------------------------------------------------- */

function generatePaymentSchedule(
    releaseDateValue,
    firstPaymentDateValue,
    term,
    termPeriod,
    paymentType,
    totalPayable
) {
    const container = document.getElementById('detail_payment_schedule');
    if (!container) return;

    container.innerHTML = '';

    let firstDueDate = firstPaymentDateValue
        ? parseLocalDate(firstPaymentDateValue)
        : null;

    if (!firstDueDate && releaseDateValue) {
        const releaseDate = parseLocalDate(releaseDateValue);

        if (releaseDate) {
            firstDueDate = addPaymentPeriod(
                releaseDate,
                termPeriod,
                1
            );
        }
    }

    if (!firstDueDate) {
        container.innerHTML =
            '<span class="loan-payment-schedule-empty">' +
            'Release date is required to calculate the due date.' +
            '</span>';
        return;
    }

    if (String(paymentType).toLowerCase() === 'full_payment') {
        container.innerHTML =
            '<div class="loan-payment-schedule-title">' +
            'Full Payment Due Date</div>' +
            '<ol class="loan-payment-schedule-list">' +
            '<li><strong>' +
            escapeHtml(formatDisplayDate(firstDueDate)) +
            '</strong> — ' +
            formatMoney(totalPayable) +
            '</li></ol>';

        return;
    }

    const paymentCount = Math.max(Number(term || 1), 1);
    const installmentAmount =
        Number(totalPayable || 0) / paymentCount;

    let html =
        '<div class="loan-payment-schedule-title">' +
        paymentCount +
        ' Installment Payment' +
        (paymentCount !== 1 ? 's' : '') +
        '</div>' +
        '<ol class="loan-payment-schedule-list">';

    for (let i = 1; i <= paymentCount; i++) {
        const dueDate = i === 1
            ? new Date(firstDueDate)
            : addPaymentPeriod(
                firstDueDate,
                termPeriod,
                i - 1
            );

        html +=
            '<li><strong>' +
            escapeHtml(formatDisplayDate(dueDate)) +
            '</strong> — ' +
            formatMoney(installmentAmount) +
            '</li>';
    }

    container.innerHTML = html + '</ol>';
}


/* ---------------------------------------------------------
   LOAN DETAILS
--------------------------------------------------------- */

function openLoanDetails(
    id,
    loanNumber,
    borrower,
    category,
    principal,
    interestRate,
    interestType,
    term,
    termPeriod,
    paymentType,
    processingFee,
    totalInterest,
    totalPayable,
    releaseDate,
    firstPaymentDate,
    status,
    purpose,
    notes
) {
    const setText = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    };

    setText('detail_loan_number', loanNumber || '-');
    setText('detail_borrower', borrower || '-');
    setText('detail_category', category || '-');
    setText('detail_payment_method', formatPaymentMethod(paymentType));
    setText('detail_principal', formatMoney(principal));
    setText(
        'detail_interest_rate',
        Number(interestRate || 0).toFixed(2) + '%'
    );
    setText('detail_interest_type', formatText(interestType));
    setText(
        'detail_term',
        Number(term || 0) + ' ' + formatText(termPeriod)
    );
    setText('detail_processing_fee', formatMoney(processingFee));
    setText('detail_total_interest', formatMoney(totalInterest));
    setText('detail_total_payable', formatMoney(totalPayable));
    setText('detail_purpose', purpose || '-');
    setText('detail_notes', notes || '-');

    let calculatedFirstDueDate = firstPaymentDate
        ? parseLocalDate(firstPaymentDate)
        : null;

    if (!calculatedFirstDueDate && releaseDate) {
        const releaseDateObject = parseLocalDate(releaseDate);

        if (releaseDateObject) {
            calculatedFirstDueDate = addPaymentPeriod(
                releaseDateObject,
                termPeriod,
                1
            );
        }
    }

    const dueDateElement = document.getElementById('detail_due_date');

    if (dueDateElement) {
        dueDateElement.innerHTML = calculatedFirstDueDate
            ? '<span class="loan-due-date">' +
              escapeHtml(
                  formatDisplayDate(calculatedFirstDueDate)
              ) +
              '</span>'
            : '-';
    }

    const releaseDateElement =
        document.getElementById('detail_release_date');

    setText(
        'detail_release_date',
        releaseDate
            ? formatDisplayDate(parseLocalDate(releaseDate))
            : '-'
    );

    const firstPaymentDateElement =
        document.getElementById('detail_first_payment_date');

    setText(
        'detail_first_payment_date',
        firstPaymentDate
            ? formatDisplayDate(parseLocalDate(firstPaymentDate))
            : calculatedFirstDueDate
                ? formatDisplayDate(calculatedFirstDueDate)
                : '-'
    );

    const statusElement = document.getElementById('detail_status');

    if (statusElement) {
        statusElement.innerHTML = createStatusBadge(status);
    }

    generatePaymentSchedule(
        releaseDate,
        firstPaymentDate,
        term,
        termPeriod,
        paymentType,
        totalPayable
    );

    const approveLoanId =
        document.getElementById('approve_loan_id');

    const rejectLoanId =
        document.getElementById('reject_loan_id');

    const approveButton =
        document.getElementById('approveLoanButton');

    const rejectButton =
        document.getElementById('rejectLoanButton');

    if (approveLoanId) approveLoanId.value = id;
    if (rejectLoanId) rejectLoanId.value = id;

    const canApprove =
        String(status || 'pending').toLowerCase().trim() === 'pending';

    if (approveButton) {
        approveButton.disabled = !canApprove;
        approveButton.classList.toggle('disabled', !canApprove);
    }

    if (rejectButton) {
        rejectButton.disabled = !canApprove;
        rejectButton.classList.toggle('disabled', !canApprove);
    }

    const modal = document.getElementById('loanDetailsModal');

    if (modal) modal.classList.add('active');
}

function closeLoanDetails(event) {
    if (event && event.target !== event.currentTarget) return;

    const modal = document.getElementById('loanDetailsModal');

    if (modal) modal.classList.remove('active');
}


/* ---------------------------------------------------------
   LOAN ACTION DROPDOWN
--------------------------------------------------------- */

function toggleLoanActions(loanId) {
    const dropdown =
        document.getElementById('loan-actions-' + loanId);

    if (!dropdown) {
        console.warn(
            'Loan action dropdown not found:',
            'loan-actions-' + loanId
        );
        return;
    }

    const button = dropdown
        .closest('.loan-action-menu')
        ?.querySelector('.loan-action-button');

    const wasOpen = dropdown.classList.contains('active');

    closeLoanActions();

    if (!wasOpen) {
        dropdown.classList.add('active');

        if (button) {
            button.setAttribute('aria-expanded', 'true');
        }
    }
}

function closeLoanActions() {
    document
        .querySelectorAll('.loan-action-dropdown.active')
        .forEach(dropdown => {
            dropdown.classList.remove('active');

            const button = dropdown
                .closest('.loan-action-menu')
                ?.querySelector('.loan-action-button');

            if (button) {
                button.setAttribute('aria-expanded', 'false');
            }
        });
}

document.addEventListener('click', event => {
    if (!event.target.closest('.loan-action-menu')) {
        closeLoanActions();
    }
});


/* ---------------------------------------------------------
   APPROVE / REJECT
--------------------------------------------------------- */

const approveLoanForm =
    document.getElementById('approveLoanForm');

if (approveLoanForm) {
    approveLoanForm.addEventListener('submit', event => {
        const loanId =
            document.getElementById('approve_loan_id');

        if (!loanId || !loanId.value) {
            event.preventDefault();
            alert('No loan selected.');
            return;
        }

        if (!confirm(
            'Are you sure you want to approve this loan?\n\n' +
            'Once approved, the loan will proceed to the next stage.'
        )) {
            event.preventDefault();
        }
    });
}

const rejectLoanForm =
    document.getElementById('rejectLoanForm');

if (rejectLoanForm) {
    rejectLoanForm.addEventListener('submit', event => {
        const loanId =
            document.getElementById('reject_loan_id');

        if (!loanId || !loanId.value) {
            event.preventDefault();
            alert('No loan selected.');
            return;
        }

        if (!confirm(
            'Are you sure you want to reject this loan?\n\n' +
            'This action cannot be easily undone.'
        )) {
            event.preventDefault();
        }
    });
}


/* ---------------------------------------------------------
   KEYBOARD / INITIALIZATION
--------------------------------------------------------- */

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        closeCreateLoanModal();
        closeLoanDetails();
        closeLoanActions();
    }
});

if (releaseDateInput &&
    firstPaymentInput &&
    !firstPaymentInput.value) {
    updateAutomaticFirstPaymentDate();
}

if (accountSelect) {
    updateAccountBalanceHint();
}

document.addEventListener('DOMContentLoaded', () => {
    console.log(
        'Loan action buttons found:',
        document.querySelectorAll('.loan-action-button').length
    );
});
</script>
</body>
</html>