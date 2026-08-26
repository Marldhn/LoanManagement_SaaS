
<?php

/*
|--------------------------------------------------------------------------
| Login View
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    <meta
        name="theme-color"
        content="#2563eb"
    >

    <title>
        Login | Loan Management
    </title>

    <style>

        /* =========================================================
           RESET
        ========================================================= */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }


        /* =========================================================
           BODY
        ========================================================= */

        body {

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 25px 15px;

            background:
                linear-gradient(
                    135deg,
                    #eef5ff 0%,
                    #f8fbff 45%,
                    #eef6ff 100%
                );

            color: #1e293b;

            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Arial,
                sans-serif;

            position: relative;

            overflow-x: hidden;
        }


        /* =========================================================
           BACKGROUND DECORATIONS
        ========================================================= */

        body::before {

            content: "";

            position: fixed;

            width: 520px;

            height: 520px;

            top: -220px;

            left: -180px;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(37, 99, 235, 0.15) 0%,
                    rgba(37, 99, 235, 0.06) 45%,
                    transparent 72%
                );

            pointer-events: none;
        }


        body::after {

            content: "";

            position: fixed;

            width: 600px;

            height: 600px;

            right: -260px;

            bottom: -280px;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(59, 130, 246, 0.14) 0%,
                    rgba(59, 130, 246, 0.05) 45%,
                    transparent 72%
                );

            pointer-events: none;
        }


        /* =========================================================
           BACKGROUND PATTERN
        ========================================================= */

        .background-pattern {

            position: fixed;

            inset: 0;

            pointer-events: none;

            overflow: hidden;

            opacity: 0.45;
        }


        .background-pattern span {

            position: absolute;

            display: block;

            border: 1px solid
                rgba(37, 99, 235, 0.08);

            border-radius: 50%;
        }


        .circle-one {

            width: 180px;

            height: 180px;

            top: 12%;

            left: 8%;
        }


        .circle-two {

            width: 300px;

            height: 300px;

            top: 28%;

            right: 5%;
        }


        .circle-three {

            width: 110px;

            height: 110px;

            bottom: 15%;

            left: 18%;
        }


        .circle-four {

            width: 220px;

            height: 220px;

            bottom: 5%;

            right: 25%;
        }


        /* =========================================================
           FLOATING FINANCE SYMBOLS
        ========================================================= */

        .money-symbol {

            position: fixed;

            color:
                rgba(37, 99, 235, 0.08);

            font-weight: 800;

            user-select: none;

            pointer-events: none;

            line-height: 1;
        }


        .money-one {

            top: 12%;

            right: 18%;

            font-size: 75px;

            transform: rotate(12deg);
        }


        .money-two {

            bottom: 18%;

            left: 8%;

            font-size: 55px;

            transform: rotate(-12deg);
        }


        .money-three {

            top: 35%;

            left: 4%;

            font-size: 42px;

            transform: rotate(8deg);
        }


        .money-four {

            bottom: 8%;

            right: 10%;

            font-size: 65px;

            transform: rotate(-8deg);
        }


        /* =========================================================
           LOGIN WRAPPER
        ========================================================= */

        .login-wrapper {

            width: 100%;

            max-width: 430px;

            position: relative;

            z-index: 10;
        }


        /* =========================================================
           BRAND
        ========================================================= */

        .brand {

            text-align: center;

            margin-bottom: 22px;
        }


        .brand-logo {

            width: 52px;

            height: 52px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin: 0 auto 12px;

            border-radius: 14px;

            background: #2563eb;

            color: #ffffff;

            font-size: 23px;

            font-weight: 800;

            box-shadow:
                0 5px 15px
                rgba(37, 99, 235, 0.18);
        }


        .brand h1 {

            margin: 0;

            color: #0f172a;

            font-size: 23px;

            font-weight: 800;

            letter-spacing: -0.5px;
        }


        .brand p {

            margin: 6px 0 0;

            color: #64748b;

            font-size: 13px;
        }


        /* =========================================================
           LOGIN CARD
        ========================================================= */

        .login-card {

            background:
                rgba(255, 255, 255, 0.96);

            border:
                1px solid
                rgba(226, 232, 240, 0.95);

            border-radius: 14px;

            padding: 30px;

            box-shadow:
                0 20px 50px
                rgba(15, 23, 42, 0.09),

                0 5px 15px
                rgba(37, 99, 235, 0.04);

            backdrop-filter: blur(10px);

            -webkit-backdrop-filter: blur(10px);
        }


        /* =========================================================
           HEADER
        ========================================================= */

        .login-header {

            margin-bottom: 24px;
        }


        .login-header h2 {

            margin: 0 0 6px;

            color: #0f172a;

            font-size: 21px;

            font-weight: 750;
        }


        .login-header p {

            margin: 0;

            color: #64748b;

            font-size: 13px;

            line-height: 1.5;
        }


        /* =========================================================
           ERROR
        ========================================================= */

        .login-error {

            display: flex;

            align-items: flex-start;

            gap: 9px;

            margin-bottom: 20px;

            padding: 11px 12px;

            border:
                1px solid #fecaca;

            border-radius: 8px;

            background: #fef2f2;

            color: #b91c1c;

            font-size: 12px;

            line-height: 1.5;
        }


        .error-icon {

            width: 18px;

            height: 18px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background: #fee2e2;

            font-size: 11px;

            font-weight: 800;
        }


        /* =========================================================
           FORM GROUP
        ========================================================= */

        .form-group {

            margin-bottom: 18px;
        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            color: #334155;

            font-size: 12px;

            font-weight: 650;
        }


        /* =========================================================
           INPUT
        ========================================================= */

        .input-wrapper {

            position: relative;
        }


        .input-icon {

            position: absolute;

            left: 13px;

            top: 50%;

            transform:
                translateY(-50%);

            color: #94a3b8;

            font-size: 14px;

            pointer-events: none;
        }


        .login-input {

            width: 100%;

            height: 46px;

            padding:
                0
                42px
                0
                38px;

            border:
                1px solid #cbd5e1;

            border-radius: 8px;

            outline: none;

            background: #ffffff;

            color: #0f172a;

            font-family: inherit;

            font-size: 13px;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }


        .login-input::placeholder {

            color: #94a3b8;
        }


        .login-input:hover {

            border-color: #94a3b8;
        }


        .login-input:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.10);
        }


        /* =========================================================
           PASSWORD TOGGLE
        ========================================================= */

        .password-toggle {

            position: absolute;

            right: 8px;

            top: 50%;

            transform:
                translateY(-50%);

            height: 32px;

            padding: 0 9px;

            border: 0;

            border-radius: 6px;

            background: transparent;

            color: #64748b;

            font-family: inherit;

            font-size: 11px;

            font-weight: 600;

            cursor: pointer;
        }


        .password-toggle:hover {

            background: #f1f5f9;

            color: #2563eb;
        }


        /* =========================================================
           OPTIONS
        ========================================================= */

        .form-options {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-top: 3px;

            margin-bottom: 22px;
        }


        .remember-me {

            display: flex;

            align-items: center;

            gap: 7px;

            color: #64748b;

            font-size: 11px;

            cursor: pointer;
        }


        .remember-me input {

            width: 14px;

            height: 14px;

            margin: 0;

            accent-color: #2563eb;

            cursor: pointer;
        }


        /* =========================================================
           LOGIN BUTTON
        ========================================================= */

        .login-button {

            width: 100%;

            height: 46px;

            border: 0;

            border-radius: 8px;

            background: #2563eb;

            color: #ffffff;

            font-family: inherit;

            font-size: 13px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 4px 10px
                rgba(37, 99, 235, 0.16);

            transition:
                background 0.2s ease,
                transform 0.15s ease,
                box-shadow 0.2s ease;
        }


        .login-button:hover {

            background: #1d4ed8;

            box-shadow:
                0 6px 14px
                rgba(37, 99, 235, 0.20);
        }


        .login-button:active {

            transform:
                translateY(1px);
        }


        .login-button:focus-visible {

            outline:
                3px solid
                rgba(37, 99, 235, 0.18);

            outline-offset: 2px;
        }


        /* =========================================================
           REGISTER
        ========================================================= */

        .register-section {

            margin-top: 22px;

            padding-top: 20px;

            border-top:
                1px solid #e2e8f0;

            text-align: center;
        }


        .register-section p {

            margin: 0;

            color: #64748b;

            font-size: 12px;
        }


        .register-section a {

            color: #2563eb;

            font-weight: 700;

            text-decoration: none;
        }


        .register-section a:hover {

            text-decoration: underline;
        }


        /* =========================================================
           FOOTER
        ========================================================= */

        .login-footer {

            margin-top: 18px;

            text-align: center;

            color: #94a3b8;

            font-size: 10px;
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 480px) {

            body {

                padding: 20px 14px;
            }


            .brand {

                margin-bottom: 18px;
            }


            .brand-logo {

                width: 48px;

                height: 48px;

                margin-bottom: 10px;
            }


            .brand h1 {

                font-size: 21px;
            }


            .login-card {

                padding: 24px 20px;

                border-radius: 12px;
            }


            .login-header h2 {

                font-size: 19px;
            }


            .form-options {

                gap: 10px;
            }


            .money-one {

                right: -10px;

                top: 8%;

                font-size: 55px;
            }


            .money-two {

                left: -10px;

                bottom: 10%;

                font-size: 40px;
            }


            .money-three {

                display: none;
            }


            .money-four {

                right: -10px;

                bottom: 4%;

                font-size: 45px;
            }


            .circle-one {

                left: -70px;

                top: 15%;
            }


            .circle-two {

                right: -150px;

                top: 30%;
            }

        }

    </style>

</head>


<body>


    <!-- =========================================================
         BACKGROUND PATTERN
    ========================================================== -->

    <div class="background-pattern">

        <span class="circle-one"></span>

        <span class="circle-two"></span>

        <span class="circle-three"></span>

        <span class="circle-four"></span>

    </div>


    <!-- =========================================================
         FLOATING MONEY SYMBOLS
    ========================================================== -->

    <div class="money-symbol money-one">
        ₱
    </div>

    <div class="money-symbol money-two">
        ₱
    </div>

    <div class="money-symbol money-three">
        ₱
    </div>

    <div class="money-symbol money-four">
        ₱
    </div>


    <!-- =========================================================
         LOGIN WRAPPER
    ========================================================== -->

    <div class="login-wrapper">


        <!-- =====================================================
             BRAND
        ====================================================== -->

        <div class="brand">

            <div class="brand-logo">
                ₱
            </div>


            <h1>
                Loan Management
            </h1>


            <p>
                Simple loan management for your business
            </p>

        </div>


        <!-- =====================================================
             LOGIN CARD
        ====================================================== -->

        <div class="login-card">


            <!-- =================================================
                 HEADER
            ================================================== -->

            <div class="login-header">

                <h2>
                    Welcome back
                </h2>


                <p>
                    Sign in to access your account.
                </p>

            </div>


            <!-- =================================================
                 ERROR
            ================================================== -->

            <?php if (!empty($error)): ?>

                <div
                    class="login-error"
                    role="alert"
                >

                    <div class="error-icon">
                        !
                    </div>


                    <div>

                        <?= htmlspecialchars($error) ?>

                    </div>

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


                    <div class="input-wrapper">

                        <span
                            class="input-icon"
                            aria-hidden="true"
                        >
                            @
                        </span>


                        <input
                            type="text"
                            id="login"
                            name="login"
                            class="login-input"
                            placeholder="Enter username or email"
                            autocomplete="username"
                            required
                            autofocus
                        >

                    </div>

                </div>


                <!-- =============================================
                     PASSWORD
                ============================================== -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>


                    <div class="input-wrapper">

                        <span
                            class="input-icon"
                            aria-hidden="true"
                        >
                            •
                        </span>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="login-input"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >


                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                            aria-label="Show password"
                        >
                            Show
                        </button>

                    </div>

                </div>


                <!-- =============================================
                     OPTIONS
                ============================================== -->

                <div class="form-options">

                    <label class="remember-me">

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                        >

                        <span>
                            Remember me
                        </span>

                    </label>

                </div>


                <!-- =============================================
                     LOGIN BUTTON
                ============================================== -->

                <button
                    type="submit"
                    class="login-button"
                >
                    Sign In
                </button>


            </form>


            <!-- =================================================
                 REGISTER
            ================================================== -->

            <div class="register-section">

                <p>

                    Don't have an account?

                    <a
                        href="index.php?url=auth/register"
                    >
                        Create a business account
                    </a>

                </p>

            </div>


        </div>


        <!-- =====================================================
             FOOTER
        ====================================================== -->

        <div class="login-footer">

            &copy;

            <?= date('Y') ?>

            Loan Management.

            All rights reserved.

        </div>


    </div>


    <!-- =========================================================
         PASSWORD VISIBILITY
    ========================================================== -->

    <script>

        const passwordInput =
            document.getElementById(
                'password'
            );

        const passwordToggle =
            document.getElementById(
                'passwordToggle'
            );


        if (
            passwordInput &&
            passwordToggle
        ) {

            passwordToggle.addEventListener(
                'click',
                function () {

                    const isPassword =
                        passwordInput.type ===
                        'password';


                    passwordInput.type =
                        isPassword
                            ? 'text'
                            : 'password';


                    passwordToggle.textContent =
                        isPassword
                            ? 'Hide'
                            : 'Show';


                    passwordToggle.setAttribute(
                        'aria-label',
                        isPassword
                            ? 'Hide password'
                            : 'Show password'
                    );

                }
            );

        }

    </script>


</body>

</html>
