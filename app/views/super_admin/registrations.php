<?php

$registrations = $registrations ?? [];

$userName = $_SESSION['user_name'] ?? 'Super Admin';
$userRole = $_SESSION['role'] ?? 'super_admin';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Registration Approvals - LoanSaaS</title>

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
            background: #ffffff;
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

        .table th {
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #6c757d;
        }

        .business-name {
            font-weight: 600;
        }

        .applicant-name {
            font-weight: 600;
        }

        .small-muted {
            color: #6c757d;
            font-size: 13px;
        }

    </style>

</head>

<body>


<!-- SIDEBAR -->

<?php

$sidebarPath =
    BASE_PATH .
    '/app/views/partials/sidebar.php';

if (file_exists($sidebarPath)) {
    require $sidebarPath;
}

?>


<!-- MAIN CONTENT -->

<div class="main-content">


    <!-- TOPBAR -->

    <div class="topbar">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h4 class="mb-1">
                    Registration Approvals
                </h4>

                <div class="text-muted">
                    Review and manage new business registrations
                </div>

            </div>


            <div class="text-end">

                <div class="fw-semibold">
                    <?= htmlspecialchars($userName) ?>
                </div>

                <span class="badge bg-danger text-uppercase">
                    <?= htmlspecialchars(
                        str_replace('_', ' ', $userRole)
                    ) ?>
                </span>

            </div>

        </div>

    </div>


    <!-- CONTENT -->

    <div class="content">


        <!-- ALERTS -->

        <?php if (!empty($success)): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <?= htmlspecialchars($success) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <?php if (!empty($error)): ?>

            <div class="alert alert-danger alert-dismissible fade show">

                <?= htmlspecialchars($error) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <!-- PAGE HEADER -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h5 class="mb-1">
                    Pending Registrations
                </h5>

                <div class="text-muted">

                    <?= count($registrations) ?>

                    pending registration(s)

                </div>

            </div>

        </div>


        <!-- REGISTRATION TABLE -->

        <div class="card">

            <div class="card-body p-0">

                <?php if (empty($registrations)): ?>

                    <div class="empty-state">

                        <div
                            class="mb-3"
                            style="font-size:48px;"
                        >
                            ✓
                        </div>

                        <h5>
                            No Pending Registrations
                        </h5>

                        <p class="mb-0">
                            There are currently no registrations
                            waiting for approval.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        Applicant
                                    </th>

                                    <th>
                                        Email
                                    </th>

                                    <th>
                                        Business
                                    </th>

                                    <th>
                                        Business Contact
                                    </th>

                                    <th>
                                        Registered
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th class="text-end">
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach ($registrations as $registration): ?>

                                    <tr>

                                        <!-- APPLICANT -->

                                        <td>

                                            <div class="applicant-name">

                                                <?= htmlspecialchars(
                                                    $registration['name']
                                                ) ?>

                                            </div>

                                            <div class="small-muted">

                                                User ID:
                                                <?= (int)$registration['user_id'] ?>

                                            </div>

                                        </td>


                                        <!-- EMAIL -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $registration['email']
                                            ) ?>

                                        </td>


                                        <!-- BUSINESS -->

                                        <td>

                                            <div class="business-name">

                                                <?= htmlspecialchars(
                                                    $registration['business_name']
                                                ) ?>

                                            </div>

                                            <?php if (
                                                !empty(
                                                    $registration[
                                                        'business_email'
                                                    ]
                                                )
                                            ): ?>

                                                <div class="small-muted">

                                                    <?= htmlspecialchars(
                                                        $registration[
                                                            'business_email'
                                                        ]
                                                    ) ?>

                                                </div>

                                            <?php endif; ?>

                                        </td>


                                        <!-- CONTACT -->

                                        <td>

                                            <?php if (
                                                !empty(
                                                    $registration[
                                                        'business_phone'
                                                    ]
                                                )
                                            ): ?>

                                                <div>

                                                    <?= htmlspecialchars(
                                                        $registration[
                                                            'business_phone'
                                                        ]
                                                    ) ?>

                                                </div>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    No phone
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- REGISTERED -->

                                        <td>

                                            <?= htmlspecialchars(
                                                date(
                                                    'M d, Y h:i A',
                                                    strtotime(
                                                        $registration[
                                                            'registered_at'
                                                        ]
                                                    )
                                                )
                                            ) ?>

                                        </td>


                                        <!-- STATUS -->

                                        <td>

                                            <span class="badge bg-warning text-dark">
                                                Pending
                                            </span>

                                        </td>


                                        <!-- ACTIONS -->

                                        <td class="text-end">

                                            <div
                                                class="d-flex justify-content-end gap-2"
                                            >

                                                <!-- APPROVE -->

                                                <form
                                                    method="POST"
                                                    action="<?= BASE_URL ?>/index.php?page=registration_approve"
                                                    onsubmit="return confirm('Approve this registration?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="user_id"
                                                        value="<?= (int)$registration['user_id'] ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-success"
                                                    >
                                                        Approve
                                                    </button>

                                                </form>


                                                <!-- REJECT -->

                                                <form
                                                    method="POST"
                                                    action="<?= BASE_URL ?>/index.php?page=registration_reject"
                                                    onsubmit="return confirm('Reject this registration?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="user_id"
                                                        value="<?= (int)$registration['user_id'] ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                    >
                                                        Reject
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>