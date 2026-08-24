<?php

/*
|--------------------------------------------------------------------------
| Start Session
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Login Error
|--------------------------------------------------------------------------
*/

$error = $error ?? null;

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
        Login | Loan Management SaaS
    </title>


    <!-- =====================================================
         GLOBAL SYSTEM CSS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body class="login-page">


<!-- =========================================================
     LOGIN CONTAINER
========================================================= -->

<div class="login-container">


    <!-- =====================================================
         LOGIN CARD
    ====================================================== -->

    <div class="login-card">


        <!-- =================================================
             LOGO / HEADER
        ================================================== -->

        <div class="logo">

            <h1>
                Loan Management
            </h1>

            <p>
                Sign in to your account
            </p>

        </div>


        <!-- =================================================
             ERROR MESSAGE
        ================================================== -->

        <?php if (!empty($error)): ?>

            <div class="alert">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             LOGIN FORM
        ================================================== -->

        <form
            method="POST"
            action="index.php?url=auth/login"
        >


            <!-- =============================================
                 USERNAME / EMAIL
            ============================================== -->

            <div class="form-group">

                <label for="login">

                    Username or Email

                </label>


                <input
                    type="text"
                    id="login"
                    name="login"
                    placeholder="Enter username or email"
                    autocomplete="username"
                    required
                >

            </div>


            <!-- =============================================
                 PASSWORD
            ============================================== -->

            <div class="form-group">

                <label for="password">

                    Password

                </label>


                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <!-- =============================================
                 LOGIN BUTTON
            ============================================== -->

            <button type="submit">

                Sign In

            </button>


            <!-- =============================================
                 REGISTER LINK
            ============================================== -->

            <div class="register-link">

                <span>
                    Don't have an account?
                </span>


                <a
                    href="index.php?url=auth/register"
                >
                    Create a business account
                </a>

            </div>


        </form>


        <!-- =================================================
             FOOTER
        ================================================== -->

        <div class="footer">

            Loan Management SaaS

        </div>


    </div>


</div>


</body>

</html>