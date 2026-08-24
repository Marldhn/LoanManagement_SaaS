<?php

$hash = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LoanSaaS - Password Hash Generator</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f4f6;
        }

        .container {
            width: 100%;
            max-width: 650px;
            padding: 20px;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin: 0 0 8px;
            color: #111827;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 15px;
            outline: none;
        }

        input:focus,
        textarea:focus {
            border-color: #2563eb;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
            font-family: Consolas, monospace;
        }

        .group {
            margin-bottom: 20px;
        }

        button {
            width: 100%;
            border: 0;
            padding: 13px;
            border-radius: 7px;
            background: #2563eb;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .result {
            margin-top: 25px;
        }

        .result textarea {
            background: #f9fafb;
        }

        .copy {
            margin-top: 10px;
            background: #111827;
        }

        .copy:hover {
            background: #1f2937;
        }

        .warning {
            margin-top: 20px;
            padding: 12px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            border-radius: 7px;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <h1>LoanSaaS</h1>

        <div class="subtitle">
            Password Hash Generator
        </div>

        <form method="POST">

            <div class="group">

                <label for="password">
                    Password
                </label>

                <input
                    type="text"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    value="<?= htmlspecialchars($password) ?>"
                    required
                >

            </div>

            <button type="submit">
                Generate Hash
            </button>

        </form>

        <?php if ($hash !== ''): ?>

            <div class="result">

                <div class="group">

                    <label for="hash">
                        Generated Password Hash
                    </label>

                    <textarea
                        id="hash"
                        readonly
                    ><?= htmlspecialchars($hash) ?></textarea>

                </div>

                <button
                    type="button"
                    class="copy"
                    onclick="copyHash()"
                >
                    Copy Hash
                </button>

            </div>

        <?php endif; ?>

        <div class="warning">
            For security, delete or restrict this file after development.
            Do not leave a public password-hash generator on a production server.
        </div>

    </div>

</div>

<script>
function copyHash() {
    const hash = document.getElementById('hash');

    hash.select();
    hash.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(hash.value).then(() => {
        alert('Hash copied to clipboard.');
    });
}
</script>

</body>
</html>