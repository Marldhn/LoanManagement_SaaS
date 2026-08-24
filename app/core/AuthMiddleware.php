<?php

class AuthMiddleware
{
    /**
     * Require the user to be logged in.
     */
    public static function requireLogin(): void
    {
        Auth::startSession();

        if (!Auth::check()) {
            header('Location: index.php?url=auth/login');
            exit;
        }
    }

    /**
     * Require a platform super admin.
     */
    public static function requireSuperAdmin(): void
    {
        self::requireLogin();

        if (!Auth::isSuperAdmin()) {
            http_response_code(403);

            echo '403 - Access Denied';
            exit;
        }
    }

    /**
     * Require a business user.
     */
    public static function requireBusinessUser(): void
    {
        self::requireLogin();

        if (Auth::isSuperAdmin()) {
            return;
        }

        if (Auth::businessId() === null) {
            http_response_code(403);

            echo '403 - Business Access Required';
            exit;
        }
    }

    /**
     * Check a tenant role.
     */
    public static function requireRole(array $roles): void
    {
        self::requireBusinessUser();

        if (Auth::isSuperAdmin()) {
            return;
        }

        $role = Auth::tenantRole();

        if (!in_array($role, $roles, true)) {
            http_response_code(403);

            echo '403 - You do not have permission to access this page.';
            exit;
        }
    }
}