<?php

/*
|--------------------------------------------------------------------------
| Reusable Sidebar
|--------------------------------------------------------------------------
|
| Expected variables:
|
| $currentUrl
| $user
| $business
| $tenantRole
|
*/

$currentUrl = $currentUrl ?? ($_GET['url'] ?? '');

$user = $user ?? ($_SESSION['user'] ?? []);

$business = $business ?? ($_SESSION['business'] ?? null);

$tenantRole = $tenantRole ?? ($_SESSION['tenant_role'] ?? null);


/*
|--------------------------------------------------------------------------
| Determine user type
|--------------------------------------------------------------------------
*/

$isSuperAdmin = (
    ($user['role'] ?? '') === 'super_admin'
);

$isBusinessAdmin = (
    in_array(
        $tenantRole,
        ['owner', 'admin'],
        true
    )
);

$isLoanOfficer = (
    $tenantRole === 'loan_officer'
);

$isCashier = (
    $tenantRole === 'cashier'
);

$isStaff = (
    $tenantRole === 'staff'
);

?>

<aside class="sidebar" id="sidebar">


    <!-- =====================================================
         SIDEBAR BRAND
    ====================================================== -->

    <div class="sidebar-brand">

        <div class="sidebar-brand-icon">
            ₱
        </div>


        <div class="sidebar-brand-text">

            <div class="sidebar-brand-title">
                Loan Management
            </div>

            <div class="sidebar-brand-subtitle">
                SaaS Platform
            </div>

        </div>

    </div>


    <!-- =====================================================
         BUSINESS INFORMATION
    ====================================================== -->

    <?php if (!$isSuperAdmin && !empty($business)): ?>

        <div class="sidebar-business">

            <div class="sidebar-business-box">

                <div class="sidebar-business-label">
                    Business
                </div>

                <div class="sidebar-business-name">

                    <?= htmlspecialchars(
                        $business['name'] ?? 'Business'
                    ) ?>

                </div>

            </div>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         NAVIGATION
    ====================================================== -->

    <nav class="sidebar-nav">


        <?php if ($isSuperAdmin): ?>


            <!-- =================================================
                 SUPER ADMIN
            ================================================== -->

            <div class="sidebar-section">
                Main
            </div>


            <a
                href="index.php?url=dashboard"
                class="sidebar-link
                    <?= $currentUrl === 'dashboard'
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ▦
                </span>

                <span class="sidebar-link-text">
                    Dashboard
                </span>

            </a>


            <a
                href="index.php?url=businesses"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'businesses'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ◫
                </span>

                <span class="sidebar-link-text">
                    Businesses
                </span>

            </a>


            <a
                href="index.php?url=users"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'users'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ◉
                </span>

                <span class="sidebar-link-text">
                    Users
                </span>

            </a>


            <!-- BILLING -->

            <div class="sidebar-section">
                Billing
            </div>


            <a
                href="index.php?url=plans"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'plans'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ◆
                </span>

                <span class="sidebar-link-text">
                    Plans
                </span>

            </a>


            <a
                href="index.php?url=subscriptions"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'subscriptions'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ▤
                </span>

                <span class="sidebar-link-text">
                    Subscriptions
                </span>

            </a>


            <!-- SYSTEM -->

            <div class="sidebar-section">
                System
            </div>


            <a
                href="index.php?url=settings"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'settings'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ⚙
                </span>

                <span class="sidebar-link-text">
                    System Settings
                </span>

            </a>


            <a
                href="index.php?url=audit-logs"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'audit-logs'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ≡
                </span>

                <span class="sidebar-link-text">
                    Audit Logs
                </span>

            </a>


        <?php else: ?>


            <!-- =================================================
                 BUSINESS USER
            ================================================== -->

            <div class="sidebar-section">
                Main
            </div>


            <a
                href="index.php?url=dashboard"
                class="sidebar-link
                    <?= $currentUrl === 'dashboard'
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ▦
                </span>

                <span class="sidebar-link-text">
                    Dashboard
                </span>

            </a>


            <!-- =================================================
                 LOANS
            ================================================== -->

            <div class="sidebar-section">
                Lending
            </div>


            <a
                href="index.php?url=borrowers"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'borrowers'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ◉
                </span>

                <span class="sidebar-link-text">
                    Borrowers
                </span>

            </a>


            <a
                href="index.php?url=loans"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'loans'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ₱
                </span>

                <span class="sidebar-link-text">
                    Loans
                </span>

            </a>


            <a
                href="index.php?url=payments"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'payments'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ▤
                </span>

                <span class="sidebar-link-text">
                    Payments
                </span>

            </a>


            


            <a
                href="index.php?url=collections"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'collections'
                    )
                        ? 'active'
                        : '' ?>"
            >

                <span class="sidebar-icon">
                    ◷
                </span>

                <span class="sidebar-link-text">
                    Collections
                </span>

            </a>


          <!-- =================================================
     FINANCE
================================================== -->

<div class="sidebar-section">
    Finance
</div>


<a
    href="index.php?url=accounts"
    class="sidebar-link
        <?= str_starts_with(
            $currentUrl,
            'accounts'
        )
            ? 'active'
            : '' ?>"
>

    <span class="sidebar-icon">
        ◈
    </span>

    <span class="sidebar-link-text">
        Accounts
    </span>

</a>


<a
    href="index.php?url=expenses"
    class="sidebar-link
        <?= str_starts_with(
            $currentUrl,
            'expenses'
        )
            ? 'active'
            : '' ?>"
>

    <span class="sidebar-icon">
        −
    </span>

    <span class="sidebar-link-text">
        Expenses
    </span>

</a>


<!-- =================================================
     REPORTS
================================================== -->

<div class="sidebar-section">
    Reports
</div>


<a
    href="index.php?url=categories"
    class="sidebar-link
        <?= str_starts_with(
            $currentUrl,
            'categories'
        )
            ? 'active'
            : '' ?>"
>

    <span class="sidebar-icon">
        ◫
    </span>

    <span class="sidebar-link-text">
        Categories
    </span>

</a>


<a
    href="index.php?url=reports"
    class="sidebar-link
        <?= str_starts_with(
            $currentUrl,
            'reports'
        )
            ? 'active'
            : '' ?>"
>

    <span class="sidebar-icon">
        ▥
    </span>

    <span class="sidebar-link-text">
        Reports
    </span>

</a>

         


            <!-- =================================================
                 BUSINESS MANAGEMENT
            ================================================== -->

            <?php if ($isBusinessAdmin): ?>

                <div class="sidebar-section">
                    Management
                </div>


             <a
    href="index.php?url=business-users"
    class="sidebar-link
        <?= str_starts_with(
            $currentUrl,
            'business-users'
        )
            ? 'active'
            : '' ?>"
>

    <span class="sidebar-icon">
        ◉
    </span>

    <span class="sidebar-link-text">
        Users
    </span>

</a>

                <a
                    href="index.php?url=business-settings"
                    class="sidebar-link
                        <?= str_starts_with(
                            $currentUrl,
                            'business-settings'
                        )
                            ? 'active'
                            : '' ?>"
                >

                    <span class="sidebar-icon">
                        ⚙
                    </span>

                    <span class="sidebar-link-text">
                        Business Settings
                    </span>

                </a>

            <?php endif; ?>


            <!-- =================================================
                 SYSTEM
            ================================================== -->

            <div class="sidebar-section">
                System
            </div>


            <a
                href="index.php?url=settings"
                class="sidebar-link
                    <?= str_starts_with(
                        $currentUrl,
                        'settings'
                    )
                        ? 'active'
                        : '' ?>"
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


    <!-- =====================================================
         SIDEBAR FOOTER
    ====================================================== -->

    <div class="sidebar-footer">


        <div class="sidebar-user">


            <div class="sidebar-avatar">

                <?= htmlspecialchars(
                    strtoupper(
                        substr(
                            $user['full_name']
                            ?? $user['username']
                            ?? 'A',
                            0,
                            1
                        )
                    )
                ) ?>

            </div>


            <div class="sidebar-user-info">


                <div class="sidebar-user-name">

                    <?= htmlspecialchars(
                        $user['full_name']
                        ?? $user['username']
                        ?? 'Administrator'
                    ) ?>

                </div>


                <div class="sidebar-user-role">

                    <?= htmlspecialchars(
                        $tenantRole
                        ?? $user['role']
                        ?? 'Administrator'
                    ) ?>

                </div>


            </div>


        </div>


        <!-- =================================================
             LOGOUT
        ================================================== -->

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