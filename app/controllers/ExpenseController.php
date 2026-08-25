<?php

declare(strict_types=1);

require_once APP_PATH . '/models/Expense.php';


class ExpenseController
{
    private Expense $expense;


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->expense = new Expense();
    }


    /*
    |--------------------------------------------------------------------------
    | Get Business ID
    |--------------------------------------------------------------------------
    */

    private function getBusinessId(): int
    {
        return (int)(
            $_SESSION['user']['business_id']
            ?? $_SESSION['business_id']
            ?? $_SESSION['business']['id']
            ?? 0
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get User ID
    |--------------------------------------------------------------------------
    */

    private function getUserId(): int
    {
        return (int)(
            $_SESSION['user']['id']
            ?? $_SESSION['user_id']
            ?? 0
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $businessId =
            $this->getBusinessId();


        if ($businessId <= 0) {

            die('Business ID not found.');
        }


        $expenses =
            $this->expense->getAll(
                $businessId
            );


        $totalExpenses =
            $this->expense->getTotal(
                $businessId
            );


        $categories =
            $this->expense->getCategories(
                $businessId
            );


        $accounts =
            $this->expense->getAccounts(
                $businessId
            );


        $user =
            $_SESSION['user'] ?? [];


        $success =
            $_SESSION['success'] ?? null;

        $error =
            $_SESSION['error'] ?? null;


        unset(
            $_SESSION['success'],
            $_SESSION['error']
        );


        $currentUrl =
            $_GET['url'] ?? 'expenses';


        require APP_PATH .
            '/views/expenses/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    |
    | The create form is now handled by the modal on index.php.
    |
    | This method is kept so old links don't break.
    |
    */

    public function create(): void
    {
        header(
            'Location: index.php?url=expenses'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        $businessId =
            $this->getBusinessId();

        $userId =
            $this->getUserId();


        if ($businessId <= 0) {

            $_SESSION['error'] =
                'Business ID not found.';

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Input
        |--------------------------------------------------------------------------
        */

        $description =
            trim(
                $_POST['description'] ?? ''
            );


        $amount =
            str_replace(
                ',',
                '',
                trim(
                    (string)(
                        $_POST['amount'] ?? ''
                    )
                )
            );


        $expenseDate =
            trim(
                $_POST['expense_date'] ?? ''
            );


        $categoryId =
            (int)(
                $_POST['category_id'] ?? 0
            );


        $accountId =
            (int)(
                $_POST['account_id'] ?? 0
            );


        $notes =
            trim(
                $_POST['notes'] ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | Validate Description
        |--------------------------------------------------------------------------
        */

        if ($description === '') {

            $_SESSION['error'] =
                'Expense description is required.';

            header(
                'Location: index.php?url=expenses'
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

            $_SESSION['error'] =
                'Please enter a valid expense amount.';

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Date
        |--------------------------------------------------------------------------
        */

        if ($expenseDate === '') {

            $_SESSION['error'] =
                'Expense date is required.';

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Account
        |--------------------------------------------------------------------------
        */

        if ($accountId <= 0) {

            $_SESSION['error'] =
                'Please select the account used for this expense.';

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Create Expense
        |--------------------------------------------------------------------------
        */

        try {

            $expenseId =
                $this->expense->create([
                    'business_id' =>
                        $businessId,

                    'category_id' =>
                        $categoryId > 0
                            ? $categoryId
                            : null,

                    'account_id' =>
                        $accountId,

                    'description' =>
                        $description,

                    'amount' =>
                        (float)$amount,

                    'expense_date' =>
                        $expenseDate,

                    'notes' =>
                        $notes,

                    'status' =>
                        'active',

                    'created_by' =>
                        $userId
                ]);


            $_SESSION['success'] =
                'Expense created successfully. Account balance has been deducted.';


        } catch (Throwable $e) {

            $_SESSION['error'] =
                $e->getMessage();
        }


        header(
            'Location: index.php?url=expenses'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(): void
    {
        $businessId =
            $this->getBusinessId();


        $id =
            (int)(
                $_GET['id'] ?? 0
            );


        if (
            $businessId <= 0
            || $id <= 0
        ) {

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        $expense =
            $this->expense->find(
                $id,
                $businessId
            );


        if (!$expense) {

            $_SESSION['error'] =
                'Expense not found.';

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        $categories =
            $this->expense->getCategories(
                $businessId
            );


        $accounts =
            $this->expense->getAccounts(
                $businessId
            );


        require APP_PATH .
            '/views/expenses/edit.php';
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        $businessId =
            $this->getBusinessId();


        $id =
            (int)(
                $_POST['id'] ?? 0
            );


        if (
            $businessId <= 0
            || $id <= 0
        ) {

            $_SESSION['error'] =
                'Invalid expense.';

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        $description =
            trim(
                $_POST['description'] ?? ''
            );


        $amount =
            str_replace(
                ',',
                '',
                trim(
                    (string)(
                        $_POST['amount'] ?? ''
                    )
                )
            );


        $expenseDate =
            trim(
                $_POST['expense_date'] ?? ''
            );


        $categoryId =
            (int)(
                $_POST['category_id'] ?? 0
            );


        $accountId =
            (int)(
                $_POST['account_id'] ?? 0
            );


        $notes =
            trim(
                $_POST['notes'] ?? ''
            );


        $status =
            $_POST['status'] ?? 'active';


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        if ($description === '') {

            $_SESSION['error'] =
                'Expense description is required.';

            header(
                'Location: index.php?url=expenses/edit&id='
                . $id
            );

            exit;
        }


        if (
            $amount === ''
            || !is_numeric($amount)
            || (float)$amount <= 0
        ) {

            $_SESSION['error'] =
                'Please enter a valid expense amount.';

            header(
                'Location: index.php?url=expenses/edit&id='
                . $id
            );

            exit;
        }


        if ($expenseDate === '') {

            $_SESSION['error'] =
                'Expense date is required.';

            header(
                'Location: index.php?url=expenses/edit&id='
                . $id
            );

            exit;
        }


        if ($accountId <= 0) {

            $_SESSION['error'] =
                'Please select an account.';

            header(
                'Location: index.php?url=expenses/edit&id='
                . $id
            );

            exit;
        }


        if (
            !in_array(
                $status,
                ['active', 'void'],
                true
            )
        ) {

            $status = 'active';
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        try {

            $this->expense->update(
                $id,
                $businessId,
                [
                    'category_id' =>
                        $categoryId > 0
                            ? $categoryId
                            : null,

                    'account_id' =>
                        $accountId,

                    'description' =>
                        $description,

                    'amount' =>
                        (float)$amount,

                    'expense_date' =>
                        $expenseDate,

                    'notes' =>
                        $notes,

                    'status' =>
                        $status
                ]
            );


            $_SESSION['success'] =
                'Expense updated successfully.';


        } catch (Throwable $e) {

            $_SESSION['error'] =
                $e->getMessage();
        }


        header(
            'Location: index.php?url=expenses'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(): void
    {
        $businessId =
            $this->getBusinessId();


        $id =
            (int)(
                $_GET['id']
                ?? $_POST['id']
                ?? 0
            );


        if (
            $businessId <= 0
            || $id <= 0
        ) {

            $_SESSION['error'] =
                'Invalid expense.';

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        try {

            $this->expense->delete(
                $id,
                $businessId
            );


            $_SESSION['success'] =
                'Expense deleted successfully. Account balance has been restored.';


        } catch (Throwable $e) {

            $_SESSION['error'] =
                $e->getMessage();
        }


        header(
            'Location: index.php?url=expenses'
        );

        exit;
    }
}