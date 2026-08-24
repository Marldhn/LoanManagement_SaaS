<?php

require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private Category $category;


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->category = new Category();
    }


    /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */

    private function getCurrentUser(): array
    {
        return Auth::user() ?? [];
    }


    /*
    |--------------------------------------------------------------------------
    | Current User ID
    |--------------------------------------------------------------------------
    */

    private function getUserId(): ?int
    {
        $user = $this->getCurrentUser();

        if (!empty($user['id'])) {
            return (int) $user['id'];
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Current Business ID
    |--------------------------------------------------------------------------
    */

    private function getBusinessId(): int
    {
        $businessId = (int) Auth::businessId();

        if ($businessId <= 0) {

            http_response_code(403);

            exit('Business context not found.');
        }

        return $businessId;
    }


    /*
    |--------------------------------------------------------------------------
    | Categories Index
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $user = $this->getCurrentUser();

        $businessId = $this->getBusinessId();

        $categories =
            $this->category->getAll(
                $businessId
            );

        $currentUrl = 'categories';

        require BASE_PATH .
            '/app/views/categories/index.php';
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        $businessId =
            $this->getBusinessId();


        $userId =
            $this->getUserId();


        $name =
            trim(
                $_POST['name'] ?? ''
            );


        $description =
            trim(
                $_POST['description'] ?? ''
            );


        $type =
            $_POST['type']
            ?? 'both';


        $status =
            $_POST['status']
            ?? 'active';


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        if ($name === '') {

            $_SESSION['error'] =
                'Category name is required.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        $allowedTypes = [
            'expense',
            'loan',
            'both'
        ];


        if (
            !in_array(
                $type,
                $allowedTypes,
                true
            )
        ) {

            $_SESSION['error'] =
                'Invalid category type.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        $allowedStatuses = [
            'active',
            'inactive'
        ];


        if (
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {

            $_SESSION['error'] =
                'Invalid category status.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Check
        |--------------------------------------------------------------------------
        */

        if (
            $this->category->exists(
                $businessId,
                $name,
                $type
            )
        ) {

            $_SESSION['error'] =
                'A category with this name and type already exists in your business.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Create Category
        |--------------------------------------------------------------------------
        */

        $created =
            $this->category->create(
                $businessId,
                $name,
                $description !== ''
                    ? $description
                    : null,
                $type,
                $status,
                $userId
            );


        if ($created) {

            $_SESSION['success'] =
                'Category created successfully.';

        } else {

            $_SESSION['error'] =
                'Failed to create category.';
        }


        header(
            'Location: index.php?url=categories'
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
            (int) (
                $_GET['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['error'] =
                'Invalid category.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        $category =
            $this->category->find(
                $id,
                $businessId
            );


        if (!$category) {

            $_SESSION['error'] =
                'Category not found or you do not have access to it.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        $user =
            $this->getCurrentUser();


        $currentUrl =
            'categories/edit';


        require BASE_PATH .
            '/app/views/categories/edit.php';
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        $businessId =
            $this->getBusinessId();


        $id =
            (int) (
                $_POST['id']
                ?? 0
            );


        $name =
            trim(
                $_POST['name'] ?? ''
            );


        $description =
            trim(
                $_POST['description'] ?? ''
            );


        $type =
            $_POST['type']
            ?? 'both';


        $status =
            $_POST['status']
            ?? 'active';


        if (
            $id <= 0
            ||
            $name === ''
        ) {

            $_SESSION['error'] =
                'Category information is invalid.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Verify Ownership
        |--------------------------------------------------------------------------
        */

        $existing =
            $this->category->find(
                $id,
                $businessId
            );


        if (!$existing) {

            $_SESSION['error'] =
                'Category not found or you do not have access to it.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        $allowedTypes = [
            'expense',
            'loan',
            'both'
        ];


        if (
            !in_array(
                $type,
                $allowedTypes,
                true
            )
        ) {

            $_SESSION['error'] =
                'Invalid category type.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        $allowedStatuses = [
            'active',
            'inactive'
        ];


        if (
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {

            $_SESSION['error'] =
                'Invalid category status.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Duplicate Check
        |--------------------------------------------------------------------------
        */

        if (
            $this->category->exists(
                $businessId,
                $name,
                $type,
                $id
            )
        ) {

            $_SESSION['error'] =
                'A category with this name and type already exists in your business.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $updated =
            $this->category->update(
                $id,
                $businessId,
                $name,
                $description !== ''
                    ? $description
                    : null,
                $type,
                $status
            );


        if ($updated) {

            $_SESSION['success'] =
                'Category updated successfully.';

        } else {

            $_SESSION['error'] =
                'Failed to update category.';
        }


        header(
            'Location: index.php?url=categories'
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
            (int) (
                $_GET['id']
                ?? 0
            );


        if ($id <= 0) {

            $_SESSION['error'] =
                'Invalid category.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Verify Ownership
        |--------------------------------------------------------------------------
        */

        $category =
            $this->category->find(
                $id,
                $businessId
            );


        if (!$category) {

            $_SESSION['error'] =
                'Category not found or you do not have access to it.';

            header(
                'Location: index.php?url=categories'
            );

            exit;
        }


        $deleted =
            $this->category->delete(
                $id,
                $businessId
            );


        if ($deleted) {

            $_SESSION['success'] =
                'Category deleted successfully.';

        } else {

            $_SESSION['error'] =
                'Failed to delete category.';
        }


        header(
            'Location: index.php?url=categories'
        );

        exit;
    }
}
