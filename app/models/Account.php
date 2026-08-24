<?php

declare(strict_types=1);

class Account
{
    private PDO $pdo;


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }


    /*
    |--------------------------------------------------------------------------
    | Get All Accounts
    |--------------------------------------------------------------------------
    */

    public function getAll(int $businessId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                business_id,
                account_name,
                account_type,
                balance,
                status,
                created_by,
                created_at,
                updated_at
            FROM accounts
            WHERE business_id = ?
            ORDER BY account_name ASC
        ");

        $stmt->execute([
            $businessId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    |--------------------------------------------------------------------------
    | Find Account
    |--------------------------------------------------------------------------
    */

    public function find(
        int $id,
        int $businessId
    ): ?array {

        $stmt = $this->pdo->prepare("
            SELECT
                id,
                business_id,
                account_name,
                account_type,
                balance,
                status,
                created_by,
                created_at,
                updated_at
            FROM accounts
            WHERE id = ?
              AND business_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $id,
            $businessId
        ]);

        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        return $account ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | Create Account
    |--------------------------------------------------------------------------
    */

    public function create(
    int $businessId,
    int $createdBy,
    string $accountName,
    ?string $accountType,
    string $balance
): int {

    // Normalize opening balance
    $balance = trim($balance);

    if ($balance === '') {
        $balance = '0.00';
    }

    // Remove commas such as 1,000.00
    $balance = str_replace(',', '', $balance);

    // Validate
    if (!is_numeric($balance)) {
        throw new RuntimeException(
            'Opening balance must be a valid number.'
        );
    }

    // Never allow negative opening balance
    if ((float)$balance < 0) {
        throw new RuntimeException(
            'Opening balance cannot be negative.'
        );
    }

    // Force exactly 2 decimal places
    $balance = number_format(
        (float)$balance,
        2,
        '.',
        ''
    );

    $stmt = $this->pdo->prepare("
        INSERT INTO accounts
        (
            business_id,
            account_name,
            account_type,
            balance,
            status,
            created_by
        )
        VALUES
        (
            :business_id,
            :account_name,
            :account_type,
            :balance,
            'active',
            :created_by
        )
    ");

    $stmt->bindValue(
        ':business_id',
        $businessId,
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        ':account_name',
        $accountName,
        PDO::PARAM_STR
    );

    $stmt->bindValue(
        ':account_type',
        $accountType ?: null,
        $accountType !== null && $accountType !== ''
            ? PDO::PARAM_STR
            : PDO::PARAM_NULL
    );

    // IMPORTANT:
    // DECIMAL should be sent as a string, not a PHP float.
    $stmt->bindValue(
        ':balance',
        $balance,
        PDO::PARAM_STR
    );

    $stmt->bindValue(
        ':created_by',
        $createdBy,
        PDO::PARAM_INT
    );

    $stmt->execute();

    return (int)$this->pdo->lastInsertId();
}

/*
|--------------------------------------------------------------------------
| Deduct Balance For Loan
|--------------------------------------------------------------------------
|
| Deducts the loan principal from the selected account.
|
| This method is intended to be called while another transaction
| is already running, so it does NOT start or commit its own
| transaction.
|
*/

public function deductForLoan(
    int $accountId,
    int $businessId,
    float $amount
): bool {

    if ($accountId <= 0) {

        throw new RuntimeException(
            'Invalid account selected.'
        );
    }

    if ($amount <= 0) {

        throw new RuntimeException(
            'Loan principal must be greater than zero.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Lock Account
    |--------------------------------------------------------------------------
    */

    $stmt = $this->pdo->prepare("
        SELECT
            id,
            account_name,
            balance,
            status
        FROM accounts
        WHERE id = ?
          AND business_id = ?
        FOR UPDATE
    ");

    $stmt->execute([
        $accountId,
        $businessId
    ]);

    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$account) {

        throw new RuntimeException(
            'Selected account was not found.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Check Account Status
    |--------------------------------------------------------------------------
    */

    if (
        isset($account['status'])
        && $account['status'] !== 'active'
    ) {

        throw new RuntimeException(
            'The selected account is inactive.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Check Balance
    |--------------------------------------------------------------------------
    */

    $currentBalance =
        (float)$account['balance'];

    if ($currentBalance < $amount) {

        throw new RuntimeException(
            'Insufficient balance in account "'
            . $account['account_name']
            . '". Available balance: ₱'
            . number_format(
                $currentBalance,
                2
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate New Balance
    |--------------------------------------------------------------------------
    */

    $newBalance =
        $currentBalance - $amount;

    /*
    |--------------------------------------------------------------------------
    | Update Account
    |--------------------------------------------------------------------------
    */

    $stmt = $this->pdo->prepare("
        UPDATE accounts
        SET
            balance = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
          AND business_id = ?
    ");

    $stmt->execute([
        number_format(
            $newBalance,
            2,
            '.',
            ''
        ),
        $accountId,
        $businessId
    ]);

    return true;
}


    /*
    |--------------------------------------------------------------------------
    | Update Account
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        int $businessId,
        string $accountName,
        ?string $accountType,
        float $balance,
        string $status
    ): bool {

        $stmt = $this->pdo->prepare("
            UPDATE accounts
            SET
                account_name = ?,
                account_type = ?,
                balance = ?,
                status = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
              AND business_id = ?
        ");

        return $stmt->execute([
            $accountName,
            $accountType ?: null,
            $balance,
            $status,
            $id,
            $businessId
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Adjust Balance
    |--------------------------------------------------------------------------
    |
    | $type:
    |
    | add      = increase balance
    | subtract = decrease balance
    |
    */

    public function adjustBalance(
        int $accountId,
        int $businessId,
        int $userId,
        string $type,
        float $amount,
        string $reason
    ): bool {

        if ($amount <= 0) {

            throw new RuntimeException(
                'Adjustment amount must be greater than zero.'
            );
        }


        if (
            !in_array(
                $type,
                ['add', 'subtract'],
                true
            )
        ) {

            throw new RuntimeException(
                'Invalid adjustment type.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Start Transaction
        |--------------------------------------------------------------------------
        */

        $this->pdo->beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Lock Account
            |--------------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                SELECT
                    id,
                    balance,
                    status
                FROM accounts
                WHERE id = ?
                  AND business_id = ?
                FOR UPDATE
            ");

            $stmt->execute([
                $accountId,
                $businessId
            ]);


            $account = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$account) {

                throw new RuntimeException(
                    'Account not found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Calculate New Balance
            |--------------------------------------------------------------------------
            */

            $currentBalance = (float)$account['balance'];


            if ($type === 'add') {

                $newBalance =
                    $currentBalance + $amount;

            } else {

                $newBalance =
                    $currentBalance - $amount;
            }


            /*
            |--------------------------------------------------------------------------
            | Prevent Negative Balance
            |--------------------------------------------------------------------------
            */

            if ($newBalance < 0) {

                throw new RuntimeException(
                    'Insufficient balance. The account cannot have a negative balance.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Update Account
            |--------------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                UPDATE accounts
                SET
                    balance = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
                  AND business_id = ?
            ");

            $stmt->execute([
                $newBalance,
                $accountId,
                $businessId
            ]);


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
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
    | Transfer Balance
    |--------------------------------------------------------------------------
    |
    | Transfers money from one account to another.
    |
    */

    public function transferBalance(
        int $fromAccountId,
        int $toAccountId,
        int $businessId,
        int $userId,
        float $amount,
        string $description
    ): bool {

        if ($amount <= 0) {

            throw new RuntimeException(
                'Transfer amount must be greater than zero.'
            );
        }


        if ($fromAccountId === $toAccountId) {

            throw new RuntimeException(
                'You cannot transfer money to the same account.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Start Transaction
        |--------------------------------------------------------------------------
        */

        $this->pdo->beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Lock Source Account
            |--------------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                SELECT
                    id,
                    account_name,
                    balance,
                    status
                FROM accounts
                WHERE id = ?
                  AND business_id = ?
                FOR UPDATE
            ");

            $stmt->execute([
                $fromAccountId,
                $businessId
            ]);


            $fromAccount =
                $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$fromAccount) {

                throw new RuntimeException(
                    'Source account not found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Lock Destination Account
            |--------------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                SELECT
                    id,
                    account_name,
                    balance,
                    status
                FROM accounts
                WHERE id = ?
                  AND business_id = ?
                FOR UPDATE
            ");

            $stmt->execute([
                $toAccountId,
                $businessId
            ]);


            $toAccount =
                $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$toAccount) {

                throw new RuntimeException(
                    'Destination account not found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Check Source Balance
            |--------------------------------------------------------------------------
            */

            $sourceBalance =
                (float)$fromAccount['balance'];


            if ($sourceBalance < $amount) {

                throw new RuntimeException(
                    'Insufficient balance in the source account.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Calculate New Balances
            |--------------------------------------------------------------------------
            */

            $newSourceBalance =
                $sourceBalance - $amount;


            $newDestinationBalance =
                (float)$toAccount['balance'] + $amount;


            /*
            |--------------------------------------------------------------------------
            | Update Source Account
            |--------------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                UPDATE accounts
                SET
                    balance = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
                  AND business_id = ?
            ");

            $stmt->execute([
                $newSourceBalance,
                $fromAccountId,
                $businessId
            ]);


            /*
            |--------------------------------------------------------------------------
            | Update Destination Account
            |--------------------------------------------------------------------------
            */

            $stmt = $this->pdo->prepare("
                UPDATE accounts
                SET
                    balance = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
                  AND business_id = ?
            ");

            $stmt->execute([
                $newDestinationBalance,
                $toAccountId,
                $businessId
            ]);


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
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
    | Delete Account
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id,
        int $businessId
    ): bool {

        $stmt = $this->pdo->prepare("
            DELETE FROM accounts
            WHERE id = ?
              AND business_id = ?
        ");

        return $stmt->execute([
            $id,
            $businessId
        ]);
    }
}