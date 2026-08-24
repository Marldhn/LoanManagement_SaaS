
<?php

require_once APP_PATH . '/models/Expense.php';
require_once APP_PATH . '/models/Category.php';
require_once APP_PATH . '/models/Loan.php';
require_once APP_PATH . '/models/Account.php';

class LoanController
{
    private Loan $loan;

    public function __construct()
    {
        $this->loan = new Loan();
    }


    /*
    |--------------------------------------------------------------------------
    | LOAN LIST
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        AuthMiddleware::requireLogin();

        $user = Auth::user();
        $business = Auth::business();
        $businessId = Auth::businessId();
        $tenantRole = Auth::tenantRole();

        $loanModel = new Loan();

        // Get loans for the current business
        $loans = $loanModel->all($businessId);

        // Get borrowers for the create-loan modal
        $borrowers = $loanModel->borrowers($businessId);

        // Get loan categories
$categories = $loanModel->categories($businessId);
        $accountModel = new Account();

$accounts = $accountModel->getAll($businessId);

        // Success / error messages
        $success = $_SESSION['loan_success'] ?? '';
        $error = $_SESSION['loan_error'] ?? '';

        // Clear flash messages after reading them
        unset(
            $_SESSION['loan_success'],
            $_SESSION['loan_error']
        );

        require APP_PATH . '/views/loans/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE LOAN
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        AuthMiddleware::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=loans');
            exit;
        }

        $user = Auth::user();
        $businessId = Auth::businessId();

        $loanModel = new Loan();


        /*
        |--------------------------------------------------------------------------
        | FORM DATA
        |--------------------------------------------------------------------------
        */

        $borrowerId = (int)(
            $_POST['borrower_id'] ?? 0
        );

        $categoryId = !empty($_POST['category_id'])
            ? (int)$_POST['category_id']
            : null;

        $principalAmount = (float)(
            $_POST['principal_amount'] ?? 0
        );

        $interestRate = (float)(
            $_POST['interest_rate'] ?? 0
        );

        $interestType = trim(
            $_POST['interest_type'] ?? 'flat'
        );

        $accountId = (int)(
    $_POST['account_id'] ?? 0
);

        /*
         * PAYMENT TYPE
         *
         * Must match the form:
         *
         * name="payment_type"
         *
         * Allowed values:
         *
         * installment
         * full_payment
         */
        $paymentType = trim(
            $_POST['payment_type'] ?? 'installment'
        );

        $term = (int)(
            $_POST['term'] ?? 1
        );

        $termPeriod = trim(
            $_POST['term_period'] ?? 'months'
        );

        $processingFee = (float)(
            $_POST['processing_fee'] ?? 0
        );

        $releaseDate = !empty($_POST['release_date'])
            ? $_POST['release_date']
            : null;

        $firstPaymentDate = !empty($_POST['first_payment_date'])
            ? $_POST['first_payment_date']
            : null;

        $status = trim(
            $_POST['status'] ?? 'pending'
        );

        $purpose = trim(
            $_POST['purpose'] ?? ''
        );

        $notes = trim(
            $_POST['notes'] ?? ''
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($borrowerId <= 0) {

            $_SESSION['loan_error'] =
                'Please select a borrower.';

            header('Location: index.php?url=loans');
            exit;
        }

        if ($accountId <= 0) {
    $_SESSION['loan_error'] = 'Please select the account from which the loan will be released.';
    header('Location: index.php?url=loans');
    exit;
}


        if ($principalAmount <= 0) {

            $_SESSION['loan_error'] =
                'Principal amount must be greater than zero.';

            header('Location: index.php?url=loans');
            exit;
        }


        if ($interestRate < 0) {

            $_SESSION['loan_error'] =
                'Interest rate cannot be negative.';

            header('Location: index.php?url=loans');
            exit;
        }


        if ($term <= 0) {

            $_SESSION['loan_error'] =
                'Loan term must be greater than zero.';

            header('Location: index.php?url=loans');
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE INTEREST TYPE
        |--------------------------------------------------------------------------
        */

        $allowedInterestTypes = [
            'flat',
            'reducing_balance'
        ];

        if (!in_array(
            $interestType,
            $allowedInterestTypes,
            true
        )) {

            $_SESSION['loan_error'] =
                'Invalid interest type.';

            header('Location: index.php?url=loans');
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE PAYMENT TYPE
        |--------------------------------------------------------------------------
        */

        $allowedPaymentTypes = [
            'installment',
            'full_payment'
        ];

        if (!in_array(
            $paymentType,
            $allowedPaymentTypes,
            true
        )) {

            $_SESSION['loan_error'] =
                'Invalid payment type.';

            header('Location: index.php?url=loans');
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE TERM PERIOD
        |--------------------------------------------------------------------------
        */

        $allowedPeriods = [
            'days',
            'weeks',
            'months',
            'years'
        ];

        if (!in_array(
            $termPeriod,
            $allowedPeriods,
            true
        )) {

            $_SESSION['loan_error'] =
                'Invalid loan term period.';

            header('Location: index.php?url=loans');
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY BORROWER BELONGS TO BUSINESS
        |--------------------------------------------------------------------------
        */

        if (!$loanModel->borrowerBelongsToBusiness(
            $borrowerId,
            $businessId
        )) {

            $_SESSION['loan_error'] =
                'Invalid borrower selected.';

            header('Location: index.php?url=loans');
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | CALCULATE INTEREST
        |--------------------------------------------------------------------------
        */

        $totalInterest = 0;


        if ($interestType === 'flat') {

            /*
             * Flat interest:
             *
             * Principal × Rate × Term
             *
             * Example:
             *
             * ₱50,000
             * 5%
             * 12 months
             *
             * ₱50,000 × 0.05 × 12
             * = ₱30,000
             */

            $totalInterest =
                $principalAmount
                * ($interestRate / 100)
                * $term;

        } else {

            /*
             * Reducing balance:
             *
             * Simple amortization calculation.
             */

            $periods = $term;

            if ($periods > 0) {

                $periodicRate =
                    ($interestRate / 100);

                if ($periodicRate > 0) {

                    $payment =
                        $principalAmount
                        *
                        (
                            $periodicRate
                            *
                            pow(
                                1 + $periodicRate,
                                $periods
                            )
                        )
                        /
                        (
                            pow(
                                1 + $periodicRate,
                                $periods
                            )
                            - 1
                        );

                    $totalInterest =
                        ($payment * $periods)
                        - $principalAmount;

                } else {

                    $totalInterest = 0;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL PAYABLE
        |--------------------------------------------------------------------------
        */

        $totalPayable =
            $principalAmount
            + $totalInterest
            + $processingFee;


        /*
        |--------------------------------------------------------------------------
        | GENERATE LOAN NUMBER
        |--------------------------------------------------------------------------
        */

        $loanNumber =
            $loanModel->generateLoanNumber(
                $businessId
            );


        /*
        |--------------------------------------------------------------------------
        | CREATE LOAN
        |--------------------------------------------------------------------------
        */

        $loanId = $loanModel->create([

            'business_id' =>
                $businessId,

            'borrower_id' =>
                $borrowerId,

                'account_id' =>
    $accountId,

            'category_id' =>
                $categoryId,

            'loan_number' =>
                $loanNumber,

            'principal_amount' =>
                $principalAmount,

            'interest_rate' =>
                $interestRate,

            'interest_type' =>
                $interestType,

            /*
             * IMPORTANT:
             * This was missing before.
             */
            'payment_type' =>
                $paymentType,

            'term' =>
                $term,

            'term_period' =>
                $termPeriod,

            'processing_fee' =>
                $processingFee,

            'total_interest' =>
                $totalInterest,

            'total_payable' =>
                $totalPayable,

            'release_date' =>
                $releaseDate,

            'first_payment_date' =>
                $firstPaymentDate,

            'status' =>
                $status,

            'purpose' =>
                $purpose,

            'notes' =>
                $notes,

            'created_by' =>
                $user['id'] ?? null
        ]);



        if ($loanId) {

    try {

        $accountModel = new Account();

        $accountModel->deductForLoan(
            $accountId,
            $businessId,
            $principalAmount
        );

    } catch (Throwable $e) {

        $_SESSION['loan_error'] =
            $e->getMessage();

        header('Location: index.php?url=loans');
        exit;
    }
}


        /*
        |--------------------------------------------------------------------------
        | GENERATE PAYMENT SCHEDULE
        |--------------------------------------------------------------------------
        */

        if ($loanId) {

            $loanModel->generateSchedule(

                $loanId,

                $principalAmount,

                $totalInterest,

                $term,

                $termPeriod,

                $firstPaymentDate,

                /*
                 * IMPORTANT:
                 * Pass the selected payment type.
                 */
                $paymentType
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $_SESSION['loan_success'] =
            'Loan created successfully.';


        header('Location: index.php?url=loans');
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW LOAN
    |--------------------------------------------------------------------------
    */

    public function show(): void
    {
        AuthMiddleware::requireLogin();

        $businessId = Auth::businessId();

        $id = (int)(
            $_GET['id'] ?? 0
        );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header('Location: index.php?url=loans');
            exit;
        }


        $loanModel = new Loan();

        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header('Location: index.php?url=loans');
            exit;
        }


        $schedule =
            $loanModel->getSchedule(
                $id
            );


        $payments =
            $loanModel->getPayments(
                $id
            );


        $user = Auth::user();
        $business = Auth::business();
        $tenantRole = Auth::tenantRole();


        require APP_PATH . '/views/loans/show.php';
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE LOAN
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        AuthMiddleware::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header('Location: index.php?url=loans');
            exit;
        }


        $businessId = Auth::businessId();


        $id = (int)(
            $_POST['id'] ?? 0
        );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header('Location: index.php?url=loans');
            exit;
        }


        $loanModel = new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header('Location: index.php?url=loans');
            exit;
        }


        $borrowerId = (int)(
            $_POST['borrower_id']
            ?? $loan['borrower_id']
        );


        $categoryId = !empty($_POST['category_id'])
            ? (int)$_POST['category_id']
            : null;


        $status = trim(
            $_POST['status']
            ?? $loan['status']
        );


        $purpose = trim(
            $_POST['purpose']
            ?? ''
        );


        $notes = trim(
            $_POST['notes']
            ?? ''
        );


        $loanModel->update(
            $id,
            $businessId,
            [
                'borrower_id' =>
                    $borrowerId,

                'category_id' =>
                    $categoryId,

                'status' =>
                    $status,

                'purpose' =>
                    $purpose,

                'notes' =>
                    $notes
            ]
        );


        $_SESSION['loan_success'] =
            'Loan updated successfully.';


        header('Location: index.php?url=loans');
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE / CANCEL LOAN
    |--------------------------------------------------------------------------
    */

    public function delete(): void
    {
        AuthMiddleware::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header('Location: index.php?url=loans');
            exit;
        }


        $businessId = Auth::businessId();


        $id = (int)(
            $_POST['id'] ?? 0
        );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header('Location: index.php?url=loans');
            exit;
        }


        $loanModel = new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header('Location: index.php?url=loans');
            exit;
        }


        /*
         * We don't physically delete the loan.
         *
         * We mark it as cancelled so financial
         * history remains intact.
         */

        $loanModel->update(
            $id,
            $businessId,
            [
                'status' => 'cancelled'
            ]
        );


        $_SESSION['loan_success'] =
            'Loan cancelled successfully.';


        header('Location: index.php?url=loans');
        exit;
    }

        /*
    |--------------------------------------------------------------------------
    | REJECT LOAN
    |--------------------------------------------------------------------------
    */

    public function reject(): void
    {
        AuthMiddleware::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=loans');
            exit;
        }

        $businessId = Auth::businessId();

        $id = (int)(
            $_POST['id'] ?? 0
        );

        if ($id <= 0) {
            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel = new Loan();

        /*
         * Make sure the loan belongs
         * to the current business.
         */
        $loan = $loanModel->findByBusiness(
            $id,
            $businessId
        );

        if (!$loan) {
            $_SESSION['loan_error'] =
                'Loan not found.';

            header('Location: index.php?url=loans');
            exit;
        }

        /*
         * Only pending loans can be rejected.
         */
        if ($loan['status'] !== 'pending') {
            $_SESSION['loan_error'] =
                'Only pending loans can be rejected.';

            header('Location: index.php?url=loans');
            exit;
        }

        /*
         * Change loan status to rejected.
         */
        $updated = $loanModel->update(
            $id,
            $businessId,
            [
                'status' => 'rejected'
            ]
        );

        if (!$updated) {
            $_SESSION['loan_error'] =
                'Failed to reject the loan.';

            header('Location: index.php?url=loans');
            exit;
        }

        $_SESSION['loan_success'] =
            'Loan rejected successfully.';

        header('Location: index.php?url=loans');
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVE LOAN
    |--------------------------------------------------------------------------
    */

    public function approve(): void
    {
        AuthMiddleware::requireLogin();

        $businessId = Auth::businessId();


        $id = (int)(
            $_POST['id'] ?? 0
        );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header('Location: index.php?url=loans');
            exit;
        }


        $loanModel = new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header('Location: index.php?url=loans');
            exit;
        }


        if ($loan['status'] !== 'pending') {

            $_SESSION['loan_error'] =
                'Only pending loans can be approved.';

            header('Location: index.php?url=loans');
            exit;
        }


        $loanModel->update(
            $id,
            $businessId,
            [
                'status' => 'approved'
            ]
        );


        $_SESSION['loan_success'] =
            'Loan approved successfully.';


        header('Location: index.php?url=loans');
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | RELEASE LOAN
    |--------------------------------------------------------------------------
    */

    public function release(): void
    {
        AuthMiddleware::requireLogin();

        $businessId = Auth::businessId();


        $id = (int)(
            $_POST['id'] ?? 0
        );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header('Location: index.php?url=loans');
            exit;
        }


        $loanModel = new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header('Location: index.php?url=loans');
            exit;
        }


        if (!in_array(
            $loan['status'],
            [
                'approved',
                'pending'
            ],
            true
        )) {

            $_SESSION['loan_error'] =
                'This loan cannot be released.';

            header('Location: index.php?url=loans');
            exit;
        }


        $releaseDate =
            date('Y-m-d');


        $loanModel->update(
            $id,
            $businessId,
            [
                'status' =>
                    'active',

                'release_date' =>
                    $releaseDate
            ]
        );


        $_SESSION['loan_success'] =
            'Loan released successfully.';


        header('Location: index.php?url=loans');
        exit;
    }
}
