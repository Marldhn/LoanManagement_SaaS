<?php

declare(strict_types=1);

class RegistrationController
{
    private Registration $registration;

    public function __construct()
    {
        $this->registration = new Registration();
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW REGISTER
    |--------------------------------------------------------------------------
    */

    public function showRegister(): void
    {
        $error = $_SESSION['register_error'] ?? '';
        $success = $_SESSION['register_success'] ?? '';

        unset($_SESSION['register_error']);
        unset($_SESSION['register_success']);

        require BASE_PATH . '/app/views/auth/register.php';
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $businessName = trim($_POST['business_name'] ?? '');
        $businessEmail = trim($_POST['business_email'] ?? '');
        $businessPhone = trim($_POST['business_phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        /*
        |--------------------------------------------------------------------------
        | REQUIRED FIELDS
        |--------------------------------------------------------------------------
        */

        if (
            $name === '' ||
            $email === '' ||
            $password === '' ||
            $confirmPassword === '' ||
            $businessName === ''
        ) {
            $this->error(
                'Please fill in all required fields.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | EMAIL VALIDATION
        |--------------------------------------------------------------------------
        */

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error(
                'Please enter a valid email address.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | BUSINESS EMAIL VALIDATION
        |--------------------------------------------------------------------------
        */

        if (
            $businessEmail !== '' &&
            !filter_var($businessEmail, FILTER_VALIDATE_EMAIL)
        ) {
            $this->error(
                'Please enter a valid business email address.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PASSWORD LENGTH
        |--------------------------------------------------------------------------
        */

        if (strlen($password) < 8) {
            $this->error(
                'Password must be at least 8 characters long.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PASSWORD CONFIRMATION
        |--------------------------------------------------------------------------
        */

        if ($password !== $confirmPassword) {
            $this->error(
                'Passwords do not match.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK EXISTING EMAIL
        |--------------------------------------------------------------------------
        */

        if ($this->registration->emailExists($email)) {
            $this->error(
                'An account with this email address already exists.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE REGISTRATION
        |--------------------------------------------------------------------------
        */

        try {

            $this->registration->register([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'business_name' => $businessName,
                'business_email' => $businessEmail !== ''
                    ? $businessEmail
                    : null,
                'business_phone' => $businessPhone !== ''
                    ? $businessPhone
                    : null,
                'address' => $address !== ''
                    ? $address
                    : null
            ]);

            $_SESSION['register_success'] =
                'Registration submitted successfully. ' .
                'Your account is now waiting for Super Admin approval.';

            header(
                'Location: ' .
                BASE_URL .
                '/index.php?page=register'
            );

            exit;

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | SHOW ACTUAL ERROR DURING DEVELOPMENT
            |--------------------------------------------------------------------------
            */

            $_SESSION['register_error'] =
                $e->getMessage();

            header(
                'Location: ' .
                BASE_URL .
                '/index.php?page=register'
            );

            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ERROR
    |--------------------------------------------------------------------------
    */

    private function error(string $message): void
    {
        $_SESSION['register_error'] = $message;

        header(
            'Location: ' .
            BASE_URL .
            '/index.php?page=register'
        );

        exit;
    }
}