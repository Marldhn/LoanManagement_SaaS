
<?php

$user = $user ?? Auth::user();

$business = $business ?? Auth::business();

$tenantRole = $tenantRole ?? Auth::tenantRole();

$currentUrl = $currentUrl ?? 'accounts/create';

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
        Add Account | Loan Management
    </title>

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


    <nav class="navbar">


        <div class="page-title">

            Add Account

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


        <div class="page-header">


            <div>

                <h1>
                    Add Account
                </h1>


                <p>
                    Create a new account for
                    <?= htmlspecialchars(
                        $business['name']
                        ?? 'your business'
                    ) ?>.
                </p>


            </div>


            <div>

                <a
                    href="index.php?url=accounts"
                    class="btn btn-secondary"
                >
                    ← Back to Accounts
                </a>

            </div>


        </div>


        <?php if (!empty($error)): ?>


            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>


        <?php endif; ?>


        <?php if (!empty($errors) && is_array($errors)): ?>


            <div class="alert alert-danger">

                <ul style="margin: 0; padding-left: 20px;">

                    <?php foreach ($errors as $fieldError): ?>

                        <li>
                            <?= htmlspecialchars($fieldError) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>


        <?php endif; ?>


        <div class="form-card">


            <h2>
                Account Information
            </h2>


            <form
                method="POST"
                action="index.php?url=accounts/store"
            >


                <div class="form-grid">


                    <!-- ACCOUNT CODE -->

                    <div class="form-group">

                        <label for="account_code">
                            Account Code
                        </label>

                        <input
                            type="text"
                            id="account_code"
                            name="account_code"
                            value="<?= htmlspecialchars(
                                $_POST['account_code']
                                ?? ''
                            ) ?>"
                            placeholder="e.g. 1000"
                            required
                        >

                    </div>


                    <!-- ACCOUNT TYPE -->

                    <div class="form-group">

                        <label for="account_type">
                            Account Type
                        </label>

                        <select
                            id="account_type"
                            name="account_type"
                            required
                        >

                            <option value="">
                                Select Account Type
                            </option>

                            <option
                                value="asset"
                                <?= (
                                    ($_POST['account_type'] ?? '')
                                    === 'asset'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Asset
                            </option>

                            <option
                                value="liability"
                                <?= (
                                    ($_POST['account_type'] ?? '')
                                    === 'liability'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Liability
                            </option>

                            <option
                                value="equity"
                                <?= (
                                    ($_POST['account_type'] ?? '')
                                    === 'equity'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Equity
                            </option>

                            <option
                                value="income"
                                <?= (
                                    ($_POST['account_type'] ?? '')
                                    === 'income'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Income
                            </option>

                            <option
                                value="expense"
                                <?= (
                                    ($_POST['account_type'] ?? '')
                                    === 'expense'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Expense
                            </option>

                        </select>

                    </div>


                    <!-- ACCOUNT NAME -->

                    <div class="form-group">

                        <label for="account_name">
                            Account Name
                        </label>

                        <input
                            type="text"
                            id="account_name"
                            name="account_name"
                            value="<?= htmlspecialchars(
                                $_POST['account_name']
                                ?? ''
                            ) ?>"
                            placeholder="e.g. Cash on Hand"
                            required
                        >

                    </div>


                    <!-- OPENING BALANCE -->

                    <div class="form-group">

                        <label for="opening_balance">
                            Opening Balance
                        </label>

                        <input
                            type="number"
                            id="opening_balance"
                            name="opening_balance"
                            value="<?= htmlspecialchars(
                                $_POST['opening_balance']
                                ?? '0.00'
                            ) ?>"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                        >

                    </div>


                    <!-- DESCRIPTION -->

                    <div class="form-group form-grid-full">

                        <label for="description">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Enter account description..."
                        ><?= htmlspecialchars(
                            $_POST['description']
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <!-- STATUS -->

                    <div class="form-group">

                        <label for="status">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                        >

                            <option
                                value="active"
                                <?= (
                                    ($_POST['status'] ?? 'active')
                                    === 'active'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Active
                            </option>

                            <option
                                value="inactive"
                                <?= (
                                    ($_POST['status'] ?? '')
                                    === 'inactive'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                </div>


                <br>


                <div
                    style="
                        display: flex;
                        gap: 10px;
                        justify-content: flex-end;
                    "
                >


                    <a
                        href="index.php?url=accounts"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>


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


</div>


</body>

</html>
