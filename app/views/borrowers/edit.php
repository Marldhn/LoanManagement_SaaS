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
        Edit Borrower | Loan Management
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
            Edit Borrower
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
                    Edit Borrower
                </h1>

                <p>
                    Update borrower information.
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
                action="index.php?url=borrowers/update"
            >


                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $borrower['id'] ?>"
                >


                <div class="form-grid">


                    <div class="form-group">

                        <label>
                            Borrower Code
                        </label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $borrower['borrower_code']
                            ) ?>"
                            readonly
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Status
                        </label>

                        <select name="status">

                            <option
                                value="active"
                                <?= ($borrower['status'] ?? '') === 'active'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Active
                            </option>

                            <option
                                value="inactive"
                                <?= ($borrower['status'] ?? '') === 'inactive'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Inactive
                            </option>

                            <option
                                value="blacklisted"
                                <?= ($borrower['status'] ?? '') === 'blacklisted'
                                    ? 'selected'
                                    : '' ?>
                            >
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
                            value="<?= htmlspecialchars(
                                $borrower['first_name']
                                ?? ''
                            ) ?>"
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
                            value="<?= htmlspecialchars(
                                $borrower['middle_name']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Last Name *
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            value="<?= htmlspecialchars(
                                $borrower['last_name']
                                ?? ''
                            ) ?>"
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

                            <option
                                value="male"
                                <?= ($borrower['gender'] ?? '') === 'male'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Male
                            </option>

                            <option
                                value="female"
                                <?= ($borrower['gender'] ?? '') === 'female'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Female
                            </option>

                            <option
                                value="other"
                                <?= ($borrower['gender'] ?? '') === 'other'
                                    ? 'selected'
                                    : '' ?>
                            >
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
                            value="<?= htmlspecialchars(
                                $borrower['date_of_birth']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="<?= htmlspecialchars(
                                $borrower['phone']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars(
                                $borrower['email']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Occupation
                        </label>

                        <input
                            type="text"
                            name="occupation"
                            value="<?= htmlspecialchars(
                                $borrower['occupation']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Employer
                        </label>

                        <input
                            type="text"
                            name="employer"
                            value="<?= htmlspecialchars(
                                $borrower['employer']
                                ?? ''
                            ) ?>"
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
                            value="<?= htmlspecialchars(
                                $borrower['monthly_income']
                                ?? '0.00'
                            ) ?>"
                        >

                    </div>


                    <div class="form-group form-grid-full">

                        <label>
                            Address
                        </label>

                        <textarea
                            name="address"
                            rows="3"
                        ><?= htmlspecialchars(
                            $borrower['address']
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <div class="form-group">

                        <label>
                            City
                        </label>

                        <input
                            type="text"
                            name="city"
                            value="<?= htmlspecialchars(
                                $borrower['city']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Province
                        </label>

                        <input
                            type="text"
                            name="province"
                            value="<?= htmlspecialchars(
                                $borrower['province']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Postal Code
                        </label>

                        <input
                            type="text"
                            name="postal_code"
                            value="<?= htmlspecialchars(
                                $borrower['postal_code']
                                ?? ''
                            ) ?>"
                        >

                    </div>


                    <div class="form-group form-grid-full">

                        <label>
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            rows="4"
                        ><?= htmlspecialchars(
                            $borrower['notes']
                            ?? ''
                        ) ?></textarea>

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
                        Update Borrower
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