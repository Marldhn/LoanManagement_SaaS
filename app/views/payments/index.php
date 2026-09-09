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

$currentUrl =
    $currentUrl
    ?? 'payments/index';

$payments =
    $payments
    ?? [];

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
        Payments | Loan Management
    </title>


    <!-- ==========================================================
         MAIN STYLESHEET
    =========================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Public+Sans:wght@400;500;600;700;800&display=swap"
    >


    <!-- ==========================================================
         PAYMENT PAGE STYLES
    =========================================================== -->

    <style>

        /*
        |--------------------------------------------------------------------------
        | TOKENS — shares the sidebar / dashboard / borrowers / loans language
        |--------------------------------------------------------------------------
        */

        :root {

            --lm-ink-900: #16211D;
            --lm-ink-700: #33413B;
            --lm-ink-500: #6B7670;
            --lm-ink-300: #9CA69F;
            --lm-line: #E7E2D6;
            --lm-line-soft: #F0EDE4;
            --lm-surface: #FFFFFF;
            --lm-surface-tint: #FAF8F2;
            --lm-brass: #B8860F;
            --lm-brass-ink: #8A6608;
            --lm-brass-soft: #F7EFD9;
            --lm-forest: #1F7A52;
            --lm-forest-soft: #E7F3EC;
            --lm-danger: #B0392E;
            --lm-danger-soft: #FBEBE8;
            --lm-info: #2E5C8A;
            --lm-info-soft: #E9F0F7;
            --lm-font-serif: 'Fraunces', Georgia, 'Iowan Old Style', serif;
            --lm-font-sans: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

        }

        .container {
            font-family: var(--lm-font-sans);
        }

        .page-header h1,
        .page-header h2 {
            font-family: var(--lm-font-serif);
            font-weight: 600;
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT SUMMARY
        |--------------------------------------------------------------------------
        */

        .payment-summary {
            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 16px;

            margin-bottom: 25px;
        }


        .payment-summary-card {
            background: var(--lm-surface);

            border: 1px solid var(--lm-line);

            border-top: 3px solid var(--lm-ink-300);

            border-radius: 12px;

            padding: 20px;

            box-sizing: border-box;
        }


        .payment-summary-card:nth-child(2) {
            border-top-color: var(--lm-forest);
        }


        .payment-summary-card:nth-child(3) {
            border-top-color: var(--lm-brass);
        }


        .payment-summary-label {
            font-size: 12px;

            font-weight: 600;

            color: var(--lm-ink-500);

            margin-bottom: 8px;
        }


        .payment-summary-value {
            font-size: 24px;

            font-weight: 700;

            color: var(--lm-ink-900);

            letter-spacing: -.01em;
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT NUMBER
        |--------------------------------------------------------------------------
        */

        .payment-number {
            font-weight: 600;

            color: var(--lm-ink-900);
        }


        /*
        |--------------------------------------------------------------------------
        | LOAN LINK
        |--------------------------------------------------------------------------
        */

        .payment-loan-link {
            color: var(--lm-info);

            text-decoration: none;

            font-weight: 600;
        }


        .payment-loan-link:hover {
            text-decoration: underline;
        }


        /*
        |--------------------------------------------------------------------------
        | AMOUNT
        |--------------------------------------------------------------------------
        */

        .payment-amount {
            font-weight: 700;

            color: var(--lm-ink-900);

            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .payment-empty-state {
            text-align: center;

            padding: 60px 20px;
        }


        .payment-empty-icon {
            font-size: 42px;

            margin-bottom: 15px;
        }


        .payment-empty-state h3 {
            margin-bottom: 8px;

            color: var(--lm-ink-900);
        }


        .payment-empty-state p {
            color: var(--lm-ink-300);

            margin: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT STATUS
        |--------------------------------------------------------------------------
        */

        .payment-status {
            display: inline-flex;

            align-items: center;

            padding: 5px 10px;

            border-radius: 999px;

            font-size: 12px;

            font-weight: 600;

            text-transform: capitalize;
        }


        .payment-status-posted {
            background: var(--lm-forest-soft);

            color: var(--lm-forest);
        }


        .payment-status-pending {
            background: var(--lm-brass-soft);

            color: var(--lm-brass-ink);
        }


        .payment-status-voided,
        .payment-status-cancelled {
            background: var(--lm-danger-soft);

            color: var(--lm-danger);
        }


        .payment-status-default {
            background: var(--lm-line-soft);

            color: var(--lm-ink-500);
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        .payments-table {
            width: 100%;

            min-width: 1200px;

            border-collapse: collapse;
        }


        .payments-table th {
            background: var(--lm-surface-tint);

            color: var(--lm-ink-500);

            font-weight: 700;

            letter-spacing: .02em;
        }


        .payments-table th,
        .payments-table td {
            white-space: nowrap;
        }


        .payments-table td {
            color: var(--lm-ink-700);

            border-top: 1px solid var(--lm-line-soft);
        }


        .payments-table tbody tr {
            transition: background .15s ease;
        }


        .payments-table tbody tr:hover {
            background: var(--lm-surface-tint);
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1000px) {

            .payment-summary {

                grid-template-columns:
                    1fr;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | REDUCED MOTION
        |--------------------------------------------------------------------------
        */

        @media (prefers-reduced-motion: reduce) {

            .payments-table tbody tr {
                transition: none;
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


    <!-- ==========================================================
         NAVBAR
    =========================================================== -->

    <nav class="navbar">


        <div class="page-title">

            Payments

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
                    ?? 'User'
                ) ?>

            </span>


        </div>


    </nav>



    <!-- ==========================================================
         PAGE CONTAINER
    =========================================================== -->

    <div class="container">


        <!-- ======================================================
             PAGE HEADER
        ======================================================= -->

        <div class="page-header">


            <div>

                <h1>
                    Payments
                </h1>


                <p>

                    View and manage all loan payments for

                    <?= htmlspecialchars(
                        $business['name']
                        ??
                        'your business'
                    ) ?>.

                </p>

            </div>


        </div>



        <!-- ======================================================
             FLASH MESSAGES
        ======================================================= -->

        <?php if (
            !empty(
                $_SESSION['payment_success']
            )
        ): ?>


            <div class="alert alert-success">

                <?= htmlspecialchars(
                    $_SESSION['payment_success']
                ) ?>

            </div>


            <?php

            unset(
                $_SESSION['payment_success']
            );

            ?>


        <?php endif; ?>



        <?php if (
            !empty(
                $_SESSION['payment_error']
            )
        ): ?>


            <div class="alert alert-danger">

                <?= htmlspecialchars(
                    $_SESSION['payment_error']
                ) ?>

            </div>


            <?php

            unset(
                $_SESSION['payment_error']
            );

            ?>


        <?php endif; ?>



        <!-- ======================================================
             PAYMENT TOTALS
        ======================================================= -->

        <?php

        $totalPayments =
            count(
                $payments
            );


        $totalAmount =
            0;


        $totalPrincipal =
            0;


        $totalInterest =
            0;


        $totalPenalty =
            0;


        foreach (
            $payments
            as $payment
        ) {

            if (
                ($payment['status']
                    ?? 'posted')
                === 'posted'
            ) {

                $totalAmount +=
                    (float)(
                        $payment['amount']
                        ?? 0
                    );


                $totalPrincipal +=
                    (float)(
                        $payment[
                            'principal_amount'
                        ]
                        ?? 0
                    );


                $totalInterest +=
                    (float)(
                        $payment[
                            'interest_amount'
                        ]
                        ?? 0
                    );


                $totalPenalty +=
                    (float)(
                        $payment[
                            'penalty_amount'
                        ]
                        ?? 0
                    );

            }

        }

        ?>



        <!-- ======================================================
             PAYMENT SUMMARY
        ======================================================= -->

        <div class="payment-summary">


            <!-- TOTAL PAYMENTS -->

            <div class="payment-summary-card">

                <div class="payment-summary-label">

                    Total Payments

                </div>


                <div class="payment-summary-value">

                    <?= number_format(
                        $totalPayments
                    ) ?>

                </div>

            </div>



            <!-- TOTAL COLLECTED -->

            <div class="payment-summary-card">

                <div class="payment-summary-label">

                    Total Collected

                </div>


                <div class="payment-summary-value">

                    ₱<?= number_format(
                        $totalAmount,
                        2
                    ) ?>

                </div>

            </div>



            <!-- TOTAL PRINCIPAL -->

            <div class="payment-summary-card">

                <div class="payment-summary-label">

                    Principal Collected

                </div>


                <div class="payment-summary-value">

                    ₱<?= number_format(
                        $totalPrincipal,
                        2
                    ) ?>

                </div>

            </div>


        </div>



        <!-- ======================================================
             PAYMENT HISTORY
        ======================================================= -->

        <div class="form-card">


            <!-- ==================================================
                 CARD HEADER
            =================================================== -->

            <div class="page-header">


                <div>

                    <h2>

                        Payment History

                    </h2>


                    <p>

                        All recorded loan payments.

                    </p>

                </div>


            </div>



            <!-- ==================================================
                 NO PAYMENTS
            =================================================== -->

            <?php if (
                empty(
                    $payments
                )
            ): ?>


                <div class="payment-empty-state">


                    <div class="payment-empty-icon">

                        💳

                    </div>


                    <h3>

                        No Payments Found

                    </h3>


                    <p>

                        There are currently no recorded
                        loan payments.

                    </p>


                </div>


            <?php else: ?>



                <!-- ==================================================
                     PAYMENT TABLE
                =================================================== -->

                <div class="table-container">


                    <table class="payments-table">


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
                                    Payment Date
                                </th>


                                <th>
                                    Amount
                                </th>


                                <th>
                                    Principal
                                </th>


                                <th>
                                    Interest
                                </th>


                                <th>
                                    Penalty
                                </th>


                                <th>
                                    Account
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
                            $payments
                            as $payment
                        ): ?>


                            <?php

                            $status =
                                $payment['status']
                                ??
                                'posted';


                            $statusClass =
                                match (
                                    $status
                                ) {

                                    'posted' =>
                                        'payment-status-posted',

                                    'pending' =>
                                        'payment-status-pending',

                                    'voided' =>
                                        'payment-status-voided',

                                    'cancelled' =>
                                        'payment-status-cancelled',

                                    default =>
                                        'payment-status-default'

                                };

                            ?>


                            <tr>


                                <!-- ==================================
                                     PAYMENT NUMBER
                                =================================== -->

                                <td>

                                    <span
                                        class="payment-number"
                                    >

                                        <?= htmlspecialchars(
                                            $payment[
                                                'payment_number'
                                            ]
                                            ??
                                            '—'
                                        ) ?>

                                    </span>

                                </td>



                                <!-- ==================================
                                     LOAN NUMBER
                                =================================== -->

                                <td>


                                    <?php if (
                                        !empty(
                                            $payment['loan_id']
                                        )
                                    ): ?>


                                        <a
                                            href="index.php?url=loans/show&id=<?= (int)$payment['loan_id'] ?>"
                                            class="payment-loan-link"
                                        >

                                            <?= htmlspecialchars(
                                                $payment[
                                                    'loan_number'
                                                ]
                                                ??
                                                (
                                                    '#'
                                                    .
                                                    $payment[
                                                        'loan_id'
                                                    ]
                                                )
                                            ) ?>

                                        </a>


                                    <?php else: ?>


                                        —


                                    <?php endif; ?>


                                </td>



                                <!-- ==================================
                                     BORROWER
                                =================================== -->

                                <td>

                                    <?= htmlspecialchars(
                                        $payment[
                                            'borrower_name'
                                        ]
                                        ??
                                        '—'
                                    ) ?>

                                </td>



                                <!-- ==================================
                                     PAYMENT DATE
                                =================================== -->

                                <td>


                                    <?php

                                    if (
                                        !empty(
                                            $payment[
                                                'payment_date'
                                            ]
                                        )
                                    ) {

                                        echo htmlspecialchars(
                                            date(
                                                'M d, Y',
                                                strtotime(
                                                    $payment[
                                                        'payment_date'
                                                    ]
                                                )
                                            )
                                        );

                                    } else {

                                        echo '—';

                                    }

                                    ?>


                                </td>



                                <!-- ==================================
                                     AMOUNT
                                =================================== -->

                                <td>

                                    <span
                                        class="payment-amount"
                                    >

                                        ₱<?= number_format(
                                            (float)(
                                                $payment[
                                                    'amount'
                                                ]
                                                ??
                                                0
                                            ),
                                            2
                                        ) ?>

                                    </span>

                                </td>



                                <!-- ==================================
                                     PRINCIPAL
                                =================================== -->

                                <td>

                                    ₱<?= number_format(
                                        (float)(
                                            $payment[
                                                'principal_amount'
                                            ]
                                            ??
                                            0
                                        ),
                                        2
                                    ) ?>

                                </td>



                                <!-- ==================================
                                     INTEREST
                                =================================== -->

                                <td>

                                    ₱<?= number_format(
                                        (float)(
                                            $payment[
                                                'interest_amount'
                                            ]
                                            ??
                                            0
                                        ),
                                        2
                                    ) ?>

                                </td>



                                <!-- ==================================
                                     PENALTY
                                =================================== -->

                                <td>

                                    ₱<?= number_format(
                                        (float)(
                                            $payment[
                                                'penalty_amount'
                                            ]
                                            ??
                                            0
                                        ),
                                        2
                                    ) ?>

                                </td>



                                <!-- ==================================
                                     ACCOUNT
                                =================================== -->

                                <td>

                                    <?= htmlspecialchars(
                                        $payment[
                                            'account_name'
                                        ]
                                        ??
                                        '—'
                                    ) ?>

                                </td>



                                <!-- ==================================
                                     STATUS
                                =================================== -->

                                <td>


                                    <span
                                        class="payment-status <?= $statusClass ?>"
                                    >

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $status
                                            )
                                        ) ?>

                                    </span>


                                </td>



                                <!-- ==================================
                                     CREATED
                                =================================== -->

                                <td>


                                    <?php

                                    if (
                                        !empty(
                                            $payment[
                                                'created_at'
                                            ]
                                        )
                                    ) {

                                        echo htmlspecialchars(
                                            date(
                                                'M d, Y h:i A',
                                                strtotime(
                                                    $payment[
                                                        'created_at'
                                                    ]
                                                )
                                            )
                                        );

                                    } else {

                                        echo '—';

                                    }

                                    ?>


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