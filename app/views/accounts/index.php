<?php

$user = $user ?? Auth::user();

$business = $business ?? Auth::business();

$tenantRole = $tenantRole ?? Auth::tenantRole();

$currentUrl = $currentUrl ?? 'accounts';


/*
|--------------------------------------------------------------------------
| ACCOUNTS
|--------------------------------------------------------------------------
*/

$accounts = is_array($accounts ?? null)
    ? $accounts
    : [];


/*
|--------------------------------------------------------------------------
| ACCOUNT SUMMARY
|--------------------------------------------------------------------------
*/

$totalAccounts = count($accounts);

$totalAssets = 0.00;

$totalLiabilities = 0.00;

$totalEquity = 0.00;

$totalIncome = 0.00;

$totalExpenses = 0.00;


foreach ($accounts as $account) {

    $accountType = strtolower(
        trim(
            $account['account_type']
            ?? $account['type']
            ?? ''
        )
    );

    $balance = (float)(
        $account['balance']
        ?? $account['current_balance']
        ?? 0
    );


    if ($accountType === 'asset') {

        $totalAssets += $balance;

    } elseif ($accountType === 'liability') {

        $totalLiabilities += $balance;

    } elseif ($accountType === 'equity') {

        $totalEquity += $balance;

    } elseif ($accountType === 'income') {

        $totalIncome += $balance;

    } elseif ($accountType === 'expense') {

        $totalExpenses += $balance;

    }

}


$netBalance =
    $totalAssets
    - $totalLiabilities;


/*
|--------------------------------------------------------------------------
| FLASH MESSAGES
|--------------------------------------------------------------------------
*/

$success = $success
    ?? ($_SESSION['account_success'] ?? '');

$error = $error
    ?? ($_SESSION['account_error'] ?? '');


/*
|--------------------------------------------------------------------------
| USER DISPLAY
|--------------------------------------------------------------------------
*/

$displayName =
    $user['full_name']
    ?? $user['username']
    ?? 'User';

$displayRole =
    $tenantRole
    ?? 'User';

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
        Accounts | Loan Management
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
        | TOKENS — shares the sidebar / dashboard / borrowers / loans / payments /
        | collections language
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
        | ACCOUNTS PAGE
        |--------------------------------------------------------------------------
        */

        .accounts-page {

            font-family: var(--lm-font-sans);

            animation:
                accountsFadeIn
                0.35s
                ease;

        }


        @keyframes accountsFadeIn {

            from {

                opacity: 0;

                transform:
                    translateY(8px);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0);

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PAGE HEADER
        |--------------------------------------------------------------------------
        */

        .accounts-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 24px;

        }


        .accounts-heading {

            display: flex;

            align-items: center;

            gap: 14px;

        }


        .accounts-heading-icon {

            width: 48px;

            height: 48px;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: var(--lm-brass-soft);

            color: var(--lm-brass-ink);

            font-size: 22px;

            flex-shrink: 0;

        }


        .accounts-heading h1 {

            margin: 0;

            font-family: var(--lm-font-serif);

            font-size: 26px;

            font-weight: 600;

            color: var(--lm-ink-900);

            letter-spacing: -0.01em;

        }


        .accounts-heading p {

            margin: 4px 0 0;

            color: var(--lm-ink-500);

            font-size: 14px;

        }


        .accounts-actions {

            display: flex;

            align-items: center;

            gap: 9px;

            flex-wrap: wrap;

        }


        .account-action-btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            min-height: 40px;

            padding: 0 14px;

            border-radius: 9px;

            font-size: 13px;

            font-weight: 650;

            cursor: pointer;

            transition:
                transform 0.15s ease;

        }


        .account-action-btn:hover {

            transform:
                translateY(-1px);

        }


        /*
        |--------------------------------------------------------------------------
        | ALERTS
        |--------------------------------------------------------------------------
        */

        .accounts-alert {

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 13px 16px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 14px;

            font-weight: 550;

        }


        .accounts-alert-success {

            color: var(--lm-forest);

            background: var(--lm-forest-soft);

            border: 1px solid rgba(31, 122, 82, 0.3);

        }


        .accounts-alert-error {

            color: var(--lm-danger);

            background: var(--lm-danger-soft);

            border: 1px solid rgba(176, 57, 46, 0.3);

        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY CARDS
        |--------------------------------------------------------------------------
        */

        .account-summary-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
                );

            gap: 16px;

            margin-bottom: 24px;

        }


        .account-summary-card {

            position: relative;

            background: var(--lm-surface);

            border:
                1px solid var(--lm-line);

            border-top: 3px solid var(--lm-ink-300);

            border-radius: 12px;

            padding: 19px;

            transition:
                transform 0.18s ease,
                box-shadow 0.18s ease;

        }


        .account-summary-card:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 10px 24px
                rgba(22, 33, 29, .08);

        }


        .account-summary-grid
        .account-summary-card:nth-child(2) {

            border-top-color: var(--lm-forest);

        }


        .account-summary-grid
        .account-summary-card:nth-child(3) {

            border-top-color: var(--lm-rust);

        }


        .account-summary-grid
        .account-summary-card:nth-child(4) {

            border-top-color: var(--lm-brass);

        }


        .account-summary-top {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            margin-bottom: 15px;

        }


        .account-summary-icon {

            width: 38px;

            height: 38px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 10px;

            font-size: 17px;

        }


        .summary-icon-blue {

            background: var(--lm-line-soft);

            color: var(--lm-ink-700);

        }


        .summary-icon-green {

            background: var(--lm-forest-soft);

            color: var(--lm-forest);

        }


        .summary-icon-red {

            background: var(--lm-rust-soft);

            color: var(--lm-rust);

        }


        .summary-icon-purple {

            background: var(--lm-brass-soft);

            color: var(--lm-brass-ink);

        }


        .account-summary-title {

            font-size: 11px;

            font-weight: 650;

            color: var(--lm-ink-500);

            letter-spacing: 0.02em;

        }


        .account-summary-value {

            font-size: 24px;

            font-weight: 700;

            color: var(--lm-ink-900);

            letter-spacing: -0.01em;

        }


        .account-summary-subtitle {

            margin-top: 5px;

            font-size: 11px;

            color: var(--lm-ink-300);

        }


        /*
        |--------------------------------------------------------------------------
        | ACCOUNTS PANEL
        |--------------------------------------------------------------------------
        */

        .accounts-panel {

            background: var(--lm-surface);

            border:
                1px solid var(--lm-line);

            border-radius: 12px;

            overflow: hidden;

        }


        .accounts-panel-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 18px 20px;

            border-bottom:
                1px solid var(--lm-line-soft);

        }


        .accounts-panel-title {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .accounts-panel-title h2 {

            margin: 0;

            font-family: var(--lm-font-serif);

            font-size: 16px;

            font-weight: 600;

            color: var(--lm-ink-900);

        }


        .accounts-count {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-width: 25px;

            height: 24px;

            padding: 0 7px;

            border-radius: 20px;

            background: var(--lm-line-soft);

            color: var(--lm-ink-700);

            font-size: 11px;

            font-weight: 700;

        }


        .accounts-panel-description {

            margin: 3px 0 0;

            color: var(--lm-ink-300);

            font-size: 12px;

        }


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        .accounts-table-wrapper {

            width: 100%;

            overflow-x: auto;

        }


        .accounts-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 850px;

        }


        .accounts-table thead th {

            padding: 12px 18px;

            background: var(--lm-surface-tint);

            border-bottom:
                1px solid var(--lm-line);

            color: var(--lm-ink-500);

            font-size: 11px;

            font-weight: 700;

            letter-spacing: 0.02em;

            text-align: left;

            white-space: nowrap;

        }


        .accounts-table tbody td {

            padding: 15px 18px;

            border-bottom:
                1px solid var(--lm-line-soft);

            color: var(--lm-ink-700);

            font-size: 13px;

            vertical-align: middle;

        }


        .accounts-table tbody tr {

            transition:
                background 0.15s ease;

        }


        .accounts-table tbody tr:hover {

            background: var(--lm-surface-tint);

        }


        .accounts-table tbody tr:last-child td {

            border-bottom: none;

        }


        .account-code {

            display: inline-flex;

            align-items: center;

            padding: 5px 8px;

            border-radius: 6px;

            background: var(--lm-line-soft);

            color: var(--lm-ink-700);

            font-size: 11px;

            font-weight: 700;

            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;

        }


        .account-name-cell {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .account-name-icon {

            width: 34px;

            height: 34px;

            border-radius: 9px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: var(--lm-brass-soft);

            color: var(--lm-brass-ink);

            font-size: 14px;

            flex-shrink: 0;

        }


        .account-name {

            color: var(--lm-ink-900);

            font-weight: 650;

        }


        .account-type-badge {

            display: inline-flex;

            align-items: center;

            padding: 5px 9px;

            border-radius: 20px;

            background: var(--lm-surface-tint);

            border: 1px solid var(--lm-line);

            color: var(--lm-ink-700);

            font-size: 11px;

            font-weight: 650;

        }


        .account-balance {

            font-size: 14px;

            font-weight: 750;

            color: var(--lm-ink-900);

            white-space: nowrap;

        }


        .status-pill {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding: 5px 9px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 700;

        }


        .status-pill::before {

            content: '';

            width: 6px;

            height: 6px;

            border-radius: 50%;

            background: currentColor;

        }


        .status-active {

            color: var(--lm-forest);

            background: var(--lm-forest-soft);

        }


        .status-inactive {

            color: var(--lm-ink-500);

            background: var(--lm-line-soft);

        }


        .status-suspended {

            color: var(--lm-brass-ink);

            background: var(--lm-brass-soft);

        }


        .status-default {

            color: var(--lm-ink-500);

            background: var(--lm-line-soft);

        }


        /*
        |--------------------------------------------------------------------------
        | TABLE ACTIONS
        |--------------------------------------------------------------------------
        */

        .account-row-actions {

            display: flex;

            align-items: center;

            gap: 6px;

        }


        .table-action {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            height: 32px;

            padding: 0 10px;

            border-radius: 7px;

            border: 1px solid var(--lm-line);

            background: var(--lm-surface);

            color: var(--lm-ink-700);

            font-size: 11px;

            font-weight: 650;

            cursor: pointer;

            transition:
                background 0.15s ease,
                border-color 0.15s ease,
                color 0.15s ease;

        }


        .table-action:hover {

            border-color: var(--lm-brass);

            background: var(--lm-brass-soft);

            color: var(--lm-brass-ink);

        }


        .table-action-delete:hover {

            border-color: rgba(176, 57, 46, 0.4);

            background: var(--lm-danger-soft);

            color: var(--lm-danger);

        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .accounts-empty {

            padding: 65px 25px;

            text-align: center;

        }


        .accounts-empty-icon {

            width: 62px;

            height: 62px;

            margin: 0 auto 16px;

            border-radius: 16px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: var(--lm-brass-soft);

            color: var(--lm-brass-ink);

            font-size: 25px;

        }


        .accounts-empty h3 {

            margin: 0 0 6px;

            font-size: 17px;

            color: var(--lm-ink-900);

        }


        .accounts-empty p {

            max-width: 380px;

            margin: 0 auto 20px;

            color: var(--lm-ink-500);

            font-size: 13px;

            line-height: 1.6;

        }


        /*
        |--------------------------------------------------------------------------
        | MODALS
        |--------------------------------------------------------------------------
        */

        .modal-overlay {

            position: fixed;

            inset: 0;

            width: 100%;

            height: 100%;

            background:
                rgba(
                    22,
                    33,
                    29,
                    0.55
                );

            backdrop-filter:
                blur(3px);

            -webkit-backdrop-filter:
                blur(3px);

            display: none;

            align-items: center;

            justify-content: center;

            z-index: 9999;

            padding: 20px;

            box-sizing: border-box;

        }


        .modal-overlay.active {

            display: flex;

            animation:
                modalOverlayIn
                0.18s
                ease;

        }


        @keyframes modalOverlayIn {

            from {

                opacity: 0;

            }

            to {

                opacity: 1;

            }

        }


        .modal {

            background: var(--lm-surface);

            width: 100%;

            max-width: 550px;

            max-height: 90vh;

            overflow-y: auto;

            border-radius: 16px;

            padding: 25px;

            box-sizing: border-box;

            box-shadow:
                0 25px 70px
                rgba(
                    22,
                    33,
                    29,
                    0.28
                );

            animation:
                modalIn
                0.2s
                ease;

            font-family: var(--lm-font-sans);

        }


        @keyframes modalIn {

            from {

                opacity: 0;

                transform:
                    translateY(12px)
                    scale(0.98);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);

            }

        }


        .modal-header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 24px;

        }


        .modal-header h2 {

            margin: 0 0 5px;

            color: var(--lm-ink-900);

            font-family: var(--lm-font-serif);

            font-weight: 600;

            font-size: 20px;

        }


        .modal-header p {

            margin: 0;

            color: var(--lm-ink-500);

            font-size: 13px;

        }


        .modal-close {

            width: 34px;

            height: 34px;

            border: none;

            border-radius: 8px;

            background: var(--lm-line-soft);

            color: var(--lm-ink-500);

            font-size: 22px;

            line-height: 1;

            cursor: pointer;

            transition:
                background 0.15s ease,
                color 0.15s ease;

        }


        .modal-close:hover {

            background: var(--lm-danger-soft);

            color: var(--lm-danger);

        }


        /*
        |--------------------------------------------------------------------------
        | FORMS
        |--------------------------------------------------------------------------
        */

        .form-group {

            margin-bottom: 17px;

        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            color: var(--lm-ink-700);

            font-size: 13px;

            font-weight: 650;

        }


        .form-group input,
        .form-group select,
        .form-group textarea {

            width: 100%;

            box-sizing: border-box;

            border:
                1px solid var(--lm-line);

            border-radius: 9px;

            padding: 10px 12px;

            color: var(--lm-ink-900);

            background: var(--lm-surface);

            font-size: 13px;

            outline: none;

            transition:
                border-color 0.15s ease,
                box-shadow 0.15s ease;

        }


        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {

            border-color: var(--lm-brass);

            box-shadow:
                0 0 0 3px
                rgba(
                    184,
                    134,
                    15,
                    0.14
                );

        }


        .form-group textarea {

            min-height: 90px;

            resize: vertical;

        }


        .modal-footer {

            display: flex;

            justify-content: flex-end;

            gap: 9px;

            margin-top: 23px;

            padding-top: 18px;

            border-top:
                1px solid var(--lm-line-soft);

        }


        /*
        |--------------------------------------------------------------------------
        | MODAL BUTTONS
        |--------------------------------------------------------------------------
        */

        .modal-btn {

            min-height: 39px;

            padding: 0 15px;

            border-radius: 8px;

            border: 1px solid transparent;

            font-size: 12px;

            font-weight: 650;

            cursor: pointer;

        }


        .modal-btn-secondary {

            background: var(--lm-surface);

            border-color: var(--lm-line);

            color: var(--lm-ink-700);

        }


        .modal-btn-primary {

            background: var(--lm-ink-900);

            border-color: var(--lm-ink-900);

            color: var(--lm-brass);

        }


        .modal-btn-primary:hover {

            background: #0F1815;

            border-color: #0F1815;

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1050px) {

            .account-summary-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );

            }

        }


        @media (max-width: 760px) {

            .accounts-header {

                align-items: flex-start;

                flex-direction: column;

            }


            .accounts-actions {

                width: 100%;

            }


            .account-action-btn {

                flex: 1;

            }

        }


        @media (max-width: 600px) {

            .account-summary-grid {

                grid-template-columns: 1fr;

            }


            .accounts-heading h1 {

                font-size: 22px;

            }


            .accounts-heading-icon {

                width: 42px;

                height: 42px;

            }


            .account-action-btn {

                flex: 1 1 100%;

            }


            .modal {

                padding: 20px;

                border-radius: 13px;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | REDUCED MOTION
        |--------------------------------------------------------------------------
        */

        @media (prefers-reduced-motion: reduce) {

            .accounts-page {
                animation: none;
            }

            .account-summary-card,
            .account-action-btn,
            .table-action,
            .modal-overlay.active,
            .modal {
                animation: none;
                transition: none;
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


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <nav class="navbar">

        <div class="page-title">

            Accounts

        </div>


        <div class="user-info">

            <span class="user-name">

                <?= htmlspecialchars(
                    $displayName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </span>


            <span class="badge">

                <?= htmlspecialchars(
                    $displayRole,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </span>

        </div>

    </nav>


    <!-- =====================================================
         PAGE
    ====================================================== -->

    <div class="container accounts-page">


        <!-- =================================================
             PAGE HEADER
        ================================================== -->

        <div class="accounts-header">


            <div class="accounts-heading">


                <div class="accounts-heading-icon">

                    💰

                </div>


                <div>

                    <h1>
                        Accounts
                    </h1>

                    <p>
                        Manage your business accounts,
                        balances, and transfers.
                    </p>

                </div>


            </div>


            <div class="accounts-actions">


                <button
                    type="button"
                    class="btn btn-secondary account-action-btn"
                    onclick="openAdjustModal()"
                >

                    ↕
                    Adjust Balance

                </button>


                <button
                    type="button"
                    class="btn btn-secondary account-action-btn"
                    onclick="openTransferModal()"
                >

                    ⇄
                    Transfer

                </button>


                <button
                    type="button"
                    class="btn btn-primary account-action-btn"
                    onclick="openCreateModal()"
                >

                    +
                    Add Account

                </button>


            </div>


        </div>


        <!-- =================================================
             ALERTS
        ================================================== -->

        <?php if (!empty($success)): ?>

            <div class="accounts-alert accounts-alert-success">

                ✓

                <span>
                    <?= htmlspecialchars(
                        $success,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

        <?php endif; ?>


        <?php if (!empty($error)): ?>

            <div class="accounts-alert accounts-alert-error">

                !

                <span>
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

            </div>

        <?php endif; ?>


        <!-- =================================================
             SUMMARY
        ================================================== -->

        <div class="account-summary-grid">


            <!-- TOTAL ACCOUNTS -->

            <div class="account-summary-card">

                <div class="account-summary-top">

                    <div>

                        <div class="account-summary-title">
                            Total Accounts
                        </div>

                    </div>


                    <div
                        class="
                            account-summary-icon
                            summary-icon-blue
                        "
                    >

                        ◫

                    </div>

                </div>


                <div class="account-summary-value">

                    <?= number_format(
                        $totalAccounts
                    ) ?>

                </div>


                <div class="account-summary-subtitle">

                    Business accounts

                </div>

            </div>


            <!-- TOTAL ASSETS -->

            <div class="account-summary-card">

                <div class="account-summary-top">

                    <div>

                        <div class="account-summary-title">
                            Total Assets
                        </div>

                    </div>


                    <div
                        class="
                            account-summary-icon
                            summary-icon-green
                        "
                    >

                        ↑

                    </div>

                </div>


                <div class="account-summary-value">

                    ₱<?= number_format(
                        $totalAssets,
                        2
                    ) ?>

                </div>


                <div class="account-summary-subtitle">

                    Available business funds

                </div>

            </div>


            <!-- LIABILITIES -->

            <div class="account-summary-card">

                <div class="account-summary-top">

                    <div>

                        <div class="account-summary-title">
                            Liabilities
                        </div>

                    </div>


                    <div
                        class="
                            account-summary-icon
                            summary-icon-red
                        "
                    >

                        ↓

                    </div>

                </div>


                <div class="account-summary-value">

                    ₱<?= number_format(
                        $totalLiabilities,
                        2
                    ) ?>

                </div>


                <div class="account-summary-subtitle">

                    Outstanding obligations

                </div>

            </div>


            <!-- NET BALANCE -->

            <div class="account-summary-card">

                <div class="account-summary-top">

                    <div>

                        <div class="account-summary-title">
                            Net Balance
                        </div>

                    </div>


                    <div
                        class="
                            account-summary-icon
                            summary-icon-purple
                        "
                    >

                        ◆

                    </div>

                </div>


                <div class="account-summary-value">

                    ₱<?= number_format(
                        $netBalance,
                        2
                    ) ?>

                </div>


                <div class="account-summary-subtitle">

                    Assets minus liabilities

                </div>

            </div>


        </div>


        <!-- =================================================
             ACCOUNTS PANEL
        ================================================== -->

        <div class="accounts-panel">


            <div class="accounts-panel-header">


                <div>


                    <div class="accounts-panel-title">

                        <h2>
                            Business Accounts
                        </h2>


                        <span class="accounts-count">

                            <?= number_format(
                                $totalAccounts
                            ) ?>

                        </span>

                    </div>


                    <p class="accounts-panel-description">

                        View and manage your account balances.

                    </p>


                </div>


            </div>


            <?php if (empty($accounts)): ?>


                <!-- EMPTY -->

                <div class="accounts-empty">


                    <div class="accounts-empty-icon">

                        💰

                    </div>


                    <h3>
                        No Accounts Yet
                    </h3>


                    <p>

                        Create your first business account
                        to start tracking balances,
                        transfers, and financial activity.

                    </p>


                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="openCreateModal()"
                    >

                        + Add Your First Account

                    </button>


                </div>


            <?php else: ?>


                <!-- TABLE -->

                <div class="accounts-table-wrapper">


                    <table class="accounts-table">


                        <thead>

                            <tr>

                                <th>
                                    Code
                                </th>

                                <th>
                                    Account
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Balance
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
                            $accounts as $account
                        ): ?>


                            <?php

                            $accountId =
                                (int)(
                                    $account['id']
                                    ?? 0
                                );


                            $accountCode =
                                $account['account_code']
                                ?? $account['code']
                                ?? '';


                            $accountName =
                                $account['account_name']
                                ?? $account['name']
                                ?? '';


                            $accountType =
                                $account['account_type']
                                ?? $account['type']
                                ?? '';


                            $accountBalance =
                                (float)(
                                    $account['balance']
                                    ?? $account['current_balance']
                                    ?? 0
                                );


                            $accountStatus =
                                strtolower(
                                    trim(
                                        $account['status']
                                        ?? 'active'
                                    )
                                );


                            $formattedType =
                                ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $accountType
                                    )
                                );


                            $statusClass =
                                match ($accountStatus) {

                                    'active' =>
                                        'status-active',

                                    'inactive' =>
                                        'status-inactive',

                                    'suspended' =>
                                        'status-suspended',

                                    default =>
                                        'status-default'

                                };


                            $editData = [
                                'id' =>
                                    $accountId,

                                'name' =>
                                    $accountName,

                                'type' =>
                                    $accountType,

                                'balance' =>
                                    $accountBalance,

                                'status' =>
                                    $accountStatus
                            ];

                            ?>


                            <tr>


                                <!-- CODE -->

                                <td>

                                    <span class="account-code">

                                        <?= htmlspecialchars(
                                            $accountCode ?: '-',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                </td>


                                <!-- ACCOUNT -->

                                <td>


                                    <div
                                        class="
                                            account-name-cell
                                        "
                                    >


                                        <div
                                            class="
                                                account-name-icon
                                            "
                                        >

                                            $

                                        </div>


                                        <span
                                            class="account-name"
                                        >

                                            <?= htmlspecialchars(
                                                $accountName ?: '-',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </span>


                                    </div>


                                </td>


                                <!-- TYPE -->

                                <td>

                                    <span
                                        class="
                                            account-type-badge
                                        "
                                    >

                                        <?= htmlspecialchars(
                                            $formattedType,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                </td>


                                <!-- BALANCE -->

                                <td>

                                    <span
                                        class="account-balance"
                                    >

                                        ₱<?= number_format(
                                            $accountBalance,
                                            2
                                        ) ?>

                                    </span>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="
                                            status-pill
                                            <?= htmlspecialchars(
                                                $statusClass,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        "
                                    >

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $accountStatus
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                </td>


                                <!-- ACTIONS -->

                                <td>


                                    <div
                                        class="
                                            account-row-actions
                                        "
                                    >


                                        <!-- EDIT -->

                                        <button
                                            type="button"
                                            class="table-action edit-account-btn"
                                            data-account-id="<?= $accountId ?>"
                                            data-account-name="<?= htmlspecialchars(
                                                $accountName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            data-account-type="<?= htmlspecialchars(
                                                $accountType,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            data-account-balance="<?= htmlspecialchars(
                                                (string)$accountBalance,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            data-account-status="<?= htmlspecialchars(
                                                $accountStatus,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                            Edit

                                        </button>


                                        <!-- DELETE -->

                                        <form
                                            method="POST"
                                            action="index.php?url=accounts/delete"
                                            style="display:inline;"
                                            onsubmit="
                                                return confirm(
                                                    'Are you sure you want to delete this account?'
                                                );
                                            "
                                        >


                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= $accountId ?>"
                                            >


                                            <button
                                                type="submit"
                                                class="
                                                    table-action
                                                    table-action-delete
                                                "
                                            >

                                                Delete

                                            </button>


                                        </form>


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


<!-- =========================================================
     CREATE ACCOUNT MODAL
========================================================== -->

<div
    class="modal-overlay"
    id="createAccountModal"
    onclick="closeCreateModal(event)"
>


    <div
        class="modal"
        onclick="event.stopPropagation();"
    >


        <div class="modal-header">


            <div>

                <h2>
                    Create Account
                </h2>


                <p>
                    Add a new business account.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                onclick="closeCreateModal()"
            >

                &times;

            </button>


        </div>


        <form
            method="POST"
            action="index.php?url=accounts/store"
        >


            <div class="form-group">

                <label
                    for="create_account_name"
                >
                    Account Name
                </label>


                <input
                    type="text"
                    id="create_account_name"
                    name="account_name"
                    placeholder="Example: Cash"
                    required
                >

            </div>


            <div class="form-group">

                <label
                    for="create_account_type"
                >
                    Account Type
                </label>


                <select
                    id="create_account_type"
                    name="account_type"
                    required
                >

                    <option value="">
                        Select Account Type
                    </option>

                    <option value="asset">
                        Asset
                    </option>

                    <option value="liability">
                        Liability
                    </option>

                    <option value="equity">
                        Equity
                    </option>

                    <option value="income">
                        Income
                    </option>

                    <option value="expense">
                        Expense
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label
                    for="create_balance"
                >
                    Opening Balance
                </label>


                <input
                    type="number"
                    id="create_balance"
                    name="balance"
                    min="0"
                    step="0.01"
                    value="0.00"
                    placeholder="0.00"
                >

            </div>


            <div class="modal-footer">


                <button
                    type="button"
                    class="
                        modal-btn
                        modal-btn-secondary
                    "
                    onclick="closeCreateModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="
                        modal-btn
                        modal-btn-primary
                    "
                >

                    Create Account

                </button>


            </div>


        </form>


    </div>


</div>


<!-- =========================================================
     EDIT ACCOUNT MODAL
========================================================== -->

<div
    class="modal-overlay"
    id="editAccountModal"
    onclick="closeEditModal(event)"
>


    <div
        class="modal"
        onclick="event.stopPropagation();"
    >


        <div class="modal-header">


            <div>

                <h2>
                    Edit Account
                </h2>


                <p>
                    Update the account information.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                onclick="closeEditModal()"
            >

                &times;

            </button>


        </div>


        <form
            method="POST"
            action="index.php?url=accounts/update"
        >


            <input
                type="hidden"
                name="id"
                id="edit_account_id"
            >


            <div class="form-group">

                <label
                    for="edit_account_name"
                >
                    Account Name
                </label>


                <input
                    type="text"
                    id="edit_account_name"
                    name="account_name"
                    required
                >

            </div>


            <div class="form-group">

                <label
                    for="edit_account_type"
                >
                    Account Type
                </label>


                <select
                    id="edit_account_type"
                    name="account_type"
                    required
                >

                    <option value="">
                        Select Account Type
                    </option>

                    <option value="asset">
                        Asset
                    </option>

                    <option value="liability">
                        Liability
                    </option>

                    <option value="equity">
                        Equity
                    </option>

                    <option value="income">
                        Income
                    </option>

                    <option value="expense">
                        Expense
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label
                    for="edit_balance"
                >
                    Balance
                </label>


                <input
                    type="number"
                    id="edit_balance"
                    name="balance"
                    min="0"
                    step="0.01"
                    required
                >

            </div>


            <div class="form-group">

                <label
                    for="edit_status"
                >
                    Status
                </label>


                <select
                    id="edit_status"
                    name="status"
                    required
                >

                    <option value="active">
                        Active
                    </option>

                    <option value="inactive">
                        Inactive
                    </option>

                </select>

            </div>


            <div class="modal-footer">


                <button
                    type="button"
                    class="
                        modal-btn
                        modal-btn-secondary
                    "
                    onclick="closeEditModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="
                        modal-btn
                        modal-btn-primary
                    "
                >

                    Save Changes

                </button>


            </div>


        </form>


    </div>


</div>


<!-- =========================================================
     ADJUST BALANCE MODAL
========================================================== -->

<div
    class="modal-overlay"
    id="adjustBalanceModal"
    onclick="closeAdjustModal(event)"
>


    <div
        class="modal"
        onclick="event.stopPropagation();"
    >


        <div class="modal-header">


            <div>

                <h2>
                    Adjust Balance
                </h2>


                <p>
                    Increase or decrease an account balance.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                onclick="closeAdjustModal()"
            >

                &times;

            </button>


        </div>


        <form
            method="POST"
            action="index.php?url=accounts/adjust-balance"
        >


            <div class="form-group">

                <label
                    for="adjust_account_id"
                >
                    Account
                </label>


                <select
                    id="adjust_account_id"
                    name="account_id"
                    required
                >

                    <option value="">
                        Select Account
                    </option>


                    <?php foreach (
                        $accounts as $account
                    ): ?>


                        <?php

                        $adjustId =
                            (int)(
                                $account['id']
                                ?? 0
                            );


                        $adjustName =
                            $account['account_name']
                            ?? $account['name']
                            ?? 'Account';


                        $adjustCode =
                            $account['account_code']
                            ?? $account['code']
                            ?? '';


                        $adjustBalance =
                            (float)(
                                $account['balance']
                                ?? $account['current_balance']
                                ?? 0
                            );

                        ?>


                        <option
                            value="<?= $adjustId ?>"
                        >

                            <?= htmlspecialchars(
                                $adjustCode,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            -

                            <?= htmlspecialchars(
                                $adjustName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            -

                            ₱<?= number_format(
                                $adjustBalance,
                                2
                            ) ?>

                        </option>


                    <?php endforeach; ?>


                </select>

            </div>


            <div class="form-group">

                <label
                    for="adjustment_type"
                >
                    Adjustment Type
                </label>


                <select
                    id="adjustment_type"
                    name="adjustment_type"
                    required
                >

                    <option value="">
                        Select Adjustment
                    </option>

                    <option value="add">
                        Increase Balance
                    </option>

                    <option value="subtract">
                        Decrease Balance
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label
                    for="adjustment_amount"
                >
                    Amount
                </label>


                <input
                    type="number"
                    id="adjustment_amount"
                    name="amount"
                    min="0.01"
                    step="0.01"
                    placeholder="0.00"
                    required
                >

            </div>


            <div class="form-group">

                <label
                    for="adjustment_reason"
                >
                    Reason
                </label>


                <textarea
                    id="adjustment_reason"
                    name="reason"
                    rows="4"
                    placeholder="Enter the reason for this adjustment..."
                    required
                ></textarea>

            </div>


            <div class="modal-footer">


                <button
                    type="button"
                    class="
                        modal-btn
                        modal-btn-secondary
                    "
                    onclick="closeAdjustModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="
                        modal-btn
                        modal-btn-primary
                    "
                >

                    Adjust Balance

                </button>


            </div>


        </form>


    </div>


</div>


<!-- =========================================================
     TRANSFER BALANCE MODAL
========================================================== -->

<div
    class="modal-overlay"
    id="transferBalanceModal"
    onclick="closeTransferModal(event)"
>


    <div
        class="modal"
        onclick="event.stopPropagation();"
    >


        <div class="modal-header">


            <div>

                <h2>
                    Transfer Balance
                </h2>


                <p>
                    Transfer money from one account to another.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                onclick="closeTransferModal()"
            >

                &times;

            </button>


        </div>


        <form
            method="POST"
            action="index.php?url=accounts/transfer-balance"
        >


            <div class="form-group">

                <label
                    for="transfer_from_id"
                >
                    From Account
                </label>


                <select
                    id="transfer_from_id"
                    name="from_account_id"
                    required
                >

                    <option value="">
                        Select Source Account
                    </option>


                    <?php foreach (
                        $accounts as $account
                    ): ?>


                        <?php

                        $fromId =
                            (int)(
                                $account['id']
                                ?? 0
                            );


                        $fromName =
                            $account['account_name']
                            ?? $account['name']
                            ?? 'Account';


                        $fromCode =
                            $account['account_code']
                            ?? $account['code']
                            ?? '';


                        $fromBalance =
                            (float)(
                                $account['balance']
                                ?? $account['current_balance']
                                ?? 0
                            );

                        ?>


                        <option
                            value="<?= $fromId ?>"
                        >

                            <?= htmlspecialchars(
                                $fromCode,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            -

                            <?= htmlspecialchars(
                                $fromName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            -

                            ₱<?= number_format(
                                $fromBalance,
                                2
                            ) ?>

                        </option>


                    <?php endforeach; ?>


                </select>

            </div>


            <div class="form-group">

                <label
                    for="transfer_to_id"
                >
                    To Account
                </label>


                <select
                    id="transfer_to_id"
                    name="to_account_id"
                    required
                >

                    <option value="">
                        Select Destination Account
                    </option>


                    <?php foreach (
                        $accounts as $account
                    ): ?>


                        <?php

                        $toId =
                            (int)(
                                $account['id']
                                ?? 0
                            );


                        $toName =
                            $account['account_name']
                            ?? $account['name']
                            ?? 'Account';


                        $toCode =
                            $account['account_code']
                            ?? $account['code']
                            ?? '';

                        ?>


                        <option
                            value="<?= $toId ?>"
                        >

                            <?= htmlspecialchars(
                                $toCode,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            -

                            <?= htmlspecialchars(
                                $toName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </option>


                    <?php endforeach; ?>


                </select>

            </div>


            <div class="form-group">

                <label
                    for="transfer_amount"
                >
                    Transfer Amount
                </label>


                <input
                    type="number"
                    id="transfer_amount"
                    name="amount"
                    min="0.01"
                    step="0.01"
                    placeholder="0.00"
                    required
                >

            </div>


            <div class="form-group">

                <label
                    for="transfer_description"
                >
                    Description
                </label>


                <textarea
                    id="transfer_description"
                    name="description"
                    rows="4"
                    placeholder="Enter the reason for this transfer..."
                    required
                ></textarea>

            </div>


            <div class="modal-footer">


                <button
                    type="button"
                    class="
                        modal-btn
                        modal-btn-secondary
                    "
                    onclick="closeTransferModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="
                        modal-btn
                        modal-btn-primary
                    "
                >

                    Transfer Balance

                </button>


            </div>


        </form>


    </div>


</div>


<!-- =========================================================
     JAVASCRIPT
========================================================== -->

<script>


/*
|--------------------------------------------------------------------------
| CREATE ACCOUNT
|--------------------------------------------------------------------------
*/

function openCreateModal()
{

    const modal =
        document.getElementById(
            'createAccountModal'
        );


    if (modal) {

        modal.classList.add('active');

        const nameInput =
            document.getElementById(
                'create_account_name'
            );

        if (nameInput) {

            setTimeout(
                function() {

                    nameInput.focus();

                },
                100
            );

        }

    }

}


function closeCreateModal(event)
{

    if (
        event &&
        event.target !== event.currentTarget
    ) {

        return;

    }


    const modal =
        document.getElementById(
            'createAccountModal'
        );


    if (modal) {

        modal.classList.remove('active');

    }

}


/*
|--------------------------------------------------------------------------
| EDIT ACCOUNT
|--------------------------------------------------------------------------
*/

function openEditModal(
    id,
    name,
    type,
    balance,
    status
)
{

    const idInput =
        document.getElementById(
            'edit_account_id'
        );

    const nameInput =
        document.getElementById(
            'edit_account_name'
        );

    const typeInput =
        document.getElementById(
            'edit_account_type'
        );

    const balanceInput =
        document.getElementById(
            'edit_balance'
        );

    const statusInput =
        document.getElementById(
            'edit_status'
        );


    if (idInput) {

        idInput.value = id;

    }


    if (nameInput) {

        nameInput.value = name;

    }


    if (typeInput) {

        typeInput.value = type;

    }


    if (balanceInput) {

        balanceInput.value = balance;

    }


    if (statusInput) {

        statusInput.value = status;

    }


    const modal =
        document.getElementById(
            'editAccountModal'
        );


    if (modal) {

        modal.classList.add('active');

    }

}


function closeEditModal(event)
{

    if (
        event &&
        event.target !== event.currentTarget
    ) {

        return;

    }


    const modal =
        document.getElementById(
            'editAccountModal'
        );


    if (modal) {

        modal.classList.remove('active');

    }

}


/*
|--------------------------------------------------------------------------
| EDIT BUTTONS
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll(
        '.edit-account-btn'
    )
    .forEach(
        function(button)
        {

            button.addEventListener(
                'click',
                function()
                {

                    openEditModal(

                        this.dataset.accountId,

                        this.dataset.accountName,

                        this.dataset.accountType,

                        this.dataset.accountBalance,

                        this.dataset.accountStatus

                    );

                }
            );

        }
    );


/*
|--------------------------------------------------------------------------
| ADJUST BALANCE
|--------------------------------------------------------------------------
*/

function openAdjustModal()
{

    const modal =
        document.getElementById(
            'adjustBalanceModal'
        );


    if (modal) {

        modal.classList.add('active');

    }

}


function closeAdjustModal(event)
{

    if (
        event &&
        event.target !== event.currentTarget
    ) {

        return;

    }


    const modal =
        document.getElementById(
            'adjustBalanceModal'
        );


    if (modal) {

        modal.classList.remove('active');

    }

}


/*
|--------------------------------------------------------------------------
| TRANSFER BALANCE
|--------------------------------------------------------------------------
*/

function openTransferModal()
{

    const modal =
        document.getElementById(
            'transferBalanceModal'
        );


    if (modal) {

        modal.classList.add('active');

    }

}


function closeTransferModal(event)
{

    if (
        event &&
        event.target !== event.currentTarget
    ) {

        return;

    }


    const modal =
        document.getElementById(
            'transferBalanceModal'
        );


    if (modal) {

        modal.classList.remove('active');

    }

}


/*
|--------------------------------------------------------------------------
| PREVENT SAME ACCOUNT TRANSFER
|--------------------------------------------------------------------------
*/

const transferFrom =
    document.getElementById(
        'transfer_from_id'
    );


const transferTo =
    document.getElementById(
        'transfer_to_id'
    );


if (
    transferFrom &&
    transferTo
) {

    transferFrom.addEventListener(
        'change',
        function()
        {

            const selected =
                this.value;


            Array.from(
                transferTo.options
            ).forEach(
                function(option)
                {

                    if (
                        option.value !== ''
                    ) {

                        option.disabled =
                            option.value ===
                            selected;

                    }

                }
            );


            if (
                transferTo.value ===
                selected
            ) {

                transferTo.value = '';

            }

        }
    );

}


/*
|--------------------------------------------------------------------------
| ESCAPE KEY
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'keydown',
    function(event)
    {

        if (
            event.key === 'Escape'
        ) {

            closeCreateModal();

            closeEditModal();

            closeAdjustModal();

            closeTransferModal();

        }

    }
);


</script>


</body>

</html>