<?php

$user =
    $user ?? Auth::user();

$business =
    $business ?? Auth::business();

$tenantRole =
    $tenantRole ?? Auth::tenantRole();

$currentUrl =
    $currentUrl ?? 'borrowers';


$fullName =
    trim(
        ($borrower['first_name'] ?? '')
        . ' '
        . ($borrower['middle_name'] ?? '')
        . ' '
        . ($borrower['last_name'] ?? '')
    );


$status =
    $borrower['status']
    ?? 'active';


$statusClass =
    'status-' . $status;

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
        Borrower Details | Loan Management
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        .details-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 20px;

        }


        .details-card {

            background: #fff;

            border-radius: 10px;

            padding: 24px;

            box-shadow:
                0 2px 10px
                rgba(
                    0,
                    0,
                    0,
                    0.05
                );

            margin-bottom: 20px;

        }


        .details-card h2 {

            margin-top: 0;

            margin-bottom: 20px;

        }


        .details-row {

            display: grid;

            grid-template-columns:
                180px
                1fr;

            padding: 10px 0;

            border-bottom:
                1px solid #eee;

        }


        .details-row:last-child {

            border-bottom: none;

        }


        .details-label {

            font-weight: 600;

            color: #666;

        }


        .details-value {

            color: #222;

        }


        .summary-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    1fr
                );

            gap: 15px;

            margin-bottom: 20px;

        }


        .summary-card {

            background: #fff;

            border-radius: 10px;

            padding: 20px;

            box-shadow:
                0 2px 10px
                rgba(
                    0,
                    0,
                    0,
                    0.05
                );

        }


        .summary-title {

            color: #777;

            font-size: 13px;

            margin-bottom: 8px;

        }


        .summary-value {

            font-size: 22px;

            font-weight: 700;

        }


        .loan-table {

            width: 100%;

            border-collapse: collapse;

        }


        .loan-table th,
        .loan-table td {

            padding: 12px;

            border-bottom:
                1px solid #eee;

            text-align: left;

            white-space: nowrap;

        }


        .loan-table th {

            background: #f7f7f7;

            font-size: 13px;

        }


        .loan-table-wrapper {

            overflow-x: auto;

        }


        .loan-number {

            font-weight: 600;

        }


        .empty-loans {

            padding: 40px;

            text-align: center;

            color: #777;

        }


        @media (
            max-width: 900px
        ) {

            .details-grid {

                grid-template-columns: 1fr;

            }


            .summary-grid {

                grid-template-columns:
                    repeat(
                        2,
                        1fr
                    );

            }

        }


        @media (
            max-width: 600px
        ) {

            .summary-grid {

                grid-template-columns: 1fr;

            }


            .details-row {

                grid-template-columns: 1fr;

                gap: 5px;

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


    <!-- NAVBAR -->

    <nav class="navbar">

        <div class="page-title">

            Borrower Details

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


    <div class="container">


        <!-- HEADER -->

        <div class="page-header">

            <div>

                <h1>

                    <?= htmlspecialchars(
                        $fullName
                    ) ?>

                </h1>


                <p>

                    Borrower Code:

                    <strong>

                        <?= htmlspecialchars(
                            $borrower['borrower_code']
                            ?? '-'
                        ) ?>

                    </strong>

                </p>

            </div>


            <div>

                <a
                    href="index.php?url=borrowers"
                    class="btn btn-secondary"
                >

                    ← Back

                </a>


                <a
                    href="index.php?url=borrowers/edit&id=<?= (int)$borrower['id'] ?>"
                    class="btn btn-primary"
                >

                    ✏️ Edit Borrower

                </a>

            </div>

        </div>


        <!-- SUMMARY -->

        <div class="summary-grid">


            <div class="summary-card">

                <div class="summary-title">

                    Total Loans

                </div>

                <div class="summary-value">

                    <?= number_format(
                        $totalLoans
                    ) ?>

                </div>

            </div>


            <div class="summary-card">

                <div class="summary-title">

                    Total Principal

                </div>

                <div class="summary-value">

                    ₱<?= number_format(
                        $totalPrincipal,
                        2
                    ) ?>

                </div>

            </div>


            <div class="summary-card">

                <div class="summary-title">

                    Total Paid

                </div>

                <div class="summary-value">

                    ₱<?= number_format(
                        $totalPaid,
                        2
                    ) ?>

                </div>

            </div>


            <div class="summary-card">

                <div class="summary-title">

                    Remaining Balance

                </div>

                <div class="summary-value">

                    ₱<?= number_format(
                        $remainingBalance,
                        2
                    ) ?>

                </div>

            </div>


        </div>


        <!-- BORROWER INFORMATION -->

        <div class="details-card">

            <h2>

                Borrower Information

            </h2>


            <div class="details-grid">


                <div>


                    <div class="details-row">

                        <div class="details-label">
                            Borrower Code
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['borrower_code']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            First Name
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['first_name']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Middle Name
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['middle_name']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Last Name
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['last_name']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Email
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['email']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Phone
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['phone']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Date of Birth
                        </div>

                        <div class="details-value">

                            <?= !empty(
                                $borrower['date_of_birth']
                            )
                                ? htmlspecialchars(
                                    $borrower['date_of_birth']
                                )
                                : '-'
                            ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Gender
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                ucfirst(
                                    $borrower['gender']
                                    ?? '-'
                                )
                            ) ?>

                        </div>

                    </div>


                </div>


                <div>


                    <div class="details-row">

                        <div class="details-label">
                            Address
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['address']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            City
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['city']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Province
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['province']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Postal Code
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['postal_code']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Occupation
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['occupation']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Employer
                        </div>

                        <div class="details-value">

                            <?= htmlspecialchars(
                                $borrower['employer']
                                ?? '-'
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Monthly Income
                        </div>

                        <div class="details-value">

                            ₱<?= number_format(
                                (float)(
                                    $borrower['monthly_income']
                                    ?? 0
                                ),
                                2
                            ) ?>

                        </div>

                    </div>


                    <div class="details-row">

                        <div class="details-label">
                            Status
                        </div>

                        <div class="details-value">

                            <span
                                class="status
                                    <?= htmlspecialchars(
                                        $statusClass
                                    ) ?>"
                            >

                                <?= htmlspecialchars(
                                    ucfirst(
                                        $status
                                    )
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

                <div
                    class="details-row"
                    style="margin-top:20px;"
                >

                    <div class="details-label">

                        Notes

                    </div>

                    <div class="details-value">

                        <?= nl2br(
                            htmlspecialchars(
                                $borrower['notes']
                            )
                        ) ?>

                    </div>

                </div>

            <?php endif; ?>


        </div>


        <!-- LOAN SUMMARY -->

        <div class="details-card">

            <h2>

                Loan Summary

            </h2>


            <div class="details-grid">


                <div class="details-row">

                    <div class="details-label">
                        Active Loans
                    </div>

                    <div class="details-value">

                        <?= number_format(
                            $activeLoans
                        ) ?>

                    </div>

                </div>


                <div class="details-row">

                    <div class="details-label">
                        Pending Loans
                    </div>

                    <div class="details-value">

                        <?= number_format(
                            $pendingLoans
                        ) ?>

                    </div>

                </div>


                <div class="details-row">

                    <div class="details-label">
                        Completed Loans
                    </div>

                    <div class="details-value">

                        <?= number_format(
                            $completedLoans
                        ) ?>

                    </div>

                </div>


                <div class="details-row">

                    <div class="details-label">
                        Overdue Loans
                    </div>

                    <div class="details-value">

                        <?= number_format(
                            $overdueLoans
                        ) ?>

                    </div>

                </div>


                <div class="details-row">

                    <div class="details-label">
                        Total Payable
                    </div>

                    <div class="details-value">

                        ₱<?= number_format(
                            $totalPayable,
                            2
                        ) ?>

                    </div>

                </div>


                <div class="details-row">

                    <div class="details-label">
                        Total Paid
                    </div>

                    <div class="details-value">

                        ₱<?= number_format(
                            $totalPaid,
                            2
                        ) ?>

                    </div>

                </div>


            </div>

        </div>


        <!-- LOANS -->

        <div class="details-card">

            <h2>

                All Loans

            </h2>


            <?php if (empty($loans)): ?>


                <div class="empty-loans">

                    <h3>
                        No Loans Found
                    </h3>

                    <p>
                        This borrower does not have any loans yet.
                    </p>


                    <a
                        href="index.php?url=loans"
                        class="btn btn-primary"
                    >

                        Go to Loans

                    </a>

                </div>


            <?php else: ?>


                <div class="loan-table-wrapper">

                    <table class="loan-table">

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

                            $loanPayable =
                                (float)(
                                    $loan['total_payable']
                                    ?? 0
                                );


                            $loanPaid =
                                (float)(
                                    $loan['total_paid']
                                    ?? 0
                                );


                            $loanBalance =
                                $loanPayable
                                - $loanPaid;


                            if (
                                $loanBalance < 0
                            ) {

                                $loanBalance = 0;
                            }


                            $loanStatus =
                                $loan['status']
                                ?? 'pending';

                            ?>


                            <tr>


                                <td>

                                    <span
                                        class="loan-number"
                                    >

                                        <?= htmlspecialchars(
                                            $loan['loan_number']
                                            ?? '-'
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $loan['category_name']
                                        ?? '-'
                                    ) ?>

                                </td>


                                <td>

                                    ₱<?= number_format(
                                        (float)(
                                            $loan['principal_amount']
                                            ?? 0
                                        ),
                                        2
                                    ) ?>

                                </td>


                                <td>

                                    ₱<?= number_format(
                                        (float)(
                                            $loan['total_interest']
                                            ?? 0
                                        ),
                                        2
                                    ) ?>

                                </td>


                                <td>

                                    ₱<?= number_format(
                                        $loanPayable,
                                        2
                                    ) ?>

                                </td>


                                <td>

                                    ₱<?= number_format(
                                        $loanPaid,
                                        2
                                    ) ?>

                                </td>


                                <td>

                                    ₱<?= number_format(
                                        $loanBalance,
                                        2
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $loan['payment_type']
                                                ?? '-'
                                            )
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $loan['term']
                                        ?? '-'
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $loan['term_period']
                                        ?? ''
                                    ) ?>

                                </td>


                                <td>

                                    <?= !empty(
                                        $loan['release_date']
                                    )
                                        ? htmlspecialchars(
                                            $loan['release_date']
                                        )
                                        : '-'
                                    ?>

                                </td>


                                <td>

                                    <span
                                        class="status
                                            status-<?= htmlspecialchars(
                                                $loanStatus
                                            ) ?>"
                                    >

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $loanStatus
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <a
                                        href="index.php?url=loans/show&id=<?= (int)$loan['id'] ?>"
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