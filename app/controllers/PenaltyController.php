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


        /*
        |--------------------------------------------------------------------------
        | GET PENALTIES
        |--------------------------------------------------------------------------
        */

        $penalties =
            $this->penalty->getAll(
                $businessId
            );


        /*
        |--------------------------------------------------------------------------
        | GET ALL LOANS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | These are the loans that will appear
        | inside the Add Penalty modal.
        |
        */

        $loans =
            $this->penalty->getLoans(
                $businessId
            );


        /*
        |--------------------------------------------------------------------------
        | GET ALL LOAN SCHEDULES
        |--------------------------------------------------------------------------
        |
        | These are the installments that will
        | appear after selecting a loan.
        |
        */

        $loanSchedules =
            $this->penalty->getLoanSchedules(
                $businessId
            );


        /*
        |--------------------------------------------------------------------------
        | PENALTY STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalPenalties =
            $this->penalty->getTotalPenalties(
                $businessId
            );


        $thisMonthPenalties =
            $this->penalty->getThisMonthPenalties(
                $businessId
            );


        $penaltyCount =
            $this->penalty->getCount(
                $businessId
            );


        /*
        |--------------------------------------------------------------------------
        | LOAD PENALTY INDEX
        |--------------------------------------------------------------------------
        */

        require APP_PATH .
            '/views/penalties/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    public function view(int $id): void
    {
        $businessId =
            $this->checkAccess();


        /*
        |--------------------------------------------------------------------------
        | GET PENALTY
        |--------------------------------------------------------------------------
        */

        $penalty =
            $this->penalty->find(
                $id,
                $businessId
            );


        /*
        |--------------------------------------------------------------------------
        | NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$penalty) {
            http_response_code(404);

            die(
                'Penalty not found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD VIEW
        |--------------------------------------------------------------------------
        */

        require APP_PATH .
            '/views/penalties/index.php';
    }
}