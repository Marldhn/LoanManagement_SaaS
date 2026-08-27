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

$penalties =
    $penalties
    ?? [];

$loans =
    $loans
    ?? $loanAccounts
    ?? [];

$totalPenalties =
    (float) (
        $totalPenalties
        ?? 0
    );

$thisMonthPenalties =
    (float) (
        $thisMonthPenalties
        ?? 0
    );

$penaltyCount =
    (int) (
        $penaltyCount
        ?? count($penalties)
    );


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
| HELPERS
|--------------------------------------------------------------------------
*/

if (!function_exists('penaltyMoney')) {

    function penaltyMoney(
        float $amount
    ): string {

        return '₱' .
            number_format(
                $amount,
                2
            );

    }

}


if (!function_exists('penaltyTypeLabel')) {

    function penaltyTypeLabel(
        ?string $type
    ): string {

        return match ($type) {

            'fixed' =>
                'Fixed',

            'percentage' =>
                'Percentage',

            default =>
                ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $type ?? ''
                    )
                )

        };

    }

}


if (!function_exists('penaltyBaseLabel')) {

    function penaltyBaseLabel(
        ?string $base
    ): string {

        return match ($base) {

            'principal' =>
                'Principal',

            'total_due' =>
                'Total Due',

            'overdue_amount' =>
                'Overdue Amount',

            default =>
                ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $base ?? ''
                    )
                )

        };

    }

}


if (!function_exists('penaltyStatusClass')) {

    function penaltyStatusClass(
        ?string $status
    ): string {

        return match (
            strtolower(
                trim(
                    $status ?? ''
                )
            )
        ) {

            'overdue' =>
                'penalty-status-overdue',

            'partial' =>
                'penalty-status-partial',

            'paid' =>
                'penalty-status-paid',

            default =>
                'penalty-status-pending'

        };

    }

}


if (!function_exists('penaltyEscape')) {

    function penaltyEscape(
        $value
    ): string {

        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );

    }

}


if (!function_exists('penaltyRemainingDays')) {

    function penaltyRemainingDays(
        ?string $dueDate
    ): array {

        if (!$dueDate) {

            return [
                'days' => null,
                'label' => 'No due date',
                'class' => 'penalty-remaining-none'
            ];

        }

        try {

            $today =
                new DateTime(
                    date('Y-m-d')
                );

            $due =
                new DateTime(
                    date(
                        'Y-m-d',
                        strtotime($dueDate)
                    )
                );

            $difference =
                (int) $today
                    ->diff($due)
                    ->format('%r%a');


            if ($difference < 0) {

                $daysOverdue =
                    abs($difference);

                return [

                    'days' =>
                        $difference,

                    'label' =>
                        $daysOverdue === 1
                            ? '1 day overdue'
                            : $daysOverdue .
                                ' days overdue',

                    'class' =>
                        'penalty-remaining-overdue'

                ];

            }


            if ($difference === 0) {

                return [

                    'days' => 0,

                    'label' =>
                        'Due today',

                    'class' =>
                        'penalty-remaining-today'

                ];

            }


            if ($difference === 1) {

                return [

                    'days' => 1,

                    'label' =>
                        '1 day remaining',

                    'class' =>
                        'penalty-remaining-warning'

                ];

            }


            if ($difference <= 3) {

                return [

                    'days' =>
                        $difference,

                    'label' =>
                        $difference .
                        ' days remaining',

                    'class' =>
                        'penalty-remaining-warning'

                ];

            }


            return [

                'days' =>
                    $difference,

                'label' =>
                    $difference .
                    ' days remaining',

                'class' =>
                    'penalty-remaining-normal'

            ];

        } catch (
            Throwable $e
        ) {

            return [

                'days' => null,

                'label' =>
                    'Unknown',

                'class' =>
                    'penalty-remaining-none'

            ];

        }

    }

}

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
    Penalties |
    <?= htmlspecialchars(
        $businessName
    ) ?>
</title>


<link
    rel="stylesheet"
    href="assets/css/style.css"
>


<style>

/*
|--------------------------------------------------------------------------
| PENALTY PAGE
|--------------------------------------------------------------------------
*/

.penalties-page {

    width: 100%;

}


/*
|--------------------------------------------------------------------------
| PAGE HEADER
|--------------------------------------------------------------------------
*/

.penalties-header {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 25px;

}


.penalties-heading h1 {

    margin: 0;

    color: #111827;

    font-size: 28px;

    line-height: 1.2;

    font-weight: 750;

    letter-spacing: -.025em;

}


.penalties-heading p {

    margin: 7px 0 0;

    color: #6b7280;

    font-size: 14px;

    line-height: 1.5;

}


.penalties-header-actions {

    display: flex;

    align-items: center;

    gap: 10px;

    flex-shrink: 0;

}


/*
|--------------------------------------------------------------------------
| COUNT BADGE
|--------------------------------------------------------------------------
*/

.penalty-count-badge {

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


.penalty-count-badge strong {

    color: #111827;

    font-weight: 750;

}


/*
|--------------------------------------------------------------------------
| SUMMARY
|--------------------------------------------------------------------------
*/

.penalties-summary {

    display: grid;

    grid-template-columns:
        repeat(
            3,
            minmax(0, 1fr)
        );

    gap: 15px;

    margin-bottom: 20px;

}


.penalty-summary-card {

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


.penalty-summary-label {

    color: #6b7280;

    font-size: 11px;

    font-weight: 650;

}


.penalty-summary-value {

    margin-top: 5px;

    color: #111827;

    font-size: 22px;

    line-height: 1.2;

    font-weight: 750;

}


.penalty-summary-description {

    margin-top: 4px;

    color: #9ca3af;

    font-size: 10px;

}


/*
|--------------------------------------------------------------------------
| MAIN PANEL
|--------------------------------------------------------------------------
*/

.penalties-panel {

    background: #ffffff;

    border: 1px solid #e5e7eb;

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


.penalties-panel-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    padding: 18px 20px;

    border-bottom:
        1px solid #f0f1f3;

}


.penalties-panel-title {

    margin: 0;

    color: #111827;

    font-size: 16px;

    font-weight: 700;

}


.penalties-panel-subtitle {

    margin: 4px 0 0;

    color: #9ca3af;

    font-size: 11px;

}


/*
|--------------------------------------------------------------------------
| TABLE
|--------------------------------------------------------------------------
*/

.penalties-table-wrapper {

    width: 100%;

    overflow-x: auto;

    -webkit-overflow-scrolling: touch;

}


.penalties-table {

    width: 100%;

    min-width: 1250px;

    border-collapse: collapse;

}


.penalties-table th {

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


.penalties-table td {

    padding:
        14px 20px;

    color: #4b5563;

    font-size: 12px;

    border-top:
        1px solid #f3f4f6;

    vertical-align: middle;

    white-space: nowrap;

}


.penalties-table tbody tr {

    transition:
        background .15s ease;

}


.penalties-table tbody tr:hover {

    background: #fafafa;

}


/*
|--------------------------------------------------------------------------
| LOAN
|--------------------------------------------------------------------------
*/

.penalty-loan {

    color: #111827;

    font-size: 12px;

    font-weight: 700;

}


.penalty-loan a {

    color: #111827;

    text-decoration: none;

}


.penalty-loan a:hover {

    text-decoration: underline;

}


/*
|--------------------------------------------------------------------------
| BORROWER
|--------------------------------------------------------------------------
*/

.penalty-borrower {

    color: #111827;

    font-size: 12px;

    font-weight: 650;

}


/*
|--------------------------------------------------------------------------
| MUTED
|--------------------------------------------------------------------------
*/

.penalty-muted {

    color: #9ca3af;

}


/*
|--------------------------------------------------------------------------
| MONEY
|--------------------------------------------------------------------------
*/

.penalty-money {

    color: #111827;

    font-size: 12px;

    font-weight: 700;

}


/*
|--------------------------------------------------------------------------
| TYPE
|--------------------------------------------------------------------------
*/

.penalty-type {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding:
        5px 9px;

    border-radius: 999px;

    background: #f3f4f6;

    color: #374151;

    font-size: 10px;

    font-weight: 700;

    white-space: nowrap;

}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

.penalty-status {

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


.penalty-status::before {

    content: '';

    width: 5px;

    height: 5px;

    border-radius: 50%;

    background: currentColor;

}


.penalty-status-overdue {

    background: #fef2f2;

    color: #b91c1c;

}


.penalty-status-partial {

    background: #fffbeb;

    color: #b45309;

}


.penalty-status-paid {

    background: #ecfdf5;

    color: #047857;

}


.penalty-status-pending {

    background: #f3f4f6;

    color: #6b7280;

}


/*
|--------------------------------------------------------------------------
| REMAINING
|--------------------------------------------------------------------------
*/

.penalty-remaining {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding:
        5px 9px;

    border-radius: 999px;

    font-size: 10px;

    font-weight: 750;

    white-space: nowrap;

}


.penalty-remaining-normal {

    background: #ecfdf5;

    color: #047857;

}


.penalty-remaining-warning {

    background: #fffbeb;

    color: #b45309;

}


.penalty-remaining-today {

    background: #fff7ed;

    color: #c2410c;

}


.penalty-remaining-overdue {

    background: #fef2f2;

    color: #b91c1c;

}


.penalty-remaining-none {

    background: #f3f4f6;

    color: #6b7280;

}


/*
|--------------------------------------------------------------------------
| DUE DATE
|--------------------------------------------------------------------------
*/

.penalty-due-date {

    color: #4b5563;

    font-size: 12px;

}


.penalty-due-date.overdue {

    color: #b91c1c;

    font-weight: 700;

}


/*
|--------------------------------------------------------------------------
| ACTION
|--------------------------------------------------------------------------
*/

.penalty-action {

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

    text-decoration: none;

    font-size: 14px;

    transition:
        background .15s ease,
        border-color .15s ease,
        color .15s ease,
        box-shadow .15s ease;

}


.penalty-action:hover {

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


/*
|--------------------------------------------------------------------------
| EMPTY
|--------------------------------------------------------------------------
*/

.penalties-empty {

    padding:
        65px 25px;

    text-align: center;

}


.penalties-empty-icon {

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


.penalties-empty h3 {

    margin: 0;

    color: #111827;

    font-size: 16px;

    font-weight: 700;

}


.penalties-empty p {

    margin:
        6px 0 20px;

    color: #9ca3af;

    font-size: 12px;

}


/*
|--------------------------------------------------------------------------
| FOOTER
|--------------------------------------------------------------------------
*/

.penalties-panel-footer {

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


.penalties-footer-text {

    color: #9ca3af;

    font-size: 10px;

}


.penalties-footer-count {

    color: #6b7280;

    font-size: 11px;

    font-weight: 650;

}


/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

.penalty-modal-overlay {

    position: fixed;

    inset: 0;

    z-index: 9999;

    display: none;

    align-items: center;

    justify-content: center;

    padding: 20px;

    background:
        rgba(
            17,
            24,
            39,
            .55
        );

}


.penalty-modal-overlay.active {

    display: flex;

}


.penalty-modal {

    width: 100%;

    max-width: 560px;

    max-height:
        calc(
            100vh - 40px
        );

    overflow-y: auto;

    background: #ffffff;

    border-radius: 14px;

    box-shadow:
        0 20px 40px
        rgba(
            0,
            0,
            0,
            .18
        );

}


.penalty-modal-header {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 20px;

    padding:
        20px 22px;

    border-bottom:
        1px solid #e5e7eb;

}


.penalty-modal-header h2 {

    margin: 0;

    color: #111827;

    font-size: 19px;

    font-weight: 700;

}


.penalty-modal-header p {

    margin:
        5px 0 0;

    color: #6b7280;

    font-size: 13px;

}


.penalty-modal-close {

    width: 32px;

    height: 32px;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 0;

    border: none;

    border-radius: 7px;

    background: #f3f4f6;

    color: #374151;

    font-size: 22px;

    line-height: 1;

    cursor: pointer;

}


.penalty-modal-close:hover {

    background: #e5e7eb;

}


/*
|--------------------------------------------------------------------------
| MODAL BODY
|--------------------------------------------------------------------------
*/

.penalty-modal-body {

    padding: 22px;

}


.penalty-form-row {

    display: grid;

    grid-template-columns:
        repeat(
            2,
            minmax(0, 1fr)
        );

    gap: 15px;

}


.penalty-form-group {

    margin-bottom: 17px;

}


.penalty-form-group label {

    display: block;

    margin-bottom: 7px;

    color: #374151;

    font-size: 13px;

    font-weight: 600;

}


.penalty-form-group label span {

    color: #dc2626;

}


.penalty-form-group input,
.penalty-form-group select,
.penalty-form-group textarea {

    width: 100%;

    box-sizing: border-box;

    padding:
        10px 12px;

    border:
        1px solid #d1d5db;

    border-radius: 8px;

    outline: none;

    background: #ffffff;

    color: #111827;

    font-family: inherit;

    font-size: 13px;

    transition:
        border-color .2s ease,
        box-shadow .2s ease;

}


.penalty-form-group input:focus,
.penalty-form-group select:focus,
.penalty-form-group textarea:focus {

    border-color: #111827;

    box-shadow:
        0 0 0 3px
        rgba(
            17,
            24,
            39,
            .08
        );

}


.penalty-form-group textarea {

    resize: vertical;

}


.penalty-form-group small {

    display: block;

    margin-top: 5px;

    color: #9ca3af;

    font-size: 11px;

}


/*
|--------------------------------------------------------------------------
| MODAL FOOTER
|--------------------------------------------------------------------------
*/

.penalty-modal-footer {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 10px;

    padding:
        16px 22px;

    background: #fafafa;

    border-top:
        1px solid #e5e7eb;

}


.penalty-cancel-btn,
.penalty-save-btn {

    padding:
        10px 15px;

    border-radius: 8px;

    font-size: 13px;

    font-weight: 600;

    cursor: pointer;

}


.penalty-cancel-btn {

    border:
        1px solid #d1d5db;

    background: #ffffff;

    color: #374151;

}


.penalty-cancel-btn:hover {

    background: #f3f4f6;

}


.penalty-save-btn {

    border:
        1px solid #111827;

    background: #111827;

    color: #ffffff;

}


.penalty-save-btn:hover {

    background: #000000;

}


/*
|--------------------------------------------------------------------------
| RESPONSIVE
|--------------------------------------------------------------------------
*/

@media (max-width: 1000px) {

    .penalties-summary {

        grid-template-columns:
            repeat(
                2,
                minmax(0, 1fr)
            );

    }

}


@media (max-width: 700px) {

    .penalties-header {

        flex-direction: column;

        margin-bottom: 20px;

    }


    .penalties-heading h1 {

        font-size: 23px;

    }


    .penalties-heading p {

        font-size: 12px;

    }


    .penalties-header-actions {

        width: 100%;

        justify-content: space-between;

    }


    .penalty-count-badge {

        flex: 1;

        justify-content: center;

    }


    .penalties-summary {

        grid-template-columns: 1fr;

        gap: 10px;

        margin-bottom: 15px;

    }


    .penalty-summary-card {

        padding: 15px;

        border-radius: 12px;

    }


    .penalties-panel {

        border-radius: 13px;

    }


    .penalties-panel-header {

        padding:
            15px 16px;

    }


    .penalties-panel-title {

        font-size: 15px;

    }


    .penalties-table {

        min-width: 1100px;

    }


    .penalties-panel-footer {

        padding:
            12px 16px;

        border-radius:
            0 0 13px 13px;

    }


    .penalty-modal-overlay {

        padding: 10px;

    }


    .penalty-form-row {

        grid-template-columns: 1fr;

    }


    .penalty-modal-footer {

        flex-direction: column-reverse;

    }


    .penalty-cancel-btn,
    .penalty-save-btn {

        width: 100%;

    }

}


@media (max-width: 400px) {

    .penalties-header-actions {

        flex-direction: column;

        align-items: stretch;

    }


    .penalty-count-badge {

        width: 100%;

        box-sizing: border-box;

    }


    .penalties-header-actions .penalty-primary-btn {

        width: 100%;

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

        Penalties

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

<div class="container penalties-page">


    <!-- =================================================
         PAGE HEADER
    ================================================== -->

    <div class="penalties-header">

        <div class="penalties-heading">

            <h1>
                Penalties
            </h1>

            <p>

                View and manage loan penalties for

                <?= htmlspecialchars(
                    $businessName
                ) ?>.

            </p>

        </div>


        <div class="penalties-header-actions">

            <div class="penalty-count-badge">

                <strong>

                    <?= number_format(
                        $penaltyCount
                    ) ?>

                </strong>

                <span>

                    <?= $penaltyCount === 1
                        ? 'Penalty'
                        : 'Penalties'
                    ?>

                </span>

            </div>


            <button
                type="button"
                class="penalty-primary-btn"
                onclick="openPenaltyModal()"
            >

                + Add Penalty

            </button>

        </div>

    </div>


    <!-- =================================================
         SUMMARY
    ================================================== -->

    <div class="penalties-summary">


        <div class="penalty-summary-card">

            <div class="penalty-summary-label">

                Total Penalties

            </div>

            <div class="penalty-summary-value">

                <?= penaltyMoney(
                    $totalPenalties
                ) ?>

            </div>

            <div class="penalty-summary-description">

                Total penalty amount

            </div>

        </div>


        <div class="penalty-summary-card">

            <div class="penalty-summary-label">

                This Month

            </div>

            <div class="penalty-summary-value">

                <?= penaltyMoney(
                    $thisMonthPenalties
                ) ?>

            </div>

            <div class="penalty-summary-description">

                Penalties recorded this month

            </div>

        </div>


        <div class="penalty-summary-card">

            <div class="penalty-summary-label">

                Penalty Records

            </div>

            <div class="penalty-summary-value">

                <?= number_format(
                    $penaltyCount
                ) ?>

            </div>

            <div class="penalty-summary-description">

                Registered penalty records

            </div>

        </div>


    </div>


    <!-- =================================================
         PENALTY PANEL
    ================================================== -->

    <div class="penalties-panel">


        <div class="penalties-panel-header">

            <div>

                <h2 class="penalties-panel-title">

                    Penalty Records

                </h2>

                <p class="penalties-panel-subtitle">

                    View and manage loan penalty records.

                </p>

            </div>

        </div>


        <?php if (empty($penalties)): ?>


            <!-- =========================================
                 EMPTY STATE
            ========================================== -->

            <div class="penalties-empty">

                <div class="penalties-empty-icon">

                    ₱

                </div>


                <h3>

                    No Penalties Found

                </h3>


                <p>

                    There are currently no penalty records
                    for this business.

                </p>


                <button
                    type="button"
                    class="penalty-primary-btn"
                    onclick="openPenaltyModal()"
                >

                    + Add Your First Penalty

                </button>

            </div>


        <?php else: ?>


            <!-- =========================================
                 TABLE
            ========================================== -->

            <div class="penalties-table-wrapper">

                <table class="penalties-table">


                    <thead>

                        <tr>

                            <th>
                                Loan
                            </th>

                            <th>
                                Borrower
                            </th>

                            <th>
                                Installment
                            </th>

                            <th>
                                Due Date
                            </th>

                            <th>
                                Remaining
                            </th>

                            <th>
                                Type
                            </th>

                            <th>
                                Base
                            </th>

                            <th>
                                Rate
                            </th>

                            <th>
                                Base Amount
                            </th>

                            <th>
                                Penalty
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach (
                        $penalties
                        as $penalty
                    ): ?>


                        <?php

                        $penaltyId =
                            (int) (
                                $penalty['id']
                                ?? 0
                            );


                        $loanId =
                            (int) (
                                $penalty['loan_id']
                                ?? 0
                            );


                        $loanNumber =
                            $penalty['loan_number']
                            ??
                            '-';


                        $borrowerName =
                            $penalty['borrower_name']
                            ??
                            '-';


                        $installmentNumber =
                            $penalty['installment_number']
                            ??
                            null;


                        $dueDate =
                            $penalty['due_date']
                            ??
                            null;


                        $createdAt =
                            $penalty['created_at']
                            ??
                            null;


                        $rate =
                            (float) (
                                $penalty['rate']
                                ?? 0
                            );


                        $penaltyType =
                            $penalty['penalty_type']
                            ??
                            null;


                        $penaltyBase =
                            $penalty['penalty_base']
                            ??
                            null;


                        $scheduleStatus =
                            strtolower(
                                trim(
                                    $penalty['schedule_status']
                                    ??
                                    'pending'
                                )
                            );


                        $remaining =
                            penaltyRemainingDays(
                                $dueDate
                            );

                        ?>


                        <tr>


                            <!-- LOAN -->

                            <td>

                                <div class="penalty-loan">

                                    <?php if ($loanId > 0): ?>

                                        <a
                                            href="index.php?url=loans/show&id=<?= $loanId ?>"
                                        >

                                            <?= penaltyEscape(
                                                $loanNumber
                                            ) ?>

                                        </a>

                                    <?php else: ?>

                                        <?= penaltyEscape(
                                            $loanNumber
                                        ) ?>

                                    <?php endif; ?>

                                </div>

                            </td>


                            <!-- BORROWER -->

                            <td>

                                <div class="penalty-borrower">

                                    <?= penaltyEscape(
                                        $borrowerName
                                    ) ?>

                                </div>

                            </td>


                            <!-- INSTALLMENT -->

                            <td>

                                <?php if (
                                    $installmentNumber !== null
                                ): ?>

                                    #

                                    <?= penaltyEscape(
                                        $installmentNumber
                                    ) ?>

                                <?php else: ?>

                                    <span class="penalty-muted">
                                        -
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- DUE DATE -->

                            <td>

                                <?php if ($dueDate): ?>

                                    <span
                                        class="penalty-due-date
                                        <?= $remaining['days'] !== null
                                            && $remaining['days'] < 0
                                            ? 'overdue'
                                            : ''
                                        ?>"
                                    >

                                        <?= date(
                                            'M d, Y',
                                            strtotime(
                                                $dueDate
                                            )
                                        ) ?>

                                    </span>

                                <?php else: ?>

                                    <span class="penalty-muted">
                                        -
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- REMAINING -->

                            <td>

                                <span
                                    class="penalty-remaining
                                    <?= penaltyEscape(
                                        $remaining['class']
                                    ) ?>"
                                >

                                    <?= penaltyEscape(
                                        $remaining['label']
                                    ) ?>

                                </span>

                            </td>


                            <!-- TYPE -->

                            <td>

                                <span class="penalty-type">

                                    <?= penaltyEscape(
                                        penaltyTypeLabel(
                                            $penaltyType
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <!-- BASE -->

                            <td>

                                <?= penaltyEscape(
                                    penaltyBaseLabel(
                                        $penaltyBase
                                    )
                                ) ?>

                            </td>


                            <!-- RATE -->

                            <td>

                                <?php if (
                                    $penaltyType ===
                                    'percentage'
                                ): ?>

                                    <?= number_format(
                                        $rate,
                                        2
                                    ) ?>%

                                <?php else: ?>

                                    <?= penaltyMoney(
                                        $rate
                                    ) ?>

                                <?php endif; ?>

                            </td>


                            <!-- BASE AMOUNT -->

                            <td>

                                <?= penaltyMoney(
                                    (float) (
                                        $penalty[
                                            'base_amount'
                                        ]
                                        ?? 0
                                    )
                                ) ?>

                            </td>


                            <!-- PENALTY -->

                            <td>

                                <span class="penalty-money">

                                    <?= penaltyMoney(
                                        (float) (
                                            $penalty[
                                                'penalty_amount'
                                            ]
                                            ?? 0
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="penalty-status
                                    <?= penaltyEscape(
                                        penaltyStatusClass(
                                            $scheduleStatus
                                        )
                                    ) ?>"
                                >

                                    <?= penaltyEscape(
                                        ucfirst(
                                            $scheduleStatus
                                        )
                                    ) ?>

                                </span>

                            </td>


                            <!-- DATE -->

                            <td>

                                <?php if ($createdAt): ?>

                                    <?= date(
                                        'M d, Y',
                                        strtotime(
                                            $createdAt
                                        )
                                    ) ?>

                                <?php else: ?>

                                    <span class="penalty-muted">
                                        -
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- ACTION -->

                            <td>

                                <?php if (
                                    $penaltyId > 0
                                ): ?>

                                    <a
                                        href="index.php?url=penalties/view&id=<?= $penaltyId ?>"
                                        class="penalty-action"
                                        title="View penalty"
                                    >

                                        👁

                                    </a>

                                <?php endif; ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


            <!-- =========================================
                 FOOTER
            ========================================== -->

            <div class="penalties-panel-footer">

                <span class="penalties-footer-text">

                    Showing registered penalty records

                </span>


                <span class="penalties-footer-count">

                    <?= number_format(
                        $penaltyCount
                    ) ?>

                    <?= $penaltyCount === 1
                        ? 'penalty'
                        : 'penalties'
                    ?>

                </span>

            </div>


        <?php endif; ?>


    </div>


</div>


</div>


<!-- ================================================================
     CREATE PENALTY MODAL
================================================================ -->


<div
    id="penaltyModal"
    class="penalty-modal-overlay"
    onclick="closePenaltyModal(event)"
>


    <div
        class="penalty-modal"
        onclick="event.stopPropagation()"
    >


        <div class="penalty-modal-header">

            <div>

                <h2>
                    Add Penalty
                </h2>

                <p>
                    Add a penalty to a loan installment.
                </p>

            </div>


            <button
                type="button"
                class="penalty-modal-close"
                onclick="closePenaltyModal()"
            >

                ×

            </button>

        </div>


        <form
            method="POST"
            action="index.php?url=penalties/store"
            id="penaltyCreateForm"
        >


            <div class="penalty-modal-body">


                <!-- LOAN -->

                <div class="penalty-form-group">

                    <label for="penaltyLoan">

                        Loan

                        <span>
                            *
                        </span>

                    </label>


                    <select
                        id="penaltyLoan"
                        name="loan_id"
                        required
                        onchange="updatePenaltyInstallments()"
                    >

                        <option value="">
                            Select Loan
                        </option>


                        <?php foreach (
                            $loans
                            as $loan
                        ): ?>


                            <?php

                            $modalLoanId =
                                (int) (
                                    $loan['id']
                                    ?? 0
                                );


                            $modalLoanNumber =
                                $loan['loan_number']
                                ??
                                (
                                    'Loan #' .
                                    $modalLoanId
                                );


                            $modalBorrower =
                                $loan['borrower_name']
                                ??
                                '';

                            ?>


                            <?php if (
                                $modalLoanId > 0
                            ): ?>

                                <option
                                    value="<?= $modalLoanId ?>"
                                >

                                    <?= penaltyEscape(
                                        $modalLoanNumber
                                    ) ?>


                                    <?php if (
                                        $modalBorrower !== ''
                                    ): ?>

                                        -

                                        <?= penaltyEscape(
                                            $modalBorrower
                                        ) ?>

                                    <?php endif; ?>

                                </option>

                            <?php endif; ?>


                        <?php endforeach; ?>


                    </select>

                </div>


                <!-- INSTALLMENT -->

                <div class="penalty-form-group">

                    <label for="penaltySchedule">

                        Installment

                        <span>
                            *
                        </span>

                    </label>


                    <select
                        id="penaltySchedule"
                        name="schedule_id"
                        required
                    >

                        <option value="">
                            Select installment
                        </option>


                        <?php foreach (
                            $penalties
                            as $penalty
                        ): ?>


                            <?php

                            $scheduleId =
                                (int) (
                                    $penalty['schedule_id']
                                    ?? 0
                                );


                            $scheduleLoanId =
                                (int) (
                                    $penalty['loan_id']
                                    ?? 0
                                );


                            $scheduleNumber =
                                $penalty[
                                    'installment_number'
                                ]
                                ??
                                '';


                            $scheduleDueDate =
                                $penalty[
                                    'due_date'
                                ]
                                ??
                                '';

                            ?>


                            <?php if (
                                $scheduleId > 0
                            ): ?>

                                <option
                                    value="<?= $scheduleId ?>"
                                    data-loan-id="<?= $scheduleLoanId ?>"
                                >

                                    Installment
                                    #<?= penaltyEscape(
                                        $scheduleNumber
                                    ) ?>


                                    <?php if (
                                        $scheduleDueDate
                                    ): ?>

                                        -

                                        Due

                                        <?= date(
                                            'M d, Y',
                                            strtotime(
                                                $scheduleDueDate
                                            )
                                        ) ?>

                                    <?php endif; ?>

                                </option>

                            <?php endif; ?>


                        <?php endforeach; ?>


                    </select>


                    <small>

                        Select the installment where the penalty applies.

                    </small>

                </div>


                <!-- TYPE AND RATE -->

                <div class="penalty-form-row">


                    <div class="penalty-form-group">

                        <label for="penaltyType">

                            Penalty Type

                            <span>
                                *
                            </span>

                        </label>


                        <select
                            id="penaltyType"
                            name="penalty_type"
                            required
                            onchange="updatePenaltyRateLabel()"
                        >

                            <option value="fixed">

                                Fixed Amount

                            </option>


                            <option value="percentage">

                                Percentage

                            </option>

                        </select>

                    </div>


                    <div class="penalty-form-group">

                        <label
                            for="penaltyRate"
                            id="penaltyRateLabel"
                        >

                            Penalty Amount

                        </label>


                        <input
                            type="number"
                            id="penaltyRate"
                            name="rate"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            required
                        >

                    </div>


                </div>


                <!-- BASE -->

                <div class="penalty-form-group">

                    <label for="penaltyBase">

                        Penalty Base

                        <span>
                            *
                        </span>

                    </label>


                    <select
                        id="penaltyBase"
                        name="penalty_base"
                        required
                    >

                        <option value="principal">

                            Principal

                        </option>


                        <option value="total_due">

                            Total Due

                        </option>


                        <option value="overdue_amount">

                            Overdue Amount

                        </option>

                    </select>

                </div>


                <!-- NOTES -->

                <div class="penalty-form-group">

                    <label for="penaltyNotes">

                        Notes

                    </label>


                    <textarea
                        id="penaltyNotes"
                        name="notes"
                        rows="3"
                        placeholder="Optional notes..."
                    ></textarea>

                </div>


            </div>


            <div class="penalty-modal-footer">


                <button
                    type="button"
                    class="penalty-cancel-btn"
                    onclick="closePenaltyModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="penalty-save-btn"
                >

                    Add Penalty

                </button>


            </div>


        </form>


    </div>


</div>


<script>

/*
|--------------------------------------------------------------------------
| OPEN PENALTY MODAL
|--------------------------------------------------------------------------
*/

function openPenaltyModal()
{

    const modal =
        document.getElementById(
            'penaltyModal'
        );


    if (!modal) {

        return;

    }


    modal.classList.add(
        'active'
    );


    document.body.style.overflow =
        'hidden';


    updatePenaltyRateLabel();

}



/*
|--------------------------------------------------------------------------
| CLOSE PENALTY MODAL
|--------------------------------------------------------------------------
*/

function closePenaltyModal(
    event
)
{

    if (
        event &&
        event.target &&
        event.target.id !==
            'penaltyModal'
    ) {

        return;

    }


    const modal =
        document.getElementById(
            'penaltyModal'
        );


    if (!modal) {

        return;

    }


    modal.classList.remove(
        'active'
    );


    document.body.style.overflow =
        '';

}



/*
|--------------------------------------------------------------------------
| ESCAPE
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'keydown',
    function(event)
    {

        if (
            event.key ===
            'Escape'
        ) {

            closePenaltyModal();

        }

    }
);



/*
|--------------------------------------------------------------------------
| RATE LABEL
|--------------------------------------------------------------------------
*/

function updatePenaltyRateLabel()
{

    const type =
        document.getElementById(
            'penaltyType'
        );


    const label =
        document.getElementById(
            'penaltyRateLabel'
        );


    const input =
        document.getElementById(
            'penaltyRate'
        );


    if (
        !type ||
        !label ||
        !input
    ) {

        return;

    }


    if (
        type.value ===
        'percentage'
    ) {

        label.textContent =
            'Penalty Rate (%)';


        input.placeholder =
            'e.g. 5';

    } else {

        label.textContent =
            'Penalty Amount';


        input.placeholder =
            '0.00';

    }

}



/*
|--------------------------------------------------------------------------
| FILTER INSTALLMENTS
|--------------------------------------------------------------------------
*/

function updatePenaltyInstallments()
{

    const loanSelect =
        document.getElementById(
            'penaltyLoan'
        );


    const scheduleSelect =
        document.getElementById(
            'penaltySchedule'
        );


    if (
        !loanSelect ||
        !scheduleSelect
    ) {

        return;

    }


    const selectedLoan =
        loanSelect.value;


    const options =
        scheduleSelect.querySelectorAll(
            'option[data-loan-id]'
        );


    scheduleSelect.value =
        '';


    options.forEach(
        function(option)
        {

            const optionLoanId =
                option.getAttribute(
                    'data-loan-id'
                );


            if (
                !selectedLoan ||
                optionLoanId ===
                    selectedLoan
            ) {

                option.hidden =
                    false;

            } else {

                option.hidden =
                    true;

            }

        }
    );

}

</script>


</body>

</html>