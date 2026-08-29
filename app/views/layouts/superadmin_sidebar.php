<?php

/*
|--------------------------------------------------------------------------
| SUPER ADMIN SIDEBAR
|--------------------------------------------------------------------------
|
| Separate sidebar for Super Admin pages.
| Keeps the same general layout structure as the tenant sidebar.
|
*/

$currentUrl = $_GET['url'] ?? '';

$currentUrl = trim(
    strtolower(
        strtok($currentUrl, '&')
    ),
    '/'
);


/*
|--------------------------------------------------------------------------
| ACTIVE MENU HELPER
|--------------------------------------------------------------------------
*/

function super_adminActive(
    string $route,
    string $currentUrl
): string {

    $route = trim(
        strtolower($route),
        '/'
    );

    return $currentUrl === $route
        ? 'active'
        : '';
}


/*
|--------------------------------------------------------------------------
| ACTIVE SECTION HELPER
|--------------------------------------------------------------------------
*/

function super_adminContains(
    array $routes,
    string $currentUrl
): string {

    foreach ($routes as $route) {

        $route = trim(
            strtolower($route),
            '/'
        );

        if (
            $currentUrl === $route ||
            str_starts_with(
                $currentUrl,
                $route . '/'
            )
        ) {
            return 'active';
        }
    }

    return '';
}

?>


<!--
|--------------------------------------------------------------------------
| SUPER ADMIN SIDEBAR
|--------------------------------------------------------------------------
-->

<aside
    class="sidebar super_admin-sidebar"
    id="super_adminSidebar"
>

    <!--
    |--------------------------------------------------------------------------
    | LOGO / BRAND
    |--------------------------------------------------------------------------
    -->

    <div class="sidebar-header">

        <a
                href="index.php?url=super_admin/dashboard"

            class="sidebar-brand"
        >

            <div class="sidebar-brand-icon">
                K
            </div>

            <div class="sidebar-brand-text">

                <span class="sidebar-brand-name">
                    KwentaLoan
                </span>

                <span class="sidebar-brand-subtitle">
                    Super Admin
                </span>

            </div>

        </a>

    </div>


    <!--
    |--------------------------------------------------------------------------
    | NAVIGATION
    |--------------------------------------------------------------------------
    -->

    <nav class="sidebar-nav">


        <!--
        |--------------------------------------------------------------------------
        | OVERVIEW
        |--------------------------------------------------------------------------
        -->

        <div class="sidebar-section-title">
            Overview
        </div>


        <a
            href="index.php?url=super_admin/dashboard"
            class="
                sidebar-link
                <?= super_adminActive(
                    'super_admin/dashboard',
                    $currentUrl
                ) ?>
            "
        >

            <span class="sidebar-link-icon">
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect
                        x="3"
                        y="3"
                        width="7"
                        height="7"
                    ></rect>

                    <rect
                        x="14"
                        y="3"
                        width="7"
                        height="7"
                    ></rect>

                    <rect
                        x="3"
                        y="14"
                        width="7"
                        height="7"
                    ></rect>

                    <rect
                        x="14"
                        y="14"
                        width="7"
                        height="7"
                    ></rect>
                </svg>
            </span>

            <span class="sidebar-link-text">
                Dashboard
            </span>

        </a>


        <!--
        |--------------------------------------------------------------------------
        | MANAGEMENT
        |--------------------------------------------------------------------------
        -->

        <div class="sidebar-section-title">
            Management
        </div>


        <!-- BUSINESSES -->

        <a
            href="index.php?url=super_admin/businesses"
            class="
                sidebar-link
                <?= super_adminContains(
                    [
                        'super_admin/businesses',
                        'super_admin/businesses/create',
                        'super_admin/businesses/edit'
                    ],
                    $currentUrl
                ) ?>
            "
        >

            <span class="sidebar-link-icon">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path
                        d="M3 21h18"
                    ></path>

                    <path
                        d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"
                    ></path>

                    <path
                        d="M9 7h1"
                    ></path>

                    <path
                        d="M14 7h1"
                    ></path>

                    <path
                        d="M9 11h1"
                    ></path>

                    <path
                        d="M14 11h1"
                    ></path>

                    <path
                        d="M9 15h1"
                    ></path>

                    <path
                        d="M14 15h1"
                    ></path>

                </svg>

            </span>

            <span class="sidebar-link-text">
                Businesses
            </span>

        </a>


        <!-- USERS -->

        <a
            href="index.php?url=super_admin/users"
            class="
                sidebar-link
                <?= super_adminContains(
                    [
                        'super_admin/users',
                        'super_admin/users/create',
                        'super_admin/users/edit'
                    ],
                    $currentUrl
                ) ?>
            "
        >

            <span class="sidebar-link-icon">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path
                        d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                    ></path>

                    <circle
                        cx="9"
                        cy="7"
                        r="4"
                    ></circle>

                    <path
                        d="M22 21v-2a4 4 0 0 0-3-3.87"
                    ></path>

                    <path
                        d="M16 3.13a4 4 0 0 1 0 7.75"
                    ></path>

                </svg>

            </span>

            <span class="sidebar-link-text">
                Users
            </span>

        </a>


        <!--
        |--------------------------------------------------------------------------
        | SUBSCRIPTIONS
        |--------------------------------------------------------------------------
        -->

        <a
            href="index.php?url=super_admin/subscriptions"
            class="
                sidebar-link
                <?= super_adminContains(
                    [
                        'super_admin/subscriptions',
                        'super_admin/subscriptions/create',
                        'super_admin/subscriptions/edit'
                    ],
                    $currentUrl
                ) ?>
            "
        >

            <span class="sidebar-link-icon">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <rect
                        x="2"
                        y="5"
                        width="20"
                        height="14"
                        rx="2"
                    ></rect>

                    <line
                        x1="2"
                        y1="10"
                        x2="22"
                        y2="10"
                    ></line>

                </svg>

            </span>

            <span class="sidebar-link-text">
                Subscriptions
            </span>

        </a>


        <!--
        |--------------------------------------------------------------------------
        | PLANS
        |--------------------------------------------------------------------------
        -->

        <a
            href="index.php?url=super_admin/plans"
            class="
                sidebar-link
                <?= super_adminContains(
                    [
                        'super_admin/plans',
                        'super_admin/plans/create',
                        'super_admin/plans/edit'
                    ],
                    $currentUrl
                ) ?>
            "
        >

            <span class="sidebar-link-icon">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path
                        d="M20 12V8H4v4"
                    ></path>

                    <path
                        d="M4 8l8-5 8 5"
                    ></path>

                    <path
                        d="M4 12v8h16v-8"
                    ></path>

                    <path
                        d="M9 16h6"
                    ></path>

                </svg>

            </span>

            <span class="sidebar-link-text">
                Plans
            </span>

        </a>


        <!--
        |--------------------------------------------------------------------------
        | SYSTEM
        |--------------------------------------------------------------------------
        -->

        <div class="sidebar-section-title">
            System
        </div>


        <!-- SYSTEM SETTINGS -->

        <a
            href="index.php?url=super_admin/settings"
            class="
                sidebar-link
                <?= super_adminContains(
                    [
                        'super_admin/settings'
                    ],
                    $currentUrl
                ) ?>
            "
        >

            <span class="sidebar-link-icon">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <circle
                        cx="12"
                        cy="12"
                        r="3"
                    ></circle>

                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09A1.65 1.65 0 0 0 15 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.14.58.66 1 1.27 1H21a2 2 0 1 1 0 4h-.09c-.61 0-1.13.42-1.27 1z"
                    ></path>

                </svg>

            </span>

            <span class="sidebar-link-text">
                System Settings
            </span>

        </a>


        <!-- ACTIVITY LOGS -->

        <a
            href="index.php?url=super_admin/activity-logs"
            class="
                sidebar-link
                <?= super_adminContains(
                    [
                        'super_admin/activity-logs'
                    ],
                    $currentUrl
                ) ?>
            "
        >

            <span class="sidebar-link-icon">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path
                        d="M3 12h4l3-9 4 18 3-9h4"
                    ></path>

                </svg>

            </span>

            <span class="sidebar-link-text">
                Activity Logs
            </span>

        </a>


    </nav>


    <!--
    |--------------------------------------------------------------------------
    | SIDEBAR FOOTER
    |--------------------------------------------------------------------------
    -->

    <div class="sidebar-footer">

        <a
            href="index.php?url=auth/logout"
            class="sidebar-link sidebar-logout"
        >

            <span class="sidebar-link-icon">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path
                        d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                    ></path>

                    <polyline
                        points="16 17 21 12 16 7"
                    ></polyline>

                    <line
                        x1="21"
                        y1="12"
                        x2="9"
                        y2="12"
                    ></line>

                </svg>

            </span>

            <span class="sidebar-link-text">
                Logout
            </span>

        </a>

    </div>

</aside>


<!--
|--------------------------------------------------------------------------
| SUPER ADMIN SIDEBAR STYLES
|--------------------------------------------------------------------------
-->

<style>

.super_admin-sidebar {
    display: flex;
    flex-direction: column;
}

.super_admin-sidebar .sidebar-nav {
    flex: 1;
    overflow-y: auto;
}

.super_admin-sidebar .sidebar-footer {
    margin-top: auto;
}

.super_admin-sidebar .sidebar-section-title {
    padding: 18px 14px 8px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #9ca3af;
}

.super_admin-sidebar .sidebar-link {
    display: flex;
    align-items: center;
    gap: 11px;
    width: 100%;
    box-sizing: border-box;
    padding: 10px 12px;
    margin-bottom: 3px;
    border-radius: 8px;
    color: #4b5563;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition:
        background .18s ease,
        color .18s ease;
}

.super_admin-sidebar .sidebar-link:hover {
    background: #f3f4f6;
    color: #111827;
}

.super_admin-sidebar .sidebar-link.active {
    background: #111827;
    color: #ffffff;
}

.super_admin-sidebar .sidebar-link.active .sidebar-link-icon {
    color: #ffffff;
}

.super_admin-sidebar .sidebar-link-icon {
    width: 20px;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.super_admin-sidebar .sidebar-link-text {
    white-space: nowrap;
}

.super_admin-sidebar .sidebar-brand {
    display: flex;
    align-items: center;
    gap: 11px;
    text-decoration: none;
    color: inherit;
}

.super_admin-sidebar .sidebar-brand-icon {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #111827;
    color: #ffffff;
    font-size: 18px;
    font-weight: 800;
}

.super_admin-sidebar .sidebar-brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.super_admin-sidebar .sidebar-brand-name {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
}

.super_admin-sidebar .sidebar-brand-subtitle {
    margin-top: 2px;
    font-size: 10px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .05em;
}

.super_admin-sidebar .sidebar-logout {
    color: #6b7280;
}

.super_admin-sidebar .sidebar-logout:hover {
    color: #b91c1c;
    background: #fef2f2;
}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 900px) {

    .super_admin-sidebar .sidebar-link-text,
    .super_admin-sidebar .sidebar-section-title,
    .super_admin-sidebar .sidebar-brand-text {
        display: none;
    }

    .super_admin-sidebar .sidebar-link {
        justify-content: center;
        padding: 11px;
    }

    .super_admin-sidebar .sidebar-brand {
        justify-content: center;
    }

}


/*
|--------------------------------------------------------------------------
| SMALL MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 600px) {

    .super_admin-sidebar {
        width: 64px;
    }

    .super_admin-sidebar .sidebar-header {
        padding: 14px 8px;
    }

    .super_admin-sidebar .sidebar-nav {
        padding: 8px;
    }

    .super_admin-sidebar .sidebar-footer {
        padding: 8px;
    }

}

</style>