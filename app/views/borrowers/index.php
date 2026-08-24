<?php

$user = $user ?? Auth::user();

$business = $business ?? Auth::business();

$tenantRole = $tenantRole ?? Auth::tenantRole();

$currentUrl = $currentUrl ?? 'borrowers';

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
        Borrowers | Loan Management
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

            Borrowers

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
                    Borrowers
                </h1>

                <p>
                    Manage borrowers for
                    <?= htmlspecialchars(
                        $business['name']
                        ?? 'your business'
                    ) ?>.
                </p>

            </div>


            <div>

                <a
                    href="index.php?url=borrowers/create"
                    class="btn btn-primary"
                >
                    + Add Borrower
                </a>

            </div>

        </div>


        <?php if (empty($borrowers)): ?>


            <div class="form-card">

                <div class="empty-state">

                    <h3>
                        No Borrowers Found
                    </h3>

                    <p>
                        You haven't added any borrowers yet.
                    </p>


                    <br>


                    <a
                        href="index.php?url=borrowers/create"
                        class="btn btn-primary"
                    >
                        Add Your First Borrower
                    </a>

                </div>

            </div>


        <?php else: ?>


            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Code
                            </th>

                            <th>
                                Borrower
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Monthly Income
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
                        $borrowers
                        as $borrower
                    ): ?>


                        <tr>


                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $borrower['borrower_code']
                                    ) ?>
                                </strong>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    trim(
                                        $borrower['first_name']
                                        . ' '
                                        . ($borrower['middle_name'] ?? '')
                                        . ' '
                                        . $borrower['last_name']
                                    )
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $borrower['phone']
                                    ?? '-'
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $borrower['email']
                                    ?? '-'
                                ) ?>

                            </td>


                            <td>

                                ₱<?= number_format(
                                    (float) (
                                        $borrower['monthly_income']
                                        ?? 0
                                    ),
                                    2
                                ) ?>

                            </td>


                            <td>


                                <?php

                                $status =
                                    $borrower['status']
                                    ?? 'active';

                                $statusClass =
                                    'status-' . $status;

                                ?>


                                <span
                                    class="status
                                        <?= htmlspecialchars(
                                            $statusClass
                                        ) ?>"
                                >

                                    <?= htmlspecialchars(
                                        ucfirst($status)
                                    ) ?>

                                </span>


                            </td>


                            <td>

                                <a
                                    href="index.php?url=borrowers/edit&id=<?= (int) $borrower['id'] ?>"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>


                                <a
                                    href="index.php?url=borrowers/delete&id=<?= (int) $borrower['id'] ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this borrower?');"
                                >
                                    Delete
                                </a>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php endif; ?>


    </div>

</div>


</body>

</html>