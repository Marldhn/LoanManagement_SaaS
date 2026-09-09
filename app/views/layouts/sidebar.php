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

$currentUrl = $currentUrl ?? ($_GET['url'] ?? '');

$user = $user ?? ($_SESSION['user'] ?? []);

$business = $business ?? ($_SESSION['business'] ?? null);

$tenantRole = $tenantRole ?? ($_SESSION['tenant_role'] ?? null);

$settings = $settings ?? ($_SESSION['settings'] ?? []);

$isSuperAdmin = ($user['role'] ?? '') === 'super_admin';

$isBusinessAdmin = in_array(
    $tenantRole,
    ['owner', 'admin'],
    true
);

$isLoanOfficer = $tenantRole === 'loan_officer';
$isCashier = $tenantRole === 'cashier';
$isStaff = $tenantRole === 'staff';

function sidebarActive(
    string $currentUrl,
    string $route
): string {
    return (
        $currentUrl === $route ||
        str_starts_with($currentUrl, $route . '/')
    )
        ? 'active'
        : '';
}

$displayName = trim(
    $user['full_name'] ??
    $user['username'] ??
    'Administrator'
);

if ($displayName === '') {
    $displayName = 'Administrator';
}

$avatarLetter = strtoupper(
    substr($displayName, 0, 1)
);

$displayRole = $tenantRole ?? $user['role'] ?? 'Administrator';

$displayRole = ucwords(
    str_replace('_', ' ', $displayRole)
);

$sidebarBrandName = trim(
    $settings['business_name'] ??
    $settings['company_name'] ??
    $settings['system_name'] ??
    $business['name'] ??
    'Loan Management'
);

if ($sidebarBrandName === '') {
    $sidebarBrandName = 'Loan Management';
}

$sidebarBrandTagline = trim(
    $settings['business_tagline'] ??
    $settings['tagline'] ??
    $settings['system_tagline'] ??
    'SaaS Platform'
);

if ($sidebarBrandTagline === '') {
    $sidebarBrandTagline = 'SaaS Platform';
}

$sidebarLogo = trim(
    $settings['sidebar_logo'] ?? ''
);

$sidebarLogoUrl = '';

if ($sidebarLogo !== '') {

    if (
        str_starts_with($sidebarLogo, 'http://') ||
        str_starts_with($sidebarLogo, 'https://')
    ) {
        $sidebarLogoUrl = $sidebarLogo;
    } else {

        $sidebarLogo = ltrim($sidebarLogo, '/');

        $sidebarLogoUrl = $sidebarLogo;
    }
}

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

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>

<aside
    class="sidebar"
    id="sidebar"
>

    <div class="sidebar-brand">

        <div class="sidebar-brand-icon">

            <?php if ($sidebarLogoUrl !== ''): ?>

                <img
                    src="<?= htmlspecialchars(
                        $sidebarLogoUrl,
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

    <?php if (!$isSuperAdmin && !empty($business)): ?>

        <div class="sidebar-business">

            <div class="sidebar-business-card">

                <div class="sidebar-business-icon">
                    ◈
                </div>

                <div class="sidebar-business-content">

                    <div class="sidebar-business-label">
                        Business
                    </div>

                    <div class="sidebar-business-name">
                        <?= htmlspecialchars(
                            $business['name'] ?? 'Business',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                </div>

            </div>

        </div>

    <?php endif; ?>

    <nav class="sidebar-nav">

        <?php if ($isSuperAdmin): ?>

            <div class="sidebar-section">
                <span>Main</span>
            </div>

            <a
                href="index.php?url=dashboard"
                class="sidebar-link <?= sidebarActive(
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

            <a
                href="index.php?url=businesses"
                class="sidebar-link <?= sidebarActive(
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

            <a
                href="index.php?url=users"
                class="sidebar-link <?= sidebarActive(
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

            <div class="sidebar-section">
                <span>Billing</span>
            </div>

            <a
                href="index.php?url=plans"
                class="sidebar-link <?= sidebarActive(
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

            <a
                href="index.php?url=subscriptions"
                class="sidebar-link <?= sidebarActive(
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

            <div class="sidebar-section">
                <span>System</span>
            </div>

            <a
                href="index.php?url=settings"
                class="sidebar-link <?= sidebarActive(
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

            <a
                href="index.php?url=audit-logs"
                class="sidebar-link <?= sidebarActive(
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

            <div class="sidebar-section">
                <span>Main</span>
            </div>

            <a
                href="index.php?url=dashboard"
                class="sidebar-link <?= sidebarActive(
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

            <div class="sidebar-section">
                <span>Lending</span>
            </div>

            <a
                href="index.php?url=borrowers"
                class="sidebar-link <?= sidebarActive(
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

            <a
                href="index.php?url=loans"
                class="sidebar-link <?= sidebarActive(
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

            <a
                href="index.php?url=payments"
                class="sidebar-link <?= sidebarActive(
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

            <a
                href="index.php?url=collections"
                class="sidebar-link <?= sidebarActive(
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

            <!-- PENALTIES -->
            <a
                href="index.php?url=penalties"
                class="sidebar-link <?= sidebarActive(
                    $currentUrl,
                    'penalties'
                ) ?>"
            >
                <span class="sidebar-icon">
                    ⚠
                </span>

                <span class="sidebar-link-text">
                    Penalties
                </span>
            </a>

            <div class="sidebar-section">
                <span>Finance</span>
            </div>

            <a
                href="index.php?url=accounts"
                class="sidebar-link <?= sidebarActive(
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

            <a
                href="index.php?url=expenses"
                class="sidebar-link <?= sidebarActive(
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

            <div class="sidebar-section">
                <span>Reports</span>
            </div>

            <a
                href="index.php?url=categories"
                class="sidebar-link <?= sidebarActive(
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

            <a
                href="index.php?url=reports"
                class="sidebar-link <?= sidebarActive(
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

            <?php if ($isBusinessAdmin): ?>

                <div class="sidebar-section">
                    <span>Management</span>
                </div>

                <a
                    href="index.php?url=business-users"
                    class="sidebar-link <?= sidebarActive(
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

        
                            <a
                href="index.php?url=settings"
                class="sidebar-link <?= sidebarActive(
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
        <?php endif; ?>

    </nav>

    <div class="sidebar-footer">

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
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Public+Sans:wght@400;500;600;700;800&display=swap');

:root{
    --sb-ink-950:#0E1512;
    --sb-ink-900:#152220;
    --sb-ink-850:#1B2926;
    --sb-ink-800:#22322E;
    --sb-line:rgba(243,241,234,.08);
    --sb-line-soft:rgba(243,241,234,.05);
    --sb-brass:#C9A227;
    --sb-brass-dim:#A5851F;
    --sb-brass-soft:rgba(201,162,39,.14);
    --sb-text:#F3F1EA;
    --sb-text-soft:#A9B4AC;
    --sb-text-faint:#6F7A73;
    --sb-danger:#C0473A;
    --sb-danger-soft:rgba(192,71,58,.14);
    --sb-font-serif:'Fraunces',Georgia,'Iowan Old Style',serif;
    --sb-font-sans:'Public Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
}

.sidebar{
    position:fixed;
    top:0;
    left:0;
    width:264px;
    height:100vh;
    display:flex;
    flex-direction:column;
    background:var(--sb-ink-950);
    background-image:radial-gradient(120% 60% at 0% 0%,var(--sb-ink-900) 0%,var(--sb-ink-950) 55%);
    border-right:1px solid var(--sb-line);
    z-index:1100;
    overflow:hidden;
    font-family:var(--sb-font-sans);
}

.sidebar-brand{
    height:76px;
    padding:0 20px;
    display:flex;
    align-items:center;
    gap:13px;
    border-bottom:1px solid var(--sb-line);
    flex-shrink:0;
}

.sidebar-brand-icon{
    width:40px;
    height:40px;
    min-width:40px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
    border-radius:9px;
    background:var(--sb-brass);
    color:var(--sb-ink-950);
    font-size:18px;
    font-weight:700;
    box-shadow:0 4px 14px rgba(201,162,39,.25);
}

.sidebar-logo-image{
    display:block;
    width:100%;
    height:100%;
    object-fit:contain;
    border-radius:9px;
    background:#fff;
}

.sidebar-default-logo{
    display:flex;
    align-items:center;
    justify-content:center;
    width:100%;
    height:100%;
    color:var(--sb-ink-950);
    font-size:19px;
    font-weight:700;
}

.sidebar-brand-content{
    min-width:0;
    flex:1;
}

.sidebar-brand-title{
    color:var(--sb-text);
    font-family:var(--sb-font-serif);
    font-size:16px;
    font-weight:600;
    line-height:1.25;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    letter-spacing:.01em;
}

.sidebar-brand-subtitle{
    margin-top:3px;
    color:var(--sb-text-faint);
    font-size:11px;
    font-weight:500;
    letter-spacing:.02em;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.sidebar-business{
    padding:16px 14px 6px;
}

.sidebar-business-card{
    display:flex;
    align-items:center;
    gap:10px;
    padding:11px 12px;
    background:var(--sb-ink-850);
    border:1px solid var(--sb-line);
    border-radius:10px;
}

.sidebar-business-icon{
    width:32px;
    height:32px;
    flex-shrink:0;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    background:var(--sb-brass-soft);
    border:1px solid rgba(201,162,39,.25);
    color:var(--sb-brass);
    font-size:14px;
}

.sidebar-business-content{
    min-width:0;
}

.sidebar-business-label{
    color:var(--sb-text-faint);
    font-size:10px;
    font-weight:600;
    letter-spacing:.03em;
}

.sidebar-business-name{
    margin-top:2px;
    color:var(--sb-text);
    font-size:12.5px;
    font-weight:600;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.sidebar-nav{
    flex:1;
    min-height:0;
    padding:8px 12px 18px;
    overflow-y:auto;
    overflow-x:hidden;
}

.sidebar-nav::-webkit-scrollbar{
    width:5px;
}

.sidebar-nav::-webkit-scrollbar-thumb{
    background:var(--sb-ink-800);
    border-radius:999px;
}

.sidebar-section{
    padding:16px 11px 6px;
    color:var(--sb-text-faint);
    font-size:11px;
    font-weight:600;
    letter-spacing:.01em;
}

.sidebar-section:first-child{
    padding-top:8px;
}

.sidebar-link{
    position:relative;
    display:flex;
    align-items:center;
    gap:11px;
    width:100%;
    min-height:41px;
    margin:1px 0;
    padding:0 11px;
    border-radius:8px;
    color:var(--sb-text-soft);
    text-decoration:none;
    font-size:13.5px;
    font-weight:500;
    transition:background .15s ease,color .15s ease;
}

.sidebar-link:hover{
    background:var(--sb-ink-850);
    color:var(--sb-text);
}

.sidebar-link:focus-visible,
.mobile-sidebar-toggle:focus-visible,
.sidebar-logout:focus-visible{
    outline:2px solid var(--sb-brass);
    outline-offset:2px;
}

.sidebar-link.active{
    background:var(--sb-brass-soft);
    color:var(--sb-text);
    font-weight:600;
}

.sidebar-link.active::before{
    content:"";
    position:absolute;
    left:0;
    top:9px;
    bottom:9px;
    width:3px;
    border-radius:0 4px 4px 0;
    background:var(--sb-brass);
}

.sidebar-icon{
    width:22px;
    min-width:22px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--sb-text-faint);
    font-size:15px;
    line-height:1;
}

.sidebar-link:hover .sidebar-icon{
    color:var(--sb-text-soft);
}

.sidebar-link.active .sidebar-icon{
    color:var(--sb-brass);
}

.sidebar-link-text{
    flex:1;
}

.sidebar-footer{
    padding:12px;
    border-top:1px solid var(--sb-line);
    background:var(--sb-ink-950);
    flex-shrink:0;
}

.sidebar-user{
    display:flex;
    align-items:center;
    gap:10px;
    padding:8px 6px 12px;
}

.sidebar-avatar{
    width:36px;
    height:36px;
    min-width:36px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:9px;
    background:var(--sb-ink-850);
    border:1px solid rgba(201,162,39,.3);
    color:var(--sb-brass);
    font-family:var(--sb-font-serif);
    font-size:14px;
    font-weight:600;
    text-transform:uppercase;
}

.sidebar-user-info{
    min-width:0;
    flex:1;
}

.sidebar-user-name{
    color:var(--sb-text);
    font-size:12.5px;
    font-weight:600;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.sidebar-user-role{
    margin-top:3px;
    color:var(--sb-text-faint);
    font-size:10.5px;
    font-weight:500;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.sidebar-logout{
    display:flex;
    align-items:center;
    gap:10px;
    width:100%;
    height:38px;
    padding:0 10px;
    border-radius:8px;
    color:var(--sb-text-soft);
    text-decoration:none;
    font-size:12.5px;
    font-weight:500;
    transition:background .15s ease,color .15s ease;
}

.sidebar-logout:hover{
    background:var(--sb-danger-soft);
    color:#E08278;
}

.sidebar-logout-icon{
    width:22px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:15px;
}

.sidebar-logout-text{
    flex:1;
}

.mobile-sidebar-toggle{
    display:none;
    position:fixed;
    top:14px;
    left:14px;
    width:42px;
    height:42px;
    padding:0;
    border:1px solid var(--sb-line);
    border-radius:10px;
    background:var(--sb-ink-950);
    box-shadow:0 4px 14px rgba(0,0,0,.25);
    z-index:1200;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    gap:4px;
    cursor:pointer;
    -webkit-tap-highlight-color:transparent;
}

.mobile-sidebar-toggle span{
    display:block;
    width:18px;
    height:2px;
    background:var(--sb-text);
    border-radius:999px;
    transition:transform .2s ease,opacity .2s ease;
}

.mobile-sidebar-toggle.active span:nth-child(1){
    transform:translateY(6px) rotate(45deg);
}

.mobile-sidebar-toggle.active span:nth-child(2){
    opacity:0;
}

.mobile-sidebar-toggle.active span:nth-child(3){
    transform:translateY(-6px) rotate(-45deg);
}

.sidebar-overlay{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(6,10,8,.55);
    backdrop-filter:blur(1px);
    z-index:1000;
    cursor:pointer;
}

.sidebar-overlay.active{
    display:block;
}

@media (max-width:900px){
    .sidebar{
        width:240px;
    }
}

@media (max-width:700px){

    .mobile-sidebar-toggle{
        display:flex;
    }

    .sidebar{
        width:258px;
        transform:translateX(-100%);
        transition:transform .25s ease;
        z-index:1100;
        box-shadow:4px 0 24px rgba(0,0,0,.3);
        max-width:85vw;
    }

    .sidebar.open{
        transform:translateX(0);
    }

    body.sidebar-mobile-open{
        overflow:hidden;
        touch-action:none;
    }
}

@media (max-width:400px){

    .sidebar{
        width:250px;
        max-width:85vw;
    }

    .mobile-sidebar-toggle{
        top:12px;
        left:12px;
        width:40px;
        height:40px;
    }
}

@media (prefers-reduced-motion:reduce){
    .sidebar,
    .sidebar-link,
    .sidebar-logout,
    .mobile-sidebar-toggle span{
        transition:none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded',function(){

    const sidebar=document.getElementById('sidebar');
    const toggle=document.getElementById('mobileSidebarToggle');
    const overlay=document.getElementById('sidebarOverlay');

    if(!sidebar||!toggle||!overlay){
        return;
    }

    function openSidebar(){
        sidebar.classList.add('open');
        overlay.classList.add('active');
        toggle.classList.add('active');
        toggle.setAttribute('aria-expanded','true');
        document.body.classList.add('sidebar-mobile-open');
    }

    function closeSidebar(){
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        toggle.classList.remove('active');
        toggle.setAttribute('aria-expanded','false');
        document.body.classList.remove('sidebar-mobile-open');
    }

    toggle.addEventListener('click',function(event){
        event.preventDefault();
        event.stopPropagation();

        if(sidebar.classList.contains('open')){
            closeSidebar();
        }else{
            openSidebar();
        }
    });

    overlay.addEventListener('click',function(){
        closeSidebar();
    });

    sidebar.querySelectorAll('.sidebar-link').forEach(function(link){
        link.addEventListener('click',function(){
            if(window.innerWidth<=700){
                closeSidebar();
            }
        });
    });

    const logout=sidebar.querySelector('.sidebar-logout');

    if(logout){
        logout.addEventListener('click',function(){
            if(window.innerWidth<=700){
                closeSidebar();
            }
        });
    }

    document.addEventListener('keydown',function(event){
        if(
            event.key==='Escape' &&
            sidebar.classList.contains('open')
        ){
            closeSidebar();
        }
    });

    window.addEventListener('resize',function(){

        if(window.innerWidth>700){

            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            toggle.classList.remove('active');
            toggle.setAttribute('aria-expanded','false');
            document.body.classList.remove('sidebar-mobile-open');
        }
    });

    if(window.innerWidth>700){

        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        toggle.classList.remove('active');
        toggle.setAttribute('aria-expanded','false');
        document.body.classList.remove('sidebar-mobile-open');
    }

});
</script>