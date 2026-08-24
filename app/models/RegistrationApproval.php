<?php

declare(strict_types=1);

class RegistrationApproval
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /*
    |--------------------------------------------------------------------------
    | GET PENDING REGISTRATIONS
    |--------------------------------------------------------------------------
    */

    public function getPendingRegistrations(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                u.id AS user_id,
                u.name,
                u.email,
                u.role AS user_role,
                u.status AS user_status,
                u.created_at AS registered_at,

                b.id AS business_id,
                b.business_name,
                b.business_email,
                b.business_phone,
                b.address,
                b.status AS business_status

            FROM users u

            INNER JOIN businesses b
                ON b.owner_user_id = u.id

            WHERE u.status = 'pending'

            ORDER BY u.created_at DESC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ONE REGISTRATION
    |--------------------------------------------------------------------------
    */

    public function find(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                u.id AS user_id,
                u.name,
                u.email,
                u.role AS user_role,
                u.status AS user_status,
                u.created_at AS registered_at,

                b.id AS business_id,
                b.business_name,
                b.business_email,
                b.business_phone,
                b.address,
                b.status AS business_status

            FROM users u

            INNER JOIN businesses b
                ON b.owner_user_id = u.id

            WHERE u.id = ?

            LIMIT 1
        ");

        $stmt->execute([$userId]);

        $registration = $stmt->fetch(PDO::FETCH_ASSOC);

        return $registration ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE REGISTRATION
    |--------------------------------------------------------------------------
    */

    public function approve(int $userId): bool
    {
        try {

            $this->pdo->beginTransaction();

            /*
            |----------------------------------------------------------------------
            | Check user
            |----------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                SELECT id
                FROM users
                WHERE id = ?
                AND status = 'pending'
                LIMIT 1
            ");

            $stmt->execute([$userId]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new RuntimeException(
                    'Registration was not found or has already been processed.'
                );
            }

            /*
            |----------------------------------------------------------------------
            | Activate user
            |----------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                UPDATE users
                SET status = 'active'
                WHERE id = ?
            ");

            $stmt->execute([$userId]);

            /*
            |----------------------------------------------------------------------
            | Activate business
            |----------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                UPDATE businesses
                SET status = 'active'
                WHERE owner_user_id = ?
            ");

            $stmt->execute([$userId]);

            /*
            |----------------------------------------------------------------------
            | Activate business relationship
            |----------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                UPDATE business_users
                SET status = 'active'
                WHERE user_id = ?
            ");

            $stmt->execute([$userId]);

            /*
            |----------------------------------------------------------------------
            | Commit
            |----------------------------------------------------------------------
            */

            $this->pdo->commit();

            return true;

        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT REGISTRATION
    |--------------------------------------------------------------------------
    */

    public function reject(int $userId): bool
    {
        try {

            $this->pdo->beginTransaction();

            /*
            |----------------------------------------------------------------------
            | Check user
            |----------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                SELECT id
                FROM users
                WHERE id = ?
                AND status = 'pending'
                LIMIT 1
            ");

            $stmt->execute([$userId]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new RuntimeException(
                    'Registration was not found or has already been processed.'
                );
            }

            /*
            |----------------------------------------------------------------------
            | Reject user
            |----------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                UPDATE users
                SET status = 'rejected'
                WHERE id = ?
            ");

            $stmt->execute([$userId]);

            /*
            |----------------------------------------------------------------------
            | Reject business
            |----------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                UPDATE businesses
                SET status = 'rejected'
                WHERE owner_user_id = ?
            ");

            $stmt->execute([$userId]);

            /*
            |----------------------------------------------------------------------
            | Deactivate business relationship
            |----------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                UPDATE business_users
                SET status = 'inactive'
                WHERE user_id = ?
            ");

            $stmt->execute([$userId]);

            /*
            |----------------------------------------------------------------------
            | Commit
            |----------------------------------------------------------------------
            */

            $this->pdo->commit();

            return true;

        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}