<?php

$user = Auth::user();

?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        SaaS Administration | Loan Management
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f7fb;

            color: #1f2937;
        }

        .navbar {
            height: 64px;

            background: #111827;

            color: #ffffff;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 30px;
        }

        .brand {
            font-size: 20px;

            font-weight: 700;
        }

        .user-area {
            display: flex;

            align-items: center;

            gap: 20px;
        }

        .user-name {
            font-size: 14px;
        }

        .role {
            background: #374151;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 600;
        }

        .logout {
            color: #ffffff;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;
        }

        .logout:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1400px;

            margin: 0 auto;

            padding: 30px;
        }

        .welcome {
            background: #ffffff;

            border-radius: 12px;

            padding: 30px;

            margin-bottom: 25px;

            box-shadow:
                0 2px 10px
                rgba(0, 0, 0, 0.05);
        }

        .welcome h1 {
            margin: 0 0 8px 0;

            font-size: 28px;
        }

        .welcome p {
            margin: 0;

            color: #6b7280;
        }

        .cards {
            display: grid;

            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(220px, 1fr)
                );

            gap: 20px;
        }

        .card {
            background: #ffffff;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 2px 10px
                rgba(0, 0, 0, 0.05);
        }

        .card-title {
            color: #6b7280;

            font-size: 14px;

            margin-bottom: 12px;
        }

        .card-value {
            font-size: 30px;

            font-weight: 700;
        }

        .section {
            margin-top: 30px;

            background: #ffffff;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 2px 10px
                rgba(0, 0, 0, 0.05);
        }

        .section h2 {
            margin-top: 0;
        }

    </style>

</head>

<body>


<nav class="navbar">

    <div class="brand">
        Loan Management SaaS
    </div>


    <div class="user-area">

        <span class="user-name">

            <?= htmlspecialchars(
                $user['full_name']
                ?? $user['username']
                ?? 'Administrator'
            ) ?>

        </span>


        <span class="role">

            <?= htmlspecialchars(
                $user['role']
                ?? 'super_admin'
            ) ?>

        </span>


        <a
            href="index.php?url=auth/logout"
            class="logout"
        >
            Logout
        </a>

    </div>

</nav>


<main class="container">


    <div class="welcome">

        <h1>
            SaaS Administration
        </h1>

        <p>
            Welcome back,
            <?= htmlspecialchars(
                $user['full_name']
                ?? $user['username']
                ?? 'Administrator'
            ) ?>.
        </p>

    </div>


    <div class="cards">


        <div class="card">

            <div class="card-title">
                Total Businesses
            </div>

            <div class="card-value">
                0
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Active Subscriptions
            </div>

            <div class="card-value">
                0
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Total Users
            </div>

            <div class="card-value">
                1
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Active Loans
            </div>

            <div class="card-value">
                0
            </div>

        </div>

    </div>


    <div class="section">

        <h2>
            Platform Administration
        </h2>

        <p>
            From this dashboard you will manage businesses,
            subscriptions, users, plans, and the entire
            Loan Management SaaS platform.
        </p>

    </div>


</main>

</body>

</html>