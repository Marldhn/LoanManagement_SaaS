<?php

declare(strict_types=1);

class Registration
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK EMAIL
    |--------------------------------------------------------------------------
    */

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER USER + BUSINESS
    |--------------------------------------------------------------------------
    */

    public function register(array $data): int
    {
        try {

            $this->pdo->beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | CREATE USER FIRST
            |--------------------------------------------------------------------------
            |
            | businesses.owner_user_id is NOT NULL,
            | so we need the user ID before creating the business.
            |
            */

            $passwordHash = password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            );

            $stmt = $this->pdo->prepare("
                INSERT INTO users (
                    name,
                    email,
                    password,
                    role,
                    status
                )
                VALUES (?, ?, ?, 'admin', 'pending')
            ");

            $stmt->execute([
                $data['name'],
                $data['email'],
                $passwordHash
            ]);

            $userId = (int) $this->pdo->lastInsertId();

            /*
            |--------------------------------------------------------------------------
            | CREATE BUSINESS
            |--------------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                INSERT INTO businesses (
                    business_name,
                    business_email,
                    business_phone,
                    address,
                    owner_user_id,
                    status
                )
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");

            $stmt->execute([
                $data['business_name'],
                $data['business_email'],
                $data['business_phone'],
                $data['address'],
                $userId
            ]);

            $businessId = (int) $this->pdo->lastInsertId();

            /*
            |--------------------------------------------------------------------------
            | CONNECT USER TO BUSINESS
            |--------------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                INSERT INTO business_users (
                    business_id,
                    user_id,
                    role,
                    status
                )
                VALUES (?, ?, 'admin', 'active')
            ");

            $stmt->execute([
                $businessId,
                $userId
            ]);

            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            $this->pdo->commit();

            return $userId;

        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}