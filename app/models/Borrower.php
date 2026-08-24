<?php

class Borrower
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    /**
     * Get all borrowers for a business.
     */
    public function getAllByBusiness(int $businessId): array
    {
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

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function getAll($businessId)
{
    $sql = "
        SELECT *
        FROM borrowers
        WHERE business_id = ?
        ORDER BY first_name ASC, last_name ASC
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        $businessId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    /**
     * Find borrower by ID and business.
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

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id'          => $id,
            ':business_id' => $businessId
        ]);

        $borrower = $stmt->fetch(PDO::FETCH_ASSOC);

        return $borrower ?: null;
    }


    /**
     * Generate borrower code.
     */
    public function generateCode(int $businessId): string
    {
        do {

            $code = 'BRW-' . date('Ymd') . '-' . strtoupper(
                substr(
                    bin2hex(random_bytes(4)),
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

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':business_id'   => $businessId,
                ':borrower_code' => $code
            ]);

            $exists = $stmt->fetch();

        } while ($exists);

        return $code;
    }


    /**
     * Create borrower.
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

            ) VALUES (

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

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([

            ':business_id'   => $businessId,

            ':borrower_code' => $data['borrower_code'],

            ':first_name'    => $data['first_name'],
            ':middle_name'   => $data['middle_name'],
            ':last_name'     => $data['last_name'],

            ':email'         => $data['email'],
            ':phone'         => $data['phone'],

            ':date_of_birth' => $data['date_of_birth'],
            ':gender'        => $data['gender'],

            ':address'       => $data['address'],
            ':city'          => $data['city'],
            ':province'      => $data['province'],
            ':postal_code'   => $data['postal_code'],

            ':occupation'    => $data['occupation'],
            ':employer'      => $data['employer'],

            ':monthly_income' => $data['monthly_income'],

            ':status'        => $data['status'],

            ':notes'         => $data['notes'],

            ':created_by'    => $createdBy

        ]);
    }


    /**
     * Update borrower.
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

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([

            ':first_name'     => $data['first_name'],
            ':middle_name'    => $data['middle_name'],
            ':last_name'     => $data['last_name'],

            ':email'          => $data['email'],
            ':phone'          => $data['phone'],

            ':date_of_birth'  => $data['date_of_birth'],
            ':gender'         => $data['gender'],

            ':address'        => $data['address'],
            ':city'           => $data['city'],
            ':province'       => $data['province'],
            ':postal_code'    => $data['postal_code'],

            ':occupation'     => $data['occupation'],
            ':employer'       => $data['employer'],
            ':monthly_income' => $data['monthly_income'],

            ':status'         => $data['status'],
            ':notes'          => $data['notes'],

            ':id'             => $id,
            ':business_id'    => $businessId

        ]);
    }


    /**
     * Delete borrower.
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

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'          => $id,
            ':business_id' => $businessId
        ]);
    }
}