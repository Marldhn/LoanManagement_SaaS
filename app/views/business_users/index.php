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

    <style>

        /*
        |--------------------------------------------------------------------------
        | USERS PAGE
        |--------------------------------------------------------------------------
        */

        .users-page {
            width: 100%;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE CARD
        |--------------------------------------------------------------------------
        */

        .users-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: visible;
            box-shadow:
                0 2px 8px rgba(0, 0, 0, .04);
        }


        .users-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
        }


        .users-card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }


        .users-card-description {
            margin: 5px 0 0;
            font-size: 13px;
            color: #6b7280;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        .users-table-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
        }


        .users-table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
        }


        .users-table th {
            padding: 13px 18px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }


        .users-table td {
            padding: 15px 18px;
            border-top: 1px solid #f3f4f6;
            color: #374151;
            font-size: 13px;
            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        .user-name-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }


        .user-avatar {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 10px;
            background: #f3f4f6;
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }


        .user-name-main {
            color: #111827;
            font-weight: 700;
        }


        .user-name-secondary {
            margin-top: 3px;
            color: #9ca3af;
            font-size: 11px;
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        .users-status {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
        }


        .users-status-active {
            background: #ecfdf5;
            color: #047857;
        }


        .users-status-inactive {
            background: #fef2f2;
            color: #b91c1c;
        }


        /*
        |--------------------------------------------------------------------------
        | ACTION DROPDOWN
        |--------------------------------------------------------------------------
        */

        .user-action {
            position: relative;
            display: inline-block;
        }


        .user-action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 36px;
            padding: 0 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #ffffff;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition:
                background .15s ease,
                border-color .15s ease;
        }


        .user-action-button:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }


        .user-action-arrow {
            font-size: 10px;
            transition: transform .15s ease;
        }


        .user-action.active .user-action-arrow {
            transform: rotate(180deg);
        }


        .user-action-menu {
            position: absolute;
            top: calc(100% + 7px);
            right: 0;
            z-index: 9999;
            width: 200px;
            padding: 6px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow:
                0 12px 30px rgba(0, 0, 0, .12);
            display: none;
        }


        .user-action.active .user-action-menu {
            display: block;
        }


        .user-action-menu a,
        .user-action-menu button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            border: none;
            border-radius: 7px;
            background: transparent;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            text-align: left;
            cursor: pointer;
        }


        .user-action-menu a:hover,
        .user-action-menu button:hover {
            background: #f3f4f6;
        }


        .user-action-menu .danger-action {
            color: #dc2626;
        }


        .user-action-menu .danger-action:hover {
            background: #fef2f2;
        }


        .user-action-menu .success-action {
            color: #047857;
        }


        .user-action-menu .success-action:hover {
            background: #ecfdf5;
        }


        .user-action-divider {
            height: 1px;
            margin: 5px 4px;
            background: #f3f4f6;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY
        |--------------------------------------------------------------------------
        */

        .users-empty {
            padding: 55px 20px;
            text-align: center;
        }


        .users-empty-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 14px;
            border-radius: 13px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-weight: 700;
        }


        .users-empty h3 {
            margin: 0;
            color: #111827;
            font-size: 16px;
        }


        .users-empty p {
            margin: 6px 0 0;
            color: #9ca3af;
            font-size: 13px;
        }


        /*
        |--------------------------------------------------------------------------
        | MODAL
        |--------------------------------------------------------------------------
        */

        .password-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(17, 24, 39, .55);
        }


        .password-modal-overlay.active {
            display: flex;
        }


        .password-modal {
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow:
                0 25px 60px rgba(0, 0, 0, .20);
            overflow: hidden;
            animation: passwordModalIn .15s ease;
        }


        @keyframes passwordModalIn {

            from {
                opacity: 0;
                transform: translateY(-10px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

        }


        .password-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
        }


        .password-modal-title {
            margin: 0;
            color: #111827;
            font-size: 18px;
            font-weight: 700;
        }


        .password-modal-user {
            margin-top: 5px;
            color: #6b7280;
            font-size: 13px;
        }


        .password-modal-close {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            background: #f3f4f6;
            color: #6b7280;
            font-size: 18px;
            cursor: pointer;
        }


        .password-modal-close:hover {
            background: #e5e7eb;
            color: #111827;
        }


        .password-modal-body {
            padding: 20px;
        }


        .password-modal-info {
            margin-bottom: 18px;
            padding: 11px 12px;
            border-radius: 9px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
        }


        .password-field {
            margin-bottom: 16px;
        }


        .password-field label {
            display: block;
            margin-bottom: 7px;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
        }


        .password-field input {
            width: 100%;
            box-sizing: border-box;
            height: 42px;
            padding: 0 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-size: 13px;
            color: #111827;
        }


        .password-field input:focus {
            border-color: #9ca3af;
            box-shadow:
                0 0 0 3px rgba(107, 114, 128, .10);
        }


        .password-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 20px;
            border-top: 1px solid #f0f0f0;
        }


        .password-cancel-button {
            height: 38px;
            padding: 0 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #ffffff;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }


        .password-cancel-button:hover {
            background: #f9fafb;
        }


        .password-submit-button {
            height: 38px;
            padding: 0 16px;
            border: none;
            border-radius: 8px;
            background: #111827;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }


        .password-submit-button:hover {
            background: #1f2937;
        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 700px) {

            .users-card-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .users-card-header .btn {
                width: 100%;
                text-align: center;
            }

            .password-modal {
                max-width: 100%;
            }

        }

    </style>

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
                    ??
                    $user['username']
                    ??
                    'Administrator'
                ) ?>

            </span>


            <span class="badge">

                <?= htmlspecialchars(
                    $tenantRole
                    ??
                    'Administrator'
                ) ?>

            </span>

        </div>

    </nav>


    <!-- =====================================================
         CONTENT
    ====================================================== -->

    <div class="container users-page">


        <!-- =================================================
             PAGE HEADER
        ================================================== -->

        <div class="page-header">

            <div>

                <h1>
                    Business Users
                </h1>

                <p>

                    Manage users and their access to

                    <?= htmlspecialchars(
                        $business['name']
                        ??
                        'your business'
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
             ALERTS
        ================================================== -->

        <?php if ($success): ?>

            <div class="alert alert-success">

                <?= htmlspecialchars($success) ?>

            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             USERS CARD
        ================================================== -->

        <div class="users-card">


            <div class="users-card-header">

                <div>

                    <h2 class="users-card-title">
                        Users
                    </h2>

                    <p class="users-card-description">
                        Manage business members and account access.
                    </p>

                </div>


                <div>

                    <strong>
                        <?= number_format(
                            count($users ?? [])
                        ) ?>
                    </strong>

                    users

                </div>

            </div>


            <?php if (!empty($users)): ?>


                <div class="users-table-wrapper">

                    <table class="users-table">

                        <thead>

                            <tr>

                                <th>
                                    User
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

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach ($users as $businessUser): ?>

                            <?php

                            $membershipStatus =
                                $businessUser['membership_status']
                                ??
                                'inactive';


                            $tenantUserRole =
                                $businessUser['tenant_role']
                                ??
                                'staff';


                            $userId =
                                (int) (
                                    $businessUser['user_id']
                                    ??
                                    0
                                );


                            $businessUserId =
                                (int) (
                                    $businessUser['business_user_id']
                                    ??
                                    0
                                );


                            $fullName =
                                trim(
                                    $businessUser['full_name']
                                    ??
                                    ''
                                );


                            $username =
                                $businessUser['username']
                                ??
                                'User';


                            $initialSource =
                                $fullName !== ''
                                    ? $fullName
                                    : $username;


                            $initial =
                                strtoupper(
                                    substr(
                                        $initialSource,
                                        0,
                                        1
                                    )
                                );


                            $isOwner =
                                $tenantUserRole === 'owner';

                            ?>



                            <tr>


                                <!-- USER -->

                                <td>

                                    <div class="user-name-cell">

                                        <div class="user-avatar">

                                            <?= htmlspecialchars(
                                                $initial
                                            ) ?>

                                        </div>


                                        <div>

                                            <div class="user-name-main">

                                                <?= htmlspecialchars(
                                                    $fullName !== ''
                                                        ? $fullName
                                                        : 'Unnamed User'
                                                ) ?>

                                            </div>


                                            <div class="user-name-secondary">

                                                <?= htmlspecialchars(
                                                    $businessUser[
                                                        'system_role'
                                                    ]
                                                    ??
                                                    'user'
                                                ) ?>

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <!-- USERNAME -->

                                <td>

                                    <?= htmlspecialchars(
                                        $businessUser['username']
                                        ??
                                        '—'
                                    ) ?>

                                </td>


                                <!-- EMAIL -->

                                <td>

                                    <?= htmlspecialchars(
                                        $businessUser['email']
                                        ??
                                        '—'
                                    ) ?>

                                </td>


                                <!-- ROLE -->

                                <td>

                                    <span
                                        class="users-status users-status-active"
                                    >

                                        <?= htmlspecialchars(
                                            ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $tenantUserRole
                                                )
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <?php

                                    $statusClass =
                                        $membershipStatus === 'active'
                                            ? 'users-status-active'
                                            : 'users-status-inactive';

                                    ?>


                                    <span
                                        class="users-status <?= $statusClass ?>"
                                    >

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $membershipStatus
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <!-- CREATED -->

                                <td>

                                    <?php

                                    $createdAt =
                                        $businessUser['created_at']
                                        ??
                                        null;


                                    if ($createdAt) {

                                        echo htmlspecialchars(
                                            date(
                                                'M d, Y',
                                                strtotime($createdAt)
                                            )
                                        );

                                    } else {

                                        echo '—';

                                    }

                                    ?>

                                </td>


                                <!-- ACTION -->

                                <td>


                                    <?php if (!$isOwner): ?>


                                        <div
                                            class="user-action"
                                            data-user-action
                                        >


                                            <button
                                                type="button"
                                                class="user-action-button"
                                                data-action-toggle
                                            >

                                                Action

                                                <span
                                                    class="user-action-arrow"
                                                >
                                                    ▼
                                                </span>

                                            </button>


                                            <div
                                                class="user-action-menu"
                                            >


                                                <!-- CHANGE PASSWORD -->

                                                <button
                                                    type="button"
                                                    data-password-user
                                                    data-user-id="<?= $businessUserId ?>"
                                                    data-user-name="<?= htmlspecialchars(
                                                        $fullName !== ''
                                                            ? $fullName
                                                            : $username,
                                                        ENT_QUOTES
                                                    ) ?>"
                                                >

                                                    🔑

                                                    Change Password

                                                </button>


                                                <div
                                                    class="user-action-divider"
                                                ></div>


                                                <?php if (
                                                    $membershipStatus
                                                    ===
                                                    'active'
                                                ): ?>


                                                    <!-- DISABLE -->

                                                    <form
                                                        method="POST"
                                                        action="index.php?url=business-users/disable"
                                                        onsubmit="return confirm('Are you sure you want to disable this user?');"
                                                    >

                                                        <input
                                                            type="hidden"
                                                            name="business_user_id"
                                                            value="<?= $businessUserId ?>"
                                                        >


                                                        <button
                                                            type="submit"
                                                            class="danger-action"
                                                        >

                                                            ⛔

                                                            Disable User

                                                        </button>

                                                    </form>


                                                <?php else: ?>


                                                    <!-- ENABLE -->

                                                    <form
                                                        method="POST"
                                                        action="index.php?url=business-users/enable"
                                                        onsubmit="return confirm('Enable this user again?');"
                                                    >

                                                        <input
                                                            type="hidden"
                                                            name="business_user_id"
                                                            value="<?= $businessUserId ?>"
                                                        >


                                                        <button
                                                            type="submit"
                                                            class="success-action"
                                                        >

                                                            ✓

                                                            Enable User

                                                        </button>

                                                    </form>


                                                <?php endif; ?>


                                            </div>

                                        </div>


                                    <?php else: ?>


                                        <span
                                            style="
                                                color:#9ca3af;
                                                font-size:12px;
                                            "
                                        >
                                            Owner
                                        </span>


                                    <?php endif; ?>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>

                    </table>

                </div>


            <?php else: ?>


                <div class="users-empty">

                    <div class="users-empty-icon">
                        U
                    </div>

                    <h3>
                        No Users Found
                    </h3>

                    <p>
                        You haven't added any users
                        to this business yet.
                    </p>

                </div>


            <?php endif; ?>


        </div>


    </div>

</div>



<!-- =========================================================
     CHANGE PASSWORD MODAL
========================================================== -->

<div
    class="password-modal-overlay"
    id="passwordModal"
>


    <div
        class="password-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="passwordModalTitle"
    >


        <div class="password-modal-header">


            <div>

                <h2
                    class="password-modal-title"
                    id="passwordModalTitle"
                >
                    Change Password
                </h2>


                <div
                    class="password-modal-user"
                    id="passwordModalUser"
                >
                    User
                </div>

            </div>


            <button
                type="button"
                class="password-modal-close"
                id="passwordModalClose"
            >
                ×
            </button>


        </div>



        <form
            method="POST"
            action="index.php?url=business-users/update-password"
        >


            <div class="password-modal-body">


                <div class="password-modal-info">

                    You are resetting this user's password.
                    The user's current password is not required.

                </div>


                <input
                    type="hidden"
                    name="business_user_id"
                    id="passwordBusinessUserId"
                    value=""
                >


                <div class="password-field">

                    <label for="newPassword">
                        New Password
                    </label>


                    <input
                        type="password"
                        id="newPassword"
                        name="password"
                        minlength="8"
                        required
                        autocomplete="new-password"
                        placeholder="Enter new password"
                    >

                </div>


                <div class="password-field">

                    <label for="confirmPassword">
                        Confirm New Password
                    </label>


                    <input
                        type="password"
                        id="confirmPassword"
                        name="password_confirmation"
                        minlength="8"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                    >

                </div>


            </div>



            <div class="password-modal-footer">


                <button
                    type="button"
                    class="password-cancel-button"
                    id="passwordModalCancel"
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    class="password-submit-button"
                >
                    Change Password
                </button>


            </div>


        </form>


    </div>

</div>



<script>

/*
|--------------------------------------------------------------------------
| ACTION DROPDOWNS
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'click',
    function(event)
    {
        const toggle =
            event.target.closest(
                '[data-action-toggle]'
            );


        if (toggle) {

            const current =
                toggle.closest(
                    '[data-user-action]'
                );


            document
                .querySelectorAll(
                    '[data-user-action].active'
                )
                .forEach(
                    function(action)
                    {
                        if (
                            action !== current
                        ) {
                            action.classList.remove(
                                'active'
                            );
                        }
                    }
                );


            current.classList.toggle(
                'active'
            );


            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CLICK OUTSIDE DROPDOWN
        |--------------------------------------------------------------------------
        */

        if (
            !event.target.closest(
                '[data-user-action]'
            )
        ) {

            document
                .querySelectorAll(
                    '[data-user-action].active'
                )
                .forEach(
                    function(action)
                    {
                        action.classList.remove(
                            'active'
                        );
                    }
                );

        }

    }
);


/*
|--------------------------------------------------------------------------
| CHANGE PASSWORD MODAL
|--------------------------------------------------------------------------
*/

const passwordModal =
    document.getElementById(
        'passwordModal'
    );


const passwordModalClose =
    document.getElementById(
        'passwordModalClose'
    );


const passwordModalCancel =
    document.getElementById(
        'passwordModalCancel'
    );


const passwordBusinessUserId =
    document.getElementById(
        'passwordBusinessUserId'
    );


const passwordModalUser =
    document.getElementById(
        'passwordModalUser'
    );


const newPassword =
    document.getElementById(
        'newPassword'
    );


const confirmPassword =
    document.getElementById(
        'confirmPassword'
    );


/*
|--------------------------------------------------------------------------
| OPEN MODAL
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'click',
    function(event)
    {

        const button =
            event.target.closest(
                '[data-password-user]'
            );


        if (!button) {
            return;
        }


        const userId =
            button.dataset.userId;


        const userName =
            button.dataset.userName;


        passwordBusinessUserId.value =
            userId;


        passwordModalUser.textContent =
            'Reset password for ' + userName;


        newPassword.value = '';

        confirmPassword.value = '';


        passwordModal.classList.add(
            'active'
        );


        /*
        |--------------------------------------------------------------------------
        | CLOSE DROPDOWN
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '[data-user-action].active'
            )
            .forEach(
                function(action)
                {
                    action.classList.remove(
                        'active'
                    );
                }
            );


        setTimeout(
            function()
            {
                newPassword.focus();
            },
            100
        );

    }
);


/*
|--------------------------------------------------------------------------
| CLOSE MODAL FUNCTION
|--------------------------------------------------------------------------
*/

function closePasswordModal()
{
    passwordModal.classList.remove(
        'active'
    );

    passwordBusinessUserId.value = '';

    passwordModalUser.textContent =
        'User';

    newPassword.value = '';

    confirmPassword.value = '';
}


/*
|--------------------------------------------------------------------------
| CLOSE BUTTON
|--------------------------------------------------------------------------
*/

passwordModalClose.addEventListener(
    'click',
    closePasswordModal
);


/*
|--------------------------------------------------------------------------
| CANCEL BUTTON
|--------------------------------------------------------------------------
*/

passwordModalCancel.addEventListener(
    'click',
    closePasswordModal
);


/*
|--------------------------------------------------------------------------
| CLICK OUTSIDE MODAL
|--------------------------------------------------------------------------
*/

passwordModal.addEventListener(
    'click',
    function(event)
    {

        if (
            event.target === passwordModal
        ) {

            closePasswordModal();

        }

    }
);


/*
|--------------------------------------------------------------------------
| ESC KEY
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'keydown',
    function(event)
    {

        if (
            event.key === 'Escape'
        ) {

            /*
            | Close modal
            */

            if (
                passwordModal.classList.contains(
                    'active'
                )
            ) {

                closePasswordModal();

                return;

            }


            /*
            | Close dropdowns
            */

            document
                .querySelectorAll(
                    '[data-user-action].active'
                )
                .forEach(
                    function(action)
                    {
                        action.classList.remove(
                            'active'
                        );
                    }
                );

        }

    }
);

</script>


</body>

</html>