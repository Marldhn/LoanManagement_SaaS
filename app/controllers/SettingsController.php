<?php

class SettingsController
{
    private Setting $setting;


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
        if (!Auth::check()) {

            header(
                'Location: index.php?url=auth/login'
            );

            exit;
        }


        $businessId =
            (int)(
                $_SESSION['business_id']
                ?? 0
            );


        $userId =
            (int)(
                $_SESSION['user_id']
                ?? 0
            );


        if ($businessId <= 0) {

            http_response_code(403);

            exit(
                'Business account not found.'
            );
        }


        $user =
            Auth::user();


        $business =
            Auth::business();


        $tenantRole =
            Auth::tenantRole();


        /*
        |--------------------------------------------------------------------------
        | SETTINGS PERMISSIONS
        |--------------------------------------------------------------------------
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


        return [
            'business_id' => $businessId,
            'user_id' => $userId,
            'user' => $user,
            'business' => $business,
            'tenantRole' => $tenantRole
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $context =
            $this->checkAccess();


        $businessId =
            $context['business_id'];


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

            'sidebar_logo' =>
                '',

            'login_logo' =>
                '',

            'favicon' =>
                '',

            'primary_color' =>
                '#2563eb',

            'loan_number_prefix' =>
                'LN',

            'payment_number_prefix' =>
                'PAY',

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

            'enable_penalty' =>
                '0',

            'penalty_type' =>
                'fixed',

            'penalty_rate' =>
                '0',

            'penalty_amount' =>
                '0',

            'maintenance_mode' =>
                '0',

            'allow_registration' =>
                '1',

            'overdue_reminders' =>
                '1',

            'payment_reminders' =>
                '1'
        ];


        $settings =
            array_merge(
                $defaults,
                $settings
            );


        extract($context);


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
        $context =
            $this->checkAccess();


        $businessId =
            $context['business_id'];


        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET')
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


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
                (int)(
                    $_POST['default_term']
                    ?? 1
                ),

            'default_term_period' =>
                $_POST['default_term_period']
                ?? 'months',

            'default_interest_rate' =>
                (float)(
                    $_POST['default_interest_rate']
                    ?? 0
                ),

            'default_processing_fee' =>
                (float)(
                    $_POST['default_processing_fee']
                    ?? 0
                ),


            /*
            |--------------------------------------------------------------------------
            | COLLECTION
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
                (float)(
                    $_POST['penalty_rate']
                    ?? 0
                ),

            'penalty_amount' =>
                (float)(
                    $_POST['penalty_amount']
                    ?? 0
                ),


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATIONS
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
                    : '0'
        ];


        /*
        |--------------------------------------------------------------------------
        | VALIDATE COLOR
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
        | TIMEZONE
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
        | SAVE
        |--------------------------------------------------------------------------
        */

        $this->setting->setMany(
            $businessId,
            $settings
        );


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $_SESSION['success'] =
            'System settings updated successfully.';


        header(
            'Location: index.php?url=settings'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | LOGO UPLOAD
    |--------------------------------------------------------------------------
    */

    public function uploadLogo(): void
    {
        $context =
            $this->checkAccess();


        $businessId =
            $context['business_id'];


        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET')
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        if (
            !isset(
                $_FILES['sidebar_logo']
            )
        ) {

            $_SESSION['error'] =
                'Please select a logo file.';

            header(
                'Location: index.php?url=settings'
            );

            exit;
        }


        $file =
            $_FILES['sidebar_logo'];


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
        | SIZE
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
        | MIME
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


        $allowed = [

            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];


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
        | DIRECTORY
        |--------------------------------------------------------------------------
        */

        $uploadDirectory =
            PUBLIC_PATH .
            '/uploads/settings/logo';


        if (
            !is_dir(
                $uploadDirectory
            )
        ) {

            mkdir(
                $uploadDirectory,
                0755,
                true
            );
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


        $destination =
            $uploadDirectory .
            '/' .
            $fileName;


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
        | SAVE PATH
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


        $_SESSION['success'] =
            'Logo updated successfully.';


        header(
            'Location: index.php?url=settings'
        );

        exit;
    }
}