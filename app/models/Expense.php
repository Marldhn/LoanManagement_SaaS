<?php

declare(strict_types=1);

class Expense
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    /*
    |--------------------------------------------------------------------------
    | Get All Expenses
    |--------------------------------------------------------------------------
    */

    public function getAll(int $businessId): array
    {
        $sql = "
            SELECT
                e.*,

                c.name AS category_name,

                a.account_name,
                a.account_type,
                a.balance AS account_balance

            FROM expenses e

            LEFT JOIN categories c
                ON c.id = e.category_id

            LEFT JOIN accounts a
                ON a.id = e.account_id
                AND a.business_id = e.business_id

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

    public function find(
        int $id,
        int $businessId
    ): ?array {

        $sql = "
            SELECT
                e.*,

                c.name AS category_name,

                a.account_name,
                a.account_type,
                a.balance AS account_balance

            FROM expenses e

            LEFT JOIN categories c
                ON c.id = e.category_id

            LEFT JOIN accounts a
                ON a.id = e.account_id
                AND a.business_id = e.business_id

            WHERE e.id = ?
              AND e.business_id = ?

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $id,
            $businessId
        ]);

        $expense = $stmt->fetch(PDO::FETCH_ASSOC);

        return $expense ?: null;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Accounts
    |--------------------------------------------------------------------------
    */

    public function getAccounts(int $businessId): array
    {
        $sql = "
            SELECT
                id,
                account_name,
                account_type,
                balance,
                status

            FROM accounts

            WHERE business_id = ?

            AND status = 'active'

            ORDER BY account_name ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $businessId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    |--------------------------------------------------------------------------
    | Create Expense + Deduct Account
    |--------------------------------------------------------------------------
    |
    | Both operations happen inside ONE transaction.
    |
    */

    public function create(array $data): int
    {
        $businessId = (int)$data['business_id'];
        $accountId  = (int)$data['account_id'];
        $amount     = (float)$data['amount'];

        if ($businessId <= 0) {
            throw new RuntimeException(
                'Invalid business.'
            );
        }

        if ($accountId <= 0) {
            throw new RuntimeException(
                'Please select an account.'
            );
        }

        if ($amount <= 0) {
            throw new RuntimeException(
                'Expense amount must be greater than zero.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Start Transaction
        |--------------------------------------------------------------------------
        */

        $this->db->beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Lock Account
            |--------------------------------------------------------------------------
            */

            $stmt = $this->db->prepare("
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
            | Account Status
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
            | Deduct Account
            |--------------------------------------------------------------------------
            */

            $stmt = $this->db->prepare("
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


            /*
            |--------------------------------------------------------------------------
            | Insert Expense
            |--------------------------------------------------------------------------
            */

            $stmt = $this->db->prepare("
                INSERT INTO expenses
                (
                    business_id,
                    category_id,
                    account_id,
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
                    :account_id,
                    :description,
                    :amount,
                    :expense_date,
                    :notes,
                    :status,
                    :created_by
                )
            ");

            $stmt->execute([

                ':business_id' =>
                    $businessId,

                ':category_id' =>
                    !empty($data['category_id'])
                        ? (int)$data['category_id']
                        : null,

                ':account_id' =>
                    $accountId,

                ':description' =>
                    $data['description'],

                ':amount' =>
                    number_format(
                        $amount,
                        2,
                        '.',
                        ''
                    ),

                ':expense_date' =>
                    $data['expense_date'],

                ':notes' =>
                    !empty($data['notes'])
                        ? $data['notes']
                        : null,

                ':status' =>
                    $data['status'] ?? 'active',

                ':created_by' =>
                    $data['created_by'] ?? null
            ]);


            $expenseId =
                (int)$this->db->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            $this->db->commit();


            return $expenseId;


        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {

                $this->db->rollBack();
            }

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Update Expense
    |--------------------------------------------------------------------------
    |
    | This correctly handles:
    |
    | Same account + changed amount
    | Different account
    |
    */

    public function update(
        int $id,
        int $businessId,
        array $data
    ): bool {

        $newAccountId =
            (int)$data['account_id'];

        $newAmount =
            (float)$data['amount'];


        if ($newAccountId <= 0) {

            throw new RuntimeException(
                'Please select an account.'
            );
        }


        if ($newAmount <= 0) {

            throw new RuntimeException(
                'Expense amount must be greater than zero.'
            );
        }


        $this->db->beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Lock Expense
            |--------------------------------------------------------------------------
            */

            $stmt = $this->db->prepare("
                SELECT
                    id,
                    account_id,
                    amount,
                    status

                FROM expenses

                WHERE id = ?
                  AND business_id = ?

                FOR UPDATE
            ");

            $stmt->execute([
                $id,
                $businessId
            ]);

            $oldExpense =
                $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$oldExpense) {

                throw new RuntimeException(
                    'Expense not found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Only Active Expenses Affect Accounts
            |--------------------------------------------------------------------------
            */

            $oldAccountId =
                (int)($oldExpense['account_id'] ?? 0);

            $oldAmount =
                (float)$oldExpense['amount'];

            $oldStatus =
                $oldExpense['status'];


            /*
            |--------------------------------------------------------------------------
            | Lock Old Account
            |--------------------------------------------------------------------------
            */

            if ($oldAccountId > 0) {

                $stmt = $this->db->prepare("
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
                    $oldAccountId,
                    $businessId
                ]);

                $oldAccount =
                    $stmt->fetch(PDO::FETCH_ASSOC);


                if (!$oldAccount) {

                    throw new RuntimeException(
                        'Original expense account was not found.'
                    );
                }
            } else {

                $oldAccount = null;
            }


            /*
            |--------------------------------------------------------------------------
            | Lock New Account
            |--------------------------------------------------------------------------
            */

            if (
                $newAccountId !== $oldAccountId
                || $oldAccountId <= 0
            ) {

                $stmt = $this->db->prepare("
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
                    $newAccountId,
                    $businessId
                ]);

                $newAccount =
                    $stmt->fetch(PDO::FETCH_ASSOC);


                if (!$newAccount) {

                    throw new RuntimeException(
                        'Selected account was not found.'
                    );
                }

            } else {

                $newAccount =
                    $oldAccount;
            }


            /*
            |--------------------------------------------------------------------------
            | Check New Account Status
            |--------------------------------------------------------------------------
            */

            if (
                $newAccount['status'] !== 'active'
            ) {

                throw new RuntimeException(
                    'The selected account is inactive.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Restore Original Expense Amount
            |--------------------------------------------------------------------------
            |
            | Only active expenses have affected the account.
            |
            */

            if (
                $oldStatus === 'active'
                && $oldAccountId > 0
            ) {

                $restoredBalance =
                    (float)$oldAccount['balance']
                    + $oldAmount;


                $stmt = $this->db->prepare("
                    UPDATE accounts

                    SET
                        balance = ?,
                        updated_at = CURRENT_TIMESTAMP

                    WHERE id = ?
                      AND business_id = ?
                ");

                $stmt->execute([
                    number_format(
                        $restoredBalance,
                        2,
                        '.',
                        ''
                    ),
                    $oldAccountId,
                    $businessId
                ]);


                /*
                |----------------------------------------------------------------------
                | Refresh New Account Balance
                |----------------------------------------------------------------------
                */

                if (
                    $newAccountId === $oldAccountId
                ) {

                    $newAccount['balance'] =
                        $restoredBalance;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Deduct New Amount
            |--------------------------------------------------------------------------
            |
            | If status is active, the new amount affects the account.
            |
            */

            if ($data['status'] === 'active') {

                $availableBalance =
                    (float)$newAccount['balance'];


                if (
                    $availableBalance < $newAmount
                ) {

                    throw new RuntimeException(
                        'Insufficient balance in account "'
                        . $newAccount['account_name']
                        . '". Available balance: ₱'
                        . number_format(
                            $availableBalance,
                            2
                        )
                    );
                }


                $finalBalance =
                    $availableBalance - $newAmount;


                $stmt = $this->db->prepare("
                    UPDATE accounts

                    SET
                        balance = ?,
                        updated_at = CURRENT_TIMESTAMP

                    WHERE id = ?
                      AND business_id = ?
                ");

                $stmt->execute([
                    number_format(
                        $finalBalance,
                        2,
                        '.',
                        ''
                    ),
                    $newAccountId,
                    $businessId
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Update Expense
            |--------------------------------------------------------------------------
            */

            $stmt = $this->db->prepare("
                UPDATE expenses

                SET
                    category_id = :category_id,
                    account_id = :account_id,
                    description = :description,
                    amount = :amount,
                    expense_date = :expense_date,
                    notes = :notes,
                    status = :status

                WHERE id = :id
                  AND business_id = :business_id
            ");

            $stmt->execute([

                ':category_id' =>
                    !empty($data['category_id'])
                        ? (int)$data['category_id']
                        : null,

                ':account_id' =>
                    $newAccountId,

                ':description' =>
                    $data['description'],

                ':amount' =>
                    number_format(
                        $newAmount,
                        2,
                        '.',
                        ''
                    ),

                ':expense_date' =>
                    $data['expense_date'],

                ':notes' =>
                    !empty($data['notes'])
                        ? $data['notes']
                        : null,

                ':status' =>
                    $data['status'],

                ':id' =>
                    $id,

                ':business_id' =>
                    $businessId
            ]);


            $this->db->commit();


            return true;


        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {

                $this->db->rollBack();
            }

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Expense
    |--------------------------------------------------------------------------
    |
    | Deleting an active expense restores the money to the account.
    |
    */

    public function delete(
        int $id,
        int $businessId
    ): bool {

        $this->db->beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | Lock Expense
            |--------------------------------------------------------------------------
            */

            $stmt = $this->db->prepare("
                SELECT
                    id,
                    account_id,
                    amount,
                    status

                FROM expenses

                WHERE id = ?
                  AND business_id = ?

                FOR UPDATE
            ");

            $stmt->execute([
                $id,
                $businessId
            ]);

            $expense =
                $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$expense) {

                throw new RuntimeException(
                    'Expense not found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Restore Account Balance
            |--------------------------------------------------------------------------
            */

            if (
                $expense['status'] === 'active'
                && !empty($expense['account_id'])
            ) {

                $stmt = $this->db->prepare("
                    SELECT
                        id,
                        balance

                    FROM accounts

                    WHERE id = ?
                      AND business_id = ?

                    FOR UPDATE
                ");

                $stmt->execute([
                    $expense['account_id'],
                    $businessId
                ]);

                $account =
                    $stmt->fetch(PDO::FETCH_ASSOC);


                if (!$account) {

                    throw new RuntimeException(
                        'Expense account was not found.'
                    );
                }


                $newBalance =
                    (float)$account['balance']
                    + (float)$expense['amount'];


                $stmt = $this->db->prepare("
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
                    $expense['account_id'],
                    $businessId
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Delete Expense
            |--------------------------------------------------------------------------
            */

            $stmt = $this->db->prepare("
                DELETE FROM expenses

                WHERE id = ?
                  AND business_id = ?
            ");

            $stmt->execute([
                $id,
                $businessId
            ]);


            $this->db->commit();


            return true;


        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {

                $this->db->rollBack();
            }

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Get Expense Total
    |--------------------------------------------------------------------------
    */

    public function getTotal(int $businessId): float
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

        $result =
            $stmt->fetch(PDO::FETCH_ASSOC);

        return (float)(
            $result['total'] ?? 0
        );
    }


    /*
|--------------------------------------------------------------------------
| Get Expense Categories
|--------------------------------------------------------------------------
|
| Only return categories that are:
|
| expense = specifically for expenses
| both    = usable for expenses and loans
|
*/

public function getCategories($businessId)
{
    $sql = "
        SELECT
            id,
            name,
            description,
            type
        FROM categories

        WHERE business_id = ?

        AND type IN ('expense', 'both')

        AND status = 'active'

        ORDER BY name ASC
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        $businessId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}