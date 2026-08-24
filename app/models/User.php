<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByUsername(string $username): ?array
    {
        $sql = "
            SELECT
                id,
                username,
                email,
                password,
                full_name,
                role,
                status
            FROM users
            WHERE username = :username
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':username' => $username
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function updateLastLogin(int $userId): bool
    {
        $sql = "
            UPDATE users
            SET last_login = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $userId
        ]);
    }

    public function create(
        string $username,
        string $email,
        string $password,
        string $fullName,
        string $role = 'staff'
    ): bool {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "
            INSERT INTO users (
                username,
                email,
                password,
                full_name,
                role,
                status
            )
            VALUES (
                :username,
                :email,
                :password,
                :full_name,
                :role,
                'approved'
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':username'  => $username,
            ':email'     => $email,
            ':password'  => $hashedPassword,
            ':full_name' => $fullName,
            ':role'      => $role
        ]);
    }
}