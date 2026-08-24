<?php

class Borrower
{
    private PDO $db;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->db =
            Database::getInstance();
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL BORROWERS
    |--------------------------------------------------------------------------
    */

    public function getAllByBusiness(
        int $businessId
    ): array {

        $sql = "
            SELECT
                id,
                business_id,
                borrower_code,
                first_name,
                middle_name,
                last_name,
                email,
                phone,
                date_of_birth,
                gender,
                address,
                city,
                province,
                postal_code,
                occupation,
                employer,
                monthly_income,
                status,
                notes,
                created_by,
                created_at,
                updated_at

            FROM borrowers

            WHERE business_id = :business_id

            ORDER BY id DESC
        ";


        $stmt =
            $this->db->prepare($sql);


        $stmt->execute([
            ':business_id' =>
                $businessId
        ]);


        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ALIAS GET ALL
    |--------------------------------------------------------------------------
    */

    public function getAll(
        int $businessId
    ): array {

        $sql = "
            SELECT *
            FROM borrowers

            WHERE business_id = ?

            ORDER BY
                first_name ASC,
                last_name ASC
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


    /*
    |--------------------------------------------------------------------------
    | FIND BORROWER
    |--------------------------------------------------------------------------
    */

    public function findById(
        int $id,
        int $businessId
    ): ?array {

        $sql = "
            SELECT *

            FROM borrowers

            WHERE id = :id

              AND business_id = :business_id

            LIMIT 1
        ";


        $stmt =
            $this->db->prepare($sql);


        $stmt->execute([

            ':id' =>
                $id,

            ':business_id' =>
                $businessId
        ]);


        $borrower =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        return $borrower ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL LOANS FOR BORROWER
    |--------------------------------------------------------------------------
    */

    public function getLoansByBorrower(
        int $borrowerId,
        int $businessId
    ): array {

        $sql = "

            SELECT

                l.id,

                l.business_id,

                l.borrower_id,

                l.account_id,

                l.category_id,

                l.loan_number,

                l.principal_amount,

                l.interest_rate,

                l.interest_type,

                l.payment_type,

                l.term,

                l.term_period,

                l.processing_fee,

                l.total_interest,

                l.total_payable,

                l.release_date,

                l.first_payment_date,

                l.status,

                l.purpose,

                l.notes,

                l.created_by,

                l.created_at,

                l.updated_at,

                c.name AS category_name,

                a.account_name,

                a.account_type,

                COALESCE(
                    (
                        SELECT
                            SUM(lp.amount)

                        FROM loan_payments lp

                        WHERE lp.loan_id = l.id
                    ),
                    0
                ) AS total_paid

            FROM loans l

            INNER JOIN borrowers b
                ON b.id = l.borrower_id

            LEFT JOIN categories c
                ON c.id = l.category_id

            LEFT JOIN accounts a
                ON a.id = l.account_id

            WHERE l.borrower_id = :borrower_id

              AND l.business_id = :business_id

              AND b.business_id = :borrower_business_id

            ORDER BY
                l.id DESC
        ";


        $stmt =
            $this->db->prepare($sql);


        $stmt->execute([

            ':borrower_id' =>
                $borrowerId,

            ':business_id' =>
                $businessId,

            ':borrower_business_id' =>
                $businessId
        ]);


        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE BORROWER CODE
    |--------------------------------------------------------------------------
    */

    public function generateCode(
        int $businessId
    ): string {

        do {

            $code =
                'BRW-'
                . date('Ymd')
                . '-'
                . strtoupper(
                    substr(
                        bin2hex(
                            random_bytes(4)
                        ),
                        0,
                        6
                    )
                );


            $sql = "
                SELECT id

                FROM borrowers

                WHERE business_id = :business_id

                  AND borrower_code = :borrower_code

                LIMIT 1
            ";


            $stmt =
                $this->db->prepare($sql);


            $stmt->execute([

                ':business_id' =>
                    $businessId,

                ':borrower_code' =>
                    $code
            ]);


            $exists =
                $stmt->fetch();

        } while ($exists);


        return $code;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE BORROWER
    |--------------------------------------------------------------------------
    */

    public function create(
        int $businessId,
        array $data,
        ?int $createdBy
    ): bool {

        $sql = "

            INSERT INTO borrowers (

                business_id,

                borrower_code,

                first_name,
                middle_name,
                last_name,

                email,
                phone,

                date_of_birth,
                gender,

                address,
                city,
                province,
                postal_code,

                occupation,
                employer,
                monthly_income,

                status,
                notes,

                created_by

            )

            VALUES (

                :business_id,

                :borrower_code,

                :first_name,
                :middle_name,
                :last_name,

                :email,
                :phone,

                :date_of_birth,
                :gender,

                :address,
                :city,
                :province,
                :postal_code,

                :occupation,
                :employer,
                :monthly_income,

                :status,
                :notes,

                :created_by

            )
        ";


        $stmt =
            $this->db->prepare($sql);


        return $stmt->execute([

            ':business_id' =>
                $businessId,

            ':borrower_code' =>
                $data['borrower_code'],

            ':first_name' =>
                $data['first_name'],

            ':middle_name' =>
                $data['middle_name'],

            ':last_name' =>
                $data['last_name'],

            ':email' =>
                $data['email'],

            ':phone' =>
                $data['phone'],

            ':date_of_birth' =>
                $data['date_of_birth'],

            ':gender' =>
                $data['gender'],

            ':address' =>
                $data['address'],

            ':city' =>
                $data['city'],

            ':province' =>
                $data['province'],

            ':postal_code' =>
                $data['postal_code'],

            ':occupation' =>
                $data['occupation'],

            ':employer' =>
                $data['employer'],

            ':monthly_income' =>
                $data['monthly_income'],

            ':status' =>
                $data['status'],

            ':notes' =>
                $data['notes'],

            ':created_by' =>
                $createdBy
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE BORROWER
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        int $businessId,
        array $data
    ): bool {

        $sql = "

            UPDATE borrowers

            SET

                first_name = :first_name,

                middle_name = :middle_name,

                last_name = :last_name,

                email = :email,

                phone = :phone,

                date_of_birth = :date_of_birth,

                gender = :gender,

                address = :address,

                city = :city,

                province = :province,

                postal_code = :postal_code,

                occupation = :occupation,

                employer = :employer,

                monthly_income = :monthly_income,

                status = :status,

                notes = :notes

            WHERE id = :id

              AND business_id = :business_id
        ";


        $stmt =
            $this->db->prepare($sql);


        return $stmt->execute([

            ':first_name' =>
                $data['first_name'],

            ':middle_name' =>
                $data['middle_name'],

            ':last_name' =>
                $data['last_name'],

            ':email' =>
                $data['email'],

            ':phone' =>
                $data['phone'],

            ':date_of_birth' =>
                $data['date_of_birth'],

            ':gender' =>
                $data['gender'],

            ':address' =>
                $data['address'],

            ':city' =>
                $data['city'],

            ':province' =>
                $data['province'],

            ':postal_code' =>
                $data['postal_code'],

            ':occupation' =>
                $data['occupation'],

            ':employer' =>
                $data['employer'],

            ':monthly_income' =>
                $data['monthly_income'],

            ':status' =>
                $data['status'],

            ':notes' =>
                $data['notes'],

            ':id' =>
                $id,

            ':business_id' =>
                $businessId
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE BORROWER
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id,
        int $businessId
    ): bool {

        $sql = "

            DELETE FROM borrowers

            WHERE id = :id

              AND business_id = :business_id
        ";


        $stmt =
            $this->db->prepare($sql);


        return $stmt->execute([

            ':id' =>
                $id,

            ':business_id' =>
                $businessId
        ]);
    }
}