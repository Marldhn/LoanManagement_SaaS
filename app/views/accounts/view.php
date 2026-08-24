<?php

$account = $account ?? [];

$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'staff';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Account Details - LoanSaaS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            margin: 0;
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 18px 25px;
        }

        .content {
            padding: 25px;
        }

        .card {
            border: 0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        }

        .account-balance {
            font-size: 32px;
            font-weight: 700;
        }

        .detail-label {
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .detail-value {
            font-weight: 600;
            font-size: 16px;
        }

    </style>

</head>

<body>

<?php
require BASE_PATH . '/app/views/partials/sidebar.php';
?>

<div class="main-content">

    <div class="topbar">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h4 class="mb-1">
                    Account Details
                </h4>

                <div class="text-muted">
                    View account information
                </div>

            </div>

            <div class="text-end">

                <div class="fw-semibold">
                    <?= htmlspecialchars($userName) ?>
                </div>

                <span class="badge bg-primary text-uppercase">
                    <?= htmlspecialchars(
                        str_replace('_', ' ', $userRole)
                    ) ?>
                </span>

            </div>

        </div>

    </div>


    <div class="content">

        <div class="card">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">

                    <div>

                        <h3 class="mb-1">

                            <?= htmlspecialchars(
                                $account['account_name'] ?? ''
                            ) ?>

                        </h3>

                        <div class="text-muted">

                            <?= htmlspecialchars(
                                $account['account_type']
                                ?: 'General Account'
                            ) ?>

                        </div>

                    </div>


                    <?php if (
                        ($account['status'] ?? 'active')
                        === 'active'
                    ): ?>

                        <span class="badge bg-success">
                            Active
                        </span>

                    <?php else: ?>

                        <span class="badge bg-secondary">
                            Inactive
                        </span>

                    <?php endif; ?>

                </div>


                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="detail-label">
                            Current Balance
                        </div>

                        <div class="account-balance">

                            ₱<?= number_format(
                                (float)($account['balance'] ?? 0),
                                2
                            ) ?>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Account ID
                        </div>

                        <div class="detail-value">
                            #<?= (int)$account['id'] ?>
                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Account Type
                        </div>

                        <div class="detail-value">

                            <?= htmlspecialchars(
                                $account['account_type']
                                ?: 'General'
                            ) ?>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Status
                        </div>

                        <div class="detail-value">

                            <?= htmlspecialchars(
                                ucfirst(
                                    $account['status'] ?? 'active'
                                )
                            ) ?>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Created At
                        </div>

                        <div class="detail-value">

                            <?= !empty($account['created_at'])
                                ? date(
                                    'F d, Y h:i A',
                                    strtotime($account['created_at'])
                                )
                                : '-'
                            ?>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="detail-label">
                            Last Updated
                        </div>

                        <div class="detail-value">

                            <?= !empty($account['updated_at'])
                                ? date(
                                    'F d, Y h:i A',
                                    strtotime($account['updated_at'])
                                )
                                : '-'
                            ?>

                        </div>

                    </div>

                </div>


                <hr class="my-4">


                <div class="d-flex justify-content-between">

                    <a
                        href="<?= BASE_URL ?>/index.php?page=accounts"
                        class="btn btn-secondary"
                    >
                        Back to Accounts
                    </a>

                    <a
                        href="<?= BASE_URL ?>/index.php?page=account_edit&id=<?= (int)$account['id'] ?>"
                        class="btn btn-primary"
                    >
                        Edit Account
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>
