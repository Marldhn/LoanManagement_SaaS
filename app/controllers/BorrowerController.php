<?php

class BorrowerController
{
    private Borrower $borrower;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        Auth::startSession();

        $this->borrower = new Borrower();
    }


    /*
    |--------------------------------------------------------------------------
    | REQUIRE BUSINESS
    |--------------------------------------------------------------------------
    */

    private function requireBusiness(): int
    {
        if (!Auth::check()) {

            header(
                'Location: index.php?url=auth/login'
            );

            exit;
        }


        $businessId = Auth::businessId();


        if (!$businessId) {

            http_response_code(403);

            echo '<h1>403 - Access Denied</h1>';

            echo '<p>This page is only available to business users.</p>';

            exit;
        }


        return (int)$businessId;
    }


    /*
    |--------------------------------------------------------------------------
    | BORROWER LIST
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $businessId = $this->requireBusiness();


        $user = Auth::user();

        $business = Auth::business();

        $tenantRole = Auth::tenantRole();


        $borrowers =
            $this->borrower
                ->getAllByBusiness(
                    $businessId
                );


        $currentUrl = 'borrowers';


        require APP_PATH .
            '/views/borrowers/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | BORROWER DETAILS
    |--------------------------------------------------------------------------
    */

    public function details(): void
    {
        $businessId = $this->requireBusiness();


        $id = (int)(
            $_GET['id'] ?? 0
        );


        if ($id <= 0) {

            header(
                'Location: index.php?url=borrowers'
            );

            exit;
        }


        /*
         * Get borrower.
         *
         * The model checks the business_id,
         * so users cannot access another
         * business's borrower.
         */

        $borrower =
            $this->borrower
                ->findById(
                    $id,
                    $businessId
                );


        if (!$borrower) {

            http_response_code(404);

            echo '<h1>404 - Borrower Not Found</h1>';

            echo '<p>The borrower does not exist.</p>';

            echo '<br>';

            echo '
                <a href="index.php?url=borrowers">
                    Back to Borrowers
                </a>
            ';

            exit;
        }


        /*
         * Get all loans belonging to
         * this borrower.
         */

        $loans =
            $this->borrower
                ->getLoansByBorrower(
                    $id,
                    $businessId
                );


        /*
         * Calculate borrower loan summary.
         */

        $totalLoans = count($loans);

        $totalPrincipal = 0;

        $totalPayable = 0;

        $totalPaid = 0;

        $activeLoans = 0;

        $completedLoans = 0;

        $pendingLoans = 0;

        $overdueLoans = 0;


        foreach ($loans as $loan) {

            $totalPrincipal +=
                (float)(
                    $loan['principal_amount']
                    ?? 0
                );


            $totalPayable +=
                (float)(
                    $loan['total_payable']
                    ?? 0
                );


            $totalPaid +=
                (float)(
                    $loan['total_paid']
                    ?? 0
                );


            $loanStatus =
                $loan['status']
                ?? 'pending';


            if ($loanStatus === 'active') {

                $activeLoans++;

            } elseif ($loanStatus === 'completed') {

                $completedLoans++;

            } elseif ($loanStatus === 'pending') {

                $pendingLoans++;

            } elseif ($loanStatus === 'overdue') {

                $overdueLoans++;
            }
        }


        /*
         * Remaining balance.
         */

        $remainingBalance =
            $totalPayable - $totalPaid;


        if ($remainingBalance < 0) {

            $remainingBalance = 0;
        }


        $user = Auth::user();

        $business = Auth::business();

        $tenantRole = Auth::tenantRole();

        $currentUrl = 'borrowers';


        require APP_PATH .
            '/views/borrowers/details.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE BORROWER
    |--------------------------------------------------------------------------
    */

    public function create(): void
    {
        $businessId = $this->requireBusiness();


        $user = Auth::user();

        $business = Auth::business();

        $tenantRole = Auth::tenantRole();


        $borrowerCode =
            $this->borrower
                ->generateCode(
                    $businessId
                );


        $error = null;


        $currentUrl =
            'borrowers/create';


        require APP_PATH .
            '/views/borrowers/create.php';
    }


    /*
    |--------------------------------------------------------------------------
    | STORE BORROWER
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        $businessId = $this->requireBusiness();


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header(
                'Location: index.php?url=borrowers/create'
            );

            exit;
        }


        $user = Auth::user();


        $data = [

            'borrower_code' =>
                trim(
                    $_POST['borrower_code'] ?? ''
                ),

            'first_name' =>
                trim(
                    $_POST['first_name'] ?? ''
                ),

            'middle_name' =>
                trim(
                    $_POST['middle_name'] ?? ''
                ),

            'last_name' =>
                trim(
                    $_POST['last_name'] ?? ''
                ),

            'email' =>
                trim(
                    $_POST['email'] ?? ''
                ),

            'phone' =>
                trim(
                    $_POST['phone'] ?? ''
                ),

            'date_of_birth' =>
                !empty(
                    $_POST['date_of_birth'] ?? ''
                )
                    ? $_POST['date_of_birth']
                    : null,

            'gender' =>
                !empty(
                    $_POST['gender'] ?? ''
                )
                    ? $_POST['gender']
                    : null,

            'address' =>
                trim(
                    $_POST['address'] ?? ''
                ),

            'city' =>
                trim(
                    $_POST['city'] ?? ''
                ),

            'province' =>
                trim(
                    $_POST['province'] ?? ''
                ),

            'postal_code' =>
                trim(
                    $_POST['postal_code'] ?? ''
                ),

            'occupation' =>
                trim(
                    $_POST['occupation'] ?? ''
                ),

            'employer' =>
                trim(
                    $_POST['employer'] ?? ''
                ),

            'monthly_income' =>
                (float)(
                    $_POST['monthly_income'] ?? 0
                ),

            'status' =>
                $_POST['status'] ?? 'active',

            'notes' =>
                trim(
                    $_POST['notes'] ?? ''
                )
        ];


        /*
         * Required fields.
         */

        if (
            $data['first_name'] === ''
            ||
            $data['last_name'] === ''
        ) {

            $error =
                'First name and last name are required.';


            $user = Auth::user();

            $business = Auth::business();

            $tenantRole = Auth::tenantRole();


            $borrowerCode =
                $data['borrower_code'];


            $currentUrl =
                'borrowers/create';


            require APP_PATH .
                '/views/borrowers/create.php';

            return;
        }


        /*
         * Generate code if missing.
         */

        if (
            $data['borrower_code'] === ''
        ) {

            $data['borrower_code'] =
                $this->borrower
                    ->generateCode(
                        $businessId
                    );
        }


        /*
         * Create borrower.
         */

        $created =
            $this->borrower->create(
                $businessId,
                $data,
                $user['id'] ?? null
            );


        if (!$created) {

            $error =
                'Unable to create borrower. Please try again.';


            $user = Auth::user();

            $business = Auth::business();

            $tenantRole = Auth::tenantRole();


            $borrowerCode =
                $data['borrower_code'];


            $currentUrl =
                'borrowers/create';


            require APP_PATH .
                '/views/borrowers/create.php';

            return;
        }


        header(
            'Location: index.php?url=borrowers'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT BORROWER
    |--------------------------------------------------------------------------
    */

    public function edit(): void
    {
        $businessId = $this->requireBusiness();


        $id = (int)(
            $_GET['id'] ?? 0
        );


        if ($id <= 0) {

            header(
                'Location: index.php?url=borrowers'
            );

            exit;
        }


        $borrower =
            $this->borrower
                ->findById(
                    $id,
                    $businessId
                );


        if (!$borrower) {

            http_response_code(404);

            echo '<h1>404 - Borrower Not Found</h1>';

            echo '<p>The borrower does not exist.</p>';

            exit;
        }


        $user = Auth::user();

        $business = Auth::business();

        $tenantRole = Auth::tenantRole();

        $error = null;

        $currentUrl =
            'borrowers/edit';


        require APP_PATH .
            '/views/borrowers/edit.php';
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE BORROWER
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        $businessId = $this->requireBusiness();


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header(
                'Location: index.php?url=borrowers'
            );

            exit;
        }


        $id = (int)(
            $_POST['id'] ?? 0
        );


        if ($id <= 0) {

            header(
                'Location: index.php?url=borrowers'
            );

            exit;
        }


        $data = [

            'first_name' =>
                trim(
                    $_POST['first_name'] ?? ''
                ),

            'middle_name' =>
                trim(
                    $_POST['middle_name'] ?? ''
                ),

            'last_name' =>
                trim(
                    $_POST['last_name'] ?? ''
                ),

            'email' =>
                trim(
                    $_POST['email'] ?? ''
                ),

            'phone' =>
                trim(
                    $_POST['phone'] ?? ''
                ),

            'date_of_birth' =>
                !empty(
                    $_POST['date_of_birth'] ?? ''
                )
                    ? $_POST['date_of_birth']
                    : null,

            'gender' =>
                !empty(
                    $_POST['gender'] ?? ''
                )
                    ? $_POST['gender']
                    : null,

            'address' =>
                trim(
                    $_POST['address'] ?? ''
                ),

            'city' =>
                trim(
                    $_POST['city'] ?? ''
                ),

            'province' =>
                trim(
                    $_POST['province'] ?? ''
                ),

            'postal_code' =>
                trim(
                    $_POST['postal_code'] ?? ''
                ),

            'occupation' =>
                trim(
                    $_POST['occupation'] ?? ''
                ),

            'employer' =>
                trim(
                    $_POST['employer'] ?? ''
                ),

            'monthly_income' =>
                (float)(
                    $_POST['monthly_income'] ?? 0
                ),

            'status' =>
                $_POST['status'] ?? 'active',

            'notes' =>
                trim(
                    $_POST['notes'] ?? ''
                )
        ];


        if (
            $data['first_name'] === ''
            ||
            $data['last_name'] === ''
        ) {

            $error =
                'First name and last name are required.';


            $borrower =
                $this->borrower
                    ->findById(
                        $id,
                        $businessId
                    );


            $borrower =
                array_merge(
                    $borrower ?? [],
                    $data
                );


            $user = Auth::user();

            $business = Auth::business();

            $tenantRole = Auth::tenantRole();

            $currentUrl =
                'borrowers/edit';


            require APP_PATH .
                '/views/borrowers/edit.php';

            return;
        }


        $updated =
            $this->borrower->update(
                $id,
                $businessId,
                $data
            );


        if (!$updated) {

            $error =
                'Unable to update borrower. Please try again.';


            $borrower =
                $this->borrower
                    ->findById(
                        $id,
                        $businessId
                    );


            $borrower =
                array_merge(
                    $borrower ?? [],
                    $data
                );


            $user = Auth::user();

            $business = Auth::business();

            $tenantRole = Auth::tenantRole();

            $currentUrl =
                'borrowers/edit';


            require APP_PATH .
                '/views/borrowers/edit.php';

            return;
        }


        header(
            'Location: index.php?url=borrowers'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE BORROWER
    |--------------------------------------------------------------------------
    */

    public function delete(): void
    {
        $businessId = $this->requireBusiness();


        $id = (int)(
            $_GET['id'] ?? 0
        );


        if ($id > 0) {

            $this->borrower
                ->delete(
                    $id,
                    $businessId
                );
        }


        header(
            'Location: index.php?url=borrowers'
        );

        exit;
    }
}