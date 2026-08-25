<?php

/*
|--------------------------------------------------------------------------
| SETTINGS PAGE
|--------------------------------------------------------------------------
|
| Multi-business / SaaS settings page.
|
| Expected variables:
|
| $user
| $business
| $tenantRole
|
| Optional:
|
| $settings
| $success
| $error
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| AUTH / BUSINESS DATA
|--------------------------------------------------------------------------
*/

$user =
    $user ?? Auth::user();

$business =
    $business ?? Auth::business();

$tenantRole =
    $tenantRole ?? Auth::tenantRole();

$settings =
    is_array($settings ?? null)
        ? $settings
        : [];


/*
|--------------------------------------------------------------------------
| BUSINESS DATA
|--------------------------------------------------------------------------
*/

$businessId =
    (int)($business['id'] ?? 0);

$businessName =
    $business['name'] ?? '';

$businessSlug =
    $business['slug'] ?? '';

$businessEmail =
    $business['email'] ?? '';

$businessPhone =
    $business['phone'] ?? '';

$businessAddress =
    $business['address'] ?? '';

$businessLogo =
    trim((string)($business['logo'] ?? ''));

$businessStatus =
    $business['status'] ?? 'pending';


/*
|--------------------------------------------------------------------------
| SYSTEM SETTINGS
|--------------------------------------------------------------------------
*/

$currency =
    $settings['currency']
    ?? 'PHP';

$currencySymbol =
    $settings['currency_symbol']
    ?? '₱';

$dateFormat =
    $settings['date_format']
    ?? 'Y-m-d';

$timezone =
    $settings['timezone']
    ?? 'Asia/Manila';

$language =
    $settings['language']
    ?? 'English';


/*
|--------------------------------------------------------------------------
| LOAN SETTINGS
|--------------------------------------------------------------------------
*/

$defaultInterestType =
    $settings['default_interest_type']
    ?? 'flat';

$defaultPaymentType =
    $settings['default_payment_type']
    ?? 'installment';

$defaultTermPeriod =
    $settings['default_term_period']
    ?? 'months';

$defaultInterestRate =
    $settings['default_interest_rate']
    ?? '0.00';

$defaultProcessingFee =
    $settings['default_processing_fee']
    ?? '0.00';


/*
|--------------------------------------------------------------------------
| NOTIFICATION SETTINGS
|--------------------------------------------------------------------------
*/

$emailNotifications =
    isset($settings['email_notifications'])
        ? (bool)$settings['email_notifications']
        : true;

$paymentNotifications =
    isset($settings['payment_notifications'])
        ? (bool)$settings['payment_notifications']
        : true;

$overdueNotifications =
    isset($settings['overdue_notifications'])
        ? (bool)$settings['overdue_notifications']
        : true;


/*
|--------------------------------------------------------------------------
| SECURITY SETTINGS
|--------------------------------------------------------------------------
*/

$sessionTimeout =
    $settings['session_timeout']
    ?? '120';

$loginAttempts =
    $settings['login_attempts']
    ?? '5';


/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

function settingsValue(
    $value,
    $fallback = ''
) {
    if (
        $value === null ||
        $value === ''
    ) {
        $value = $fallback;
    }

    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}


function settingsChecked(
    $value
) {
    return $value
        ? 'checked'
        : '';
}


function settingsSelected(
    $value,
    $expected
) {
    return (string)$value === (string)$expected
        ? 'selected'
        : '';
}


/*
|--------------------------------------------------------------------------
| LOGO URL
|--------------------------------------------------------------------------
|
| The database may contain:
|
| uploads/businesses/logo.png
| /uploads/businesses/logo.png
| http://...
| https://...
|
| Convert relative paths into browser paths.
|
|--------------------------------------------------------------------------
*/

$logoUrl = '';

if ($businessLogo !== '') {

    /*
    |--------------------------------------------------------------------------
    | Absolute URL
    |--------------------------------------------------------------------------
    */

    if (
        str_starts_with(
            $businessLogo,
            'http://'
        )
        ||
        str_starts_with(
            $businessLogo,
            'https://'
        )
    ) {

        $logoUrl =
            $businessLogo;

    }

    /*
    |--------------------------------------------------------------------------
    | Root-relative URL
    |--------------------------------------------------------------------------
    */

    elseif (
        str_starts_with(
            $businessLogo,
            '/'
        )
    ) {

        $logoUrl =
            $businessLogo;

    }

    /*
    |--------------------------------------------------------------------------
    | Relative path
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | uploads/businesses/logo.png
    |
    |--------------------------------------------------------------------------
    */

    else {

        $logoUrl =
            '/' .
            ltrim(
                $businessLogo,
                '/'
            );

    }

}


/*
|--------------------------------------------------------------------------
| SUCCESS / ERROR
|--------------------------------------------------------------------------
*/

$success =
    $success ?? null;

$error =
    $error ?? null;

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
    Settings |
    <?= settingsValue(
        $businessName,
        'SaaS'
    ) ?>
</title>


<link
    rel="stylesheet"
    href="assets/css/style.css"
>


<style>

    /*
    |--------------------------------------------------------------------------
    | PAGE
    |--------------------------------------------------------------------------
    */

    .settings-page {

        width: 100%;

        max-width: 1400px;

        margin: 0 auto;

    }


    /*
    |--------------------------------------------------------------------------
    | HEADER
    |--------------------------------------------------------------------------
    */

    .settings-page-header {

        display: flex;

        align-items: flex-start;

        justify-content: space-between;

        gap: 20px;

        margin-bottom: 25px;

    }


    .settings-page-title h1 {

        margin: 0 0 6px;

        font-size: 28px;

        font-weight: 700;

        color: #111827;

    }


    .settings-page-title p {

        margin: 0;

        color: #6b7280;

        font-size: 14px;

    }


    /*
    |--------------------------------------------------------------------------
    | LAYOUT
    |--------------------------------------------------------------------------
    */

    .settings-layout {

        display: grid;

        grid-template-columns:
            230px
            minmax(0, 1fr);

        gap: 22px;

        align-items: start;

    }


    /*
    |--------------------------------------------------------------------------
    | SETTINGS SIDEBAR
    |--------------------------------------------------------------------------
    */

    .settings-sidebar {

        background: #fff;

        border:
            1px solid
            #e5e7eb;

        border-radius: 12px;

        overflow: hidden;

        box-shadow:
            0 2px 8px
            rgba(
                0,
                0,
                0,
                0.04
            );

    }


    .settings-sidebar-title {

        padding:
            16px
            18px;

        border-bottom:
            1px solid
            #e5e7eb;

        font-size: 13px;

        font-weight: 700;

        color: #64748b;

        text-transform: uppercase;

        letter-spacing: .04em;

    }


    .settings-nav {

        padding: 8px;

    }


    .settings-nav-item {

        width: 100%;

        display: flex;

        align-items: center;

        gap: 10px;

        padding:
            11px
            12px;

        border-radius: 8px;

        text-decoration: none;

        color: #475569;

        font-size: 13px;

        font-weight: 500;

        margin-bottom: 3px;

        transition:
            background .15s ease,
            color .15s ease;

    }


    .settings-nav-item:hover {

        background: #f8fafc;

        color: #2563eb;

    }


    .settings-nav-item.active {

        background: #eff6ff;

        color: #2563eb;

        font-weight: 700;

    }


    .settings-nav-icon {

        width: 25px;

        text-align: center;

        font-size: 15px;

    }


    /*
    |--------------------------------------------------------------------------
    | CONTENT
    |--------------------------------------------------------------------------
    */

    .settings-content {

        min-width: 0;

    }


    .settings-card {

        background: #fff;

        border:
            1px solid
            #e5e7eb;

        border-radius: 12px;

        margin-bottom: 22px;

        overflow: hidden;

        box-shadow:
            0 2px 8px
            rgba(
                0,
                0,
                0,
                0.04
            );

    }


    .settings-card-header {

        padding:
            18px
            22px;

        border-bottom:
            1px solid
            #e5e7eb;

    }


    .settings-card-header h2 {

        margin:
            0 0 4px;

        font-size: 17px;

        font-weight: 700;

        color: #111827;

    }


    .settings-card-header p {

        margin: 0;

        color: #6b7280;

        font-size: 13px;

    }


    .settings-card-body {

        padding: 22px;

    }


    /*
    |--------------------------------------------------------------------------
    | FORM GRID
    |--------------------------------------------------------------------------
    */

    .settings-form-grid {

        display: grid;

        grid-template-columns:
            repeat(
                2,
                minmax(
                    0,
                    1fr
                )
            );

        gap: 20px;

    }


    .settings-form-group {

        min-width: 0;

    }


    .settings-form-group.full {

        grid-column:
            1 / -1;

    }


    .settings-label {

        display: block;

        margin-bottom: 7px;

        color: #374151;

        font-size: 13px;

        font-weight: 600;

    }


    .settings-required {

        color: #dc2626;

    }


    .settings-input,
    .settings-select,
    .settings-textarea {

        width: 100%;

        box-sizing: border-box;

        padding:
            10px
            12px;

        border:
            1px solid
            #d1d5db;

        border-radius: 8px;

        background: #fff;

        color: #111827;

        font-size: 14px;

        outline: none;

        transition:
            border-color .15s ease,
            box-shadow .15s ease;

    }


    .settings-input:focus,
    .settings-select:focus,
    .settings-textarea:focus {

        border-color: #2563eb;

        box-shadow:
            0 0 0 3px
            rgba(
                37,
                99,
                235,
                .10
            );

    }


    .settings-textarea {

        min-height: 100px;

        resize: vertical;

    }


    .settings-help {

        margin-top: 5px;

        color: #94a3b8;

        font-size: 12px;

        line-height: 1.5;

    }


    /*
    |--------------------------------------------------------------------------
    | BRANDING
    |--------------------------------------------------------------------------
    */

    .business-branding {

        display: grid;

        grid-template-columns:
            150px
            minmax(0, 1fr);

        gap: 25px;

        align-items: center;

    }


    .business-logo-preview {

        width: 140px;

        height: 140px;

        border:
            1px solid
            #e5e7eb;

        border-radius: 14px;

        background: #f8fafc;

        display: flex;

        align-items: center;

        justify-content: center;

        overflow: hidden;

    }


    .business-logo-preview img {

        display: block;

        width: 100%;

        height: 100%;

        object-fit: contain;

        padding: 12px;

        box-sizing: border-box;

    }


    .business-logo-placeholder {

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        gap: 6px;

        color: #94a3b8;

        text-align: center;

        font-size: 12px;

    }


    .business-logo-placeholder-icon {

        font-size: 30px;

    }


    .logo-upload-area {

        display: flex;

        flex-direction: column;

        gap: 10px;

    }


    .logo-upload-input {

        display: block;

        width: 100%;

        padding: 10px;

        border:
            1px dashed
            #cbd5e1;

        border-radius: 8px;

        background: #f8fafc;

        font-size: 13px;

    }


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    .business-status-row {

        display: flex;

        align-items: center;

        gap: 10px;

    }


    .settings-status {

        display: inline-flex;

        align-items: center;

        padding:
            5px
            10px;

        border-radius: 999px;

        font-size: 12px;

        font-weight: 700;

    }


    .settings-status.active {

        background: #dcfce7;

        color: #166534;

    }


    .settings-status.pending {

        background: #fef3c7;

        color: #92400e;

    }


    .settings-status.inactive {

        background: #f1f5f9;

        color: #475569;

    }


    .settings-status.suspended {

        background: #fee2e2;

        color: #991b1b;

    }


    /*
    |--------------------------------------------------------------------------
    | TOGGLE
    |--------------------------------------------------------------------------
    */

    .settings-toggle-list {

        display: flex;

        flex-direction: column;

    }


    .settings-toggle {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

        padding:
            15px
            0;

        border-bottom:
            1px solid
            #f1f5f9;

    }


    .settings-toggle:first-child {

        padding-top: 0;

    }


    .settings-toggle:last-child {

        border-bottom: none;

        padding-bottom: 0;

    }


    .settings-toggle-info {

        min-width: 0;

    }


    .settings-toggle-title {

        display: block;

        margin-bottom: 3px;

        color: #111827;

        font-size: 14px;

        font-weight: 600;

    }


    .settings-toggle-description {

        display: block;

        color: #64748b;

        font-size: 12px;

        line-height: 1.5;

    }


    .settings-switch {

        position: relative;

        width: 44px;

        height: 24px;

        flex-shrink: 0;

    }


    .settings-switch input {

        opacity: 0;

        width: 0;

        height: 0;

    }


    .settings-slider {

        position: absolute;

        cursor: pointer;

        inset: 0;

        background: #cbd5e1;

        border-radius: 999px;

        transition: .2s;

    }


    .settings-slider:before {

        content: "";

        position: absolute;

        width: 18px;

        height: 18px;

        left: 3px;

        top: 3px;

        background: #fff;

        border-radius: 50%;

        transition: .2s;

        box-shadow:
            0 1px 3px
            rgba(
                0,
                0,
                0,
                .20
            );

    }


    .settings-switch input:checked
    + .settings-slider {

        background: #2563eb;

    }


    .settings-switch input:checked
    + .settings-slider:before {

        transform:
            translateX(20px);

    }


    /*
    |--------------------------------------------------------------------------
    | INFO BOX
    |--------------------------------------------------------------------------
    */

    .settings-info-box {

        padding:
            14px
            16px;

        border:
            1px solid
            #dbeafe;

        background: #eff6ff;

        border-radius: 9px;

        color: #1e40af;

        font-size: 13px;

        line-height: 1.6;

        margin-bottom: 20px;

    }


    /*
    |--------------------------------------------------------------------------
    | ALERTS
    |--------------------------------------------------------------------------
    */

    .settings-alert {

        padding:
            12px
            15px;

        border-radius: 8px;

        margin-bottom: 20px;

        font-size: 13px;

        font-weight: 500;

    }


    .settings-alert-success {

        background: #dcfce7;

        border:
            1px solid
            #bbf7d0;

        color: #166534;

    }


    .settings-alert-error {

        background: #fee2e2;

        border:
            1px solid
            #fecaca;

        color: #991b1b;

    }


    /*
    |--------------------------------------------------------------------------
    | FOOTER
    |--------------------------------------------------------------------------
    */

    .settings-card-footer {

        padding:
            16px
            22px;

        border-top:
            1px solid
            #e5e7eb;

        background: #fafafa;

        display: flex;

        justify-content: flex-end;

        gap: 10px;

    }


    /*
    |--------------------------------------------------------------------------
    | SaaS PLAN
    |--------------------------------------------------------------------------
    */

    .saas-plan-card {

        background:
            linear-gradient(
                135deg,
                #eff6ff,
                #ffffff
            );

        border:
            1px solid
            #bfdbfe;

        border-radius: 12px;

        padding: 18px;

        margin-bottom: 22px;

    }


    .saas-plan-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 15px;

        margin-bottom: 10px;

    }


    .saas-plan-title {

        font-size: 15px;

        font-weight: 700;

        color: #1e3a8a;

    }


    .saas-plan-badge {

        background: #2563eb;

        color: #fff;

        padding:
            5px
            10px;

        border-radius: 999px;

        font-size: 11px;

        font-weight: 700;

        text-transform: uppercase;

    }


    .saas-plan-text {

        margin: 0;

        color: #475569;

        font-size: 13px;

        line-height: 1.6;

    }


    /*
    |--------------------------------------------------------------------------
    | DANGER ZONE
    |--------------------------------------------------------------------------
    */

    .settings-danger-card {

        border:
            1px solid
            #fecaca;

    }


    .settings-danger-card
    .settings-card-header {

        background: #fffafa;

        border-bottom:
            1px solid
            #fecaca;

    }


    .settings-danger-card
    .settings-card-header h2 {

        color: #991b1b;

    }


    .danger-zone-content {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

    }


    .danger-zone-content h3 {

        margin:
            0 0 5px;

        font-size: 14px;

        color: #374151;

    }


    .danger-zone-content p {

        margin: 0;

        color: #64748b;

        font-size: 12px;

        line-height: 1.5;

    }


    .btn-danger {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        padding:
            9px
            14px;

        border:
            1px solid
            #dc2626;

        border-radius: 8px;

        background: #fff;

        color: #dc2626;

        text-decoration: none;

        font-size: 13px;

        font-weight: 600;

        cursor: pointer;

    }


    .btn-danger:hover {

        background: #fef2f2;

    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */

    @media (
        max-width: 1050px
    ) {

        .settings-layout {

            grid-template-columns: 1fr;

        }


        .settings-sidebar {

            display: block;

        }


        .settings-nav {

            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 5px;

        }


        .settings-nav-item {

            margin: 0;

        }

    }


    @media (
        max-width: 750px
    ) {

        .settings-form-grid {

            grid-template-columns: 1fr;

        }


        .settings-form-group.full {

            grid-column: auto;

        }


        .business-branding {

            grid-template-columns: 1fr;

        }


        .settings-page-header {

            flex-direction: column;

        }


        .settings-nav {

            grid-template-columns:
                1fr 1fr;

        }


        .danger-zone-content {

            flex-direction: column;

            align-items: flex-start;

        }

    }


    @media (
        max-width: 500px
    ) {

        .settings-nav {

            grid-template-columns: 1fr;

        }


        .settings-card-body {

            padding: 16px;

        }


        .settings-card-header {

            padding: 16px;

        }


        .settings-card-footer {

            padding:
                14px
                16px;

        }

    }

</style>

</head>

<body>

<?php

/*
|--------------------------------------------------------------------------
| SIDEBAR
|--------------------------------------------------------------------------
*/

require APP_PATH .
    '/views/layouts/sidebar.php';

?>

<div class="main-content">

<!-- NAVBAR -->

<nav class="navbar">


    <div class="page-title">

        Settings

    </div>


    <div class="user-info">

        <span class="user-name">

            <?= settingsValue(
                $user['full_name']
                ?? $user['username']
                ?? 'User'
            ) ?>

        </span>


        <span class="badge">

            <?= settingsValue(
                $tenantRole,
                'User'
            ) ?>

        </span>

    </div>


</nav>


<div class="container settings-page">


    <!-- PAGE HEADER -->

    <div class="settings-page-header">


        <div class="settings-page-title">

            <h1>
                System Settings
            </h1>

            <p>
                Configure your business, loan system,
                notifications and SaaS preferences.
            </p>

        </div>


    </div>


    <!-- SUCCESS -->

    <?php if (!empty($success)): ?>

        <div
            class="
                settings-alert
                settings-alert-success
            "
        >

            <?= settingsValue(
                $success
            ) ?>

        </div>

    <?php endif; ?>


    <!-- ERROR -->

    <?php if (!empty($error)): ?>

        <div
            class="
                settings-alert
                settings-alert-error
            "
        >

            <?= settingsValue(
                $error
            ) ?>

        </div>

    <?php endif; ?>


    <!-- SETTINGS LAYOUT -->

    <div class="settings-layout">


        <!-- SETTINGS SIDEBAR -->

        <aside class="settings-sidebar">


            <div class="settings-sidebar-title">

                Settings

            </div>


            <div class="settings-nav">


                <a
                    href="#business"
                    class="
                        settings-nav-item
                        active
                    "
                >

                    <span
                        class="settings-nav-icon"
                    >
                        🏢
                    </span>

                    Business

                </a>


                <a
                    href="#branding"
                    class="settings-nav-item"
                >

                    <span
                        class="settings-nav-icon"
                    >
                        🎨
                    </span>

                    Branding

                </a>


                <a
                    href="#regional"
                    class="settings-nav-item"
                >

                    <span
                        class="settings-nav-icon"
                    >
                        🌐
                    </span>

                    Regional

                </a>


                <a
                    href="#loans"
                    class="settings-nav-item"
                >

                    <span
                        class="settings-nav-icon"
                    >
                        💰
                    </span>

                    Loans

                </a>


                <a
                    href="#notifications"
                    class="settings-nav-item"
                >

                    <span
                        class="settings-nav-icon"
                    >
                        🔔
                    </span>

                    Notifications

                </a>


                <a
                    href="#security"
                    class="settings-nav-item"
                >

                    <span
                        class="settings-nav-icon"
                    >
                        🔐
                    </span>

                    Security

                </a>


            </div>


        </aside>


        <!-- SETTINGS CONTENT -->

        <main class="settings-content">


            <!-- SaaS PLAN -->

            <div class="saas-plan-card">


                <div class="saas-plan-header">


                    <div class="saas-plan-title">

                        SaaS Business Account

                    </div>


                    <span
                        class="saas-plan-badge"
                    >

                        <?= settingsValue(
                            ucfirst(
                                $businessStatus
                            )
                        ) ?>

                    </span>


                </div>


                <p class="saas-plan-text">

                    You are currently managing the settings
                    for

                    <strong>
                        <?= settingsValue(
                            $businessName,
                            'your business'
                        ) ?>
                    </strong>.

                    These settings belong to this business
                    tenant and do not affect other businesses
                    using the SaaS platform.

                </p>


            </div>


            <!-- MAIN FORM -->

            <form
                method="POST"
                action="index.php?url=settings/update"
                enctype="multipart/form-data"
            >


                <!-- BUSINESS -->

                <div
                    class="settings-card"
                    id="business"
                >


                    <div
                        class="settings-card-header"
                    >

                        <h2>
                            Business Information
                        </h2>

                        <p>
                            Information displayed throughout
                            your business account.
                        </p>

                    </div>


                    <div
                        class="settings-card-body"
                    >


                        <div
                            class="settings-form-grid"
                        >


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >

                                    Business Name

                                    <span
                                        class="settings-required"
                                    >
                                        *
                                    </span>

                                </label>


                                <input
                                    type="text"
                                    name="name"
                                    class="settings-input"
                                    value="<?= settingsValue(
                                        $businessName
                                    ) ?>"
                                    required
                                >


                                <div
                                    class="settings-help"
                                >

                                    The name of your
                                    lending business.

                                </div>

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >

                                    Business Slug

                                    <span
                                        class="settings-required"
                                    >
                                        *
                                    </span>

                                </label>


                                <input
                                    type="text"
                                    name="slug"
                                    class="settings-input"
                                    value="<?= settingsValue(
                                        $businessSlug
                                    ) ?>"
                                    required
                                >


                                <div
                                    class="settings-help"
                                >

                                    Unique identifier
                                    for this business.

                                </div>

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >

                                    Business Email

                                </label>


                                <input
                                    type="email"
                                    name="email"
                                    class="settings-input"
                                    value="<?= settingsValue(
                                        $businessEmail
                                    ) ?>"
                                >

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >

                                    Phone Number

                                </label>


                                <input
                                    type="text"
                                    name="phone"
                                    class="settings-input"
                                    value="<?= settingsValue(
                                        $businessPhone
                                    ) ?>"
                                >

                            </div>


                            <div
                                class="
                                    settings-form-group
                                    full
                                "
                            >

                                <label
                                    class="settings-label"
                                >

                                    Business Address

                                </label>


                                <textarea
                                    name="address"
                                    class="settings-textarea"
                                ><?= settingsValue(
                                    $businessAddress
                                ) ?></textarea>

                            </div>


                            <div
                                class="
                                    settings-form-group
                                    full
                                "
                            >

                                <label
                                    class="settings-label"
                                >

                                    Business Status

                                </label>


                                <div
                                    class="business-status-row"
                                >

                                    <span
                                        class="
                                            settings-status
                                            <?= settingsValue(
                                                $businessStatus
                                            ) ?>
                                        "
                                    >

                                        <?= settingsValue(
                                            ucfirst(
                                                $businessStatus
                                            )
                                        ) ?>

                                    </span>


                                    <span
                                        class="settings-help"
                                    >

                                        Business status is
                                        controlled by the
                                        SaaS administration
                                        layer.

                                    </span>

                                </div>

                            </div>


                        </div>


                    </div>


                </div>


                <!-- BRANDING -->

                <div
                    class="settings-card"
                    id="branding"
                >


                    <div
                        class="settings-card-header"
                    >

                        <h2>
                            Branding
                        </h2>

                        <p>
                            Customize the appearance of
                            your business inside the SaaS.
                        </p>

                    </div>


                    <div
                        class="settings-card-body"
                    >


                        <div
                            class="business-branding"
                        >


                            <!-- LOGO PREVIEW -->

                            <div
                                class="
                                    business-logo-preview
                                "
                                id="logoPreview"
                            >


                                <?php if (
                                    $logoUrl !== ''
                                ): ?>

                                    <img
                                        src="<?= settingsValue(
                                            $logoUrl
                                        ) ?>"
                                        alt="Business Logo"
                                        onerror="
                                            this.style.display='none';
                                            document.getElementById('logoPlaceholder').style.display='flex';
                                        "
                                    >


                                    <div
                                        id="logoPlaceholder"
                                        class="
                                            business-logo-placeholder
                                        "
                                        style="display:none;"
                                    >

                                        <div
                                            class="
                                                business-logo-placeholder-icon
                                            "
                                        >
                                            🏢
                                        </div>

                                        Logo Not Found

                                    </div>

                                <?php else: ?>

                                    <div
                                        id="logoPlaceholder"
                                        class="
                                            business-logo-placeholder
                                        "
                                    >

                                        <div
                                            class="
                                                business-logo-placeholder-icon
                                            "
                                        >
                                            🏢
                                        </div>

                                        No Logo

                                    </div>

                                <?php endif; ?>


                            </div>


                            <!-- LOGO UPLOAD -->

                            <div
                                class="logo-upload-area"
                            >


                                <label
                                    class="settings-label"
                                >

                                    Business Logo

                                </label>


                                <input
                                    type="file"
                                    name="logo"
                                    id="logoInput"
                                    class="
                                        logo-upload-input
                                    "
                                    accept="
                                        image/png,
                                        image/jpeg,
                                        image/jpg,
                                        image/webp
                                    "
                                >


                                <div
                                    class="settings-help"
                                >

                                    Recommended:
                                    PNG or WebP,
                                    square image,
                                    at least
                                    300 × 300 pixels.

                                </div>


                                <?php if (
                                    $logoUrl !== ''
                                ): ?>

                                    <label
                                        style="
                                            display:flex;
                                            align-items:center;
                                            gap:8px;
                                            font-size:13px;
                                            color:#475569;
                                        "
                                    >

                                        <input
                                            type="checkbox"
                                            name="remove_logo"
                                            value="1"
                                        >

                                        Remove current logo

                                    </label>

                                <?php endif; ?>


                            </div>


                        </div>


                    </div>


                </div>


                <!-- REGIONAL -->

                <div
                    class="settings-card"
                    id="regional"
                >


                    <div
                        class="settings-card-header"
                    >

                        <h2>
                            Regional Settings
                        </h2>

                        <p>
                            Configure currency, timezone
                            and date formatting.
                        </p>

                    </div>


                    <div
                        class="settings-card-body"
                    >


                        <div
                            class="settings-form-grid"
                        >


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Currency
                                </label>


                                <select
                                    name="currency"
                                    id="currencySelect"
                                    class="settings-select"
                                >

                                    <option
                                        value="PHP"
                                        <?= settingsSelected(
                                            $currency,
                                            'PHP'
                                        ) ?>
                                    >
                                        PHP - Philippine Peso
                                    </option>

                                    <option
                                        value="USD"
                                        <?= settingsSelected(
                                            $currency,
                                            'USD'
                                        ) ?>
                                    >
                                        USD - US Dollar
                                    </option>

                                    <option
                                        value="EUR"
                                        <?= settingsSelected(
                                            $currency,
                                            'EUR'
                                        ) ?>
                                    >
                                        EUR - Euro
                                    </option>

                                    <option
                                        value="GBP"
                                        <?= settingsSelected(
                                            $currency,
                                            'GBP'
                                        ) ?>
                                    >
                                        GBP - British Pound
                                    </option>

                                </select>

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Currency Symbol
                                </label>


                                <input
                                    type="text"
                                    name="currency_symbol"
                                    id="currencySymbol"
                                    class="settings-input"
                                    value="<?= settingsValue(
                                        $currencySymbol
                                    ) ?>"
                                    maxlength="5"
                                    readonly
                                >

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Timezone
                                </label>


                                <select
                                    name="timezone"
                                    class="settings-select"
                                >

                                    <option
                                        value="Asia/Manila"
                                        <?= settingsSelected(
                                            $timezone,
                                            'Asia/Manila'
                                        ) ?>
                                    >
                                        Asia/Manila
                                    </option>

                                    <option
                                        value="Asia/Singapore"
                                        <?= settingsSelected(
                                            $timezone,
                                            'Asia/Singapore'
                                        ) ?>
                                    >
                                        Asia/Singapore
                                    </option>

                                    <option
                                        value="Asia/Tokyo"
                                        <?= settingsSelected(
                                            $timezone,
                                            'Asia/Tokyo'
                                        ) ?>
                                    >
                                        Asia/Tokyo
                                    </option>

                                    <option
                                        value="UTC"
                                        <?= settingsSelected(
                                            $timezone,
                                            'UTC'
                                        ) ?>
                                    >
                                        UTC
                                    </option>

                                </select>

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Date Format
                                </label>


                                <select
                                    name="date_format"
                                    class="settings-select"
                                >

                                    <option
                                        value="Y-m-d"
                                        <?= settingsSelected(
                                            $dateFormat,
                                            'Y-m-d'
                                        ) ?>
                                    >
                                        2026-08-25
                                    </option>

                                    <option
                                        value="m/d/Y"
                                        <?= settingsSelected(
                                            $dateFormat,
                                            'm/d/Y'
                                        ) ?>
                                    >
                                        08/25/2026
                                    </option>

                                    <option
                                        value="d/m/Y"
                                        <?= settingsSelected(
                                            $dateFormat,
                                            'd/m/Y'
                                        ) ?>
                                    >
                                        25/08/2026
                                    </option>

                                    <option
                                        value="F d, Y"
                                        <?= settingsSelected(
                                            $dateFormat,
                                            'F d, Y'
                                        ) ?>
                                    >
                                        August 25, 2026
                                    </option>

                                </select>

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Language
                                </label>


                                <select
                                    name="language"
                                    class="settings-select"
                                >

                                    <option
                                        value="English"
                                        <?= settingsSelected(
                                            $language,
                                            'English'
                                        ) ?>
                                    >
                                        English
                                    </option>

                                </select>

                            </div>


                        </div>


                    </div>


                </div>


                <!-- LOANS -->

                <div
                    class="settings-card"
                    id="loans"
                >


                    <div
                        class="settings-card-header"
                    >

                        <h2>
                            Loan Defaults
                        </h2>

                        <p>
                            Default values used when creating
                            new loans.
                        </p>

                    </div>


                    <div
                        class="settings-card-body"
                    >


                        <div
                            class="settings-info-box"
                        >

                            These values are only defaults.
                            Loan officers can change the
                            applicable values when creating
                            an individual loan.

                        </div>


                        <div
                            class="settings-form-grid"
                        >


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Default Interest Type
                                </label>


                                <select
                                    name="default_interest_type"
                                    class="settings-select"
                                >

                                    <option
                                        value="flat"
                                        <?= settingsSelected(
                                            $defaultInterestType,
                                            'flat'
                                        ) ?>
                                    >
                                        Flat Interest
                                    </option>

                                    <option
                                        value="reducing_balance"
                                        <?= settingsSelected(
                                            $defaultInterestType,
                                            'reducing_balance'
                                        ) ?>
                                    >
                                        Reducing Balance
                                    </option>

                                </select>

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Default Payment Type
                                </label>


                                <select
                                    name="default_payment_type"
                                    class="settings-select"
                                >

                                    <option
                                        value="installment"
                                        <?= settingsSelected(
                                            $defaultPaymentType,
                                            'installment'
                                        ) ?>
                                    >
                                        Installment
                                    </option>

                                    <option
                                        value="full_payment"
                                        <?= settingsSelected(
                                            $defaultPaymentType,
                                            'full_payment'
                                        ) ?>
                                    >
                                        Full Payment
                                    </option>

                                </select>

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Default Term Period
                                </label>


                                <select
                                    name="default_term_period"
                                    class="settings-select"
                                >

                                    <option
                                        value="days"
                                        <?= settingsSelected(
                                            $defaultTermPeriod,
                                            'days'
                                        ) ?>
                                    >
                                        Days
                                    </option>

                                    <option
                                        value="weeks"
                                        <?= settingsSelected(
                                            $defaultTermPeriod,
                                            'weeks'
                                        ) ?>
                                    >
                                        Weeks
                                    </option>

                                    <option
                                        value="months"
                                        <?= settingsSelected(
                                            $defaultTermPeriod,
                                            'months'
                                        ) ?>
                                    >
                                        Months
                                    </option>

                                    <option
                                        value="years"
                                        <?= settingsSelected(
                                            $defaultTermPeriod,
                                            'years'
                                        ) ?>
                                    >
                                        Years
                                    </option>

                                </select>

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Default Interest Rate (%)
                                </label>


                                <input
                                    type="number"
                                    name="default_interest_rate"
                                    class="settings-input"
                                    value="<?= settingsValue(
                                        $defaultInterestRate
                                    ) ?>"
                                    min="0"
                                    step="0.01"
                                >

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Default Processing Fee
                                </label>


                                <input
                                    type="number"
                                    name="default_processing_fee"
                                    class="settings-input"
                                    value="<?= settingsValue(
                                        $defaultProcessingFee
                                    ) ?>"
                                    min="0"
                                    step="0.01"
                                >

                            </div>


                        </div>


                    </div>


                </div>


                <!-- NOTIFICATIONS -->

                <div
                    class="settings-card"
                    id="notifications"
                >


                    <div
                        class="settings-card-header"
                    >

                        <h2>
                            Notifications
                        </h2>

                        <p>
                            Control notifications for this
                            business.
                        </p>

                    </div>


                    <div
                        class="settings-card-body"
                    >


                        <div
                            class="settings-toggle-list"
                        >


                            <div
                                class="settings-toggle"
                            >

                                <div
                                    class="settings-toggle-info"
                                >

                                    <span
                                        class="
                                            settings-toggle-title
                                        "
                                    >
                                        Email Notifications
                                    </span>

                                    <span
                                        class="
                                            settings-toggle-description
                                        "
                                    >
                                        Allow the system to send
                                        business notifications
                                        through email.
                                    </span>

                                </div>


                                <label
                                    class="settings-switch"
                                >

                                    <input
                                        type="checkbox"
                                        name="email_notifications"
                                        value="1"
                                        <?= settingsChecked(
                                            $emailNotifications
                                        ) ?>
                                    >

                                    <span
                                        class="settings-slider"
                                    ></span>

                                </label>

                            </div>


                            <div
                                class="settings-toggle"
                            >

                                <div
                                    class="settings-toggle-info"
                                >

                                    <span
                                        class="
                                            settings-toggle-title
                                        "
                                    >
                                        Payment Notifications
                                    </span>

                                    <span
                                        class="
                                            settings-toggle-description
                                        "
                                    >
                                        Notify authorized users
                                        when loan payments
                                        are posted.
                                    </span>

                                </div>


                                <label
                                    class="settings-switch"
                                >

                                    <input
                                        type="checkbox"
                                        name="payment_notifications"
                                        value="1"
                                        <?= settingsChecked(
                                            $paymentNotifications
                                        ) ?>
                                    >

                                    <span
                                        class="settings-slider"
                                    ></span>

                                </label>

                            </div>


                            <div
                                class="settings-toggle"
                            >

                                <div
                                    class="settings-toggle-info"
                                >

                                    <span
                                        class="
                                            settings-toggle-title
                                        "
                                    >
                                        Overdue Notifications
                                    </span>

                                    <span
                                        class="
                                            settings-toggle-description
                                        "
                                    >
                                        Notify authorized users
                                        about overdue loan
                                        schedules.
                                    </span>

                                </div>


                                <label
                                    class="settings-switch"
                                >

                                    <input
                                        type="checkbox"
                                        name="overdue_notifications"
                                        value="1"
                                        <?= settingsChecked(
                                            $overdueNotifications
                                        ) ?>
                                    >

                                    <span
                                        class="settings-slider"
                                    ></span>

                                </label>

                            </div>


                        </div>


                    </div>


                </div>


                <!-- SECURITY -->

                <div
                    class="settings-card"
                    id="security"
                >


                    <div
                        class="settings-card-header"
                    >

                        <h2>
                            Security
                        </h2>

                        <p>
                            Configure basic security behavior
                            for this business account.
                        </p>

                    </div>


                    <div
                        class="settings-card-body"
                    >


                        <div
                            class="settings-form-grid"
                        >


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Session Timeout
                                    (minutes)
                                </label>


                                <input
                                    type="number"
                                    name="session_timeout"
                                    class="settings-input"
                                    value="<?= settingsValue(
                                        $sessionTimeout
                                    ) ?>"
                                    min="5"
                                    max="1440"
                                >


                                <div
                                    class="settings-help"
                                >
                                    Users will be required
                                    to log in again after
                                    being inactive for this
                                    period.
                                </div>

                            </div>


                            <div
                                class="settings-form-group"
                            >

                                <label
                                    class="settings-label"
                                >
                                    Maximum Login Attempts
                                </label>


                                <input
                                    type="number"
                                    name="login_attempts"
                                    class="settings-input"
                                    value="<?= settingsValue(
                                        $loginAttempts
                                    ) ?>"
                                    min="1"
                                    max="20"
                                >


                                <div
                                    class="settings-help"
                                >
                                    Number of failed login
                                    attempts allowed before
                                    additional protection
                                    is applied.
                                </div>

                            </div>


                        </div>


                    </div>


                </div>


                <!-- SAVE -->

                <div class="settings-card">


                    <div
                        class="settings-card-footer"
                    >

                        <button
                            type="reset"
                            class="btn btn-secondary"
                        >
                            Reset
                        </button>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Save Settings
                        </button>

                    </div>


                </div>


            </form>


            <!-- DANGER ZONE -->

            <div
                class="
                    settings-card
                    settings-danger-card
                "
            >


                <div
                    class="settings-card-header"
                >

                    <h2>
                        Danger Zone
                    </h2>

                    <p>
                        Business account actions that may
                        require SaaS administrator approval.
                    </p>

                </div>


                <div
                    class="settings-card-body"
                >


                    <div
                        class="danger-zone-content"
                    >


                        <div>

                            <h3>
                                Deactivate Business
                            </h3>


                            <p>
                                Deactivating a business will
                                prevent users from accessing
                                the tenant until it is
                                reactivated.
                            </p>

                        </div>


                        <button
                            type="button"
                            class="btn-danger"
                            onclick="
                                alert(
                                    'Business deactivation should be handled by the SaaS administrator.'
                                );
                            "
                        >
                            Deactivate Business
                        </button>


                    </div>


                </div>


            </div>


        </main>


    </div>


</div>

</div>

<script>

/*
|--------------------------------------------------------------------------
| LOGO PREVIEW
|--------------------------------------------------------------------------
*/

document
    .getElementById('logoInput')
    ?.addEventListener(
        'change',
        function(event)
        {

            const file =
                event.target.files[0];

            if (!file) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Validate image
            |--------------------------------------------------------------------------
            */

            if (
                !file.type.startsWith(
                    'image/'
                )
            ) {

                alert(
                    'Please select a valid image file.'
                );

                event.target.value = '';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Preview
            |--------------------------------------------------------------------------
            */

            const reader =
                new FileReader();


            reader.onload =
                function(e)
                {

                    const preview =
                        document.getElementById(
                            'logoPreview'
                        );


                    if (!preview) {
                        return;
                    }


                    preview.innerHTML = '';


                    const image =
                        document.createElement(
                            'img'
                        );


                    image.src =
                        e.target.result;

                    image.alt =
                        'Business Logo Preview';


                    image.style.display =
                        'block';

                    image.style.width =
                        '100%';

                    image.style.height =
                        '100%';

                    image.style.objectFit =
                        'contain';

                    image.style.padding =
                        '12px';

                    image.style.boxSizing =
                        'border-box';


                    preview.appendChild(
                        image
                    );

                };


            reader.readAsDataURL(
                file
            );

        }
    );


/*
|--------------------------------------------------------------------------
| AUTOMATIC CURRENCY SYMBOL
|--------------------------------------------------------------------------
|
| The currency symbol automatically changes when
| the currency selection changes.
|
|--------------------------------------------------------------------------
*/

const currencySelect =
    document.getElementById(
        'currencySelect'
    );

const currencySymbol =
    document.getElementById(
        'currencySymbol'
    );


const currencySymbols = {

    PHP: '₱',

    USD: '$',

    EUR: '€',

    GBP: '£'

};


function updateCurrencySymbol()
{

    if (
        !currencySelect ||
        !currencySymbol
    ) {
        return;
    }


    const selectedCurrency =
        currencySelect.value;


    currencySymbol.value =
        currencySymbols[
            selectedCurrency
        ] ?? '';


}


if (currencySelect) {

    currencySelect.addEventListener(
        'change',
        updateCurrencySymbol
    );

}


/*
|--------------------------------------------------------------------------
| SETTINGS NAVIGATION
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll(
        '.settings-nav-item'
    )
    .forEach(
        function(item)
        {

            item.addEventListener(
                'click',
                function()
                {

                    document
                        .querySelectorAll(
                            '.settings-nav-item'
                        )
                        .forEach(
                            function(nav)
                            {

                                nav.classList.remove(
                                    'active'
                                );

                            }
                        );


                    item.classList.add(
                        'active'
                    );

                }
            );

        }
    );

</script>

</body>

</html>