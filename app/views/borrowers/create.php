<?php

$user = $user ?? Auth::user();

$business = $business ?? Auth::business();

$tenantRole = $tenantRole ?? Auth::tenantRole();

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
        Add Borrower | Loan Management
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Public+Sans:wght@400;500;600;700;800&display=swap"
    >

    <style>

        /*
        |--------------------------------------------------------------------------
        | TOKENS — shares the sidebar / dashboard / borrowers / loans / payments /
        | collections / accounts / penalties / expenses / categories / settings /
        | borrower-details / business-users / superadmin language
        |--------------------------------------------------------------------------
        */

        :root {

            --lm-ink-900: #16211D;
            --lm-ink-700: #33413B;
            --lm-ink-500: #6B7670;
            --lm-ink-300: #9CA69F;
            --lm-line: #E7E2D6;
            --lm-line-soft: #F0EDE4;
            --lm-surface: #FFFFFF;
            --lm-surface-tint: #FAF8F2;
            --lm-brass: #B8860F;
            --lm-brass-ink: #8A6608;
            --lm-brass-soft: #F7EFD9;
            --lm-forest: #1F7A52;
            --lm-forest-soft: #E7F3EC;
            --lm-danger: #B0392E;
            --lm-danger-soft: #FBEBE8;
            --lm-info: #2E5C8A;
            --lm-info-soft: #E9F0F7;
            --lm-font-serif: 'Fraunces', Georgia, 'Iowan Old Style', serif;
            --lm-font-sans: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

        }


        /*
        |--------------------------------------------------------------------------
        | This file had no local <style> block at all — everything below is new,
        | written to match the rest of the app, since assets/css/style.css is
        | what was actually rendering this page before.
        |--------------------------------------------------------------------------
        */

        body {

            font-family: var(--lm-font-sans);

            color: var(--lm-ink-900);

        }


        .page-header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 24px;

        }


        .page-header h1 {

            margin: 0 0 6px;

            font-family: var(--lm-font-serif);

            font-size: 27px;

            font-weight: 600;

            color: var(--lm-ink-900);

            letter-spacing: -.01em;

        }


        .page-header p {

            margin: 0;

            color: var(--lm-ink-500);

            font-size: 14px;

        }


        /*
        |--------------------------------------------------------------------------
        | ALERTS
        |--------------------------------------------------------------------------
        */

        .alert {

            padding: 12px 15px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 13px;

            font-weight: 500;

        }


        .alert-success {

            background: var(--lm-forest-soft);

            border: 1px solid rgba(31, 122, 82, .3);

            color: var(--lm-forest);

        }


        .alert-danger {

            background: var(--lm-danger-soft);

            border: 1px solid rgba(176, 57, 46, .3);

            color: var(--lm-danger);

        }


        /*
        |--------------------------------------------------------------------------
        | FORM CARD
        |--------------------------------------------------------------------------
        */

        .form-card {

            background: var(--lm-surface);

            border: 1px solid var(--lm-line);

            border-radius: 14px;

            padding: 26px;

        }


        /*
        |--------------------------------------------------------------------------
        | FORM GRID
        |--------------------------------------------------------------------------
        */

        .form-grid {

            display: grid;

            grid-template-columns: repeat(2, minmax(0, 1fr));

            gap: 20px;

        }


        .form-grid-full {

            grid-column: 1 / -1;

        }


        .form-group {

            min-width: 0;

        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            color: var(--lm-ink-700);

            font-size: 13px;

            font-weight: 600;

        }


        .form-group input,
        .form-group select,
        .form-group textarea {

            width: 100%;

            box-sizing: border-box;

            padding: 10px 12px;

            border: 1px solid var(--lm-line);

            border-radius: 8px;

            background: var(--lm-surface);

            color: var(--lm-ink-900);

            font-family: inherit;

            font-size: 14px;

            outline: none;

            transition: border-color .15s ease, box-shadow .15s ease;

        }


        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {

            border-color: var(--lm-brass);

            box-shadow: 0 0 0 3px rgba(184, 134, 15, .14);

        }


        .form-group input[readonly] {

            background: var(--lm-surface-tint);

            color: var(--lm-ink-500);

        }


        .form-group textarea {

            resize: vertical;

        }


        /*
        |--------------------------------------------------------------------------
        | BUTTONS
        |--------------------------------------------------------------------------
        */

        .btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            min-height: 40px;

            padding: 0 18px;

            border-radius: 9px;

            border: 1px solid transparent;

            font-size: 13px;

            font-weight: 650;

            text-decoration: none;

            cursor: pointer;

            transition: background .15s ease, transform .15s ease, border-color .15s ease;

        }


        .btn:hover {

            transform: translateY(-1px);

        }


        .btn-primary {

            background: var(--lm-ink-900);

            border-color: var(--lm-ink-900);

            color: var(--lm-brass);

        }


        .btn-primary:hover {

            background: #0F1815;

        }


        .btn-secondary {

            background: var(--lm-surface);

            border-color: var(--lm-line);

            color: var(--lm-ink-700);

        }


        .btn-secondary:hover {

            background: var(--lm-surface-tint);

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 700px) {

            .form-grid {

                grid-template-columns: 1fr;

            }


            .form-grid-full {

                grid-column: auto;

            }


            .page-header {

                flex-direction: column;

            }


            .form-card {

                padding: 18px;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | REDUCED MOTION
        |--------------------------------------------------------------------------
        */

        @media (prefers-reduced-motion: reduce) {

            .btn,
            .form-group input,
            .form-group select,
            .form-group textarea {

                transition: none;

            }

        }

    </style>

</head>

<body>


<?php

require APP_PATH .
    '/views/layouts/sidebar.php';

?>


<div class="main-content">


    <nav class="navbar">

        <div class="page-title">
            Add Borrower
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
                    Add Borrower
                </h1>

                <p>
                    Create a new borrower for your business.
                </p>

            </div>

        </div>


        <?php if (!empty($error)): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <div class="form-card">


            <form
                method="POST"
                action="index.php?url=borrowers/store"
            >


                <div class="form-grid">


                    <div class="form-group">

                        <label>
                            Borrower Code
                        </label>

                        <input
                            type="text"
                            name="borrower_code"
                            value="<?= htmlspecialchars(
                                $borrowerCode ?? ''
                            ) ?>"
                            readonly
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Status
                        </label>

                        <select name="status">

                            <option value="active">
                                Active
                            </option>

                            <option value="inactive">
                                Inactive
                            </option>

                            <option value="blacklisted">
                                Blacklisted
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            First Name *
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Middle Name
                        </label>

                        <input
                            type="text"
                            name="middle_name"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Last Name *
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Gender
                        </label>

                        <select name="gender">

                            <option value="">
                                Select Gender
                            </option>

                            <option value="male">
                                Male
                            </option>

                            <option value="female">
                                Female
                            </option>

                            <option value="other">
                                Other
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Date of Birth
                        </label>

                        <input
                            type="date"
                            name="date_of_birth"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            placeholder="09XXXXXXXXX"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Occupation
                        </label>

                        <input
                            type="text"
                            name="occupation"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Employer
                        </label>

                        <input
                            type="text"
                            name="employer"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Monthly Income
                        </label>

                        <input
                            type="number"
                            name="monthly_income"
                            step="0.01"
                            min="0"
                            value="0.00"
                        >

                    </div>


                    <div class="form-group form-grid-full">

                        <label>
                            Address
                        </label>

                        <textarea
                            name="address"
                            rows="3"
                        ></textarea>

                    </div>


                    <div class="form-group">

                        <label>
                            City
                        </label>

                        <input
                            type="text"
                            name="city"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Province
                        </label>

                        <input
                            type="text"
                            name="province"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Postal Code
                        </label>

                        <input
                            type="text"
                            name="postal_code"
                        >

                    </div>


                    <div class="form-group form-grid-full">

                        <label>
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            rows="4"
                        ></textarea>

                    </div>


                </div>


                <div
                    style="
                        display:flex;
                        gap:10px;
                        margin-top:20px;
                    "
                >

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save Borrower
                    </button>


                    <a
                        href="index.php?url=borrowers"
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
