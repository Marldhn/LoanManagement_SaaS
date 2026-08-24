<?php

class ReportController
{
    /*
    |--------------------------------------------------------------------------
    | REPORT INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        AuthMiddleware::requireLogin();

        /*
        |--------------------------------------------------------------------------
        | AUTH DATA
        |--------------------------------------------------------------------------
        */

        $user =
            Auth::user();

        $business =
            Auth::business();

        $businessId =
            Auth::businessId();

        $tenantRole =
            Auth::tenantRole();


        /*
        |--------------------------------------------------------------------------
        | DATABASE
        |--------------------------------------------------------------------------
        */

        $db =
            Database::getInstance();


        /*
        |--------------------------------------------------------------------------
        | LOAN SUMMARY
        |--------------------------------------------------------------------------
        */

        $stmt =
            $db->prepare(
                "
                SELECT

                    COUNT(*) AS total_loans,

                    SUM(
                        CASE
                            WHEN status = 'pending'
                            THEN 1
                            ELSE 0
                        END
                    ) AS pending_loans,

                    SUM(
                        CASE
                            WHEN status = 'approved'
                            THEN 1
                            ELSE 0
                        END
                    ) AS approved_loans,

                    SUM(
                        CASE
                            WHEN status = 'active'
                            THEN 1
                            ELSE 0
                        END
                    ) AS active_loans,

                    SUM(
                        CASE
                            WHEN status = 'overdue'
                            THEN 1
                            ELSE 0
                        END
                    ) AS overdue_loans,

                    SUM(
                        CASE
                            WHEN status = 'completed'
                            THEN 1
                            ELSE 0
                        END
                    ) AS completed_loans,

                    SUM(
                        CASE
                            WHEN status = 'rejected'
                            THEN 1
                            ELSE 0
                        END
                    ) AS rejected_loans,

                    SUM(
                        CASE
                            WHEN status = 'cancelled'
                            THEN 1
                            ELSE 0
                        END
                    ) AS cancelled_loans

                FROM loans

                WHERE business_id = ?
                "
            );


        $stmt->execute([
            $businessId
        ]);


        $loanSummary =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | FINANCIAL SUMMARY
        |--------------------------------------------------------------------------
        */

        $stmt =
            $db->prepare(
                "
                SELECT

                    COALESCE(
                        SUM(principal_amount),
                        0
                    ) AS total_principal,

                    COALESCE(
                        SUM(total_interest),
                        0
                    ) AS total_interest,

                    COALESCE(
                        SUM(processing_fee),
                        0
                    ) AS total_processing_fee,

                    COALESCE(
                        SUM(total_payable),
                        0
                    ) AS total_payable

                FROM loans

                WHERE business_id = ?

                AND status NOT IN (
                    'rejected',
                    'cancelled'
                )
                "
            );


        $stmt->execute([
            $businessId
        ]);


        $financialSummary =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | PAYMENT SUMMARY
        |--------------------------------------------------------------------------
        */

        $stmt =
            $db->prepare(
                "
                SELECT

                    COUNT(*) AS payment_count,

                    COALESCE(
                        SUM(amount),
                        0
                    ) AS total_collected,

                    COALESCE(
                        SUM(principal_amount),
                        0
                    ) AS principal_collected,

                    COALESCE(
                        SUM(interest_amount),
                        0
                    ) AS interest_collected,

                    COALESCE(
                        SUM(penalty_amount),
                        0
                    ) AS penalty_collected

                FROM loan_payments

                WHERE business_id = ?

                AND status = 'posted'
                "
            );


        $stmt->execute([
            $businessId
        ]);


        $paymentSummary =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING BALANCE
        |--------------------------------------------------------------------------
        */

        $totalPayable =
            (float)(
                $financialSummary['total_payable']
                ?? 0
            );


        $totalCollected =
            (float)(
                $paymentSummary['total_collected']
                ?? 0
            );


        $outstandingBalance =
            max(
                0,
                $totalPayable
                -
                $totalCollected
            );


        /*
        |--------------------------------------------------------------------------
        | BORROWER SUMMARY
        |--------------------------------------------------------------------------
        */

        $stmt =
            $db->prepare(
                "
                SELECT

                    COUNT(*) AS total_borrowers,

                    SUM(
                        CASE
                            WHEN status = 'active'
                            THEN 1
                            ELSE 0
                        END
                    ) AS active_borrowers,

                    SUM(
                        CASE
                            WHEN status = 'inactive'
                            THEN 1
                            ELSE 0
                        END
                    ) AS inactive_borrowers

                FROM loan_borrowers

                WHERE business_id = ?
                "
            );


        $stmt->execute([
            $businessId
        ]);


        $borrowerSummary =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | RECENT PAYMENTS
        |--------------------------------------------------------------------------
        */

        $stmt =
            $db->prepare(
                "
                SELECT

                    lp.id,

                    lp.payment_number,

                    lp.payment_date,

                    lp.amount,

                    lp.principal_amount,

                    lp.interest_amount,

                    lp.penalty_amount,

                    lp.status,

                    l.loan_number,

                    CONCAT(
                        COALESCE(lb.first_name, ''),
                        ' ',
                        COALESCE(lb.middle_name, ''),
                        ' ',
                        COALESCE(lb.last_name, '')
                    ) AS borrower_name

                FROM loan_payments lp

                INNER JOIN loans l
                    ON l.id = lp.loan_id

                LEFT JOIN loan_borrowers lb
                    ON lb.id = l.borrower_id

                WHERE lp.business_id = ?

                ORDER BY
                    lp.payment_date DESC,
                    lp.id DESC

                LIMIT 10
                "
            );


        $stmt->execute([
            $businessId
        ]);


        $recentPayments =
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | RECENT LOANS
        |--------------------------------------------------------------------------
        */

        $stmt =
            $db->prepare(
                "
                SELECT

                    l.id,

                    l.loan_number,

                    l.principal_amount,

                    l.total_payable,

                    l.status,

                    l.created_at,

                    CONCAT(
                        COALESCE(lb.first_name, ''),
                        ' ',
                        COALESCE(lb.middle_name, ''),
                        ' ',
                        COALESCE(lb.last_name, '')
                    ) AS borrower_name

                FROM loans l

                LEFT JOIN loan_borrowers lb
                    ON lb.id = l.borrower_id

                WHERE l.business_id = ?

                ORDER BY
                    l.created_at DESC,
                    l.id DESC

                LIMIT 10
                "
            );


        $stmt->execute([
            $businessId
        ]);


        $recentLoans =
            $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );


        /*
        |--------------------------------------------------------------------------
        | REPORT VIEW
        |--------------------------------------------------------------------------
        */

        require APP_PATH .
            '/views/reports/index.php';
    }
}