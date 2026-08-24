<?php

class Auth
{
    /**
     * Start session safely.
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Attempt to authenticate a user.
     */
    public static function login(string $login, string $password): array
{
    self::startSession();

    $db = Database::getInstance();

    /*
     * Find user by username OR email.
     *
     * IMPORTANT:
     * We use two different parameters because native
     * PDO prepared statements require unique parameter
     * names when emulate prepares is disabled.
     */
    $sql = "
        SELECT
            id,
            username,
            email,
            password,
            full_name,
            role,
            status
        FROM users
        WHERE username = :username
           OR email = :email
        LIMIT 1
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':username' => $login,
        ':email'    => $login
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /*
     * User doesn't exist.
     */
    if (!$user) {
        return [
            'success' => false,
            'message' => 'Invalid username/email or password.'
        ];
    }

    /*
     * Verify password.
     */
    if (!password_verify($password, $user['password'])) {
        return [
            'success' => false,
            'message' => 'Invalid username/email or password.'
        ];
    }

    /*
     * Only approved users can login.
     */
    if ($user['status'] !== 'approved') {
        return [
            'success' => false,
            'message' => 'Your account is not approved.'
        ];
    }

    /*
     * Regenerate session ID after successful login.
     */
    session_regenerate_id(true);

    /*
     * =====================================================
     * SUPER ADMIN
     * =====================================================
     */

    if ($user['role'] === 'super_admin') {

        $_SESSION['authenticated'] = true;

        $_SESSION['user'] = [
            'id'        => (int) $user['id'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'full_name' => $user['full_name'],
            'role'      => $user['role']
        ];

        $_SESSION['business_id'] = null;

        $_SESSION['business'] = null;

        $_SESSION['tenant_role'] = null;

        return [
            'success'  => true,
            'type'     => 'platform',
            'redirect' => 'dashboard'
        ];
    }

    /*
     * =====================================================
     * BUSINESS USER
     * =====================================================
     */

    $sql = "
        SELECT
            bu.id,
            bu.business_id,
            bu.role,
            bu.status,
            b.name AS business_name,
            b.slug AS business_slug,
            b.status AS business_status
        FROM business_users bu

        INNER JOIN businesses b
            ON b.id = bu.business_id

        WHERE bu.user_id = :user_id
          AND bu.status = 'active'
          AND b.status = 'active'

        ORDER BY bu.id ASC

        LIMIT 1
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':user_id' => $user['id']
    ]);

    $business = $stmt->fetch(PDO::FETCH_ASSOC);

    /*
     * No active business membership.
     */
    if (!$business) {
        return [
            'success' => false,
            'message' => 'Your account is not assigned to an active business.'
        ];
    }

    /*
     * Create business session.
     */
    $_SESSION['authenticated'] = true;

    $_SESSION['user'] = [
        'id'        => (int) $user['id'],
        'username'  => $user['username'],
        'email'     => $user['email'],
        'full_name' => $user['full_name'],
        'role'      => $user['role']
    ];

    $_SESSION['business_id'] = (int) $business['business_id'];

    $_SESSION['business'] = [
        'id'     => (int) $business['business_id'],
        'name'   => $business['business_name'],
        'slug'   => $business['business_slug'],
        'status' => $business['business_status']
    ];

    $_SESSION['tenant_role'] = $business['role'];

    return [
        'success'  => true,
        'type'     => 'business',
        'redirect' => 'dashboard'
    ];
}


    /**
     * Check if user is authenticated.
     */
    public static function check(): bool
    {
        self::startSession();

        return !empty($_SESSION['authenticated']);
    }


    /**
     * Get currently authenticated user.
     */
    public static function user(): ?array
    {
        self::startSession();

        return $_SESSION['user'] ?? null;
    }


    /**
     * Get current business ID.
     */
    public static function businessId(): ?int
    {
        self::startSession();

        if (
            !isset($_SESSION['business_id']) ||
            $_SESSION['business_id'] === null
        ) {
            return null;
        }

        return (int) $_SESSION['business_id'];
    }


    /**
     * Get current business.
     */
    public static function business(): ?array
    {
        self::startSession();

        return $_SESSION['business'] ?? null;
    }


    /**
     * Get tenant role.
     */
    public static function tenantRole(): ?string
    {
        self::startSession();

        return $_SESSION['tenant_role'] ?? null;
    }


    /**
     * Check if current user is super admin.
     */
    public static function isSuperAdmin(): bool
    {
        self::startSession();

        return (
            isset($_SESSION['user']['role']) &&
            $_SESSION['user']['role'] === 'super_admin'
        );
    }


    /**
     * Logout.
     */
    public static function logout(): void
    {
        self::startSession();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}