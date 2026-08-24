<?php

class AuthController
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(): void
    {
        Auth::startSession();

        /*
         * Already logged in?
         */
        if (Auth::check()) {

            header(
                'Location: index.php?url=dashboard'
            );

            exit;
        }

        $error = null;

        /*
         * Process login.
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $login = trim(
                $_POST['login'] ?? ''
            );

            $password = $_POST['password'] ?? '';

            if (
                $login === '' ||
                $password === ''
            ) {

                $error =
                    'Please enter your username/email and password.';

            } else {

                $result = Auth::login(
                    $login,
                    $password
                );

                if ($result['success']) {

                    header(
                        'Location: index.php?url=' .
                        urlencode(
                            $result['redirect']
                        )
                    );

                    exit;
                }

                $error = $result['message'];
            }
        }

        require APP_PATH . '/views/auth/login.php';
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */

    public function register(): void
    {
        Auth::startSession();

        /*
         * Already logged in?
         */
        if (Auth::check()) {

            header(
                'Location: index.php?url=dashboard'
            );

            exit;
        }

        $error = null;

        $old = [
            'full_name'       => '',
            'username'        => '',
            'email'           => '',
            'business_name'   => '',
            'business_email'  => '',
            'business_phone'  => '',
            'business_address'=> ''
        ];


        /*
         * Process registration.
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            /*
             * Get submitted values.
             */
            $fullName = trim(
                $_POST['full_name'] ?? ''
            );

            $username = trim(
                $_POST['username'] ?? ''
            );

            $email = trim(
                $_POST['email'] ?? ''
            );

            $password = $_POST['password'] ?? '';

            $confirmPassword =
                $_POST['confirm_password'] ?? '';

            $businessName = trim(
                $_POST['business_name'] ?? ''
            );

            $businessEmail = trim(
                $_POST['business_email'] ?? ''
            );

            $businessPhone = trim(
                $_POST['business_phone'] ?? ''
            );

            $businessAddress = trim(
                $_POST['business_address'] ?? ''
            );


            /*
             * Preserve values if validation fails.
             */
            $old = [
                'full_name'        => $fullName,
                'username'         => $username,
                'email'            => $email,
                'business_name'    => $businessName,
                'business_email'   => $businessEmail,
                'business_phone'   => $businessPhone,
                'business_address' => $businessAddress
            ];


            /*
             * =====================================================
             * VALIDATION
             * =====================================================
             */

            if (
                $fullName === '' ||
                $username === '' ||
                $email === '' ||
                $password === '' ||
                $confirmPassword === '' ||
                $businessName === ''
            ) {

                $error =
                    'Please complete all required fields.';

            } elseif (
                !filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {

                $error =
                    'Please enter a valid email address.';

            } elseif (
                $businessEmail !== '' &&
                !filter_var(
                    $businessEmail,
                    FILTER_VALIDATE_EMAIL
                )
            ) {

                $error =
                    'Please enter a valid business email address.';

            } elseif (
                strlen($username) < 4
            ) {

                $error =
                    'Username must be at least 4 characters.';

            } elseif (
                !preg_match(
                    '/^[a-zA-Z0-9_.-]+$/',
                    $username
                )
            ) {

                $error =
                    'Username may only contain letters, numbers, dots, underscores, and hyphens.';

            } elseif (
                strlen($password) < 8
            ) {

                $error =
                    'Password must be at least 8 characters.';

            } elseif (
                $password !== $confirmPassword
            ) {

                $error =
                    'Passwords do not match.';

            }


            /*
             * =====================================================
             * CREATE ACCOUNT
             * =====================================================
             */

            if ($error === null) {

                $db = Database::getInstance();


                try {

                    /*
                     * Start database transaction.
                     */
                    $db->beginTransaction();


                    /*
                     * -------------------------------------------------
                     * Check username
                     * -------------------------------------------------
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

                        throw new Exception(
                            'Username is already registered.'
                        );
                    }


                    /*
                     * -------------------------------------------------
                     * Check email
                     * -------------------------------------------------
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

                        throw new Exception(
                            'Email address is already registered.'
                        );
                    }


                    /*
                     * -------------------------------------------------
                     * Create user
                     * -------------------------------------------------
                     *
                     * The user becomes an ADMIN at the platform
                     * users level.
                     *
                     * Their tenant/business role will be OWNER.
                     */

                    $passwordHash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


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
                            'admin',
                            'approved'
                        )
                    ");


                    $stmt->execute([
                        ':username'  => $username,
                        ':email'     => $email,
                        ':password'  => $passwordHash,
                        ':full_name' => $fullName
                    ]);


                    $userId =
                        (int) $db->lastInsertId();


                    /*
                     * -------------------------------------------------
                     * Generate business slug
                     * -------------------------------------------------
                     */

                    $slug = strtolower(
                        trim(
                            preg_replace(
                                '/[^a-zA-Z0-9]+/',
                                '-',
                                $businessName
                            ),
                            '-'
                        )
                    );


                    /*
                     * Make slug unique.
                     */
                    $baseSlug = $slug;

                    $counter = 1;


                    while (true) {

                        $stmt = $db->prepare("
                            SELECT id
                            FROM businesses
                            WHERE slug = :slug
                            LIMIT 1
                        ");

                        $stmt->execute([
                            ':slug' => $slug
                        ]);

                        if (!$stmt->fetch()) {
                            break;
                        }

                        $counter++;

                        $slug =
                            $baseSlug .
                            '-' .
                            $counter;
                    }


                    /*
                     * -------------------------------------------------
                     * Create business
                     * -------------------------------------------------
                     */

                    $stmt = $db->prepare("
                        INSERT INTO businesses (
                            name,
                            slug,
                            email,
                            phone,
                            address,
                            status
                        )
                        VALUES (
                            :name,
                            :slug,
                            :email,
                            :phone,
                            :address,
                            'active'
                        )
                    ");


                    $stmt->execute([
                        ':name'    => $businessName,
                        ':slug'    => $slug,
                        ':email'   =>
                            $businessEmail !== ''
                                ? $businessEmail
                                : null,
                        ':phone'   =>
                            $businessPhone !== ''
                                ? $businessPhone
                                : null,
                        ':address' =>
                            $businessAddress !== ''
                                ? $businessAddress
                                : null
                    ]);


                    $businessId =
                        (int) $db->lastInsertId();


                    /*
                     * -------------------------------------------------
                     * Create business membership
                     * -------------------------------------------------
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
                            'owner',
                            'active'
                        )
                    ");


                    $stmt->execute([
                        ':business_id' =>
                            $businessId,

                        ':user_id' =>
                            $userId
                    ]);


                    /*
                     * Commit everything.
                     */
                    $db->commit();


                    /*
                     * -------------------------------------------------
                     * Automatically log the user in.
                     * -------------------------------------------------
                     */

                    session_regenerate_id(true);


                    $_SESSION['authenticated'] = true;


                    $_SESSION['user'] = [
                        'id' =>
                            $userId,

                        'username' =>
                            $username,

                        'email' =>
                            $email,

                        'full_name' =>
                            $fullName,

                        'role' =>
                            'admin'
                    ];


                    $_SESSION['business_id'] =
                        $businessId;


                    $_SESSION['business'] = [
                        'id' =>
                            $businessId,

                        'name' =>
                            $businessName,

                        'slug' =>
                            $slug,

                        'status' =>
                            'active'
                    ];


                    $_SESSION['tenant_role'] =
                        'owner';


                    /*
                     * Redirect to business dashboard.
                     */

                    header(
                        'Location: index.php?url=dashboard'
                    );

                    exit;


                } catch (Exception $e) {

                    /*
                     * Rollback if something failed.
                     */

                    if (
                        $db->inTransaction()
                    ) {

                        $db->rollBack();
                    }


                    $error =
                        $e->getMessage();

                } catch (PDOException $e) {

                    /*
                     * Rollback database transaction.
                     */

                    if (
                        $db->inTransaction()
                    ) {

                        $db->rollBack();
                    }


                    $error =
                        'Registration failed. Please try again.';

                    /*
                     * For development only:
                     *
                     * Uncomment temporarily if you need
                     * to see the actual database error.
                     *
                     * $error = $e->getMessage();
                     */
                }
            }
        }


        /*
         * Show registration page.
         */

        require APP_PATH . '/views/auth/register.php';
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(): void
    {
        Auth::logout();

        header(
            'Location: index.php?url=auth/login'
        );

        exit;
    }
}