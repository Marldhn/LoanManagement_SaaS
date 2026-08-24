<?php

declare(strict_types=1);

class AccountController
{
    private Account $account;


    public function __construct()
    {
        $this->account = new Account();
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK ACCESS
    |--------------------------------------------------------------------------
    */

    private function checkAccess(): array
    {
        if (!Auth::check()) {

            header('Location: index.php?url=auth/login');
            exit;
        }


        $businessId = (int)(
            $_SESSION['business_id']
            ?? $_SESSION['business']['id']
            ?? $_SESSION['user']['business_id']
            ?? 0
        );


        $userId = (int)(
            $_SESSION['user_id']
            ?? $_SESSION['user']['id']
            ?? 0
        );


        if ($businessId <= 0) {

            die(
                '<div style="
                    font-family: Arial;
                    padding: 30px;
                    color: #b91c1c;
                    background: #fee2e2;
                    border: 1px solid #fecaca;
                    margin: 30px;
                    border-radius: 10px;
                ">
                    <h2>Account Error</h2>

                    <p>
                        Business ID was not found in the session.
                    </p>

                    <p>
                        Please check your login/session code.
                    </p>

                    <a href="index.php?url=dashboard">
                        Back to Dashboard
                    </a>
                </div>'
            );
        }


        if ($userId <= 0) {

            die(
                '<div style="
                    font-family: Arial;
                    padding: 30px;
                    color: #b91c1c;
                    background: #fee2e2;
                    border: 1px solid #fecaca;
                    margin: 30px;
                    border-radius: 10px;
                ">
                    <h2>Account Error</h2>

                    <p>
                        User ID was not found in the session.
                    </p>

                    <p>
                        Please check your login/session code.
                    </p>

                    <a href="index.php?url=dashboard">
                        Back to Dashboard
                    </a>
                </div>'
            );
        }


        return [
            'business_id' => $businessId,
            'user_id'     => $userId
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $access = $this->checkAccess();


        $accounts = $this->account->getAll(
            $access['business_id']
        );


        $success = $_SESSION['account_success'] ?? '';
        $error   = $_SESSION['account_error'] ?? '';


        unset(
            $_SESSION['account_success'],
            $_SESSION['account_error']
        );


        $user = $_SESSION['user'] ?? [];


        $userName =
            $_SESSION['user_name']
            ?? $user['full_name']
            ?? $user['username']
            ?? 'User';


        $userRole =
            $_SESSION['tenant_role']
            ?? $_SESSION['role']
            ?? $user['role']
            ?? 'staff';


        $business = $_SESSION['business'] ?? null;


        $currentUrl = $_GET['url'] ?? 'accounts';


        require BASE_PATH .
            '/app/views/accounts/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(): void
    {
        $this->checkAccess();


        $error = $_SESSION['account_error'] ?? '';


        unset(
            $_SESSION['account_error']
        );


        require BASE_PATH .
            '/app/views/accounts/create.php';
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

   public function store(): void
{
    $access = $this->checkAccess();

    $accountName = trim(
        $_POST['account_name'] ?? ''
    );

    $accountType = trim(
        $_POST['account_type'] ?? ''
    );

    /*
    |--------------------------------------------------------------------------
    | Get Opening Balance
    |--------------------------------------------------------------------------
    */

    $balance = trim(
        (string)($_POST['balance'] ?? '0')
    );

    /*
    |--------------------------------------------------------------------------
    | Validate Account Name
    |--------------------------------------------------------------------------
    */

    if ($accountName === '') {

        $_SESSION['account_error'] =
            'Account name is required.';

        header(
            'Location: index.php?url=accounts'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Balance
    |--------------------------------------------------------------------------
    */

    $balance = str_replace(
        ',',
        '',
        $balance
    );

    if ($balance === '') {

        $balance = '0.00';
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Balance
    |--------------------------------------------------------------------------
    */

    if (!is_numeric($balance)) {

        $_SESSION['account_error'] =
            'Opening balance must be a valid number.';

        header(
            'Location: index.php?url=accounts'
        );

        exit;
    }

    if ((float)$balance < 0) {

        $_SESSION['account_error'] =
            'Opening balance cannot be negative.';

        header(
            'Location: index.php?url=accounts'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Force Decimal Format
    |--------------------------------------------------------------------------
    */

    $balance = number_format(
        (float)$balance,
        2,
        '.',
        ''
    );

    /*
    |--------------------------------------------------------------------------
    | Create Account
    |--------------------------------------------------------------------------
    */

    try {

        $accountId = $this->account->create(
            $access['business_id'],
            $access['user_id'],
            $accountName,
            $accountType !== ''
                ? $accountType
                : null,
            $balance
        );

        /*
        |--------------------------------------------------------------------------
        | Verify What Was Actually Saved
        |--------------------------------------------------------------------------
        |
        | This catches cases where something else changes the balance
        | during the INSERT/database operation.
        |
        */

        $createdAccount = $this->account->find(
            $accountId,
            $access['business_id']
        );

        if (!$createdAccount) {

            throw new RuntimeException(
                'Account was created but could not be verified.'
            );
        }

        $savedBalance = number_format(
            (float)$createdAccount['balance'],
            2,
            '.',
            ''
        );

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        $_SESSION['account_success'] =
            'Account created successfully with opening balance ₱'
            . number_format(
                (float)$savedBalance,
                2
            );

        header(
            'Location: index.php?url=accounts'
        );

        exit;

    } catch (Throwable $e) {

        $_SESSION['account_error'] =
            $e->getMessage();

        header(
            'Location: index.php?url=accounts'
        );

        exit;
    }
}


    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    public function view(): void
    {
        $access = $this->checkAccess();


        $id = (int)(
            $_GET['id'] ?? 0
        );


        if ($id <= 0) {

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        $account = $this->account->find(
            $id,
            $access['business_id']
        );


        if (!$account) {

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        require BASE_PATH .
            '/app/views/accounts/view.php';
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(): void
    {
        $access = $this->checkAccess();


        $id = (int)(
            $_GET['id'] ?? 0
        );


        if ($id <= 0) {

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        $account = $this->account->find(
            $id,
            $access['business_id']
        );


        if (!$account) {

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        $error =
            $_SESSION['account_error']
            ?? '';


        unset(
            $_SESSION['account_error']
        );


        require BASE_PATH .
            '/app/views/accounts/edit.php';
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        $access = $this->checkAccess();


        $id = (int)(
            $_POST['id'] ?? 0
        );


        $accountName = trim(
            $_POST['account_name'] ?? ''
        );


        $accountType = trim(
            $_POST['account_type'] ?? ''
        );


        $balance = trim(
            $_POST['balance'] ?? '0'
        );


        $status = $_POST['status']
            ?? 'active';


        if ($id <= 0) {

            $_SESSION['account_error'] =
                'Invalid account.';

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        if ($accountName === '') {

            $_SESSION['account_error'] =
                'Account name is required.';

            header(
                'Location: index.php?url=accounts/edit&id=' . $id
            );

            exit;
        }


        if ($balance === '') {

            $balance = '0';
        }


        if (!is_numeric($balance)) {

            $_SESSION['account_error'] =
                'Balance must be a valid number.';

            header(
                'Location: index.php?url=accounts/edit&id=' . $id
            );

            exit;
        }


        if (
            !in_array(
                $status,
                ['active', 'inactive'],
                true
            )
        ) {

            $status = 'active';
        }


        try {

            $this->account->update(
                $id,
                $access['business_id'],
                $accountName,
                $accountType !== ''
                    ? $accountType
                    : null,
                (float)$balance,
                $status
            );


            $_SESSION['account_success'] =
                'Account updated successfully.';


            header(
                'Location: index.php?url=accounts'
            );

            exit;

        } catch (Throwable $e) {

            $_SESSION['account_error'] =
                $e->getMessage();


            header(
                'Location: index.php?url=accounts/edit&id=' . $id
            );

            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ADJUST BALANCE
    |--------------------------------------------------------------------------
    |
    | Adds or subtracts money from an account.
    |
    | POST:
    |
    | account_id
    | adjustment_type = add / subtract
    | amount
    | reason
    |
    */

    public function adjustBalance(): void
    {
        $access = $this->checkAccess();


        $accountId = (int)(
            $_POST['account_id'] ?? 0
        );


        $adjustmentType = trim(
            $_POST['adjustment_type'] ?? ''
        );


        $amount = trim(
            $_POST['amount'] ?? ''
        );


        $reason = trim(
            $_POST['reason'] ?? ''
        );


        /*
        |--------------------------------------------------------------------------
        | Validate Account
        |--------------------------------------------------------------------------
        */

        if ($accountId <= 0) {

            $_SESSION['account_error'] =
                'Invalid account selected.';

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Adjustment Type
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $adjustmentType,
                ['add', 'subtract'],
                true
            )
        ) {

            $_SESSION['account_error'] =
                'Invalid adjustment type.';

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Amount
        |--------------------------------------------------------------------------
        */

        if (
            $amount === ''
            || !is_numeric($amount)
            || (float)$amount <= 0
        ) {

            $_SESSION['account_error'] =
                'Adjustment amount must be greater than zero.';

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        $amount = (float)$amount;


        /*
        |--------------------------------------------------------------------------
        | Reason
        |--------------------------------------------------------------------------
        */

        if ($reason === '') {

            $reason = 'Balance adjustment';
        }


        /*
        |--------------------------------------------------------------------------
        | Process
        |--------------------------------------------------------------------------
        */

        try {

            $this->account->adjustBalance(
                $accountId,
                $access['business_id'],
                $access['user_id'],
                $adjustmentType,
                $amount,
                $reason
            );


            $_SESSION['account_success'] =
                'Account balance adjusted successfully.';


        } catch (Throwable $e) {

            $_SESSION['account_error'] =
                $e->getMessage();
        }


        header(
            'Location: index.php?url=accounts'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSFER BALANCE
    |--------------------------------------------------------------------------
    |
    | Transfers money from one account to another.
    |
    | POST:
    |
    | from_account_id
    | to_account_id
    | amount
    | description
    |
    */

    public function transferBalance(): void
    {
        $access = $this->checkAccess();


        $fromAccountId = (int)(
            $_POST['from_account_id'] ?? 0
        );


        $toAccountId = (int)(
            $_POST['to_account_id'] ?? 0
        );


        $amount = trim(
            $_POST['amount'] ?? ''
        );


        $description = trim(
            $_POST['description'] ?? ''
        );


        /*
        |--------------------------------------------------------------------------
        | Validate Source Account
        |--------------------------------------------------------------------------
        */

        if ($fromAccountId <= 0) {

            $_SESSION['account_error'] =
                'Please select the account to transfer from.';

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Destination Account
        |--------------------------------------------------------------------------
        */

        if ($toAccountId <= 0) {

            $_SESSION['account_error'] =
                'Please select the account to transfer to.';

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Same Account
        |--------------------------------------------------------------------------
        */

        if ($fromAccountId === $toAccountId) {

            $_SESSION['account_error'] =
                'You cannot transfer money to the same account.';

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Amount
        |--------------------------------------------------------------------------
        */

        if (
            $amount === ''
            || !is_numeric($amount)
            || (float)$amount <= 0
        ) {

            $_SESSION['account_error'] =
                'Transfer amount must be greater than zero.';

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        $amount = (float)$amount;


        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        if ($description === '') {

            $description = 'Account balance transfer';
        }


        /*
        |--------------------------------------------------------------------------
        | Process Transfer
        |--------------------------------------------------------------------------
        */

        try {

            $this->account->transferBalance(
                $fromAccountId,
                $toAccountId,
                $access['business_id'],
                $access['user_id'],
                $amount,
                $description
            );


            $_SESSION['account_success'] =
                'Balance transferred successfully.';


        } catch (Throwable $e) {

            $_SESSION['account_error'] =
                $e->getMessage();
        }


        header(
            'Location: index.php?url=accounts'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(): void
    {
        $access = $this->checkAccess();


        $id = (int)(
            $_POST['id'] ?? 0
        );


        if ($id <= 0) {

            $_SESSION['account_error'] =
                'Invalid account.';

            header(
                'Location: index.php?url=accounts'
            );

            exit;
        }


        try {

            $this->account->delete(
                $id,
                $access['business_id']
            );


            $_SESSION['account_success'] =
                'Account deleted successfully.';

        } catch (Throwable $e) {

            $_SESSION['account_error'] =
                $e->getMessage();
        }


        header(
            'Location: index.php?url=accounts'
        );

        exit;
    }
}