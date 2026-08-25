<?php

$user =
    $user ?? Auth::user();

$business =
    $business ?? Auth::business();

$tenantRole =
    $tenantRole ?? Auth::tenantRole();

$currentUrl =
    $currentUrl ?? 'collections';


$todaySummary =
    $todaySummary ?? [];

$monthSummary =
    $monthSummary ?? [];

$overdueSummary =
    $overdueSummary ?? [];

$recentPayments =
    $recentPayments ?? [];

$todayCollections =
    $todayCollections ?? [];


$todayExpected =
    (float)(
        $todaySummary['expected']
        ?? 0
    );


$todayCollected =
    (float)(
        $todaySummary['collected']
        ?? 0
    );


$todayRemaining =
    (float)(
        $todaySummary['remaining']
        ?? 0
    );


$monthCollected =
    (float)(
        $monthSummary['collected']
        ?? 0
    );


$overdueCount =
    (int)(
        $overdueSummary['count']
        ?? 0
    );


$overdueAmount =
    (float)(
        $overdueSummary['amount']
        ?? 0
    );


$collectionRate = 0;

if ($todayExpected > 0) {

    $collectionRate =
        min(
            100,
            (
                $todayCollected
                /
                $todayExpected
            ) * 100
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
        Collections | Loan Management
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        .collection-page {
            padding-bottom: 40px;
        }


        .collection-stats {

            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 18px;

            margin-bottom: 24px;

        }


        .collection-stat {

            background: #fff;

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            padding: 20px;

            box-shadow:
                0 2px 8px
                rgba(
                    0,
                    0,
                    0,
                    0.04
                );

        }


        .collection-stat-label {

            color: #64748b;

            font-size: 13px;

            margin-bottom: 8px;

        }


        .collection-stat-value {

            font-size: 25px;

            font-weight: 700;

            color: #111827;

        }


        .collection-stat-description {

            margin-top: 6px;

            color: #94a3b8;

            font-size: 12px;

        }


        .collection-grid {

            display: grid;

            grid-template-columns:
                minmax(
                    0,
                    1fr
                )
                380px;

            gap: 20px;

            align-items: start;

        }


        .collection-card {

            background: #fff;

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 2px 8px
                rgba(
                    0,
                    0,
                    0,
                    0.04
                );

            margin-bottom: 20px;

        }


        .collection-card-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            padding: 18px 20px;

            border-bottom:
                1px solid #e5e7eb;

        }


        .collection-card-header h2 {

            margin: 0;

            font-size: 17px;

        }


        .collection-card-header p {

            margin: 4px 0 0;

            color: #64748b;

            font-size: 12px;

        }


        .collection-card-body {

            padding: 20px;

        }


        .collection-progress {

            margin-top: 15px;

        }


        .collection-progress-header {

            display: flex;

            justify-content: space-between;

            margin-bottom: 7px;

            font-size: 12px;

            color: #64748b;

        }


        .collection-progress-bar {

            height: 9px;

            background: #e5e7eb;

            border-radius: 999px;

            overflow: hidden;

        }


        .collection-progress-fill {

            height: 100%;

            background: #2563eb;

            border-radius: 999px;

        }


        .collection-actions {

            display: grid;

            grid-template-columns:
                1fr
                1fr;

            gap: 10px;

        }


        .collection-action {

            display: block;

            padding: 14px;

            border: 1px solid #e5e7eb;

            border-radius: 9px;

            text-decoration: none;

            color: #1f2937;

            transition: 0.2s ease;

        }


        .collection-action:hover {

            background: #f8fafc;

            border-color: #cbd5e1;

        }


        .collection-action-title {

            font-weight: 600;

            font-size: 14px;

        }


        .collection-action-text {

            margin-top: 4px;

            font-size: 12px;

            color: #64748b;

        }


        .collection-table-wrapper {

            overflow-x: auto;

        }


        .collection-table {

            width: 100%;

            border-collapse: collapse;

        }


        .collection-table th,
        .collection-table td {

            padding: 12px 16px;

            border-bottom:
                1px solid #f1f5f9;

            text-align: left;

            white-space: nowrap;

        }


        .collection-table th {

            background: #f8fafc;

            color: #64748b;

            font-size: 12px;

        }


        .collection-table td {

            font-size: 13px;

        }


        .collection-empty {

            padding: 35px;

            text-align: center;

            color: #64748b;

        }


        .collection-danger {

            color: #dc2626;

            font-weight: 600;

        }


        .collection-success {

            color: #16a34a;

            font-weight: 600;

        }


        @media (max-width: 1100px) {

            .collection-stats {

                grid-template-columns:
                    repeat(
                        2,
                        1fr
                    );

            }


            .collection-grid {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 600px) {

            .collection-stats {

                grid-template-columns: 1fr;

            }


            .collection-actions {

                grid-template-columns: 1fr;

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

            Collections

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


    <div class="container collection-page">


        <!-- HEADER -->

        <div class="page-header">

            <div>

                <h1>
                    Collection Dashboard
                </h1>

                <p>

                    Monitor today's collections,
                    payments, and overdue accounts.

                </p>

            </div>


            <div>

                <a
                    href="index.php?url=loans/payment"
                    class="btn btn-primary"
                >

                    + Record Payment

                </a>

            </div>

        </div>


        <!-- STATISTICS -->

        <div class="collection-stats">


            <div class="collection-stat">

                <div class="collection-stat-label">

                    Today's Expected

                </div>

                <div class="collection-stat-value">

                    ₱<?= number_format(
                        $todayExpected,
                        2
                    ) ?>

                </div>

                <div class="collection-stat-description">

                    Amount scheduled for collection today

                </div>

            </div>


            <div class="collection-stat">

                <div class="collection-stat-label">

                    Collected Today

                </div>

                <div class="collection-stat-value">

                    ₱<?= number_format(
                        $todayCollected,
                        2
                    ) ?>

                </div>

                <div class="collection-stat-description">

                    Payments received today

                </div>

            </div>


            <div class="collection-stat">

                <div class="collection-stat-label">

                    Remaining Today

                </div>

                <div class="collection-stat-value">

                    ₱<?= number_format(
                        $todayRemaining,
                        2
                    ) ?>

                </div>

                <div class="collection-stat-description">

                    Amount still expected today

                </div>

            </div>


            <div class="collection-stat">

                <div class="collection-stat-label">

                    Overdue

                </div>

                <div class="collection-stat-value collection-danger">

                    ₱<?= number_format(
                        $overdueAmount,
                        2
                    ) ?>

                </div>

                <div class="collection-stat-description">

                    <?= number_format(
                        $overdueCount
                    ) ?>

                    overdue installments

                </div>

            </div>


        </div>


        <div class="collection-grid">


            <!-- LEFT -->

            <div>


                <!-- TODAY'S COLLECTION -->

                <div class="collection-card">

                    <div class="collection-card-header">

                        <div>

                            <h2>
                                Today's Collections
                            </h2>

                            <p>
                                Installments due today
                            </p>

                        </div>


                        <a
                            href="index.php?url=collections/today"
                            class="btn btn-secondary"
                        >

                            View All

                        </a>

                    </div>


                    <?php if (
                        empty(
                            $todayCollections
                        )
                    ): ?>


                        <div class="collection-empty">

                            No collections are due today.

                        </div>


                    <?php else: ?>


                        <div
                            class="collection-table-wrapper"
                        >

                            <table
                                class="collection-table"
                            >

                                <thead>

                                    <tr>

                                        <th>
                                            Borrower
                                        </th>

                                        <th>
                                            Loan
                                        </th>

                                        <th>
                                            Due
                                        </th>

                                        <th>
                                            Balance
                                        </th>

                                        <th>
                                            Action
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                <?php foreach (
                                    array_slice(
                                        $todayCollections,
                                        0,
                                        10
                                    )
                                    as $item
                                ): ?>


                                    <tr>

                                        <td>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    trim(
                                                        $item[
                                                            'borrower_name'
                                                        ]
                                                        ?? ''
                                                    )
                                                ) ?>

                                            </strong>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $item[
                                                    'loan_number'
                                                ]
                                                ?? '-'
                                            ) ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $item[
                                                    'due_date'
                                                ]
                                                ?? '-'
                                            ) ?>

                                        </td>


                                        <td class="collection-danger">

                                            ₱<?= number_format(
                                                (float)(
                                                    $item[
                                                        'balance'
                                                    ]
                                                    ?? 0
                                                ),
                                                2
                                            ) ?>

                                        </td>


                                        <td>

                                            <a
                                                href="index.php?url=loans/payment&id=<?= (int)(
                                                    $item[
                                                        'loan_id'
                                                    ]
                                                    ?? 0
                                                ) ?>"
                                                class="btn btn-primary"
                                            >

                                                Pay

                                            </a>

                                        </td>

                                    </tr>


                                <?php endforeach; ?>


                                </tbody>

                            </table>

                        </div>


                    <?php endif; ?>


                </div>


                <!-- RECENT PAYMENTS -->

                <div class="collection-card">

                    <div class="collection-card-header">

                        <div>

                            <h2>
                                Recent Payments
                            </h2>

                            <p>
                                Latest recorded payments
                            </p>

                        </div>


                        <a
                            href="index.php?url=collections/payments"
                            class="btn btn-secondary"
                        >

                            View All

                        </a>

                    </div>


                    <?php if (
                        empty(
                            $recentPayments
                        )
                    ): ?>


                        <div class="collection-empty">

                            No payments recorded yet.

                        </div>


                    <?php else: ?>


                        <div
                            class="collection-table-wrapper"
                        >

                            <table
                                class="collection-table"
                            >

                                <thead>

                                    <tr>

                                        <th>
                                            Date
                                        </th>

                                        <th>
                                            Borrower
                                        </th>

                                        <th>
                                            Loan
                                        </th>

                                        <th>
                                            Amount
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                <?php foreach (
                                    $recentPayments
                                    as $payment
                                ): ?>


                                    <tr>

                                        <td>

                                            <?= htmlspecialchars(
                                                $payment[
                                                    'payment_date'
                                                ]
                                                ?? '-'
                                            ) ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $payment[
                                                    'borrower_name'
                                                ]
                                                ?? '-'
                                            ) ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $payment[
                                                    'loan_number'
                                                ]
                                                ?? '-'
                                            ) ?>

                                        </td>


                                        <td class="collection-success">

                                            ₱<?= number_format(
                                                (float)(
                                                    $payment[
                                                        'amount'
                                                    ]
                                                    ?? 0
                                                ),
                                                2
                                            ) ?>

                                        </td>

                                    </tr>


                                <?php endforeach; ?>


                                </tbody>

                            </table>

                        </div>


                    <?php endif; ?>


                </div>


            </div>


            <!-- RIGHT -->

            <div>


                <!-- COLLECTION PROGRESS -->

                <div class="collection-card">

                    <div class="collection-card-header">

                        <div>

                            <h2>
                                Today's Progress
                            </h2>

                            <p>
                                Collection performance
                            </p>

                        </div>

                    </div>


                    <div class="collection-card-body">


                        <div
                            style="
                                font-size:30px;
                                font-weight:700;
                            "
                        >

                            <?= number_format(
                                $collectionRate,
                                1
                            ) ?>%

                        </div>


                        <div
                            style="
                                color:#64748b;
                                font-size:13px;
                                margin-top:4px;
                            "
                        >

                            Collection rate

                        </div>


                        <div class="collection-progress">

                            <div
                                class="collection-progress-header"
                            >

                                <span>

                                    ₱<?= number_format(
                                        $todayCollected,
                                        2
                                    ) ?>

                                </span>


                                <span>

                                    ₱<?= number_format(
                                        $todayExpected,
                                        2
                                    ) ?>

                                </span>

                            </div>


                            <div
                                class="collection-progress-bar"
                            >

                                <div
                                    class="collection-progress-fill"
                                    style="
                                        width:
                                        <?= $collectionRate ?>%;
                                    "
                                ></div>

                            </div>

                        </div>


                    </div>

                </div>


                <!-- QUICK ACTIONS -->

                <div class="collection-card">

                    <div class="collection-card-header">

                        <div>

                            <h2>
                                Quick Actions
                            </h2>

                            <p>
                                Collection management
                            </p>

                        </div>

                    </div>


                    <div class="collection-card-body">


                        <div class="collection-actions">


                            <a
                                href="index.php?url=collections/today"
                                class="collection-action"
                            >

                                <div
                                    class="collection-action-title"
                                >

                                    Today's Collections

                                </div>

                                <div
                                    class="collection-action-text"
                                >

                                    View installments due today.

                                </div>

                            </a>


                            <a
                                href="index.php?url=loans/payment"
                                class="collection-action"
                            >

                                <div
                                    class="collection-action-title"
                                >

                                    Record Payment

                                </div>

                                <div
                                    class="collection-action-text"
                                >

                                    Record a borrower payment.

                                </div>

                            </a>


                            <a
                                href="index.php?url=collections/payments"
                                class="collection-action"
                            >

                                <div
                                    class="collection-action-title"
                                >

                                    Payment History

                                </div>

                                <div
                                    class="collection-action-text"
                                >

                                    Review recorded payments.

                                </div>

                            </a>


                            <a
                                href="index.php?url=collections/overdue"
                                class="collection-action"
                            >

                                <div
                                    class="collection-action-title"
                                >

                                    Overdue Loans

                                </div>

                                <div
                                    class="collection-action-text"
                                >

                                    Review overdue installments.

                                </div>

                            </a>


                        </div>


                    </div>

                </div>


            </div>


        </div>


    </div>

</div>


</body>

</html>