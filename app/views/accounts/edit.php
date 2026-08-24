<?php

$user = $user ?? Auth::user();

$business = $business ?? Auth::business();

$tenantRole = $tenantRole ?? Auth::tenantRole();

$currentUrl = $currentUrl ?? 'accounts';

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
    Edit Account | Loan Management
</title>

<link
    rel="stylesheet"
    href="assets/css/style.css"
>
```

</head>

<body>

<?php

require APP_PATH .
    '/views/layouts/sidebar.php';

?>

<div class="main-content">

```
<nav class="navbar">


    <div class="page-title">

        Edit Account

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
                Edit Account
            </h1>

            <p>
                Update the account information
                for your business.
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


    <?php if (!empty($_SESSION['error'])): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars(
                $_SESSION['error']
            ) ?>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>


    <?php if (!empty($_SESSION['success'])): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars(
                $_SESSION['success']
            ) ?>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>


    <div class="form-card">


        <h2>
            Account Information
        </h2>


        <form
            method="POST"
            action="index.php?url=accounts/update"
        >


            <input
                type="hidden"
                name="id"
                value="<?= (int) (
                    $account['id']
                    ?? 0
                ) ?>"
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
                            $account['account_code']
                            ?? $account['code']
                            ?? ''
                        ) ?>"
                        required
                    >

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
                            $account['account_name']
                            ?? $account['name']
                            ?? ''
                        ) ?>"
                        required
                    >

                </div>


                <!-- ACCOUNT TYPE -->

                <div class="form-group">

                    <label for="account_type">
                        Account Type
                    </label>

                    <?php

                    $selectedType =
                        $account['account_type']
                        ?? $account['type']
                        ?? '';

                    ?>

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
                            <?= $selectedType === 'asset'
                                ? 'selected'
                                : '' ?>
                        >
                            Asset
                        </option>

                        <option
                            value="liability"
                            <?= $selectedType === 'liability'
                                ? 'selected'
                                : '' ?>
                        >
                            Liability
                        </option>

                        <option
                            value="equity"
                            <?= $selectedType === 'equity'
                                ? 'selected'
                                : '' ?>
                        >
                            Equity
                        </option>

                        <option
                            value="income"
                            <?= $selectedType === 'income'
                                ? 'selected'
                                : '' ?>
                        >
                            Income
                        </option>

                        <option
                            value="expense"
                            <?= $selectedType === 'expense'
                                ? 'selected'
                                : '' ?>
                        >
                            Expense
                        </option>

                    </select>

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label for="status">
                        Status
                    </label>

                    <?php

                    $selectedStatus =
                        $account['status']
                        ?? 'active';

                    ?>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <option
                            value="active"
                            <?= $selectedStatus === 'active'
                                ? 'selected'
                                : '' ?>
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            <?= $selectedStatus === 'inactive'
                                ? 'selected'
                                : '' ?>
                        >
                            Inactive
                        </option>

                    </select>

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
                        $account['description']
                        ?? ''
                    ) ?></textarea>

                </div>


            </div>


            <div
                style="
                    display:flex;
                    justify-content:flex-end;
                    gap:10px;
                    margin-top:20px;
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
                    Update Account
                </button>


            </div>


        </form>


    </div>


</div>

</div>

</body>

</html>
