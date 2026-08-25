<?php

class SettingsController
{
    private Setting $setting;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->setting = new Setting();
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESS CHECK
    |--------------------------------------------------------------------------
    */

    private function checkAccess(): array
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATION
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            header(
                'Location: index.php?url=auth/login'
            );

            exit;
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
        | USER ID
        |--------------------------------------------------------------------------
        */

        $userId =
            (int)(
                $_SESSION['user']['id']
                ?? $_SESSION['user_id']
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | BUSINESS CHECK
        |--------------------------------------------------------------------------
        */

        if ($businessId <= 0) {

            http_response_code(403);

            exit(
                'Business account not found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CURRENT USER
        |--------------------------------------------------------------------------
        */

        $user =
            Auth::user();


        /*
        |--------------------------------------------------------------------------
        | CURRENT BUSINESS
        |--------------------------------------------------------------------------
        */

        $business =
            Auth::business();


        /*
        |--------------------------------------------------------------------------
        | TENANT ROLE
        |--------------------------------------------------------------------------
        */

        $tenantRole =
            Auth::tenantRole();


        /*
        |--------------------------------------------------------------------------
        | SETTINGS PERMISSIONS
        |--------------------------------------------------------------------------
        |
        | Only owners and admins can access system settings.
        |
        */

        $allowedRoles = [
            'owner',
            'admin'
        ];


        if (
            !in_array(
                strtolower(
                    (string)$tenantRole
                ),
                $allowedRoles,
                true
            )
        ) {

            http_response_code(403);

            exit(
                'You do not have permission to access system settings.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN CONTEXT
        |--------------------------------------------------------------------------
        */

        return [
            'business_id' =>
                $businessId,

            'user_id' =>
                $userId,

            'user' =>
                $user,

            'business' =>
                $business,

            'tenantRole' =>
                $tenantRole
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ACCESS
        |--------------------------------------------------------------------------
        */

        $context =
            $this->checkAccess();


        /*
        |--------------------------------------------------------------------------
        | BUSINESS ID
        |--------------------------------------------------------------------------
        */

        $businessId =
            $context['business_id'];


        /*
        |--------------------------------------------------------------------------
        | LOAD CURRENT BUSINESS DIRECTLY FROM DATABASE
        |--------------------------------------------------------------------------
        |
        | This prevents stale business information from the session
        | from being displayed after an update.
        |
        */

        $db =
            Database::getInstance();


        $businessStatement =
            $db->prepare(
                "
                SELECT
                    id,
                    name,
                    slug,
                    email,
                    phone,
                    address,
                    logo,
                    status
                FROM businesses
                WHERE id = :business_id
                LIMIT 1
                "
            );


        $businessStatement->execute([
            ':business_id' =>
                $businessId
        ]);


        $databaseBusiness =
            $businessStatement->fetch(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | USE DATABASE BUSINESS
        |--------------------------------------------------------------------------
        */

        if ($databaseBusiness) {

            $business =
                $databaseBusiness;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD SETTINGS
        |--------------------------------------------------------------------------
        */

        $settings =
            $this->setting->all(
                $businessId
            );


        /*
        |--------------------------------------------------------------------------
        | DEFAULT SETTINGS
        |--------------------------------------------------------------------------
        */

        $defaults = [

            /*
            |--------------------------------------------------------------------------
            | GENERAL
            |--------------------------------------------------------------------------
            */

            'system_name' =>
                'Loan Management System',

            'system_tagline' =>
                'Loan Management SaaS',

            'currency' =>
                'PHP',

            'currency_symbol' =>
                '₱',

            'date_format' =>
                'Y-m-d',

            'timezone' =>
                'Asia/Manila',


            /*
            |--------------------------------------------------------------------------
            | BRANDING
            |--------------------------------------------------------------------------
            */

            'sidebar_logo' =>
                '',

            'login_logo' =>
                '',

            'favicon' =>
                '',

            'primary_color' =>
                '#2563eb',


            /*
            |--------------------------------------------------------------------------
            | LOAN NUMBERING
            |--------------------------------------------------------------------------
            */

            'loan_number_prefix' =>
                'LN',

            'payment_number_prefix' =>
                'PAY',


            /*
            |--------------------------------------------------------------------------
            | LOAN DEFAULTS
            |--------------------------------------------------------------------------
            */

            'default_interest_type' =>
                'flat',

            'default_payment_type' =>
                'installment',

            'default_term' =>
                '1',

            'default_term_period' =>
                'months',

            'default_interest_rate' =>
                '0',

            'default_processing_fee' =>
                '0',


            /*
            |--------------------------------------------------------------------------
            | PENALTY
            |--------------------------------------------------------------------------
            */

            'enable_penalty' =>
                '0',

            'penalty_type' =>
                'fixed',

            'penalty_rate' =>
                '0',

            'penalty_amount' =>
                '0',


            /*
            |--------------------------------------------------------------------------
            | SYSTEM
            |--------------------------------------------------------------------------
            */

            'maintenance_mode' =>
                '0',

            'allow_registration' =>
                '1',


            /*
            |--------------------------------------------------------------------------
            | REMINDERS
            |--------------------------------------------------------------------------
            */

            'overdue_reminders' =>
                '1',

            'payment_reminders' =>
                '1',


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATIONS
            |--------------------------------------------------------------------------
            */

            'email_notifications' =>
                '1',

            'payment_notifications' =>
                '1',

            'overdue_notifications' =>
                '1',


            /*
            |--------------------------------------------------------------------------
            | SECURITY
            |--------------------------------------------------------------------------
            */

            'session_timeout' =>
                '120',

            'login_attempts' =>
                '5'
        ];


        /*
        |--------------------------------------------------------------------------
        | MERGE DEFAULTS
        |--------------------------------------------------------------------------
        */

        $settings =
            array_merge(
                $defaults,
                $settings
            );


        /*
        |--------------------------------------------------------------------------
        | MAKE CONTEXT AVAILABLE TO VIEW
        |--------------------------------------------------------------------------
        */

        extract($context);


        /*
        |--------------------------------------------------------------------------
        | OVERRIDE BUSINESS WITH DATABASE VERSION
        |--------------------------------------------------------------------------
        */

        if ($databaseBusiness) {

            $business =
                $databaseBusiness;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD VIEW
        |--------------------------------------------------------------------------
        */

        require APP_PATH .
            '/views/settings/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ACCESS
        |--------------------------------------------------------------------------
        */

        $context =
            $this->checkAccess();


        /*
        |--------------------------------------------------------------------------
        | BUSINESS ID
        |--------------------------------------------------------------------------
        */

        $businessId =
            $context['business_id'];


        /*
        |--------------------------------------------------------------------------
        | REQUEST METHOD
        |--------------------------------------------------------------------------
        */

        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET')
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | DATABASE
        |--------------------------------------------------------------------------
        */

        $db =
            Database::getInstance();


        /*
        |--------------------------------------------------------------------------
        | BUSINESS INFORMATION
        |--------------------------------------------------------------------------
        */

        $businessName =
            trim(
                $_POST['name']
                ?? ''
            );


        $businessSlug =
            trim(
                $_POST['slug']
                ?? ''
            );


        $businessEmail =
            trim(
                $_POST['email']
                ?? ''
            );


        $businessPhone =
            trim(
                $_POST['phone']
                ?? ''
            );


        $businessAddress =
            trim(
                $_POST['address']
                ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDATE BUSINESS NAME
        |--------------------------------------------------------------------------
        */

        if (
            $businessName === ''
        ) {

            $_SESSION['error'] =
                'Business name is required.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE BUSINESS SLUG
        |--------------------------------------------------------------------------
        */

        if (
            $businessSlug === ''
        ) {

            $_SESSION['error'] =
                'Business slug is required.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | CLEAN BUSINESS SLUG
        |--------------------------------------------------------------------------
        */

        $businessSlug =
            strtolower(
                preg_replace(
                    '/[^a-zA-Z0-9\-]/',
                    '-',
                    $businessSlug
                )
            );


        $businessSlug =
            trim(
                $businessSlug,
                '-'
            );


        if (
            $businessSlug === ''
        ) {

            $_SESSION['error'] =
                'Business slug is invalid.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK SLUG DUPLICATE
        |--------------------------------------------------------------------------
        */

        $slugStatement =
            $db->prepare(
                "
                SELECT id
                FROM businesses
                WHERE slug = :slug
                  AND id != :business_id
                LIMIT 1
                "
            );


        $slugStatement->execute([

            ':slug' =>
                $businessSlug,

            ':business_id' =>
                $businessId
        ]);


        if (
            $slugStatement->fetchColumn()
        ) {

            $_SESSION['error'] =
                'Business slug is already in use.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE BUSINESS INFORMATION
        |--------------------------------------------------------------------------
        */

        $businessStatement =
            $db->prepare(
                "
                UPDATE businesses
                SET
                    name = :name,
                    slug = :slug,
                    email = :email,
                    phone = :phone,
                    address = :address
                WHERE id = :business_id
                "
            );


        $businessStatement->execute([

            ':name' =>
                $businessName,

            ':slug' =>
                $businessSlug,

            ':email' =>
                $businessEmail !== ''
                    ? $businessEmail
                    : null,

            ':phone' =>
                $businessPhone !== ''
                    ? $businessPhone
                    : null,

            ':address' =>
                $businessAddress !== ''
                    ? $businessAddress
                    : null,

            ':business_id' =>
                $businessId
        ]);


        /*
        |--------------------------------------------------------------------------
        | REFRESH BUSINESS SESSION
        |--------------------------------------------------------------------------
        |
        | Auth::business() reads from $_SESSION['business'].
        | Therefore the session must be updated after changing
        | the business information in the database.
        |
        */

        $currentBusinessStatus =
            $_SESSION['business']['status']
            ?? 'active';


        $_SESSION['business'] = [

            'id' =>
                $businessId,

            'name' =>
                $businessName,

            'slug' =>
                $businessSlug,

            'email' =>
                $businessEmail,

            'phone' =>
                $businessPhone,

            'address' =>
                $businessAddress,

            'status' =>
                $currentBusinessStatus
        ];


        /*
        |--------------------------------------------------------------------------
        | COLLECT SETTINGS
        |--------------------------------------------------------------------------
        */

        $settings = [

            /*
            |--------------------------------------------------------------------------
            | GENERAL
            |--------------------------------------------------------------------------
            */

            'system_name' =>
                trim(
                    $_POST['system_name']
                    ?? 'Loan Management System'
                ),

            'system_tagline' =>
                trim(
                    $_POST['system_tagline']
                    ?? ''
                ),

            'currency' =>
                trim(
                    $_POST['currency']
                    ?? 'PHP'
                ),

            'currency_symbol' =>
                trim(
                    $_POST['currency_symbol']
                    ?? '₱'
                ),

            'date_format' =>
                trim(
                    $_POST['date_format']
                    ?? 'Y-m-d'
                ),

            'timezone' =>
                trim(
                    $_POST['timezone']
                    ?? 'Asia/Manila'
                ),


            /*
            |--------------------------------------------------------------------------
            | BRANDING
            |--------------------------------------------------------------------------
            */

            'primary_color' =>
                trim(
                    $_POST['primary_color']
                    ?? '#2563eb'
                ),


            /*
            |--------------------------------------------------------------------------
            | LOANS
            |--------------------------------------------------------------------------
            */

            'loan_number_prefix' =>
                trim(
                    $_POST['loan_number_prefix']
                    ?? 'LN'
                ),

            'payment_number_prefix' =>
                trim(
                    $_POST['payment_number_prefix']
                    ?? 'PAY'
                ),

            'default_interest_type' =>
                $_POST['default_interest_type']
                ?? 'flat',

            'default_payment_type' =>
                $_POST['default_payment_type']
                ?? 'installment',

            'default_term' =>
                max(
                    1,
                    (int)(
                        $_POST['default_term']
                        ?? 1
                    )
                ),

            'default_term_period' =>
                $_POST['default_term_period']
                ?? 'months',

            'default_interest_rate' =>
                max(
                    0,
                    (float)(
                        $_POST['default_interest_rate']
                        ?? 0
                    )
                ),

            'default_processing_fee' =>
                max(
                    0,
                    (float)(
                        $_POST['default_processing_fee']
                        ?? 0
                    )
                ),


            /*
            |--------------------------------------------------------------------------
            | COLLECTION / PENALTY
            |--------------------------------------------------------------------------
            */

            'enable_penalty' =>
                isset(
                    $_POST['enable_penalty']
                )
                    ? '1'
                    : '0',

            'penalty_type' =>
                $_POST['penalty_type']
                ?? 'fixed',

            'penalty_rate' =>
                max(
                    0,
                    (float)(
                        $_POST['penalty_rate']
                        ?? 0
                    )
                ),

            'penalty_amount' =>
                max(
                    0,
                    (float)(
                        $_POST['penalty_amount']
                        ?? 0
                    )
                ),


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATIONS
            |--------------------------------------------------------------------------
            */

            'email_notifications' =>
                isset(
                    $_POST['email_notifications']
                )
                    ? '1'
                    : '0',

            'payment_notifications' =>
                isset(
                    $_POST['payment_notifications']
                )
                    ? '1'
                    : '0',

            'overdue_notifications' =>
                isset(
                    $_POST['overdue_notifications']
                )
                    ? '1'
                    : '0',


            /*
            |--------------------------------------------------------------------------
            | BACKWARD COMPATIBILITY
            |--------------------------------------------------------------------------
            */

            'overdue_reminders' =>
                isset(
                    $_POST['overdue_reminders']
                )
                    ? '1'
                    : '0',

            'payment_reminders' =>
                isset(
                    $_POST['payment_reminders']
                )
                    ? '1'
                    : '0',


            /*
            |--------------------------------------------------------------------------
            | SYSTEM
            |--------------------------------------------------------------------------
            */

            'maintenance_mode' =>
                isset(
                    $_POST['maintenance_mode']
                )
                    ? '1'
                    : '0',

            'allow_registration' =>
                isset(
                    $_POST['allow_registration']
                )
                    ? '1'
                    : '0',


            /*
            |--------------------------------------------------------------------------
            | SECURITY - SESSION TIMEOUT
            |--------------------------------------------------------------------------
            */

            'session_timeout' =>
                max(
                    5,
                    min(
                        1440,
                        (int)(
                            $_POST['session_timeout']
                            ?? 120
                        )
                    )
                ),


            /*
            |--------------------------------------------------------------------------
            | SECURITY - LOGIN ATTEMPTS
            |--------------------------------------------------------------------------
            */

            'login_attempts' =>
                max(
                    1,
                    min(
                        20,
                        (int)(
                            $_POST['login_attempts']
                            ?? 5
                        )
                    )
                )
        ];


        /*
        |--------------------------------------------------------------------------
        | VALIDATE PRIMARY COLOR
        |--------------------------------------------------------------------------
        */

        if (
            !preg_match(
                '/^#[a-fA-F0-9]{6}$/',
                $settings['primary_color']
            )
        ) {

            $settings['primary_color'] =
                '#2563eb';
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE TIMEZONE
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $settings['timezone'],
                timezone_identifiers_list(),
                true
            )
        ) {

            $settings['timezone'] =
                'Asia/Manila';
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE SESSION TIMEOUT
        |--------------------------------------------------------------------------
        */

        $settings['session_timeout'] =
            max(
                5,
                min(
                    1440,
                    (int)(
                        $settings['session_timeout']
                    )
                )
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDATE LOGIN ATTEMPTS
        |--------------------------------------------------------------------------
        */

        $settings['login_attempts'] =
            max(
                1,
                min(
                    20,
                    (int)(
                        $settings['login_attempts']
                    )
                )
            );


        /*
        |--------------------------------------------------------------------------
        | LOGO UPLOAD
        |--------------------------------------------------------------------------
        |
        | Supports:
        |
        | name="logo"
        |
        */

        if (
            isset(
                $_FILES['logo']
            )
            &&
            (
                $_FILES['logo']['error']
                !== UPLOAD_ERR_NO_FILE
            )
        ) {

            $file =
                $_FILES['logo'];


            /*
            |--------------------------------------------------------------------------
            | UPLOAD ERROR
            |--------------------------------------------------------------------------
            */

            if (
                $file['error']
                !== UPLOAD_ERR_OK
            ) {

                $_SESSION['error'] =
                    'Logo upload failed.';

                header(
                    'Location: index.php?url=settings'
                );

                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | FILE SIZE
            |--------------------------------------------------------------------------
            */

            $maxSize =
                2 * 1024 * 1024;


            if (
                $file['size']
                > $maxSize
            ) {

                $_SESSION['error'] =
                    'Logo must not exceed 2MB.';

                header(
                    'Location: index.php?url=settings'
                );

                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | MIME TYPE
            |--------------------------------------------------------------------------
            */

            $finfo =
                new finfo(
                    FILEINFO_MIME_TYPE
                );


            $mime =
                $finfo->file(
                    $file['tmp_name']
                );


            /*
            |--------------------------------------------------------------------------
            | ALLOWED IMAGE TYPES
            |--------------------------------------------------------------------------
            */

            $allowed = [

                'image/jpeg' =>
                    'jpg',

                'image/png' =>
                    'png',

                'image/webp' =>
                    'webp'
            ];


            /*
            |--------------------------------------------------------------------------
            | MIME VALIDATION
            |--------------------------------------------------------------------------
            */

            if (
                !isset(
                    $allowed[$mime]
                )
            ) {

                $_SESSION['error'] =
                    'Only JPG, PNG, and WEBP logos are allowed.';

                header(
                    'Location: index.php?url=settings'
                );

                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | UPLOAD DIRECTORY
            |--------------------------------------------------------------------------
            */

            $uploadDirectory =
                PUBLIC_PATH .
                '/uploads/settings/logo';


            /*
            |--------------------------------------------------------------------------
            | CREATE DIRECTORY
            |--------------------------------------------------------------------------
            */

            if (
                !is_dir(
                    $uploadDirectory
                )
            ) {

                if (
                    !mkdir(
                        $uploadDirectory,
                        0755,
                        true
                    )
                ) {

                    $_SESSION['error'] =
                        'Unable to create logo upload directory.';

                    header(
                        'Location: index.php?url=settings'
                    );

                    exit;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | FILE NAME
            |--------------------------------------------------------------------------
            */

            $fileName =
                'business_' .
                $businessId .
                '_' .
                time() .
                '.' .
                $allowed[$mime];


            /*
            |--------------------------------------------------------------------------
            | DESTINATION
            |--------------------------------------------------------------------------
            */

            $destination =
                $uploadDirectory .
                '/' .
                $fileName;


            /*
            |--------------------------------------------------------------------------
            | MOVE FILE
            |--------------------------------------------------------------------------
            */

            if (
                !move_uploaded_file(
                    $file['tmp_name'],
                    $destination
                )
            ) {

                $_SESSION['error'] =
                    'Unable to save logo.';

                header(
                    'Location: index.php?url=settings'
                );

                exit;
            }


            /*
            |--------------------------------------------------------------------------
            | SAVE LOGO PATH
            |--------------------------------------------------------------------------
            */

            $logoPath =
                'uploads/settings/logo/' .
                $fileName;


            /*
            |--------------------------------------------------------------------------
            | SAVE LOGO TO SETTINGS
            |--------------------------------------------------------------------------
            */

            $this->setting->set(
                $businessId,
                'sidebar_logo',
                $logoPath
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REMOVE LOGO
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $_POST['remove_logo']
            )
            &&
            $_POST['remove_logo'] === '1'
        ) {

            $this->setting->set(
                $businessId,
                'sidebar_logo',
                ''
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SAVE SETTINGS
        |--------------------------------------------------------------------------
        */

        $this->setting->setMany(
            $businessId,
            $settings
        );


        /*
        |--------------------------------------------------------------------------
        | SUCCESS MESSAGE
        |--------------------------------------------------------------------------
        */

        $_SESSION['success'] =
            'System settings updated successfully.';


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        header(
            'Location: index.php?url=settings'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD LOGO
    |--------------------------------------------------------------------------
    |
    | Kept for backward compatibility.
    |
    */

    public function uploadLogo(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ACCESS
        |--------------------------------------------------------------------------
        */

        $context =
            $this->checkAccess();


        /*
        |--------------------------------------------------------------------------
        | BUSINESS ID
        |--------------------------------------------------------------------------
        */

        $businessId =
            $context['business_id'];


        /*
        |--------------------------------------------------------------------------
        | REQUEST METHOD
        |--------------------------------------------------------------------------
        */

        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET')
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | SUPPORT BOTH FIELD NAMES
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $_FILES['logo']
            )
        ) {

            $file =
                $_FILES['logo'];

        } elseif (
            isset(
                $_FILES['sidebar_logo']
            )
        ) {

            $file =
                $_FILES['sidebar_logo'];

        } else {

            $_SESSION['error'] =
                'Please select a logo file.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | UPLOAD ERROR
        |--------------------------------------------------------------------------
        */

        if (
            $file['error']
            !== UPLOAD_ERR_OK
        ) {

            $_SESSION['error'] =
                'Logo upload failed.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | FILE SIZE
        |--------------------------------------------------------------------------
        */

        $maxSize =
            2 * 1024 * 1024;


        if (
            $file['size']
            > $maxSize
        ) {

            $_SESSION['error'] =
                'Logo must not exceed 2MB.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | MIME TYPE
        |--------------------------------------------------------------------------
        */

        $finfo =
            new finfo(
                FILEINFO_MIME_TYPE
            );


        $mime =
            $finfo->file(
                $file['tmp_name']
            );


        /*
        |--------------------------------------------------------------------------
        | ALLOWED IMAGE TYPES
        |--------------------------------------------------------------------------
        */

        $allowed = [

            'image/jpeg' =>
                'jpg',

            'image/png' =>
                'png',

            'image/webp' =>
                'webp'
        ];


        /*
        |--------------------------------------------------------------------------
        | MIME VALIDATION
        |--------------------------------------------------------------------------
        */

        if (
            !isset(
                $allowed[$mime]
            )
        ) {

            $_SESSION['error'] =
                'Only JPG, PNG, and WEBP logos are allowed.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | UPLOAD DIRECTORY
        |--------------------------------------------------------------------------
        */

        $uploadDirectory =
            PUBLIC_PATH .
            '/uploads/settings/logo';


        /*
        |--------------------------------------------------------------------------
        | CREATE DIRECTORY
        |--------------------------------------------------------------------------
        */

        if (
            !is_dir(
                $uploadDirectory
            )
        ) {

            if (
                !mkdir(
                    $uploadDirectory,
                    0755,
                    true
                )
            ) {

                $_SESSION['error'] =
                    'Unable to create logo upload directory.';

                header(
                    'Location: index.php?url=settings'
                );

                exit;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FILE NAME
        |--------------------------------------------------------------------------
        */

        $fileName =
            'business_' .
            $businessId .
            '_' .
            time() .
            '.' .
            $allowed[$mime];


        /*
        |--------------------------------------------------------------------------
        | DESTINATION
        |--------------------------------------------------------------------------
        */

        $destination =
            $uploadDirectory .
            '/' .
            $fileName;


        /*
        |--------------------------------------------------------------------------
        | MOVE FILE
        |--------------------------------------------------------------------------
        */

        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $destination
            )
        ) {

            $_SESSION['error'] =
                'Unable to save logo.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | SAVE LOGO PATH
        |--------------------------------------------------------------------------
        */

        $logoPath =
            'uploads/settings/logo/' .
            $fileName;


        $this->setting->set(
            $businessId,
            'sidebar_logo',
            $logoPath
        );


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $_SESSION['success'] =
            'Logo updated successfully.';


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        header(
            'Location: index.php?url=settings'
        );

        exit;
    }
}