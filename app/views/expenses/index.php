<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $user ?? ($_SESSION['user'] ?? []);

$expenses = $expenses ?? [];

$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;

unset($_SESSION['success']);
unset($_SESSION['error']);

$currentUrl = $currentUrl ?? ($_GET['url'] ?? 'expenses');

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

</head>

<body>


<?php require BASE_PATH . '/app/views/layouts/sidebar.php'; ?>


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

                <a
                    href="index.php?url=expenses/create"
                    class="btn btn-primary"
                >
                    + Add Expense
                </a>

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


                <!-- =========================================
                     EMPTY STATE
                ========================================== -->

                <div class="empty-state">

                    <h3>
                        No Expenses Found
                    </h3>


                    <p>
                        You have not recorded any expenses yet.
                    </p>


                    <br>


                    <a
                        href="index.php?url=expenses/create"
                        class="btn btn-primary"
                    >
                        + Add Your First Expense
                    </a>

                </div>


            <?php else: ?>


                <!-- =========================================
                     TABLE
                ========================================== -->

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
                                Description
                            </th>

                            <th>
                                Amount
                            </th>

                            <th>
                                Paid By
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


                            <!-- DESCRIPTION -->

                            <td>

                                <?= !empty(
                                    $expense['description']
                                )
                                    ? htmlspecialchars(
                                        $expense['description']
                                    )
                                    : '<span style="color:#9ca3af;">
                                        —
                                       </span>'
                                ?>

                            </td>


                            <!-- AMOUNT -->

                            <td>

                                ₱<?= number_format(
                                    (float) (
                                        $expense['amount']
                                        ?? 0
                                    ),
                                    2
                                ) ?>

                            </td>


                            <!-- PAID BY -->

                            <td>

                                <?= htmlspecialchars(
                                    $expense['paid_by']
                                    ?? '-'
                                ) ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php

                                $status =
                                    $expense['status']
                                    ?? 'active';

                                $statusClass =
                                    strtolower($status);

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
                                    href="index.php?url=expenses/edit&id=<?= (int) $expense['id'] ?>"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>


                                <a
                                    href="index.php?url=expenses/delete&id=<?= (int) $expense['id'] ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm(
                                        'Are you sure you want to delete this expense?'
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


</body>

</html>
