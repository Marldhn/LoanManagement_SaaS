<?php

/*
|--------------------------------------------------------------------------
| REUSABLE SIDEBAR
|--------------------------------------------------------------------------
|
| Expected variables:
|
| $currentUrl
| $user
| $business
| $tenantRole
| $settings
|
*/


/*
|--------------------------------------------------------------------------
| CURRENT URL
|--------------------------------------------------------------------------
*/

$currentUrl =
    $currentUrl
    ?? ($_GET['url'] ?? '');


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

$user =
    $user
    ?? ($_SESSION['user'] ?? []);


/*
|--------------------------------------------------------------------------
| BUSINESS
|--------------------------------------------------------------------------
*/

$business =
    $business
    ?? ($_SESSION['business'] ?? null);


/*
|--------------------------------------------------------------------------
| TENANT ROLE
|--------------------------------------------------------------------------
*/

$tenantRole =
    $tenantRole
    ?? ($_SESSION['tenant_role'] ?? null);


/*
|--------------------------------------------------------------------------
| SETTINGS
|--------------------------------------------------------------------------
|
| Settings should be passed from the controller/view.
|
| We also provide safe defaults so the sidebar does not
| throw an undefined variable warning.
|
*/

$settings =
    $settings
    ?? ($_SESSION['settings'] ?? []);


/*
|--------------------------------------------------------------------------
| USER TYPES
|--------------------------------------------------------------------------
*/

$isSuperAdmin =
    ($user['role'] ?? '') === 'super_admin';

$isBusinessAdmin =
    in_array(
        $tenantRole,
        ['owner', 'admin'],
        true
    );

$isLoanOfficer =
    $tenantRole === 'loan_officer';

$isCashier =
    $tenantRole === 'cashier';

$isStaff =
    $tenantRole === 'staff';


/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function sidebarActive(
    string $currentUrl,
    string $route
): string {

    return (
        $currentUrl === $route
        ||
        str_starts_with(
            $currentUrl,
            $route . '/'
        )
    )
        ? 'active'
        : '';
}


/*
|--------------------------------------------------------------------------
| USER DISPLAY
|--------------------------------------------------------------------------
*/

$displayName =
    trim(
        $user['full_name']
        ??
        $user['username']
        ??
        'Administrator'
    );


if ($displayName === '') {

    $displayName =
        'Administrator';
}


/*
|--------------------------------------------------------------------------
| AVATAR LETTER
|--------------------------------------------------------------------------
*/

$avatarLetter =
    strtoupper(
        substr(
            $displayName,
            0,
            1
        )
    );


/*
|--------------------------------------------------------------------------
| DISPLAY ROLE
|--------------------------------------------------------------------------
*/

$displayRole =
    $tenantRole
    ??
    $user['role']
    ??
    'Administrator';


$displayRole =
    ucwords(
        str_replace(
            '_',
            ' ',
            $displayRole
        )
    );


/*
|--------------------------------------------------------------------------
| BRAND SETTINGS
|--------------------------------------------------------------------------
|
| These values come from the Settings page/database.
|
*/


/*
|--------------------------------------------------------------------------
| BUSINESS / APPLICATION NAME
|--------------------------------------------------------------------------
*/

$sidebarBrandName =
    trim(
        $settings['business_name']
        ??
        $settings['company_name']
        ??
        $business['name']
        ??
        'Loan Management'
    );


if ($sidebarBrandName === '') {

    $sidebarBrandName =
        'Loan Management';
}


/*
|--------------------------------------------------------------------------
| BRAND TAGLINE
|--------------------------------------------------------------------------
*/

$sidebarBrandTagline =
    trim(
        $settings['business_tagline']
        ??
        $settings['tagline']
        ??
        'SaaS Platform'
    );


if ($sidebarBrandTagline === '') {

    $sidebarBrandTagline =
        'SaaS Platform';
}


/*
|--------------------------------------------------------------------------
| BRAND LOGO
|--------------------------------------------------------------------------
*/

$sidebarLogo =
    trim(
        $settings['logo']
        ??
        $settings['logo_path']
        ??
        ''
    );


/*
|--------------------------------------------------------------------------
| LOGO URL NORMALIZATION
|--------------------------------------------------------------------------
|
| If the database stores:
|
| uploads/settings/logo.png
|
| it will work as:
|
| ../uploads/settings/logo.png
|
| depending on the location of the current page.
|
| For your current index.php?url= routing, assets are normally
| relative to public/.
|
*/


if (
    $sidebarLogo !== ''
    &&
    !str_starts_with(
        $sidebarLogo,
        'http://'
    )
    &&
    !str_starts_with(
        $sidebarLogo,
        'https://'
    )
    &&
    !str_starts_with(
        $sidebarLogo,
        '/'
    )
) {

    /*
    |--------------------------------------------------------------------------
    | Remove leading ./ if present
    |--------------------------------------------------------------------------
    */

    $sidebarLogo =
        ltrim(
            $sidebarLogo,
            './'
        );

    $sidebarLogo =
        $sidebarLogo;
}


/*
|--------------------------------------------------------------------------
| MOBILE SIDEBAR TOGGLE
|--------------------------------------------------------------------------
*/

?>

<button
    type="button"
    class="mobile-sidebar-toggle"
    id="mobileSidebarToggle"
    aria-label="Open navigation"
    aria-controls="sidebar"
    aria-expanded="false"
>
    <span></span>
    <span></span>
    <span></span>
</button>


<!-- ======================================================================
     MOBILE SIDEBAR OVERLAY
======================================================================= -->

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>


<!-- ======================================================================
     SIDEBAR
======================================================================= -->

<aside
    class="sidebar"
    id="sidebar"
>


    <!-- ==================================================================
         BRAND
    =================================================================== -->

    <div class="sidebar-brand">


        <!-- ==============================================================
             BRAND LOGO
        ============================================================== -->

        <div class="sidebar-brand-icon">

            <?php if ($sidebarLogo !== ''): ?>

                <img
                    src="<?= htmlspecialchars(
                        $sidebarLogo,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    alt="<?= htmlspecialchars(
                        $sidebarBrandName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    class="sidebar-logo-image"
                >

            <?php else: ?>

                <span class="sidebar-default-logo">
                    ₱
                </span>

            <?php endif; ?>

        </div>


        <!-- ==============================================================
             BRAND INFORMATION
        ============================================================== -->

        <div class="sidebar-brand-content">

            <div class="sidebar-brand-title">

                <?= htmlspecialchars(
                    $sidebarBrandName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>


            <div class="sidebar-brand-subtitle">

                <?= htmlspecialchars(
                    $sidebarBrandTagline,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>

    </div>


    <!-- ==================================================================
         BUSINESS INFORMATION
    =================================================================== -->

    <?php if (
        !$isSuperAdmin
        &&
        !empty($business)
    ): ?>

        <div class="sidebar-business">

            <div class="sidebar-business-card">


                <div class="sidebar-business-icon">

                    ◈

                </div>


                <div class="sidebar-business-content">

                    <div class="sidebar-business-label">

                        BUSINESS

                    </div>


                    <div class="sidebar-business-name">

                        <?= htmlspecialchars(
                            $business['name']
                            ??
                            'Business',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>

            </div>

        </div>

    <?php endif; ?>


    <!-- ==================================================================
         NAVIGATION
    =================================================================== -->

    <nav class="sidebar-nav">


        <?php if ($isSuperAdmin): ?>


            <!-- ==========================================================
                 SUPER ADMIN
            =========================================================== -->


            <!-- MAIN -->

            <div class="sidebar-section">

                <span>
                    Main
                </span>

            </div>


            <!-- DASHBOARD -->

            <a
                href="index.php?url=dashboard"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'dashboard'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ▦
                </span>

                <span class="sidebar-link-text">
                    Dashboard
                </span>

            </a>


            <!-- BUSINESSES -->

            <a
                href="index.php?url=businesses"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'businesses'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ◫
                </span>

                <span class="sidebar-link-text">
                    Businesses
                </span>

            </a>


            <!-- USERS -->

            <a
                href="index.php?url=users"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'users'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ◉
                </span>

                <span class="sidebar-link-text">
                    Users
                </span>

            </a>


            <!-- ==========================================================
                 BILLING
            =========================================================== -->

            <div class="sidebar-section">

                <span>
                    Billing
                </span>

            </div>


            <!-- PLANS -->

            <a
                href="index.php?url=plans"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'plans'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ◆
                </span>

                <span class="sidebar-link-text">
                    Plans
                </span>

            </a>


            <!-- SUBSCRIPTIONS -->

            <a
                href="index.php?url=subscriptions"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'subscriptions'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ▤
                </span>

                <span class="sidebar-link-text">
                    Subscriptions
                </span>

            </a>


            <!-- ==========================================================
                 SYSTEM
            =========================================================== -->

            <div class="sidebar-section">

                <span>
                    System
                </span>

            </div>


            <!-- SETTINGS -->

            <a
                href="index.php?url=settings"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'settings'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ⚙
                </span>

                <span class="sidebar-link-text">
                    System Settings
                </span>

            </a>


            <!-- AUDIT LOGS -->

            <a
                href="index.php?url=audit-logs"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'audit-logs'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ≡
                </span>

                <span class="sidebar-link-text">
                    Audit Logs
                </span>

            </a>


        <?php else: ?>


            <!-- ==========================================================
                 BUSINESS USER
            =========================================================== -->


            <!-- ==========================================================
                 MAIN
            =========================================================== -->

            <div class="sidebar-section">

                <span>
                    Main
                </span>

            </div>


            <!-- DASHBOARD -->

            <a
                href="index.php?url=dashboard"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'dashboard'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ▦
                </span>

                <span class="sidebar-link-text">
                    Dashboard
                </span>

            </a>


            <!-- ==========================================================
                 LENDING
            =========================================================== -->

            <div class="sidebar-section">

                <span>
                    Lending
                </span>

            </div>


            <!-- BORROWERS -->

            <a
                href="index.php?url=borrowers"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'borrowers'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ◉
                </span>

                <span class="sidebar-link-text">
                    Borrowers
                </span>

            </a>


            <!-- LOANS -->

            <a
                href="index.php?url=loans"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'loans'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ₱
                </span>

                <span class="sidebar-link-text">
                    Loans
                </span>

            </a>


            <!-- PAYMENTS -->

            <a
                href="index.php?url=payments"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'payments'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ▤
                </span>

                <span class="sidebar-link-text">
                    Payments
                </span>

            </a>


            <!-- COLLECTIONS -->

            <a
                href="index.php?url=collections"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'collections'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ◷
                </span>

                <span class="sidebar-link-text">
                    Collections
                </span>

            </a>


            <!-- ==========================================================
                 FINANCE
            =========================================================== -->

            <div class="sidebar-section">

                <span>
                    Finance
                </span>

            </div>


            <!-- ACCOUNTS -->

            <a
                href="index.php?url=accounts"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'accounts'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ◈
                </span>

                <span class="sidebar-link-text">
                    Accounts
                </span>

            </a>


            <!-- EXPENSES -->

            <a
                href="index.php?url=expenses"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'expenses'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    −
                </span>

                <span class="sidebar-link-text">
                    Expenses
                </span>

            </a>


            <!-- ==========================================================
                 REPORTS
            =========================================================== -->

            <div class="sidebar-section">

                <span>
                    Reports
                </span>

            </div>


            <!-- CATEGORIES -->

            <a
                href="index.php?url=categories"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'categories'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ◫
                </span>

                <span class="sidebar-link-text">
                    Categories
                </span>

            </a>


            <!-- REPORTS -->

            <a
                href="index.php?url=reports"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'reports'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ▥
                </span>

                <span class="sidebar-link-text">
                    Reports
                </span>

            </a>


            <!-- ==========================================================
                 MANAGEMENT
            =========================================================== -->

            <?php if ($isBusinessAdmin): ?>


                <div class="sidebar-section">

                    <span>
                        Management
                    </span>

                </div>


                <!-- BUSINESS USERS -->

                <a
                    href="index.php?url=business-users"
                    class="sidebar-link
                        <?= sidebarActive(
                            $currentUrl,
                            'business-users'
                        ) ?>"
                >

                    <span class="sidebar-icon">
                        ◉
                    </span>

                    <span class="sidebar-link-text">
                        Users
                    </span>

                </a>


                <!-- BUSINESS SETTINGS -->

                <a
                    href="index.php?url=business-settings"
                    class="sidebar-link
                        <?= sidebarActive(
                            $currentUrl,
                            'business-settings'
                        ) ?>"
                >

                    <span class="sidebar-icon">
                        ⚙
                    </span>

                    <span class="sidebar-link-text">
                        Business Settings
                    </span>

                </a>


            <?php endif; ?>


            <!-- ==========================================================
                 SYSTEM
            =========================================================== -->

            <div class="sidebar-section">

                <span>
                    System
                </span>

            </div>


            <!-- SETTINGS -->

            <a
                href="index.php?url=settings"
                class="sidebar-link
                    <?= sidebarActive(
                        $currentUrl,
                        'settings'
                    ) ?>"
            >

                <span class="sidebar-icon">
                    ⚙
                </span>

                <span class="sidebar-link-text">
                    Settings
                </span>

            </a>


        <?php endif; ?>


    </nav>


    <!-- ==================================================================
         FOOTER
    =================================================================== -->

    <div class="sidebar-footer">


        <!-- USER PROFILE -->

        <div class="sidebar-user">


            <div class="sidebar-avatar">

                <?= htmlspecialchars(
                    $avatarLetter,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>


            <div class="sidebar-user-info">


                <div class="sidebar-user-name">

                    <?= htmlspecialchars(
                        $displayName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>


                <div class="sidebar-user-role">

                    <?= htmlspecialchars(
                        $displayRole,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>


            </div>

        </div>


        <!-- LOGOUT -->

        <a
            href="index.php?url=auth/logout"
            class="sidebar-logout"
        >

            <span class="sidebar-logout-icon">
                ↪
            </span>

            <span class="sidebar-logout-text">
                Logout
            </span>

        </a>


    </div>


</aside>


<style>

/*
|--------------------------------------------------------------------------
| SIDEBAR
|--------------------------------------------------------------------------
*/

.sidebar {

    position: fixed;

    top: 0;

    left: 0;

    width: 255px;

    height: 100vh;

    display: flex;

    flex-direction: column;

    background: #ffffff;

    border-right:
        1px solid #e5e7eb;

    /*
    IMPORTANT:
    Sidebar must be ABOVE the overlay.
    */

    z-index: 1100;

    overflow: hidden;
}


/*
|--------------------------------------------------------------------------
| BRAND
|--------------------------------------------------------------------------
*/

.sidebar-brand {

    height: 72px;

    padding:
        0 20px;

    display: flex;

    align-items: center;

    gap: 12px;

    border-bottom:
        1px solid #f1f5f9;

    flex-shrink: 0;
}


.sidebar-brand-icon {

    width: 38px;

    height: 38px;

    min-width: 38px;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

    border-radius: 10px;

    background: #111827;

    color: #ffffff;

    font-size: 18px;

    font-weight: 800;
}


/*
|--------------------------------------------------------------------------
| DYNAMIC LOGO
|--------------------------------------------------------------------------
*/

.sidebar-logo-image {

    display: block;

    width: 100%;

    height: 100%;

    object-fit: contain;

    border-radius: 10px;

    background: #ffffff;
}


.sidebar-default-logo {

    display: flex;

    align-items: center;

    justify-content: center;

    width: 100%;

    height: 100%;

    color: #ffffff;

    font-size: 18px;

    font-weight: 800;
}


.sidebar-brand-content {

    min-width: 0;

    flex: 1;
}


.sidebar-brand-title {

    color: #111827;

    font-size: 14px;

    font-weight: 800;

    line-height: 1.2;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


.sidebar-brand-subtitle {

    margin-top: 3px;

    color: #9ca3af;

    font-size: 10px;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: .08em;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/*
|--------------------------------------------------------------------------
| BUSINESS
|--------------------------------------------------------------------------
*/

.sidebar-business {

    padding:
        14px 14px 8px;
}


.sidebar-business-card {

    display: flex;

    align-items: center;

    gap: 10px;

    padding:
        11px 12px;

    background: #f8fafc;

    border:
        1px solid #eef2f7;

    border-radius: 10px;
}


.sidebar-business-icon {

    width: 32px;

    height: 32px;

    flex-shrink: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 8px;

    background: #ffffff;

    border:
        1px solid #e5e7eb;

    color: #374151;

    font-size: 14px;
}


.sidebar-business-content {

    min-width: 0;
}


.sidebar-business-label {

    color: #9ca3af;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: .08em;
}


.sidebar-business-name {

    margin-top: 2px;

    color: #111827;

    font-size: 12px;

    font-weight: 700;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;
}


/*
|--------------------------------------------------------------------------
| NAVIGATION
|--------------------------------------------------------------------------
*/

.sidebar-nav {

    flex: 1;

    min-height: 0;

    padding:
        10px 12px 18px;

    overflow-y: auto;

    overflow-x: hidden;
}


.sidebar-nav::-webkit-scrollbar {

    width: 5px;
}


.sidebar-nav::-webkit-scrollbar-thumb {

    background: #d1d5db;

    border-radius: 999px;
}


/*
|--------------------------------------------------------------------------
| SECTION
|--------------------------------------------------------------------------
*/

.sidebar-section {

    padding:
        15px 10px 7px;

    color: #9ca3af;

    font-size: 9px;

    font-weight: 800;

    text-transform: uppercase;

    letter-spacing: .09em;
}


.sidebar-section:first-child {

    padding-top: 8px;
}


/*
|--------------------------------------------------------------------------
| LINK
|--------------------------------------------------------------------------
*/

.sidebar-link {

    position: relative;

    display: flex;

    align-items: center;

    gap: 11px;

    width: 100%;

    min-height: 42px;

    margin:
        2px 0;

    padding:
        0 11px;

    border-radius: 9px;

    color: #6b7280;

    text-decoration: none;

    font-size: 13px;

    font-weight: 600;

    transition:
        background .15s ease,
        color .15s ease;
}


.sidebar-link:hover {

    background: #f8fafc;

    color: #111827;
}


.sidebar-link.active {

    background: #f3f4f6;

    color: #111827;
}


.sidebar-link.active::before {

    content: "";

    position: absolute;

    left: 0;

    top: 9px;

    bottom: 9px;

    width: 3px;

    border-radius:
        0 4px 4px 0;

    background: #111827;
}


/*
|--------------------------------------------------------------------------
| ICON
|--------------------------------------------------------------------------
*/

.sidebar-icon {

    width: 22px;

    min-width: 22px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #9ca3af;

    font-size: 15px;

    line-height: 1;
}


.sidebar-link:hover .sidebar-icon,

.sidebar-link.active .sidebar-icon {

    color: #111827;
}


.sidebar-link-text {

    flex: 1;
}


/*
|--------------------------------------------------------------------------
| FOOTER
|--------------------------------------------------------------------------
*/

.sidebar-footer {

    padding:
        12px;

    border-top:
        1px solid #f1f5f9;

    background: #ffffff;

    flex-shrink: 0;
}


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

.sidebar-user {

    display: flex;

    align-items: center;

    gap: 10px;

    padding:
        8px 6px 12px;
}


.sidebar-avatar {

    width: 36px;

    height: 36px;

    min-width: 36px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 10px;

    background: #f3f4f6;

    color: #374151;

    font-size: 13px;

    font-weight: 800;

    text-transform: uppercase;
}


.sidebar-user-info {

    min-width: 0;

    flex: 1;
}


.sidebar-user-name {

    color: #111827;

    font-size: 12px;

    font-weight: 700;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;
}


.sidebar-user-role {

    margin-top: 3px;

    color: #9ca3af;

    font-size: 10px;

    font-weight: 600;

    text-transform: capitalize;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;
}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

.sidebar-logout {

    display: flex;

    align-items: center;

    gap: 10px;

    width: 100%;

    height: 39px;

    padding:
        0 10px;

    border-radius: 8px;

    color: #6b7280;

    text-decoration: none;

    font-size: 12px;

    font-weight: 600;

    transition:
        background .15s ease,
        color .15s ease;
}


.sidebar-logout:hover {

    background: #fef2f2;

    color: #dc2626;
}


.sidebar-logout-icon {

    width: 22px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 15px;
}


.sidebar-logout-text {

    flex: 1;
}


/*
|--------------------------------------------------------------------------
| MOBILE SIDEBAR TOGGLE
|--------------------------------------------------------------------------
*/

.mobile-sidebar-toggle {

    display: none;

    position: fixed;

    top: 14px;

    left: 14px;

    width: 42px;

    height: 42px;

    padding: 0;

    border:
        1px solid #e5e7eb;

    border-radius: 10px;

    background: #ffffff;

    box-shadow:
        0 2px 8px
        rgba(0, 0, 0, 0.08);

    /*
    Toggle must be above everything.
    */

    z-index: 1200;

    align-items: center;

    justify-content: center;

    flex-direction: column;

    gap: 4px;

    cursor: pointer;

    -webkit-tap-highlight-color: transparent;
}


.mobile-sidebar-toggle span {

    display: block;

    width: 18px;

    height: 2px;

    background: #111827;

    border-radius: 999px;

    transition:
        transform .2s ease,
        opacity .2s ease;
}


/*
|--------------------------------------------------------------------------
| MOBILE TOGGLE ACTIVE
|--------------------------------------------------------------------------
*/

.mobile-sidebar-toggle.active
span:nth-child(1) {

    transform:
        translateY(6px)
        rotate(45deg);
}


.mobile-sidebar-toggle.active
span:nth-child(2) {

    opacity: 0;
}


.mobile-sidebar-toggle.active
span:nth-child(3) {

    transform:
        translateY(-6px)
        rotate(-45deg);
}


/*
|--------------------------------------------------------------------------
| MOBILE OVERLAY
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| Overlay is BELOW sidebar.
|
*/

.sidebar-overlay {

    display: none;

    position: fixed;

    inset: 0;

    background:
        rgba(0, 0, 0, 0.35);

    /*
    BELOW SIDEBAR
    */

    z-index: 1000;

    cursor: pointer;
}


.sidebar-overlay.active {

    display: block;
}


/*
|--------------------------------------------------------------------------
| RESPONSIVE
|--------------------------------------------------------------------------
*/

@media (max-width: 900px) {

    .sidebar {

        width: 230px;

    }

}


@media (max-width: 700px) {


    /*
    |--------------------------------------------------------------------------
    | MOBILE TOGGLE
    |--------------------------------------------------------------------------
    */

    .mobile-sidebar-toggle {

        display: flex;

    }


    /*
    |--------------------------------------------------------------------------
    | SIDEBAR
    |--------------------------------------------------------------------------
    */

    .sidebar {

        width: 250px;

        /*
        Start hidden.
        */

        transform:
            translateX(-100%);

        transition:
            transform .25s ease;

        /*
        Keep sidebar ABOVE overlay.
        */

        z-index: 1100;

        box-shadow:
            4px 0 20px
            rgba(0, 0, 0, 0.12);

        /*
        Prevent accidental horizontal overflow.
        */

        max-width: 85vw;

    }


    /*
    |--------------------------------------------------------------------------
    | OPEN SIDEBAR
    |--------------------------------------------------------------------------
    */

    .sidebar.open {

        transform:
            translateX(0);

    }


    /*
    |--------------------------------------------------------------------------
    | MOBILE BODY
    |--------------------------------------------------------------------------
    */

    body.sidebar-mobile-open {

        overflow: hidden;

        /*
        Prevent touch scrolling behind sidebar.
        */

        touch-action: none;

    }

}


/*
|--------------------------------------------------------------------------
| SMALL MOBILE DEVICES
|--------------------------------------------------------------------------
*/

@media (max-width: 400px) {

    .sidebar {

        width: 250px;

        max-width: 85vw;

    }


    .mobile-sidebar-toggle {

        top: 12px;

        left: 12px;

        width: 40px;

        height: 40px;

    }

}

</style>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const sidebar =
            document.getElementById(
                'sidebar'
            );


        const toggle =
            document.getElementById(
                'mobileSidebarToggle'
            );


        const overlay =
            document.getElementById(
                'sidebarOverlay'
            );


        /*
        |--------------------------------------------------------------------------
        | SAFETY CHECK
        |--------------------------------------------------------------------------
        */

        if (
            !sidebar ||
            !toggle ||
            !overlay
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | OPEN SIDEBAR
        |--------------------------------------------------------------------------
        */

        function openSidebar() {

            sidebar.classList.add(
                'open'
            );

            overlay.classList.add(
                'active'
            );

            toggle.classList.add(
                'active'
            );

            toggle.setAttribute(
                'aria-expanded',
                'true'
            );

            document.body.classList.add(
                'sidebar-mobile-open'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE SIDEBAR
        |--------------------------------------------------------------------------
        */

        function closeSidebar() {

            sidebar.classList.remove(
                'open'
            );

            overlay.classList.remove(
                'active'
            );

            toggle.classList.remove(
                'active'
            );

            toggle.setAttribute(
                'aria-expanded',
                'false'
            );

            document.body.classList.remove(
                'sidebar-mobile-open'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | TOGGLE SIDEBAR
        |--------------------------------------------------------------------------
        */

        toggle.addEventListener(
            'click',
            function (event) {

                event.preventDefault();

                event.stopPropagation();


                if (
                    sidebar.classList.contains(
                        'open'
                    )
                ) {

                    closeSidebar();

                } else {

                    openSidebar();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLOSE BY CLICKING OVERLAY
        |--------------------------------------------------------------------------
        */

        overlay.addEventListener(
            'click',
            function () {

                closeSidebar();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SIDEBAR LINKS
        |--------------------------------------------------------------------------
        |
        | Clicking a menu item should navigate normally.
        | We only close the mobile sidebar before navigation.
        |
        */

        sidebar
            .querySelectorAll(
                '.sidebar-link'
            )
            .forEach(
                function (link) {

                    link.addEventListener(
                        'click',
                        function () {

                            if (
                                window.innerWidth <= 700
                            ) {

                                closeSidebar();

                            }

                        }
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | LOGOUT
        |--------------------------------------------------------------------------
        */

        const logout =
            sidebar.querySelector(
                '.sidebar-logout'
            );


        if (logout) {

            logout.addEventListener(
                'click',
                function () {

                    if (
                        window.innerWidth <= 700
                    ) {

                        closeSidebar();

                    }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | ESC KEY
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape'
                    &&
                    sidebar.classList.contains(
                        'open'
                    )
                ) {

                    closeSidebar();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | RESIZE
        |--------------------------------------------------------------------------
        |
        | When switching from mobile back to desktop,
        | remove all mobile-only states.
        |
        */

        window.addEventListener(
            'resize',
            function () {

                if (
                    window.innerWidth > 700
                ) {

                    sidebar.classList.remove(
                        'open'
                    );

                    overlay.classList.remove(
                        'active'
                    );

                    toggle.classList.remove(
                        'active'
                    );

                    toggle.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                    document.body.classList.remove(
                        'sidebar-mobile-open'
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | INITIAL STATE
        |--------------------------------------------------------------------------
        */

        if (
            window.innerWidth > 700
        ) {

            sidebar.classList.remove(
                'open'
            );

            overlay.classList.remove(
                'active'
            );

            toggle.classList.remove(
                'active'
            );

            toggle.setAttribute(
                'aria-expanded',
                'false'
            );

            document.body.classList.remove(
                'sidebar-mobile-open'
            );

        }

    }
);

</script>