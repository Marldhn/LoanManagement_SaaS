<?php

class CollectionController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION / BUSINESS ACCESS
    |--------------------------------------------------------------------------
    */

    private function checkAccess(): array
    {
        if (!Auth::check()) {
            header('Location: index.php?url=auth/login');
            exit;
        }

        $user = Auth::user();

        $business = Auth::business();

        $tenantRole = Auth::tenantRole();

        $businessId = (int)($_SESSION['business_id'] ?? 0);

        if ($businessId <= 0 && is_array($business)) {
            $businessId = (int)($business['id'] ?? 0);
        }

        if ($businessId <= 0) {
            http_response_code(403);

            die('Business context not found.');
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);

        if ($userId <= 0 && is_array($user)) {
            $userId = (int)($user['id'] ?? 0);
        }

        return [
            'user'       => $user,
            'business'   => $business,
            'tenantRole' => $tenantRole,
            'businessId' => $businessId,
            'userId'     => $userId,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | COLLECTION DASHBOARD
    |--------------------------------------------------------------------------
    |
    | URL:
    |
    | index.php?url=collections
    |
    */

    public function index(): void
    {
        $context = $this->checkAccess();

        $businessId = $context['businessId'];

        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        $dateFrom =
            $_GET['date_from']
            ?? date('Y-m-01');

        $dateTo =
            $_GET['date_to']
            ?? date('Y-m-d');


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        $search =
            trim(
                $_GET['search']
                ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | PAYMENT STATUS
        |--------------------------------------------------------------------------
        */

        $status =
            $_GET['status']
            ?? 'posted';


        /*
        |--------------------------------------------------------------------------
        | COLLECTION SUMMARY
        |--------------------------------------------------------------------------
        */

        $summarySql = "
            SELECT
                COUNT(*) AS total_payments,

                COALESCE(
                    SUM(lp.amount),
                    0
                ) AS total_collected,

                COALESCE(
                    SUM(lp.principal_amount),
                    0
                ) AS principal_collected,

                COALESCE(
                    SUM(lp.interest_amount),
                    0
                ) AS interest_collected,

                COALESCE(
                    SUM(lp.penalty_amount),
                    0
                ) AS penalty_collected

            FROM loan_payments lp

            INNER JOIN loans l
                ON l.id = lp.loan_id

            WHERE
                lp.business_id = :business_id

                AND lp.payment_date
                    BETWEEN :date_from
                    AND :date_to
        ";

        $summaryParams = [
            ':business_id' => $businessId,
            ':date_from'   => $dateFrom,
            ':date_to'     => $dateTo,
        ];


        if (
            $status !== '' &&
            $status !== 'all'
        ) {
            $summarySql .= "
                AND lp.status = :status
            ";

            $summaryParams[':status'] =
                $status;
        }


        $summaryStmt =
            $this->db->prepare(
                $summarySql
            );

        $summaryStmt->execute(
            $summaryParams
        );

        $summary =
            $summaryStmt->fetch(
                PDO::FETCH_ASSOC
            )
            ?: [];


        /*
        |--------------------------------------------------------------------------
        | COLLECTION LIST
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT

                lp.id,

                lp.payment_number,

                lp.payment_date,

                lp.amount,

                lp.principal_amount,

                lp.interest_amount,

                lp.penalty_amount,

                lp.notes,

                lp.status,

                lp.created_at,

                l.id AS loan_id,

                l.loan_number,

                l.principal_amount AS loan_principal,

                l.total_payable,

                l.status AS loan_status,

                b.id AS borrower_id,

                b.borrower_code,

                b.first_name,

                b.middle_name,

                b.last_name,

                CONCAT(
                    b.first_name,
                    ' ',
                    COALESCE(
                        CONCAT(
                            b.middle_name,
                            ' '
                        ),
                        ''
                    ),
                    b.last_name
                ) AS borrower_name,

                a.id AS account_id,

                a.account_name,

                u.full_name AS collected_by

            FROM loan_payments lp

            INNER JOIN loans l
                ON l.id = lp.loan_id

            INNER JOIN borrowers b
                ON b.id = l.borrower_id

            INNER JOIN accounts a
                ON a.id = lp.account_id

            LEFT JOIN users u
                ON u.id = lp.created_by

            WHERE
                lp.business_id = :business_id

                AND lp.payment_date
                    BETWEEN :date_from
                    AND :date_to
        ";

        $params = [
            ':business_id' => $businessId,
            ':date_from'   => $dateFrom,
            ':date_to'     => $dateTo,
        ];


        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $status !== '' &&
            $status !== 'all'
        ) {
            $sql .= "
                AND lp.status = :status
            ";

            $params[':status'] =
                $status;
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH FILTER
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $sql .= "
                AND (
                    lp.payment_number LIKE :search1
                    OR l.loan_number LIKE :search2
                    OR b.borrower_code LIKE :search3
                    OR b.first_name LIKE :search4
                    OR b.last_name LIKE :search5
                    OR a.account_name LIKE :search6
                )
            ";

            $searchValue =
                '%' .
                $search .
                '%';

            $params[':search1'] =
                $searchValue;

            $params[':search2'] =
                $searchValue;

            $params[':search3'] =
                $searchValue;

            $params[':search4'] =
                $searchValue;

            $params[':search5'] =
                $searchValue;

            $params[':search6'] =
                $searchValue;
        }


        $sql .= "
            ORDER BY
                lp.payment_date DESC,
                lp.id DESC
        ";


        $stmt =
            $this->db->prepare(
                $sql
            );

        $stmt->execute(
            $params
        );

        $collections =
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | ACCOUNTS
        |--------------------------------------------------------------------------
        */

        $accountStmt =
            $this->db->prepare("
                SELECT
                    id,
                    account_name,
                    account_type,
                    balance,
                    status

                FROM accounts

                WHERE
                    business_id = :business_id

                    AND status = 'active'

                ORDER BY
                    account_name ASC
            ");

        $accountStmt->execute([
            ':business_id' => $businessId,
        ]);

        $accounts =
            $accountStmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | LOANS FOR COLLECTION
        |--------------------------------------------------------------------------
        */

        $loanStmt =
            $this->db->prepare("
                SELECT

                    l.id,

                    l.loan_number,

                    l.principal_amount,

                    l.total_interest,

                    l.total_payable,

                    l.status,

                    b.borrower_code,

                    CONCAT(
                        b.first_name,
                        ' ',
                        COALESCE(
                            CONCAT(
                                b.middle_name,
                                ' '
                            ),
                            ''
                        ),
                        b.last_name
                    ) AS borrower_name,

                    COALESCE(
                        (
                            SELECT
                                SUM(lp2.amount)

                            FROM loan_payments lp2

                            WHERE
                                lp2.loan_id = l.id

                                AND lp2.status = 'posted'
                        ),
                        0
                    ) AS total_paid

                FROM loans l

                INNER JOIN borrowers b
                    ON b.id = l.borrower_id

                WHERE
                    l.business_id = :business_id

                    AND l.status IN (
                        'approved',
                        'active',
                        'overdue'
                    )

                ORDER BY
                    b.last_name ASC,
                    b.first_name ASC,
                    l.id DESC
            ");

        $loanStmt->execute([
            ':business_id' => $businessId,
        ]);

        $loans =
            $loanStmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        require APP_PATH .
            '/views/collections/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE COLLECTION PAGE
    |--------------------------------------------------------------------------
    |
    | URL:
    |
    | index.php?url=collections/create
    |
    */

    public function create(): void
    {
        $context = $this->checkAccess();

        $businessId = $context['businessId'];

        $loanId =
            (int)(
                $_GET['loan_id']
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | ACTIVE ACCOUNTS
        |--------------------------------------------------------------------------
        */

        $accountStmt =
            $this->db->prepare("
                SELECT
                    id,
                    account_name,
                    account_type,
                    balance

                FROM accounts

                WHERE
                    business_id = :business_id

                    AND status = 'active'

                ORDER BY
                    account_name ASC
            ");

        $accountStmt->execute([
            ':business_id' => $businessId,
        ]);

        $accounts =
            $accountStmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | LOAN
        |--------------------------------------------------------------------------
        */

        $loan = null;


        if ($loanId > 0) {

            $stmt =
                $this->db->prepare("
                    SELECT

                        l.*,

                        b.borrower_code,

                        b.first_name,

                        b.middle_name,

                        b.last_name,

                        CONCAT(
                            b.first_name,
                            ' ',
                            COALESCE(
                                CONCAT(
                                    b.middle_name,
                                    ' '
                                ),
                                ''
                            ),
                            b.last_name
                        ) AS borrower_name,

                        COALESCE(
                            (
                                SELECT
                                    SUM(lp.amount)

                                FROM loan_payments lp

                                WHERE
                                    lp.loan_id = l.id

                                    AND lp.status = 'posted'
                            ),
                            0
                        ) AS total_paid

                    FROM loans l

                    INNER JOIN borrowers b
                        ON b.id = l.borrower_id

                    WHERE
                        l.id = :loan_id

                        AND l.business_id = :business_id

                    LIMIT 1
                ");

            $stmt->execute([
                ':loan_id'    => $loanId,
                ':business_id' => $businessId,
            ]);

            $loan =
                $stmt->fetch(
                    PDO::FETCH_ASSOC
                )
                ?: null;


            if (!$loan) {

                $_SESSION['error'] =
                    'Loan not found.';

                header(
                    'Location: index.php?url=collections'
                );

                exit;
            }


            $loan['remaining_balance'] =
                max(
                    0,
                    (float)$loan['total_payable']
                    -
                    (float)$loan['total_paid']
                );
        }


        /*
        |--------------------------------------------------------------------------
        | AVAILABLE LOANS
        |--------------------------------------------------------------------------
        */

        $loanStmt =
            $this->db->prepare("
                SELECT

                    l.id,

                    l.loan_number,

                    l.total_payable,

                    l.status,

                    b.borrower_code,

                    CONCAT(
                        b.first_name,
                        ' ',
                        COALESCE(
                            CONCAT(
                                b.middle_name,
                                ' '
                            ),
                            ''
                        ),
                        b.last_name
                    ) AS borrower_name,

                    COALESCE(
                        (
                            SELECT
                                SUM(lp.amount)

                            FROM loan_payments lp

                            WHERE
                                lp.loan_id = l.id

                                AND lp.status = 'posted'
                        ),
                        0
                    ) AS total_paid

                FROM loans l

                INNER JOIN borrowers b
                    ON b.id = l.borrower_id

                WHERE
                    l.business_id = :business_id

                    AND l.status IN (
                        'approved',
                        'active',
                        'overdue'
                    )

                ORDER BY
                    b.last_name ASC,
                    b.first_name ASC
            ");

        $loanStmt->execute([
            ':business_id' => $businessId,
        ]);

        $loans =
            $loanStmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        foreach ($loans as &$item) {

            $item['remaining_balance'] =
                max(
                    0,
                    (float)$item['total_payable']
                    -
                    (float)$item['total_paid']
                );
        }

        unset($item);


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        require APP_PATH .
            '/views/collections/create.php';
    }


    /*
    |--------------------------------------------------------------------------
    | STORE COLLECTION
    |--------------------------------------------------------------------------
    |
    | URL:
    |
    | index.php?url=collections/store
    |
    */

    public function store(): void
    {
        $context = $this->checkAccess();

        $businessId =
            $context['businessId'];

        $userId =
            $context['userId'];


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header(
                'Location: index.php?url=collections'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | INPUT
        |--------------------------------------------------------------------------
        */

        $loanId =
            (int)(
                $_POST['loan_id']
                ?? 0
            );

        $accountId =
            (int)(
                $_POST['account_id']
                ?? 0
            );

        $paymentDate =
            trim(
                $_POST['payment_date']
                ?? date('Y-m-d')
            );

        $amount =
            (float)(
                $_POST['amount']
                ?? 0
            );

        $principalAmount =
            (float)(
                $_POST['principal_amount']
                ?? 0
            );

        $interestAmount =
            (float)(
                $_POST['interest_amount']
                ?? 0
            );

        $penaltyAmount =
            (float)(
                $_POST['penalty_amount']
                ?? 0
            );

        $notes =
            trim(
                $_POST['notes']
                ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | BASIC VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($loanId <= 0) {

            $_SESSION['error'] =
                'Please select a loan.';

            header(
                'Location: index.php?url=collections/create'
            );

            exit;
        }


        if ($accountId <= 0) {

            $_SESSION['error'] =
                'Please select the account receiving the payment.';

            header(
                'Location: index.php?url=collections/create&loan_id='
                . $loanId
            );

            exit;
        }


        if ($amount <= 0) {

            $_SESSION['error'] =
                'Payment amount must be greater than zero.';

            header(
                'Location: index.php?url=collections/create&loan_id='
                . $loanId
            );

            exit;
        }


        if (
            $principalAmount < 0 ||
            $interestAmount < 0 ||
            $penaltyAmount < 0
        ) {

            $_SESSION['error'] =
                'Payment breakdown cannot contain negative amounts.';

            header(
                'Location: index.php?url=collections/create&loan_id='
                . $loanId
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE PAYMENT BREAKDOWN
        |--------------------------------------------------------------------------
        */

        $breakdownTotal =
            $principalAmount
            +
            $interestAmount
            +
            $penaltyAmount;


        if (
            abs(
                $breakdownTotal
                -
                $amount
            ) > 0.01
        ) {

            $_SESSION['error'] =
                'Principal, interest, and penalty must equal the payment amount.';

            header(
                'Location: index.php?url=collections/create&loan_id='
                . $loanId
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        try {

            $this->db->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | GET LOAN
            |--------------------------------------------------------------------------
            */

            $loanStmt =
                $this->db->prepare("
                    SELECT

                        l.id,

                        l.business_id,

                        l.borrower_id,

                        l.account_id,

                        l.loan_number,

                        l.total_payable,

                        l.status

                    FROM loans l

                    WHERE
                        l.id = :loan_id

                        AND l.business_id = :business_id

                    LIMIT 1

                    FOR UPDATE
                ");

            $loanStmt->execute([
                ':loan_id' =>
                    $loanId,

                ':business_id' =>
                    $businessId,
            ]);

            $loan =
                $loanStmt->fetch(
                    PDO::FETCH_ASSOC
                );


            if (!$loan) {

                throw new Exception(
                    'Loan not found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATE LOAN STATUS
            |--------------------------------------------------------------------------
            */

            $allowedStatuses = [
                'approved',
                'active',
                'overdue',
            ];


            if (
                !in_array(
                    $loan['status'],
                    $allowedStatuses,
                    true
                )
            ) {

                throw new Exception(
                    'This loan cannot receive a payment because its current status is '
                    . $loan['status']
                    . '.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GET CURRENT PAID AMOUNT
            |--------------------------------------------------------------------------
            */

            $paidStmt =
                $this->db->prepare("
                    SELECT

                        COALESCE(
                            SUM(amount),
                            0
                        ) AS total_paid

                    FROM loan_payments

                    WHERE
                        loan_id = :loan_id

                        AND business_id = :business_id

                        AND status = 'posted'
                ");

            $paidStmt->execute([
                ':loan_id' =>
                    $loanId,

                ':business_id' =>
                    $businessId,
            ]);

            $paidRow =
                $paidStmt->fetch(
                    PDO::FETCH_ASSOC
                );


            $totalPaid =
                (float)(
                    $paidRow['total_paid']
                    ?? 0
                );


            $totalPayable =
                (float)$loan[
                    'total_payable'
                ];


            $remainingBalance =
                max(
                    0,
                    $totalPayable
                    -
                    $totalPaid
                );


            /*
            |--------------------------------------------------------------------------
            | PREVENT OVERPAYMENT
            |--------------------------------------------------------------------------
            */

            if (
                $amount >
                $remainingBalance
                &&
                $remainingBalance > 0
            ) {

                throw new Exception(
                    'Payment amount exceeds the remaining loan balance of ₱'
                    .
                    number_format(
                        $remainingBalance,
                        2
                    )
                    .
                    '.'
                );
            }


            if (
                $remainingBalance <= 0
            ) {

                throw new Exception(
                    'This loan is already fully paid.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATE ACCOUNT
            |--------------------------------------------------------------------------
            */

            $accountStmt =
                $this->db->prepare("
                    SELECT

                        id,

                        account_name,

                        balance,

                        status

                    FROM accounts

                    WHERE
                        id = :account_id

                        AND business_id = :business_id

                    LIMIT 1

                    FOR UPDATE
                ");

            $accountStmt->execute([
                ':account_id' =>
                    $accountId,

                ':business_id' =>
                    $businessId,
            ]);

            $account =
                $accountStmt->fetch(
                    PDO::FETCH_ASSOC
                );


            if (!$account) {

                throw new Exception(
                    'Selected account was not found.'
                );
            }


            if (
                $account['status']
                !==
                'active'
            ) {

                throw new Exception(
                    'Selected account is inactive.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GENERATE PAYMENT NUMBER
            |--------------------------------------------------------------------------
            */

            $paymentNumber =
                $this->generatePaymentNumber(
                    $businessId
                );


            /*
            |--------------------------------------------------------------------------
            | INSERT PAYMENT
            |--------------------------------------------------------------------------
            */

            $insertStmt =
                $this->db->prepare("
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
                ");


            /*
            |--------------------------------------------------------------------------
            | SCHEDULE
            |--------------------------------------------------------------------------
            |
            | We initially leave schedule_id NULL.
            | The payment can still be recorded because schedule_id
            | is nullable in your database.
            |
            */

            $insertStmt->execute([
                ':business_id' =>
                    $businessId,

                ':loan_id' =>
                    $loanId,

                ':schedule_id' =>
                    null,

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
                    $notes !== ''
                        ? $notes
                        : null,

                ':created_by' =>
                    $userId > 0
                        ? $userId
                        : null,
            ]);


            /*
            |--------------------------------------------------------------------------
            | UPDATE ACCOUNT BALANCE
            |--------------------------------------------------------------------------
            */

            $updateAccountStmt =
                $this->db->prepare("
                    UPDATE accounts

                    SET
                        balance =
                            balance
                            +
                            :amount

                    WHERE
                        id = :account_id

                        AND business_id = :business_id
                ");

            $updateAccountStmt->execute([
                ':amount' =>
                    $amount,

                ':account_id' =>
                    $accountId,

                ':business_id' =>
                    $businessId,
            ]);


            /*
            |--------------------------------------------------------------------------
            | CALCULATE NEW BALANCE
            |--------------------------------------------------------------------------
            */

            $newPaid =
                $totalPaid
                +
                $amount;


            $newBalance =
                max(
                    0,
                    $totalPayable
                    -
                    $newPaid
                );


            /*
            |--------------------------------------------------------------------------
            | UPDATE LOAN STATUS
            |--------------------------------------------------------------------------
            */

            if ($newBalance <= 0.01) {

                $newLoanStatus =
                    'completed';

            } else {

                /*
                | Keep overdue status if it was already overdue.
                */

                if (
                    $loan['status']
                    ===
                    'overdue'
                ) {

                    $newLoanStatus =
                        'overdue';

                } else {

                    $newLoanStatus =
                        'active';
                }
            }


            $updateLoanStmt =
                $this->db->prepare("
                    UPDATE loans

                    SET
                        status = :status

                    WHERE
                        id = :loan_id

                        AND business_id = :business_id
                ");

            $updateLoanStmt->execute([
                ':status' =>
                    $newLoanStatus,

                ':loan_id' =>
                    $loanId,

                ':business_id' =>
                    $businessId,
            ]);


            /*
            |--------------------------------------------------------------------------
            | UPDATE SCHEDULES
            |--------------------------------------------------------------------------
            |
            | Apply payment to the oldest unpaid/partial schedules first.
            |
            */

            $remainingPayment =
                $amount;


            $scheduleStmt =
                $this->db->prepare("
                    SELECT

                        id,

                        installment_number,

                        due_date,

                        total_due,

                        paid_amount,

                        status

                    FROM loan_schedules

                    WHERE
                        loan_id = :loan_id

                        AND status IN (
                            'pending',
                            'partial',
                            'overdue'
                        )

                    ORDER BY
                        due_date ASC,
                        installment_number ASC

                    FOR UPDATE
                ");

            $scheduleStmt->execute([
                ':loan_id' =>
                    $loanId,
            ]);

            $schedules =
                $scheduleStmt->fetchAll(
                    PDO::FETCH_ASSOC
                );


            foreach (
                $schedules
                as $schedule
            ) {

                if (
                    $remainingPayment
                    <=
                    0
                ) {
                    break;
                }


                $scheduleDue =
                    (float)(
                        $schedule[
                            'total_due'
                        ]
                    );


                $schedulePaid =
                    (float)(
                        $schedule[
                            'paid_amount'
                        ]
                    );


                $scheduleRemaining =
                    max(
                        0,
                        $scheduleDue
                        -
                        $schedulePaid
                    );


                if (
                    $scheduleRemaining
                    <=
                    0
                ) {
                    continue;
                }


                $appliedAmount =
                    min(
                        $remainingPayment,
                        $scheduleRemaining
                    );


                $newSchedulePaid =
                    $schedulePaid
                    +
                    $appliedAmount;


                if (
                    $newSchedulePaid
                    >=
                    $scheduleDue
                ) {

                    $scheduleStatus =
                        'paid';

                    $paidDate =
                        $paymentDate;

                } else {

                    $scheduleStatus =
                        'partial';

                    $paidDate =
                        null;
                }


                $updateScheduleStmt =
                    $this->db->prepare("
                        UPDATE loan_schedules

                        SET

                            paid_amount =
                                :paid_amount,

                            status =
                                :status,

                            paid_date =
                                :paid_date

                        WHERE
                            id = :id
                    ");

                $updateScheduleStmt->execute([
                    ':paid_amount' =>
                        $newSchedulePaid,

                    ':status' =>
                        $scheduleStatus,

                    ':paid_date' =>
                        $paidDate,

                    ':id' =>
                        $schedule['id'],
                ]);


                $remainingPayment -=
                    $appliedAmount;
            }


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            $this->db->commit();


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            $_SESSION['success'] =
                'Payment '
                .
                $paymentNumber
                .
                ' was successfully recorded.';


            header(
                'Location: index.php?url=collections'
            );

            exit;


        } catch (
            Throwable $e
        ) {

            /*
            |--------------------------------------------------------------------------
            | ROLLBACK
            |--------------------------------------------------------------------------
            */

            if (
                $this->db->inTransaction()
            ) {

                $this->db->rollBack();
            }


            $_SESSION['error'] =
                $e->getMessage();


            header(
                'Location: index.php?url=collections/create&loan_id='
                .
                $loanId
            );

            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW COLLECTION
    |--------------------------------------------------------------------------
    |
    | URL:
    |
    | index.php?url=collections/show&id=1
    |
    */

    public function show(): void
    {
        $context =
            $this->checkAccess();

        $businessId =
            $context['businessId'];


        $id =
            (int)(
                $_GET['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['error'] =
                'Invalid collection ID.';

            header(
                'Location: index.php?url=collections'
            );

            exit;
        }


        $stmt =
            $this->db->prepare("
                SELECT

                    lp.*,

                    l.loan_number,

                    l.principal_amount
                        AS loan_principal,

                    l.total_payable
                        AS loan_total_payable,

                    l.status
                        AS loan_status,

                    b.borrower_code,

                    b.first_name,

                    b.middle_name,

                    b.last_name,

                    b.email,

                    b.phone,

                    CONCAT(
                        b.first_name,
                        ' ',
                        COALESCE(
                            CONCAT(
                                b.middle_name,
                                ' '
                            ),
                            ''
                        ),
                        b.last_name
                    ) AS borrower_name,

                    a.account_name,

                    a.account_type,

                    u.full_name
                        AS created_by_name

                FROM loan_payments lp

                INNER JOIN loans l
                    ON l.id = lp.loan_id

                INNER JOIN borrowers b
                    ON b.id = l.borrower_id

                INNER JOIN accounts a
                    ON a.id = lp.account_id

                LEFT JOIN users u
                    ON u.id = lp.created_by

                WHERE
                    lp.id = :id

                    AND lp.business_id = :business_id

                LIMIT 1
            ");

        $stmt->execute([
            ':id' =>
                $id,

            ':business_id' =>
                $businessId,
        ]);


        $collection =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        if (!$collection) {

            $_SESSION['error'] =
                'Collection record not found.';

            header(
                'Location: index.php?url=collections'
            );

            exit;
        }


        require APP_PATH .
            '/views/collections/show.php';
    }


    /*
    |--------------------------------------------------------------------------
    | VOID COLLECTION
    |--------------------------------------------------------------------------
    |
    | URL:
    |
    | index.php?url=collections/void
    |
    */

    public function void(): void
    {
        $context =
            $this->checkAccess();

        $businessId =
            $context['businessId'];


        $id =
            (int)(
                $_POST['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['error'] =
                'Invalid collection ID.';

            header(
                'Location: index.php?url=collections'
            );

            exit;
        }


        try {

            $this->db->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | GET PAYMENT
            |--------------------------------------------------------------------------
            */

            $stmt =
                $this->db->prepare("
                    SELECT

                        id,

                        business_id,

                        loan_id,

                        account_id,

                        amount,

                        status

                    FROM loan_payments

                    WHERE
                        id = :id

                        AND business_id = :business_id

                    LIMIT 1

                    FOR UPDATE
                ");

            $stmt->execute([
                ':id' =>
                    $id,

                ':business_id' =>
                    $businessId,
            ]);


            $payment =
                $stmt->fetch(
                    PDO::FETCH_ASSOC
                );


            if (!$payment) {

                throw new Exception(
                    'Collection record not found.'
                );
            }


            if (
                $payment['status']
                ===
                'void'
            ) {

                throw new Exception(
                    'This collection has already been voided.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | VOID PAYMENT
            |--------------------------------------------------------------------------
            */

            $voidStmt =
                $this->db->prepare("
                    UPDATE loan_payments

                    SET
                        status = 'void'

                    WHERE
                        id = :id

                        AND business_id = :business_id
                ");

            $voidStmt->execute([
                ':id' =>
                    $id,

                ':business_id' =>
                    $businessId,
            ]);


            /*
            |--------------------------------------------------------------------------
            | REMOVE MONEY FROM ACCOUNT
            |--------------------------------------------------------------------------
            */

            $accountStmt =
                $this->db->prepare("
                    UPDATE accounts

                    SET
                        balance =
                            balance
                            -
                            :amount

                    WHERE
                        id = :account_id

                        AND business_id = :business_id
                ");

            $accountStmt->execute([
                ':amount' =>
                    $payment['amount'],

                ':account_id' =>
                    $payment['account_id'],

                ':business_id' =>
                    $businessId,
            ]);


            /*
            |--------------------------------------------------------------------------
            | RESTORE LOAN STATUS
            |--------------------------------------------------------------------------
            */

            $paidStmt =
                $this->db->prepare("
                    SELECT

                        COALESCE(
                            SUM(amount),
                            0
                        ) AS total_paid

                    FROM loan_payments

                    WHERE
                        loan_id = :loan_id

                        AND business_id = :business_id

                        AND status = 'posted'
                ");

            $paidStmt->execute([
                ':loan_id' =>
                    $payment['loan_id'],

                ':business_id' =>
                    $businessId,
            ]);


            $paid =
                (float)(
                    $paidStmt->fetchColumn()
                    ?? 0
                );


            $loanStmt =
                $this->db->prepare("
                    SELECT

                        total_payable,

                        status

                    FROM loans

                    WHERE
                        id = :loan_id

                        AND business_id = :business_id

                    LIMIT 1
                ");

            $loanStmt->execute([
                ':loan_id' =>
                    $payment['loan_id'],

                ':business_id' =>
                    $businessId,
            ]);


            $loan =
                $loanStmt->fetch(
                    PDO::FETCH_ASSOC
                );


            if ($loan) {

                $remaining =
                    max(
                        0,
                        (float)$loan[
                            'total_payable'
                        ]
                        -
                        $paid
                    );


                if (
                    $remaining <= 0.01
                ) {

                    $loanStatus =
                        'completed';

                } else {

                    /*
                    | Determine if loan has overdue schedules.
                    */

                    $overdueStmt =
                        $this->db->prepare("
                            SELECT COUNT(*)

                            FROM loan_schedules

                            WHERE
                                loan_id = :loan_id

                                AND status = 'overdue'
                        ");

                    $overdueStmt->execute([
                        ':loan_id' =>
                            $payment['loan_id'],
                    ]);


                    $hasOverdue =
                        (int)(
                            $overdueStmt->fetchColumn()
                            ?? 0
                        );


                    $loanStatus =
                        $hasOverdue > 0
                            ? 'overdue'
                            : 'active';
                }


                $updateLoanStmt =
                    $this->db->prepare("
                        UPDATE loans

                        SET
                            status = :status

                        WHERE
                            id = :loan_id

                            AND business_id = :business_id
                    ");

                $updateLoanStmt->execute([
                    ':status' =>
                        $loanStatus,

                    ':loan_id' =>
                        $payment['loan_id'],

                    ':business_id' =>
                        $businessId,
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | REBUILD SCHEDULE PAID AMOUNTS
            |--------------------------------------------------------------------------
            */

            $scheduleResetStmt =
                $this->db->prepare("
                    UPDATE loan_schedules

                    SET
                        paid_amount = 0,

                        paid_date = NULL,

                        status = 'pending'

                    WHERE
                        loan_id = :loan_id
                ");

            $scheduleResetStmt->execute([
                ':loan_id' =>
                    $payment['loan_id'],
            ]);


            /*
            |--------------------------------------------------------------------------
            | RE-APPLY ALL POSTED PAYMENTS
            |--------------------------------------------------------------------------
            */

            $paymentsStmt =
                $this->db->prepare("
                    SELECT

                        payment_date,

                        amount

                    FROM loan_payments

                    WHERE
                        loan_id = :loan_id

                        AND business_id = :business_id

                        AND status = 'posted'

                    ORDER BY
                        payment_date ASC,
                        id ASC
                ");

            $paymentsStmt->execute([
                ':loan_id' =>
                    $payment['loan_id'],

                ':business_id' =>
                    $businessId,
            ]);


            $postedPayments =
                $paymentsStmt->fetchAll(
                    PDO::FETCH_ASSOC
                );


            foreach (
                $postedPayments
                as $postedPayment
            ) {

                $remainingPayment =
                    (float)$postedPayment[
                        'amount'
                    ];


                if (
                    $remainingPayment
                    <=
                    0
                ) {
                    continue;
                }


                $schedulesStmt =
                    $this->db->prepare("
                        SELECT

                            id,

                            total_due,

                            paid_amount

                        FROM loan_schedules

                        WHERE
                            loan_id = :loan_id

                            AND status IN (
                                'pending',
                                'partial',
                                'overdue'
                            )

                        ORDER BY
                            due_date ASC,
                            installment_number ASC

                        FOR UPDATE
                    ");

                $schedulesStmt->execute([
                    ':loan_id' =>
                        $payment['loan_id'],
                ]);


                $schedules =
                    $schedulesStmt->fetchAll(
                        PDO::FETCH_ASSOC
                    );


                foreach (
                    $schedules
                    as $schedule
                ) {

                    if (
                        $remainingPayment
                        <=
                        0
                    ) {
                        break;
                    }


                    $due =
                        (float)$schedule[
                            'total_due'
                        ];


                    $schedulePaid =
                        (float)$schedule[
                            'paid_amount'
                        ];


                    $remainingSchedule =
                        max(
                            0,
                            $due
                            -
                            $schedulePaid
                        );


                    if (
                        $remainingSchedule
                        <=
                        0
                    ) {
                        continue;
                    }


                    $applied =
                        min(
                            $remainingPayment,
                            $remainingSchedule
                        );


                    $newPaid =
                        $schedulePaid
                        +
                        $applied;


                    $scheduleStatus =
                        $newPaid >= $due
                            ? 'paid'
                            : 'partial';


                    $paidDate =
                        $newPaid >= $due
                            ? $postedPayment[
                                'payment_date'
                            ]
                            : null;


                    $updateScheduleStmt =
                        $this->db->prepare("
                            UPDATE loan_schedules

                            SET

                                paid_amount =
                                    :paid_amount,

                                status =
                                    :status,

                                paid_date =
                                    :paid_date

                            WHERE
                                id = :id
                        ");

                    $updateScheduleStmt->execute([
                        ':paid_amount' =>
                            $newPaid,

                        ':status' =>
                            $scheduleStatus,

                        ':paid_date' =>
                            $paidDate,

                        ':id' =>
                            $schedule['id'],
                    ]);


                    $remainingPayment -=
                        $applied;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            $this->db->commit();


            $_SESSION['success'] =
                'Collection successfully voided.';


            header(
                'Location: index.php?url=collections'
            );

            exit;


        } catch (
            Throwable $e
        ) {

            if (
                $this->db->inTransaction()
            ) {

                $this->db->rollBack();
            }


            $_SESSION['error'] =
                $e->getMessage();


            header(
                'Location: index.php?url=collections/show&id='
                .
                $id
            );

            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT NUMBER GENERATOR
    |--------------------------------------------------------------------------
    */

    private function generatePaymentNumber(
        int $businessId
    ): string {

        $prefix =
            'COL-'
            .
            date('Ymd')
            .
            '-';


        $stmt =
            $this->db->prepare("
                SELECT
                    payment_number

                FROM loan_payments

                WHERE
                    business_id = :business_id

                    AND payment_number LIKE :prefix

                ORDER BY
                    id DESC

                LIMIT 1
            ");


        $stmt->execute([
            ':business_id' =>
                $businessId,

            ':prefix' =>
                $prefix . '%',
        ]);


        $lastNumber =
            $stmt->fetchColumn();


        if (!$lastNumber) {

            $sequence =
                1;

        } else {

            $parts =
                explode(
                    '-',
                    $lastNumber
                );


            $sequence =
                (int)(
                    end($parts)
                )
                +
                1;
        }


        return
            $prefix
            .
            str_pad(
                (string)$sequence,
                4,
                '0',
                STR_PAD_LEFT
            );
    }
}