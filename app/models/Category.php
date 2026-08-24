<?php

class Category
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    /*
    |--------------------------------------------------------------------------
    | Get All Categories
    |--------------------------------------------------------------------------
    */

    public function getAll(int $businessId): array
    {
        $sql = "
            SELECT
                c.*,
                u.username AS created_by_username
            FROM categories c
            LEFT JOIN users u
                ON u.id = c.created_by
            WHERE c.business_id = :business_id
            ORDER BY c.name ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Active Categories
    |--------------------------------------------------------------------------
    */

    public function getActive(int $businessId): array
    {
        $sql = "
            SELECT *
            FROM categories
            WHERE business_id = :business_id
              AND status = 'active'
            ORDER BY name ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Categories By Type
    |--------------------------------------------------------------------------
    |
    | expense = Expense categories
    | loan    = Loan categories
    | both    = Available to both
    |
    */

    public function getByType(
        int $businessId,
        string $type
    ): array {

        $sql = "
            SELECT *
            FROM categories
            WHERE business_id = :business_id
              AND status = 'active'
              AND type IN (:type, 'both')
            ORDER BY name ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':business_id' => $businessId,
            ':type'        => $type
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    |--------------------------------------------------------------------------
    | Find Category
    |--------------------------------------------------------------------------
    */

    public function find(
        int $id,
        int $businessId
    ): ?array {

        $sql = "
            SELECT *
            FROM categories
            WHERE id = :id
              AND business_id = :business_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id'          => $id,
            ':business_id' => $businessId
        ]);

        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        return $category ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | Check Duplicate Category
    |--------------------------------------------------------------------------
    */

    public function exists(
        int $businessId,
        string $name,
        string $type,
        ?int $excludeId = null
    ): bool {

        $sql = "
            SELECT COUNT(*)
            FROM categories
            WHERE business_id = :business_id
              AND name = :name
              AND type = :type
        ";

        if ($excludeId !== null) {

            $sql .= "
                AND id != :exclude_id
            ";
        }

        $stmt = $this->db->prepare($sql);

        $params = [
            ':business_id' => $businessId,
            ':name'        => $name,
            ':type'        => $type
        ];

        if ($excludeId !== null) {

            $params[':exclude_id'] = $excludeId;
        }

        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Create Category
    |--------------------------------------------------------------------------
    */

    public function create(
        int $businessId,
        string $name,
        ?string $description,
        string $type,
        string $status,
        ?int $createdBy
    ): bool {

        $sql = "
            INSERT INTO categories (
                business_id,
                name,
                description,
                type,
                status,
                created_by
            )
            VALUES (
                :business_id,
                :name,
                :description,
                :type,
                :status,
                :created_by
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':business_id' => $businessId,
            ':name'        => $name,
            ':description' => $description,
            ':type'        => $type,
            ':status'      => $status,
            ':created_by'  => $createdBy
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Category
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        int $businessId,
        string $name,
        ?string $description,
        string $type,
        string $status
    ): bool {

        $sql = "
            UPDATE categories
            SET
                name = :name,
                description = :description,
                type = :type,
                status = :status
            WHERE id = :id
              AND business_id = :business_id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'          => $id,
            ':business_id' => $businessId,
            ':name'        => $name,
            ':description' => $description,
            ':type'        => $type,
            ':status'      => $status
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Category
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id,
        int $businessId
    ): bool {

        $sql = "
            DELETE FROM categories
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