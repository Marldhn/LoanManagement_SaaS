<?php

/*
|--------------------------------------------------------------------------
| COLLECTIONS INDEX VIEW
|--------------------------------------------------------------------------
|
| Expected controller variables:
|
| $user
| $business
| $tenantRole
| $collections
| $totalCollected
| $todayCollected
| $monthCollected
| $pendingAmount
| $overdueAmount
| $currentUrl
|
|--------------------------------------------------------------------------
*/


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
    $currentUrl ?? 'collections';


/*
|--------------------------------------------------------------------------
| COLLECTION DATA
|--------------------------------------------------------------------------
*/

$collections =
    is_array($collections ?? null)
        ? $collections
        : [];


/*
|--------------------------------------------------------------------------
| SUMMARY VALUES
|--------------------------------------------------------------------------
*/

$totalCollected =
    (float)($totalCollected ?? 0);

$todayCollected =
    (float)($todayCollected ?? 0);

$monthCollected =
    (float)($monthCollected ?? 0);

$pendingAmount =
    (float)($pendingAmount ?? 0);

$overdueAmount =
    (float)($overdueAmount ?? 0);


/*
|--------------------------------------------------------------------------
| FILTER VALUES
|--------------------------------------------------------------------------
*/

$search =
    $_GET['search'] ?? '';

$status =
    $_GET['status'] ?? '';

$dateFrom =
    $_GET['date_from'] ?? '';

$dateTo =
    $_GET['date_to'] ?? '';


/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

function collectionValue(
    $value,
    $fallback = '-'
) {
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


function collectionMoney(
    $value
) {
    return '₱' . number_format(
        (float)$value,
        2
    );
}


function collectionStatusClass(
    $status
) {
    $status =
        strtolower(
            trim(
                (string)$status
            )
        );

    switch ($status) {

        case 'posted':
        case 'paid':
            return 'status-paid';

        case 'partial':
            return 'status-partial';

        case 'pending':
            return 'status-pending';

        case 'overdue':
            return 'status-overdue';

        case 'void':
            return 'status-void';

        default:
            return 'status-default';
    }
}


function collectionStatusLabel(
    $status
) {
    if (
        $status === null ||
        $status === ''
    ) {
        return '-';
    }

    return ucfirst(
        str_replace(
            '_',
            ' ',
            (string)$status
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
        Collections
    </title>


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


    <style>

        /*
        |--------------------------------------------------------------------------
        | TOKENS — shares the sidebar / dashboard / borrowers / loans / payments
        | language
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
            --lm-rust: #B5451C;
            --lm-rust-soft: #FBEAE2;
            --lm-danger: #B0392E;
            --lm-danger-soft: #FBEBE8;
            --lm-info: #2E5C8A;
            --lm-info-soft: #E9F0F7;
            --lm-font-serif: 'Fraunces', Georgia, 'Iowan Old Style', serif;
            --lm-font-sans: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

        }


        /*
        |--------------------------------------------------------------------------
        | PAGE
        |--------------------------------------------------------------------------
        */

        .collections-page {
            width: 100%;

            font-family: var(--lm-font-sans);
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .collections-header {
            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;
        }


        .collections-title h1 {
            margin: 0 0 5px;

            font-family: var(--lm-font-serif);

            font-size: 27px;

            font-weight: 600;

            color: var(--lm-ink-900);

            letter-spacing: -.01em;
        }


        .collections-title p {
            margin: 0;

            color: var(--lm-ink-500);

            font-size: 14px;
        }


        .collections-header-actions {
            display: flex;

            align-items: center;

            gap: 10px;

            flex-wrap: wrap;
        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        .collections-summary-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;

            margin-bottom: 22px;
        }


        .collection-summary-card {
            background: var(--lm-surface);

            border:
                1px solid var(--lm-line);

            border-top: 3px solid var(--lm-forest);

            border-radius: 12px;

            padding: 20px;
        }


        .collections-summary-grid
        .collection-summary-card:nth-child(2) {
            border-top-color: var(--lm-info);
        }


        .collections-summary-grid
        .collection-summary-card:nth-child(3) {
            border-top-color: var(--lm-brass);
        }


        .collections-summary-grid
        .collection-summary-card:nth-child(4) {
            border-top-color: var(--lm-rust);
        }


        .collection-summary-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            margin-bottom: 12px;
        }


        .collection-summary-title {
            color: var(--lm-ink-500);

            font-size: 12px;

            font-weight: 600;
        }


        .collection-summary-icon {
            width: 35px;

            height: 35px;

            border-radius: 9px;

            background: var(--lm-forest-soft);

            color: var(--lm-forest);

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 15px;
        }


        .collections-summary-grid
        .collection-summary-card:nth-child(2)
        .collection-summary-icon {
            background: var(--lm-info-soft);

            color: var(--lm-info);
        }


        .collections-summary-grid
        .collection-summary-card:nth-child(3)
        .collection-summary-icon {
            background: var(--lm-brass-soft);

            color: var(--lm-brass-ink);
        }


        .collections-summary-grid
        .collection-summary-card:nth-child(4)
        .collection-summary-icon {
            background: var(--lm-rust-soft);

            color: var(--lm-rust);
        }


        .collection-summary-value {
            font-size: 23px;

            line-height: 1.2;

            font-weight: 700;

            color: var(--lm-ink-900);

            letter-spacing: -.01em;
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER CARD
        |--------------------------------------------------------------------------
        */

        .collections-filter-card {
            background: var(--lm-surface);

            border:
                1px solid var(--lm-line);

            border-radius: 12px;

            margin-bottom: 22px;

            padding: 20px;
        }


        .collections-filter-form {
            display: grid;

            grid-template-columns:
                minmax(220px, 2fr)
                minmax(150px, 1fr)
                minmax(150px, 1fr)
                minmax(150px, 1fr)
                auto;

            gap: 12px;

            align-items: end;
        }


        .collection-filter-group {
            min-width: 0;
        }


        .collection-filter-label {
            display: block;

            margin-bottom: 6px;

            color: var(--lm-ink-700);

            font-size: 12px;

            font-weight: 600;
        }


        .collection-filter-input,
        .collection-filter-select {
            width: 100%;

            height: 42px;

            padding:
                0 12px;

            border:
                1px solid var(--lm-line);

            border-radius: 8px;

            background: var(--lm-surface);

            color: var(--lm-ink-900);

            font-size: 13px;

            outline: none;
        }


        .collection-filter-input:focus,
        .collection-filter-select:focus {
            border-color: var(--lm-brass);

            box-shadow:
                0 0 0 3px
                rgba(184, 134, 15, 0.14);
        }


        .collection-filter-actions {
            display: flex;

            gap: 8px;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE CARD
        |--------------------------------------------------------------------------
        */

        .collections-table-card {
            background: var(--lm-surface);

            border:
                1px solid var(--lm-line);

            border-radius: 12px;

            overflow: hidden;
        }


        .collections-table-header {
            padding:
                18px 22px;

            border-bottom:
                1px solid var(--lm-line-soft);

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;
        }


        .collections-table-header h2 {
            margin: 0;

            font-family: var(--lm-font-serif);

            font-size: 17px;

            font-weight: 600;

            color: var(--lm-ink-900);
        }


        .collections-table-header p {
            margin: 4px 0 0;

            color: var(--lm-ink-500);

            font-size: 13px;
        }


        .collections-count {
            color: var(--lm-ink-500);

            font-size: 13px;

            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        .collections-table-wrapper {
            width: 100%;

            overflow-x: auto;
        }


        .collections-table {
            width: 100%;

            min-width: 1250px;

            border-collapse: collapse;
        }


        .collections-table th {
            padding:
                13px 14px;

            background: var(--lm-surface-tint);

            border-bottom:
                1px solid var(--lm-line);

            color: var(--lm-ink-500);

            font-size: 11px;

            font-weight: 700;

            letter-spacing: .02em;

            text-align: left;

            white-space: nowrap;
        }


        .collections-table td {
            padding:
                14px;

            border-bottom:
                1px solid var(--lm-line-soft);

            color: var(--lm-ink-700);

            font-size: 13px;

            white-space: nowrap;
        }


        .collections-table tbody tr {
            transition: background .15s ease;
        }


        .collections-table tbody tr:hover {
            background: var(--lm-surface-tint);
        }


        /*
        |--------------------------------------------------------------------------
        | BORROWER
        |--------------------------------------------------------------------------
        */

        .collection-borrower-name {
            display: block;

            font-weight: 700;

            color: var(--lm-ink-900);

            margin-bottom: 3px;
        }


        .collection-borrower-code {
            display: block;

            color: var(--lm-ink-300);

            font-size: 11px;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAN
        |--------------------------------------------------------------------------
        */

        .collection-loan-number {
            font-weight: 700;

            color: var(--lm-info);
        }


        /*
        |--------------------------------------------------------------------------
        | MONEY
        |--------------------------------------------------------------------------
        */

        .collection-money {
            font-weight: 600;

            color: var(--lm-ink-900);
        }


        .collection-money-paid {
            font-weight: 700;

            color: var(--lm-forest);
        }


        .collection-money-balance {
            font-weight: 700;

            color: var(--lm-danger);
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        .collection-status {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding:
                5px 9px;

            border-radius: 999px;

            font-size: 11px;

            font-weight: 700;

            white-space: nowrap;
        }


        .status-paid {
            background: var(--lm-forest-soft);

            color: var(--lm-forest);
        }


        .status-partial {
            background: var(--lm-brass-soft);

            color: var(--lm-brass-ink);
        }


        .status-pending {
            background: var(--lm-info-soft);

            color: var(--lm-info);
        }


        .status-overdue {
            background: var(--lm-rust-soft);

            color: var(--lm-rust);
        }


        .status-void {
            background: var(--lm-line-soft);

            color: var(--lm-ink-500);
        }


        .status-default {
            background: var(--lm-line-soft);

            color: var(--lm-ink-500);
        }


        /*
        |--------------------------------------------------------------------------
        | ACTIONS
        |--------------------------------------------------------------------------
        */

        .collection-actions {
            display: flex;

            align-items: center;

            gap: 7px;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .collections-empty {
            padding:
                60px 25px;

            text-align: center;
        }


        .collections-empty-icon {
            width: 58px;

            height: 58px;

            margin:
                0 auto 15px;

            border-radius: 50%;

            background: var(--lm-line-soft);

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 23px;
        }


        .collections-empty h3 {
            margin:
                0 0 8px;

            font-size: 17px;

            color: var(--lm-ink-900);
        }


        .collections-empty p {
            margin: 0;

            color: var(--lm-ink-300);

            font-size: 14px;
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1200px) {

            .collections-summary-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }


            .collections-filter-form {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

        }


        @media (max-width: 800px) {

            .collections-header {
                flex-direction: column;
            }


            .collections-header-actions {
                width: 100%;
            }


            .collections-filter-form {
                grid-template-columns: 1fr;
            }


            .collection-filter-actions {
                width: 100%;
            }


            .collection-filter-actions .btn {
                flex: 1;
            }

        }


        @media (max-width: 600px) {

            .collections-summary-grid {
                grid-template-columns: 1fr;
            }


            .collections-title h1 {
                font-size: 22px;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | REDUCED MOTION
        |--------------------------------------------------------------------------
        */

        @media (prefers-reduced-motion: reduce) {

            .collections-table tbody tr {
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


    <!--
    |--------------------------------------------------------------------------
    | NAVBAR
    |--------------------------------------------------------------------------
    -->

    <nav class="navbar">

        <div class="page-title">

            Collections

        </div>


        <div class="user-info">

            <span class="user-name">

                <?= collectionValue(
                    $user['full_name']
                    ?? $user['username']
                    ?? 'User'
                ) ?>

            </span>


            <span class="badge">

                <?= collectionValue(
                    $tenantRole
                    ?? 'User'
                ) ?>

            </span>

        </div>

    </nav>


    <div class="container collections-page">


        <!--
        |--------------------------------------------------------------------------
        | PAGE HEADER
        |--------------------------------------------------------------------------
        -->

        <div class="collections-header">


            <div class="collections-title">

                <h1>
                    Collections
                </h1>

                <p>
                    Record and monitor loan payments and collections.
                </p>

            </div>


            <div class="collections-header-actions">


                <a
                    href="index.php?url=collections/create"
                    class="btn btn-primary"
                >

                    + Record Payment

                </a>


                <a
                    href="index.php?url=loans"
                    class="btn btn-secondary"
                >

                    View Loans

                </a>


            </div>


        </div>


        <!--
        |--------------------------------------------------------------------------
        | SUMMARY CARDS
        |--------------------------------------------------------------------------
        -->

        <div class="collections-summary-grid">


            <!-- TOTAL COLLECTED -->

            <div class="collection-summary-card">

                <div class="collection-summary-header">

                    <span class="collection-summary-title">

                        Total Collected

                    </span>


                    <span class="collection-summary-icon">

                        ₱

                    </span>

                </div>


                <div class="collection-summary-value">

                    <?= collectionMoney(
                        $totalCollected
                    ) ?>

                </div>

            </div>


            <!-- TODAY -->

            <div class="collection-summary-card">

                <div class="collection-summary-header">

                    <span class="collection-summary-title">

                        Collected Today

                    </span>


                    <span class="collection-summary-icon">

                        📅

                    </span>

                </div>


                <div class="collection-summary-value">

                    <?= collectionMoney(
                        $todayCollected
                    ) ?>

                </div>

            </div>


            <!-- MONTH -->

            <div class="collection-summary-card">

                <div class="collection-summary-header">

                    <span class="collection-summary-title">

                        Collected This Month

                    </span>


                    <span class="collection-summary-icon">

                        📊

                    </span>

                </div>


                <div class="collection-summary-value">

                    <?= collectionMoney(
                        $monthCollected
                    ) ?>

                </div>

            </div>


            <!-- OVERDUE -->

            <div class="collection-summary-card">

                <div class="collection-summary-header">

                    <span class="collection-summary-title">

                        Overdue Amount

                    </span>


                    <span class="collection-summary-icon">

                        ⚠

                    </span>

                </div>


                <div class="collection-summary-value">

                    <?= collectionMoney(
                        $overdueAmount
                    ) ?>

                </div>

            </div>


        </div>


        <!--
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        -->

        <div class="collections-filter-card">


            <form
                method="GET"
                action="index.php"
                class="collections-filter-form"
            >


                <input
                    type="hidden"
                    name="url"
                    value="collections"
                >


                <!-- SEARCH -->

                <div class="collection-filter-group">

                    <label
                        class="collection-filter-label"
                        for="collection-search"
                    >

                        Search

                    </label>


                    <input
                        type="text"
                        id="collection-search"
                        name="search"
                        class="collection-filter-input"
                        value="<?= collectionValue(
                            $search,
                            ''
                        ) ?>"
                        placeholder="Borrower, loan number, payment number..."
                    >

                </div>


                <!-- STATUS -->

                <div class="collection-filter-group">

                    <label
                        class="collection-filter-label"
                        for="collection-status"
                    >

                        Status

                    </label>


                    <select
                        id="collection-status"
                        name="status"
                        class="collection-filter-select"
                    >

                        <option
                            value=""
                        >
                            All Statuses
                        </option>


                        <option
                            value="posted"
                            <?= $status === 'posted'
                                ? 'selected'
                                : '' ?>
                        >
                            Posted
                        </option>


                        <option
                            value="void"
                            <?= $status === 'void'
                                ? 'selected'
                                : '' ?>
                        >
                            Void
                        </option>


                    </select>

                </div>


                <!-- DATE FROM -->

                <div class="collection-filter-group">

                    <label
                        class="collection-filter-label"
                        for="collection-date-from"
                    >

                        Date From

                    </label>


                    <input
                        type="date"
                        id="collection-date-from"
                        name="date_from"
                        class="collection-filter-input"
                        value="<?= collectionValue(
                            $dateFrom,
                            ''
                        ) ?>"
                    >

                </div>


                <!-- DATE TO -->

                <div class="collection-filter-group">

                    <label
                        class="collection-filter-label"
                        for="collection-date-to"
                    >

                        Date To

                    </label>


                    <input
                        type="date"
                        id="collection-date-to"
                        name="date_to"
                        class="collection-filter-input"
                        value="<?= collectionValue(
                            $dateTo,
                            ''
                        ) ?>"
                    >

                </div>


                <!-- BUTTONS -->

                <div class="collection-filter-actions">


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        Filter

                    </button>


                    <a
                        href="index.php?url=collections"
                        class="btn btn-secondary"
                    >

                        Reset

                    </a>


                </div>


            </form>


        </div>


        <!--
        |--------------------------------------------------------------------------
        | COLLECTION TABLE
        |--------------------------------------------------------------------------
        -->

        <div class="collections-table-card">


            <div class="collections-table-header">


                <div>

                    <h2>
                        Payment Collections
                    </h2>


                    <p>
                        Recorded payments for your business
                    </p>

                </div>


                <div class="collections-count">

                    <?= number_format(
                        count($collections)
                    ) ?>

                    record(s)

                </div>


            </div>


            <?php if (
                empty($collections)
            ): ?>


                <!--
                |--------------------------------------------------------------------------
                | EMPTY STATE
                |--------------------------------------------------------------------------
                -->

                <div class="collections-empty">


                    <div class="collections-empty-icon">

                        💰

                    </div>


                    <h3>
                        No Collections Found
                    </h3>


                    <p>
                        No payment collections match your current filters.
                    </p>


                    <br>


                    <a
                        href="index.php?url=collections/create"
                        class="btn btn-primary"
                    >

                        + Record Payment

                    </a>


                </div>


            <?php else: ?>


                <div class="collections-table-wrapper">


                    <table class="collections-table">


                        <thead>

                            <tr>

                                <th>
                                    Payment #
                                </th>

                                <th>
                                    Borrower
                                </th>

                                <th>
                                    Loan #
                                </th>

                                <th>
                                    Due Date
                                </th>

                                <th>
                                    Payment Date
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
                                    Amount
                                </th>

                                <th>
                                    Account
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
                            $collections
                            as $collection
                        ): ?>


                            <?php

                            /*
                            |--------------------------------------------------------------------------
                            | PAYMENT VALUES
                            |--------------------------------------------------------------------------
                            */

                            $paymentId =
                                (int)(
                                    $collection['id']
                                    ?? 0
                                );


                            $paymentNumber =
                                $collection[
                                    'payment_number'
                                ]
                                ?? '-';


                            $borrowerName =
                                trim(
                                    (
                                        $collection[
                                            'first_name'
                                        ]
                                        ?? ''
                                    )
                                    . ' ' .
                                    (
                                        $collection[
                                            'middle_name'
                                        ]
                                        ?? ''
                                    )
                                    . ' ' .
                                    (
                                        $collection[
                                            'last_name'
                                        ]
                                        ?? ''
                                    )
                                );


                            if (
                                $borrowerName === ''
                            ) {

                                $borrowerName =
                                    $collection[
                                        'borrower_name'
                                    ]
                                    ?? 'Unknown Borrower';

                            }


                            $borrowerCode =
                                $collection[
                                    'borrower_code'
                                ]
                                ?? '-';


                            $loanNumber =
                                $collection[
                                    'loan_number'
                                ]
                                ?? '-';


                            $scheduleDueDate =
                                $collection[
                                    'due_date'
                                ]
                                ?? null;


                            $paymentDate =
                                $collection[
                                    'payment_date'
                                ]
                                ?? null;


                            $principalAmount =
                                (float)(
                                    $collection[
                                        'principal_amount'
                                    ]
                                    ?? 0
                                );


                            $interestAmount =
                                (float)(
                                    $collection[
                                        'interest_amount'
                                    ]
                                    ?? 0
                                );


                            $penaltyAmount =
                                (float)(
                                    $collection[
                                        'penalty_amount'
                                    ]
                                    ?? 0
                                );


                            $amount =
                                (float)(
                                    $collection[
                                        'amount'
                                    ]
                                    ?? 0
                                );


                            $accountName =
                                $collection[
                                    'account_name'
                                ]
                                ?? '-';


                            $paymentStatus =
                                $collection[
                                    'status'
                                ]
                                ?? 'posted';


                            ?>


                            <tr>


                                <!-- PAYMENT NUMBER -->

                                <td>

                                    <strong>

                                        <?= collectionValue(
                                            $paymentNumber
                                        ) ?>

                                    </strong>

                                </td>


                                <!-- BORROWER -->

                                <td>

                                    <span
                                        class="collection-borrower-name"
                                    >

                                        <?= collectionValue(
                                            $borrowerName
                                        ) ?>

                                    </span>


                                    <span
                                        class="collection-borrower-code"
                                    >

                                        <?= collectionValue(
                                            $borrowerCode
                                        ) ?>

                                    </span>

                                </td>


                                <!-- LOAN -->

                                <td>

                                    <span
                                        class="collection-loan-number"
                                    >

                                        <?= collectionValue(
                                            $loanNumber
                                        ) ?>

                                    </span>

                                </td>


                                <!-- DUE DATE -->

                                <td>

                                    <?= collectionValue(
                                        $scheduleDueDate
                                    ) ?>

                                </td>


                                <!-- PAYMENT DATE -->

                                <td>

                                    <?= collectionValue(
                                        $paymentDate
                                    ) ?>

                                </td>


                                <!-- PRINCIPAL -->

                                <td>

                                    <span
                                        class="collection-money"
                                    >

                                        <?= collectionMoney(
                                            $principalAmount
                                        ) ?>

                                    </span>

                                </td>


                                <!-- INTEREST -->

                                <td>

                                    <span
                                        class="collection-money"
                                    >

                                        <?= collectionMoney(
                                            $interestAmount
                                        ) ?>

                                    </span>

                                </td>


                                <!-- PENALTY -->

                                <td>

                                    <span
                                        class="collection-money"
                                    >

                                        <?= collectionMoney(
                                            $penaltyAmount
                                        ) ?>

                                    </span>

                                </td>


                                <!-- TOTAL AMOUNT -->

                                <td>

                                    <span
                                        class="collection-money-paid"
                                    >

                                        <?= collectionMoney(
                                            $amount
                                        ) ?>

                                    </span>

                                </td>


                                <!-- ACCOUNT -->

                                <td>

                                    <?= collectionValue(
                                        $accountName
                                    ) ?>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="collection-status <?= collectionStatusClass(
                                            $paymentStatus
                                        ) ?>"
                                    >

                                        <?= collectionValue(
                                            collectionStatusLabel(
                                                $paymentStatus
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <!-- ACTION -->

                                <td>

                                    <div
                                        class="collection-actions"
                                    >


                                        <a
                                            href="index.php?url=collections/show&id=<?= $paymentId ?>"
                                            class="btn btn-secondary"
                                        >

                                            View

                                        </a>


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


</div>


</body>

</html>