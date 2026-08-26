<?php

class Loan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureLoanSchedulesTable();
    }

    private function ensureLoanSchedulesTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS loan_schedules
            (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                loan_id INT UNSIGNED NOT NULL,
                installment_number INT NOT NULL,
                due_date DATE NOT NULL,
                principal_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                interest_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                total_due DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                status ENUM(
                    'pending',
                    'paid',
                    'partial',
                    'overdue',
                    'cancelled'
                ) NOT NULL DEFAULT 'pending',
                paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                paid_date DATE NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX idx_loan_schedules_loan_id (loan_id),
                INDEX idx_loan_schedules_due_date (due_date),
                INDEX idx_loan_schedules_status (status)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci
        ";

        $this->db->exec($sql);
    }

    public function getByBusiness(int $businessId): array
    {
        $sql = "
            SELECT
                l.*,
                CONCAT(
                    COALESCE(b.first_name, ''),
                    ' ',
                    COALESCE(b.middle_name, ''),
                    ' ',
                    COALESCE(b.last_name, '')
                ) AS borrower_name,
                c.name AS category_name
            FROM loans l
            INNER JOIN borrowers b
                ON b.id = l.borrower_id
            LEFT JOIN categories c
                ON c.id = l.category_id
            WHERE l.business_id = ?
            AND b.business_id = ?
            ORDER BY l.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $businessId,
            $businessId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function all(int $businessId): array
    {
        return $this->getByBusiness($businessId);
    }

    public function findByBusiness(
        int $id,
        int $businessId
    ): ?array {

        $sql = "
            SELECT
                l.*,
                CONCAT(
                    COALESCE(b.first_name, ''),
                    ' ',
                    COALESCE(b.middle_name, ''),
                    ' ',
                    COALESCE(b.last_name, '')
                ) AS borrower_name,
                c.name AS category_name
            FROM loans l
            INNER JOIN borrowers b
                ON b.id = l.borrower_id
            LEFT JOIN categories c
                ON c.id = l.category_id
            WHERE l.id = ?
            AND l.business_id = ?
            AND b.business_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $id,
            $businessId,
            $businessId
        ]);

        $loan = $stmt->fetch(PDO::FETCH_ASSOC);

        return $loan ?: null;
    }

    public function find(
        int $id,
        int $businessId
    ): ?array {

        return $this->findByBusiness(
            $id,
            $businessId
        );
    }

    public function borrowerBelongsToBusiness(
        int $borrowerId,
        int $businessId
    ): bool {

        $stmt = $this->db->prepare(
            "
            SELECT id
            FROM borrowers
            WHERE id = ?
            AND business_id = ?
            LIMIT 1
            "
        );

        $stmt->execute([
            $borrowerId,
            $businessId
        ]);

        return (bool)$stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $paymentType =
            $data['payment_type']
            ?? 'installment';

        $allowedPaymentTypes = [
            'installment',
            'full_payment'
        ];

        if (!in_array(
            $paymentType,
            $allowedPaymentTypes,
            true
        )) {
            throw new InvalidArgumentException(
                'Invalid payment type.'
            );
        }

        $accountId =
            !empty($data['account_id'])
            ? (int)$data['account_id']
            : null;

        $sql = "
            INSERT INTO loans
            (
                business_id,
                borrower_id,
                account_id,
                category_id,
                loan_number,
                principal_amount,
                interest_rate,
                interest_type,
                payment_type,
                term,
                term_period,
                processing_fee,
                total_interest,
                total_payable,
                release_date,
                first_payment_date,
                status,
                purpose,
                notes,
                created_by
            )
            VALUES
            (
                :business_id,
                :borrower_id,
                :account_id,
                :category_id,
                :loan_number,
                :principal_amount,
                :interest_rate,
                :interest_type,
                :payment_type,
                :term,
                :term_period,
                :processing_fee,
                :total_interest,
                :total_payable,
                :release_date,
                :first_payment_date,
                :status,
                :purpose,
                :notes,
                :created_by
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' =>
                $data['business_id'],

            ':borrower_id' =>
                $data['borrower_id'],

            ':account_id' =>
                $accountId,

            ':category_id' =>
                $data['category_id'],

            ':loan_number' =>
                $data['loan_number'],

            ':principal_amount' =>
                $data['principal_amount'],

            ':interest_rate' =>
                $data['interest_rate'],

            ':interest_type' =>
                $data['interest_type'],

            ':payment_type' =>
                $paymentType,

            ':term' =>
                $data['term'],

            ':term_period' =>
                $data['term_period'],

            ':processing_fee' =>
                $data['processing_fee'],

            ':total_interest' =>
                $data['total_interest'],

            ':total_payable' =>
                $data['total_payable'],

            ':release_date' =>
                $data['release_date'],

            ':first_payment_date' =>
                $data['first_payment_date'],

            ':status' =>
                $data['status'],

            ':purpose' =>
                $data['purpose'],

            ':notes' =>
                $data['notes'],

            ':created_by' =>
                $data['created_by']
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function generateLoanNumber(
        int $businessId = 0
    ): string {

        do {

            $number =
                'LN-' .
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

            $stmt = $this->db->prepare(
                "
                SELECT COUNT(*)
                FROM loans
                WHERE loan_number = ?
                "
            );

            $stmt->execute([
                $number
            ]);

            $exists =
                (int)$stmt->fetchColumn();

        } while ($exists > 0);

        return $number;
    }

    public function update(
        int $id,
        int $businessId,
        array $data
    ): bool {

        $allowedFields = [
            'borrower_id',
            'account_id',
            'category_id',
            'principal_amount',
            'interest_rate',
            'interest_type',
            'payment_type',
            'term',
            'term_period',
            'processing_fee',
            'total_interest',
            'total_payable',
            'release_date',
            'first_payment_date',
            'status',
            'purpose',
            'notes'
        ];

        $fields = [];
        $values = [];

        foreach ($allowedFields as $field) {

            if (array_key_exists(
                $field,
                $data
            )) {

                $fields[] =
                    "{$field} = :{$field}";

                $values[$field] =
                    $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values['id'] =
            $id;

        $values['business_id'] =
            $businessId;

        $sql = "
            UPDATE loans
            SET
                " .
                implode(
                    ",\n                ",
                    $fields
                ) .
            "
            WHERE id = :id
            AND business_id = :business_id
        ";

        $stmt =
            $this->db->prepare($sql);

        return $stmt->execute(
            $values
        );
    }

    public function borrowers(
        int $businessId
    ): array {

        $stmt = $this->db->prepare(
            "
            SELECT *
            FROM borrowers
            WHERE business_id = ?
            ORDER BY first_name ASC
            "
        );

        $stmt->execute([
            $businessId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function categories(
        int $businessId
    ): array {

        $stmt = $this->db->prepare(
            "
            SELECT *
            FROM categories
            WHERE business_id = :business_id
            AND status = 'active'
            AND type IN ('loan', 'both')
            ORDER BY name ASC
            "
        );

        $stmt->execute([
            ':business_id' =>
                $businessId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function getSchedule(
        int $loanId
    ): array {

        $stmt = $this->db->prepare(
            "
            SELECT *
            FROM loan_schedules
            WHERE loan_id = ?
            ORDER BY installment_number ASC
            "
        );

        $stmt->execute([
            $loanId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function getScheduleById(
        int $scheduleId,
        int $loanId
    ): ?array {

        $stmt = $this->db->prepare(
            "
            SELECT *
            FROM loan_schedules
            WHERE id = ?
            AND loan_id = ?
            LIMIT 1
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

        return $schedule ?: null;
    }

    public function getScheduleInterest(
        int $scheduleId
    ): float {

        $stmt = $this->db->prepare(
            "
            SELECT
                interest_amount
            FROM loan_schedules
            WHERE id = ?
            LIMIT 1
            "
        );

        $stmt->execute([
            $scheduleId
        ]);

        $interest =
            $stmt->fetchColumn();

        return (float)(
            $interest ?? 0
        );
    }

    public function getPayments(
        int $loanId
    ): array {

        $stmt = $this->db->prepare(
            "
            SELECT *
            FROM loan_payments
            WHERE loan_id = ?
            ORDER BY payment_date DESC, id DESC
            "
        );

        $stmt->execute([
            $loanId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function getAllPayments(
        int $businessId
    ): array {

        $sql = "
            SELECT
                lp.*,
                l.loan_number,
                l.principal_amount,
                l.total_payable,
                l.status AS loan_status,
                CONCAT(
                    COALESCE(b.first_name, ''),
                    ' ',
                    COALESCE(b.middle_name, ''),
                    ' ',
                    COALESCE(b.last_name, '')
                ) AS borrower_name,
                a.account_name,
                ls.installment_number
            FROM loan_payments lp
            INNER JOIN loans l
                ON l.id = lp.loan_id
            INNER JOIN borrowers b
                ON b.id = l.borrower_id
            LEFT JOIN accounts a
                ON a.id = lp.account_id
            LEFT JOIN loan_schedules ls
                ON ls.id = lp.schedule_id
            WHERE lp.business_id = ?
            ORDER BY
                lp.payment_date DESC,
                lp.id DESC
        ";

        $stmt =
            $this->db->prepare($sql);

        $stmt->execute([
            $businessId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function generateSchedule(
        int $loanId,
        float $principal,
        float $interest,
        int $term,
        string $termPeriod,
        ?string $firstPaymentDate,
        string $paymentType = 'installment'
    ): void {

        $this->ensureLoanSchedulesTable();

        $allowedPaymentTypes = [
            'installment',
            'full_payment'
        ];

        if (!in_array(
            $paymentType,
            $allowedPaymentTypes,
            true
        )) {

            throw new InvalidArgumentException(
                'Invalid payment type.'
            );
        }

        if (
            $paymentType === 'full_payment'
        ) {
            $term = 1;
        }

        if ($term <= 0) {
            return;
        }

        $totalPayable =
            $principal
            + $interest;

        if (
            $paymentType === 'full_payment'
        ) {

            $principalPerPeriod =
                $principal;

            $interestPerPeriod =
                $interest;

            $paymentPerPeriod =
                $totalPayable;

        } else {

            $principalPerPeriod =
                $principal / $term;

            $interestPerPeriod =
                $interest / $term;

            $paymentPerPeriod =
                $totalPayable / $term;
        }

        $startDate =
            $firstPaymentDate
            ?: date('Y-m-d');

        try {

            $date =
                new DateTime(
                    $startDate
                );

        } catch (
            Exception $e
        ) {

            $date =
                new DateTime(
                    date('Y-m-d')
                );
        }

        $this->db->beginTransaction();

        try {

            $stmt =
                $this->db->prepare(
                    "
                    INSERT INTO loan_schedules
                    (
                        loan_id,
                        installment_number,
                        due_date,
                        principal_amount,
                        interest_amount,
                        total_due,
                        status
                    )
                    VALUES
                    (
                        :loan_id,
                        :installment_number,
                        :due_date,
                        :principal_amount,
                        :interest_amount,
                        :total_due,
                        :status
                    )
                    "
                );

            for (
                $i = 1;
                $i <= $term;
                $i++
            ) {

                if (
                    $i > 1 &&
                    $paymentType === 'installment'
                ) {

                    switch (
                        $termPeriod
                    ) {

                        case 'days':

                            $date->modify(
                                '+1 day'
                            );

                            break;

                        case 'weeks':

                            $date->modify(
                                '+1 week'
                            );

                            break;

                        case 'years':

                            $date->modify(
                                '+1 year'
                            );

                            break;

                        case 'months':

                        default:

                            $date->modify(
                                '+1 month'
                            );

                            break;
                    }
                }

                $stmt->execute([

                    ':loan_id' =>
                        $loanId,

                    ':installment_number' =>
                        $i,

                    ':due_date' =>
                        $date->format(
                            'Y-m-d'
                        ),

                    ':principal_amount' =>
                        $principalPerPeriod,

                    ':interest_amount' =>
                        $interestPerPeriod,

                    ':total_due' =>
                        $paymentPerPeriod,

                    ':status' =>
                        'pending'
                ]);
            }

            $this->db->commit();

        } catch (
            Throwable $e
        ) {

            if (
                $this->db->inTransaction()
            ) {

                $this->db->rollBack();
            }

            throw $e;
        }
    }
}