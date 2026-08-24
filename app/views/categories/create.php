<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = $error ?? null;

?>

<div class="main-content">

    <!-- =====================================================
         TOP NAVBAR
    ====================================================== -->

    <div class="navbar">

        <div class="page-title">
            Create Category
        </div>

        <div class="user-info">

            <span class="user-name">
                <?= htmlspecialchars(
                    $_SESSION['user']['full_name']
                    ?? $_SESSION['user']['username']
                    ?? 'Administrator'
                ) ?>
            </span>

            <span class="badge">
                <?= htmlspecialchars(
                    $_SESSION['user']['role']
                    ?? 'Administrator'
                ) ?>
            </span>

        </div>

    </div>


    <!-- =====================================================
         PAGE CONTENT
    ====================================================== -->

    <div class="container">


        <!-- PAGE HEADER -->

        <div class="page-header">

            <div>

                <h1>
                    Create Category
                </h1>

                <p>
                    Add a new category for expenses and loans.
                </p>

            </div>

            <div>

                <a
                    href="index.php?url=categories"
                    class="btn btn-secondary"
                >
                    ← Back to Categories
                </a>

            </div>

        </div>


        <!-- ERROR -->

        <?php if (!empty($error)): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- FORM CARD -->

        <div class="form-card">

            <h2>
                Category Information
            </h2>


            <form
                method="POST"
                action="index.php?url=categories/store"
            >

                <div class="form-grid">


                    <!-- CATEGORY NAME -->

                    <div class="form-group">

                        <label for="name">
                            Category Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= htmlspecialchars(
                                $_POST['name'] ?? ''
                            ) ?>"
                            placeholder="Enter category name"
                            required
                        >

                    </div>


                    <!-- CATEGORY TYPE -->

                    <div class="form-group">

                        <label for="type">
                            Category Type
                        </label>

                        <select
                            id="type"
                            name="type"
                            required
                        >

                            <option value="">
                                Select Type
                            </option>

                            <option
                                value="expense"
                                <?= (
                                    ($_POST['type'] ?? '')
                                    === 'expense'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Expense
                            </option>

                            <option
                                value="loan"
                                <?= (
                                    ($_POST['type'] ?? '')
                                    === 'loan'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Loan
                            </option>

                            <option
                                value="both"
                                <?= (
                                    ($_POST['type'] ?? '')
                                    === 'both'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Expense & Loan
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
                            placeholder="Enter category description (optional)"
                        ><?= htmlspecialchars(
                            $_POST['description'] ?? ''
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


                <!-- FORM ACTIONS -->

                <div
                    style="
                        display: flex;
                        justify-content: flex-end;
                        gap: 10px;
                        margin-top: 25px;
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
                        Create Category
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>