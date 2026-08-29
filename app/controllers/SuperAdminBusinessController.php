<?php

class SuperAdminBusinessController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESS CHECK
    |--------------------------------------------------------------------------
    */

    private function checkAccess(): array
    {
        if (!Auth::check()) {
            header('Location: index.php?url=auth/login');
            exit;
        }

        $user = Auth::user();

        $role = $user['role'] ?? '';

        if ($role !== 'super_admin') {
            http_response_code(403);
            die('Access denied.');
        }

        return $user;
    }


    /*
    |--------------------------------------------------------------------------
    | BUSINESS LIST
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $user = $this->checkAccess();

        /*
        |--------------------------------------------------------------------------
        | GET ALL BUSINESSES
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                b.id,
                b.name,
                b.slug,
                b.email,
                b.phone,
                b.address,
                b.status,
                b.created_at,

                (
                    SELECT COUNT(*)
                    FROM business_users bu
                    WHERE bu.business_id = b.id
                ) AS user_count,

                (
                    SELECT COUNT(*)
                    FROM loans l
                    WHERE l.business_id = b.id
                ) AS loan_count

            FROM businesses b

            ORDER BY b.created_at DESC, b.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        $businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);


        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalBusinesses = count($businesses);

        $activeBusinesses = 0;

        $inactiveBusinesses = 0;

        $totalUsers = 0;

        $totalLoans = 0;


        foreach ($businesses as $business) {

            if (
                ($business['status'] ?? '') === 'active'
            ) {
                $activeBusinesses++;
            } else {
                $inactiveBusinesses++;
            }

            $totalUsers += (int) (
                $business['user_count'] ?? 0
            );

            $totalLoans += (int) (
                $business['loan_count'] ?? 0
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        require APP_PATH
            . '/views/super_admin/businesses/index.php';
    }
}