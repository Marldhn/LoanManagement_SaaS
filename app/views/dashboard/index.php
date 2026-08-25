<?php

/*
|--------------------------------------------------------------------------
| SAFE DEFAULTS
|--------------------------------------------------------------------------
*/

$user =
    $user
    ?? Auth::user();

$business =
    $business
    ?? Auth::business()
    ?? [];

$businessId =
    (int) (
        $businessId
        ?? Auth::businessId()
        ?? 0
    );

$tenantRole =
    $tenantRole
    ?? Auth::tenantRole()
    ?? 'User';

$totalBorrowers =
    (int) (
        $totalBorrowers
        ?? 0
    );

$activeLoans =
    (int) (
        $activeLoans
        ?? 0
    );

$outstandingBalance =
    (float) (
        $outstandingBalance
        ?? 0
    );

$totalAccounts =
    (int) (
        $totalAccounts
        ?? 0
    );

$totalFunds =
    (float) (
        $totalFunds
        ?? 0
    );

$totalAssets =
    (float) (
        $totalAssets
        ?? 0
    );

$totalLiabilities =
    (float) (
        $totalLiabilities
        ?? 0
    );

$netBalance =
    (float) (
        $netBalance
        ?? (
            $totalAssets
            -
            $totalLiabilities
        )
    );

$accounts =
    $accounts
    ?? [];

$recentLoans =
    $recentLoans
    ?? [];


/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

if (!function_exists('dashboardMoney')) {
    function dashboardMoney(
        float $amount
    ): string {
        return '₱' .
            number_format(
                $amount,
                2
            );
    }
}

if (!function_exists('dashboardStatusClass')) {
    function dashboardStatusClass(
        string $status
    ): string {
        return match (
            strtolower(trim($status))
        ) {
            'active' =>
                'dashboard-status-active',

            'approved' =>
                'dashboard-status-approved',

            'pending' =>
                'dashboard-status-pending',

            'completed' =>
                'dashboard-status-completed',

            'overdue' =>
                'dashboard-status-overdue',

            'cancelled',
            'rejected' =>
                'dashboard-status-danger',

            default =>
                'dashboard-status-default'
        };
    }
}


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
    'Administrator';

$businessName =
    $business['name']
    ??
    'Loan Management';

$role =
    $tenantRole
    ??
    $user['role']
    ??
    'User';


/*
|--------------------------------------------------------------------------
| DASHBOARD CALCULATIONS
|--------------------------------------------------------------------------
*/

$netBalancePositive =
    $netBalance >= 0;

$fundsPositive =
    $totalFunds >= 0;

$assetsPositive =
    $totalAssets >= 0;

$liabilitiesPositive =
    $totalLiabilities >= 0;

$currentDate =
    date('M d, Y');

$currentTime =
    date('h:i A');


/*
|--------------------------------------------------------------------------
| INITIALS
|--------------------------------------------------------------------------
*/

$nameParts =
    preg_split(
        '/\s+/',
        trim($userName)
    );

$userInitials =
    '';

if (!empty($nameParts)) {

    $userInitials .=
        strtoupper(
            substr(
                $nameParts[0],
                0,
                1
            )
        );

    if (
        count($nameParts) > 1
    ) {
        $userInitials .=
            strtoupper(
                substr(
                    $nameParts[
                        count($nameParts) - 1
                    ],
                    0,
                    1
                )
            );
    }
}

$userInitials =
    $userInitials ?: 'U';

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
        Dashboard |
        <?= htmlspecialchars($businessName) ?>
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD ROOT
        |--------------------------------------------------------------------------
        */

        .lm-dashboard {
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
        }


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD HEADER
        |--------------------------------------------------------------------------
        */

        .lm-dashboard-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 28px;
        }

        .lm-dashboard-heading {
            min-width: 0;
        }

        .lm-dashboard-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 8px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .lm-dashboard-eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 4px #ecfdf5;
        }

        .lm-dashboard-heading h1 {
            margin: 0;
            color: #111827;
            font-size: 30px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -.035em;
        }

        .lm-dashboard-heading p {
            margin: 8px 0 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
        }

        .lm-dashboard-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .lm-dashboard-date {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            min-height: 40px;
            padding: 0 13px;
            border: 1px solid #e5e7eb;
            border-radius: 11px;
            background: #ffffff;
            color: #6b7280;
            font-size: 12px;
            font-weight: 650;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .025);
        }

        .lm-date-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 25px;
            height: 25px;
            border-radius: 7px;
            background: #f3f4f6;
            color: #374151;
            font-size: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | KPI GRID
        |--------------------------------------------------------------------------
        */

        .lm-kpi-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
                );
            gap: 16px;
            margin-bottom: 22px;
        }

        .lm-kpi-card {
            position: relative;
            overflow: hidden;
            min-width: 0;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow:
                0 2px 8px
                rgba(
                    15,
                    23,
                    42,
                    .035
                );
            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease;
        }

        .lm-kpi-card:hover {
            transform: translateY(-2px);
            border-color: #dbe0e7;
            box-shadow:
                0 12px 28px
                rgba(
                    15,
                    23,
                    42,
                    .075
                );
        }

        .lm-kpi-card::after {
            content: "";
            position: absolute;
            right: -30px;
            bottom: -35px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #f8fafc;
            pointer-events: none;
        }

        .lm-kpi-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 20px;
        }

        .lm-kpi-label {
            color: #6b7280;
            font-size: 12px;
            line-height: 1.4;
            font-weight: 650;
        }

        .lm-kpi-icon {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 12px;
            background: #f3f4f6;
            color: #374151;
            font-size: 15px;
            font-weight: 800;
        }

        .lm-kpi-card.borrowers .lm-kpi-icon {
            background: #eff6ff;
            color: #2563eb;
        }

        .lm-kpi-card.loans .lm-kpi-icon {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .lm-kpi-card.outstanding .lm-kpi-icon {
            background: #fff7ed;
            color: #ea580c;
        }

        .lm-kpi-card.funds .lm-kpi-icon {
            background: #ecfdf5;
            color: #059669;
        }

        .lm-kpi-value {
            position: relative;
            z-index: 1;
            color: #111827;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -.03em;
            word-break: break-word;
        }

        .lm-kpi-description {
            position: relative;
            z-index: 1;
            margin-top: 7px;
            color: #9ca3af;
            font-size: 11px;
            line-height: 1.4;
        }


        /*
        |--------------------------------------------------------------------------
        | MAIN GRID
        |--------------------------------------------------------------------------
        */

        .lm-main-grid {
            display: grid;
            grid-template-columns:
                minmax(0, 1.35fr)
                minmax(310px, .85fr);
            gap: 20px;
            margin-bottom: 20px;
        }


        /*
        |--------------------------------------------------------------------------
        | PANEL
        |--------------------------------------------------------------------------
        */

        .lm-panel {
            min-width: 0;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            box-shadow:
                0 2px 8px
                rgba(
                    15,
                    23,
                    42,
                    .035
                );
        }

        .lm-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 72px;
            padding: 16px 20px;
            border-bottom: 1px solid #f0f1f3;
        }

        .lm-panel-heading {
            min-width: 0;
        }

        .lm-panel-title {
            margin: 0;
            color: #111827;
            font-size: 16px;
            line-height: 1.3;
            font-weight: 750;
            letter-spacing: -.015em;
        }

        .lm-panel-subtitle {
            margin: 5px 0 0;
            color: #9ca3af;
            font-size: 11px;
            line-height: 1.4;
        }

        .lm-panel-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 0 9px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | PORTFOLIO
        |--------------------------------------------------------------------------
        */

        .lm-portfolio {
            padding: 22px;
        }

        .lm-portfolio-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 25px;
            padding-bottom: 22px;
            border-bottom: 1px solid #f0f1f3;
        }

        .lm-portfolio-label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
        }

        .lm-portfolio-value {
            margin-top: 6px;
            color: #111827;
            font-size: 30px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -.035em;
        }

        .lm-portfolio-description {
            margin-top: 6px;
            color: #9ca3af;
            font-size: 11px;
        }

        .lm-portfolio-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            min-width: 60px;
            border-radius: 16px;
            background:
                linear-gradient(
                    145deg,
                    #111827,
                    #374151
                );
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
            box-shadow:
                0 8px 18px
                rgba(
                    17,
                    24,
                    39,
                    .14
                );
        }

        .lm-loan-breakdown {
            display: grid;
            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );
            gap: 10px;
            padding-top: 17px;
        }

        .lm-loan-breakdown-item {
            min-width: 0;
            padding: 14px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 11px;
        }

        .lm-loan-breakdown-label {
            color: #9ca3af;
            font-size: 9px;
            line-height: 1.3;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: .055em;
        }

        .lm-loan-breakdown-value {
            margin-top: 6px;
            color: #111827;
            font-size: 18px;
            line-height: 1.2;
            font-weight: 800;
        }


        /*
        |--------------------------------------------------------------------------
        | FINANCIAL OVERVIEW
        |--------------------------------------------------------------------------
        */

        .lm-financial-body {
            padding: 20px;
        }

        .lm-financial-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f1f3;
        }

        .lm-financial-item:first-child {
            padding-top: 0;
        }

        .lm-financial-item:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .lm-financial-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .lm-financial-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            min-width: 32px;
            border-radius: 9px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 11px;
            font-weight: 800;
        }

        .lm-financial-label {
            color: #6b7280;
            font-size: 12px;
        }

        .lm-financial-value {
            color: #111827;
            font-size: 13px;
            font-weight: 750;
            text-align: right;
            white-space: nowrap;
        }

        .lm-financial-value.net {
            font-size: 16px;
        }

        .lm-financial-value.positive {
            color: #047857;
        }

        .lm-financial-value.negative {
            color: #b91c1c;
        }


        /*
        |--------------------------------------------------------------------------
        | ACCOUNTS
        |--------------------------------------------------------------------------
        */

        .lm-account-list {
            display: flex;
            flex-direction: column;
        }

        .lm-account {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 15px 20px;
            border-bottom: 1px solid #f3f4f6;
            transition: background .15s ease;
        }

        .lm-account:hover {
            background: #fafafa;
        }

        .lm-account:last-child {
            border-bottom: none;
        }

        .lm-account-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .lm-account-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 11px;
            background: #ecfdf5;
            color: #059669;
            font-size: 14px;
            font-weight: 800;
        }

        .lm-account-details {
            min-width: 0;
        }

        .lm-account-name {
            color: #111827;
            font-size: 13px;
            line-height: 1.35;
            font-weight: 700;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .lm-account-type {
            margin-top: 3px;
            color: #9ca3af;
            font-size: 10px;
            text-transform: capitalize;
        }

        .lm-account-balance {
            color: #111827;
            font-size: 13px;
            font-weight: 750;
            white-space: nowrap;
        }

        .lm-account-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 15px 20px;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }

        .lm-account-footer-label {
            color: #6b7280;
            font-size: 11px;
        }

        .lm-account-footer-value {
            color: #111827;
            font-size: 13px;
            font-weight: 800;
        }


        /*
        |--------------------------------------------------------------------------
        | RECENT LOANS TABLE
        |--------------------------------------------------------------------------
        */

        .lm-table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .lm-table {
            width: 100%;
            min-width: 780px;
            border-collapse: collapse;
        }

        .lm-table th {
            padding: 12px 20px;
            background: #f8fafc;
            color: #6b7280;
            font-size: 9px;
            font-weight: 800;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .06em;
            white-space: nowrap;
        }

        .lm-table td {
            padding: 15px 20px;
            color: #4b5563;
            font-size: 12px;
            border-top: 1px solid #f3f4f6;
            white-space: nowrap;
        }

        .lm-table tbody tr {
            transition: background .15s ease;
        }

        .lm-table tbody tr:hover {
            background: #fafafa;
        }

        .lm-loan-number {
            display: inline-flex;
            align-items: center;
            padding: 5px 8px;
            border-radius: 7px;
            background: #f3f4f6;
            color: #111827;
            font-size: 11px;
            font-weight: 750;
        }

        .lm-borrower {
            color: #111827;
            font-weight: 650;
        }

        .lm-amount {
            color: #111827;
            font-weight: 700;
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS BADGES
        |--------------------------------------------------------------------------
        */

        .lm-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 800;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .lm-status::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            opacity: .8;
        }

        .dashboard-status-active {
            background: #ecfdf5;
            color: #047857;
        }

        .dashboard-status-approved {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .dashboard-status-pending {
            background: #fffbeb;
            color: #b45309;
        }

        .dashboard-status-completed {
            background: #f0fdf4;
            color: #15803d;
        }

        .dashboard-status-overdue {
            background: #fff7ed;
            color: #c2410c;
        }

        .dashboard-status-danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .dashboard-status-default {
            background: #f3f4f6;
            color: #4b5563;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .lm-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 150px;
            padding: 30px 20px;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
        }

        .lm-empty-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            margin-bottom: 10px;
            border-radius: 12px;
            background: #f3f4f6;
            color: #9ca3af;
            font-size: 15px;
            font-weight: 800;
        }


        /*
        |--------------------------------------------------------------------------
        | BUSINESS INFORMATION
        |--------------------------------------------------------------------------
        */

        .lm-business-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );
            gap: 12px;
            padding: 20px;
        }

        .lm-business-item {
            min-width: 0;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #f0f1f3;
            border-radius: 12px;
        }

        .lm-business-label {
            margin-bottom: 7px;
            color: #9ca3af;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .lm-business-value {
            color: #111827;
            font-size: 13px;
            line-height: 1.45;
            font-weight: 700;
            word-break: break-word;
        }


        /*
        |--------------------------------------------------------------------------
        | VIEW BUTTON
        |--------------------------------------------------------------------------
        */

        .lm-view-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 34px;
            padding: 0 11px;
            border: 1px solid #e5e7eb;
            border-radius: 9px;
            background: #ffffff;
            color: #374151;
            font-size: 10px;
            font-weight: 750;
            text-decoration: none;
            white-space: nowrap;
            transition:
                background .15s ease,
                border-color .15s ease,
                color .15s ease,
                transform .15s ease;
        }

        .lm-view-button:hover {
            background: #111827;
            border-color: #111827;
            color: #ffffff;
            transform: translateY(-1px);
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE - LARGE TABLET
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1200px) {

            .lm-kpi-grid {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );
            }

            .lm-main-grid {
                grid-template-columns: 1fr;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE - TABLET
        |--------------------------------------------------------------------------
        */

        @media (max-width: 850px) {

            .lm-dashboard-header {
                align-items: flex-start;
            }

            .lm-business-grid {
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

        @media (max-width: 650px) {

            .lm-dashboard-header {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
                margin-bottom: 20px;
            }

            .lm-dashboard-heading h1 {
                font-size: 24px;
            }

            .lm-dashboard-heading p {
                font-size: 13px;
            }

            .lm-dashboard-meta {
                width: 100%;
            }

            .lm-dashboard-date {
                width: 100%;
                justify-content: center;
            }

            .lm-kpi-grid {
                grid-template-columns: 1fr;
                gap: 12px;
                margin-bottom: 15px;
            }

            .lm-kpi-card {
                padding: 17px;
                border-radius: 14px;
            }

            .lm-kpi-value {
                font-size: 24px;
            }

            .lm-main-grid {
                gap: 15px;
                margin-bottom: 15px;
            }

            .lm-panel {
                border-radius: 14px;
            }

            .lm-panel-header {
                min-height: 64px;
                padding: 14px 16px;
            }

            .lm-panel-title {
                font-size: 15px;
            }

            .lm-panel-subtitle {
                font-size: 10px;
            }

            .lm-portfolio {
                padding: 17px;
            }

            .lm-portfolio-main {
                gap: 15px;
                padding-bottom: 18px;
            }

            .lm-portfolio-value {
                font-size: 25px;
            }

            .lm-portfolio-icon {
                width: 50px;
                height: 50px;
                min-width: 50px;
                border-radius: 13px;
                font-size: 17px;
            }

            .lm-loan-breakdown {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );
            }

            .lm-account {
                padding: 14px 16px;
            }

            .lm-account-footer {
                padding: 14px 16px;
            }

            .lm-business-grid {
                grid-template-columns: 1fr;
                padding: 15px;
            }

            .lm-table {
                min-width: 720px;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE - SMALL MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 430px) {

            .lm-dashboard-heading h1 {
                font-size: 22px;
            }

            .lm-kpi-value {
                font-size: 22px;
            }

            .lm-loan-breakdown {
                grid-template-columns: 1fr;
            }

            .lm-portfolio-value {
                font-size: 23px;
            }

            .lm-financial-body {
                padding: 16px;
            }

            .lm-financial-item {
                padding: 13px 0;
            }

            .lm-financial-label {
                font-size: 11px;
            }

            .lm-financial-value {
                font-size: 12px;
            }

            .lm-financial-value.net {
                font-size: 14px;
            }

            .lm-account-balance {
                font-size: 12px;
            }

            .lm-panel-header {
                align-items: flex-start;
            }

            .lm-panel-count {
                font-size: 9px;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | VERY SMALL DEVICES
        |--------------------------------------------------------------------------
        */

        @media (max-width: 350px) {

            .lm-kpi-card {
                padding: 15px;
            }

            .lm-kpi-icon {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .lm-account-left {
                gap: 8px;
            }

            .lm-account-icon {
                width: 36px;
                height: 36px;
                min-width: 36px;
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
            Dashboard
        </div>

        <div class="user-info">

            <span class="user-name">
                <?= htmlspecialchars($userName) ?>
            </span>

            <span class="badge">
                <?= htmlspecialchars($role) ?>
            </span>

        </div>

    </nav>


    <!-- =====================================================
         DASHBOARD CONTENT
    ====================================================== -->

    <div class="container lm-dashboard">


        <!-- =================================================
             WELCOME HEADER
        ================================================== -->

        <div class="lm-dashboard-header">

            <div class="lm-dashboard-heading">

                <div class="lm-dashboard-eyebrow">

                    <span class="lm-dashboard-eyebrow-dot"></span>

                    Overview

                </div>

                <h1>

                    Welcome back,
                    <?= htmlspecialchars($userName) ?>

                </h1>

                <p>

                    Here's an overview of
                    <?= htmlspecialchars($businessName) ?>.

                </p>

            </div>


            <div class="lm-dashboard-meta">

                <div class="lm-dashboard-date">

                    <span class="lm-date-icon">
                        ▣
                    </span>

                    <span>
                        <?= htmlspecialchars($currentDate) ?>
                    </span>

                </div>

            </div>

        </div>


        <!-- =================================================
             KPI CARDS
        ================================================== -->

        <div class="lm-kpi-grid">


            <!-- BORROWERS -->

            <div class="lm-kpi-card borrowers">

                <div class="lm-kpi-top">

                    <div class="lm-kpi-label">
                        Total Borrowers
                    </div>

                    <div class="lm-kpi-icon">
                        B
                    </div>

                </div>

                <div class="lm-kpi-value">

                    <?= number_format(
                        $totalBorrowers
                    ) ?>

                </div>

                <div class="lm-kpi-description">

                    Registered borrowers

                </div>

            </div>


            <!-- ACTIVE LOANS -->

            <div class="lm-kpi-card loans">

                <div class="lm-kpi-top">

                    <div class="lm-kpi-label">
                        Active Loans
                    </div>

                    <div class="lm-kpi-icon">
                        L
                    </div>

                </div>

                <div class="lm-kpi-value">

                    <?= number_format(
                        $activeLoans
                    ) ?>

                </div>

                <div class="lm-kpi-description">

                    Currently active loans

                </div>

            </div>


            <!-- OUTSTANDING -->

            <div class="lm-kpi-card outstanding">

                <div class="lm-kpi-top">

                    <div class="lm-kpi-label">
                        Outstanding Balance
                    </div>

                    <div class="lm-kpi-icon">
                        ₱
                    </div>

                </div>

                <div class="lm-kpi-value">

                    <?= dashboardMoney(
                        $outstandingBalance
                    ) ?>

                </div>

                <div class="lm-kpi-description">

                    Remaining loan balance

                </div>

            </div>


            <!-- AVAILABLE FUNDS -->

            <div class="lm-kpi-card funds">

                <div class="lm-kpi-top">

                    <div class="lm-kpi-label">
                        Available Funds
                    </div>

                    <div class="lm-kpi-icon">
                        ₱
                    </div>

                </div>

                <div class="lm-kpi-value">

                    <?= dashboardMoney(
                        $totalFunds
                    ) ?>

                </div>

                <div class="lm-kpi-description">

                    Active account balances

                </div>

            </div>


        </div>


        <!-- =================================================
             PORTFOLIO + FINANCIAL
        ================================================== -->

        <div class="lm-main-grid">


            <!-- =================================================
                 LOAN PORTFOLIO
            ================================================== -->

            <div class="lm-panel">


                <div class="lm-panel-header">

                    <div class="lm-panel-heading">

                        <h2 class="lm-panel-title">
                            Loan Portfolio
                        </h2>

                        <p class="lm-panel-subtitle">
                            Current lending activity
                        </p>

                    </div>

                    <div class="lm-panel-count">
                        <?= number_format($activeLoans) ?>
                        active
                    </div>

                </div>


                <div class="lm-portfolio">


                    <div class="lm-portfolio-main">


                        <div>

                            <div class="lm-portfolio-label">
                                Outstanding Balance
                            </div>

                            <div class="lm-portfolio-value">

                                <?= dashboardMoney(
                                    $outstandingBalance
                                ) ?>

                            </div>

                            <div class="lm-portfolio-description">
                                Total amount currently outstanding
                            </div>

                        </div>


                        <div class="lm-portfolio-icon">
                            ₱
                        </div>


                    </div>


                    <div class="lm-loan-breakdown">


                        <div class="lm-loan-breakdown-item">

                            <div class="lm-loan-breakdown-label">
                                Active Loans
                            </div>

                            <div class="lm-loan-breakdown-value">

                                <?= number_format(
                                    $activeLoans
                                ) ?>

                            </div>

                        </div>


                        <div class="lm-loan-breakdown-item">

                            <div class="lm-loan-breakdown-label">
                                Borrowers
                            </div>

                            <div class="lm-loan-breakdown-value">

                                <?= number_format(
                                    $totalBorrowers
                                ) ?>

                            </div>

                        </div>


                        <div class="lm-loan-breakdown-item">

                            <div class="lm-loan-breakdown-label">
                                Accounts
                            </div>

                            <div class="lm-loan-breakdown-value">

                                <?= number_format(
                                    $totalAccounts
                                ) ?>

                            </div>

                        </div>


                    </div>


                </div>

            </div>


            <!-- =================================================
                 FINANCIAL OVERVIEW
            ================================================== -->

            <div class="lm-panel">


                <div class="lm-panel-header">

                    <div class="lm-panel-heading">

                        <h2 class="lm-panel-title">
                            Financial Overview
                        </h2>

                        <p class="lm-panel-subtitle">
                            Current business position
                        </p>

                    </div>

                </div>


                <div class="lm-financial-body">


                    <!-- ASSETS -->

                    <div class="lm-financial-item">

                        <div class="lm-financial-left">

                            <div class="lm-financial-icon">
                                A
                            </div>

                            <span class="lm-financial-label">
                                Total Assets
                            </span>

                        </div>

                        <span class="lm-financial-value">

                            <?= dashboardMoney(
                                $totalAssets
                            ) ?>

                        </span>

                    </div>


                    <!-- LIABILITIES -->

                    <div class="lm-financial-item">

                        <div class="lm-financial-left">

                            <div class="lm-financial-icon">
                                L
                            </div>

                            <span class="lm-financial-label">
                                Total Liabilities
                            </span>

                        </div>

                        <span class="lm-financial-value">

                            <?= dashboardMoney(
                                $totalLiabilities
                            ) ?>

                        </span>

                    </div>


                    <!-- FUNDS -->

                    <div class="lm-financial-item">

                        <div class="lm-financial-left">

                            <div class="lm-financial-icon">
                                F
                            </div>

                            <span class="lm-financial-label">
                                Available Funds
                            </span>

                        </div>

                        <span class="lm-financial-value">

                            <?= dashboardMoney(
                                $totalFunds
                            ) ?>

                        </span>

                    </div>


                    <!-- NET -->

                    <div class="lm-financial-item">

                        <div class="lm-financial-left">

                            <div class="lm-financial-icon">
                                N
                            </div>

                            <span class="lm-financial-label">
                                Net Balance
                            </span>

                        </div>

                        <span
                            class="
                                lm-financial-value
                                net
                                <?= $netBalancePositive
                                    ? 'positive'
                                    : 'negative'
                                ?>
                            "
                        >

                            <?= dashboardMoney(
                                $netBalance
                            ) ?>

                        </span>

                    </div>


                </div>

            </div>


        </div>


        <!-- =================================================
             BUSINESS ACCOUNTS
        ================================================== -->

        <div
            class="lm-panel"
            style="margin-bottom:20px;"
        >


            <div class="lm-panel-header">

                <div class="lm-panel-heading">

                    <h2 class="lm-panel-title">
                        Business Accounts
                    </h2>

                    <p class="lm-panel-subtitle">
                        Current balances of active accounts
                    </p>

                </div>

                <div class="lm-panel-count">

                    <?= number_format(
                        $totalAccounts
                    ) ?>

                    accounts

                </div>

            </div>


            <?php if (!empty($accounts)): ?>


                <div class="lm-account-list">


                    <?php foreach (
                        $accounts
                        as $account
                    ): ?>


                        <div class="lm-account">


                            <div class="lm-account-left">


                                <div class="lm-account-icon">
                                    ₱
                                </div>


                                <div class="lm-account-details">

                                    <div class="lm-account-name">

                                        <?= htmlspecialchars(
                                            $account['account_name']
                                            ??
                                            'Unnamed Account'
                                        ) ?>

                                    </div>

                                    <div class="lm-account-type">

                                        <?= htmlspecialchars(
                                            $account['account_type']
                                            ??
                                            'Account'
                                        ) ?>

                                    </div>

                                </div>


                            </div>


                            <div class="lm-account-balance">

                                <?= dashboardMoney(
                                    (float) (
                                        $account['balance']
                                        ??
                                        0
                                    )
                                ) ?>

                            </div>


                        </div>


                    <?php endforeach; ?>


                </div>


                <div class="lm-account-footer">

                    <span class="lm-account-footer-label">
                        Total Available Funds
                    </span>

                    <span class="lm-account-footer-value">

                        <?= dashboardMoney(
                            $totalFunds
                        ) ?>

                    </span>

                </div>


            <?php else: ?>


                <div class="lm-empty">

                    <div class="lm-empty-icon">
                        ₱
                    </div>

                    No active business accounts found.

                </div>


            <?php endif; ?>


        </div>


        <!-- =================================================
             RECENT LOANS
        ================================================== -->

        <div class="lm-panel">


            <div class="lm-panel-header">

                <div class="lm-panel-heading">

                    <h2 class="lm-panel-title">
                        Recent Loans
                    </h2>

                    <p class="lm-panel-subtitle">
                        Latest loan records in the system
                    </p>

                </div>


                <a
                    href="index.php?url=loans/index"
                    class="lm-view-button"
                >
                    View Loans
                    <span>→</span>
                </a>


            </div>


            <?php if (!empty($recentLoans)): ?>


                <div class="lm-table-wrapper">


                    <table class="lm-table">


                        <thead>

                            <tr>

                                <th>
                                    Loan Number
                                </th>

                                <th>
                                    Borrower
                                </th>

                                <th>
                                    Account
                                </th>

                                <th>
                                    Principal
                                </th>

                                <th>
                                    Total Payable
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php foreach (
                                $recentLoans
                                as $loan
                            ): ?>


                                <?php

                                $loanStatus =
                                    $loan['status']
                                    ??
                                    'pending';

                                ?>


                                <tr>


                                    <td>

                                        <span
                                            class="lm-loan-number"
                                        >

                                            <?= htmlspecialchars(
                                                $loan['loan_number']
                                                ??
                                                'N/A'
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="lm-borrower"
                                        >

                                            <?= htmlspecialchars(
                                                $loan['borrower_name']
                                                ??
                                                'Unknown Borrower'
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $loan['account_name']
                                            ??
                                            'N/A'
                                        ) ?>

                                    </td>


                                    <td>

                                        <span
                                            class="lm-amount"
                                        >

                                            <?= dashboardMoney(
                                                (float) (
                                                    $loan[
                                                        'principal_amount'
                                                    ]
                                                    ??
                                                    0
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="lm-amount"
                                        >

                                            <?= dashboardMoney(
                                                (float) (
                                                    $loan[
                                                        'total_payable'
                                                    ]
                                                    ??
                                                    0
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="
                                                lm-status
                                                <?= htmlspecialchars(
                                                    dashboardStatusClass(
                                                        $loanStatus
                                                    )
                                                )
                                            ?>"
                                        >

                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $loanStatus
                                                )
                                            ) ?>

                                        </span>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            <?php else: ?>


                <div class="lm-empty">

                    <div class="lm-empty-icon">
                        L
                    </div>

                    No loans have been created yet.

                </div>


            <?php endif; ?>


        </div>


        <!-- =================================================
             BUSINESS INFORMATION
        ================================================== -->

        <div
            class="lm-panel"
            style="margin-top:20px;"
        >


            <div class="lm-panel-header">

                <div class="lm-panel-heading">

                    <h2 class="lm-panel-title">
                        Business Information
                    </h2>

                    <p class="lm-panel-subtitle">
                        Current account and business details
                    </p>

                </div>

            </div>


            <div class="lm-business-grid">


                <!-- BUSINESS -->

                <div class="lm-business-item">

                    <div class="lm-business-label">
                        Business
                    </div>

                    <div class="lm-business-value">

                        <?= htmlspecialchars(
                            $businessName
                        ) ?>

                    </div>

                </div>


                <!-- BUSINESS ID -->

                <div class="lm-business-item">

                    <div class="lm-business-label">
                        Business ID
                    </div>

                    <div class="lm-business-value">

                        <?= htmlspecialchars(
                            (string) $businessId
                        ) ?>

                    </div>

                </div>


                <!-- ROLE -->

                <div class="lm-business-item">

                    <div class="lm-business-label">
                        Your Role
                    </div>

                    <div class="lm-business-value">

                        <?= htmlspecialchars(
                            $role
                        ) ?>

                    </div>

                </div>


            </div>


        </div>


    </div>


</div>


</body>

</html>