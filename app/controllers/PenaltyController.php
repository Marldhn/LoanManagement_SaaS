<?php

class PenaltyController
{
    private Penalty $penalty;

    public function __construct()
    {
        $this->penalty = new Penalty();
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESS CHECK
    |--------------------------------------------------------------------------
    */

    private function checkAccess(): int
    {
        if (!Auth::check()) {
            header('Location: index.php?page=login');
            exit;
        }

        $businessId = (int) ($_SESSION['business_id'] ?? 0);

        if ($businessId <= 0) {
            die('Business account not found.');
        }

        return $businessId;
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $businessId = $this->checkAccess();

        $penalties = $this->penalty->getAll($businessId);

        $totalPenalties =
            $this->penalty->getTotalPenalties($businessId);

        $thisMonthPenalties =
            $this->penalty->getThisMonthPenalties($businessId);

        $penaltyCount =
            $this->penalty->getCount($businessId);

        require APP_PATH . '/views/penalties/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    public function view(int $id): void
    {
        $businessId = $this->checkAccess();

        $penalty =
            $this->penalty->find(
                $id,
                $businessId
            );

        if (!$penalty) {
            http_response_code(404);
            die('Penalty not found.');
        }

        require APP_PATH . '/views/penalties/index.php';
    }
}