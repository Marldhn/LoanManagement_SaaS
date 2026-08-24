<?php

class Expense
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    /*
    |--------------------------------------------------------------------------
    | Get All Expenses
    |--------------------------------------------------------------------------
    */

    public function getAll($businessId)
    {
        $sql = "
            SELECT
                e.*,
                c.name AS category_name
            FROM expenses e

            LEFT JOIN categories c
                ON c.id = e.category_id

            WHERE e.business_id = ?

            ORDER BY
                e.expense_date DESC,
                e.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $businessId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    |--------------------------------------------------------------------------
    | Find Expense
    |--------------------------------------------------------------------------
    */

    public function find($id, $businessId)
    {
        $sql = "
            SELECT
                e.*,
                c.name AS category_name
            FROM expenses e

            LEFT JOIN categories c
                ON c.id = e.category_id

            WHERE e.id = ?
            AND e.business_id = ?

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $id,
            $businessId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /*
    |--------------------------------------------------------------------------
    | Create Expense
    |--------------------------------------------------------------------------
    */

    public function create($data)
    {
        $sql = "
            INSERT INTO expenses
            (
                business_id,
                category_id,
                description,
                amount,
                expense_date,
                notes,
                status,
                created_by
            )

            VALUES
            (
                :business_id,
                :category_id,
                :description,
                :amount,
                :expense_date,
                :notes,
                :status,
                :created_by
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':business_id'  => $data['business_id'],
            ':category_id'  => !empty($data['category_id'])
                                ? $data['category_id']
                                : null,
            ':description'  => $data['description'],
            ':amount'       => $data['amount'],
            ':expense_date' => $data['expense_date'],
            ':notes'        => !empty($data['notes'])
                                ? $data['notes']
                                : null,
            ':status'       => $data['status'] ?? 'active',
            ':created_by'   => $data['created_by'] ?? null
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Expense
    |--------------------------------------------------------------------------
    */

    public function update($id, $businessId, $data)
    {
        $sql = "
            UPDATE expenses

            SET
                category_id = :category_id,
                description = :description,
                amount = :amount,
                expense_date = :expense_date,
                notes = :notes,
                status = :status

            WHERE id = :id
            AND business_id = :business_id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':category_id'  => !empty($data['category_id'])
                                ? $data['category_id']
                                : null,
            ':description'  => $data['description'],
            ':amount'       => $data['amount'],
            ':expense_date' => $data['expense_date'],
            ':notes'        => !empty($data['notes'])
                                ? $data['notes']
                                : null,
            ':status'       => $data['status'] ?? 'active',
            ':id'           => $id,
            ':business_id'  => $businessId
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Expense
    |--------------------------------------------------------------------------
    */

    public function delete($id, $businessId)
    {
        $sql = "
            DELETE FROM expenses

            WHERE id = ?
            AND business_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $id,
            $businessId
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Expense Total
    |--------------------------------------------------------------------------
    */

    public function getTotal($businessId)
    {
        $sql = "
            SELECT
                COALESCE(
                    SUM(amount),
                    0
                ) AS total
            FROM expenses

            WHERE business_id = ?

            AND status = 'active'
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $businessId
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Expense Categories
    |--------------------------------------------------------------------------
    |
    | This currently gets categories belonging to the same business.
    |
    */

    public function getCategories($businessId)
    {
        $sql = "
            SELECT
                id,
                name
            FROM categories

            WHERE business_id = ?

            ORDER BY name ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $businessId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}