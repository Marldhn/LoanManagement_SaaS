<?php

class Penalty
{
    private PDO $db;


    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL PENALTIES
    |--------------------------------------------------------------------------
    */

    public function getAll(int $businessId): array
    {
        $sql = "
            SELECT
                lp.id,
                lp.business_id,
                lp.loan_id,
                lp.schedule_id,
                lp.penalty_type,
                lp.penalty_base,
                lp.rate,
                lp.base_amount,
                lp.penalty_amount,
                lp.reason,
                lp.created_by,
                lp.created_at,

                l.loan_number,
                l.borrower_id,

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

                ls.installment_number,
                ls.due_date,
                ls.total_due,
                ls.paid_amount,
                ls.status AS schedule_status

            FROM loan_penalties lp

            INNER JOIN loans l
                ON l.id = lp.loan_id

            INNER JOIN borrowers b
                ON b.id = l.borrower_id

            LEFT JOIN loan_schedules ls
                ON ls.id = lp.schedule_id

            WHERE lp.business_id = :business_id

            ORDER BY
                lp.created_at DESC,
                lp.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET PENALTY BY ID
    |--------------------------------------------------------------------------
    */

    public function find(
        int $id,
        int $businessId
    ): ?array {

        $sql = "
            SELECT
                lp.*,

                l.loan_number,
                l.borrower_id,

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

                ls.installment_number,
                ls.due_date,
                ls.total_due,
                ls.paid_amount,
                ls.status AS schedule_status

            FROM loan_penalties lp

            INNER JOIN loans l
                ON l.id = lp.loan_id

            INNER JOIN borrowers b
                ON b.id = l.borrower_id

            LEFT JOIN loan_schedules ls
                ON ls.id = lp.schedule_id

            WHERE lp.id = :id
              AND lp.business_id = :business_id

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':business_id' => $businessId
        ]);

        $result =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );

        return $result ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL LOANS
    |--------------------------------------------------------------------------
    |
    | These loans are used by the Add Penalty
    | modal.
    |
    */

    public function getLoans(
        int $businessId
    ): array {

        $sql = "
            SELECT
                l.id,
                l.loan_number,
                l.borrower_id,

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
                ) AS borrower_name

            FROM loans l

            INNER JOIN borrowers b
                ON b.id = l.borrower_id

            WHERE l.business_id = :business_id

            ORDER BY
                l.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL LOAN SCHEDULES
    |--------------------------------------------------------------------------
    |
    | These schedules are used by the installment
    | dropdown in the Add Penalty modal.
    |
    */

    public function getLoanSchedules(
        int $businessId
    ): array {

        $sql = "
            SELECT
                ls.id,
                ls.loan_id,
                ls.installment_number,
                ls.due_date,
                ls.total_due,
                ls.paid_amount,
                ls.status

            FROM loan_schedules ls

            INNER JOIN loans l
                ON l.id = ls.loan_id

            WHERE l.business_id = :business_id

            ORDER BY
                ls.loan_id ASC,
                ls.installment_number ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL PENALTIES
    |--------------------------------------------------------------------------
    */

    public function getTotalPenalties(
        int $businessId
    ): float {

        $sql = "
            SELECT
                COALESCE(
                    SUM(penalty_amount),
                    0
                )

            FROM loan_penalties

            WHERE business_id = :business_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        return (float)
            $stmt->fetchColumn();
    }


    /*
    |--------------------------------------------------------------------------
    | THIS MONTH PENALTIES
    |--------------------------------------------------------------------------
    */

    public function getThisMonthPenalties(
        int $businessId
    ): float {

        $sql = "
            SELECT
                COALESCE(
                    SUM(penalty_amount),
                    0
                )

            FROM loan_penalties

            WHERE business_id = :business_id

              AND YEAR(created_at)
                    = YEAR(CURDATE())

              AND MONTH(created_at)
                    = MONTH(CURDATE())
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        return (float)
            $stmt->fetchColumn();
    }


    /*
    |--------------------------------------------------------------------------
    | PENALTY COUNT
    |--------------------------------------------------------------------------
    */

    public function getCount(
        int $businessId
    ): int {

        $sql = "
            SELECT
                COUNT(*)

            FROM loan_penalties

            WHERE business_id = :business_id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        return (int)
            $stmt->fetchColumn();
    }
}