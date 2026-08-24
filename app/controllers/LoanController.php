<?php

require_once APP_PATH . '/models/Expense.php';
require_once APP_PATH . '/models/Category.php';
require_once APP_PATH . '/models/Loan.php';
require_once APP_PATH . '/models/Account.php';

class LoanController
{
    private Loan $loan;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

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


        /*
         * Get loans for current business.
         */

        $loans =
            $loanModel->all(
                $businessId
            );


        /*
         * Get borrowers.
         */

        $borrowers =
            $loanModel->borrowers(
                $businessId
            );


        /*
         * Get loan categories.
         */

        $categories =
            $loanModel->categories(
                $businessId
            );


        /*
         * Get accounts.
         */

        $accountModel =
            new Account();

        $accounts =
            $accountModel->getAll(
                $businessId
            );


        /*
         * Success / error messages.
         */

        $success =
            $_SESSION['loan_success']
            ?? '';

        $error =
            $_SESSION['loan_error']
            ?? '';


        /*
         * Clear flash messages.
         */

        unset(
            $_SESSION['loan_success'],
            $_SESSION['loan_error']
        );


        require APP_PATH .
            '/views/loans/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE LOAN
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        AuthMiddleware::requireLogin();

        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $user =
            Auth::user();

        $businessId =
            Auth::businessId();

        $loanModel =
            new Loan();


        /*
        |--------------------------------------------------------------------------
        | FORM DATA
        |--------------------------------------------------------------------------
        */

        $borrowerId =
            (int)(
                $_POST['borrower_id']
                ?? 0
            );


        $categoryId =
            !empty(
                $_POST['category_id']
            )
            ? (int)$_POST['category_id']
            : null;


        $principalAmount =
            (float)(
                $_POST['principal_amount']
                ?? 0
            );


        $interestRate =
            (float)(
                $_POST['interest_rate']
                ?? 0
            );


        $interestType =
            trim(
                $_POST['interest_type']
                ?? 'flat'
            );


        $accountId =
            (int)(
                $_POST['account_id']
                ?? 0
            );


        /*
         * PAYMENT TYPE
         */

        $paymentType =
            trim(
                $_POST['payment_type']
                ?? 'installment'
            );


        $term =
            (int)(
                $_POST['term']
                ?? 1
            );


        $termPeriod =
            trim(
                $_POST['term_period']
                ?? 'months'
            );


        $processingFee =
            (float)(
                $_POST['processing_fee']
                ?? 0
            );


        $releaseDate =
            !empty(
                $_POST['release_date']
            )
            ? $_POST['release_date']
            : null;


        $firstPaymentDate =
            !empty(
                $_POST['first_payment_date']
            )
            ? $_POST['first_payment_date']
            : null;


        $status =
            trim(
                $_POST['status']
                ?? 'pending'
            );


        $purpose =
            trim(
                $_POST['purpose']
                ?? ''
            );


        $notes =
            trim(
                $_POST['notes']
                ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($borrowerId <= 0) {

            $_SESSION['loan_error'] =
                'Please select a borrower.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        if ($accountId <= 0) {

            $_SESSION['loan_error'] =
                'Please select the account from which the loan will be released.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        if ($principalAmount <= 0) {

            $_SESSION['loan_error'] =
                'Principal amount must be greater than zero.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        if ($interestRate < 0) {

            $_SESSION['loan_error'] =
                'Interest rate cannot be negative.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        if ($term <= 0) {

            $_SESSION['loan_error'] =
                'Loan term must be greater than zero.';

            header(
                'Location: index.php?url=loans'
            );

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

            header(
                'Location: index.php?url=loans'
            );

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

            header(
                'Location: index.php?url=loans'
            );

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

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY BORROWER BELONGS TO BUSINESS
        |--------------------------------------------------------------------------
        */

        if (
            !$loanModel->borrowerBelongsToBusiness(
                $borrowerId,
                $businessId
            )
        ) {

            $_SESSION['loan_error'] =
                'Invalid borrower selected.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | CALCULATE INTEREST
        |--------------------------------------------------------------------------
        */

        $totalInterest = 0;


        if (
            $interestType === 'flat'
        ) {

            /*
             * Flat interest:
             *
             * Principal × Rate × Term
             */

            $totalInterest =
                $principalAmount
                *
                ($interestRate / 100)
                *
                $term;

        } else {

            /*
             * Reducing balance.
             */

            $periods =
                $term;


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
                        (
                            $payment
                            *
                            $periods
                        )
                        -
                        $principalAmount;

                } else {

                    $totalInterest =
                        0;
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
            +
            $totalInterest
            +
            $processingFee;


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

        $loanId =
            $loanModel->create([

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
                    $user['id']
                    ?? null
            ]);


        /*
        |--------------------------------------------------------------------------
        | DEDUCT LOAN FROM ACCOUNT
        |--------------------------------------------------------------------------
        */

        if ($loanId) {

            try {

                $accountModel =
                    new Account();


                $accountModel->deductForLoan(
                    $accountId,
                    $businessId,
                    $principalAmount
                );

            } catch (
                Throwable $e
            ) {

                $_SESSION['loan_error'] =
                    $e->getMessage();

                header(
                    'Location: index.php?url=loans'
                );

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


        header(
            'Location: index.php?url=loans'
        );

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

        $businessId =
            Auth::businessId();


        $id =
            (int)(
                $_GET['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $loanModel =
            new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header(
                'Location: index.php?url=loans'
            );

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


        $user =
            Auth::user();

        $business =
            Auth::business();

        $tenantRole =
            Auth::tenantRole();


        require APP_PATH .
            '/views/loans/show.php';
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE LOAN
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        AuthMiddleware::requireLogin();


        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $businessId =
            Auth::businessId();


        $id =
            (int)(
                $_POST['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $loanModel =
            new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $borrowerId =
            (int)(
                $_POST['borrower_id']
                ??
                $loan['borrower_id']
            );


        $categoryId =
            !empty(
                $_POST['category_id']
            )
            ? (int)$_POST['category_id']
            : null;


        $status =
            trim(
                $_POST['status']
                ??
                $loan['status']
            );


        $purpose =
            trim(
                $_POST['purpose']
                ?? ''
            );


        $notes =
            trim(
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


        header(
            'Location: index.php?url=loans'
        );

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


        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $businessId =
            Auth::businessId();


        $id =
            (int)(
                $_POST['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $loanModel =
            new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header(
                'Location: index.php?url=loans'
            );

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
                'status' =>
                    'cancelled'
            ]
        );


        $_SESSION['loan_success'] =
            'Loan cancelled successfully.';


        header(
            'Location: index.php?url=loans'
        );

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


        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $businessId =
            Auth::businessId();


        $id =
            (int)(
                $_POST['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $loanModel =
            new Loan();


        /*
         * Make sure the loan belongs
         * to the current business.
         */

        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        /*
         * Only pending loans can be rejected.
         */

        if (
            $loan['status']
            !== 'pending'
        ) {

            $_SESSION['loan_error'] =
                'Only pending loans can be rejected.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        /*
         * Change loan status to rejected.
         */

        $updated =
            $loanModel->update(
                $id,
                $businessId,
                [
                    'status' =>
                        'rejected'
                ]
            );


        if (!$updated) {

            $_SESSION['loan_error'] =
                'Failed to reject the loan.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $_SESSION['loan_success'] =
            'Loan rejected successfully.';


        header(
            'Location: index.php?url=loans'
        );

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


        $businessId =
            Auth::businessId();


        $id =
            (int)(
                $_POST['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $loanModel =
            new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        if (
            $loan['status']
            !== 'pending'
        ) {

            $_SESSION['loan_error'] =
                'Only pending loans can be approved.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $loanModel->update(
            $id,
            $businessId,
            [
                'status' =>
                    'active'
            ]
        );


        $_SESSION['loan_success'] =
            'Loan approved successfully.';


        header(
            'Location: index.php?url=loans'
        );

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


        $businessId =
            Auth::businessId();


        $id =
            (int)(
                $_POST['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $loanModel =
            new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header(
                'Location: index.php?url=loans'
            );

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

            header(
                'Location: index.php?url=loans'
            );

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


        header(
            'Location: index.php?url=loans'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT FORM
    |--------------------------------------------------------------------------
    */

    public function payment(): void
    {
        AuthMiddleware::requireLogin();


        $businessId =
            Auth::businessId();


        $id =
            (int)(
                $_GET['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $loanModel =
            new Loan();


        $loan =
            $loanModel->findByBusiness(
                $id,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        if (!in_array(
            $loan['status'],
            [
                'approved',
                'active',
                'overdue'
            ],
            true
        )) {

            $_SESSION['loan_error'] =
                'Payment cannot be made for this loan.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Get schedules
        |--------------------------------------------------------------------------
        */

        $schedule =
            $loanModel->getSchedule(
                $id
            );


        /*
        |--------------------------------------------------------------------------
        | Get accounts
        |--------------------------------------------------------------------------
        */

        $db =
            Database::getInstance();


        $stmt =
            $db->prepare(
                "
                SELECT
                    id,
                    account_name,
                    account_type,
                    balance
                FROM accounts
                WHERE business_id = ?
                AND status = 'active'
                ORDER BY account_name ASC
                "
            );


        $stmt->execute([
            $businessId
        ]);


        $accounts =
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | Get existing payments
        |--------------------------------------------------------------------------
        */

        $payments =
            $loanModel->getPayments(
                $id
            );


        /*
        |--------------------------------------------------------------------------
        | Calculate total paid
        |--------------------------------------------------------------------------
        */

        $totalPaid = 0;


        foreach (
            $payments
            as $payment
        ) {

            if (
                ($payment['status']
                    ?? 'posted')
                === 'posted'
            ) {

                $totalPaid +=
                    (float)(
                        $payment['amount']
                        ?? 0
                    );
            }
        }


        $remainingBalance =
            max(
                0,
                (float)$loan['total_payable']
                -
                $totalPaid
            );


        require APP_PATH .
            '/views/loans/payment.php';
    }


    /*
    |--------------------------------------------------------------------------
    | STORE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function storePayment(): void
    {
        AuthMiddleware::requireLogin();


        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $businessId =
            Auth::businessId();


        $user =
            Auth::user();


        $loanId =
            (int)(
                $_POST['loan_id']
                ?? 0
            );


        $scheduleId =
            !empty(
                $_POST['schedule_id']
            )
            ? (int)$_POST['schedule_id']
            : null;


        $accountId =
            (int)(
                $_POST['account_id']
                ?? 0
            );


        $amount =
            (float)(
                $_POST['amount']
                ?? 0
            );


        $paymentDate =
            !empty(
                $_POST['payment_date']
            )
            ? $_POST['payment_date']
            : date('Y-m-d');


        $notes =
            trim(
                $_POST['notes']
                ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | Validate loan
        |--------------------------------------------------------------------------
        */

        if ($loanId <= 0) {

            $_SESSION['loan_error'] =
                'Invalid loan ID.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate amount
        |--------------------------------------------------------------------------
        */

        if ($amount <= 0) {

            $_SESSION['loan_error'] =
                'Payment amount must be greater than zero.';

            header(
                'Location: index.php?url=loans/payment&id='
                . $loanId
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate account
        |--------------------------------------------------------------------------
        */

        if ($accountId <= 0) {

            $_SESSION['loan_error'] =
                'Please select an account.';

            header(
                'Location: index.php?url=loans/payment&id='
                . $loanId
            );

            exit;
        }


        $loanModel =
            new Loan();


        $loan =
            $loanModel->findByBusiness(
                $loanId,
                $businessId
            );


        if (!$loan) {

            $_SESSION['loan_error'] =
                'Loan not found.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Only active/approved/overdue loans
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $loan['status'],
            [
                'approved',
                'active',
                'overdue'
            ],
            true
        )) {

            $_SESSION['loan_error'] =
                'Payment cannot be made for this loan.';

            header(
                'Location: index.php?url=loans'
            );

            exit;
        }


        $db =
            Database::getInstance();


        /*
        |--------------------------------------------------------------------------
        | Start transaction
        |--------------------------------------------------------------------------
        */

        $db->beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Check account
            |--------------------------------------------------------------------------
            */

            $stmt =
                $db->prepare(
                    "
                    SELECT
                        id,
                        balance,
                        status
                    FROM accounts
                    WHERE id = ?
                    AND business_id = ?
                    FOR UPDATE
                    "
                );


            $stmt->execute([
                $accountId,
                $businessId
            ]);


            $account =
                $stmt->fetch(
                    PDO::FETCH_ASSOC
                );


            if (!$account) {

                throw new Exception(
                    'Selected account was not found.'
                );
            }


            if (
                $account['status']
                !== 'active'
            ) {

                throw new Exception(
                    'Selected account is inactive.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Calculate total already paid
            |--------------------------------------------------------------------------
            */

            $stmt =
                $db->prepare(
                    "
                    SELECT
                        COALESCE(
                            SUM(amount),
                            0
                        )
                    FROM loan_payments
                    WHERE loan_id = ?
                    AND business_id = ?
                    AND status = 'posted'
                    "
                );


            $stmt->execute([
                $loanId,
                $businessId
            ]);


            $totalPaid =
                (float)$stmt->fetchColumn();


            /*
            |--------------------------------------------------------------------------
            | Remaining balance
            |--------------------------------------------------------------------------
            */

            $remainingBalance =
                max(
                    0,
                    (float)$loan['total_payable']
                    -
                    $totalPaid
                );


            /*
            |--------------------------------------------------------------------------
            | Don't allow overpayment
            |--------------------------------------------------------------------------
            */

            if (
                $amount >
                $remainingBalance
            ) {

                throw new Exception(
                    'Payment cannot be greater than the remaining loan balance of ₱'
                    .
                    number_format(
                        $remainingBalance,
                        2
                    )
                    .
                    '.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Determine schedule
            |--------------------------------------------------------------------------
            */

            $principalAmount =
                $amount;


            $interestAmount =
                0;


            $penaltyAmount =
                0;


            if ($scheduleId) {

                $stmt =
                    $db->prepare(
                        "
                        SELECT
                            id,
                            loan_id,
                            total_due,
                            paid_amount,
                            status
                        FROM loan_schedules
                        WHERE id = ?
                        AND loan_id = ?
                        FOR UPDATE
                        "
                    );


                $stmt->execute([
                    $scheduleId,
                    $loanId
                ]);


                $schedule =
                    $stmt->fetch(
                        PDO::FETCH_ASSOC
                    );


                if (!$schedule) {

                    throw new Exception(
                        'Selected payment schedule was not found.'
                    );
                }


                $scheduleRemaining =
                    max(
                        0,
                        (float)$schedule['total_due']
                        -
                        (float)$schedule['paid_amount']
                    );


                if (
                    $amount >
                    $scheduleRemaining
                ) {

                    throw new Exception(
                        'Payment is greater than the remaining amount for this installment.'
                    );
                }


                /*
                 * Get interest for this schedule.
                 *
                 * This now calls the method that exists
                 * in Loan.php.
                 */

                $scheduleInterest =
                    $loanModel->getScheduleInterest(
                        $scheduleId
                    );


                /*
                 * Interest is paid first.
                 */

                $interestAmount =
                    min(
                        $amount,
                        $scheduleInterest
                    );


                $principalAmount =
                    max(
                        0,
                        $amount
                        -
                        $interestAmount
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Generate payment number
            |--------------------------------------------------------------------------
            */

            $paymentNumber =
                'PAY-'
                .
                date('Ymd')
                .
                '-'
                .
                strtoupper(
                    substr(
                        bin2hex(
                            random_bytes(4)
                        ),
                        0,
                        6
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | Insert payment
            |--------------------------------------------------------------------------
            */

            $stmt =
                $db->prepare(
                    "
                    INSERT INTO loan_payments
                    (
                        business_id,
                        loan_id,
                        schedule_id,
                        account_id,
                        payment_number,
                        payment_date,
                        amount,
                        principal_amount,
                        interest_amount,
                        penalty_amount,
                        notes,
                        status,
                        created_by
                    )

                    VALUES
                    (
                        :business_id,
                        :loan_id,
                        :schedule_id,
                        :account_id,
                        :payment_number,
                        :payment_date,
                        :amount,
                        :principal_amount,
                        :interest_amount,
                        :penalty_amount,
                        :notes,
                        'posted',
                        :created_by
                    )
                    "
                );


            /*
             * IMPORTANT:
             *
             * Every named placeholder is unique.
             * This avoids the HY093 issue when
             * PDO emulated prepares are disabled.
             */

            $stmt->execute([

                ':business_id' =>
                    $businessId,

                ':loan_id' =>
                    $loanId,

                ':schedule_id' =>
                    $scheduleId,

                ':account_id' =>
                    $accountId,

                ':payment_number' =>
                    $paymentNumber,

                ':payment_date' =>
                    $paymentDate,

                ':amount' =>
                    $amount,

                ':principal_amount' =>
                    $principalAmount,

                ':interest_amount' =>
                    $interestAmount,

                ':penalty_amount' =>
                    $penaltyAmount,

                ':notes' =>
                    $notes,

                ':created_by' =>
                    $user['id']
                    ?? null
            ]);


            /*
            |--------------------------------------------------------------------------
            | Update schedule
            |--------------------------------------------------------------------------
            */

            if ($scheduleId) {

                $stmt =
                    $db->prepare(
                        "
                        UPDATE loan_schedules
                        SET
                            paid_amount =
                                paid_amount
                                +
                                :payment_amount,

                            status =
                                CASE

                                    WHEN
                                        paid_amount
                                        +
                                        :status_amount
                                        >= total_due
                                    THEN 'paid'

                                    WHEN
                                        paid_amount
                                        +
                                        :partial_amount
                                        > 0
                                    THEN 'partial'

                                    ELSE status

                                END,

                            paid_date =
                                CASE

                                    WHEN
                                        paid_amount
                                        +
                                        :date_amount
                                        >= total_due
                                    THEN :paid_date

                                    ELSE paid_date

                                END

                        WHERE id = :schedule_id

                        AND loan_id = :schedule_loan_id
                        "
                    );


                /*
                 * Use different named parameters for each
                 * occurrence so PDO cannot produce HY093.
                 */

                $stmt->execute([

                    ':payment_amount' =>
                        $amount,

                    ':status_amount' =>
                        $amount,

                    ':partial_amount' =>
                        $amount,

                    ':date_amount' =>
                        $amount,

                    ':paid_date' =>
                        $paymentDate,

                    ':schedule_id' =>
                        $scheduleId,

                    ':schedule_loan_id' =>
                        $loanId
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Add payment to account
            |--------------------------------------------------------------------------
            */

            $stmt =
                $db->prepare(
                    "
                    UPDATE accounts
                    SET balance =
                        balance + ?

                    WHERE id = ?

                    AND business_id = ?

                    AND status = 'active'
                    "
                );


            $stmt->execute([
                $amount,
                $accountId,
                $businessId
            ]);


            /*
            |--------------------------------------------------------------------------
            | Check whether loan is now fully paid
            |--------------------------------------------------------------------------
            */

            $stmt =
                $db->prepare(
                    "
                    SELECT
                        COALESCE(
                            SUM(amount),
                            0
                        )
                    FROM loan_payments
                    WHERE loan_id = ?
                    AND business_id = ?
                    AND status = 'posted'
                    "
                );


            $stmt->execute([
                $loanId,
                $businessId
            ]);


            $newTotalPaid =
                (float)$stmt->fetchColumn();


            /*
            |--------------------------------------------------------------------------
            | Complete loan if fully paid
            |--------------------------------------------------------------------------
            */

            if (
                $newTotalPaid
                >=
                (float)$loan['total_payable']
            ) {

                $stmt =
                    $db->prepare(
                        "
                        UPDATE loans
                        SET status = 'completed'
                        WHERE id = ?
                        AND business_id = ?
                        "
                    );


                $stmt->execute([
                    $loanId,
                    $businessId
                ]);

            } else {

                /*
                 * If loan was approved and payment
                 * is made, make it active.
                 */

                if (
                    $loan['status']
                    === 'approved'
                ) {

                    $stmt =
                        $db->prepare(
                            "
                            UPDATE loans
                            SET status = 'active'
                            WHERE id = ?
                            AND business_id = ?
                            AND status = 'approved'
                            "
                        );


                    $stmt->execute([
                        $loanId,
                        $businessId
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            $db->commit();


            $_SESSION['loan_success'] =
                'Payment recorded successfully.';


            header(
                'Location: index.php?url=loans'
            );

            exit;

        } catch (
            Throwable $e
        ) {

            if (
                $db->inTransaction()
            ) {

                $db->rollBack();
            }


            $_SESSION['loan_error'] =
                $e->getMessage();


            header(
                'Location: index.php?url=loans/payment&id='
                .
                $loanId
            );

            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT INDEX
    |--------------------------------------------------------------------------
    |
    | Shows all payments for the current business.
    |
    */

    public function payments(): void
    {
        AuthMiddleware::requireLogin();


        $businessId =
            Auth::businessId();


        $loanModel =
            new Loan();


        /*
        |--------------------------------------------------------------------------
        | GET ALL PAYMENTS
        |--------------------------------------------------------------------------
        */

        $payments =
            $loanModel->getAllPayments(
                $businessId
            );


        /*
        |--------------------------------------------------------------------------
        | CALCULATE TOTALS
        |--------------------------------------------------------------------------
        */

        $totalPayments =
            0;


        $totalPrincipal =
            0;


        $totalInterest =
            0;


        $totalPenalty =
            0;


        foreach (
            $payments
            as $payment
        ) {

            if (
                ($payment['status']
                    ?? 'posted')
                === 'posted'
            ) {

                $totalPayments +=
                    (float)(
                        $payment['amount']
                        ?? 0
                    );


                $totalPrincipal +=
                    (float)(
                        $payment['principal_amount']
                        ?? 0
                    );


                $totalInterest +=
                    (float)(
                        $payment['interest_amount']
                        ?? 0
                    );


                $totalPenalty +=
                    (float)(
                        $payment['penalty_amount']
                        ?? 0
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FLASH MESSAGES
        |--------------------------------------------------------------------------
        */

        $success =
            $_SESSION['loan_success']
            ?? '';


        $error =
            $_SESSION['loan_error']
            ?? '';


        unset(
            $_SESSION['loan_success'],
            $_SESSION['loan_error']
        );


        /*
        |--------------------------------------------------------------------------
        | AUTH DATA
        |--------------------------------------------------------------------------
        */

        $user =
            Auth::user();


        $business =
            Auth::business();


        $tenantRole =
            Auth::tenantRole();


        /*
        |--------------------------------------------------------------------------
        | LOAD PAYMENT INDEX VIEW
        |--------------------------------------------------------------------------
        */

        require APP_PATH .
            '/views/payments/index.php';
    }
}