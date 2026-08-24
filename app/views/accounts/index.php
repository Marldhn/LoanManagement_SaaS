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
|
| Calculate directly from the accounts loaded by the controller.
| This prevents the cards from showing 0 when the controller does
| not explicitly pass totalAssets / totalLiabilities.
|
*/

$totalAccounts = count($accounts);

$totalAssets = 0.00;

$totalLiabilities = 0.00;

$totalEquity = 0.00;

$totalIncome = 0.00;

$totalExpenses = 0.00;

$totalFunds = 0.00;


foreach ($accounts as $account) {

    $accountType =
        strtolower(
            trim(
                $account['account_type']
                ?? $account['type']
                ?? ''
            )
        );


    /*
    |--------------------------------------------------------------------------
    | BALANCE
    |--------------------------------------------------------------------------
    |
    | Support both possible column names:
    | balance
    | current_balance
    |
    */

    $balance = (float)(
        $account['balance']
        ?? $account['current_balance']
        ?? 0
    );


    /*
    |--------------------------------------------------------------------------
    | TOTAL FUNDS
    |--------------------------------------------------------------------------
    |
    | Total funds represents the balances of asset accounts.
    |
    */

    if ($accountType === 'asset') {

        $totalAssets += $balance;

    }


    /*
    |--------------------------------------------------------------------------
    | LIABILITIES
    |--------------------------------------------------------------------------
    */

    elseif ($accountType === 'liability') {

        $totalLiabilities += $balance;

    }


    /*
    |--------------------------------------------------------------------------
    | EQUITY
    |--------------------------------------------------------------------------
    */

    elseif ($accountType === 'equity') {

        $totalEquity += $balance;

    }


    /*
    |--------------------------------------------------------------------------
    | INCOME
    |--------------------------------------------------------------------------
    */

    elseif ($accountType === 'income') {

        $totalIncome += $balance;

    }


    /*
    |--------------------------------------------------------------------------
    | EXPENSE
    |--------------------------------------------------------------------------
    */

    elseif ($accountType === 'expense') {

        $totalExpenses += $balance;

    }

}


/*
|--------------------------------------------------------------------------
| NET BALANCE
|--------------------------------------------------------------------------
|
| Assets - Liabilities
|
*/

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


    <style>

        /*
        |--------------------------------------------------------------------------
        | MODAL
        |--------------------------------------------------------------------------
        */

        .modal-overlay {

            position: fixed;

            inset: 0;

            width: 100%;
            height: 100%;

            background: rgba(0, 0, 0, 0.55);

            display: none;

            align-items: center;
            justify-content: center;

            z-index: 9999;

            padding: 20px;

            box-sizing: border-box;

        }


        .modal-overlay.active {

            display: flex;

        }


        .modal {

            background: #ffffff;

            width: 100%;

            max-width: 550px;

            max-height: 90vh;

            overflow-y: auto;

            border-radius: 12px;

            padding: 25px;

            box-sizing: border-box;

            box-shadow:
                0 20px 50px
                rgba(0, 0, 0, 0.25);

        }


        .modal-header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;

        }


        .modal-header h2 {

            margin: 0 0 5px 0;

        }


        .modal-header p {

            margin: 0;

            color: #6b7280;

        }


        .modal-close {

            border: none;

            background: transparent;

            font-size: 28px;

            line-height: 1;

            cursor: pointer;

            color: #6b7280;

        }


        .modal-close:hover {

            color: #111827;

        }


        .modal-footer {

            display: flex;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 25px;

        }


        /*
        |--------------------------------------------------------------------------
        | FORM
        |--------------------------------------------------------------------------
        */

        .form-group {

            margin-bottom: 18px;

        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            font-weight: 600;

        }


        .form-group input,
        .form-group select,
        .form-group textarea {

            width: 100%;

            box-sizing: border-box;

        }


        /*
        |--------------------------------------------------------------------------
        | ACCOUNT SUMMARY
        |--------------------------------------------------------------------------
        */

        .account-summary-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
                );

            gap: 20px;

            margin-top: 25px;

        }


        .account-summary-card {

            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            padding: 20px;

            box-shadow:
                0 2px 8px
                rgba(0, 0, 0, 0.04);

        }


        .account-summary-title {

            font-size: 14px;

            color: #6b7280;

            margin-bottom: 8px;

        }


        .account-summary-value {

            font-size: 26px;

            font-weight: 700;

            color: #111827;

        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 900px) {

            .account-summary-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );

            }

        }


        @media (max-width: 600px) {

            .account-summary-grid {

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
                    Accounts
                </h1>


                <p>

                    Manage your business
                    accounts and balances.

                </p>


            </div>


            <div
                style="
                    display:flex;
                    gap:10px;
                    flex-wrap:wrap;
                    align-items:center;
                "
            >


                <!-- ADJUST -->

                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="openAdjustModal()"
                >

                    Adjust Balance

                </button>


                <!-- TRANSFER -->

                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="openTransferModal()"
                >

                    Transfer Balance

                </button>


                <!-- CREATE -->

                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="openCreateModal()"
                >

                    + Add Account

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

        <div class="account-summary-grid">


            <!-- TOTAL ACCOUNTS -->

            <div class="account-summary-card">

                <div class="account-summary-title">

                    Total Accounts

                </div>


                <div class="account-summary-value">

                    <?= number_format(
                        $totalAccounts
                    ) ?>

                </div>

            </div>


            <!-- TOTAL ASSETS / FUNDS -->

            <div class="account-summary-card">

                <div class="account-summary-title">

                    Total Assets / Funds

                </div>


                <div class="account-summary-value">

                    ₱<?= number_format(
                        $totalAssets,
                        2
                    ) ?>

                </div>

            </div>


            <!-- TOTAL LIABILITIES -->

            <div class="account-summary-card">

                <div class="account-summary-title">

                    Total Liabilities

                </div>


                <div class="account-summary-value">

                    ₱<?= number_format(
                        $totalLiabilities,
                        2
                    ) ?>

                </div>

            </div>


            <!-- NET BALANCE -->

            <div class="account-summary-card">

                <div class="account-summary-title">

                    Net Balance

                </div>


                <div class="account-summary-value">

                    ₱<?= number_format(
                        $netBalance,
                        2
                    ) ?>

                </div>

            </div>


        </div>


        <!-- =================================================
             ACCOUNTS TABLE
        ================================================== -->

        <?php if (empty($accounts)): ?>


            <div
                class="form-card"
                style="margin-top:30px;"
            >


                <div class="empty-state">


                    <h3>
                        No Accounts Found
                    </h3>


                    <p>

                        You haven't added any
                        accounts yet.

                    </p>


                    <br>


                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="openCreateModal()"
                    >

                        Add Your First Account

                    </button>


                </div>


            </div>


        <?php else: ?>


            <div
                class="table-container"
                style="margin-top:30px;"
            >


                <table>


                    <thead>

                        <tr>

                            <th>
                                Code
                            </th>

                            <th>
                                Account Name
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
                            $account['status']
                            ?? 'active';


                        $statusClass =
                            'status-' .
                            $accountStatus;

                        ?>


                        <tr>


                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $accountCode
                                        ?: '-'
                                    ) ?>

                                </strong>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $accountName
                                    ?: '-'
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $accountType
                                        )
                                    )
                                ) ?>

                            </td>


                            <td>

                                <strong>

                                    ₱<?= number_format(
                                        $accountBalance,
                                        2
                                    ) ?>

                                </strong>

                            </td>


                            <td>

                                <span
                                    class="status <?= htmlspecialchars(
                                        $statusClass
                                    ) ?>"
                                >

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $accountStatus
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <td
                                style="
                                    display:flex;
                                    gap:6px;
                                    flex-wrap:wrap;
                                "
                            >


                                <!-- EDIT -->

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    onclick="openEditModal(
                                        <?= $accountId ?>,
                                        <?= htmlspecialchars(
                                            json_encode(
                                                $accountName
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>,
                                        <?= htmlspecialchars(
                                            json_encode(
                                                $accountType
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>,
                                        <?= htmlspecialchars(
                                            json_encode(
                                                $accountBalance
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>,
                                        <?= htmlspecialchars(
                                            json_encode(
                                                $accountStatus
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    )"
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
                                        class="btn btn-danger"
                                    >

                                        Delete

                                    </button>

                                </form>


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


            <!-- ACCOUNT NAME -->

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


            <!-- ACCOUNT TYPE -->

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


            <!-- OPENING BALANCE -->

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
                    class="btn btn-secondary"
                    onclick="closeCreateModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
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


            <!-- ACCOUNT NAME -->

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


            <!-- ACCOUNT TYPE -->

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


            <!-- BALANCE -->

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


            <!-- STATUS -->

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
                    class="btn btn-secondary"
                    onclick="closeEditModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
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


            <!-- ACCOUNT -->

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
                                $adjustCode
                            ) ?>

                            -

                            <?= htmlspecialchars(
                                $adjustName
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


            <!-- ADJUSTMENT TYPE -->

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


            <!-- AMOUNT -->

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


            <!-- REASON -->

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
                    class="btn btn-secondary"
                    onclick="closeAdjustModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
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


            <!-- FROM ACCOUNT -->

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
                                $fromCode
                            ) ?>

                            -

                            <?= htmlspecialchars(
                                $fromName
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


            <!-- TO ACCOUNT -->

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
                                $toCode
                            ) ?>

                            -

                            <?= htmlspecialchars(
                                $toName
                            ) ?>

                        </option>


                    <?php endforeach; ?>


                </select>

            </div>


            <!-- AMOUNT -->

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


            <!-- DESCRIPTION -->

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
                    class="btn btn-secondary"
                    onclick="closeTransferModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
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


    if (modal)
    {

        modal.classList.add('active');

    }

}


function closeCreateModal(event)
{

    if (
        event &&
        event.target !== event.currentTarget
    )
    {

        return;

    }


    const modal =
        document.getElementById(
            'createAccountModal'
        );


    if (modal)
    {

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

    document.getElementById(
        'edit_account_id'
    ).value = id;


    document.getElementById(
        'edit_account_name'
    ).value = name;


    document.getElementById(
        'edit_account_type'
    ).value = type;


    document.getElementById(
        'edit_balance'
    ).value = balance;


    document.getElementById(
        'edit_status'
    ).value = status;


    const modal =
        document.getElementById(
            'editAccountModal'
        );


    if (modal)
    {

        modal.classList.add('active');

    }

}


function closeEditModal(event)
{

    if (
        event &&
        event.target !== event.currentTarget
    )
    {

        return;

    }


    const modal =
        document.getElementById(
            'editAccountModal'
        );


    if (modal)
    {

        modal.classList.remove('active');

    }

}


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


    if (modal)
    {

        modal.classList.add('active');

    }

}


function closeAdjustModal(event)
{

    if (
        event &&
        event.target !== event.currentTarget
    )
    {

        return;

    }


    const modal =
        document.getElementById(
            'adjustBalanceModal'
        );


    if (modal)
    {

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


    if (modal)
    {

        modal.classList.add('active');

    }

}


function closeTransferModal(event)
{

    if (
        event &&
        event.target !== event.currentTarget
    )
    {

        return;

    }


    const modal =
        document.getElementById(
            'transferBalanceModal'
        );


    if (modal)
    {

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
)
{

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
                    )
                    {

                        option.disabled =
                            option.value ===
                            selected;

                    }

                }
            );


            if (
                transferTo.value ===
                selected
            )
            {

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
        )
        {

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