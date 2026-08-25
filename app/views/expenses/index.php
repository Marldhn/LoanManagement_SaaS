<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$user =
    $user
    ?? ($_SESSION['user'] ?? []);


$expenses =
    $expenses ?? [];


$categories =
    $categories ?? [];


$accounts =
    $accounts ?? [];


$success =
    $success
    ?? ($_SESSION['success'] ?? null);


$error =
    $error
    ?? ($_SESSION['error'] ?? null);


unset(
    $_SESSION['success'],
    $_SESSION['error']
);


$currentUrl =
    $currentUrl
    ?? ($_GET['url'] ?? 'expenses');

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
        Expenses | Loan Management SaaS
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /* =====================================================
           EXPENSE MODAL
        ===================================================== */

        .expense-modal-overlay {

            position: fixed;

            inset: 0;

            background: rgba(15, 23, 42, 0.60);

            display: none;

            align-items: center;

            justify-content: center;

            padding: 20px;

            z-index: 9999;

            backdrop-filter: blur(3px);
        }


        .expense-modal-overlay.active {

            display: flex;
        }


        .expense-modal {

            width: 100%;

            max-width: 620px;

            max-height: 90vh;

            overflow-y: auto;

            background: #ffffff;

            border-radius: 16px;

            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.25);
        }


        .expense-modal-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 22px 24px;

            border-bottom: 1px solid #e5e7eb;
        }


        .expense-modal-header h2 {

            margin: 0;

            font-size: 20px;

            color: #111827;
        }


        .expense-modal-close {

            width: 36px;

            height: 36px;

            border: none;

            background: #f3f4f6;

            border-radius: 8px;

            cursor: pointer;

            font-size: 22px;

            color: #6b7280;

            display: flex;

            align-items: center;

            justify-content: center;
        }


        .expense-modal-close:hover {

            background: #e5e7eb;

            color: #111827;
        }


        .expense-modal-body {

            padding: 24px;
        }


        .expense-form-group {

            margin-bottom: 18px;
        }


        .expense-form-group label {

            display: block;

            margin-bottom: 7px;

            font-weight: 600;

            font-size: 14px;

            color: #374151;
        }


        .expense-form-group input,
        .expense-form-group select,
        .expense-form-group textarea {

            width: 100%;

            box-sizing: border-box;

            border: 1px solid #d1d5db;

            border-radius: 9px;

            padding: 11px 13px;

            font-size: 14px;

            background: #ffffff;

            color: #111827;

            outline: none;

            transition:
                border-color .2s,
                box-shadow .2s;
        }


        .expense-form-group input:focus,
        .expense-form-group select:focus,
        .expense-form-group textarea:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(
                    37,
                    99,
                    235,
                    0.10
                );
        }


        .expense-form-group textarea {

            min-height: 90px;

            resize: vertical;
        }


        .expense-account-info {

            display: none;

            margin-top: 8px;

            padding: 12px 14px;

            background: #f8fafc;

            border: 1px solid #e2e8f0;

            border-radius: 9px;

            font-size: 13px;
        }


        .expense-account-info.active {

            display: block;
        }


        .expense-account-balance {

            font-weight: 700;

            color: #111827;
        }


        .expense-remaining {

            margin-top: 4px;

            color: #64748b;
        }


        .expense-remaining strong {

            color: #16a34a;
        }


        .expense-remaining.insufficient strong {

            color: #dc2626;
        }


        .expense-modal-footer {

            display: flex;

            align-items: center;

            justify-content: flex-end;

            gap: 10px;

            padding: 18px 24px;

            border-top: 1px solid #e5e7eb;
        }


        .expense-btn {

            border: none;

            border-radius: 9px;

            padding: 10px 17px;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;
        }


        .expense-btn-cancel {

            background: #f3f4f6;

            color: #374151;
        }


        .expense-btn-cancel:hover {

            background: #e5e7eb;
        }


        .expense-btn-save {

            background: #2563eb;

            color: #ffffff;
        }


        .expense-btn-save:hover {

            background: #1d4ed8;
        }


        .expense-btn-save:disabled {

            opacity: .6;

            cursor: not-allowed;
        }


        /* =====================================================
           ACCOUNT BADGE
        ===================================================== */

        .expense-account-badge {

            display: inline-flex;

            align-items: center;

            padding: 5px 9px;

            border-radius: 7px;

            background: #eff6ff;

            color: #1d4ed8;

            font-size: 13px;

            font-weight: 600;
        }


        .expense-no-account {

            color: #9ca3af;
        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 700px) {

            .expense-modal {

                max-height: 95vh;

                border-radius: 12px;
            }


            .expense-modal-header,
            .expense-modal-body,
            .expense-modal-footer {

                padding-left: 18px;

                padding-right: 18px;
            }

        }

    </style>

</head>


<body>


<?php

require BASE_PATH .
    '/app/views/layouts/sidebar.php';

?>


<div class="main-content">


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <div class="navbar">

        <div class="page-title">

            Expenses

        </div>


        <div class="user-info">

            <span class="user-name">

                <?= htmlspecialchars(
                    $user['full_name']
                    ?? $user['username']
                    ?? 'Administrator'
                ) ?>

            </span>


            <span class="badge">

                <?= htmlspecialchars(
                    $user['role']
                    ?? 'Administrator'
                ) ?>

            </span>

        </div>

    </div>


    <!-- =====================================================
         CONTENT
    ====================================================== -->

    <div class="container">


        <!-- =================================================
             PAGE HEADER
        ================================================== -->

        <div class="page-header">

            <div>

                <h1>
                    Expenses
                </h1>

                <p>
                    Manage and track business expenses.
                </p>

            </div>


            <div>

                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="openExpenseModal()"
                >
                    + Add Expense
                </button>

            </div>

        </div>


        <!-- =================================================
             ALERTS
        ================================================== -->

        <?php if (!empty($success)): ?>

            <div class="alert alert-success">

                <?= htmlspecialchars($success) ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($error)): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             EXPENSE TABLE
        ================================================== -->

        <div class="table-container">


            <?php if (empty($expenses)): ?>


                <div class="empty-state">

                    <h3>
                        No Expenses Found
                    </h3>


                    <p>
                        You have not recorded any expenses yet.
                    </p>


                    <br>


                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="openExpenseModal()"
                    >
                        + Add Your First Expense
                    </button>

                </div>


            <?php else: ?>


                <table>

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Account
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Amount
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
                        $expenses
                        as $index => $expense
                    ): ?>


                        <tr>


                            <!-- NUMBER -->

                            <td>

                                <?= $index + 1 ?>

                            </td>


                            <!-- DATE -->

                            <td>

                                <?= htmlspecialchars(
                                    $expense['expense_date']
                                    ?? ''
                                ) ?>

                            </td>


                            <!-- CATEGORY -->

                            <td>

                                <?= htmlspecialchars(
                                    $expense['category_name']
                                    ?? $expense['category']
                                    ?? 'Uncategorized'
                                ) ?>

                            </td>


                            <!-- ACCOUNT -->

                            <td>

                                <?php if (
                                    !empty(
                                        $expense['account_name']
                                    )
                                ): ?>

                                    <span
                                        class="expense-account-badge"
                                    >

                                        <?= htmlspecialchars(
                                            $expense['account_name']
                                        ) ?>

                                    </span>

                                <?php else: ?>

                                    <span
                                        class="expense-no-account"
                                    >
                                        —
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- DESCRIPTION -->

                            <td>

                                <?php if (
                                    !empty(
                                        $expense['description']
                                    )
                                ): ?>

                                    <?= htmlspecialchars(
                                        $expense['description']
                                    ) ?>

                                <?php else: ?>

                                    <span
                                        style="color:#9ca3af;"
                                    >
                                        —
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- AMOUNT -->

                            <td>

                                <strong>

                                    ₱<?= number_format(
                                        (float)(
                                            $expense['amount']
                                            ?? 0
                                        ),
                                        2
                                    ) ?>

                                </strong>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php

                                $status =
                                    $expense['status']
                                    ?? 'active';

                                $statusClass =
                                    strtolower(
                                        $status
                                    );

                                ?>


                                <span
                                    class="status status-<?= htmlspecialchars(
                                        $statusClass
                                    ) ?>"
                                >

                                    <?= htmlspecialchars(
                                        ucfirst($status)
                                    ) ?>

                                </span>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <a
                                    href="index.php?url=expenses/edit&id=<?= (int)$expense['id'] ?>"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>


                                <a
                                    href="index.php?url=expenses/delete&id=<?= (int)$expense['id'] ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm(
                                        'Are you sure you want to delete this expense? The amount will be returned to the account. Continue?'
                                    );"
                                >
                                    Delete
                                </a>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>


            <?php endif; ?>


        </div>


    </div>


</div>


<!-- =========================================================
     ADD EXPENSE MODAL
========================================================= -->

<div
    id="expenseModal"
    class="expense-modal-overlay"
    onclick="handleExpenseModalOutsideClick(event)"
>


    <div
        class="expense-modal"
        onclick="event.stopPropagation()"
    >


        <!-- =================================================
             MODAL HEADER
        ================================================== -->

        <div class="expense-modal-header">

            <h2>
                Add Expense
            </h2>


            <button
                type="button"
                class="expense-modal-close"
                onclick="closeExpenseModal()"
                aria-label="Close"
            >
                ×
            </button>

        </div>


        <!-- =================================================
             FORM
        ================================================== -->

        <form
            method="POST"
            action="index.php?url=expenses/store"
            id="expenseForm"
        >


            <div class="expense-modal-body">


                <!-- =========================================
                     DATE
                ========================================== -->

                <div class="expense-form-group">

                    <label for="expense_date">

                        Expense Date

                    </label>


                    <input
                        type="date"
                        id="expense_date"
                        name="expense_date"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>


                <!-- =========================================
                     ACCOUNT
                ========================================== -->

                <div class="expense-form-group">

                    <label for="account_id">

                        Paid From Account

                    </label>


                    <select
                        id="account_id"
                        name="account_id"
                        required
                        onchange="updateExpenseAccountInfo()"
                    >

                        <option value="">

                            Select account

                        </option>


                        <?php foreach (
                            $accounts
                            as $account
                        ): ?>

                            <option
                                value="<?= (int)$account['id'] ?>"
                                data-balance="<?= htmlspecialchars(
                                    (string)$account['balance']
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $account['account_name']
                                ) ?>

                                —
                                ₱<?= number_format(
                                    (float)$account['balance'],
                                    2
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>


                    <div
                        id="expenseAccountInfo"
                        class="expense-account-info"
                    >

                        <div>

                            Available Balance:

                            <span
                                id="expenseAvailableBalance"
                                class="expense-account-balance"
                            >
                                ₱0.00
                            </span>

                        </div>


                        <div
                            id="expenseRemaining"
                            class="expense-remaining"
                        >

                            Balance after expense:

                            <strong
                                id="expenseRemainingBalance"
                            >
                                ₱0.00
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- =========================================
                     CATEGORY
                ========================================== -->

                <div class="expense-form-group">

                    <label for="category_id">

                        Category

                    </label>


                    <select
                        id="category_id"
                        name="category_id"
                    >

                        <option value="">

                            Uncategorized

                        </option>


                        <?php foreach (
                            $categories
                            as $category
                        ): ?>

                            <option
                                value="<?= (int)$category['id'] ?>"
                            >

                                <?= htmlspecialchars(
                                    $category['name']
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- =========================================
                     DESCRIPTION
                ========================================== -->

                <div class="expense-form-group">

                    <label for="description">

                        Description

                    </label>


                    <input
                        type="text"
                        id="description"
                        name="description"
                        placeholder="e.g. Internet bill"
                        maxlength="255"
                        required
                    >

                </div>


                <!-- =========================================
                     AMOUNT
                ========================================== -->

                <div class="expense-form-group">

                    <label for="amount">

                        Amount

                    </label>


                    <input
                        type="number"
                        id="amount"
                        name="amount"
                        placeholder="0.00"
                        min="0.01"
                        step="0.01"
                        required
                        oninput="updateExpenseAccountInfo()"
                    >

                </div>


                <!-- =========================================
                     NOTES
                ========================================== -->

                <div class="expense-form-group">

                    <label for="notes">

                        Notes
                        <span
                            style="
                                color:#9ca3af;
                                font-weight:400;
                            "
                        >
                            (Optional)
                        </span>

                    </label>


                    <textarea
                        id="notes"
                        name="notes"
                        placeholder="Additional notes..."
                    ></textarea>

                </div>


            </div>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <div class="expense-modal-footer">

                <button
                    type="button"
                    class="expense-btn expense-btn-cancel"
                    onclick="closeExpenseModal()"
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    id="saveExpenseButton"
                    class="expense-btn expense-btn-save"
                >
                    Save Expense
                </button>

            </div>


        </form>


    </div>


</div>


<script>

/*
|--------------------------------------------------------------------------
| Open Expense Modal
|--------------------------------------------------------------------------
*/

function openExpenseModal()
{
    const modal =
        document.getElementById(
            'expenseModal'
        );

    if (!modal) {
        return;
    }

    modal.classList.add(
        'active'
    );


    document.body.style.overflow =
        'hidden';


    const dateInput =
        document.getElementById(
            'expense_date'
        );


    if (
        dateInput
        && !dateInput.value
    ) {

        dateInput.value =
            new Date()
                .toISOString()
                .split('T')[0];
    }
}


/*
|--------------------------------------------------------------------------
| Close Expense Modal
|--------------------------------------------------------------------------
*/

function closeExpenseModal()
{
    const modal =
        document.getElementById(
            'expenseModal'
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


/*
|--------------------------------------------------------------------------
| Click Outside Modal
|--------------------------------------------------------------------------
*/

function handleExpenseModalOutsideClick(
    event
)
{
    if (
        event.target.id
        === 'expenseModal'
    ) {

        closeExpenseModal();
    }
}


/*
|--------------------------------------------------------------------------
| ESC Key
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'keydown',
    function(event)
    {
        if (
            event.key === 'Escape'
        ) {

            closeExpenseModal();
        }
    }
);


/*
|--------------------------------------------------------------------------
| Update Account Information
|--------------------------------------------------------------------------
*/

function updateExpenseAccountInfo()
{
    const accountSelect =
        document.getElementById(
            'account_id'
        );


    const amountInput =
        document.getElementById(
            'amount'
        );


    const info =
        document.getElementById(
            'expenseAccountInfo'
        );


    const availableBalance =
        document.getElementById(
            'expenseAvailableBalance'
        );


    const remainingBalance =
        document.getElementById(
            'expenseRemainingBalance'
        );


    const remainingContainer =
        document.getElementById(
            'expenseRemaining'
        );


    const saveButton =
        document.getElementById(
            'saveExpenseButton'
        );


    if (
        !accountSelect
        || !info
    ) {

        return;
    }


    const selectedOption =
        accountSelect.options[
            accountSelect.selectedIndex
        ];


    if (
        !selectedOption
        || !selectedOption.value
    ) {

        info.classList.remove(
            'active'
        );

        if (saveButton) {

            saveButton.disabled =
                false;
        }

        return;
    }


    const balance =
        parseFloat(
            selectedOption.dataset.balance
            || '0'
        );


    const amount =
        parseFloat(
            amountInput.value
            || '0'
        );


    const remaining =
        balance - amount;


    info.classList.add(
        'active'
    );


    availableBalance.textContent =
        '₱'
        + balance.toLocaleString(
            'en-PH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );


    remainingBalance.textContent =
        '₱'
        + remaining.toLocaleString(
            'en-PH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );


    if (
        remaining < 0
    ) {

        remainingContainer.classList.add(
            'insufficient'
        );


        if (saveButton) {

            saveButton.disabled =
                true;
        }

    } else {

        remainingContainer.classList.remove(
            'insufficient'
        );


        if (saveButton) {

            saveButton.disabled =
                false;
        }
    }
}


/*
|--------------------------------------------------------------------------
| Form Validation
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        'expenseForm'
    )
    ?.addEventListener(
        'submit',
        function(event)
        {
            const account =
                document.getElementById(
                    'account_id'
                );


            const amount =
                parseFloat(
                    document.getElementById(
                        'amount'
                    ).value
                    || '0'
                );


            if (
                !account.value
            ) {

                event.preventDefault();

                alert(
                    'Please select the account used for this expense.'
                );

                return;
            }


            if (
                amount <= 0
            ) {

                event.preventDefault();

                alert(
                    'Please enter a valid expense amount.'
                );

                return;
            }


            const selectedOption =
                account.options[
                    account.selectedIndex
                ];


            const balance =
                parseFloat(
                    selectedOption.dataset.balance
                    || '0'
                );


            if (
                amount > balance
            ) {

                event.preventDefault();

                alert(
                    'Insufficient balance in the selected account.'
                );

                return;
            }
        }
    );

</script>


</body>

</html>