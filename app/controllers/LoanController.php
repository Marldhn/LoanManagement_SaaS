
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

    public function index(): void
    {
        AuthMiddleware::requireLogin();

        $user = Auth::user();
        $business = Auth::business();
        $businessId = Auth::businessId();
        $tenantRole = Auth::tenantRole();

        $loanModel = new Loan();

        $loans = $loanModel->all($businessId);
        $borrowers = $loanModel->borrowers($businessId);
        $categories = $loanModel->categories($businessId);

        $accountModel = new Account();
        $accounts = $accountModel->getAll($businessId);

        $success = $_SESSION['loan_success'] ?? '';
        $error = $_SESSION['loan_error'] ?? '';

        unset(
            $_SESSION['loan_success'],
            $_SESSION['loan_error']
        );

        require APP_PATH . '/views/loans/index.php';
    }

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

        $borrowerId = (int)($_POST['borrower_id'] ?? 0);

        $categoryId = !empty($_POST['category_id'])
            ? (int)$_POST['category_id']
            : null;

        $principalAmount = (float)($_POST['principal_amount'] ?? 0);

        $interestRate = (float)($_POST['interest_rate'] ?? 0);

        $interestType = trim(
            $_POST['interest_type'] ?? 'flat'
        );

        $accountId = (int)($_POST['account_id'] ?? 0);

        $paymentType = trim(
            $_POST['payment_type'] ?? 'installment'
        );

        $term = (int)($_POST['term'] ?? 1);

        $termPeriod = trim(
            $_POST['term_period'] ?? 'months'
        );

        $processingFee = (float)(
            $_POST['processing_fee'] ?? 0
        );

        $releaseDate = !empty($_POST['release_date'])
            ? $_POST['release_date']
            : date('Y-m-d');

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

        if ($borrowerId <= 0) {
            $_SESSION['loan_error'] =
                'Please select a borrower.';
            header('Location: index.php?url=loans');
            exit;
        }

        if ($accountId <= 0) {
            $_SESSION['loan_error'] =
                'Please select the account from which the loan will be released.';
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
         * FULL PAYMENT DATE FIX
         *
         * For a full-payment loan:
         *
         * release date + term period = payment date
         *
         * Example:
         * Release: 2026-08-15
         * Term: 15 days
         * Due: 2026-08-30
         *
         * We do not use an arbitrary first_payment_date
         * for full-payment loans.
         */

        if ($paymentType === 'full_payment') {
            try {
                $dueDate = new DateTime($releaseDate);

                switch ($termPeriod) {
                    case 'days':
                        $dueDate->modify("+{$term} days");
                        break;

                    case 'weeks':
                        $dueDate->modify("+{$term} weeks");
                        break;

                    case 'months':
                        $dueDate->modify("+{$term} months");
                        break;

                    case 'years':
                        $dueDate->modify("+{$term} years");
                        break;
                }

                $firstPaymentDate =
                    $dueDate->format('Y-m-d');

            } catch (Throwable $e) {
                $_SESSION['loan_error'] =
                    'Invalid release date.';
                header('Location: index.php?url=loans');
                exit;
            }
        } elseif (!$firstPaymentDate) {
            try {
                $firstDate = new DateTime($releaseDate);

                switch ($termPeriod) {
                    case 'days':
                        $firstDate->modify('+1 day');
                        break;

                    case 'weeks':
                        $firstDate->modify('+1 week');
                        break;

                    case 'months':
                        $firstDate->modify('+1 month');
                        break;

                    case 'years':
                        $firstDate->modify('+1 year');
                        break;
                }

                $firstPaymentDate =
                    $firstDate->format('Y-m-d');

            } catch (Throwable $e) {
                $firstPaymentDate = $releaseDate;
            }
        }

        /*
         * CALCULATE INTEREST
         */

        $totalInterest = 0;

        if ($interestType === 'flat') {
            $totalInterest =
                $principalAmount *
                ($interestRate / 100) *
                $term;
        } else {
            $periods = $term;

            if ($periods > 0) {
                $periodicRate =
                    $interestRate / 100;

                if ($periodicRate > 0) {
                    $payment =
                        $principalAmount *
                        (
                            $periodicRate *
                            pow(
                                1 + $periodicRate,
                                $periods
                            )
                        ) /
                        (
                            pow(
                                1 + $periodicRate,
                                $periods
                            ) - 1
                        );

                    $totalInterest =
                        (
                            $payment * $periods
                        ) -
                        $principalAmount;
                }
            }
        }

        $totalPayable =
            $principalAmount +
            $totalInterest +
            $processingFee;

        $loanNumber =
            $loanModel->generateLoanNumber(
                $businessId
            );

        try {
            $db = Database::getInstance();

            $db->beginTransaction();

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

            if (!$loanId) {
                throw new Exception(
                    'Unable to create loan.'
                );
            }

            $accountModel =
                new Account();

            $accountModel->deductForLoan(
                $accountId,
                $businessId,
                $principalAmount
            );

            /*
             * Generate payment schedule.
             *
             * For full_payment, the model receives the
             * already-calculated maturity/payment date.
             */

            $loanModel->generateSchedule(
                $loanId,
                $principalAmount,
                $totalInterest,
                $term,
                $termPeriod,
                $firstPaymentDate,
                $paymentType
            );

            $db->commit();

        } catch (Throwable $e) {

            if (
                isset($db) &&
                $db->inTransaction()
            ) {
                $db->rollBack();
            }

            $_SESSION['loan_error'] =
                $e->getMessage();

            header('Location: index.php?url=loans');
            exit;
        }

        $_SESSION['loan_success'] =
            'Loan created successfully.';

        header('Location: index.php?url=loans');
        exit;
    }

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

        $schedule =
            $loanModel->getSchedule($id);

        $payments =
            $loanModel->getPayments($id);

        $user = Auth::user();
        $business = Auth::business();
        $tenantRole = Auth::tenantRole();

        require APP_PATH . '/views/loans/show.php';
    }

    public function edit(): void
    {
        AuthMiddleware::requireLogin();

        $businessId = Auth::businessId();

        $id = (int)(
            $_GET['id']
            ?? $_POST['id']
            ?? 0
        );

        if ($id <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');

            echo json_encode([
                'success' => false,
                'message' => 'Invalid loan ID.'
            ]);

            exit;
        }

        $loanModel = new Loan();

        $loan = $loanModel->findByBusiness(
            $id,
            $businessId
        );

        if (!$loan) {
            http_response_code(404);
            header('Content-Type: application/json');

            echo json_encode([
                'success' => false,
                'message' => 'Loan not found.'
            ]);

            exit;
        }

        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'loan' => $loan
        ]);

        exit;
    }

    public function update(): void
    {
        AuthMiddleware::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=loans');
            exit;
        }

        $businessId = Auth::businessId();

        $id = (int)(
            $_POST['id']
            ?? $_POST['loan_id']
            ?? 0
        );

        if ($id <= 0) {
            $_SESSION['loan_error'] =
                'Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel = new Loan();

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

        $borrowerId = (int)(
            $_POST['borrower_id']
            ?? $loan['borrower_id']
            ?? 0
        );

        $categoryId = !empty(
            $_POST['category_id']
        )
            ? (int)$_POST['category_id']
            : null;

        $principalAmount = (float)(
            $_POST['principal_amount']
            ?? $loan['principal_amount']
            ?? 0
        );

        $interestRate = (float)(
            $_POST['interest_rate']
            ?? $loan['interest_rate']
            ?? 0
        );

        $interestType = trim(
            $_POST['interest_type']
            ?? $loan['interest_type']
            ?? 'flat'
        );

        $paymentType = trim(
            $_POST['payment_type']
            ?? $loan['payment_type']
            ?? 'installment'
        );

        $term = (int)(
            $_POST['term']
            ?? $loan['term']
            ?? 1
        );

        $termPeriod = trim(
            $_POST['term_period']
            ?? $loan['term_period']
            ?? 'months'
        );

        $processingFee = (float)(
            $_POST['processing_fee']
            ?? $loan['processing_fee']
            ?? 0
        );

        $releaseDate = !empty(
            $_POST['release_date']
        )
            ? $_POST['release_date']
            : ($loan['release_date'] ?? date('Y-m-d'));

        $firstPaymentDate = !empty(
            $_POST['first_payment_date']
        )
            ? $_POST['first_payment_date']
            : ($loan['first_payment_date'] ?? null);

        $status = trim(
            $_POST['status']
            ?? $loan['status']
            ?? 'pending'
        );

        $purpose = trim(
            $_POST['purpose']
            ?? $loan['purpose']
            ?? ''
        );

        $notes = trim(
            $_POST['notes']
            ?? $loan['notes']
            ?? ''
        );

        if ($borrowerId <= 0) {
            $_SESSION['loan_error'] =
                'Please select a borrower.';
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
         * FULL PAYMENT DATE FIX
         */

        if ($paymentType === 'full_payment') {
            try {
                $dueDate = new DateTime($releaseDate);

                switch ($termPeriod) {
                    case 'days':
                        $dueDate->modify("+{$term} days");
                        break;

                    case 'weeks':
                        $dueDate->modify("+{$term} weeks");
                        break;

                    case 'months':
                        $dueDate->modify("+{$term} months");
                        break;

                    case 'years':
                        $dueDate->modify("+{$term} years");
                        break;
                }

                $firstPaymentDate =
                    $dueDate->format('Y-m-d');

            } catch (Throwable $e) {
                $_SESSION['loan_error'] =
                    'Invalid release date.';
                header('Location: index.php?url=loans');
                exit;
            }
        }

        /*
         * CALCULATE INTEREST
         */

        $totalInterest = 0;

        if ($interestType === 'flat') {

            $totalInterest =
                $principalAmount *
                ($interestRate / 100) *
                $term;

        } else {

            $periods = $term;

            if ($periods > 0) {

                $periodicRate =
                    $interestRate / 100;

                if ($periodicRate > 0) {

                    $payment =
                        $principalAmount *
                        (
                            $periodicRate *
                            pow(
                                1 + $periodicRate,
                                $periods
                            )
                        ) /
                        (
                            pow(
                                1 + $periodicRate,
                                $periods
                            ) - 1
                        );

                    $totalInterest =
                        (
                            $payment * $periods
                        ) -
                        $principalAmount;
                }
            }
        }

        $totalPayable =
            $principalAmount +
            $totalInterest +
            $processingFee;

        try {

            $updated = $loanModel->update(
                $id,
                $businessId,
                [
                    'borrower_id' =>
                        $borrowerId,

                    'category_id' =>
                        $categoryId,

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
                        $notes
                ]
            );

            if ($updated === false) {
                throw new Exception(
                    'Unable to update the loan.'
                );
            }

            /*
             * Regenerate schedule after changing
             * the loan's payment terms/date.
             */

            $db = Database::getInstance();

            $db->beginTransaction();

            try {

                $stmt = $db->prepare(
                    "
                    DELETE FROM loan_schedules
                    WHERE loan_id = ?
                    "
                );

                $stmt->execute([
                    $id
                ]);

                $loanModel->generateSchedule(
                    $id,
                    $principalAmount,
                    $totalInterest,
                    $term,
                    $termPeriod,
                    $firstPaymentDate,
                    $paymentType
                );

                $db->commit();

            } catch (Throwable $e) {

                if ($db->inTransaction()) {
                    $db->rollBack();
                }

                throw $e;
            }

            $_SESSION['loan_success'] =
                'Loan updated successfully.';

            header('Location: index.php?url=loans');
            exit;

        } catch (Throwable $e) {

            $_SESSION['loan_error'] =
                $e->getMessage();

            header('Location: index.php?url=loans');
            exit;
        }
    }

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
                'status' => 'active'
            ]
        );

        $_SESSION['loan_success'] =
            'Loan approved successfully.';

        header('Location: index.php?url=loans');
        exit;
    }

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

        if (!in_array(
            $loan['status'],
            ['approved', 'pending'],
            true
        )) {
            $_SESSION['loan_error'] =
                'This loan cannot be released.';
            header('Location: index.php?url=loans');
            exit;
        }

        $releaseDate = date('Y-m-d');

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

    public function penalty(): void
    {
        AuthMiddleware::requireLogin();

        $businessId = Auth::businessId();

        $loanId = (int)(
            $_GET['id']
            ?? $_POST['id']
            ?? $_POST['loan_id']
            ?? 0
        );

        if ($loanId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');

            echo json_encode([
                'success' => false,
                'message' => 'Invalid loan ID.'
            ]);

            exit;
        }

        $loanModel = new Loan();

        $loan = $loanModel->findByBusiness(
            $loanId,
            $businessId
        );

        if (!$loan) {
            http_response_code(404);
            header('Content-Type: application/json');

            echo json_encode([
                'success' => false,
                'message' => 'Loan not found.'
            ]);

            exit;
        }

        $schedule =
            $loanModel->getSchedule($loanId);

        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'loan' => $loan,
            'schedules' => $schedule
        ]);

        exit;
    }

    public function storePenalty(): void
    {
        AuthMiddleware::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=loans');
            exit;
        }

        $businessId = Auth::businessId();
        $user = Auth::user();

        $loanId = (int)(
            $_POST['loan_id'] ?? 0
        );

        $scheduleId = !empty(
            $_POST['schedule_id']
        )
            ? (int)$_POST['schedule_id']
            : null;

        $penaltyType = trim(
            $_POST['penalty_type'] ?? 'fixed'
        );

        $penaltyBaseRate = (float)(
            $_POST['penalty_base_rate'] ?? 0
        );

        $baseAmount = (float)(
            $_POST['base_amount'] ?? 0
        );

        $penaltyAmount = (float)(
            $_POST['penalty_amount'] ?? 0
        );

        $reason = trim(
            $_POST['reason'] ?? ''
        );

        if ($loanId <= 0) {
            $_SESSION['loan_error'] =
                'Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        $allowedPenaltyTypes = [
            'fixed',
            'percentage',
            'daily_fixed',
            'daily_percentage'
        ];

        if (!in_array(
            $penaltyType,
            $allowedPenaltyTypes,
            true
        )) {
            $_SESSION['loan_error'] =
                'Invalid penalty type.';
            header('Location: index.php?url=loans');
            exit;
        }

        if ($penaltyAmount <= 0) {
            $_SESSION['loan_error'] =
                'Penalty amount must be greater than zero.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel = new Loan();

        $loan = $loanModel->findByBusiness(
            $loanId,
            $businessId
        );

        if (!$loan) {
            $_SESSION['loan_error'] =
                'Loan not found.';
            header('Location: index.php?url=loans');
            exit;
        }

        if ($scheduleId) {

            $db = Database::getInstance();

            $stmt = $db->prepare(
                "
                SELECT id
                FROM loan_schedules
                WHERE id = ?
                AND loan_id = ?
                "
            );

            $stmt->execute([
                $scheduleId,
                $loanId
            ]);

            if (!$stmt->fetchColumn()) {
                $_SESSION['loan_error'] =
                    'Selected schedule does not belong to this loan.';
                header('Location: index.php?url=loans');
                exit;
            }
        }

        try {

            $db = Database::getInstance();

            $stmt = $db->prepare(
                "
                INSERT INTO loan_penalties
                (
                    business_id,
                    loan_id,
                    schedule_id,
                    penalty_type,
                    penalty_base_rate,
                    base_amount,
                    penalty_amount,
                    reason,
                    created_by
                )
                VALUES
                (
                    :business_id,
                    :loan_id,
                    :schedule_id,
                    :penalty_type,
                    :penalty_base_rate,
                    :base_amount,
                    :penalty_amount,
                    :reason,
                    :created_by
                )
                "
            );

            $stmt->execute([
                ':business_id' =>
                    $businessId,

                ':loan_id' =>
                    $loanId,

                ':schedule_id' =>
                    $scheduleId,

                ':penalty_type' =>
                    $penaltyType,

                ':penalty_base_rate' =>
                    $penaltyBaseRate,

                ':base_amount' =>
                    $baseAmount,

                ':penalty_amount' =>
                    $penaltyAmount,

                ':reason' =>
                    $reason !== ''
                        ? $reason
                        : null,

                ':created_by' =>
                    $user['id'] ?? null
            ]);

            $_SESSION['loan_success'] =
                'Penalty added successfully.';

            header('Location: index.php?url=loans');
            exit;

        } catch (Throwable $e) {

            $_SESSION['loan_error'] =
                $e->getMessage();

            header('Location: index.php?url=loans');
            exit;
        }
    }

    public function payment(): void
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

        if (!in_array(
            $loan['status'],
            ['approved', 'active', 'overdue'],
            true
        )) {
            $_SESSION['loan_error'] =
                'Payment cannot be made for this loan.';
            header('Location: index.php?url=loans');
            exit;
        }

        $schedule =
            $loanModel->getSchedule($id);

        $db = Database::getInstance();

        $stmt = $db->prepare(
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

        $accounts = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        $payments =
            $loanModel->getPayments($id);

        $totalPaid = 0;

        foreach ($payments as $payment) {
            if (
                ($payment['status'] ?? 'posted')
                === 'posted'
            ) {
                $totalPaid +=
                    (float)(
                        $payment['amount'] ?? 0
                    );
            }
        }

        $remainingBalance = max(
            0,
            (float)$loan['total_payable'] -
            $totalPaid
        );

        require APP_PATH .
            '/views/loans/payment.php';
    }

    public function storePayment(): void
    {
        AuthMiddleware::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=loans');
            exit;
        }

        $businessId = Auth::businessId();
        $user = Auth::user();

        $loanId = (int)(
            $_POST['loan_id'] ?? 0
        );

        $scheduleId = !empty(
            $_POST['schedule_id']
        )
            ? (int)$_POST['schedule_id']
            : null;

        $accountId = (int)(
            $_POST['account_id'] ?? 0
        );

        $amount = (float)(
            $_POST['amount'] ?? 0
        );

        $paymentDate = !empty(
            $_POST['payment_date']
        )
            ? $_POST['payment_date']
            : date('Y-m-d');

        $notes = trim(
            $_POST['notes'] ?? ''
        );

        if ($loanId <= 0) {
            $_SESSION['loan_error'] =
                'Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        if ($amount <= 0) {
            $_SESSION['loan_error'] =
                'Payment amount must be greater than zero.';
            header(
                'Location: index.php?url=loans/payment&id=' .
                $loanId
            );
            exit;
        }

        if ($accountId <= 0) {
            $_SESSION['loan_error'] =
                'Please select an account.';
            header(
                'Location: index.php?url=loans/payment&id=' .
                $loanId
            );
            exit;
        }

        $loanModel = new Loan();

        $loan = $loanModel->findByBusiness(
            $loanId,
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
            ['approved', 'active', 'overdue'],
            true
        )) {
            $_SESSION['loan_error'] =
                'Payment cannot be made for this loan.';
            header('Location: index.php?url=loans');
            exit;
        }

        $db = Database::getInstance();

        $db->beginTransaction();

        try {

            $stmt = $db->prepare(
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

            $account = $stmt->fetch(
                PDO::FETCH_ASSOC
            );

            if (!$account) {
                throw new Exception(
                    'Selected account was not found.'
                );
            }

            if ($account['status'] !== 'active') {
                throw new Exception(
                    'Selected account is inactive.'
                );
            }

            $stmt = $db->prepare(
                "
                SELECT
                    COALESCE(SUM(amount),0)
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

            $remainingBalance = max(
                0,
                (float)$loan['total_payable'] -
                $totalPaid
            );

            if ($amount > $remainingBalance) {
                throw new Exception(
                    'Payment cannot be greater than the remaining loan balance of ₱' .
                    number_format(
                        $remainingBalance,
                        2
                    ) .
                    '.'
                );
            }

            $principalAmount = $amount;
            $interestAmount = 0;
            $penaltyAmount = 0;

            if ($scheduleId) {

                $stmt = $db->prepare(
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

                $schedule = $stmt->fetch(
                    PDO::FETCH_ASSOC
                );

                if (!$schedule) {
                    throw new Exception(
                        'Selected payment schedule was not found.'
                    );
                }

                $scheduleRemaining = max(
                    0,
                    (float)$schedule['total_due'] -
                    (float)$schedule['paid_amount']
                );

                if ($amount > $scheduleRemaining) {
                    throw new Exception(
                        'Payment is greater than the remaining amount for this installment.'
                    );
                }

                $scheduleInterest =
                    $loanModel->getScheduleInterest(
                        $scheduleId
                    );

                $interestAmount = min(
                    $amount,
                    $scheduleInterest
                );

                $principalAmount = max(
                    0,
                    $amount - $interestAmount
                );
            }

            $paymentNumber =
                'PAY-' .
                date('Ymd') .
                '-' .
                strtoupper(
                    substr(
                        bin2hex(
                            random_bytes(4)
                        ),
                        0,
                        6
                    )
                );

            $stmt = $db->prepare(
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
                    $user['id'] ?? null
            ]);

            if ($scheduleId) {

                $stmt = $db->prepare(
                    "
                    UPDATE loan_schedules
                    SET
                        paid_amount =
                            paid_amount + :payment_amount,

                        status =
                            CASE
                                WHEN
                                    paid_amount +
                                    :status_amount >= total_due
                                THEN 'paid'

                                WHEN
                                    paid_amount +
                                    :partial_amount > 0
                                THEN 'partial'

                                ELSE status
                            END,

                        paid_date =
                            CASE
                                WHEN
                                    paid_amount +
                                    :date_amount >= total_due
                                THEN :paid_date

                                ELSE paid_date
                            END

                    WHERE id = :schedule_id
                    AND loan_id = :schedule_loan_id
                    "
                );

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

            $stmt = $db->prepare(
                "
                UPDATE accounts
                SET balance = balance + ?
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

            $stmt = $db->prepare(
                "
                SELECT
                    COALESCE(SUM(amount),0)
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

            if (
                $newTotalPaid >=
                (float)$loan['total_payable']
            ) {

                $stmt = $db->prepare(
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

            } elseif (
                $loan['status'] === 'approved'
            ) {

                $stmt = $db->prepare(
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

            $db->commit();

            $_SESSION['loan_success'] =
                'Payment recorded successfully.';

            header('Location: index.php?url=loans');
            exit;

        } catch (Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $_SESSION['loan_error'] =
                $e->getMessage();

            header(
                'Location: index.php?url=loans/payment&id=' .
                $loanId
            );

            exit;
        }
    }

    public function payments(): void
    {
        AuthMiddleware::requireLogin();

        $businessId = Auth::businessId();

        $loanModel = new Loan();

        $payments =
            $loanModel->getAllPayments(
                $businessId
            );

        $totalPayments = 0;
        $totalPrincipal = 0;
        $totalInterest = 0;
        $totalPenalty = 0;

        foreach ($payments as $payment) {

            if (
                ($payment['status'] ?? 'posted')
                === 'posted'
            ) {

                $totalPayments +=
                    (float)(
                        $payment['amount'] ?? 0
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

        $success =
            $_SESSION['loan_success'] ?? '';

        $error =
            $_SESSION['loan_error'] ?? '';

        unset(
            $_SESSION['loan_success'],
            $_SESSION['loan_error']
        );

        $user = Auth::user();
        $business = Auth::business();
        $tenantRole = Auth::tenantRole();

        require APP_PATH .
            '/views/payments/index.php';
    }
}
