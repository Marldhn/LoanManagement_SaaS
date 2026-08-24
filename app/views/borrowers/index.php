<?php

$user = $user ?? Auth::user();

$business = $business ?? Auth::business();

$tenantRole = $tenantRole ?? Auth::tenantRole();

$currentUrl = $currentUrl ?? 'borrowers';

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
        Borrowers | Loan Management
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | BORROWER ACTION MENU
        |--------------------------------------------------------------------------
        */

        .borrower-action-menu {
            position: relative;
            display: inline-block;
        }


        .borrower-action-button {
            width: 38px;
            height: 38px;

            border: 1px solid #ddd;

            background: #fff;

            border-radius: 8px;

            cursor: pointer;

            font-size: 22px;

            line-height: 1;

            display: flex;

            align-items: center;

            justify-content: center;

            transition: all 0.2s ease;
        }


        .borrower-action-button:hover {
            background: #f5f5f5;
        }


        .borrower-action-dropdown {
            position: absolute;

            right: 0;

            top: calc(100% + 6px);

            min-width: 180px;

            background: #fff;

            border: 1px solid #e5e5e5;

            border-radius: 10px;

            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.12);

            padding: 6px;

            z-index: 9999;

            display: none;
        }


        .borrower-action-dropdown.active {
            display: block;
        }


        .borrower-action-item {
            width: 100%;

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 10px 12px;

            border: none;

            background: transparent;

            color: #333;

            text-decoration: none;

            font-size: 14px;

            border-radius: 7px;

            cursor: pointer;

            text-align: left;

            box-sizing: border-box;
        }


        .borrower-action-item:hover {
            background: #f5f5f5;
        }


        .borrower-action-item.danger {
            color: #dc3545;
        }


        .borrower-action-item.danger:hover {
            background: #fff1f2;
        }


        .borrower-action-icon {
            width: 20px;

            text-align: center;

            flex-shrink: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 700px) {

            .borrower-action-dropdown {
                right: 0;
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


    <!--
    |--------------------------------------------------------------------------
    | NAVBAR
    |--------------------------------------------------------------------------
    -->


    <nav class="navbar">

        <div class="page-title">

            Borrowers

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


        <!--
        |--------------------------------------------------------------------------
        | PAGE HEADER
        |--------------------------------------------------------------------------
        -->


        <div class="page-header">

            <div>

                <h1>
                    Borrowers
                </h1>

                <p>

                    Manage borrowers for

                    <?= htmlspecialchars(
                        $business['name']
                        ?? 'your business'
                    ) ?>.

                </p>

            </div>


            <div>

                <a
                    href="index.php?url=borrowers/create"
                    class="btn btn-primary"
                >

                    + Add Borrower

                </a>

            </div>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | BORROWER TABLE
        |--------------------------------------------------------------------------
        -->


        <?php if (empty($borrowers)): ?>


            <div class="form-card">

                <div class="empty-state">

                    <h3>
                        No Borrowers Found
                    </h3>

                    <p>
                        You haven't added any borrowers yet.
                    </p>


                    <br>


                    <a
                        href="index.php?url=borrowers/create"
                        class="btn btn-primary"
                    >

                        Add Your First Borrower

                    </a>

                </div>

            </div>


        <?php else: ?>


            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Code
                            </th>

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
                        | BORROWER VARIABLES
                        |--------------------------------------------------------------------------
                        */

                        $borrowerId =
                            (int) (
                                $borrower['id']
                                ?? 0
                            );


                        $borrowerCode =
                            $borrower['borrower_code']
                            ?? '-';


                        $firstName =
                            $borrower['first_name']
                            ?? '';


                        $middleName =
                            $borrower['middle_name']
                            ?? '';


                        $lastName =
                            $borrower['last_name']
                            ?? '';


                        $borrowerName =
                            trim(
                                $firstName
                                . ' '
                                . $middleName
                                . ' '
                                . $lastName
                            );


                        $phone =
                            $borrower['phone']
                            ?? '-';


                        $email =
                            $borrower['email']
                            ?? '-';


                        $monthlyIncome =
                            (float) (
                                $borrower['monthly_income']
                                ?? 0
                            );


                        $status =
                            $borrower['status']
                            ?? 'active';


                        $statusClass =
                            'status-' . $status;

                        ?>


                        <tr>


                            <!--
                            |--------------------------------------------------------------------------
                            | CODE
                            |--------------------------------------------------------------------------
                            -->


                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $borrowerCode
                                    ) ?>

                                </strong>

                            </td>


                            <!--
                            |--------------------------------------------------------------------------
                            | BORROWER
                            |--------------------------------------------------------------------------
                            -->


                            <td>

                                <?= htmlspecialchars(
                                    $borrowerName
                                ) ?>

                            </td>


                            <!--
                            |--------------------------------------------------------------------------
                            | PHONE
                            |--------------------------------------------------------------------------
                            -->


                            <td>

                                <?= htmlspecialchars(
                                    $phone
                                ) ?>

                            </td>


                            <!--
                            |--------------------------------------------------------------------------
                            | EMAIL
                            |--------------------------------------------------------------------------
                            -->


                            <td>

                                <?= htmlspecialchars(
                                    $email
                                ) ?>

                            </td>


                            <!--
                            |--------------------------------------------------------------------------
                            | MONTHLY INCOME
                            |--------------------------------------------------------------------------
                            -->


                            <td>

                                ₱<?= number_format(
                                    $monthlyIncome,
                                    2
                                ) ?>

                            </td>


                            <!--
                            |--------------------------------------------------------------------------
                            | STATUS
                            |--------------------------------------------------------------------------
                            -->


                            <td>

                                <span
                                    class="status <?= htmlspecialchars(
                                        $statusClass
                                    ) ?>"
                                >

                                    <?= htmlspecialchars(
                                        ucfirst($status)
                                    ) ?>

                                </span>

                            </td>


                            <!--
                            |--------------------------------------------------------------------------
                            | ACTIONS
                            |--------------------------------------------------------------------------
                            -->


                            <td>


                                <div
                                    class="borrower-action-menu"
                                >


                                    <!-- ACTION BUTTON -->


                                    <button
                                        type="button"
                                        class="borrower-action-button"
                                        onclick="toggleBorrowerActions(<?= $borrowerId ?>)"
                                        aria-label="Borrower actions"
                                        aria-expanded="false"
                                        data-borrower-id="<?= $borrowerId ?>"
                                    >

                                        ⋮

                                    </button>


                                    <!-- DROPDOWN -->


                                    <div
                                        class="borrower-action-dropdown"
                                        id="borrower-actions-<?= $borrowerId ?>"
                                    >


                                        <!--
                                        ----------------------------------------------
                                        VIEW DETAILS
                                        ----------------------------------------------
                                        -->


                                        <a
                                            href="index.php?url=borrowers/details&id=<?= $borrowerId ?>"
                                            class="borrower-action-item"
                                            onclick="closeBorrowerActions();"
                                        >

                                            <span class="borrower-action-icon">
                                                👁
                                            </span>

                                            <span>
                                                View Details
                                            </span>

                                        </a>


                                        <!--
                                        ----------------------------------------------
                                        EDIT
                                        ----------------------------------------------
                                        -->


                                        <a
                                            href="index.php?url=borrowers/edit&id=<?= $borrowerId ?>"
                                            class="borrower-action-item"
                                            onclick="closeBorrowerActions();"
                                        >

                                            <span class="borrower-action-icon">
                                                ✏️
                                            </span>

                                            <span>
                                                Edit
                                            </span>

                                        </a>


                                        <!--
                                        ----------------------------------------------
                                        DELETE
                                        ----------------------------------------------
                                        -->


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

                                            <span class="borrower-action-icon">
                                                🗑
                                            </span>

                                            <span>
                                                Delete
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


        <?php endif; ?>


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
| CLOSE BORROWER ACTIONS
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
| ESCAPE KEY
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