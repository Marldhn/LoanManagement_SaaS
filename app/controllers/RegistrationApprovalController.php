<?php

declare(strict_types=1);

class RegistrationApprovalController
{
    private RegistrationApproval $registrationApproval;

    public function __construct()
    {
        $this->registrationApproval = new RegistrationApproval();
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    private function checkSuperAdmin(): void
    {
        if (!Auth::check()) {

            header(
                'Location: ' .
                BASE_URL .
                '/index.php?page=login'
            );

            exit;
        }

        if (!Auth::isSuperAdmin()) {

            http_response_code(403);

            echo '<!DOCTYPE html>';
            echo '<html>';
            echo '<head>';
            echo '<title>403 - Access Denied</title>';
            echo '<style>';
            echo 'body{font-family:Arial;background:#f5f7fb;padding:50px;}';
            echo '.box{background:white;padding:30px;border-radius:10px;max-width:600px;margin:auto;}';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            echo '<div class="box">';
            echo '<h1>403 - Access Denied</h1>';
            echo '<p>You do not have permission to access this page.</p>';
            echo '</div>';
            echo '</body>';
            echo '</html>';

            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LIST PENDING REGISTRATIONS
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $this->checkSuperAdmin();

        $registrations =
            $this->registrationApproval->getPendingRegistrations();

        $error = $_SESSION['approval_error'] ?? '';
        $success = $_SESSION['approval_success'] ?? '';

        unset($_SESSION['approval_error']);
        unset($_SESSION['approval_success']);

        require BASE_PATH .
            '/app/views/super_admin/registrations.php';
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve(): void
    {
        $this->checkSuperAdmin();

        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId <= 0) {

            $_SESSION['approval_error'] =
                'Invalid registration.';

            header(
                'Location: ' .
                BASE_URL .
                '/index.php?page=registration_approvals'
            );

            exit;
        }

        try {

            $this->registrationApproval->approve($userId);

            $_SESSION['approval_success'] =
                'Registration approved successfully.';

        } catch (Throwable $e) {

            $_SESSION['approval_error'] =
                $e->getMessage();
        }

        header(
            'Location: ' .
            BASE_URL .
            '/index.php?page=registration_approvals'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(): void
    {
        $this->checkSuperAdmin();

        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId <= 0) {

            $_SESSION['approval_error'] =
                'Invalid registration.';

            header(
                'Location: ' .
                BASE_URL .
                '/index.php?page=registration_approvals'
            );

            exit;
        }

        try {

            $this->registrationApproval->reject($userId);

            $_SESSION['approval_success'] =
                'Registration rejected successfully.';

        } catch (Throwable $e) {

            $_SESSION['approval_error'] =
                $e->getMessage();
        }

        header(
            'Location: ' .
            BASE_URL .
            '/index.php?page=registration_approvals'
        );

        exit;
    }
}