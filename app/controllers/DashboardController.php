<?php

class DashboardController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        AuthMiddleware::requireLogin();

        $user = Auth::user();
        $business = Auth::business();
        $businessId = (int) Auth::businessId();
        $tenantRole = Auth::tenantRole();

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if (Auth::isSuperAdmin()) {

            require APP_PATH . '/views/dashboard/super_admin.php';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT VALUES
        |--------------------------------------------------------------------------
        */

        $totalBorrowers = 0;
        $activeLoans = 0;
        $outstandingBalance = 0.00;
        $totalAccounts = 0;
        $totalFunds = 0.00;
        $totalAssets = 0.00;
        $totalLiabilities = 0.00;
        $netBalance = 0.00;

        $accounts = [];
        $recentLoans = [];

        /*
        |--------------------------------------------------------------------------
        | TOTAL BORROWERS
        |--------------------------------------------------------------------------
        */

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM borrowers
            WHERE business_id = ?
        ");

        $stmt->execute([$businessId]);

        $totalBorrowers = (int) $stmt->fetchColumn();


        /*
        |--------------------------------------------------------------------------
        | ACTIVE LOANS
        |--------------------------------------------------------------------------
        */

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM loans
            WHERE business_id = ?
            AND status = 'active'
        ");

        $stmt->execute([$businessId]);

        $activeLoans = (int) $stmt->fetchColumn();


        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING LOANS
        |--------------------------------------------------------------------------
        |
        | Your loans table has total_payable.
        |
        | Since payments are not implemented yet, the current outstanding
        | amount is the total payable of active loans.
        |
        */

        $stmt = $this->db->prepare("
            SELECT COALESCE(
                SUM(total_payable),
                0
            )
            FROM loans
            WHERE business_id = ?
            AND status = 'active'
        ");

        $stmt->execute([$businessId]);

        $outstandingBalance = (float) $stmt->fetchColumn();


        /*
        |--------------------------------------------------------------------------
        | TOTAL ACCOUNTS
        |--------------------------------------------------------------------------
        */

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM accounts
            WHERE business_id = ?
            AND status = 'active'
        ");

        $stmt->execute([$businessId]);

        $totalAccounts = (int) $stmt->fetchColumn();


        /*
        |--------------------------------------------------------------------------
        | TOTAL ASSETS / AVAILABLE FUNDS
        |--------------------------------------------------------------------------
        */

        $stmt = $this->db->prepare("
            SELECT COALESCE(
                SUM(balance),
                0
            )
            FROM accounts
            WHERE business_id = ?
            AND account_type = 'asset'
            AND status = 'active'
        ");

        $stmt->execute([$businessId]);

        $totalAssets = (float) $stmt->fetchColumn();


        /*
        |--------------------------------------------------------------------------
        | TOTAL LIABILITIES
        |--------------------------------------------------------------------------
        */

        $stmt = $this->db->prepare("
            SELECT COALESCE(
                SUM(balance),
                0
            )
            FROM accounts
            WHERE business_id = ?
            AND account_type = 'liability'
            AND status = 'active'
        ");

        $stmt->execute([$businessId]);

        $totalLiabilities = (float) $stmt->fetchColumn();


        /*
        |--------------------------------------------------------------------------
        | AVAILABLE FUNDS
        |--------------------------------------------------------------------------
        */

        $totalFunds = $totalAssets;


        /*
        |--------------------------------------------------------------------------
        | NET BALANCE
        |--------------------------------------------------------------------------
        */

        $netBalance =
            $totalAssets -
            $totalLiabilities;


        /*
        |--------------------------------------------------------------------------
        | ACCOUNT LIST
        |--------------------------------------------------------------------------
        */

        $stmt = $this->db->prepare("
            SELECT
                id,
                account_name,
                account_type,
                balance,
                status
            FROM accounts
            WHERE business_id = ?
            AND status = 'active'
            ORDER BY account_name ASC
        ");

        $stmt->execute([$businessId]);

        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);


        /*
        |--------------------------------------------------------------------------
        | RECENT LOANS
        |--------------------------------------------------------------------------
        */

        $stmt = $this->db->prepare("
            SELECT
                l.id,
                l.loan_number,
                l.principal_amount,
                l.total_payable,
                l.status,
                l.created_at,

                CONCAT(
                    b.first_name,
                    ' ',
                    b.last_name
                ) AS borrower_name,

                a.account_name

            FROM loans l

            INNER JOIN borrowers b
                ON b.id = l.borrower_id

            LEFT JOIN accounts a
                ON a.id = l.account_id

            WHERE l.business_id = ?

            ORDER BY l.id DESC

            LIMIT 10
        ");

        $stmt->execute([$businessId]);

        $recentLoans = $stmt->fetchAll(PDO::FETCH_ASSOC);


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        require APP_PATH . '/views/dashboard/index.php';
    }
}