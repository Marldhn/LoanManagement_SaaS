<?php

class BusinessUserController
{
    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION HELPER
    |--------------------------------------------------------------------------
    */

    private function checkAccess(): void
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

        if (!in_array($tenantRole, ['owner', 'admin'], true)) {
            http_response_code(403);

            echo '<h1>403 - Access Denied</h1>';
            echo '<p>You do not have permission to manage users.</p>';

            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GET BUSINESS USER
    |--------------------------------------------------------------------------
    */

    private function getBusinessUser(int $businessUserId): ?array
    {
        $businessId = (int) Auth::businessId();

        $db = Database::getInstance();

        $stmt = $db->prepare("
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

            WHERE bu.id = :business_user_id
            AND bu.business_id = :business_id

            LIMIT 1
        ");

        $stmt->execute([
            ':business_user_id' => $businessUserId,
            ':business_id' => $businessId
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $this->checkAccess();

        $businessId = (int) Auth::businessId();

        $db = Database::getInstance();

        $stmt = $db->prepare("
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
        ");

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require APP_PATH . '/views/business_users/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(): void
    {
        $this->checkAccess();

        require APP_PATH . '/views/business_users/create.php';
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        $this->checkAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=business-users');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? 'staff');

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

        $businessId = (int) Auth::businessId();

        $db = Database::getInstance();

        /*
        |--------------------------------------------------------------------------
        | CHECK USERNAME
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
        | CHECK EMAIL
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
        | HASH PASSWORD
        |--------------------------------------------------------------------------
        */

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $db->beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | CREATE USER
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
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':full_name' => $fullName
            ]);

            $userId = (int) $db->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | CREATE BUSINESS MEMBERSHIP
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
                ':user_id' => $userId,
                ':role' => $role
            ]);


            $db->commit();

            $_SESSION['success'] =
                'User created successfully.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;

        } catch (Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $_SESSION['error'] =
                'Unable to create user.';

            $_SESSION['error_details'] =
                $e->getMessage();

            header(
                'Location: index.php?url=business-users/create'
            );

            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |
    | This is used by the modal.
    | No current password is required.
    |--------------------------------------------------------------------------
    */

    public function updatePassword(): void
    {
        $this->checkAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }

        $businessUserId = (int) (
            $_POST['business_user_id'] ?? 0
        );

        $password = $_POST['password'] ?? '';

        $confirmPassword = $_POST[
            'password_confirmation'
        ] ?? '';


        /*
        |--------------------------------------------------------------------------
        | VALIDATE USER ID
        |--------------------------------------------------------------------------
        */

        if ($businessUserId <= 0) {
            $_SESSION['error'] =
                'Invalid user.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | GET BUSINESS USER
        |--------------------------------------------------------------------------
        */

        $businessUser = $this->getBusinessUser(
            $businessUserId
        );

        if (!$businessUser) {
            $_SESSION['error'] =
                'User not found.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | CURRENT USER
        |--------------------------------------------------------------------------
        */

        $currentUser = Auth::user();

        $currentUserId = (int) (
            $currentUser['id'] ?? 0
        );


        /*
        |--------------------------------------------------------------------------
        | PREVENT CHANGING OWN PASSWORD
        |--------------------------------------------------------------------------
        */

        if (
            (int) $businessUser['user_id']
            ===
            $currentUserId
        ) {
            $_SESSION['error'] =
                'You cannot change your own password from the user management page.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | PROTECT OWNER
        |--------------------------------------------------------------------------
        */

        if (
            ($businessUser['tenant_role'] ?? '')
            ===
            'owner'
        ) {
            $_SESSION['error'] =
                'The business owner password cannot be changed from this page.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | PASSWORD VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($password === '') {
            $_SESSION['error'] =
                'Please enter the new password.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] =
                'Password must be at least 8 characters.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] =
                'Passwords do not match.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | HASH NEW PASSWORD
        |--------------------------------------------------------------------------
        */

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );


        /*
        |--------------------------------------------------------------------------
        | UPDATE PASSWORD
        |--------------------------------------------------------------------------
        */

        $db = Database::getInstance();

        $stmt = $db->prepare("
            UPDATE users

            SET password = :password

            WHERE id = :user_id

            LIMIT 1
        ");

        $stmt->execute([
            ':password' => $hashedPassword,
            ':user_id' => (int) $businessUser['user_id']
        ]);


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $_SESSION['success'] =
            'Password changed successfully for '
            . ($businessUser['full_name'] ?? 'the user')
            . '.';


        header(
            'Location: index.php?url=business-users'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DISABLE USER
    |--------------------------------------------------------------------------
    */

    public function disable(): void
    {
        $this->checkAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }

        $businessUserId = (int) (
            $_POST['business_user_id'] ?? 0
        );

        $businessUser = $this->getBusinessUser(
            $businessUserId
        );

        if (!$businessUser) {
            $_SESSION['error'] =
                'User not found.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | CURRENT USER
        |--------------------------------------------------------------------------
        */

        $currentUser = Auth::user();

        $currentUserId = (int) (
            $currentUser['id'] ?? 0
        );


        /*
        |--------------------------------------------------------------------------
        | PREVENT SELF-DISABLE
        |--------------------------------------------------------------------------
        */

        if (
            (int) $businessUser['user_id']
            ===
            $currentUserId
        ) {
            $_SESSION['error'] =
                'You cannot disable your own account.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | PROTECT OWNER
        |--------------------------------------------------------------------------
        */

        if (
            ($businessUser['tenant_role'] ?? '')
            ===
            'owner'
        ) {
            $_SESSION['error'] =
                'The business owner cannot be disabled.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | DISABLE
        |--------------------------------------------------------------------------
        */

        $db = Database::getInstance();

        $stmt = $db->prepare("
            UPDATE business_users

            SET status = 'inactive'

            WHERE id = :business_user_id
            AND business_id = :business_id

            LIMIT 1
        ");

        $stmt->execute([
            ':business_user_id' => $businessUserId,
            ':business_id' => (int) Auth::businessId()
        ]);


        $_SESSION['success'] =
            'User disabled successfully.';

        header(
            'Location: index.php?url=business-users'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | ENABLE USER
    |--------------------------------------------------------------------------
    */

    public function enable(): void
    {
        $this->checkAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }

        $businessUserId = (int) (
            $_POST['business_user_id'] ?? 0
        );

        $businessUser = $this->getBusinessUser(
            $businessUserId
        );

        if (!$businessUser) {
            $_SESSION['error'] =
                'User not found.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | PROTECT OWNER
        |--------------------------------------------------------------------------
        */

        if (
            ($businessUser['tenant_role'] ?? '')
            ===
            'owner'
        ) {
            $_SESSION['error'] =
                'The business owner does not need to be enabled.';

            header(
                'Location: index.php?url=business-users'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | ENABLE
        |--------------------------------------------------------------------------
        */

        $db = Database::getInstance();

        $stmt = $db->prepare("
            UPDATE business_users

            SET status = 'active'

            WHERE id = :business_user_id
            AND business_id = :business_id

            LIMIT 1
        ");

        $stmt->execute([
            ':business_user_id' => $businessUserId,
            ':business_id' => (int) Auth::businessId()
        ]);


        $_SESSION['success'] =
            'User enabled successfully.';

        header(
            'Location: index.php?url=business-users'
        );

        exit;
    }
}