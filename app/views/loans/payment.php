<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| USER / BUSINESS / ROLE
|--------------------------------------------------------------------------
*/

$user = $user ?? Auth::user();

$business = $business ?? Auth::business();

$tenantRole = $tenantRole ?? Auth::tenantRole();

$currentUrl = $currentUrl ?? 'loans/payment';


/*
|--------------------------------------------------------------------------
| FLASH MESSAGES
|--------------------------------------------------------------------------
*/

$success = $_SESSION['loan_success'] ?? '';

$error = $_SESSION['loan_error'] ?? '';

unset(
    $_SESSION['loan_success'],
    $_SESSION['loan_error']
);


/*
|--------------------------------------------------------------------------
| LOAN INFORMATION
|--------------------------------------------------------------------------
*/

$loanId = (int)(
    $loan['id']
    ?? 0
);


$loanName =
    $loan['loan_number']
    ?? ('Loan #' . $loanId);


$borrowerName =
    $loan['borrower_name']
    ?? 'Borrower';


$totalPayable =
    (float)(
        $loan['total_payable']
        ?? 0
    );


$totalPaid =
    (float)(
        $totalPaid
        ?? 0
    );


$remainingBalance =
    max(
        0,
        $totalPayable - $totalPaid
    );


/*
|--------------------------------------------------------------------------
| PAYMENT SCHEDULE
|--------------------------------------------------------------------------
*/

$schedule =
    is_array($schedule ?? null)
        ? $schedule
        : [];


/*
|--------------------------------------------------------------------------
| ACCOUNTS
|--------------------------------------------------------------------------
*/

$accounts =
    is_array($accounts ?? null)
        ? $accounts
        : [];


/*
|--------------------------------------------------------------------------
| TODAY
|--------------------------------------------------------------------------
*/

$today = date('Y-m-d');


/*
|--------------------------------------------------------------------------
| PAYMENT PROGRESS
|--------------------------------------------------------------------------
*/

$paymentPercentage = 0;

if ($totalPayable > 0) {

    $paymentPercentage =
        min(
            100,
            max(
                0,
                ($totalPaid / $totalPayable) * 100
            )
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
        Make Payment |
        <?= htmlspecialchars($loanName) ?>
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | PAYMENT PAGE
        |--------------------------------------------------------------------------
        */

        .payment-page {
            width: 100%;
            box-sizing: border-box;
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT HEADER
        |--------------------------------------------------------------------------
        */

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 25px;
        }


        .payment-header-content h1 {
            margin: 0 0 7px;
            font-size: 28px;
            font-weight: 700;
        }


        .payment-header-content p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }


        /*
        |--------------------------------------------------------------------------
        | ALERTS
        |--------------------------------------------------------------------------
        */

        .payment-alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }


        .payment-alert-success {
            background: #ecfdf3;
            border: 1px solid #bbf7d0;
            color: #166534;
        }


        .payment-alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT GRID
        |--------------------------------------------------------------------------
        */

        .payment-grid {
            display: grid;
            grid-template-columns: 360px minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }


        /*
        |--------------------------------------------------------------------------
        | CARD
        |--------------------------------------------------------------------------
        */

        .payment-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
            box-shadow:
                0 4px 14px rgba(0, 0, 0, 0.04);
        }


        .payment-card-header {
            padding: 20px 22px;
            border-bottom: 1px solid #e5e7eb;
        }


        .payment-card-header h2 {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
        }


        .payment-card-header p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 13px;
        }


        .payment-card-body {
            padding: 22px;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAN SUMMARY
        |--------------------------------------------------------------------------
        */

        .loan-summary-card {
            position: sticky;
            top: 20px;
        }


        .loan-number {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }


        .loan-borrower {
            color: #6b7280;
            font-size: 14px;
        }


        /*
        |--------------------------------------------------------------------------
        | BALANCE
        |--------------------------------------------------------------------------
        */

        .balance-box {
            margin-top: 20px;
            padding: 20px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }


        .balance-label {
            display: block;
            color: #64748b;
            font-size: 13px;
            margin-bottom: 6px;
        }


        .balance-amount {
            display: block;
            font-size: 28px;
            font-weight: 800;
            color: #111827;
        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        .summary-list {
            margin-top: 20px;
        }


        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 13px 0;
            border-bottom: 1px solid #f1f5f9;
        }


        .summary-row:last-child {
            border-bottom: none;
        }


        .summary-label {
            color: #64748b;
            font-size: 13px;
        }


        .summary-value {
            font-size: 14px;
            font-weight: 600;
            text-align: right;
        }


        /*
        |--------------------------------------------------------------------------
        | PROGRESS
        |--------------------------------------------------------------------------
        */

        .payment-progress {
            margin-top: 20px;
        }


        .payment-progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
            color: #64748b;
        }


        .payment-progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
        }


        .payment-progress-fill {
            height: 100%;
            border-radius: 999px;
            background: #2563eb;
            transition: width 0.3s ease;
        }


        /*
        |--------------------------------------------------------------------------
        | FORM
        |--------------------------------------------------------------------------
        */

        .payment-form-group {
            margin-bottom: 20px;
        }


        .payment-form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }


        .payment-form-group input,
        .payment-form-group select,
        .payment-form-group textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            background: #fff;
            font-size: 14px;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }


        .payment-form-group input:focus,
        .payment-form-group select:focus,
        .payment-form-group textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.10);
        }


        .payment-form-group textarea {
            resize: vertical;
            min-height: 100px;
        }


        .payment-help {
            display: block;
            margin-top: 6px;
            color: #6b7280;
            font-size: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | AMOUNT
        |--------------------------------------------------------------------------
        */

        .amount-wrapper {
            position: relative;
        }


        .amount-symbol {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 600;
            color: #64748b;
            pointer-events: none;
        }


        .amount-input {
            padding-left: 32px !important;
            font-size: 18px !important;
            font-weight: 600;
        }


        /*
        |--------------------------------------------------------------------------
        | WARNING
        |--------------------------------------------------------------------------
        */

        .payment-warning {
            padding: 12px 14px;
            margin-bottom: 20px;
            border-radius: 9px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 13px;
        }


        /*
        |--------------------------------------------------------------------------
        | ACTIONS
        |--------------------------------------------------------------------------
        */

        .payment-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 18px;
            margin-top: 10px;
            border-top: 1px solid #e5e7eb;
        }


        /*
        |--------------------------------------------------------------------------
        | BUTTONS
        |--------------------------------------------------------------------------
        */

        .payment-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 42px;
            padding: 0 18px;
            border-radius: 9px;
            border: none;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
            box-sizing: border-box;
        }


        .payment-btn-primary {
            background: #2563eb;
            color: #fff;
        }


        .payment-btn-primary:hover {
            background: #1d4ed8;
        }


        .payment-btn-secondary {
            background: #f1f5f9;
            color: #334155;
        }


        .payment-btn-secondary:hover {
            background: #e2e8f0;
        }


        .payment-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 950px) {

            .payment-grid {
                grid-template-columns: 1fr;
            }


            .loan-summary-card {
                position: static;
            }

        }


        @media (max-width: 700px) {

            .payment-header {
                flex-direction: column;
            }


            .payment-header .payment-btn {
                width: 100%;
            }


            .payment-actions {
                flex-direction: column-reverse;
            }


            .payment-actions .payment-btn {
                width: 100%;
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

            Make Payment

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
                    ?? $user['role']
                    ?? 'User'
                ) ?>

            </span>


        </div>


    </nav>


    <div class="container">


        <div class="payment-page">


            <!--
            |--------------------------------------------------------------------------
            | PAGE HEADER
            |--------------------------------------------------------------------------
            -->


            <div class="payment-header">


                <div class="payment-header-content">


                    <h1>

                        Make Loan Payment

                    </h1>


                    <p>

                        Record and apply a payment to this loan.

                    </p>


                </div>


                <a
                    href="index.php?url=loans"
                    class="payment-btn payment-btn-secondary"
                >

                    ← Back to Loans

                </a>


            </div>


            <!--
            |--------------------------------------------------------------------------
            | ALERTS
            |--------------------------------------------------------------------------
            -->


            <?php if ($success): ?>

                <div
                    class="payment-alert payment-alert-success"
                >

                    <?= htmlspecialchars($success) ?>

                </div>

            <?php endif; ?>


            <?php if ($error): ?>

                <div
                    class="payment-alert payment-alert-danger"
                >

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!--
            |--------------------------------------------------------------------------
            | PAYMENT GRID
            |--------------------------------------------------------------------------
            -->


            <div class="payment-grid">


                <!--
                |--------------------------------------------------------------------------
                | LOAN SUMMARY
                |--------------------------------------------------------------------------
                -->


                <div class="payment-card loan-summary-card">


                    <div class="payment-card-header">

                        <h2>
                            Loan Summary
                        </h2>

                        <p>
                            Current loan information
                        </p>

                    </div>


                    <div class="payment-card-body">


                        <div class="loan-number">

                            <?= htmlspecialchars(
                                $loanName
                            ) ?>

                        </div>


                        <div class="loan-borrower">

                            <?= htmlspecialchars(
                                $borrowerName
                            ) ?>

                        </div>


                        <!-- BALANCE -->


                        <div class="balance-box">

                            <span class="balance-label">

                                Remaining Balance

                            </span>


                            <span class="balance-amount">

                                ₱<?= number_format(
                                    $remainingBalance,
                                    2
                                ) ?>

                            </span>

                        </div>


                        <!-- SUMMARY -->


                        <div class="summary-list">


                            <div class="summary-row">

                                <span class="summary-label">

                                    Total Payable

                                </span>


                                <span class="summary-value">

                                    ₱<?= number_format(
                                        $totalPayable,
                                        2
                                    ) ?>

                                </span>

                            </div>


                            <div class="summary-row">

                                <span class="summary-label">

                                    Total Paid

                                </span>


                                <span class="summary-value">

                                    ₱<?= number_format(
                                        $totalPaid,
                                        2
                                    ) ?>

                                </span>

                            </div>


                            <div class="summary-row">

                                <span class="summary-label">

                                    Remaining

                                </span>


                                <span class="summary-value">

                                    ₱<?= number_format(
                                        $remainingBalance,
                                        2
                                    ) ?>

                                </span>

                            </div>


                        </div>


                        <!-- PROGRESS -->


                        <div class="payment-progress">


                            <div class="payment-progress-header">

                                <span>
                                    Payment Progress
                                </span>


                                <span>

                                    <?= number_format(
                                        $paymentPercentage,
                                        1
                                    ) ?>%

                                </span>

                            </div>


                            <div class="payment-progress-bar">


                                <div
                                    class="payment-progress-fill"
                                    style="
                                        width:
                                        <?= $paymentPercentage ?>%;
                                    "
                                ></div>


                            </div>


                        </div>


                    </div>


                </div>


                <!--
                |--------------------------------------------------------------------------
                | PAYMENT FORM
                |--------------------------------------------------------------------------
                -->


                <div class="payment-card">


                    <div class="payment-card-header">

                        <h2>
                            Payment Details
                        </h2>

                        <p>
                            Enter the payment information below.
                        </p>

                    </div>


                    <div class="payment-card-body">


                        <?php if (empty($accounts)): ?>

                            <div class="payment-warning">

                                No active accounts are available.
                                Please create an account before
                                recording this payment.

                            </div>

                        <?php endif; ?>


                        <form
                            method="POST"
                            action="index.php?url=loans/payment/store"
                            id="paymentForm"
                        >


                            <!-- LOAN ID -->


                            <input
                                type="hidden"
                                name="loan_id"
                                value="<?= $loanId ?>"
                            >


                            <!-- PAYMENT SCHEDULE -->


                            <div class="payment-form-group">


                                <label
                                    for="schedule_id"
                                >

                                    Payment Schedule

                                </label>


                                <select
                                    name="schedule_id"
                                    id="schedule_id"
                                >


                                    <option value="">

                                        General Loan Payment

                                    </option>


                                    <?php foreach (
                                        $schedule
                                        as $item
                                    ): ?>


                                        <?php

                                        $scheduleRemaining =
                                            max(
                                                0,
                                                (float)(
                                                    $item['total_due']
                                                    ?? 0
                                                )
                                                -
                                                (float)(
                                                    $item['paid_amount']
                                                    ?? 0
                                                )
                                            );

                                        ?>


                                        <?php if (
                                            $scheduleRemaining > 0
                                        ): ?>


                                            <option
                                                value="<?= (int)(
                                                    $item['id']
                                                    ?? 0
                                                ) ?>"
                                                data-remaining="<?= htmlspecialchars(
                                                    number_format(
                                                        $scheduleRemaining,
                                                        2,
                                                        '.',
                                                        ''
                                                    )
                                                ) ?>"
                                            >

                                                Installment
                                                <?= (int)(
                                                    $item[
                                                        'installment_number'
                                                    ]
                                                    ?? 0
                                                ) ?>

                                                —

                                                Due
                                                <?= htmlspecialchars(
                                                    $item[
                                                        'due_date'
                                                    ]
                                                    ?? ''
                                                ) ?>

                                                —

                                                ₱<?= number_format(
                                                    $scheduleRemaining,
                                                    2
                                                ) ?>

                                            </option>


                                        <?php endif; ?>


                                    <?php endforeach; ?>


                                </select>


                                <small class="payment-help">

                                    Select an installment to
                                    automatically use its
                                    remaining amount.

                                </small>


                            </div>


                            <!-- PAYMENT DATE -->


                            <div class="payment-form-group">


                                <label
                                    for="payment_date"
                                >

                                    Payment Date

                                </label>


                                <input
                                    type="date"
                                    name="payment_date"
                                    id="payment_date"
                                    value="<?= htmlspecialchars(
                                        $today
                                    ) ?>"
                                    required
                                >


                            </div>


                            <!-- PAYMENT AMOUNT -->


                            <div class="payment-form-group">


                                <label
                                    for="payment_amount"
                                >

                                    Payment Amount

                                </label>


                                <div class="amount-wrapper">


                                    <span class="amount-symbol">

                                        ₱

                                    </span>


                                    <input
                                        type="number"
                                        name="amount"
                                        id="payment_amount"
                                        class="amount-input"
                                        min="0.01"
                                        max="<?= htmlspecialchars(
                                            number_format(
                                                $remainingBalance,
                                                2,
                                                '.',
                                                ''
                                            )
                                        ) ?>"
                                        step="0.01"
                                        placeholder="0.00"
                                        required
                                    >


                                </div>


                                <small class="payment-help">

                                    Maximum payment:

                                    ₱<?= number_format(
                                        $remainingBalance,
                                        2
                                    ) ?>

                                </small>


                            </div>


                            <!-- ACCOUNT -->


                            <div class="payment-form-group">


                                <label
                                    for="account_id"
                                >

                                    Receive Payment Into

                                </label>


                                <select
                                    name="account_id"
                                    id="account_id"
                                    required
                                    <?= empty($accounts)
                                        ? 'disabled'
                                        : '' ?>
                                >


                                    <option value="">

                                        Select Account

                                    </option>


                                    <?php foreach (
                                        $accounts
                                        as $account
                                    ): ?>


                                        <option
                                            value="<?= (int)(
                                                $account['id']
                                                ?? 0
                                            ) ?>"
                                        >

                                            <?= htmlspecialchars(
                                                $account[
                                                    'account_name'
                                                ]
                                                ?? 'Account'
                                            ) ?>

                                            —

                                            ₱<?= number_format(
                                                (float)(
                                                    $account[
                                                        'balance'
                                                    ]
                                                    ?? 0
                                                ),
                                                2
                                            ) ?>

                                        </option>


                                    <?php endforeach; ?>


                                </select>


                                <small class="payment-help">

                                    The payment will be added
                                    to the selected account.

                                </small>


                            </div>


                            <!-- NOTES -->


                            <div class="payment-form-group">


                                <label
                                    for="notes"
                                >

                                    Notes

                                </label>


                                <textarea
                                    name="notes"
                                    id="notes"
                                    rows="4"
                                    placeholder="Add payment notes or reference..."
                                ></textarea>


                            </div>


                            <!-- ACTIONS -->


                            <div class="payment-actions">


                                <a
                                    href="index.php?url=loans"
                                    class="payment-btn payment-btn-secondary"
                                >

                                    Cancel

                                </a>


                                <button
                                    type="submit"
                                    class="payment-btn payment-btn-primary"
                                    id="submitPayment"
                                    <?= (
                                        $remainingBalance <= 0
                                        ||
                                        empty($accounts)
                                    )
                                        ? 'disabled'
                                        : '' ?>
                                >

                                    ✓ Record Payment

                                </button>


                            </div>


                        </form>


                    </div>


                </div>


            </div>


        </div>


    </div>


</div>


<script>


/*
|--------------------------------------------------------------------------
| PAYMENT FORM JAVASCRIPT
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function()
    {

        const schedule =
            document.getElementById(
                'schedule_id'
            );


        const amount =
            document.getElementById(
                'payment_amount'
            );


        const form =
            document.getElementById(
                'paymentForm'
            );


        const submitButton =
            document.getElementById(
                'submitPayment'
            );


        if (
            !schedule
            ||
            !amount
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | SELECT PAYMENT SCHEDULE
        |--------------------------------------------------------------------------
        */

        schedule.addEventListener(
            'change',
            function()
            {

                const selected =
                    schedule.options[
                        schedule.selectedIndex
                    ];


                const remaining =
                    selected.dataset.remaining;


                if (remaining) {

                    amount.value =
                        parseFloat(
                            remaining
                        ).toFixed(2);

                }
                else {

                    amount.value = '';

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PREVENT AMOUNT ABOVE BALANCE
        |--------------------------------------------------------------------------
        */

        amount.addEventListener(
            'input',
            function()
            {

                const max =
                    parseFloat(
                        amount.max
                    );


                const value =
                    parseFloat(
                        amount.value
                    );


                if (
                    !isNaN(max)
                    &&
                    !isNaN(value)
                    &&
                    value > max
                ) {

                    amount.value =
                        max.toFixed(2);

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | FORM SUBMISSION
        |--------------------------------------------------------------------------
        */

        if (
            form
            &&
            submitButton
        ) {

            form.addEventListener(
                'submit',
                function(event)
                {

                    const value =
                        parseFloat(
                            amount.value
                        );


                    const max =
                        parseFloat(
                            amount.max
                        );


                    /*
                    |----------------------------------------------------------------------
                    | INVALID AMOUNT
                    |----------------------------------------------------------------------
                    */

                    if (
                        isNaN(value)
                        ||
                        value <= 0
                    ) {

                        event.preventDefault();


                        alert(
                            'Please enter a valid payment amount.'
                        );


                        amount.focus();


                        return;

                    }


                    /*
                    |----------------------------------------------------------------------
                    | ABOVE BALANCE
                    |----------------------------------------------------------------------
                    */

                    if (
                        !isNaN(max)
                        &&
                        value > max
                    ) {

                        event.preventDefault();


                        alert(
                            'Payment amount cannot be greater than the remaining loan balance.'
                        );


                        amount.focus();


                        return;

                    }


                    /*
                    |----------------------------------------------------------------------
                    | PREVENT DOUBLE SUBMISSION
                    |----------------------------------------------------------------------
                    */

                    submitButton.disabled = true;


                    submitButton.innerHTML =
                        'Processing...';

                }
            );

        }

    }
);

</script>


</body>

</html>