<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $user ?? ($_SESSION['user'] ?? []);

$categories = $categories ?? [];

$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;

unset($_SESSION['success']);
unset($_SESSION['error']);

$currentUrl = $currentUrl ?? ($_GET['url'] ?? 'categories');

?>

<!DOCTYPE html>

<html lang="en">

<head>

```
<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Categories | Loan Management SaaS
</title>

<link
    rel="stylesheet"
    href="assets/css/style.css"
>

<style>

    /* =====================================================
       CREATE CATEGORY MODAL
    ====================================================== */

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.show {
        display: flex;
    }

    .category-modal {
        width: 100%;
        max-width: 520px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: modalFadeIn 0.2s ease;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .category-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .category-modal-header h2 {
        margin: 0;
        font-size: 20px;
    }

    .modal-close {
        border: none;
        background: transparent;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
        color: #6b7280;
    }

    .modal-close:hover {
        color: #111827;
    }

    .category-modal-body {
        padding: 24px;
    }

    .category-form-group {
        margin-bottom: 18px;
    }

    .category-form-group label {
        display: block;
        margin-bottom: 7px;
        font-weight: 600;
    }

    .category-form-group input,
    .category-form-group select,
    .category-form-group textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        font-size: 14px;
        background: #fff;
    }

    .category-form-group textarea {
        min-height: 90px;
        resize: vertical;
    }

    .category-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 24px;
        border-top: 1px solid #e5e7eb;
    }

    .btn-modal-cancel {
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        padding: 9px 16px;
        border-radius: 7px;
        cursor: pointer;
    }

    .btn-modal-cancel:hover {
        background: #f3f4f6;
    }

    body.modal-open {
        overflow: hidden;
    }

</style>
```

</head>

<body>

<?php require BASE_PATH . '/app/views/layouts/sidebar.php'; ?>

<div class="main-content">

```
<!-- =====================================================
     NAVBAR
====================================================== -->

<div class="navbar">

    <div class="page-title">
        Categories
    </div>


    <div class="user-info">

        <span class="user-name">
            <?= htmlspecialchars(
                $user['full_name']
                ?? $user['username']
                ?? 'Administrator'
            ) ?>
        </span>

        <span class="badge">
            <?= htmlspecialchars(
                $user['role']
                ?? 'Administrator'
            ) ?>
        </span>

    </div>

</div>


<!-- =====================================================
     CONTENT
====================================================== -->

<div class="container">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h1>
                Categories
            </h1>

            <p>
                Manage categories used by expenses and loaning.
            </p>

        </div>


        <div>

            <button
                type="button"
                class="btn btn-primary"
                onclick="openCategoryModal()"
            >
                + Add Category
            </button>

        </div>

    </div>


    <!-- =================================================
         ALERTS
    ================================================== -->

    <?php if (!empty($success)): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($success) ?>

        </div>

    <?php endif; ?>


    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         TABLE
    ================================================== -->

    <div class="table-container">

        <?php if (empty($categories)): ?>

            <div class="empty-state">

                <h3>
                    No Categories Found
                </h3>

                <p>
                    Create your first category to use it
                    for expenses and loaning.
                </p>

            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Category</th>

                        <th>Description</th>

                        <th>Type</th>

                        <th>Status</th>

                        <th>Created By</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $categories
                    as $index => $category
                ): ?>

                    <tr>

                        <td>
                            <?= $index + 1 ?>
                        </td>


                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $category['name']
                                ) ?>
                            </strong>

                        </td>


                        <td>

                            <?php if (
                                !empty(
                                    $category['description']
                                )
                            ): ?>

                                <?= htmlspecialchars(
                                    $category['description']
                                ) ?>

                            <?php else: ?>

                                <span style="color:#9ca3af;">
                                    —
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php

                            $type =
                                $category['type']
                                ?? 'both';

                            ?>

                            <span class="status
                                <?php

                                if ($type === 'expense') {
                                    echo 'status-pending';
                                } elseif ($type === 'loan') {
                                    echo 'status-approved';
                                } else {
                                    echo 'status-active';
                                }

                                ?>
                            ">

                                <?= htmlspecialchars(
                                    ucfirst($type)
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?php if (
                                ($category['status']
                                ?? 'inactive')
                                === 'active'
                            ): ?>

                                <span class="status status-active">
                                    Active
                                </span>

                            <?php else: ?>

                                <span class="status status-inactive">
                                    Inactive
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $category[
                                    'created_by_username'
                                ]
                                ?? 'System'
                            ) ?>

                        </td>


                        <td>

                            <a
                                href="index.php?url=categories/edit&id=<?= (int) $category['id'] ?>"
                                class="btn btn-secondary"
                            >
                                Edit
                            </a>


                            <a
                                href="index.php?url=categories/delete&id=<?= (int) $category['id'] ?>"
                                class="btn btn-danger"
                                onclick="return confirm(
                                    'Are you sure you want to delete this category?'
                                );"
                            >
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

</div>
```

</div>

<!-- =========================================================
     CREATE CATEGORY MODAL
========================================================== -->

<div
    id="categoryModal"
    class="modal-overlay"
    onclick="handleCategoryModalOutsideClick(event)"
>

```
<div
    class="category-modal"
    onclick="event.stopPropagation()"
>

    <!-- HEADER -->

    <div class="category-modal-header">

        <h2>
            Create Category
        </h2>

        <button
            type="button"
            class="modal-close"
            onclick="closeCategoryModal()"
            aria-label="Close"
        >
            &times;
        </button>

    </div>


    <!-- FORM -->

    <form
        method="POST"
        action="index.php?url=categories/store"
    >

        <div class="category-modal-body">


            <!-- CATEGORY NAME -->

            <div class="category-form-group">

                <label for="category_name">
                    Category Name
                </label>

                <input
                    type="text"
                    id="category_name"
                    name="name"
                    maxlength="100"
                    required
                    placeholder="Enter category name"
                >

            </div>


            <!-- DESCRIPTION -->

            <div class="category-form-group">

                <label for="category_description">
                    Description
                </label>

                <textarea
                    id="category_description"
                    name="description"
                    maxlength="500"
                    placeholder="Enter category description"
                ></textarea>

            </div>


            <!-- TYPE -->

            <div class="category-form-group">

                <label for="category_type">
                    Type
                </label>

                <select
                    id="category_type"
                    name="type"
                    required
                >

                    <option value="loan">
                        Loan
                    </option>

                    <option value="expense">
                        Expense
                    </option>

                    <option value="both">
                        Both
                    </option>

                </select>

            </div>


            <!-- STATUS -->

            <div class="category-form-group">

                <label for="category_status">
                    Status
                </label>

                <select
                    id="category_status"
                    name="status"
                    required
                >

                    <option value="active">
                        Active
                    </option>

                    <option value="inactive">
                        Inactive
                    </option>

                </select>

            </div>

        </div>


        <!-- FOOTER -->

        <div class="category-modal-footer">

            <button
                type="button"
                class="btn-modal-cancel"
                onclick="closeCategoryModal()"
            >
                Cancel
            </button>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Create Category
            </button>

        </div>

    </form>

</div>
```

</div>

<!-- =========================================================
     MODAL JAVASCRIPT
========================================================== -->

<script>

function openCategoryModal() {

    const modal =
        document.getElementById('categoryModal');

    if (!modal) {
        return;
    }

    modal.classList.add('show');

    document.body.classList.add('modal-open');

    const nameInput =
        document.getElementById('category_name');

    if (nameInput) {
        setTimeout(function () {
            nameInput.focus();
        }, 100);
    }
}


function closeCategoryModal() {

    const modal =
        document.getElementById('categoryModal');

    if (!modal) {
        return;
    }

    modal.classList.remove('show');

    document.body.classList.remove('modal-open');
}


function handleCategoryModalOutsideClick(event) {

    if (
        event.target.id === 'categoryModal'
    ) {

        closeCategoryModal();

    }

}


document.addEventListener(
    'keydown',
    function (event) {

        if (event.key === 'Escape') {

            closeCategoryModal();

        }

    }
);

</script>

<?php if (!empty($error)): ?>

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        openCategoryModal();

    }
);

</script>

<?php endif; ?>

</body>

</html>
