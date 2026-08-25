<?php

/*
|--------------------------------------------------------------------------
| SAFE DEFAULTS
|--------------------------------------------------------------------------
*/

$user =
    $user
    ?? Auth::user()
    ?? [];

$business =
    $business
    ?? Auth::business()
    ?? [];

$tenantRole =
    $tenantRole
    ?? Auth::tenantRole()
    ?? 'User';

$currentUrl =
    $currentUrl
    ?? 'borrowers';

$borrowers =
    $borrowers
    ?? [];


/*
|--------------------------------------------------------------------------
| DISPLAY DATA
|--------------------------------------------------------------------------
*/

$userName =
    $user['full_name']
    ??
    $user['username']
    ??
    'User';

$businessName =
    $business['name']
    ??
    'your business';


/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

if (!function_exists('borrowerStatusClass')) {

    function borrowerStatusClass(
        string $status
    ): string {

        return match (
            strtolower(trim($status))
        ) {

            'active' =>
                'borrower-status-active',

            'inactive' =>
                'borrower-status-inactive',

            'pending' =>
                'borrower-status-pending',

            'approved' =>
                'borrower-status-approved',

            'rejected',
            'cancelled',
            'blocked' =>
                'borrower-status-danger',

            default =>
                'borrower-status-default'
        };

    }

}


/*
|--------------------------------------------------------------------------
| BORROWER COUNT
|--------------------------------------------------------------------------
*/

$borrowerCount =
    count($borrowers);

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
    Borrowers | <?= htmlspecialchars($businessName) ?>
</title>


<link
    rel="stylesheet"
    href="assets/css/style.css"
>


<style>

    /*
    |--------------------------------------------------------------------------
    | BORROWERS PAGE
    |--------------------------------------------------------------------------
    */

    .borrowers-page {

        width: 100%;

    }


    /*
    |--------------------------------------------------------------------------
    | PAGE HEADER
    |--------------------------------------------------------------------------
    */

    .borrowers-header {

        display: flex;

        align-items: flex-start;

        justify-content: space-between;

        gap: 20px;

        margin-bottom: 25px;

    }


    .borrowers-heading h1 {

        margin: 0;

        color: #111827;

        font-size: 28px;

        line-height: 1.2;

        font-weight: 750;

        letter-spacing: -.025em;

    }


    .borrowers-heading p {

        margin: 7px 0 0;

        color: #6b7280;

        font-size: 14px;

        line-height: 1.5;

    }


    .borrowers-header-actions {

        display: flex;

        align-items: center;

        gap: 10px;

        flex-shrink: 0;

    }


    .borrower-count-badge {

        display: inline-flex;

        align-items: center;

        gap: 7px;

        height: 40px;

        padding: 0 13px;

        border: 1px solid #e5e7eb;

        border-radius: 10px;

        background: #ffffff;

        color: #6b7280;

        font-size: 12px;

        font-weight: 650;

        white-space: nowrap;

    }


    .borrower-count-badge strong {

        color: #111827;

        font-weight: 750;

    }


    /*
    |--------------------------------------------------------------------------
    | SUMMARY CARD
    |--------------------------------------------------------------------------
    */

    .borrowers-summary {

        display: grid;

        grid-template-columns:
            repeat(
                3,
                minmax(0, 1fr)
            );

        gap: 15px;

        margin-bottom: 20px;

    }


    .borrower-summary-card {

        position: relative;

        overflow: hidden;

        padding: 17px 18px;

        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 14px;

        box-shadow:
            0 2px 7px
            rgba(
                0,
                0,
                0,
                .035
            );

    }


    .borrower-summary-label {

        color: #6b7280;

        font-size: 11px;

        font-weight: 650;

    }


    .borrower-summary-value {

        margin-top: 5px;

        color: #111827;

        font-size: 22px;

        line-height: 1.2;

        font-weight: 750;

    }


    .borrower-summary-description {

        margin-top: 4px;

        color: #9ca3af;

        font-size: 10px;

    }


    /*
    |--------------------------------------------------------------------------
    | MAIN PANEL
    |--------------------------------------------------------------------------
    */

    .borrowers-panel {

        background: #ffffff;

        border:
            1px solid #e5e7eb;

        border-radius: 15px;

        overflow: visible;

        box-shadow:
            0 2px 7px
            rgba(
                0,
                0,
                0,
                .035
            );

    }


    .borrowers-panel-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 15px;

        padding: 18px 20px;

        border-bottom:
            1px solid #f0f1f3;

    }


    .borrowers-panel-title {

        margin: 0;

        color: #111827;

        font-size: 16px;

        font-weight: 700;

    }


    .borrowers-panel-subtitle {

        margin: 4px 0 0;

        color: #9ca3af;

        font-size: 11px;

    }


    /*
    |--------------------------------------------------------------------------
    | TABLE WRAPPER
    |--------------------------------------------------------------------------
    */

    .borrowers-table-wrapper {

        width: 100%;

        overflow-x: auto;

        -webkit-overflow-scrolling: touch;

    }


    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    .borrowers-table {

        width: 100%;

        min-width: 850px;

        border-collapse: collapse;

    }


    .borrowers-table th {

        padding:
            12px 20px;

        background: #f9fafb;

        color: #6b7280;

        font-size: 10px;

        font-weight: 750;

        text-align: left;

        text-transform: uppercase;

        letter-spacing: .045em;

        white-space: nowrap;

    }


    .borrowers-table td {

        padding:
            14px 20px;

        color: #4b5563;

        font-size: 12px;

        border-top:
            1px solid #f3f4f6;

        vertical-align: middle;

        white-space: nowrap;

    }


    .borrowers-table tbody tr {

        transition:
            background .15s ease;

    }


    .borrowers-table tbody tr:hover {

        background: #fafafa;

    }


    /*
    |--------------------------------------------------------------------------
    | BORROWER IDENTITY
    |--------------------------------------------------------------------------
    */

    .borrower-identity {

        display: flex;

        align-items: center;

        gap: 11px;

        min-width: 180px;

    }


    .borrower-avatar {

        width: 38px;

        height: 38px;

        min-width: 38px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 11px;

        background: #f3f4f6;

        color: #374151;

        font-size: 13px;

        font-weight: 750;

        text-transform: uppercase;

    }


    .borrower-identity-details {

        min-width: 0;

    }


    .borrower-name {

        max-width: 190px;

        overflow: hidden;

        text-overflow: ellipsis;

        white-space: nowrap;

        color: #111827;

        font-size: 13px;

        font-weight: 650;

    }


    .borrower-code {

        margin-top: 3px;

        color: #9ca3af;

        font-size: 10px;

        font-weight: 550;

    }


    /*
    |--------------------------------------------------------------------------
    | CONTACT INFORMATION
    |--------------------------------------------------------------------------
    */

    .borrower-contact {

        color: #4b5563;

        font-size: 12px;

    }


    .borrower-contact.muted {

        color: #9ca3af;

    }


    /*
    |--------------------------------------------------------------------------
    | INCOME
    |--------------------------------------------------------------------------
    */

    .borrower-income {

        color: #111827;

        font-size: 12px;

        font-weight: 700;

    }


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    .borrower-status {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 5px;

        padding:
            5px 9px;

        border-radius: 999px;

        font-size: 10px;

        font-weight: 750;

        text-transform: capitalize;

        white-space: nowrap;

    }


    .borrower-status::before {

        content: '';

        width: 5px;

        height: 5px;

        border-radius: 50%;

        background: currentColor;

    }


    .borrower-status-active {

        background: #ecfdf5;

        color: #047857;

    }


    .borrower-status-inactive {

        background: #f3f4f6;

        color: #6b7280;

    }


    .borrower-status-pending {

        background: #fffbeb;

        color: #b45309;

    }


    .borrower-status-approved {

        background: #eff6ff;

        color: #1d4ed8;

    }


    .borrower-status-danger {

        background: #fef2f2;

        color: #b91c1c;

    }


    .borrower-status-default {

        background: #f3f4f6;

        color: #4b5563;

    }


    /*
    |--------------------------------------------------------------------------
    | ACTION MENU
    |--------------------------------------------------------------------------
    */

    .borrower-action-menu {

        position: relative;

        display: inline-block;

    }


    .borrower-action-button {

        width: 36px;

        height: 36px;

        display: flex;

        align-items: center;

        justify-content: center;

        padding: 0;

        border:
            1px solid #e5e7eb;

        border-radius: 9px;

        background: #ffffff;

        color: #6b7280;

        cursor: pointer;

        font-size: 20px;

        line-height: 1;

        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease,
            box-shadow .15s ease;

    }


    .borrower-action-button:hover,

    .borrower-action-button[aria-expanded="true"] {

        background: #f9fafb;

        border-color: #d1d5db;

        color: #111827;

        box-shadow:
            0 2px 6px
            rgba(
                0,
                0,
                0,
                .05
            );

    }


    .borrower-action-dropdown {

        position: absolute;

        right: 0;

        top: calc(100% + 7px);

        width: 190px;

        padding: 6px;

        background: #ffffff;

        border:
            1px solid #e5e7eb;

        border-radius: 11px;

        box-shadow:
            0 12px 30px
            rgba(
                0,
                0,
                0,
                .12
            );

        z-index: 9999;

        display: none;

        animation:
            borrowerDropdownIn
            .14s ease;

    }


    .borrower-action-dropdown.active {

        display: block;

    }


    @keyframes borrowerDropdownIn {

        from {

            opacity: 0;

            transform:
                translateY(-4px);

        }

        to {

            opacity: 1;

            transform:
                translateY(0);

        }

    }


    .borrower-action-item {

        width: 100%;

        display: flex;

        align-items: center;

        gap: 10px;

        padding:
            9px 10px;

        border: none;

        border-radius: 8px;

        background: transparent;

        color: #374151;

        text-decoration: none;

        font-size: 12px;

        font-weight: 550;

        cursor: pointer;

        text-align: left;

        box-sizing: border-box;

        transition:
            background .15s ease,
            color .15s ease;

    }


    .borrower-action-item:hover {

        background: #f3f4f6;

        color: #111827;

    }


    .borrower-action-item.danger {

        color: #dc3545;

    }


    .borrower-action-item.danger:hover {

        background: #fff1f2;

        color: #b91c1c;

    }


    .borrower-action-icon {

        width: 22px;

        height: 22px;

        display: flex;

        align-items: center;

        justify-content: center;

        flex-shrink: 0;

        font-size: 13px;

    }


    /*
    |--------------------------------------------------------------------------
    | EMPTY STATE
    |--------------------------------------------------------------------------
    */

    .borrowers-empty {

        padding:
            65px 25px;

        text-align: center;

    }


    .borrowers-empty-icon {

        width: 58px;

        height: 58px;

        margin:
            0 auto 16px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 15px;

        background: #f3f4f6;

        color: #6b7280;

        font-size: 21px;

        font-weight: 750;

    }


    .borrowers-empty h3 {

        margin: 0;

        color: #111827;

        font-size: 16px;

        font-weight: 700;

    }


    .borrowers-empty p {

        margin:
            6px 0 20px;

        color: #9ca3af;

        font-size: 12px;

    }


    /*
    |--------------------------------------------------------------------------
    | PANEL FOOTER
    |--------------------------------------------------------------------------
    */

    .borrowers-panel-footer {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 15px;

        padding:
            13px 20px;

        background: #f9fafb;

        border-top:
            1px solid #e5e7eb;

        border-radius:
            0 0 15px 15px;

    }


    .borrowers-footer-text {

        color: #9ca3af;

        font-size: 10px;

    }


    .borrowers-footer-count {

        color: #6b7280;

        font-size: 11px;

        font-weight: 650;

    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE - TABLET
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1000px) {

        .borrowers-summary {

            grid-template-columns:
                repeat(
                    2,
                    minmax(0, 1fr)
                );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE - MOBILE
    |--------------------------------------------------------------------------
    */

    @media (max-width: 700px) {

        .borrowers-header {

            flex-direction: column;

            margin-bottom: 20px;

        }


        .borrowers-heading h1 {

            font-size: 23px;

        }


        .borrowers-heading p {

            font-size: 12px;

        }


        .borrowers-header-actions {

            width: 100%;

            justify-content: space-between;

        }


        .borrower-count-badge {

            flex: 1;

            justify-content: center;

        }


        .borrowers-summary {

            grid-template-columns: 1fr;

            gap: 10px;

            margin-bottom: 15px;

        }


        .borrower-summary-card {

            padding: 15px;

            border-radius: 12px;

        }


        .borrowers-panel {

            border-radius: 13px;

        }


        .borrowers-panel-header {

            padding: 15px 16px;

        }


        .borrowers-panel-title {

            font-size: 15px;

        }


        .borrowers-table {

            min-width: 760px;

        }


        .borrowers-panel-footer {

            padding:
                12px 16px;

            border-radius:
                0 0 13px 13px;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | VERY SMALL DEVICES
    |--------------------------------------------------------------------------
    */

    @media (max-width: 400px) {

        .borrowers-header-actions {

            flex-direction: column;

            align-items: stretch;

        }


        .borrower-count-badge {

            width: 100%;

            box-sizing: border-box;

        }


        .borrowers-header-actions .btn {

            width: 100%;

            text-align: center;

            box-sizing: border-box;

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


<!-- =====================================================
     NAVBAR
====================================================== -->

<nav class="navbar">

    <div class="page-title">

        Borrowers

    </div>


    <div class="user-info">

        <span class="user-name">

            <?= htmlspecialchars(
                $userName
            ) ?>

        </span>


        <span class="badge">

            <?= htmlspecialchars(
                $tenantRole
            ) ?>

        </span>

    </div>

</nav>


<!-- =====================================================
     PAGE CONTENT
====================================================== -->

<div class="container borrowers-page">


    <!-- =================================================
         PAGE HEADER
    ================================================== -->

    <div class="borrowers-header">

        <div class="borrowers-heading">

            <h1>
                Borrowers
            </h1>

            <p>

                Manage and view borrowers for

                <?= htmlspecialchars(
                    $businessName
                ) ?>.

            </p>

        </div>


        <div class="borrowers-header-actions">

            <div class="borrower-count-badge">

                <strong>
                    <?= number_format(
                        $borrowerCount
                    ) ?>
                </strong>

                <span>
                    <?= $borrowerCount === 1
                        ? 'Borrower'
                        : 'Borrowers'
                    ?>
                </span>

            </div>


            <a
                href="index.php?url=borrowers/create"
                class="btn btn-primary"
            >

                + Add Borrower

            </a>

        </div>

    </div>


    <!-- =================================================
         SUMMARY
    ================================================== -->

    <div class="borrowers-summary">


        <div class="borrower-summary-card">

            <div class="borrower-summary-label">

                Total Borrowers

            </div>

            <div class="borrower-summary-value">

                <?= number_format(
                    $borrowerCount
                ) ?>

            </div>

            <div class="borrower-summary-description">

                Registered in your business

            </div>

        </div>


        <div class="borrower-summary-card">

            <div class="borrower-summary-label">

                Active Borrowers

            </div>

            <div class="borrower-summary-value">

                <?php

                $activeBorrowers = 0;

                foreach (
                    $borrowers
                    as $summaryBorrower
                ) {

                    if (
                        strtolower(
                            trim(
                                $summaryBorrower['status']
                                ?? 'active'
                            )
                        )
                        ===
                        'active'
                    ) {

                        $activeBorrowers++;

                    }

                }

                ?>

                <?= number_format(
                    $activeBorrowers
                ) ?>

            </div>

            <div class="borrower-summary-description">

                Currently active borrowers

            </div>

        </div>


        <div class="borrower-summary-card">

            <div class="borrower-summary-label">

                Total Monthly Income

            </div>

            <div class="borrower-summary-value">

                <?php

                $totalMonthlyIncome = 0;

                foreach (
                    $borrowers
                    as $summaryBorrower
                ) {

                    $totalMonthlyIncome +=
                        (float) (
                            $summaryBorrower['monthly_income']
                            ?? 0
                        );

                }

                ?>

                ₱<?= number_format(
                    $totalMonthlyIncome,
                    2
                ) ?>

            </div>

            <div class="borrower-summary-description">

                Combined declared income

            </div>

        </div>


    </div>


    <!-- =================================================
         BORROWERS PANEL
    ================================================== -->

    <div class="borrowers-panel">


        <div class="borrowers-panel-header">

            <div>

                <h2 class="borrowers-panel-title">

                    Borrower Directory

                </h2>

                <p class="borrowers-panel-subtitle">

                    View, edit, and manage borrower records.

                </p>

            </div>

        </div>


        <?php if (empty($borrowers)): ?>


            <!-- =========================================
                 EMPTY STATE
            ========================================== -->

            <div class="borrowers-empty">

                <div class="borrowers-empty-icon">

                    B

                </div>


                <h3>

                    No Borrowers Found

                </h3>


                <p>

                    You haven't added any borrowers yet.

                </p>


                <a
                    href="index.php?url=borrowers/create"
                    class="btn btn-primary"
                >

                    + Add Your First Borrower

                </a>

            </div>


        <?php else: ?>


            <!-- =========================================
                 TABLE
            ========================================== -->

            <div class="borrowers-table-wrapper">

                <table class="borrowers-table">


                    <thead>

                        <tr>

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


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | BORROWER DATA
                        |--------------------------------------------------------------------------
                        */

                        $borrowerId =
                            (int) (
                                $borrower['id']
                                ?? 0
                            );


                        $borrowerCode =
                            $borrower['borrower_code']
                            ??
                            '-';


                        $firstName =
                            trim(
                                $borrower['first_name']
                                ?? ''
                            );


                        $middleName =
                            trim(
                                $borrower['middle_name']
                                ?? ''
                            );


                        $lastName =
                            trim(
                                $borrower['last_name']
                                ?? ''
                            );


                        $borrowerName =
                            trim(
                                $firstName
                                . ' '
                                . $middleName
                                . ' '
                                . $lastName
                            );


                        if (
                            $borrowerName === ''
                        ) {

                            $borrowerName =
                                'Unnamed Borrower';

                        }


                        $phone =
                            trim(
                                $borrower['phone']
                                ?? ''
                            );


                        $email =
                            trim(
                                $borrower['email']
                                ?? ''
                            );


                        $monthlyIncome =
                            (float) (
                                $borrower['monthly_income']
                                ?? 0
                            );


                        $status =
                            strtolower(
                                trim(
                                    $borrower['status']
                                    ?? 'active'
                                )
                            );


                        $statusClass =
                            borrowerStatusClass(
                                $status
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | AVATAR INITIALS
                        |--------------------------------------------------------------------------
                        */

                        $avatarInitials =
                            '';

                        if (
                            $firstName !== ''
                        ) {

                            $avatarInitials .=
                                strtoupper(
                                    substr(
                                        $firstName,
                                        0,
                                        1
                                    )
                                );

                        }

                        if (
                            $lastName !== ''
                        ) {

                            $avatarInitials .=
                                strtoupper(
                                    substr(
                                        $lastName,
                                        0,
                                        1
                                    )
                                );

                        }


                        if (
                            $avatarInitials === ''
                        ) {

                            $avatarInitials = 'B';

                        }

                        ?>


                        <tr>


                            <!-- =================================
                                 BORROWER
                            ================================== -->

                            <td>

                                <div class="borrower-identity">


                                    <div class="borrower-avatar">

                                        <?= htmlspecialchars(
                                            $avatarInitials
                                        ) ?>

                                    </div>


                                    <div class="borrower-identity-details">

                                        <div class="borrower-name">

                                            <?= htmlspecialchars(
                                                $borrowerName
                                            ) ?>

                                        </div>


                                        <div class="borrower-code">

                                            <?= htmlspecialchars(
                                                $borrowerCode
                                            ) ?>

                                        </div>

                                    </div>


                                </div>

                            </td>


                            <!-- =================================
                                 PHONE
                            ================================== -->

                            <td>

                                <span
                                    class="borrower-contact
                                    <?= $phone === ''
                                        ? 'muted'
                                        : ''
                                    ?>"
                                >

                                    <?= htmlspecialchars(
                                        $phone !== ''
                                            ? $phone
                                            : 'No phone number'
                                    ) ?>

                                </span>

                            </td>


                            <!-- =================================
                                 EMAIL
                            ================================== -->

                            <td>

                                <span
                                    class="borrower-contact
                                    <?= $email === ''
                                        ? 'muted'
                                        : ''
                                    ?>"
                                >

                                    <?= htmlspecialchars(
                                        $email !== ''
                                            ? $email
                                            : 'No email'
                                    ) ?>

                                </span>

                            </td>


                            <!-- =================================
                                 MONTHLY INCOME
                            ================================== -->

                            <td>

                                <span class="borrower-income">

                                    ₱<?= number_format(
                                        $monthlyIncome,
                                        2
                                    ) ?>

                                </span>

                            </td>


                            <!-- =================================
                                 STATUS
                            ================================== -->

                            <td>

                                <span
                                    class="borrower-status
                                    <?= htmlspecialchars(
                                        $statusClass
                                    ) ?>"
                                >

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $status
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <!-- =================================
                                 ACTIONS
                            ================================== -->

                            <td>


                                <div
                                    class="borrower-action-menu"
                                >


                                    <button
                                        type="button"
                                        class="borrower-action-button"
                                        onclick="
                                            toggleBorrowerActions(
                                                <?= $borrowerId ?>
                                            )
                                        "
                                        aria-label="Borrower actions"
                                        aria-expanded="false"
                                        data-borrower-id="<?= $borrowerId ?>"
                                    >

                                        ⋮

                                    </button>


                                    <div
                                        class="borrower-action-dropdown"
                                        id="borrower-actions-<?= $borrowerId ?>"
                                    >


                                        <!-- VIEW -->

                                        <a
                                            href="index.php?url=borrowers/details&id=<?= $borrowerId ?>"
                                            class="borrower-action-item"
                                            onclick="
                                                closeBorrowerActions();
                                            "
                                        >

                                            <span
                                                class="borrower-action-icon"
                                            >
                                                👁
                                            </span>

                                            <span>
                                                View Details
                                            </span>

                                        </a>


                                        <!-- EDIT -->

                                        <a
                                            href="index.php?url=borrowers/edit&id=<?= $borrowerId ?>"
                                            class="borrower-action-item"
                                            onclick="
                                                closeBorrowerActions();
                                            "
                                        >

                                            <span
                                                class="borrower-action-icon"
                                            >
                                                ✏️
                                            </span>

                                            <span>
                                                Edit Borrower
                                            </span>

                                        </a>


                                        <!-- DELETE -->

                                        <a
                                            href="index.php?url=borrowers/delete&id=<?= $borrowerId ?>"
                                            class="borrower-action-item danger"
                                            onclick="
                                                closeBorrowerActions();

                                                return confirm(
                                                    'Are you sure you want to delete this borrower?'
                                                );
                                            "
                                        >

                                            <span
                                                class="borrower-action-icon"
                                            >
                                                🗑
                                            </span>

                                            <span>
                                                Delete Borrower
                                            </span>

                                        </a>


                                    </div>


                                </div>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


            <!-- =========================================
                 FOOTER
            ========================================== -->

            <div class="borrowers-panel-footer">

                <span class="borrowers-footer-text">

                    Showing registered borrowers

                </span>


                <span class="borrowers-footer-count">

                    <?= number_format(
                        $borrowerCount
                    ) ?>

                    <?= $borrowerCount === 1
                        ? 'borrower'
                        : 'borrowers'
                    ?>

                </span>

            </div>


        <?php endif; ?>


    </div>


</div>


</div>

<script>

/*
|--------------------------------------------------------------------------
| TOGGLE BORROWER ACTIONS
|--------------------------------------------------------------------------
*/

function toggleBorrowerActions(
    borrowerId
)
{

    const currentDropdown =
        document.getElementById(
            'borrower-actions-' +
            borrowerId
        );


    if (!currentDropdown) {

        return;

    }


    const wasActive =
        currentDropdown.classList.contains(
            'active'
        );


    closeBorrowerActions();


    if (!wasActive) {

        currentDropdown.classList.add(
            'active'
        );


        const button =
            document.querySelector(
                '[data-borrower-id="' +
                borrowerId +
                '"]'
            );


        if (button) {

            button.setAttribute(
                'aria-expanded',
                'true'
            );

        }

    }

}



/*
|--------------------------------------------------------------------------
| CLOSE ALL ACTION MENUS
|--------------------------------------------------------------------------
*/

function closeBorrowerActions()
{

    document
        .querySelectorAll(
            '.borrower-action-dropdown.active'
        )
        .forEach(
            function(dropdown)
            {

                dropdown.classList.remove(
                    'active'
                );

            }
        );


    document
        .querySelectorAll(
            '.borrower-action-button[aria-expanded="true"]'
        )
        .forEach(
            function(button)
            {

                button.setAttribute(
                    'aria-expanded',
                    'false'
                );

            }
        );

}



/*
|--------------------------------------------------------------------------
| CLOSE WHEN CLICKING OUTSIDE
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'click',
    function(event)
    {

        if (
            !event.target.closest(
                '.borrower-action-menu'
            )
        ) {

            closeBorrowerActions();

        }

    }
);



/*
|--------------------------------------------------------------------------
| CLOSE WITH ESCAPE
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'keydown',
    function(event)
    {

        if (
            event.key === 'Escape'
        ) {

            closeBorrowerActions();

        }

    }
);

</script>

</body>

</html>
