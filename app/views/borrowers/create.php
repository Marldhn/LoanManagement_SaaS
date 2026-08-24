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