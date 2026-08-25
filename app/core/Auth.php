<?php

class Auth
{
    /*
    |--------------------------------------------------------------------------
    | CONFIGURATION
    |--------------------------------------------------------------------------
    */

    private const DEFAULT_SESSION_TIMEOUT = 120;

    private const DEFAULT_MAX_LOGIN_ATTEMPTS = 5;


    /*
    |--------------------------------------------------------------------------
    | START SESSION
    |--------------------------------------------------------------------------
    */

    public static function startSession(): void
    {
        if (
            session_status()
            === PHP_SESSION_NONE
        ) {
            session_start();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public static function login(
        string $login,
        string $password
    ): array {

        self::startSession();

        $db =
            Database::getInstance();


        /*
        |--------------------------------------------------------------------------
        | FIND USER
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                id,
                username,
                email,
                password,
                full_name,
                role,
                status,
                failed_login_attempts,
                locked_until
            FROM users
            WHERE username = :username
               OR email = :email
            LIMIT 1
        ";


        $stmt =
            $db->prepare(
                $sql
            );


        $stmt->execute([
            ':username' =>
                $login,

            ':email' =>
                $login
        ]);


        $user =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | USER NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$user) {

            return [
                'success' => false,

                'message' =>
                    'Invalid username/email or password.'
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | ACCOUNT ALREADY INACTIVE
        |--------------------------------------------------------------------------
        |
        | An inactive account means the maximum number of failed
        | login attempts has already been reached.
        |
        | The account must be manually activated by the
        | Super Admin.
        |
        */

        if (
            $user['status']
            === 'inactive'
        ) {

            return [
                'success' => false,

                'message' =>
                    'Your account has been deactivated because the maximum number of failed login attempts was reached. Please contact your administrator or Super Admin to reactivate your account.'
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | ACCOUNT STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $user['status']
            !== 'approved'
        ) {

            return [
                'success' => false,

                'message' =>
                    'Your account is not approved.'
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | GET BUSINESS
        |--------------------------------------------------------------------------
        |
        | The login_attempts setting belongs to the business.
        |
        */

        $business = null;


        /*
        |--------------------------------------------------------------------------
        | BUSINESS USER
        |--------------------------------------------------------------------------
        */

        if (
            $user['role']
            !== 'super_admin'
        ) {

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


            $stmt =
                $db->prepare(
                    $sql
                );


            $stmt->execute([
                ':user_id' =>
                    $user['id']
            ]);


            $business =
                $stmt->fetch(
                    PDO::FETCH_ASSOC
                );


            /*
            |--------------------------------------------------------------------------
            | NO ACTIVE BUSINESS
            |--------------------------------------------------------------------------
            */

            if (!$business) {

                return [
                    'success' => false,

                    'message' =>
                        'Your account is not assigned to an active business.'
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GET MAXIMUM LOGIN ATTEMPTS
        |--------------------------------------------------------------------------
        */

        $maxLoginAttempts =
            self::DEFAULT_MAX_LOGIN_ATTEMPTS;


        /*
        |--------------------------------------------------------------------------
        | BUSINESS LOGIN ATTEMPTS SETTING
        |--------------------------------------------------------------------------
        */

        if (
            $business !== null
        ) {

            try {

                $stmt =
                    $db->prepare(
                        "
                        SELECT setting_value
                        FROM system_settings
                        WHERE business_id = :business_id
                          AND setting_key = 'login_attempts'
                        LIMIT 1
                        "
                    );


                $stmt->execute([
                    ':business_id' =>
                        $business['business_id']
                ]);


                $settingValue =
                    $stmt->fetchColumn();


                if (
                    $settingValue !== false &&
                    $settingValue !== null &&
                    $settingValue !== ''
                ) {

                    $maxLoginAttempts =
                        (int)$settingValue;
                }

            } catch (
                Throwable $e
            ) {

                $maxLoginAttempts =
                    self::DEFAULT_MAX_LOGIN_ATTEMPTS;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SAFETY LIMIT
        |--------------------------------------------------------------------------
        */

        $maxLoginAttempts =
            max(
                1,
                min(
                    20,
                    $maxLoginAttempts
                )
            );


        /*
        |--------------------------------------------------------------------------
        | VERIFY PASSWORD
        |--------------------------------------------------------------------------
        */

        if (
            !password_verify(
                $password,
                $user['password']
            )
        ) {

            /*
            |--------------------------------------------------------------------------
            | INCREMENT FAILED ATTEMPTS
            |--------------------------------------------------------------------------
            */

            $failedAttempts =
                (int)(
                    $user['failed_login_attempts']
                    ?? 0
                );


            $failedAttempts++;


            /*
            |--------------------------------------------------------------------------
            | MAXIMUM ATTEMPTS REACHED
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | We use "inactive" instead of "locked".
            |
            | This means the account stays inactive permanently
            | until a Super Admin activates it again.
            |
            */

            if (
                $failedAttempts
                >= $maxLoginAttempts
            ) {

                $stmt =
                    $db->prepare(
                        "
                        UPDATE users
                        SET
                            failed_login_attempts = :failed_attempts,
                            status = 'inactive',
                            locked_until = NULL
                        WHERE id = :id
                        "
                    );


                $stmt->execute([

                    ':failed_attempts' =>
                        $failedAttempts,

                    ':id' =>
                        $user['id']
                ]);


                return [
                    'success' => false,

                    'message' =>
                        'Your account has been deactivated because the maximum number of failed login attempts was reached. Please contact your administrator or Super Admin to reactivate your account.'
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | SAVE FAILED ATTEMPT
            |--------------------------------------------------------------------------
            */

            $stmt =
                $db->prepare(
                    "
                    UPDATE users
                    SET
                        failed_login_attempts = :failed_attempts
                    WHERE id = :id
                    "
                );


            $stmt->execute([

                ':failed_attempts' =>
                    $failedAttempts,

                ':id' =>
                    $user['id']
            ]);


            /*
            |--------------------------------------------------------------------------
            | REMAINING ATTEMPTS
            |--------------------------------------------------------------------------
            */

            $remainingAttempts =
                max(
                    0,
                    $maxLoginAttempts
                    - $failedAttempts
                );


            return [
                'success' => false,

                'message' =>
                    'Invalid username/email or password. '
                    . $remainingAttempts
                    . ' login attempt(s) remaining.'
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESSFUL PASSWORD
        |--------------------------------------------------------------------------
        |
        | Reset failed login attempts.
        |
        */

        $stmt =
            $db->prepare(
                "
                UPDATE users
                SET
                    failed_login_attempts = 0,
                    locked_until = NULL
                WHERE id = :id
                "
            );


        $stmt->execute([
            ':id' =>
                $user['id']
        ]);


        /*
        |--------------------------------------------------------------------------
        | REGENERATE SESSION
        |--------------------------------------------------------------------------
        */

        session_regenerate_id(
            true
        );


        /*
        |--------------------------------------------------------------------------
        | INITIALIZE ACTIVITY TIMER
        |--------------------------------------------------------------------------
        */

        $_SESSION['last_activity'] =
            time();


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if (
            $user['role']
            === 'super_admin'
        ) {

            $_SESSION['authenticated'] =
                true;


            $_SESSION['user'] = [

                'id' =>
                    (int)$user['id'],

                'username' =>
                    $user['username'],

                'email' =>
                    $user['email'],

                'full_name' =>
                    $user['full_name'],

                'role' =>
                    $user['role']
            ];


            $_SESSION['business_id'] =
                null;


            $_SESSION['business'] =
                null;


            $_SESSION['tenant_role'] =
                null;


            return [
                'success' =>
                    true,

                'type' =>
                    'platform',

                'redirect' =>
                    'dashboard'
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE BUSINESS SESSION
        |--------------------------------------------------------------------------
        */

        $_SESSION['authenticated'] =
            true;


        $_SESSION['user'] = [

            'id' =>
                (int)$user['id'],

            'username' =>
                $user['username'],

            'email' =>
                $user['email'],

            'full_name' =>
                $user['full_name'],

            'role' =>
                $user['role']
        ];


        $_SESSION['business_id'] =
            (int)$business['business_id'];


        $_SESSION['business'] = [

            'id' =>
                (int)$business['business_id'],

            'name' =>
                $business['business_name'],

            'slug' =>
                $business['business_slug'],

            'status' =>
                $business['business_status']
        ];


        $_SESSION['tenant_role'] =
            $business['role'];


        /*
        |--------------------------------------------------------------------------
        | RESET ACTIVITY TIMER
        |--------------------------------------------------------------------------
        */

        $_SESSION['last_activity'] =
            time();


        return [
            'success' =>
                true,

            'type' =>
                'business',

            'redirect' =>
                'dashboard'
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION CHECK
    |--------------------------------------------------------------------------
    */

    public static function check(): bool
    {
        self::startSession();


        /*
        |--------------------------------------------------------------------------
        | NOT LOGGED IN
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $_SESSION['authenticated']
            )
        ) {

            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | BUSINESS ID
        |--------------------------------------------------------------------------
        */

        $businessId =
            (int)(
                $_SESSION['business_id']
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | DEFAULT TIMEOUT
        |--------------------------------------------------------------------------
        */

        $timeoutMinutes =
            self::DEFAULT_SESSION_TIMEOUT;


        /*
        |--------------------------------------------------------------------------
        | BUSINESS SESSION TIMEOUT
        |--------------------------------------------------------------------------
        */

        if (
            $businessId > 0
        ) {

            try {

                $db =
                    Database::getInstance();


                $stmt =
                    $db->prepare(
                        "
                        SELECT setting_value
                        FROM system_settings
                        WHERE business_id = :business_id
                          AND setting_key = 'session_timeout'
                        LIMIT 1
                        "
                    );


                $stmt->execute([
                    ':business_id' =>
                        $businessId
                ]);


                $timeout =
                    $stmt->fetchColumn();


                if (
                    $timeout !== false &&
                    $timeout !== null &&
                    $timeout !== ''
                ) {

                    $timeoutMinutes =
                        (int)$timeout;
                }

            } catch (
                Throwable $e
            ) {

                $timeoutMinutes =
                    self::DEFAULT_SESSION_TIMEOUT;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SAFETY LIMIT
        |--------------------------------------------------------------------------
        */

        $timeoutMinutes =
            max(
                5,
                min(
                    1440,
                    $timeoutMinutes
                )
            );


        /*
        |--------------------------------------------------------------------------
        | CURRENT TIME
        |--------------------------------------------------------------------------
        */

        $now =
            time();


        /*
        |--------------------------------------------------------------------------
        | LAST ACTIVITY
        |--------------------------------------------------------------------------
        */

        $lastActivity =
            (int)(
                $_SESSION['last_activity']
                ?? $now
            );


        /*
        |--------------------------------------------------------------------------
        | TIMEOUT CHECK
        |--------------------------------------------------------------------------
        */

        $timeoutSeconds =
            $timeoutMinutes * 60;


        if (
            ($now - $lastActivity)
            >= $timeoutSeconds
        ) {

            self::logout();

            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE ACTIVITY
        |--------------------------------------------------------------------------
        */

        $_SESSION['last_activity'] =
            $now;


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | GET USER
    |--------------------------------------------------------------------------
    */

    public static function user(): ?array
    {
        self::startSession();

        return
            $_SESSION['user']
            ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | GET BUSINESS ID
    |--------------------------------------------------------------------------
    */

    public static function businessId(): ?int
    {
        self::startSession();


        if (
            !isset(
                $_SESSION['business_id']
            )
            ||
            $_SESSION['business_id']
            === null
        ) {

            return null;
        }


        return (int)
            $_SESSION['business_id'];
    }


    /*
    |--------------------------------------------------------------------------
    | GET BUSINESS
    |--------------------------------------------------------------------------
    */

    public static function business(): ?array
    {
        self::startSession();

        return
            $_SESSION['business']
            ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | GET TENANT ROLE
    |--------------------------------------------------------------------------
    */

    public static function tenantRole(): ?string
    {
        self::startSession();

        return
            $_SESSION['tenant_role']
            ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    public static function isSuperAdmin(): bool
    {
        self::startSession();


        return (
            isset(
                $_SESSION['user']['role']
            )
            &&
            $_SESSION['user']['role']
            === 'super_admin'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public static function logout(): void
    {
        self::startSession();


        $_SESSION = [];


        if (
            ini_get(
                'session.use_cookies'
            )
        ) {

            $params =
                session_get_cookie_params();


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