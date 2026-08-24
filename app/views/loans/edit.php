<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loan = $loan ?? [];

$borrowers = $borrowers ?? [];

$categories = $categories ?? [];

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
        Edit Loan | Loan Management SaaS
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>


<?php

require BASE_PATH . '/app/views/layouts/sidebar.php';

?>


<div class="main-content">


    <!-- NAVBAR -->

    <div class="navbar">

        <div class="page-title">
            Edit Loan
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


    <!-- CONTENT -->

    <div class="container">


        <!-- PAGE HEADER -->

        <div class="page-header">

            <div>

                <h1>
                    Edit Loan
                </h1>

                <p>
                    Update the loan information below.
                </p>

            </div>


            <a
                href="index.php?url=loans"
                class="btn btn-secondary"
            >
                ← Back to Loans
            </a>

        </div>


        <!-- FORM -->

        <div class="form-card">

            <h2>

                Loan:

                <?= htmlspecialchars(
                    $loan['loan_number']
                    ?? '-'
                ) ?>

            </h2>


            <form
                method="POST"
                action="index.php?url=loans/update"
            >


                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) ($loan['id'] ?? 0) ?>"
                >


                <div class="form-grid">


                    <!-- BORROWER -->

                    <div class="form-group">

                        <label for="borrower_id">
                            Borrower
                        </label>


                        <select
                            id="borrower_id"
                            name="borrower_id"
                            required
                        >

                            <option value="">
                                Select Borrower
                            </option>


                            <?php foreach (
                                $borrowers
                                as $borrower
                            ): ?>

                                <?php

                                $borrowerId =
                                    (int) $borrower['id'];

                                $selected =
                                    $borrowerId ===
                                    (int) (
                                        $loan['borrower_id']
                                        ?? 0
                                    );

                                ?>

                                <option
                                    value="<?= $borrowerId ?>"
                                    <?= $selected
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars(
                                        $borrower['first_name']
                                        . ' '
                                        . $borrower['last_name']
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- CATEGORY -->

                    <div class="form-group">

                        <label for="category_id">
                            Loan Category
                        </label>


                        <select
                            id="category_id"
                            name="category_id"
                        >

                            <option value="">
                                Select Category
                            </option>


                            <?php foreach (
                                $categories
                                as $category
                            ): ?>

                                <?php

                                $categoryId =
                                    (int) $category['id'];

                                $selected =
                                    $categoryId ===
                                    (int) (
                                        $loan['category_id']
                                        ?? 0
                                    );

                                ?>

                                <option
                                    value="<?= $categoryId ?>"
                                    <?= $selected
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


                    <!-- PRINCIPAL -->

                    <div class="form-group">

                        <label for="principal_amount">
                            Principal Amount
                        </label>


                        <input
                            type="number"
                            id="principal_amount"
                            name="principal_amount"
                            step="0.01"
                            min="0"
                            value="<?= htmlspecialchars(
                                $loan['principal_amount']
                                ?? 0
                            ) ?>"
                            required
                        >

                    </div>


                    <!-- INTEREST -->

                    <div class="form-group">

                        <label for="interest_rate">
                            Interest Rate (%)
                        </label>


                        <input
                            type="number"
                            id="interest_rate"
                            name="interest_rate"
                            step="0.01"
                            min="0"
                            value="<?= htmlspecialchars(
                                $loan['interest_rate']
                                ?? 0
                            ) ?>"
                            required
                        >

                    </div>


                    <!-- INTEREST TYPE -->

                    <div class="form-group">

                        <label for="interest_type">
                            Interest Type
                        </label>


                        <select
                            id="interest_type"
                            name="interest_type"
                        >

                            <option
                                value="flat"
                                <?= (
                                    ($loan['interest_type']
                                    ?? '') === 'flat'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Flat
                            </option>

                            <option
                                value="reducing_balance"
                                <?= (
                                    ($loan['interest_type']
                                    ?? '') === 'reducing_balance'
                                )
                                    ? 'selected'
                                    : '' ?>
                            >
                                Reducing Balance
                            </option>

                        </select>

                    </div>


                    <!-- TERM -->

                    <div class="form-group">

                        <label for="term">
                            Loan Term
                        </label>


                        <input
                            type="number"
                            id="term"
                            name="term"
                            min="1"
                            value="<?= htmlspecialchars(
                                $loan['term']
                                ?? 1
                            ) ?>"
                            required
                        >

                    </div>


                    <!-- TERM UNIT -->

                    <div class="form-group">

                        <label for="term_unit">
                            Term Unit
                        </label>


                        <select
                            id="term_unit"
                            name="term_unit"
                        >

                            <?php

                            $termUnit =
                                $loan['term_unit']
                                ?? 'months';

                            ?>

                            <option
                                value="days"
                                <?= $termUnit === 'days'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Days
                            </option>

                            <option
                                value="weeks"
                                <?= $termUnit === 'weeks'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Weeks
                            </option>

                            <option
                                value="months"
                                <?= $termUnit === 'months'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Months
                            </option>

                            <option
                                value="years"
                                <?= $termUnit === 'years'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Years
                            </option>

                        </select>

                    </div>


                    <!-- PAYMENT FREQUENCY -->

                    <div class="form-group">

                        <label for="payment_frequency">
                            Payment Frequency
                        </label>


                        <?php

                        $frequency =
                            $loan['payment_frequency']
                            ?? 'monthly';

                        ?>

                        <select
                            id="payment_frequency"
                            name="payment_frequency"
                        >

                            <option
                                value="daily"
                                <?= $frequency === 'daily'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Daily
                            </option>

                            <option
                                value="weekly"
                                <?= $frequency === 'weekly'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Weekly
                            </option>

                            <option
                                value="monthly"
                                <?= $frequency === 'monthly'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Monthly
                            </option>

                            <option
                                value="quarterly"
                                <?= $frequency === 'quarterly'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Quarterly
                            </option>

                            <option
                                value="yearly"
                                <?= $frequency === 'yearly'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Yearly
                            </option>

                        </select>

                    </div>


                    <!-- PROCESSING FEE -->

                    <div class="form-group">

                        <label for="processing_fee">
                            Processing Fee
                        </label>


                        <input
                            type="number"
                            id="processing_fee"
                            name="processing_fee"
                            step="0.01"
                            min="0"
                            value="<?= htmlspecialchars(
                                $loan['processing_fee']
                                ?? 0
                            ) ?>"
                        >

                    </div>


                    <!-- INSURANCE -->

                    <div class="form-group">

                        <label for="insurance_fee">
                            Insurance Fee
                        </label>


                        <input
                            type="number"
                            id="insurance_fee"
                            name="insurance_fee"
                            step="0.01"
                            min="0"
                            value="<?= htmlspecialchars(
                                $loan['insurance_fee']
                                ?? 0
                            ) ?>"
                        >

                    </div>


                    <!-- OTHER CHARGES -->

                    <div class="form-group">

                        <label for="other_charges">
                            Other Charges
                        </label>


                        <input
                            type="number"
                            id="other_charges"
                            name="other_charges"
                            step="0.01"
                            min="0"
                            value="<?= htmlspecialchars(
                                $loan['other_charges']
                                ?? 0
                            ) ?>"
                        >

                    </div>


                    <!-- START DATE -->

                    <div class="form-group">

                        <label for="start_date">
                            Start Date
                        </label>


                        <input
                            type="date"
                            id="start_date"
                            name="start_date"
                            value="<?= htmlspecialchars(
                                $loan['start_date']
                                ?? ''
                            ) ?>"
                            required
                        >

                    </div>


                    <!-- MATURITY -->

                    <div class="form-group">

                        <label for="maturity_date">
                            Maturity Date
                        </label>


                        <input
                            type="date"
                            id="maturity_date"
                            name="maturity_date"
                            value="<?= htmlspecialchars(
                                $loan['maturity_date']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <!-- STATUS -->

                    <div class="form-group">

                        <label for="status">
                            Status
                        </label>


                        <?php

                        $status =
                            $loan['status']
                            ?? 'pending';

                        ?>

                        <select
                            id="status"
                            name="status"
                        >

                            <option
                                value="pending"
                                <?= $status === 'pending'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Pending
                            </option>

                            <option
                                value="approved"
                                <?= $status === 'approved'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Approved
                            </option>

                            <option
                                value="active"
                                <?= $status === 'active'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Active
                            </option>

                            <option
                                value="completed"
                                <?= $status === 'completed'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Completed
                            </option>

                            <option
                                value="rejected"
                                <?= $status === 'rejected'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Rejected
                            </option>

                        </select>

                    </div>


                    <!-- NOTES -->

                    <div class="form-group form-grid-full">

                        <label for="notes">
                            Notes
                        </label>


                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            placeholder="Additional notes..."
                        ><?= htmlspecialchars(
                            $loan['notes']
                            ?? ''
                        ) ?></textarea>

                    </div>


                </div>


                <!-- BUTTONS -->

                <div
                    style="
                        margin-top: 25px;
                        display: flex;
                        gap: 10px;
                        justify-content: flex-end;
                    "
                >

                    <a
                        href="index.php?url=loans"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update Loan
                    </button>

                </div>


            </form>

        </div>

    </div>

</div>


</body>

</html>