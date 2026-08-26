<?php

/*
|--------------------------------------------------------------------------
| LoanManagement SaaS - Web Code Modifier
|--------------------------------------------------------------------------
| Location:
| C:\xampp\htdocs\LoanManagement_SaaS\modifier.php
|
| Open:
| http://localhost/LoanManagement_SaaS/modifier.php
|--------------------------------------------------------------------------
*/

declare(strict_types=1);

session_start();

/*
|--------------------------------------------------------------------------
| CONFIGURATION
|--------------------------------------------------------------------------
*/

$projectRoot = realpath(__DIR__);

if ($projectRoot === false) {
    die('Project directory could not be found.');
}

$backupDirectory = $projectRoot . DIRECTORY_SEPARATOR . '_modifier_backups';

$allowedExtensions = [
    'php',
    'css',
    'js',
    'html',
    'htm',
    'json',
    'sql',
    'txt',
    'xml',
    'md',
    'ini',
    'env',
];


/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

function h(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


function normalizePath(string $path): string
{
    return str_replace(
        '\\',
        '/',
        $path
    );
}


function relativePath(string $absolutePath, string $root): string
{
    $absolutePath = normalizePath($absolutePath);
    $root = rtrim(
        normalizePath($root),
        '/'
    );

    if (
        str_starts_with(
            $absolutePath,
            $root . '/'
        )
    ) {
        return substr(
            $absolutePath,
            strlen($root) + 1
        );
    }

    return basename($absolutePath);
}


function isAllowedFile(
    string $file,
    array $allowedExtensions
): bool {

    if (!is_file($file)) {
        return false;
    }

    $extension =
        strtolower(
            pathinfo(
                $file,
                PATHINFO_EXTENSION
            )
        );

    return in_array(
        $extension,
        $allowedExtensions,
        true
    );
}


function isSafePath(
    string $file,
    string $root
): bool {

    $realFile =
        realpath($file);

    $realRoot =
        realpath($root);

    if (
        $realFile === false ||
        $realRoot === false
    ) {
        return false;
    }

    $realFile =
        normalizePath($realFile);

    $realRoot =
        rtrim(
            normalizePath($realRoot),
            '/'
        );

    return
        $realFile === $realRoot
        ||
        str_starts_with(
            $realFile,
            $realRoot . '/'
        );
}


function createBackup(
    string $file,
    string $root,
    string $backupDirectory
): ?string {

    if (!is_file($file)) {
        return null;
    }

    if (!isSafePath($file, $root)) {
        return null;
    }

    if (
        !is_dir($backupDirectory)
        &&
        !mkdir(
            $backupDirectory,
            0777,
            true
        )
    ) {
        return null;
    }

    $relative =
        relativePath(
            $file,
            $root
        );

    $safeName =
        str_replace(
            [
                '/',
                '\\',
                ':'
            ],
            '_',
            $relative
        );

    $timestamp =
        date('Y-m-d_H-i-s');

    $backupFile =
        $backupDirectory
        . DIRECTORY_SEPARATOR
        . $timestamp
        . '__'
        . $safeName
        . '.bak';

    if (
        copy(
            $file,
            $backupFile
        )
    ) {
        return $backupFile;
    }

    return null;
}


/*
|--------------------------------------------------------------------------
| CREATE BACKUP DIRECTORY
|--------------------------------------------------------------------------
*/

if (
    !is_dir($backupDirectory)
) {

    @mkdir(
        $backupDirectory,
        0777,
        true
    );
}


/*
|--------------------------------------------------------------------------
| GET REQUEST VALUES
|--------------------------------------------------------------------------
*/

$selectedFile =
    isset($_GET['file'])
        ? trim(
            (string)$_GET['file']
        )
        : '';

$selectedAbsolutePath = '';

$code = '';

$message = '';

$messageType = '';


/*
|--------------------------------------------------------------------------
| SAVE FILE
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    $action =
        $_POST['action']
        ?? '';

    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */

    if (
        $action === 'save'
    ) {

        $relativeFile =
            trim(
                (string)(
                    $_POST['file']
                    ?? ''
                )
            );

        $newCode =
            (string)(
                $_POST['code']
                ?? ''
            );

        if (
            $relativeFile === ''
        ) {

            $message =
                'Please select a file.';

            $messageType =
                'error';

        } else {

            $candidate =
                $projectRoot
                . DIRECTORY_SEPARATOR
                . str_replace(
                    [
                        '/',
                        '\\'
                    ],
                    DIRECTORY_SEPARATOR,
                    $relativeFile
                );

            $realCandidate =
                realpath(
                    $candidate
                );

            if (
                $realCandidate === false
                ||
                !isSafePath(
                    $realCandidate,
                    $projectRoot
                )
            ) {

                $message =
                    'Invalid file path.';

                $messageType =
                    'error';

            } elseif (
                !isAllowedFile(
                    $realCandidate,
                    $allowedExtensions
                )
            ) {

                $message =
                    'This file type is not allowed.';

                $messageType =
                    'error';

            } else {

                /*
                |--------------------------------------------------------------------------
                | BACKUP BEFORE SAVE
                |--------------------------------------------------------------------------
                */

                $backup =
                    createBackup(
                        $realCandidate,
                        $projectRoot,
                        $backupDirectory
                    );

                if (
                    $backup === null
                ) {

                    $message =
                        'Could not create backup. File was NOT changed.';

                    $messageType =
                        'error';

                } else {

                    $result =
                        file_put_contents(
                            $realCandidate,
                            $newCode
                        );

                    if (
                        $result === false
                    ) {

                        $message =
                            'Failed to save the file. Your backup is still available.';

                        $messageType =
                            'error';

                    } else {

                        $selectedFile =
                            relativePath(
                                $realCandidate,
                                $projectRoot
                            );

                        $selectedAbsolutePath =
                            $realCandidate;

                        $code =
                            $newCode;

                        $message =
                            'File saved successfully. A backup was created before saving.';

                        $messageType =
                            'success';

                    }

                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SAVE AS
    |--------------------------------------------------------------------------
    */

    if (
        $action === 'save_as'
    ) {

        $newRelativeFile =
            trim(
                (string)(
                    $_POST['new_file']
                    ?? ''
                )
            );

        $newCode =
            (string)(
                $_POST['code']
                ?? ''
            );

        if (
            $newRelativeFile === ''
        ) {

            $message =
                'Please enter a file name.';

            $messageType =
                'error';

        } else {

            $newRelativeFile =
                str_replace(
                    [
                        '/',
                        '\\'
                    ],
                    DIRECTORY_SEPARATOR,
                    $newRelativeFile
                );

            $newAbsolutePath =
                $projectRoot
                . DIRECTORY_SEPARATOR
                . $newRelativeFile;

            $parentDirectory =
                dirname(
                    $newAbsolutePath
                );

            if (
                !isSafePath(
                    $parentDirectory,
                    $projectRoot
                )
                &&
                realpath(
                    $parentDirectory
                ) !== realpath(
                    $projectRoot
                )
            ) {

                $message =
                    'Invalid destination folder.';

                $messageType =
                    'error';

            } else {

                if (
                    !is_dir(
                        $parentDirectory
                    )
                ) {

                    if (
                        !mkdir(
                            $parentDirectory,
                            0777,
                            true
                        )
                    ) {

                        $message =
                            'Could not create destination folder.';

                        $messageType =
                            'error';

                    }

                }

                if (
                    $messageType !== 'error'
                ) {

                    $extension =
                        strtolower(
                            pathinfo(
                                $newAbsolutePath,
                                PATHINFO_EXTENSION
                            )
                        );

                    if (
                        !in_array(
                            $extension,
                            $allowedExtensions,
                            true
                        )
                    ) {

                        $message =
                            'This file type is not allowed.';

                        $messageType =
                            'error';

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | IF FILE ALREADY EXISTS, BACK IT UP
                        |--------------------------------------------------------------------------
                        */

                        if (
                            file_exists(
                                $newAbsolutePath
                            )
                        ) {

                            $backup =
                                createBackup(
                                    $newAbsolutePath,
                                    $projectRoot,
                                    $backupDirectory
                                );

                            if (
                                $backup === null
                            ) {

                                $message =
                                    'Could not create backup of the existing file.';

                                $messageType =
                                    'error';

                            }

                        }

                        if (
                            $messageType !== 'error'
                        ) {

                            $result =
                                file_put_contents(
                                    $newAbsolutePath,
                                    $newCode
                                );

                            if (
                                $result === false
                            ) {

                                $message =
                                    'Could not create the file.';

                                $messageType =
                                    'error';

                            } else {

                                $selectedFile =
                                    relativePath(
                                        $newAbsolutePath,
                                        $projectRoot
                                    );

                                $selectedAbsolutePath =
                                    realpath(
                                        $newAbsolutePath
                                    );

                                $code =
                                    $newCode;

                                $message =
                                    'New file created successfully.';

                                $messageType =
                                    'success';

                            }

                        }

                    }

                }

            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| LOAD SELECTED FILE
|--------------------------------------------------------------------------
*/

if (
    $selectedFile !== ''
    &&
    $selectedAbsolutePath === ''
) {

    $candidate =
        $projectRoot
        . DIRECTORY_SEPARATOR
        . str_replace(
            [
                '/',
                '\\'
            ],
            DIRECTORY_SEPARATOR,
            $selectedFile
        );

    $realCandidate =
        realpath(
            $candidate
        );

    if (
        $realCandidate !== false
        &&
        isSafePath(
            $realCandidate,
            $projectRoot
        )
        &&
        isAllowedFile(
            $realCandidate,
            $allowedExtensions
        )
    ) {

        $selectedAbsolutePath =
            $realCandidate;

        $selectedFile =
            relativePath(
                $realCandidate,
                $projectRoot
            );

        $loadedCode =
            file_get_contents(
                $realCandidate
            );

        if (
            $loadedCode !== false
        ) {

            $code =
                $loadedCode;

        }

    } else {

        if (
            $message === ''
        ) {

            $message =
                'The selected file could not be opened.';

            $messageType =
                'error';

        }

    }

}


/*
|--------------------------------------------------------------------------
| FIND PROJECT FILES
|--------------------------------------------------------------------------
*/

$projectFiles = [];

$iterator =
    new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $projectRoot,
            FilesystemIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

foreach (
    $iterator as $fileInfo
) {

    if (
        !$fileInfo->isFile()
    ) {
        continue;
    }

    $absolute =
        $fileInfo->getPathname();

    /*
    |--------------------------------------------------------------------------
    | SKIP MODIFIER BACKUPS
    |--------------------------------------------------------------------------
    */

    if (
        str_contains(
            normalizePath($absolute),
            '/_modifier_backups/'
        )
    ) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | SKIP GIT
    |--------------------------------------------------------------------------
    */

    if (
        str_contains(
            normalizePath($absolute),
            '/.git/'
        )
    ) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | ALLOWED FILE TYPES
    |--------------------------------------------------------------------------
    */

    if (
        !isAllowedFile(
            $absolute,
            $allowedExtensions
        )
    ) {
        continue;
    }

    $projectFiles[] =
        relativePath(
            $absolute,
            $projectRoot
        );
}

sort(
    $projectFiles,
    SORT_NATURAL | SORT_FLAG_CASE
);


/*
|--------------------------------------------------------------------------
| BACKUP FILES
|--------------------------------------------------------------------------
*/

$backupFiles = [];

if (
    is_dir(
        $backupDirectory
    )
) {

    $backupIterator =
        new DirectoryIterator(
            $backupDirectory
        );

    foreach (
        $backupIterator as $backup
    ) {

        if (
            $backup->isDot()
            ||
            !$backup->isFile()
        ) {
            continue;
        }

        $backupFiles[] =
            $backup->getFilename();
    }

    rsort(
        $backupFiles,
        SORT_NATURAL
    );
}


/*
|--------------------------------------------------------------------------
| FILE COUNTS
|--------------------------------------------------------------------------
*/

$fileCount =
    count(
        $projectFiles
    );

$backupCount =
    count(
        $backupFiles
    );

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
        LoanManagement SaaS - Code Modifier
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Arial,
                sans-serif;

            background: #f3f4f6;
            color: #111827;
        }

        button,
        input,
        textarea,
        select {
            font: inherit;
        }


        /* ==========================================================
           LAYOUT
        ========================================================== */

        .modifier-app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .modifier-header {
            height: 64px;
            background: #111827;
            color: #ffffff;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding:
                0 22px;

            box-shadow:
                0 2px 8px
                rgba(
                    0,
                    0,
                    0,
                    0.12
                );

            position: sticky;
            top: 0;
            z-index: 100;
        }

        .modifier-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modifier-brand-icon {
            width: 38px;
            height: 38px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            background: #2563eb;

            font-size: 18px;
            font-weight: 700;
        }

        .modifier-brand-title {
            font-size: 16px;
            font-weight: 700;
        }

        .modifier-brand-subtitle {
            margin-top: 2px;
            color: #9ca3af;
            font-size: 11px;
        }

        .modifier-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modifier-project-path {
            color: #d1d5db;
            font-size: 12px;
        }


        /* ==========================================================
           MAIN
        ========================================================== */

        .modifier-main {
            flex: 1;

            display: grid;

            grid-template-columns:
                310px
                minmax(0, 1fr);

            min-height:
                calc(100vh - 64px);
        }


        /* ==========================================================
           SIDEBAR
        ========================================================== */

        .modifier-sidebar {
            background: #ffffff;

            border-right:
                1px solid #e5e7eb;

            display: flex;
            flex-direction: column;

            min-width: 0;
        }

        .sidebar-top {
            padding: 16px;

            border-bottom:
                1px solid #e5e7eb;
        }

        .sidebar-title {
            font-size: 13px;
            font-weight: 700;

            margin-bottom: 10px;
        }

        .sidebar-search {
            width: 100%;
            height: 38px;

            padding:
                0 11px;

            border:
                1px solid #d1d5db;

            border-radius: 7px;

            outline: none;
        }

        .sidebar-search:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(
                    37,
                    99,
                    235,
                    0.10
                );
        }

        .file-count {
            margin-top: 8px;

            color: #6b7280;

            font-size: 11px;
        }

        .file-list {
            flex: 1;

            overflow-y: auto;

            padding: 8px;
        }

        .file-item {
            display: block;

            width: 100%;

            padding:
                9px 10px;

            margin-bottom: 2px;

            border: none;

            border-radius: 6px;

            background: transparent;

            color: #374151;

            text-align: left;

            cursor: pointer;

            font-size: 12px;

            word-break: break-all;
        }

        .file-item:hover {
            background: #f3f4f6;
        }

        .file-item.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }

        .file-extension {
            color: #9ca3af;
            margin-right: 4px;
        }


        /* ==========================================================
           EDITOR AREA
        ========================================================== */

        .modifier-editor-area {
            min-width: 0;

            display: flex;
            flex-direction: column;

            background: #f9fafb;
        }

        .editor-toolbar {
            min-height: 58px;

            background: #ffffff;

            border-bottom:
                1px solid #e5e7eb;

            padding:
                9px 12px;

            display: flex;

            align-items: center;

            gap: 7px;

            flex-wrap: wrap;
        }

        .toolbar-button {
            height: 36px;

            padding:
                0 12px;

            border:
                1px solid #d1d5db;

            border-radius: 7px;

            background: #ffffff;

            color: #374151;

            cursor: pointer;

            font-size: 12px;

            font-weight: 600;
        }

        .toolbar-button:hover {
            background: #f3f4f6;
        }

        .toolbar-button.primary {
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        .toolbar-button.primary:hover {
            background: #1d4ed8;
        }

        .toolbar-button.danger {
            color: #b91c1c;
        }

        .toolbar-divider {
            width: 1px;
            height: 28px;
            background: #e5e7eb;
            margin: 0 3px;
        }

        .current-file {
            flex: 1;

            min-width: 180px;

            color: #374151;

            font-size: 12px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }

        .unsaved-indicator {
            display: none;

            color: #d97706;

            font-size: 11px;

            font-weight: 700;
        }

        .unsaved-indicator.active {
            display: inline;
        }


        /* ==========================================================
           FIND / REPLACE
        ========================================================== */

        .find-panel {
            display: none;

            background: #ffffff;

            border-bottom:
                1px solid #e5e7eb;

            padding:
                10px 12px;

            gap: 7px;

            flex-wrap: wrap;
        }

        .find-panel.active {
            display: flex;
        }

        .find-input {
            height: 34px;

            min-width: 180px;

            flex: 1;

            padding:
                0 9px;

            border:
                1px solid #d1d5db;

            border-radius: 6px;

            outline: none;

            font-size: 12px;
        }

        .find-input:focus {
            border-color: #2563eb;
        }


        /* ==========================================================
           EDITOR
        ========================================================== */

        .editor-wrapper {
            flex: 1;

            position: relative;

            display: flex;

            min-height: 0;

            background: #1e1e1e;
        }

        .line-numbers {
            width: 55px;

            flex-shrink: 0;

            overflow: hidden;

            padding-top: 16px;

            background: #181818;

            color: #6b7280;

            text-align: right;

            user-select: none;

            font-family:
                Consolas,
                "Courier New",
                monospace;

            font-size: 13px;

            line-height: 20px;

            padding-right: 12px;
        }

        #codeEditor {
            flex: 1;

            width: 100%;

            height: 100%;

            min-height:
                calc(100vh - 150px);

            resize: none;

            border: none;

            outline: none;

            padding:
                16px;

            background: #1e1e1e;

            color: #d4d4d4;

            font-family:
                Consolas,
                "Courier New",
                monospace;

            font-size: 13px;

            line-height: 20px;

            tab-size: 4;

            white-space: pre;

            overflow: auto;
        }


        /* ==========================================================
           STATUS BAR
        ========================================================== */

        .editor-status {
            height: 30px;

            background: #111827;

            color: #d1d5db;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 12px;

            font-size: 11px;
        }

        .status-left,
        .status-right {
            display: flex;
            gap: 14px;
            align-items: center;
        }


        /* ==========================================================
           MESSAGE
        ========================================================== */

        .message {
            position: fixed;

            right: 20px;
            top: 78px;

            max-width: 420px;

            padding:
                12px 15px;

            border-radius: 8px;

            box-shadow:
                0 8px 25px
                rgba(
                    0,
                    0,
                    0,
                    0.15
                );

            z-index: 1000;

            font-size: 12px;

            font-weight: 600;
        }

        .message.success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .message.error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }


        /* ==========================================================
           MODALS
        ========================================================== */

        .modifier-modal {
            position: fixed;

            inset: 0;

            display: none;

            align-items: center;
            justify-content: center;

            padding: 20px;

            background:
                rgba(
                    0,
                    0,
                    0,
                    0.45
                );

            z-index: 900;
        }

        .modifier-modal.active {
            display: flex;
        }

        .modal-box {
            width: 100%;
            max-width: 520px;

            background: #ffffff;

            border-radius: 10px;

            box-shadow:
                0 20px 60px
                rgba(
                    0,
                    0,
                    0,
                    0.25
                );

            overflow: hidden;
        }

        .modal-header {
            padding:
                16px 18px;

            border-bottom:
                1px solid #e5e7eb;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        .modal-header h3 {
            margin: 0;

            font-size: 15px;
        }

        .modal-close {
            width: 30px;
            height: 30px;

            border: none;

            border-radius: 6px;

            background: transparent;

            color: #6b7280;

            font-size: 20px;

            cursor: pointer;
        }

        .modal-close:hover {
            background: #f3f4f6;
        }

        .modal-body {
            padding: 18px;
        }

        .modal-label {
            display: block;

            margin-bottom: 7px;

            font-size: 12px;

            font-weight: 600;

            color: #374151;
        }

        .modal-input {
            width: 100%;

            height: 40px;

            padding:
                0 11px;

            border:
                1px solid #d1d5db;

            border-radius: 7px;

            outline: none;
        }

        .modal-input:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(
                    37,
                    99,
                    235,
                    0.10
                );
        }

        .modal-footer {
            padding:
                12px 18px;

            border-top:
                1px solid #e5e7eb;

            display: flex;

            justify-content: flex-end;

            gap: 8px;
        }

        .backup-list {
            max-height: 300px;

            overflow-y: auto;
        }

        .backup-item {
            padding:
                9px 10px;

            border-bottom:
                1px solid #f3f4f6;

            font-size: 11px;

            color: #4b5563;
        }


        /* ==========================================================
           RESPONSIVE
        ========================================================== */

        @media (
            max-width: 800px
        ) {

            .modifier-main {
                grid-template-columns:
                    220px
                    minmax(0, 1fr);
            }

            .modifier-project-path {
                display: none;
            }

        }

        @media (
            max-width: 600px
        ) {

            .modifier-main {
                grid-template-columns: 1fr;
            }

            .modifier-sidebar {
                max-height: 230px;

                border-right: none;

                border-bottom:
                    1px solid #e5e7eb;
            }

            .modifier-header {
                padding:
                    0 12px;
            }

            .modifier-brand-subtitle {
                display: none;
            }

            .toolbar-button {
                padding:
                    0 9px;
            }

        }

    </style>

</head>


<body>

<div class="modifier-app">


    <!-- ==========================================================
         HEADER
    =========================================================== -->

    <header class="modifier-header">

        <div class="modifier-brand">

            <div class="modifier-brand-icon">
                &lt;/&gt;
            </div>

            <div>

                <div class="modifier-brand-title">
                    LoanManagement SaaS
                </div>

                <div class="modifier-brand-subtitle">
                    Web Code Modifier
                </div>

            </div>

        </div>


        <div class="modifier-header-right">

            <span class="modifier-project-path">
                <?= h($projectRoot) ?>
            </span>

        </div>

    </header>


    <!-- ==========================================================
         MAIN
    =========================================================== -->

    <main class="modifier-main">


        <!-- ======================================================
             SIDEBAR
        ======================================================= -->

        <aside class="modifier-sidebar">

            <div class="sidebar-top">

                <div class="sidebar-title">
                    Project Files
                </div>

                <input
                    type="search"
                    id="fileSearch"
                    class="sidebar-search"
                    placeholder="Search files..."
                    autocomplete="off"
                >

                <div class="file-count">

                    <?= $fileCount ?>

                    file<?= $fileCount === 1 ? '' : 's' ?>

                </div>

            </div>


            <div
                class="file-list"
                id="fileList"
            >

                <?php foreach (
                    $projectFiles as $file
                ): ?>

                    <?php

                    $extension =
                        strtolower(
                            pathinfo(
                                $file,
                                PATHINFO_EXTENSION
                            )
                        );

                    $isActive =
                        $file ===
                        $selectedFile;

                    ?>

                    <button
                        type="button"
                        class="file-item <?= $isActive ? 'active' : '' ?>"
                        data-file="<?= h($file) ?>"
                        onclick="openFile(this.dataset.file)"
                    >

                        <span class="file-extension">
                            <?= h($extension) ?>
                        </span>

                        <?= h($file) ?>

                    </button>

                <?php endforeach; ?>

            </div>

        </aside>


        <!-- ======================================================
             EDITOR
        ======================================================= -->

        <section class="modifier-editor-area">


            <!-- ==================================================
                 TOOLBAR
            =================================================== -->

            <div class="editor-toolbar">

                <button
                    type="button"
                    class="toolbar-button primary"
                    onclick="saveFile()"
                >
                    💾 Save
                </button>


                <button
                    type="button"
                    class="toolbar-button"
                    onclick="openSaveAsModal()"
                >
                    Save As
                </button>


                <div class="toolbar-divider"></div>


                <button
                    type="button"
                    class="toolbar-button"
                    onclick="undoCode()"
                >
                    ↶ Undo
                </button>


                <button
                    type="button"
                    class="toolbar-button"
                    onclick="redoCode()"
                >
                    ↷ Redo
                </button>


                <div class="toolbar-divider"></div>


                <button
                    type="button"
                    class="toolbar-button"
                    onclick="toggleFindPanel()"
                >
                    🔍 Find
                </button>


                <button
                    type="button"
                    class="toolbar-button"
                    onclick="copyCode()"
                >
                    📋 Copy
                </button>


                <button
                    type="button"
                    class="toolbar-button"
                    onclick="selectAllCode()"
                >
                    Select All
                </button>


                <div class="current-file">

                    <?php if (
                        $selectedFile !== ''
                    ): ?>

                        📄
                        <?= h($selectedFile) ?>

                    <?php else: ?>

                        No file selected

                    <?php endif; ?>

                </div>


                <span
                    class="unsaved-indicator"
                    id="unsavedIndicator"
                >
                    ● Unsaved changes
                </span>

            </div>


            <!-- ==================================================
                 FIND PANEL
            =================================================== -->

            <div
                class="find-panel"
                id="findPanel"
            >

                <input
                    type="text"
                    id="findInput"
                    class="find-input"
                    placeholder="Find..."
                >

                <input
                    type="text"
                    id="replaceInput"
                    class="find-input"
                    placeholder="Replace with..."
                >

                <button
                    type="button"
                    class="toolbar-button"
                    onclick="findNext()"
                >
                    Find Next
                </button>

                <button
                    type="button"
                    class="toolbar-button"
                    onclick="replaceCurrent()"
                >
                    Replace
                </button>

                <button
                    type="button"
                    class="toolbar-button"
                    onclick="replaceAllText()"
                >
                    Replace All
                </button>

                <button
                    type="button"
                    class="toolbar-button"
                    onclick="toggleFindPanel()"
                >
                    Close
                </button>

            </div>


            <!-- ==================================================
                 CODE EDITOR
            =================================================== -->

            <div class="editor-wrapper">

                <div
                    class="line-numbers"
                    id="lineNumbers"
                >
                    1
                </div>


                <textarea
                    id="codeEditor"
                    spellcheck="false"
                    placeholder="Select a project file from the left..."
                ><?= h($code) ?></textarea>

            </div>


            <!-- ==================================================
                 STATUS BAR
            =================================================== -->

            <div class="editor-status">

                <div class="status-left">

                    <span id="cursorPosition">
                        Line 1, Column 1
                    </span>

                    <span id="lineCount">
                        Lines: 1
                    </span>

                </div>


                <div class="status-right">

                    <span>
                        UTF-8
                    </span>

                    <span>
                        PHP/Code
                    </span>

                </div>

            </div>

        </section>

    </main>

</div>


<!-- ==============================================================
     MESSAGE
================================================================ -->

<?php if (
    $message !== ''
): ?>

    <div
        class="message <?= h($messageType) ?>"
        id="messageBox"
    >
        <?= h($message) ?>
    </div>

<?php endif; ?>


<!-- ==============================================================
     SAVE AS MODAL
================================================================ -->

<div
    class="modifier-modal"
    id="saveAsModal"
    onclick="closeModalOutside(event, 'saveAsModal')"
>

    <div
        class="modal-box"
        onclick="event.stopPropagation()"
    >

        <div class="modal-header">

            <h3>
                Save As
            </h3>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('saveAsModal')"
            >
                ×
            </button>

        </div>


        <div class="modal-body">

            <label
                class="modal-label"
                for="saveAsFile"
            >
                File Path
            </label>

            <input
                type="text"
                id="saveAsFile"
                class="modal-input"
                placeholder="example.php"
                value="<?= h($selectedFile) ?>"
            >

            <div
                style="
                    margin-top:8px;
                    color:#6b7280;
                    font-size:11px;
                "
            >
                You can use folders, for example:
                <strong>
                    views/loans/index.php
                </strong>
            </div>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="toolbar-button"
                onclick="closeModal('saveAsModal')"
            >
                Cancel
            </button>

            <button
                type="button"
                class="toolbar-button primary"
                onclick="performSaveAs()"
            >
                Create / Save
            </button>

        </div>

    </div>

</div>


<!-- ==============================================================
     BACKUPS MODAL
================================================================ -->

<div
    class="modifier-modal"
    id="backupModal"
    onclick="closeModalOutside(event, 'backupModal')"
>

    <div
        class="modal-box"
        onclick="event.stopPropagation()"
    >

        <div class="modal-header">

            <h3>
                Recent Backups
            </h3>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('backupModal')"
            >
                ×
            </button>

        </div>


        <div class="modal-body">

            <?php if (
                empty($backupFiles)
            ): ?>

                <div
                    style="
                        color:#6b7280;
                        font-size:12px;
                    "
                >
                    No backups yet.
                </div>

            <?php else: ?>

                <div class="backup-list">

                    <?php foreach (
                        array_slice(
                            $backupFiles,
                            0,
                            30
                        ) as $backup
                    ): ?>

                        <div class="backup-item">

                            <?= h($backup) ?>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="toolbar-button"
                onclick="closeModal('backupModal')"
            >
                Close
            </button>

        </div>

    </div>

</div>


<!-- ==============================================================
     JAVASCRIPT
================================================================ -->

<script>

'use strict';


/*
|--------------------------------------------------------------------------
| GLOBAL VARIABLES
|--------------------------------------------------------------------------
*/

let originalCode =
    document.getElementById(
        'codeEditor'
    ).value;

let historyStack = [
    originalCode
];

let historyIndex = 0;

let isUndoRedo =
    false;


/*
|--------------------------------------------------------------------------
| ELEMENTS
|--------------------------------------------------------------------------
*/

const editor =
    document.getElementById(
        'codeEditor'
    );

const lineNumbers =
    document.getElementById(
        'lineNumbers'
    );

const cursorPosition =
    document.getElementById(
        'cursorPosition'
    );

const lineCount =
    document.getElementById(
        'lineCount'
    );

const unsavedIndicator =
    document.getElementById(
        'unsavedIndicator'
    );


/*
|--------------------------------------------------------------------------
| OPEN FILE
|--------------------------------------------------------------------------
*/

function openFile(file)
{

    const currentCode =
        editor.value;

    if (
        currentCode !==
        originalCode
    ) {

        const confirmed =
            confirm(
                'You have unsaved changes. Open another file anyway?'
            );

        if (!confirmed) {
            return;
        }

    }

    window.location.href =
        'modifier.php?file='
        +
        encodeURIComponent(
            file
        );

}


/*
|--------------------------------------------------------------------------
| SAVE FILE
|--------------------------------------------------------------------------
*/

function saveFile()
{

    const file =
        <?= json_encode(
            $selectedFile,
            JSON_UNESCAPED_SLASHES
        ) ?>;

    if (
        !file
    ) {

        alert(
            'Please select a file first.'
        );

        return;
    }

    const confirmed =
        confirm(
            'Save changes to "' +
            file +
            '"?\n\nA backup will be created automatically.'
        );

    if (!confirmed) {
        return;
    }

    submitEditorForm(
        'save',
        file
    );

}


/*
|--------------------------------------------------------------------------
| SUBMIT SAVE FORM
|--------------------------------------------------------------------------
*/

function submitEditorForm(
    action,
    file,
    newFile = ''
)
{

    const form =
        document.createElement(
            'form'
        );

    form.method =
        'POST';

    form.action =
        'modifier.php';

    const actionInput =
        document.createElement(
            'input'
        );

    actionInput.type =
        'hidden';

    actionInput.name =
        'action';

    actionInput.value =
        action;

    form.appendChild(
        actionInput
    );


    const fileInput =
        document.createElement(
            'input'
        );

    fileInput.type =
        'hidden';

    fileInput.name =
        'file';

    fileInput.value =
        file;

    form.appendChild(
        fileInput
    );


    const codeInput =
        document.createElement(
            'textarea'
        );

    codeInput.name =
        'code';

    codeInput.value =
        editor.value;

    form.appendChild(
        codeInput
    );


    if (
        newFile !== ''
    ) {

        const newFileInput =
            document.createElement(
                'input'
            );

        newFileInput.type =
            'hidden';

        newFileInput.name =
            'new_file';

        newFileInput.value =
            newFile;

        form.appendChild(
            newFileInput
        );

    }


    document.body.appendChild(
        form
    );

    form.submit();

}


/*
|--------------------------------------------------------------------------
| SAVE AS MODAL
|--------------------------------------------------------------------------
*/

function openSaveAsModal()
{

    document
        .getElementById(
            'saveAsModal'
        )
        .classList.add(
            'active'
        );

    setTimeout(
        function()
        {

            const input =
                document.getElementById(
                    'saveAsFile'
                );

            input.focus();

            input.select();

        },
        50
    );

}


function performSaveAs()
{

    const newFile =
        document
            .getElementById(
                'saveAsFile'
            )
            .value
            .trim();

    if (
        !newFile
    ) {

        alert(
            'Please enter a file path.'
        );

        return;
    }

    const confirmed =
        confirm(
            'Create/save this file?\n\n' +
            newFile
        );

    if (!confirmed) {
        return;
    }

    submitEditorForm(
        'save_as',
        '',
        newFile
    );

}


/*
|--------------------------------------------------------------------------
| MODAL FUNCTIONS
|--------------------------------------------------------------------------
*/

function closeModal(
    id
)
{

    const modal =
        document.getElementById(
            id
        );

    if (modal) {

        modal.classList.remove(
            'active'
        );

    }

}


function closeModalOutside(
    event,
    id
)
{

    if (
        event.target.id ===
        id
    ) {

        closeModal(
            id
        );

    }

}


/*
|--------------------------------------------------------------------------
| FIND PANEL
|--------------------------------------------------------------------------
*/

function toggleFindPanel()
{

    const panel =
        document.getElementById(
            'findPanel'
        );

    panel.classList.toggle(
        'active'
    );

    if (
        panel.classList.contains(
            'active'
        )
    ) {

        document
            .getElementById(
                'findInput'
            )
            .focus();

    }

}


/*
|--------------------------------------------------------------------------
| FIND NEXT
|--------------------------------------------------------------------------
*/

function findNext()
{

    const search =
        document
            .getElementById(
                'findInput'
            )
            .value;

    if (
        !search
    ) {
        return;
    }

    const text =
        editor.value;

    const start =
        editor.selectionEnd;

    let index =
        text.indexOf(
            search,
            start
        );

    if (
        index === -1
    ) {

        index =
            text.indexOf(
                search,
                0
            );

    }

    if (
        index !== -1
    ) {

        editor.focus();

        editor.setSelectionRange(
            index,
            index + search.length
        );

    } else {

        alert(
            'Text not found.'
        );

    }

}


/*
|--------------------------------------------------------------------------
| REPLACE CURRENT
|--------------------------------------------------------------------------
*/

function replaceCurrent()
{

    const search =
        document
            .getElementById(
                'findInput'
            )
            .value;

    const replacement =
        document
            .getElementById(
                'replaceInput'
            )
            .value;

    if (
        !search
    ) {
        return;
    }

    const start =
        editor.selectionStart;

    const end =
        editor.selectionEnd;

    const selected =
        editor.value.substring(
            start,
            end
        );

    if (
        selected ===
        search
    ) {

        editor.setRangeText(
            replacement,
            start,
            end,
            'end'
        );

        updateEditor();

    } else {

        findNext();

    }

}


/*
|--------------------------------------------------------------------------
| REPLACE ALL
|--------------------------------------------------------------------------
*/

function replaceAllText()
{

    const search =
        document
            .getElementById(
                'findInput'
            )
            .value;

    const replacement =
        document
            .getElementById(
                'replaceInput'
            )
            .value;

    if (
        !search
    ) {

        alert(
            'Enter text to find.'
        );

        return;
    }

    const escaped =
        search.replace(
            /[.*+?^${}()|[\]\\]/g,
            '\\$&'
        );

    const regex =
        new RegExp(
            escaped,
            'g'
        );

    const matches =
        editor.value.match(
            regex
        );

    const count =
        matches
            ? matches.length
            : 0;

    if (
        count === 0
    ) {

        alert(
            'Text not found.'
        );

        return;

    }

    const confirmed =
        confirm(
            'Replace ' +
            count +
            ' occurrence(s)?'
        );

    if (!confirmed) {
        return;
    }

    editor.value =
        editor.value.replace(
            regex,
            replacement
        );

    updateEditor();

}


/*
|--------------------------------------------------------------------------
| COPY
|--------------------------------------------------------------------------
*/

async function copyCode()
{

    try {

        await navigator.clipboard.writeText(
            editor.value
        );

        showTemporaryMessage(
            'Code copied to clipboard.'
        );

    } catch (error) {

        editor.focus();

        editor.select();

        document.execCommand(
            'copy'
        );

        showTemporaryMessage(
            'Code copied to clipboard.'
        );

    }

}


/*
|--------------------------------------------------------------------------
| SELECT ALL
|--------------------------------------------------------------------------
*/

function selectAllCode()
{

    editor.focus();

    editor.select();

}


/*
|--------------------------------------------------------------------------
| UNDO
|--------------------------------------------------------------------------
*/

function undoCode()
{

    if (
        historyIndex <= 0
    ) {
        return;
    }

    historyIndex--;

    isUndoRedo =
        true;

    editor.value =
        historyStack[
            historyIndex
        ];

    isUndoRedo =
        false;

    updateEditor();

}


/*
|--------------------------------------------------------------------------
| REDO
|--------------------------------------------------------------------------
*/

function redoCode()
{

    if (
        historyIndex >=
        historyStack.length - 1
    ) {
        return;
    }

    historyIndex++;

    isUndoRedo =
        true;

    editor.value =
        historyStack[
            historyIndex
        ];

    isUndoRedo =
        false;

    updateEditor();

}


/*
|--------------------------------------------------------------------------
| UPDATE HISTORY
|--------------------------------------------------------------------------
*/

function updateHistory()
{

    if (
        isUndoRedo
    ) {
        return;
    }

    const current =
        editor.value;

    if (
        historyStack[
            historyIndex
        ] === current
    ) {
        return;
    }

    historyStack =
        historyStack.slice(
            0,
            historyIndex + 1
        );

    historyStack.push(
        current
    );

    historyIndex++;

    /*
    |--------------------------------------------------------------------------
    | LIMIT HISTORY
    |--------------------------------------------------------------------------
    */

    if (
        historyStack.length > 100
    ) {

        historyStack.shift();

        historyIndex--;

    }

}


/*
|--------------------------------------------------------------------------
| UPDATE EDITOR
|--------------------------------------------------------------------------
*/

function updateEditor()
{

    updateLineNumbers();

    updateCursorPosition();

    updateUnsavedState();

}


/*
|--------------------------------------------------------------------------
| LINE NUMBERS
|--------------------------------------------------------------------------
*/

function updateLineNumbers()
{

    const lines =
        editor.value.split(
            '\n'
        ).length;

    let output = '';

    for (
        let i = 1;
        i <= lines;
        i++
    ) {

        output +=
            i
            +
            '<br>';

    }

    lineNumbers.innerHTML =
        output;

    lineCount.textContent =
        'Lines: ' +
        lines;

}


/*
|--------------------------------------------------------------------------
| CURSOR POSITION
|--------------------------------------------------------------------------
*/

function updateCursorPosition()
{

    const position =
        editor.selectionStart;

    const before =
        editor.value.substring(
            0,
            position
        );

    const line =
        before.split(
            '\n'
        ).length;

    const lastNewLine =
        before.lastIndexOf(
            '\n'
        );

    const column =
        position -
        lastNewLine;

    cursorPosition.textContent =
        'Line ' +
        line +
        ', Column ' +
        column;

}


/*
|--------------------------------------------------------------------------
| UNSAVED STATE
|--------------------------------------------------------------------------
*/

function updateUnsavedState()
{

    const changed =
        editor.value !==
        originalCode;

    if (
        changed
    ) {

        unsavedIndicator.classList.add(
            'active'
        );

    } else {

        unsavedIndicator.classList.remove(
            'active'
        );

    }

}


/*
|--------------------------------------------------------------------------
| SCROLL LINE NUMBERS
|--------------------------------------------------------------------------
*/

editor.addEventListener(
    'scroll',
    function()
    {

        lineNumbers.scrollTop =
            editor.scrollTop;

    }
);


/*
|--------------------------------------------------------------------------
| EDITOR INPUT
|--------------------------------------------------------------------------
*/

editor.addEventListener(
    'input',
    function()
    {

        updateHistory();

        updateEditor();

    }
);


/*
|--------------------------------------------------------------------------
| CURSOR EVENTS
|--------------------------------------------------------------------------
*/

editor.addEventListener(
    'keyup',
    updateCursorPosition
);

editor.addEventListener(
    'click',
    updateCursorPosition
);

editor.addEventListener(
    'select',
    updateCursorPosition
);


/*
|--------------------------------------------------------------------------
| TAB SUPPORT
|--------------------------------------------------------------------------
*/

editor.addEventListener(
    'keydown',
    function(event)
    {

        if (
            event.key ===
            'Tab'
        ) {

            event.preventDefault();

            const start =
                editor.selectionStart;

            const end =
                editor.selectionEnd;

            editor.setRangeText(
                '    ',
                start,
                end,
                'end'
            );

            updateHistory();

            updateEditor();

        }


        /*
        |--------------------------------------------------------------------------
        | CTRL + S
        |--------------------------------------------------------------------------
        */

        if (
            event.ctrlKey &&
            event.key.toLowerCase() === 's'
        ) {

            event.preventDefault();

            saveFile();

        }


        /*
        |--------------------------------------------------------------------------
        | CTRL + F
        |--------------------------------------------------------------------------
        */

        if (
            event.ctrlKey &&
            event.key.toLowerCase() === 'f'
        ) {

            event.preventDefault();

            toggleFindPanel();

        }


        /*
        |--------------------------------------------------------------------------
        | CTRL + H
        |--------------------------------------------------------------------------
        */

        if (
            event.ctrlKey &&
            event.key.toLowerCase() === 'h'
        ) {

            event.preventDefault();

            toggleFindPanel();

        }

    }
);


/*
|--------------------------------------------------------------------------
| FILE SEARCH
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        'fileSearch'
    )
    .addEventListener(
        'input',
        function()
        {

            const search =
                this.value
                    .trim()
                    .toLowerCase();

            document
                .querySelectorAll(
                    '.file-item'
                )
                .forEach(
                    function(item)
                    {

                        const file =
                            item.dataset.file
                                .toLowerCase();

                        if (
                            file.includes(
                                search
                            )
                        ) {

                            item.style.display =
                                'block';

                        } else {

                            item.style.display =
                                'none';

                        }

                    }
                );

        }
    );


/*
|--------------------------------------------------------------------------
| TEMPORARY MESSAGE
|--------------------------------------------------------------------------
*/

function showTemporaryMessage(
    text
)
{

    const existing =
        document.getElementById(
            'temporaryMessage'
        );

    if (
        existing
    ) {

        existing.remove();

    }

    const message =
        document.createElement(
            'div'
        );

    message.id =
        'temporaryMessage';

    message.className =
        'message success';

    message.textContent =
        text;

    document.body.appendChild(
        message
    );

    setTimeout(
        function()
        {

            message.remove();

        },
        2000
    );

}


/*
|--------------------------------------------------------------------------
| AUTO HIDE SERVER MESSAGE
|--------------------------------------------------------------------------
*/

const serverMessage =
    document.getElementById(
        'messageBox'
    );

if (
    serverMessage
) {

    setTimeout(
        function()
        {

            serverMessage.style.opacity =
                '0';

            serverMessage.style.transition =
                'opacity 0.3s ease';

            setTimeout(
                function()
                {

                    serverMessage.remove();

                },
                350
            );

        },
        5000
    );

}


/*
|--------------------------------------------------------------------------
| BEFORE LEAVING PAGE
|--------------------------------------------------------------------------
*/

window.addEventListener(
    'beforeunload',
    function(event)
    {

        if (
            editor.value !==
            originalCode
        ) {

            event.preventDefault();

            event.returnValue =
                '';

        }

    }
);


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

updateEditor();

</script>

</body>

</html>