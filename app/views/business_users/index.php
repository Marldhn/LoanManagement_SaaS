<?php

$user = Auth::user();

$business = Auth::business();

$tenantRole = Auth::tenantRole();

$success = $_SESSION['success'] ?? null;

$error = $_SESSION['error'] ?? null;

unset($_SESSION['success']);
unset($_SESSION['error']);

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
        Users | Loan Management
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>


<?php

require APP_PATH . '/views/layouts/sidebar.php';

?>


<div class="main-content">


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <nav class="navbar">

        <div class="page-title">
            Users
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
                    $tenantRole
                    ?? 'Administrator'
                ) ?>

            </span>

        </div>

    </nav>


    <!-- =====================================================
         CONTENT
    ====================================================== -->

    <div class="container">


        <div class="page-header">

            <div>

                <h1>
                    Business Users
                </h1>

                <p>
                    Manage users and their access to
                    <?= htmlspecialchars(
                        $business['name']
                        ?? 'your business'
                    ) ?>.
                </p>

            </div>


            <div>

                <a
                    href="index.php?url=business-users/create"
                    class="btn btn-primary"
                >
                    + Add User
                </a>

            </div>

        </div>


        <!-- =================================================
             SUCCESS
        ================================================== -->

        <?php if ($success): ?>

            <div class="alert alert-success">

                <?= htmlspecialchars($success) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             ERROR
        ================================================== -->

        <?php if ($error): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             TABLE
        ================================================== -->

        <div class="table-container">

            <?php if (!empty($users)): ?>

                <table>

                    <thead>

                        <tr>

                            <th>
                                Name
                            </th>

                            <th>
                                Username
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Created
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($users as $businessUser): ?>

                        <tr>


                            <td>

                                <?= htmlspecialchars(
                                    $businessUser['full_name']
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $businessUser['username']
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $businessUser['email']
                                ) ?>

                            </td>


                            <td>

                                <span class="status status-active">

                                    <?= htmlspecialchars(
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $businessUser['tenant_role']
                                            )
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <td>

                                <?php

                                $statusClass =
                                    $businessUser['membership_status']
                                    === 'active'
                                        ? 'status-active'
                                        : 'status-inactive';

                                ?>

                                <span
                                    class="status <?= $statusClass ?>"
                                >

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $businessUser[
                                                'membership_status'
                                            ]
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    date(
                                        'M d, Y',
                                        strtotime(
                                            $businessUser['created_at']
                                        )
                                    )
                                ) ?>

                            </td>


                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            <?php else: ?>


                <div class="empty-state">

                    <h3>
                        No Users Found
                    </h3>

                    <p>
                        You haven't added any users to this
                        business yet.
                    </p>

                </div>


            <?php endif; ?>

        </div>


    </div>

</div>


</body>

</html>