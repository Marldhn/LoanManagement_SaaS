<?php

class BusinessUserController
{
    /**
     * Display business users.
     */
    public function index(): void
    {
        Auth::startSession();

        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {
            header('Location: index.php?url=auth/login');
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin should not use this page
        |--------------------------------------------------------------------------
        */

        if (Auth::isSuperAdmin()) {
            header('Location: index.php?url=dashboard');
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Only business owner/admin can manage users
        |--------------------------------------------------------------------------
        */

        $tenantRole = Auth::tenantRole();

        if (!in_array(
            $tenantRole,
            ['owner', 'admin'],
            true
        )) {
            http_response_code(403);

            echo '<h1>403 - Access Denied</h1>';
            echo '<p>You do not have permission to manage users.</p>';

            exit;
        }

        $businessId = Auth::businessId();

        $db = Database::getInstance();

        /*
        |--------------------------------------------------------------------------
        | Get users belonging to this business
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                bu.id AS business_user_id,
                bu.business_id,
                bu.user_id,
                bu.role AS tenant_role,
                bu.status AS membership_status,

                u.username,
                u.email,
                u.full_name,
                u.role AS system_role,
                u.status AS user_status,

                bu.created_at

            FROM business_users bu

            INNER JOIN users u
                ON u.id = bu.user_id

            WHERE bu.business_id = :business_id

            ORDER BY bu.id DESC
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require APP_PATH . '/views/business_users/index.php';
    }


    /**
     * Display create user form.
     */
    public function create(): void
    {
        Auth::startSession();

        if (!Auth::check()) {
            header('Location: index.php?url=auth/login');
            exit;
        }

        if (Auth::isSuperAdmin()) {
            header('Location: index.php?url=dashboard');
            exit;
        }

        $tenantRole = Auth::tenantRole();

        if (!in_array(
            $tenantRole,
            ['owner', 'admin'],
            true
        )) {
            http_response_code(403);

            echo '<h1>403 - Access Denied</h1>';
            exit;
        }

        require APP_PATH . '/views/business_users/create.php';
    }


    /**
     * Store a new business user.
     */
    public function store(): void
    {
        Auth::startSession();

        if (!Auth::check()) {
            header('Location: index.php?url=auth/login');
            exit;
        }

        if (Auth::isSuperAdmin()) {
            header('Location: index.php?url=dashboard');
            exit;
        }

        $tenantRole = Auth::tenantRole();

        if (!in_array(
            $tenantRole,
            ['owner', 'admin'],
            true
        )) {
            http_response_code(403);

            echo '<h1>403 - Access Denied</h1>';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=business-users');
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Get submitted data
        |--------------------------------------------------------------------------
        */

        $username = trim(
            $_POST['username'] ?? ''
        );

        $email = trim(
            $_POST['email'] ?? ''
        );

        $fullName = trim(
            $_POST['full_name'] ?? ''
        );

        $password = $_POST['password'] ?? '';

        $role = trim(
            $_POST['role'] ?? 'staff'
        );


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $allowedRoles = [
            'admin',
            'loan_officer',
            'cashier',
            'staff'
        ];

        if (
            $username === '' ||
            $email === '' ||
            $fullName === '' ||
            $password === ''
        ) {
            $_SESSION['error'] =
                'Please complete all required fields.';

            header(
                'Location: index.php?url=business-users/create'
            );

            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $_SESSION['error'] =
                'Please enter a valid email address.';

            header(
                'Location: index.php?url=business-users/create'
            );

            exit;
        }

        if (strlen($password) < 8) {

            $_SESSION['error'] =
                'Password must be at least 8 characters.';

            header(
                'Location: index.php?url=business-users/create'
            );

            exit;
        }

        if (!in_array($role, $allowedRoles, true)) {

            $_SESSION['error'] =
                'Invalid user role selected.';

            header(
                'Location: index.php?url=business-users/create'
            );

            exit;
        }


        $businessId = Auth::businessId();

        $db = Database::getInstance();


        /*
        |--------------------------------------------------------------------------
        | Check username
        |--------------------------------------------------------------------------
        */

        $stmt = $db->prepare("
            SELECT id
            FROM users
            WHERE username = :username
            LIMIT 1
        ");

        $stmt->execute([
            ':username' => $username
        ]);

        if ($stmt->fetch()) {

            $_SESSION['error'] =
                'Username is already in use.';

            header(
                'Location: index.php?url=business-users/create'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Check email
        |--------------------------------------------------------------------------
        */

        $stmt = $db->prepare("
            SELECT id
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ':email' => $email
        ]);

        if ($stmt->fetch()) {

            $_SESSION['error'] =
                'Email address is already in use.';

            header(
                'Location: index.php?url=business-users/create'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Password Hash
        |--------------------------------------------------------------------------
        */

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );


        /*
        |--------------------------------------------------------------------------
        | Start Transaction
        |--------------------------------------------------------------------------
        */

        $db->beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            $stmt = $db->prepare("
                INSERT INTO users (
                    username,
                    email,
                    password,
                    full_name,
                    role,
                    status
                )
                VALUES (
                    :username,
                    :email,
                    :password,
                    :full_name,
                    'staff',
                    'approved'
                )
            ");

            $stmt->execute([
                ':username'  => $username,
                ':email'     => $email,
                ':password'  => $hashedPassword,
                ':full_name' => $fullName
            ]);

            $userId = (int) $db->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | Create Business Membership
            |--------------------------------------------------------------------------
            */

            $stmt = $db->prepare("
                INSERT INTO business_users (
                    business_id,
                    user_id,
                    role,
                    status
                )
                VALUES (
                    :business_id,
                    :user_id,
                    :role,
                    'active'
                )
            ");

            $stmt->execute([
                ':business_id' => $businessId,
                ':user_id'     => $userId,
                ':role'        => $role
            ]);


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            $db->commit();


            $_SESSION['success'] =
                'User created successfully.';


            header(
                'Location: index.php?url=business-users'
            );

            exit;

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Rollback
            |--------------------------------------------------------------------------
            */

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $_SESSION['error'] =
                'Unable to create user.';

            /*
             * For development only.
             * Remove later in production.
             */

            $_SESSION['error_details'] =
                $e->getMessage();


            header(
                'Location: index.php?url=business-users/create'
            );

            exit;
        }
    }
}