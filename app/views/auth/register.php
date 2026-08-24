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

    <title>
        Register | Loan Management
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px 15px;

            background: #f5f7fb;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: #1f2937;

        }


        .register-wrapper {

            width: 100%;

            max-width: 850px;

        }


        .brand {

            text-align: center;

            margin-bottom: 25px;

        }


        .brand h1 {

            margin: 0;

            font-size: 25px;

            color: #111827;

        }


        .brand p {

            margin: 7px 0 0;

            color: #6b7280;

            font-size: 13px;

        }


        .register-card {

            background: #ffffff;

            border-radius: 12px;

            border:
                1px solid #e5e7eb;

            padding: 35px;

            box-shadow:
                0 5px 20px
                rgba(0, 0, 0, 0.05);

        }


        .card-title {

            margin-bottom: 25px;

        }


        .card-title h2 {

            margin: 0 0 6px;

            font-size: 20px;

        }


        .card-title p {

            margin: 0;

            color: #6b7280;

            font-size: 13px;

        }


        .section {

            margin-top: 25px;

        }


        .section-title {

            font-size: 13px;

            font-weight: 700;

            color: #111827;

            padding-bottom: 10px;

            border-bottom:
                1px solid #e5e7eb;

            margin-bottom: 18px;

        }


        .form-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    1fr
                );

            gap: 18px;

        }


        .form-group {

            display: flex;

            flex-direction: column;

        }


        .form-group.full {

            grid-column: 1 / -1;

        }


        label {

            font-size: 12px;

            font-weight: 600;

            color: #374151;

            margin-bottom: 7px;

        }


        .required {

            color: #dc2626;

        }


        input,
        textarea {

            width: 100%;

            border:
                1px solid #d1d5db;

            border-radius: 7px;

            padding: 11px 12px;

            font-size: 13px;

            outline: none;

            font-family: inherit;

        }


        textarea {

            min-height: 90px;

            resize: vertical;

        }


        input:focus,
        textarea:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(
                    37,
                    99,
                    235,
                    0.10
                );

        }


        .help-text {

            margin-top: 5px;

            color: #9ca3af;

            font-size: 10px;

        }


        .error {

            background: #fef2f2;

            color: #b91c1c;

            border:
                1px solid #fecaca;

            border-radius: 7px;

            padding: 12px;

            margin-bottom: 20px;

            font-size: 13px;

        }


        .button {

            width: 100%;

            border: none;

            border-radius: 7px;

            padding: 13px;

            background: #2563eb;

            color: #ffffff;

            font-size: 13px;

            font-weight: 700;

            cursor: pointer;

            margin-top: 25px;

        }


        .button:hover {

            background: #1d4ed8;

        }


        .login-link {

            text-align: center;

            margin-top: 20px;

            font-size: 13px;

            color: #6b7280;

        }


        .login-link a {

            color: #2563eb;

            text-decoration: none;

            font-weight: 600;

        }


        .login-link a:hover {

            text-decoration: underline;

        }


        @media (max-width: 650px) {

            .register-card {

                padding: 25px 20px;

            }


            .form-grid {

                grid-template-columns:
                    1fr;

            }


            .form-group.full {

                grid-column: auto;

            }

        }

    </style>

</head>


<body>


<div class="register-wrapper">


    <!-- =====================================================
         BRAND
    ====================================================== -->

    <div class="brand">

        <h1>
            Loan Management
        </h1>

        <p>
            Create your business account
        </p>

    </div>


    <!-- =====================================================
         REGISTER CARD
    ====================================================== -->

    <div class="register-card">


        <div class="card-title">

            <h2>
                Create Your Account
            </h2>

            <p>
                Register your business and start managing loans.
            </p>

        </div>


        <?php if (!empty($error)): ?>

            <div class="error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action="index.php?url=auth/register"
        >


            <!-- =================================================
                 ACCOUNT INFORMATION
            ================================================== -->

            <div class="section">

                <div class="section-title">

                    Account Information

                </div>


                <div class="form-grid">


                    <div class="form-group full">

                        <label>

                            Full Name
                            <span class="required">*</span>

                        </label>


                        <input
                            type="text"
                            name="full_name"
                            value="<?= htmlspecialchars(
                                $old['full_name']
                                ?? ''
                            ) ?>"
                            placeholder="Enter your full name"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>

                            Username
                            <span class="required">*</span>

                        </label>


                        <input
                            type="text"
                            name="username"
                            value="<?= htmlspecialchars(
                                $old['username']
                                ?? ''
                            ) ?>"
                            placeholder="Choose a username"
                            required
                        >


                        <span class="help-text">

                            At least 4 characters.

                        </span>

                    </div>


                    <div class="form-group">

                        <label>

                            Email Address
                            <span class="required">*</span>

                        </label>


                        <input
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars(
                                $old['email']
                                ?? ''
                            ) ?>"
                            placeholder="you@example.com"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>

                            Password
                            <span class="required">*</span>

                        </label>


                        <input
                            type="password"
                            name="password"
                            placeholder="Create a password"
                            required
                        >


                        <span class="help-text">

                            Minimum 8 characters.

                        </span>

                    </div>


                    <div class="form-group">

                        <label>

                            Confirm Password
                            <span class="required">*</span>

                        </label>


                        <input
                            type="password"
                            name="confirm_password"
                            placeholder="Repeat your password"
                            required
                        >

                    </div>


                </div>

            </div>


            <!-- =================================================
                 BUSINESS INFORMATION
            ================================================== -->

            <div class="section">

                <div class="section-title">

                    Business Information

                </div>


                <div class="form-grid">


                    <div class="form-group full">

                        <label>

                            Business Name
                            <span class="required">*</span>

                        </label>


                        <input
                            type="text"
                            name="business_name"
                            value="<?= htmlspecialchars(
                                $old['business_name']
                                ?? ''
                            ) ?>"
                            placeholder="Enter your business name"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>

                            Business Email

                        </label>


                        <input
                            type="email"
                            name="business_email"
                            value="<?= htmlspecialchars(
                                $old['business_email']
                                ?? ''
                            ) ?>"
                            placeholder="business@example.com"
                        >

                    </div>


                    <div class="form-group">

                        <label>

                            Business Phone

                        </label>


                        <input
                            type="text"
                            name="business_phone"
                            value="<?= htmlspecialchars(
                                $old['business_phone']
                                ?? ''
                            ) ?>"
                            placeholder="09XXXXXXXXX"
                        >

                    </div>


                    <div class="form-group full">

                        <label>

                            Business Address

                        </label>


                        <textarea
                            name="business_address"
                            placeholder="Enter your business address"
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

            <button
                type="submit"
                class="button"
            >

                Create Business Account

            </button>


        </form>


        <!-- =================================================
             LOGIN LINK
        ================================================== -->

        <div class="login-link">

            Already have an account?

            <a
                href="index.php?url=auth/login"
            >
                Login here
            </a>

        </div>


    </div>


</div>


</body>

</html>