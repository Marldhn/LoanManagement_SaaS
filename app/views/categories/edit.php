<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $user ?? ($_SESSION['user'] ?? []);

$category = $category ?? [];

$error = $_SESSION['error'] ?? null;

unset($_SESSION['error']);

$currentUrl = $currentUrl ?? 'categories/edit';

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
        Edit Category | Loan Management SaaS
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>


<?php require BASE_PATH . '/app/views/layouts/sidebar.php'; ?>


<div class="main-content">


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <div class="navbar">

        <div class="page-title">
            Categories
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


        <!-- PAGE HEADER -->

        <div class="page-header">

            <div>

                <h1>
                    Edit Category
                </h1>

                <p>
                    Update the category information.
                </p>

            </div>


            <div>

                <a
                    href="index.php?url=categories"
                    class="btn btn-secondary"
                >
                    ← Back
                </a>

            </div>

        </div>


        <!-- ERROR -->

        <?php if (!empty($error)): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             FORM
        ================================================== -->

        <div class="form-card">

            <h2>
                Category Information
            </h2>


            <form
                method="POST"
                action="index.php?url=categories/update"
            >


                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) (
                        $category['id'] ?? 0
                    ) ?>"
                >


                <div class="form-grid">


                    <!-- NAME -->

                    <div class="form-group">

                        <label for="name">
                            Category Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= htmlspecialchars(
                                $category['name'] ?? ''
                            ) ?>"
                            maxlength="100"
                            required
                        >

                    </div>


                    <!-- TYPE -->

                    <div class="form-group">

                        <label for="type">
                            Category Type
                        </label>

                        <select
                            id="type"
                            name="type"
                            required
                        >

                            <option
                                value="both"
                                <?= (
                                    ($category['type'] ?? '')
                                    === 'both'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Both Expenses & Loaning
                            </option>


                            <option
                                value="expense"
                                <?= (
                                    ($category['type'] ?? '')
                                    === 'expense'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Expense Only
                            </option>


                            <option
                                value="loan"
                                <?= (
                                    ($category['type'] ?? '')
                                    === 'loan'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Loaning Only
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
                            placeholder="Optional description"
                        ><?= htmlspecialchars(
                            $category['description'] ?? ''
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
                            required
                        >

                            <option
                                value="active"
                                <?= (
                                    ($category['status'] ?? '')
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
                                    ($category['status'] ?? '')
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


                <!-- ACTIONS -->

                <div
                    style="
                        display:flex;
                        justify-content:flex-end;
                        gap:10px;
                        margin-top:20px;
                    "
                >

                    <a
                        href="index.php?url=categories"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>


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

</div>


</body>

</html>