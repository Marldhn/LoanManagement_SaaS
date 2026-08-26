<?php

/*
|--------------------------------------------------------------------------
| Registration View
|--------------------------------------------------------------------------
*/

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
        Create Account | Loan Management
    </title>

    <style>

        /* =========================================================
           GLOBAL RESET
        ========================================================= */

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            margin: 0;
            min-height: 100vh;

            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Arial,
                Helvetica,
                sans-serif;

            color: #111827;
            background: #f4f7fb;
        }

        button,
        input,
        textarea {
            font-family: inherit;
        }


        /* =========================================================
           PAGE
        ========================================================= */

        .register-page {
            min-height: 100vh;

            display: flex;
            align-items: stretch;
            justify-content: center;

            padding: 0;
        }


        /* =========================================================
           MAIN LAYOUT
        ========================================================= */

        .register-layout {
            width: 100%;
            min-height: 100vh;

            display: grid;

            grid-template-columns:
                390px
                minmax(0, 1fr);
        }


        /* =========================================================
           LEFT BRAND PANEL
        ========================================================= */

        .brand-panel {
            position: relative;
            overflow: hidden;

            display: flex;
            flex-direction: column;
            justify-content: space-between;

            padding: 45px;

            background:
                linear-gradient(
                    145deg,
                    #1d4ed8 0%,
                    #2563eb 45%,
                    #1e40af 100%
                );

            color: #ffffff;
        }


        /* Decorative circles */

        .brand-panel::before {
            content: "";

            position: absolute;

            width: 360px;
            height: 360px;

            border-radius: 50%;

            right: -170px;
            top: -100px;

            background:
                rgba(255, 255, 255, 0.08);
        }

        .brand-panel::after {
            content: "";

            position: absolute;

            width: 280px;
            height: 280px;

            border-radius: 50%;

            left: -150px;
            bottom: -100px;

            background:
                rgba(255, 255, 255, 0.06);
        }


        /* =========================================================
           BRAND
        ========================================================= */

        .brand {
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            display: flex;
            align-items: center;

            gap: 12px;

            margin-bottom: 55px;
        }

        .brand-logo-icon {
            width: 46px;
            height: 46px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;

            background:
                rgba(255, 255, 255, 0.16);

            border:
                1px solid
                rgba(255, 255, 255, 0.20);

            font-size: 22px;
            font-weight: 800;
        }

        .brand-logo-text {
            display: flex;
            flex-direction: column;
        }

        .brand-logo-title {
            font-size: 17px;
            font-weight: 800;

            letter-spacing: -0.3px;
        }

        .brand-logo-subtitle {
            margin-top: 2px;

            font-size: 10px;

            color:
                rgba(255, 255, 255, 0.68);

            text-transform: uppercase;

            letter-spacing: 1px;
        }


        /* =========================================================
           BRAND CONTENT
        ========================================================= */

        .brand-content {
            position: relative;
            z-index: 2;

            max-width: 290px;
        }

        .brand-content h1 {
            margin: 0 0 18px;

            font-size: 34px;
            line-height: 1.15;

            letter-spacing: -1px;
        }

        .brand-content p {
            margin: 0;

            font-size: 14px;
            line-height: 1.7;

            color:
                rgba(255, 255, 255, 0.78);
        }


        /* =========================================================
           FEATURE LIST
        ========================================================= */

        .feature-list {
            display: flex;
            flex-direction: column;

            gap: 16px;

            margin-top: 38px;
        }

        .feature {
            display: flex;
            align-items: center;

            gap: 12px;

            font-size: 13px;

            color:
                rgba(255, 255, 255, 0.90);
        }

        .feature-icon {
            width: 27px;
            height: 27px;

            min-width: 27px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background:
                rgba(255, 255, 255, 0.13);

            font-size: 13px;
        }


        /* =========================================================
           BRAND FOOTER
        ========================================================= */

        .brand-footer {
            position: relative;
            z-index: 2;

            font-size: 11px;

            color:
                rgba(255, 255, 255, 0.50);
        }


        /* =========================================================
           FORM SIDE
        ========================================================= */

        .form-side {
            min-width: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 45px 55px;

            background: #f7f9fc;

            overflow-y: auto;
        }


        /* =========================================================
           REGISTER WRAPPER
        ========================================================= */

        .register-wrapper {
            width: 100%;

            max-width: 760px;
        }


        /* =========================================================
           MOBILE BRAND
        ========================================================= */

        .mobile-brand {
            display: none;
        }


        /* =========================================================
           CARD
        ========================================================= */

        .register-card {
            width: 100%;

            background: #ffffff;

            border:
                1px solid #e5e7eb;

            border-radius: 18px;

            padding: 38px 42px;

            box-shadow:
                0 18px 45px
                rgba(15, 23, 42, 0.07);
        }


        /* =========================================================
           CARD HEADER
        ========================================================= */

        .card-header {
            margin-bottom: 30px;
        }

        .card-header h2 {
            margin: 0 0 8px;

            color: #111827;

            font-size: 25px;
            font-weight: 750;

            letter-spacing: -0.5px;
        }

        .card-header p {
            margin: 0;

            color: #6b7280;

            font-size: 13px;
            line-height: 1.6;
        }


        /* =========================================================
           ERROR
        ========================================================= */

        .error {
            display: flex;
            align-items: flex-start;

            gap: 10px;

            padding: 13px 14px;

            margin-bottom: 25px;

            border:
                1px solid #fecaca;

            border-radius: 10px;

            background: #fff1f2;

            color: #991b1b;

            font-size: 13px;
            line-height: 1.5;
        }

        .error-icon {
            width: 20px;
            height: 20px;

            min-width: 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #fee2e2;

            font-size: 11px;
            font-weight: 800;
        }


        /* =========================================================
           FORM SECTION
        ========================================================= */

        .section {
            margin-top: 30px;
        }

        .section:first-of-type {
            margin-top: 0;
        }

        .section-header {
            display: flex;
            align-items: center;

            gap: 11px;

            margin-bottom: 19px;
        }

        .section-number {
            width: 27px;
            height: 27px;

            min-width: 27px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 8px;

            background: #eff6ff;

            color: #2563eb;

            font-size: 11px;
            font-weight: 800;
        }

        .section-title {
            color: #111827;

            font-size: 13px;
            font-weight: 750;

            letter-spacing: 0.1px;
        }

        .section-line {
            flex: 1;

            height: 1px;

            background: #eef0f3;
        }


        /* =========================================================
           FORM GRID
        ========================================================= */

        .form-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(0, 1fr)
                );

            gap: 19px;
        }

        .form-group {
            min-width: 0;

            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }


        /* =========================================================
           LABEL
        ========================================================= */

        .form-group label {
            display: flex;
            align-items: center;

            gap: 3px;

            margin-bottom: 7px;

            color: #374151;

            font-size: 12px;
            font-weight: 650;
        }

        .required {
            color: #ef4444;

            font-weight: 800;
        }


        /* =========================================================
           INPUT WRAPPER
        ========================================================= */

        .input-wrapper {
            position: relative;

            width: 100%;
        }


        /* =========================================================
           INPUTS
        ========================================================= */

        .form-group input,
        .form-group textarea {
            width: 100%;

            border:
                1px solid #d7dce3;

            border-radius: 9px;

            background: #ffffff;

            color: #111827;

            padding:
                12px 13px;

            outline: none;

            font-size: 13px;
            line-height: 1.4;

            transition:
                border-color 0.18s ease,
                box-shadow 0.18s ease,
                background 0.18s ease;
        }

        .form-group input {
            min-height: 45px;
        }

        .form-group textarea {
            min-height: 100px;

            resize: vertical;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #a1a8b3;
        }

        .form-group input:hover,
        .form-group textarea:hover {
            border-color: #b9c1cc;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.10);

            background: #ffffff;
        }


        /* =========================================================
           PASSWORD INPUT
        ========================================================= */

        .password-input {
            padding-right: 47px !important;
        }

        .toggle-password {
            position: absolute;

            top: 50%;
            right: 9px;

            transform: translateY(-50%);

            width: 32px;
            height: 32px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;

            border-radius: 7px;

            background: transparent;

            color: #9ca3af;

            cursor: pointer;

            font-size: 14px;

            transition:
                background 0.15s ease,
                color 0.15s ease;
        }

        .toggle-password:hover {
            background: #f3f4f6;

            color: #374151;
        }


        /* =========================================================
           HELP TEXT
        ========================================================= */

        .help-text {
            margin-top: 6px;

            color: #9ca3af;

            font-size: 10px;
            line-height: 1.4;
        }


        /* =========================================================
           PASSWORD STRENGTH
        ========================================================= */

        .password-strength {
            display: none;

            margin-top: 8px;
        }

        .strength-bars {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 4px;

            margin-bottom: 5px;
        }

        .strength-bar {
            height: 3px;

            border-radius: 10px;

            background: #e5e7eb;

            transition:
                background 0.2s ease;
        }

        .strength-text {
            color: #9ca3af;

            font-size: 10px;
        }


        /* =========================================================
           SUBMIT AREA
        ========================================================= */

        .submit-area {
            margin-top: 32px;

            padding-top: 25px;

            border-top:
                1px solid #eef0f3;
        }

        .button {
            width: 100%;

            min-height: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 9px;

            border: none;

            border-radius: 9px;

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );

            color: #ffffff;

            font-size: 13px;
            font-weight: 700;

            letter-spacing: 0.1px;

            cursor: pointer;

            box-shadow:
                0 5px 12px
                rgba(37, 99, 235, 0.20);

            transition:
                transform 0.15s ease,
                box-shadow 0.15s ease,
                background 0.15s ease;
        }

        .button:hover {
            background:
                linear-gradient(
                    135deg,
                    #1d4ed8,
                    #1e40af
                );

            box-shadow:
                0 7px 16px
                rgba(37, 99, 235, 0.25);

            transform:
                translateY(-1px);
        }

        .button:active {
            transform:
                translateY(0);
        }

        .button-arrow {
            font-size: 16px;

            line-height: 1;
        }


        /* =========================================================
           LOGIN LINK
        ========================================================= */

        .login-link {
            margin-top: 20px;

            text-align: center;

            color: #6b7280;

            font-size: 12px;
        }

        .login-link a {
            color: #2563eb;

            text-decoration: none;

            font-weight: 700;
        }

        .login-link a:hover {
            text-decoration: underline;
        }


        /* =========================================================
           SECURITY NOTE
        ========================================================= */

        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;

            gap: 6px;

            margin-top: 17px;

            color: #9ca3af;

            font-size: 10px;
        }

        .security-icon {
            font-size: 11px;
        }


        /* =========================================================
           TABLET
        ========================================================= */

        @media (max-width: 1000px) {

            .register-layout {
                grid-template-columns:
                    300px
                    minmax(0, 1fr);
            }

            .brand-panel {
                padding: 35px;
            }

            .brand-content h1 {
                font-size: 29px;
            }

            .form-side {
                padding:
                    35px 30px;
            }

            .register-card {
                padding:
                    32px;
            }
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 760px) {

            .register-page {
                display: block;
            }

            .register-layout {
                display: block;

                min-height: 100vh;
            }

            .brand-panel {
                display: none;
            }

            .form-side {
                min-height: 100vh;

                padding:
                    25px 15px;

                align-items: flex-start;
            }

            .register-wrapper {
                max-width: 600px;

                margin: 0 auto;
            }

            .mobile-brand {
                display: flex;
                align-items: center;
                justify-content: center;

                gap: 10px;

                margin-bottom: 22px;
            }

            .mobile-brand-icon {
                width: 38px;
                height: 38px;

                display: flex;
                align-items: center;
                justify-content: center;

                border-radius: 10px;

                background: #2563eb;

                color: #ffffff;

                font-size: 17px;
                font-weight: 800;

                box-shadow:
                    0 5px 12px
                    rgba(37, 99, 235, 0.18);
            }

            .mobile-brand-text {
                display: flex;
                flex-direction: column;
            }

            .mobile-brand-title {
                color: #111827;

                font-size: 15px;
                font-weight: 800;
            }

            .mobile-brand-subtitle {
                margin-top: 1px;

                color: #9ca3af;

                font-size: 9px;

                text-transform: uppercase;

                letter-spacing: 0.8px;
            }

            .register-card {
                padding:
                    28px 22px;

                border-radius: 15px;
            }

            .card-header h2 {
                font-size: 22px;
            }

            .form-grid {
                grid-template-columns: 1fr;

                gap: 17px;
            }

            .form-group.full {
                grid-column: auto;
            }
        }


        /* =========================================================
           SMALL PHONE
        ========================================================= */

        @media (max-width: 480px) {

            .form-side {
                padding:
                    18px 10px;
            }

            .register-card {
                padding:
                    24px 18px;

                border-radius: 13px;
            }

            .card-header {
                margin-bottom: 25px;
            }

            .card-header h2 {
                font-size: 20px;
            }

            .card-header p {
                font-size: 12px;
            }

            .section {
                margin-top: 27px;
            }

            .section-header {
                margin-bottom: 16px;
            }

            .form-group input,
            .form-group textarea {
                font-size: 14px;
            }

            .button {
                min-height: 47px;
            }
        }


        /* =========================================================
           VERY SMALL PHONE
        ========================================================= */

        @media (max-width: 360px) {

            .register-card {
                padding:
                    22px 15px;
            }

            .form-side {
                padding:
                    15px 8px;
            }
        }

    </style>

</head>


<body>


<div class="register-page">


    <div class="register-layout">


        <!-- =====================================================
             LEFT BRAND PANEL
        ====================================================== -->

        <aside class="brand-panel">


            <div class="brand">


                <!-- BRAND LOGO -->

                <div class="brand-logo">

                    <div class="brand-logo-icon">
                        ₱
                    </div>

                    <div class="brand-logo-text">

                        <span class="brand-logo-title">
                            Loan Management
                        </span>

                        <span class="brand-logo-subtitle">
                            SaaS Platform
                        </span>

                    </div>

                </div>


                <!-- BRAND CONTENT -->

                <div class="brand-content">

                    <h1>
                        Manage your lending business with confidence.
                    </h1>

                    <p>
                        Everything you need to manage borrowers,
                        loans, payments, accounts, and your business
                        operations in one place.
                    </p>


                    <div class="feature-list">


                        <div class="feature">

                            <div class="feature-icon">
                                ✓
                            </div>

                            <span>
                                Manage borrowers and loan accounts
                            </span>

                        </div>


                        <div class="feature">

                            <div class="feature-icon">
                                ✓
                            </div>

                            <span>
                                Track payments and loan schedules
                            </span>

                        </div>


                        <div class="feature">

                            <div class="feature-icon">
                                ✓
                            </div>

                            <span>
                                Keep your business finances organized
                            </span>

                        </div>


                    </div>

                </div>

            </div>


            <!-- FOOTER -->

            <div class="brand-footer">

                © <?= date('Y') ?> Loan Management SaaS.
                All rights reserved.

            </div>


        </aside>


        <!-- =====================================================
             FORM SIDE
        ====================================================== -->

        <main class="form-side">


            <div class="register-wrapper">


                <!-- =================================================
                     MOBILE BRAND
                ================================================== -->

                <div class="mobile-brand">

                    <div class="mobile-brand-icon">
                        ₱
                    </div>

                    <div class="mobile-brand-text">

                        <span class="mobile-brand-title">
                            Loan Management
                        </span>

                        <span class="mobile-brand-subtitle">
                            SaaS Platform
                        </span>

                    </div>

                </div>


                <!-- =================================================
                     REGISTER CARD
                ================================================== -->

                <div class="register-card">


                    <!-- CARD HEADER -->

                    <div class="card-header">

                        <h2>
                            Create your account
                        </h2>

                        <p>
                            Set up your administrator account and
                            register your lending business.
                        </p>

                    </div>


                    <!-- =================================================
                         ERROR
                    ================================================== -->

                    <?php if (!empty($error)): ?>

                        <div class="error">

                            <div class="error-icon">
                                !
                            </div>

                            <div>
                                <?= htmlspecialchars($error) ?>
                            </div>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         FORM
                    ================================================== -->

                    <form
                        method="POST"
                        action="index.php?url=auth/register"
                        id="registerForm"
                    >


                        <!-- =================================================
                             ACCOUNT INFORMATION
                        ================================================== -->

                        <div class="section">


                            <div class="section-header">

                                <div class="section-number">
                                    01
                                </div>

                                <div class="section-title">
                                    Account Information
                                </div>

                                <div class="section-line"></div>

                            </div>


                            <div class="form-grid">


                                <!-- FULL NAME -->

                                <div class="form-group full">

                                    <label for="full_name">

                                        Full Name

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        type="text"
                                        id="full_name"
                                        name="full_name"
                                        value="<?= htmlspecialchars(
                                            $old['full_name']
                                            ?? ''
                                        ) ?>"
                                        placeholder="Enter your full name"
                                        autocomplete="name"
                                        required
                                    >

                                </div>


                                <!-- USERNAME -->

                                <div class="form-group">

                                    <label for="username">

                                        Username

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        type="text"
                                        id="username"
                                        name="username"
                                        value="<?= htmlspecialchars(
                                            $old['username']
                                            ?? ''
                                        ) ?>"
                                        placeholder="Choose a username"
                                        autocomplete="username"
                                        minlength="4"
                                        required
                                    >


                                    <span class="help-text">
                                        At least 4 characters.
                                    </span>

                                </div>


                                <!-- EMAIL -->

                                <div class="form-group">

                                    <label for="email">

                                        Email Address

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="<?= htmlspecialchars(
                                            $old['email']
                                            ?? ''
                                        ) ?>"
                                        placeholder="you@example.com"
                                        autocomplete="email"
                                        required
                                    >

                                </div>


                                <!-- PASSWORD -->

                                <div class="form-group">

                                    <label for="password">

                                        Password

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <div class="input-wrapper">


                                        <input
                                            type="password"
                                            id="password"
                                            name="password"
                                            class="password-input"
                                            placeholder="Create a password"
                                            autocomplete="new-password"
                                            minlength="8"
                                            required
                                        >


                                        <button
                                            type="button"
                                            class="toggle-password"
                                            data-target="password"
                                            aria-label="Show password"
                                        >
                                            ◉
                                        </button>


                                    </div>


                                    <div
                                        class="password-strength"
                                        id="passwordStrength"
                                    >

                                        <div class="strength-bars">

                                            <span class="strength-bar"></span>
                                            <span class="strength-bar"></span>
                                            <span class="strength-bar"></span>
                                            <span class="strength-bar"></span>

                                        </div>


                                        <div class="strength-text">
                                            Password strength
                                        </div>

                                    </div>


                                    <span class="help-text">
                                        Minimum 8 characters.
                                    </span>

                                </div>


                                <!-- CONFIRM PASSWORD -->

                                <div class="form-group">

                                    <label for="confirm_password">

                                        Confirm Password

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <div class="input-wrapper">


                                        <input
                                            type="password"
                                            id="confirm_password"
                                            name="confirm_password"
                                            class="password-input"
                                            placeholder="Repeat your password"
                                            autocomplete="new-password"
                                            required
                                        >


                                        <button
                                            type="button"
                                            class="toggle-password"
                                            data-target="confirm_password"
                                            aria-label="Show password"
                                        >
                                            ◉
                                        </button>


                                    </div>


                                    <span
                                        class="help-text"
                                        id="passwordMatchText"
                                    >
                                        Enter the same password again.
                                    </span>

                                </div>


                            </div>

                        </div>


                        <!-- =================================================
                             BUSINESS INFORMATION
                        ================================================== -->

                        <div class="section">


                            <div class="section-header">

                                <div class="section-number">
                                    02
                                </div>

                                <div class="section-title">
                                    Business Information
                                </div>

                                <div class="section-line"></div>

                            </div>


                            <div class="form-grid">


                                <!-- BUSINESS NAME -->

                                <div class="form-group full">

                                    <label for="business_name">

                                        Business Name

                                        <span class="required">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        type="text"
                                        id="business_name"
                                        name="business_name"
                                        value="<?= htmlspecialchars(
                                            $old['business_name']
                                            ?? ''
                                        ) ?>"
                                        placeholder="Enter your business name"
                                        required
                                    >

                                </div>


                                <!-- BUSINESS EMAIL -->

                                <div class="form-group">

                                    <label for="business_email">
                                        Business Email
                                    </label>


                                    <input
                                        type="email"
                                        id="business_email"
                                        name="business_email"
                                        value="<?= htmlspecialchars(
                                            $old['business_email']
                                            ?? ''
                                        ) ?>"
                                        placeholder="business@example.com"
                                    >

                                </div>


                                <!-- BUSINESS PHONE -->

                                <div class="form-group">

                                    <label for="business_phone">
                                        Business Phone
                                    </label>


                                    <input
                                        type="tel"
                                        id="business_phone"
                                        name="business_phone"
                                        value="<?= htmlspecialchars(
                                            $old['business_phone']
                                            ?? ''
                                        ) ?>"
                                        placeholder="09XXXXXXXXX"
                                        autocomplete="tel"
                                    >

                                </div>


                                <!-- BUSINESS ADDRESS -->

                                <div class="form-group full">

                                    <label for="business_address">
                                        Business Address
                                    </label>


                                    <textarea
                                        id="business_address"
                                        name="business_address"
                                        placeholder="Enter your complete business address"
                                    ><?= htmlspecialchars(
                                        $old['business_address']
                                        ?? ''
                                    ) ?></textarea>

                                </div>


                            </div>

                        </div>


                        <!-- =================================================
                             SUBMIT
                        ================================================== -->

                        <div class="submit-area">


                            <button
                                type="submit"
                                class="button"
                            >

                                <span>
                                    Create Business Account
                                </span>

                                <span class="button-arrow">
                                    →
                                </span>

                            </button>


                            <div class="security-note">

                                <span class="security-icon">
                                    🔒
                                </span>

                                Your information is securely handled.

                            </div>


                        </div>


                    </form>


                    <!-- =================================================
                         LOGIN LINK
                    ================================================== -->

                    <div class="login-link">

                        Already have an account?

                        <a
                            href="index.php?url=auth/login"
                        >
                            Sign in
                        </a>

                    </div>


                </div>


            </div>


        </main>


    </div>


</div>


<script>


/*
|--------------------------------------------------------------------------
| PASSWORD VISIBILITY
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('.toggle-password')
    .forEach(function(button)
    {

        button.addEventListener(
            'click',
            function()
            {

                const targetId =
                    this.getAttribute('data-target');

                const input =
                    document.getElementById(targetId);

                if (!input) {
                    return;
                }


                if (input.type === 'password') {

                    input.type = 'text';

                    this.textContent = '○';

                    this.setAttribute(
                        'aria-label',
                        'Hide password'
                    );

                } else {

                    input.type = 'password';

                    this.textContent = '◉';

                    this.setAttribute(
                        'aria-label',
                        'Show password'
                    );

                }

            }
        );

    });


/*
|--------------------------------------------------------------------------
| PASSWORD STRENGTH
|--------------------------------------------------------------------------
*/

const passwordInput =
    document.getElementById('password');

const passwordStrength =
    document.getElementById('passwordStrength');

const strengthBars =
    passwordStrength
        ? passwordStrength.querySelectorAll('.strength-bar')
        : [];

const strengthText =
    passwordStrength
        ? passwordStrength.querySelector('.strength-text')
        : null;


if (passwordInput) {

    passwordInput.addEventListener(
        'input',
        function()
        {

            const password =
                this.value;


            if (!password) {

                passwordStrength.style.display =
                    'none';

                return;
            }


            passwordStrength.style.display =
                'block';


            let score = 0;


            if (password.length >= 8) {
                score++;
            }

            if (password.length >= 12) {
                score++;
            }

            if (/[A-Z]/.test(password)) {
                score++;
            }

            if (/[0-9]/.test(password)) {
                score++;
            }

            if (/[^A-Za-z0-9]/.test(password)) {
                score++;
            }


            score =
                Math.min(score, 4);


            strengthBars.forEach(
                function(bar, index)
                {

                    bar.style.background =
                        index < score
                            ? '#2563eb'
                            : '#e5e7eb';

                }
            );


            if (strengthText) {

                if (score <= 1) {

                    strengthText.textContent =
                        'Weak password';

                    strengthText.style.color =
                        '#dc2626';

                } else if (score === 2) {

                    strengthText.textContent =
                        'Fair password';

                    strengthText.style.color =
                        '#d97706';

                } else if (score === 3) {

                    strengthText.textContent =
                        'Good password';

                    strengthText.style.color =
                        '#2563eb';

                } else {

                    strengthText.textContent =
                        'Strong password';

                    strengthText.style.color =
                        '#16a34a';

                }

            }

        }
    );

}


/*
|--------------------------------------------------------------------------
| PASSWORD MATCH
|--------------------------------------------------------------------------
*/

const confirmPassword =
    document.getElementById('confirm_password');

const passwordMatchText =
    document.getElementById('passwordMatchText');


function checkPasswordMatch()
{

    if (
        !passwordInput ||
        !confirmPassword ||
        !passwordMatchText
    ) {
        return;
    }


    if (!confirmPassword.value) {

        passwordMatchText.textContent =
            'Enter the same password again.';

        passwordMatchText.style.color =
            '#9ca3af';

        confirmPassword.style.borderColor =
            '#d7dce3';

        return;
    }


    if (
        passwordInput.value ===
        confirmPassword.value
    ) {

        passwordMatchText.textContent =
            'Passwords match.';

        passwordMatchText.style.color =
            '#16a34a';

        confirmPassword.style.borderColor =
            '#22c55e';

    } else {

        passwordMatchText.textContent =
            'Passwords do not match.';

        passwordMatchText.style.color =
            '#dc2626';

        confirmPassword.style.borderColor =
            '#ef4444';

    }

}


if (passwordInput) {

    passwordInput.addEventListener(
        'input',
        checkPasswordMatch
    );

}


if (confirmPassword) {

    confirmPassword.addEventListener(
        'input',
        checkPasswordMatch
    );

}


/*
|--------------------------------------------------------------------------
| PREVENT SUBMIT IF PASSWORDS DO NOT MATCH
|--------------------------------------------------------------------------
*/

const registerForm =
    document.getElementById('registerForm');


if (registerForm) {

    registerForm.addEventListener(
        'submit',
        function(event)
        {

            if (
                passwordInput &&
                confirmPassword &&
                passwordInput.value !==
                confirmPassword.value
            ) {

                event.preventDefault();

                confirmPassword.focus();

                checkPasswordMatch();

            }

        }
    );

}

</script>


</body>

</html>