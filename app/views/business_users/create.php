<?php

$user = Auth::user();

$business = Auth::business();

$tenantRole = Auth::tenantRole();

$error = $_SESSION['error'] ?? null;

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
        Add User | Loan Management
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
            Add User
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
                    Add Business User
                </h1>

                <p>
                    Create a new user for
                    <?= htmlspecialchars(
                        $business['name']
                        ?? 'your business'
                    ) ?>.
                </p>

            </div>


            <div>

                <a
                    href="index.php?url=business-users"
                    class="btn btn-secondary"
                >
                    Back to Users
                </a>

            </div>

        </div>


        <?php if ($error): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

                <?php if (!empty($_SESSION['error_details'])): ?>

                    <br><br>

                    <small>
                        <?= htmlspecialchars(
                            $_SESSION['error_details']
                        ) ?>
                    </small>

                    <?php unset($_SESSION['error_details']); ?>

                <?php endif; ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             FORM
        ================================================== -->

        <div class="form-card">


            <h2>
                User Information
            </h2>


            <form
                method="POST"
                action="index.php?url=business-users/store"
            >


                <div class="form-grid">


                    <!-- FULL NAME -->

                    <div class="form-group">

                        <label for="full_name">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            placeholder="Enter full name"
                            required
                        >

                    </div>


                    <!-- USERNAME -->

                    <div class="form-group">

                        <label for="username">
                            Username
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter username"
                            required
                        >

                    </div>


                    <!-- EMAIL -->

                    <div class="form-group">

                        <label for="email">
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter email address"
                            required
                        >

                    </div>


                    <!-- PASSWORD -->

                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimum 8 characters"
                            minlength="8"
                            required
                        >

                    </div>


                    <!-- ROLE -->

                    <div class="form-group">

                        <label for="role">
                            Business Role
                        </label>

                        <select
                            id="role"
                            name="role"
                            required
                        >

                            <option value="staff">
                                Staff
                            </option>

                            <option value="loan_officer">
                                Loan Officer
                            </option>

                            <option value="cashier">
                                Cashier
                            </option>

                            <?php if ($tenantRole === 'owner'): ?>

                                <option value="admin">
                                    Admin
                                </option>

                            <?php endif; ?>

                        </select>

                    </div>


                </div>


                <!-- =================================================
                     ACTIONS
                ================================================== -->

                <div
                    style="
                        margin-top:25px;
                        display:flex;
                        gap:10px;
                    "
                >

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Create User
                    </button>


                    <a
                        href="index.php?url=business-users"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                </div>


            </form>


        </div>


    </div>

</div>


</body>

</html>