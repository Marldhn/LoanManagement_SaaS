<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$success = $_SESSION['loan_success'] ?? '';
$error = $_SESSION['loan_error'] ?? '';

unset(
    $_SESSION['loan_success'],
    $_SESSION['loan_error']
);

$loanName =
    $loan['loan_number']
    ?? ('Loan #' . ($loan['id'] ?? ''));

$borrowerName =
    $loan['borrower_name']
    ?? 'Borrower';

$totalPayable =
    (float)($loan['total_payable'] ?? 0);

$totalPaid =
    (float)($totalPaid ?? 0);

$remainingBalance =
    max(
        0,
        $totalPayable - $totalPaid
    );

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Make Payment</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="container">

    <div class="page-header">

        <div>

            <h1>
                Make Loan Payment
            </h1>

            <p>
                Record a payment for this loan.
            </p>

        </div>

        <a
            href="index.php?url=loans"
            class="btn btn-secondary"
        >
            ← Back to Loans
        </a>

    </div>


    <?php if ($success): ?>

        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <div class="card">

        <div class="card-header">

            <h2>
                <?= htmlspecialchars($loanName) ?>
            </h2>

        </div>


        <div class="card-body">

            <div class="loan-summary">

                <div>
                    <strong>Borrower</strong>

                    <span>
                        <?= htmlspecialchars($borrowerName) ?>
                    </span>
                </div>


                <div>
                    <strong>Total Payable</strong>

                    <span>
                        ₱<?= number_format($totalPayable, 2) ?>
                    </span>
                </div>


                <div>
                    <strong>Total Paid</strong>

                    <span>
                        ₱<?= number_format($totalPaid, 2) ?>
                    </span>
                </div>


                <div>
                    <strong>Remaining Balance</strong>

                    <span>
                        ₱<?= number_format($remainingBalance, 2) ?>
                    </span>
                </div>

            </div>


            <form
                method="POST"
                action="index.php?url=loans/payment/store"
            >

                <input
                    type="hidden"
                    name="loan_id"
                    value="<?= (int)$loan['id'] ?>"
                >


                <div class="form-group">

                    <label>
                        Payment Schedule
                    </label>

                    <select
                        name="schedule_id"
                        id="schedule_id"
                    >

                        <option value="">
                            General Loan Payment
                        </option>

                        <?php foreach ($schedule as $item): ?>

                            <?php

                            $scheduleRemaining =
                                max(
                                    0,
                                    (float)$item['total_due']
                                    -
                                    (float)$item['paid_amount']
                                );

                            ?>

                            <?php if ($scheduleRemaining > 0): ?>

                                <option
                                    value="<?= (int)$item['id'] ?>"
                                    data-remaining="<?= htmlspecialchars(
                                        $scheduleRemaining
                                    ) ?>"
                                >

                                    Installment
                                    <?= (int)$item['installment_number'] ?>

                                    -
                                    Due
                                    <?= htmlspecialchars(
                                        $item['due_date']
                                    ) ?>

                                    -
                                    ₱<?= number_format(
                                        $scheduleRemaining,
                                        2
                                    ) ?>

                                </option>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Payment Date
                    </label>

                    <input
                        type="date"
                        name="payment_date"
                        value="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Payment Amount
                    </label>

                    <input
                        type="number"
                        name="amount"
                        id="payment_amount"
                        min="0.01"
                        step="0.01"
                        max="<?= htmlspecialchars(
                            $remainingBalance
                        ) ?>"
                        placeholder="0.00"
                        required
                    >

                    <small>
                        Remaining loan balance:
                        ₱<?= number_format(
                            $remainingBalance,
                            2
                        ) ?>
                    </small>

                </div>


                <div class="form-group">

                    <label>
                        Receive Payment Into
                    </label>

                    <select
                        name="account_id"
                        required
                    >

                        <option value="">
                            Select Account
                        </option>

                        <?php foreach ($accounts as $account): ?>

                            <option
                                value="<?= (int)$account['id'] ?>"
                            >

                                <?= htmlspecialchars(
                                    $account['account_name']
                                ) ?>

                                -
                                ₱<?= number_format(
                                    (float)$account['balance'],
                                    2
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Notes
                    </label>

                    <textarea
                        name="notes"
                        rows="4"
                        placeholder="Payment notes..."
                    ></textarea>

                </div>


                <div class="form-actions">

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
                        Record Payment
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function()
    {
        const schedule =
            document.getElementById(
                'schedule_id'
            );

        const amount =
            document.getElementById(
                'payment_amount'
            );

        if (!schedule || !amount) {
            return;
        }

        schedule.addEventListener(
            'change',
            function()
            {
                const selected =
                    schedule.options[
                        schedule.selectedIndex
                    ];

                const remaining =
                    selected.dataset.remaining;

                if (remaining) {

                    amount.value =
                        parseFloat(
                            remaining
                        ).toFixed(2);

                } else {

                    amount.value = '';

                }
            }
        );
    }
);

</script>

</body>
</html>