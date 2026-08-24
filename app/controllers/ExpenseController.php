<?php

require_once APP_PATH . '/models/Expense.php';
require_once APP_PATH . '/models/Category.php';


class ExpenseController
{
    private $expense;
    private $category;


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->expense = new Expense();

        $this->category = new Category();
    }


    /*
    |--------------------------------------------------------------------------
    | Get Business ID
    |--------------------------------------------------------------------------
    */

    private function getBusinessId()
    {
        return $_SESSION['user']['business_id']
            ?? $_SESSION['business_id']
            ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | Get User ID
    |--------------------------------------------------------------------------
    */

    private function getUserId()
    {
        return $_SESSION['user']['id']
            ?? $_SESSION['user_id']
            ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $businessId = $this->getBusinessId();

        if (!$businessId) {
            die('Business ID not found.');
        }

        $expenses = $this->expense->getAll(
            $businessId
        );

        $totalExpenses = $this->expense->getTotal(
            $businessId
        );

        require APP_PATH . '/views/expenses/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $businessId = $this->getBusinessId();

        if (!$businessId) {
            die('Business ID not found.');
        }

        $categories = $this->expense->getCategories(
            $businessId
        );

        require APP_PATH . '/views/expenses/create.php';
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store()
    {
        $businessId = $this->getBusinessId();

        $userId = $this->getUserId();

        if (!$businessId) {
            die('Business ID not found.');
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Required Fields
        |--------------------------------------------------------------------------
        */

        $description = trim(
            $_POST['description'] ?? ''
        );

        $amount = $_POST['amount'] ?? '';

        $expenseDate = $_POST['expense_date'] ?? '';

        $categoryId = $_POST['category_id'] ?? null;

        $notes = trim(
            $_POST['notes'] ?? ''
        );


        if ($description === '') {

            $_SESSION['error'] =
                'Expense description is required.';

            header(
                'Location: index.php?url=expenses/create'
            );

            exit;
        }


        if (
            $amount === ''
            || !is_numeric($amount)
            || $amount <= 0
        ) {

            $_SESSION['error'] =
                'Please enter a valid expense amount.';

            header(
                'Location: index.php?url=expenses/create'
            );

            exit;
        }


        if ($expenseDate === '') {

            $_SESSION['error'] =
                'Expense date is required.';

            header(
                'Location: index.php?url=expenses/create'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Create Expense
        |--------------------------------------------------------------------------
        */

        $success = $this->expense->create([
            'business_id'  => $businessId,
            'category_id'  => $categoryId,
            'description'  => $description,
            'amount'       => $amount,
            'expense_date' => $expenseDate,
            'notes'        => $notes,
            'status'       => 'active',
            'created_by'   => $userId
        ]);


        if ($success) {

            $_SESSION['success'] =
                'Expense created successfully.';

        } else {

            $_SESSION['error'] =
                'Failed to create expense.';
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

    public function edit()
    {
        $businessId = $this->getBusinessId();

        if (!$businessId) {
            die('Business ID not found.');
        }

        $id = $_GET['id'] ?? null;

        if (!$id) {

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        $expense = $this->expense->find(
            $id,
            $businessId
        );


        if (!$expense) {

            http_response_code(404);

            echo '<h1>404 - Expense Not Found</h1>';

            exit;
        }


        $categories = $this->expense->getCategories(
            $businessId
        );


        require APP_PATH . '/views/expenses/edit.php';
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update()
    {
        $businessId = $this->getBusinessId();

        if (!$businessId) {
            die('Business ID not found.');
        }


        $id = $_POST['id'] ?? null;


        if (!$id) {

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        $description = trim(
            $_POST['description'] ?? ''
        );

        $amount = $_POST['amount'] ?? '';

        $expenseDate = $_POST['expense_date'] ?? '';

        $categoryId = $_POST['category_id'] ?? null;

        $notes = trim(
            $_POST['notes'] ?? ''
        );

        $status = $_POST['status'] ?? 'active';


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
                . urlencode($id)
            );

            exit;
        }


        if (
            $amount === ''
            || !is_numeric($amount)
            || $amount <= 0
        ) {

            $_SESSION['error'] =
                'Please enter a valid expense amount.';

            header(
                'Location: index.php?url=expenses/edit&id='
                . urlencode($id)
            );

            exit;
        }


        if ($expenseDate === '') {

            $_SESSION['error'] =
                'Expense date is required.';

            header(
                'Location: index.php?url=expenses/edit&id='
                . urlencode($id)
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $success = $this->expense->update(
            $id,
            $businessId,
            [
                'category_id'  => $categoryId,
                'description'  => $description,
                'amount'       => $amount,
                'expense_date' => $expenseDate,
                'notes'        => $notes,
                'status'       => $status
            ]
        );


        if ($success) {

            $_SESSION['success'] =
                'Expense updated successfully.';

        } else {

            $_SESSION['error'] =
                'Failed to update expense.';
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

    public function delete()
    {
        $businessId = $this->getBusinessId();

        if (!$businessId) {
            die('Business ID not found.');
        }


        $id = $_GET['id'] ?? null;


        if (!$id) {

            header(
                'Location: index.php?url=expenses'
            );

            exit;
        }


        $success = $this->expense->delete(
            $id,
            $businessId
        );


        if ($success) {

            $_SESSION['success'] =
                'Expense deleted successfully.';

        } else {

            $_SESSION['error'] =
                'Failed to delete expense.';
        }


        header(
            'Location: index.php?url=expenses'
        );

        exit;
    }
}