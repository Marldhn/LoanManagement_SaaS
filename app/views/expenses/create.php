<?php

$categories = $categories ?? [];

?>

<div class="main-content">

    <div class="navbar">

        <div class="page-title">
            Add Expense
        </div>

    </div>


    <div class="container">


        <!-- =====================================================
             PAGE HEADER
        ====================================================== -->

        <div class="page-header">

            <div>

                <h1>
                    Add Expense
                </h1>

                <p>
                    Record a new business expense.
                </p>

            </div>


            <div>

                <a
                    href="index.php?url=expenses"
                    class="btn btn-secondary"
                >
                    ← Back to Expenses
                </a>

            </div>

        </div>


        <!-- =====================================================
             ERROR
        ====================================================== -->

        <?php if (!empty($_SESSION['error'])): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($_SESSION['error']) ?>

            </div>

            <?php unset($_SESSION['error']); ?>

        <?php endif; ?>


        <!-- =====================================================
             FORM
        ====================================================== -->

        <div class="form-card">

            <h2>
                Expense Information
            </h2>


            <form
                method="POST"
                action="index.php?url=expenses/store"
            >


                <div class="form-grid">


                    <!-- DATE -->

                    <div class="form-group">

                        <label for="expense_date">
                            Expense Date
                        </label>

                        <input
                            type="date"
                            id="expense_date"
                            name="expense_date"
                            value="<?= htmlspecialchars(
                                $_POST['expense_date']
                                ?? date('Y-m-d')
                            ) ?>"
                            required
                        >

                    </div>


                    <!-- CATEGORY -->

                    <div class="form-group">

                        <label for="category_id">
                            Category
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            required
                        >

                            <option value="">
                                Select Category
                            </option>


                            <?php foreach ($categories as $category): ?>

                                <option
                                    value="<?= (int) $category['id'] ?>"
                                    <?= (
                                        isset($_POST['category_id'])
                                        &&
                                        $_POST['category_id']
                                        == $category['id']
                                    )
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars(
                                        $category['name']
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- AMOUNT -->

                    <div class="form-group">

                        <label for="amount">
                            Amount
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="amount"
                            name="amount"
                            placeholder="0.00"
                            value="<?= htmlspecialchars(
                                $_POST['amount']
                                ?? ''
                            ) ?>"
                            required
                        >

                    </div>


                    <!-- PAID BY -->

                    <div class="form-group">

                        <label for="paid_by">
                            Paid By
                        </label>

                        <input
                            type="text"
                            id="paid_by"
                            name="paid_by"
                            placeholder="e.g. Cash, Bank, Owner"
                            value="<?= htmlspecialchars(
                                $_POST['paid_by']
                                ?? ''
                            ) ?>"
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
                            placeholder="Enter expense description..."
                        ><?= htmlspecialchars(
                            $_POST['description']
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <!-- REFERENCE -->

                    <div class="form-group">

                        <label for="reference_no">
                            Reference Number
                        </label>

                        <input
                            type="text"
                            id="reference_no"
                            name="reference_no"
                            placeholder="Optional reference"
                            value="<?= htmlspecialchars(
                                $_POST['reference_no']
                                ?? ''
                            ) ?>"
                        >

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
                                value="cancelled"
                                <?= (
                                    ($_POST['status'] ?? '')
                                    === 'cancelled'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Cancelled
                            </option>

                        </select>

                    </div>


                </div>


                <!-- =================================================
                     BUTTONS
                ================================================== -->

                <div
                    style="
                        display:flex;
                        gap:10px;
                        margin-top:20px;
                    "
                >

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save Expense
                    </button>


                    <a
                        href="index.php?url=expenses"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                </div>


            </form>

        </div>

    </div>

</div>