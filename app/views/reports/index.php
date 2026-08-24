<?php

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


$totalLoans =
    (int)(
        $loanSummary['total_loans']
        ?? 0
    );


$pendingLoans =
    (int)(
        $loanSummary['pending_loans']
        ?? 0
    );


$approvedLoans =
    (int)(
        $loanSummary['approved_loans']
        ?? 0
    );


$activeLoans =
    (int)(
        $loanSummary['active_loans']
        ?? 0
    );


$overdueLoans =
    (int)(
        $loanSummary['overdue_loans']
        ?? 0
    );


$completedLoans =
    (int)(
        $loanSummary['completed_loans']
        ?? 0
    );


$rejectedLoans =
    (int)(
        $loanSummary['rejected_loans']
        ?? 0
    );


$cancelledLoans =
    (int)(
        $loanSummary['cancelled_loans']
        ?? 0
    );


$totalPrincipal =
    (float)(
        $financialSummary['total_principal']
        ?? 0
    );


$totalInterest =
    (float)(
        $financialSummary['total_interest']
        ?? 0
    );


$totalProcessingFee =
    (float)(
        $financialSummary['total_processing_fee']
        ?? 0
    );


$totalPayable =
    (float)(
        $financialSummary['total_payable']
        ?? 0
    );


$paymentCount =
    (int)(
        $paymentSummary['payment_count']
        ?? 0
    );


$totalCollected =
    (float)(
        $paymentSummary['total_collected']
        ?? 0
    );


$principalCollected =
    (float)(
        $paymentSummary['principal_collected']
        ?? 0
    );


$interestCollected =
    (float)(
        $paymentSummary['interest_collected']
        ?? 0
    );


$penaltyCollected =
    (float)(
        $paymentSummary['penalty_collected']
        ?? 0
    );


$outstandingBalance =
    (float)(
        $outstandingBalance
        ?? 0
    );


$totalBorrowers =
    (int)(
        $borrowerSummary['total_borrowers']
        ?? 0
    );


$activeBorrowers =
    (int)(
        $borrowerSummary['active_borrowers']
        ?? 0
    );


$inactiveBorrowers =
    (int)(
        $borrowerSummary['inactive_borrowers']
        ?? 0
    );

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
        Reports | Loan Management
    </title>


    <!-- ==========================================================
         MAIN STYLE
    =========================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<?php

require APP_PATH .
    '/views/layouts/sidebar.php';

?>


<div class="main-content">


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
             PAGE HEADER
        =========================================================== -->

        <div class="page-header">

            <div>

                <h1>
                    Reports
                </h1>

                <p>

                    View your loan portfolio,
                    collections, payments and
                    financial summary.

                </p>

            </div>

        </div>


        <!-- ==========================================================
             FINANCIAL SUMMARY
        =========================================================== -->

        <div class="dashboard-stats">


            <!-- TOTAL PRINCIPAL -->

            <div class="stat-card">

                <div class="stat-card-content">

                    <div class="stat-card-label">

                        Principal Released

                    </div>


                    <div class="stat-card-value">

                        ₱<?= number_format(
                            $totalPrincipal,
                            2
                        ) ?>

                    </div>

                </div>

            </div>


            <!-- TOTAL PAYABLE -->

            <div class="stat-card">

                <div class="stat-card-content">

                    <div class="stat-card-label">

                        Total Payable

                    </div>


                    <div class="stat-card-value">

                        ₱<?= number_format(
                            $totalPayable,
                            2
                        ) ?>

                    </div>

                </div>

            </div>


            <!-- TOTAL COLLECTED -->

            <div class="stat-card">

                <div class="stat-card-content">

                    <div class="stat-card-label">

                        Total Collected

                    </div>


                    <div class="stat-card-value">

                        ₱<?= number_format(
                            $totalCollected,
                            2
                        ) ?>

                    </div>

                </div>

            </div>


            <!-- OUTSTANDING -->

            <div class="stat-card">

                <div class="stat-card-content">

                    <div class="stat-card-label">

                        Outstanding Balance

                    </div>


                    <div class="stat-card-value">

                        ₱<?= number_format(
                            $outstandingBalance,
                            2
                        ) ?>

                    </div>

                </div>

            </div>


        </div>


        <!-- ==========================================================
             LOAN PORTFOLIO
        =========================================================== -->

        <div class="card">

            <div class="card-header">

                <div>

                    <h2 class="card-title">

                        Loan Portfolio

                    </h2>


                    <p class="card-subtitle">

                        Current status of all loans.

                    </p>

                </div>

            </div>


            <div class="card-body">


                <div class="dashboard-stats">


                    <!-- TOTAL -->

                    <div class="stat-card">

                        <div class="stat-card-content">

                            <div class="stat-card-label">

                                Total Loans

                            </div>


                            <div class="stat-card-value">

                                <?= number_format(
                                    $totalLoans
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <!-- PENDING -->

                    <div class="stat-card">

                        <div class="stat-card-content">

                            <div class="stat-card-label">

                                Pending

                            </div>


                            <div class="stat-card-value">

                                <?= number_format(
                                    $pendingLoans
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <!-- ACTIVE -->

                    <div class="stat-card">

                        <div class="stat-card-content">

                            <div class="stat-card-label">

                                Active

                            </div>


                            <div class="stat-card-value">

                                <?= number_format(
                                    $activeLoans
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <!-- OVERDUE -->

                    <div class="stat-card">

                        <div class="stat-card-content">

                            <div class="stat-card-label">

                                Overdue

                            </div>


                            <div class="stat-card-value">

                                <?= number_format(
                                    $overdueLoans
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <!-- COMPLETED -->

                    <div class="stat-card">

                        <div class="stat-card-content">

                            <div class="stat-card-label">

                                Completed

                            </div>


                            <div class="stat-card-value">

                                <?= number_format(
                                    $completedLoans
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <!-- REJECTED -->

                    <div class="stat-card">

                        <div class="stat-card-content">

                            <div class="stat-card-label">

                                Rejected

                            </div>


                            <div class="stat-card-value">

                                <?= number_format(
                                    $rejectedLoans
                                ) ?>

                            </div>

                        </div>

                    </div>


                </div>

            </div>

        </div>


        <br>


        <!-- ==========================================================
             COLLECTION SUMMARY
        =========================================================== -->

        <div class="card">

            <div class="card-header">

                <div>

                    <h2 class="card-title">

                        Collection Summary

                    </h2>


                    <p class="card-subtitle">

                        Breakdown of recorded payments.

                    </p>

                </div>

            </div>


            <div class="card-body">


                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Description
                                </th>

                                <th>
                                    Amount
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <tr>

                                <td>
                                    Number of Payments
                                </td>

                                <td>

                                    <strong>

                                        <?= number_format(
                                            $paymentCount
                                        ) ?>

                                    </strong>

                                </td>

                            </tr>


                            <tr>

                                <td>
                                    Principal Collected
                                </td>

                                <td>

                                    ₱<?= number_format(
                                        $principalCollected,
                                        2
                                    ) ?>

                                </td>

                            </tr>


                            <tr>

                                <td>
                                    Interest Collected
                                </td>

                                <td>

                                    ₱<?= number_format(
                                        $interestCollected,
                                        2
                                    ) ?>

                                </td>

                            </tr>


                            <tr>

                                <td>
                                    Penalties Collected
                                </td>

                                <td>

                                    ₱<?= number_format(
                                        $penaltyCollected,
                                        2
                                    ) ?>

                                </td>

                            </tr>


                            <tr>

                                <td>

                                    <strong>
                                        Total Collected
                                    </strong>

                                </td>

                                <td>

                                    <strong>

                                        ₱<?= number_format(
                                            $totalCollected,
                                            2
                                        ) ?>

                                    </strong>

                                </td>

                            </tr>


                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <br>


        <!-- ==========================================================
             BORROWER SUMMARY
        =========================================================== -->

        <div class="card">

            <div class="card-header">

                <div>

                    <h2 class="card-title">

                        Borrower Summary

                    </h2>


                    <p class="card-subtitle">

                        Borrower statistics for this business.

                    </p>

                </div>

            </div>


            <div class="card-body">


                <div class="dashboard-stats">


                    <div class="stat-card">

                        <div class="stat-card-content">

                            <div class="stat-card-label">

                                Total Borrowers

                            </div>


                            <div class="stat-card-value">

                                <?= number_format(
                                    $totalBorrowers
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <div class="stat-card">

                        <div class="stat-card-content">

                            <div class="stat-card-label">

                                Active Borrowers

                            </div>


                            <div class="stat-card-value">

                                <?= number_format(
                                    $activeBorrowers
                                ) ?>

                            </div>

                        </div>

                    </div>


                    <div class="stat-card">

                        <div class="stat-card-content">

                            <div class="stat-card-label">

                                Inactive Borrowers

                            </div>


                            <div class="stat-card-value">

                                <?= number_format(
                                    $inactiveBorrowers
                                ) ?>

                            </div>

                        </div>

                    </div>


                </div>

            </div>

        </div>


        <br>


        <!-- ==========================================================
             RECENT PAYMENTS
        =========================================================== -->

        <div class="card">

            <div class="card-header">

                <div>

                    <h2 class="card-title">

                        Recent Payments

                    </h2>


                    <p class="card-subtitle">

                        Latest recorded loan payments.

                    </p>

                </div>


                <div>

                    <a
                        href="index.php?url=payments/index"
                        class="btn btn-primary"
                    >

                        View All Payments

                    </a>

                </div>

            </div>


            <div class="card-body">


                <?php if (empty($recentPayments)): ?>


                    <div class="empty-state">

                        <h3>
                            No Payments Found
                        </h3>


                        <p>

                            There are currently
                            no recorded payments.

                        </p>

                    </div>


                <?php else: ?>


                    <div class="table-container">

                        <table>

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


                                    <td>

                                        <strong>

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

                                        </strong>

                                    </td>


                                    <td>

                                        <span
                                            class="status status-<?= htmlspecialchars(
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


                                </tr>


                            <?php endforeach; ?>


                            </tbody>

                        </table>

                    </div>


                <?php endif; ?>


            </div>

        </div>


        <br>


        <!-- ==========================================================
             RECENT LOANS
        =========================================================== -->

        <div class="card">

            <div class="card-header">

                <div>

                    <h2 class="card-title">

                        Recent Loans

                    </h2>


                    <p class="card-subtitle">

                        Latest loans created in the system.

                    </p>

                </div>

            </div>


            <div class="card-body">


                <?php if (empty($recentLoans)): ?>


                    <div class="empty-state">

                        <h3>
                            No Loans Found
                        </h3>


                        <p>

                            There are currently
                            no loans recorded.

                        </p>

                    </div>


                <?php else: ?>


                    <div class="table-container">

                        <table>

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
                                                ?? 0
                                            ) ?>"
                                            class="table-link"
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

                                        ₱<?= number_format(
                                            (float)(
                                                $loan[
                                                    'principal_amount'
                                                ]
                                                ??
                                                0
                                            ),
                                            2
                                        ) ?>

                                    </td>


                                    <td>

                                        ₱<?= number_format(
                                            (float)(
                                                $loan[
                                                    'total_payable'
                                                ]
                                                ??
                                                0
                                            ),
                                            2
                                        ) ?>

                                    </td>


                                    <td>

                                        <span
                                            class="status status-<?= htmlspecialchars(
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


                                    <td>

                                        <?php

                                        if (
                                            !empty(
                                                $loan[
                                                    'created_at'
                                                ]
                                            )
                                        ) {

                                            echo htmlspecialchars(
                                                date(
                                                    'M d, Y',
                                                    strtotime(
                                                        $loan[
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

</div>


</body>

</html>