<?php

require_once APP_PATH . '/models/Expense.php';
require_once APP_PATH . '/models/Category.php';
require_once APP_PATH . '/models/Loan.php';
require_once APP_PATH . '/models/Account.php';

class LoanController
{
    private Loan $loan;

    public function __construct()
    {
        $this->loan=new Loan();
    }

    public function index(): void
    {
        AuthMiddleware::requireLogin();

        $user=Auth::user();
        $business=Auth::business();
        $businessId=Auth::businessId();
        $tenantRole=Auth::tenantRole();

        $loanModel=new Loan();
        $loans=$loanModel->all($businessId);
        $borrowers=$loanModel->borrowers($businessId);
        $categories=$loanModel->categories($businessId);

        $db=Database::getInstance();

        $stmt=$db->prepare("
            SELECT loan_id,COALESCE(SUM(penalty_amount),0) AS penalty_total
            FROM loan_penalties
            WHERE business_id=?
            GROUP BY loan_id
        ");
        $stmt->execute([$businessId]);

        $penaltyTotals=[];

        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $penalty){
            $penaltyTotals[(int)$penalty['loan_id']]=(float)$penalty['penalty_total'];
        }

        foreach($loans as &$loan){
            $loanId=(int)($loan['id']??0);
            $loan['penalty_total']=$penaltyTotals[$loanId]??0;
        }

        unset($loan);

        $accountModel=new Account();
        $accounts=$accountModel->getAll($businessId);

        $success=$_SESSION['loan_success']??'';
        $error=$_SESSION['loan_error']??'';

        unset($_SESSION['loan_success'],$_SESSION['loan_error']);

        require APP_PATH . '/views/loans/index.php';
    }

    public function store(): void
    {
        AuthMiddleware::requireLogin();

        if($_SERVER['REQUEST_METHOD']!=='POST'){
            header('Location: index.php?url=loans');
            exit;
        }

        $user=Auth::user();
        $businessId=Auth::businessId();
        $loanModel=new Loan();

        $borrowerId=(int)($_POST['borrower_id']??0);
        $categoryId=!empty($_POST['category_id'])?(int)$_POST['category_id']:null;
        $principalAmount=(float)($_POST['principal_amount']??0);
        $interestRate=(float)($_POST['interest_rate']??0);
        $interestType=trim($_POST['interest_type']??'flat');
        $accountId=(int)($_POST['account_id']??0);
        $paymentType=trim($_POST['payment_type']??'installment');
        $term=(int)($_POST['term']??1);
        $termPeriod=trim($_POST['term_period']??'months');
        $processingFee=(float)($_POST['processing_fee']??0);
        $releaseDate=!empty($_POST['release_date'])?$_POST['release_date']:date('Y-m-d');
        $firstPaymentDate=!empty($_POST['first_payment_date'])?$_POST['first_payment_date']:null;
        $status=trim($_POST['status']??'pending');
        $purpose=trim($_POST['purpose']??'');
        $notes=trim($_POST['notes']??'');

        if($borrowerId<=0){
            $_SESSION['loan_error']='Please select a borrower.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($accountId<=0){
            $_SESSION['loan_error']='Please select the account from which the loan will be released.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($principalAmount<=0){
            $_SESSION['loan_error']='Principal amount must be greater than zero.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($interestRate<0){
            $_SESSION['loan_error']='Interest rate cannot be negative.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($term<=0){
            $_SESSION['loan_error']='Loan term must be greater than zero.';
            header('Location: index.php?url=loans');
            exit;
        }

        $allowedInterestTypes=['flat','reducing_balance'];

        if(!in_array($interestType,$allowedInterestTypes,true)){
            $_SESSION['loan_error']='Invalid interest type.';
            header('Location: index.php?url=loans');
            exit;
        }

        $allowedPaymentTypes=['installment','full_payment'];

        if(!in_array($paymentType,$allowedPaymentTypes,true)){
            $_SESSION['loan_error']='Invalid payment type.';
            header('Location: index.php?url=loans');
            exit;
        }

        $allowedPeriods=['days','weeks','every_15_days','months','years'];

        if(!in_array($termPeriod,$allowedPeriods,true)){
            $_SESSION['loan_error']='Invalid loan term period.';
            header('Location: index.php?url=loans');
            exit;
        }

        if(!$loanModel->borrowerBelongsToBusiness($borrowerId,$businessId)){
            $_SESSION['loan_error']='Invalid borrower selected.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($paymentType==='full_payment'){
            try{
                $dueDate=new DateTime($releaseDate);

                switch($termPeriod){
                    case 'days':
                        $dueDate->modify("+{$term} days");
                        break;
                    case 'weeks':
                        $dueDate->modify("+{$term} weeks");
                        break;
                    case 'every_15_days':
                        $dueDate->modify("+".($term*15)." days");
                        break;
                    case 'months':
                        $dueDate->modify("+{$term} months");
                        break;
                    case 'years':
                        $dueDate->modify("+{$term} years");
                        break;
                }

                $firstPaymentDate=$dueDate->format('Y-m-d');
            }catch(Throwable $e){
                $_SESSION['loan_error']='Invalid release date.';
                header('Location: index.php?url=loans');
                exit;
            }
        }elseif(!$firstPaymentDate){
            try{
                $firstDate=new DateTime($releaseDate);

                switch($termPeriod){
                    case 'days':
                        $firstDate->modify('+1 day');
                        break;
                    case 'weeks':
                        $firstDate->modify('+1 week');
                        break;
                    case 'every_15_days':
                        $firstDate->modify('+15 days');
                        break;
                    case 'months':
                        $firstDate->modify('+1 month');
                        break;
                    case 'years':
                        $firstDate->modify('+1 year');
                        break;
                }

                $firstPaymentDate=$firstDate->format('Y-m-d');
            }catch(Throwable $e){
                $firstPaymentDate=$releaseDate;
            }
        }

        $totalInterest=0;

        if($interestType==='flat'){
            $totalInterest=$principalAmount*($interestRate/100)*$term;
        }else{
            $periods=$term;

            if($periods>0){
                $periodicRate=$interestRate/100;

                if($periodicRate>0){
                    $factor=pow(1+$periodicRate,$periods);
                    $payment=$principalAmount*($periodicRate*$factor)/($factor-1);
                    $totalInterest=($payment*$periods)-$principalAmount;
                }
            }
        }

        $totalInterest=round($totalInterest,2);

        $totalPayable=round(
            $principalAmount+$totalInterest+$processingFee,
            2
        );

        $loanNumber=$loanModel->generateLoanNumber($businessId);
        $db=Database::getInstance();

        try{
            $db->beginTransaction();

            $loanId=$loanModel->create([
                'business_id'=>$businessId,
                'borrower_id'=>$borrowerId,
                'account_id'=>$accountId,
                'category_id'=>$categoryId,
                'loan_number'=>$loanNumber,
                'principal_amount'=>$principalAmount,
                'interest_rate'=>$interestRate,
                'interest_type'=>$interestType,
                'payment_type'=>$paymentType,
                'term'=>$term,
                'term_period'=>$termPeriod,
                'processing_fee'=>$processingFee,
                'total_interest'=>$totalInterest,
                'total_payable'=>$totalPayable,
                'release_date'=>$releaseDate,
                'first_payment_date'=>$firstPaymentDate,
                'status'=>$status,
                'purpose'=>$purpose,
                'notes'=>$notes,
                'created_by'=>$user['id']??null
            ]);

            if(!$loanId){
                throw new Exception('Unable to create loan.');
            }

            $accountModel=new Account();

            $accountModel->deductForLoan(
                $accountId,
                $businessId,
                $principalAmount
            );

            $loanModel->generateSchedule(
                $loanId,
                $principalAmount,
                $totalInterest,
                $term,
                $termPeriod,
                $firstPaymentDate,
                $paymentType
            );

            if($db->inTransaction()){
                $db->commit();
            }
        }catch(Throwable $e){
            if($db->inTransaction()){
                $db->rollBack();
            }

            $_SESSION['loan_error']=$e->getMessage();
            header('Location: index.php?url=loans');
            exit;
        }

        $_SESSION['loan_success']='Loan created successfully.';
        header('Location: index.php?url=loans');
        exit;
    }

    public function show(): void
    {
        AuthMiddleware::requireLogin();

        $businessId=Auth::businessId();
        $id=(int)($_GET['id']??0);

        if($id<=0){
            $_SESSION['loan_error']='Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel=new Loan();
        $loan=$loanModel->findByBusiness($id,$businessId);

        if(!$loan){
            $_SESSION['loan_error']='Loan not found.';
            header('Location: index.php?url=loans');
            exit;
        }

        $schedule=$loanModel->getSchedule($id);
        $payments=$loanModel->getPayments($id);

        $db=Database::getInstance();

        $stmt=$db->prepare("
            SELECT
                lp.id,
                lp.schedule_id,
                lp.penalty_type,
                lp.penalty_base,
                lp.rate,
                lp.base_amount,
                lp.penalty_amount,
                lp.reason,
                lp.created_by,
                lp.created_at,
                ls.due_date,
                ls.status AS schedule_status
            FROM loan_penalties lp
            LEFT JOIN loan_schedules ls ON ls.id=lp.schedule_id
            WHERE lp.loan_id=?
            AND lp.business_id=?
            ORDER BY lp.created_at DESC,lp.id DESC
        ");

        $stmt->execute([
            $id,
            $businessId
        ]);

        $penalties=$stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalPenalties=0;

        foreach($penalties as $penalty){
            $totalPenalties+=(float)(
                $penalty['penalty_amount']??0
            );
        }

        $totalPaid=0;

        foreach($payments as $payment){
            if(($payment['status']??'posted')==='posted'){
                $totalPaid+=(float)(
                    $payment['amount']??0
                );
            }
        }

        $originalTotalPayable=(float)(
            $loan['total_payable']??0
        );

        $totalPayableWithPenalties=
            $originalTotalPayable+
            $totalPenalties;

        $remainingBalance=max(
            0,
            $totalPayableWithPenalties-
            $totalPaid
        );

        $loan['penalty_total']=$totalPenalties;
        $loan['total_paid']=$totalPaid;
        $loan['total_payable_with_penalties']=$totalPayableWithPenalties;
        $loan['remaining_balance']=$remainingBalance;

        $user=Auth::user();
        $business=Auth::business();
        $tenantRole=Auth::tenantRole();

        require APP_PATH . '/views/loans/show.php';
    }

    public function delete(): void
    {
        AuthMiddleware::requireLogin();

        if($_SERVER['REQUEST_METHOD']!=='POST'){
            $_SESSION['loan_error']='Invalid request.';
            header('Location: index.php?url=loans');
            exit;
        }

        $businessId=Auth::businessId();

        $loanId=(int)(
            $_POST['id']??
            $_POST['loan_id']??
            0
        );

        if($loanId<=0){
            $_SESSION['loan_error']='Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        $db=Database::getInstance();

        try{
            $db->beginTransaction();

            $stmt=$db->prepare("
                SELECT id,business_id,account_id,principal_amount,loan_number,status
                FROM loans
                WHERE id=? AND business_id=?
                LIMIT 1
                FOR UPDATE
            ");

            $stmt->execute([$loanId,$businessId]);
            $loan=$stmt->fetch(PDO::FETCH_ASSOC);

            if(!$loan){
                throw new Exception('Loan not found.');
            }

            $principalAmount=(float)($loan['principal_amount']??0);
            $releaseAccountId=(int)($loan['account_id']??0);

            $stmt=$db->prepare("
                SELECT account_id,COALESCE(SUM(amount),0) AS total_amount
                FROM loan_payments
                WHERE loan_id=? AND business_id=? AND status='posted' AND account_id IS NOT NULL
                GROUP BY account_id
            ");

            $stmt->execute([$loanId,$businessId]);
            $paymentReversals=$stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($paymentReversals as $reversal){
                $paymentAccountId=(int)($reversal['account_id']??0);
                $paymentAmount=(float)($reversal['total_amount']??0);

                if($paymentAccountId<=0||$paymentAmount<=0){
                    continue;
                }

                $stmt=$db->prepare("
                    SELECT id,balance,status
                    FROM accounts
                    WHERE id=? AND business_id=?
                    LIMIT 1
                    FOR UPDATE
                ");

                $stmt->execute([$paymentAccountId,$businessId]);
                $paymentAccount=$stmt->fetch(PDO::FETCH_ASSOC);

                if(!$paymentAccount){
                    throw new Exception('An account associated with a loan payment could not be found.');
                }

                $stmt=$db->prepare("
                    UPDATE accounts
                    SET balance=balance-?
                    WHERE id=? AND business_id=?
                ");

                $stmt->execute([
                    $paymentAmount,
                    $paymentAccountId,
                    $businessId
                ]);
            }

            if($releaseAccountId>0&&$principalAmount>0){
                $stmt=$db->prepare("
                    SELECT id,balance,status
                    FROM accounts
                    WHERE id=? AND business_id=?
                    LIMIT 1
                    FOR UPDATE
                ");

                $stmt->execute([
                    $releaseAccountId,
                    $businessId
                ]);

                $releaseAccount=$stmt->fetch(PDO::FETCH_ASSOC);

                if(!$releaseAccount){
                    throw new Exception('The account used to release this loan could not be found.');
                }

                $stmt=$db->prepare("
                    UPDATE accounts
                    SET balance=balance+?
                    WHERE id=? AND business_id=?
                ");

                $stmt->execute([
                    $principalAmount,
                    $releaseAccountId,
                    $businessId
                ]);
            }

            $stmt=$db->prepare("
                DELETE FROM loan_penalties
                WHERE loan_id=? AND business_id=?
            ");

            $stmt->execute([
                $loanId,
                $businessId
            ]);

            $stmt=$db->prepare("
                DELETE FROM loan_payments
                WHERE loan_id=? AND business_id=?
            ");

            $stmt->execute([
                $loanId,
                $businessId
            ]);

            $stmt=$db->prepare("
                DELETE FROM loan_schedules
                WHERE loan_id=?
            ");

            $stmt->execute([$loanId]);

            $stmt=$db->prepare("
                DELETE FROM loans
                WHERE id=? AND business_id=?
            ");

            $stmt->execute([
                $loanId,
                $businessId
            ]);

            if($stmt->rowCount()<=0){
                throw new Exception('Unable to delete the loan.');
            }

            $db->commit();

            $_SESSION['loan_success']=
                'Loan '.
                ($loan['loan_number']??'').
                ' deleted successfully. The principal amount has been returned to the release account.';

            header('Location: index.php?url=loans');
            exit;
        }catch(Throwable $e){
            if($db->inTransaction()){
                $db->rollBack();
            }

            $_SESSION['loan_error']='Unable to delete loan: '.$e->getMessage();
            header('Location: index.php?url=loans');
            exit;
        }
    }

    public function createAutomaticPenalties(
        int $businessId,
        string $penaltyType='percentage',
        float $rate=5.00,
        string $penaltyBase='overdue_amount',
        ?int $createdBy=null
    ): int {
        $db=Database::getInstance();

        if(!in_array($penaltyType,['fixed','percentage'],true)){
            throw new InvalidArgumentException('Invalid penalty type.');
        }

        if(!in_array($penaltyBase,['principal','total_due','overdue_amount'],true)){
            throw new InvalidArgumentException('Invalid penalty base.');
        }

        if($rate<=0){
            return 0;
        }

        $sql="
            SELECT
                ls.id AS schedule_id,
                ls.loan_id,
                ls.total_due,
                ls.paid_amount,
                ls.due_date,
                l.business_id
            FROM loan_schedules ls
            INNER JOIN loans l ON l.id=ls.loan_id
            LEFT JOIN loan_penalties lp
                ON lp.schedule_id=ls.id
                AND lp.business_id=l.business_id
            WHERE l.business_id=?
            AND ls.due_date<CURDATE()
            AND ls.status IN ('pending','partial')
            AND (ls.total_due-ls.paid_amount)>0
            AND lp.id IS NULL
            ORDER BY ls.due_date ASC
        ";

        $stmt=$db->prepare($sql);
        $stmt->execute([$businessId]);

        $schedules=$stmt->fetchAll(PDO::FETCH_ASSOC);

        if(empty($schedules)){
            return 0;
        }

        $insert=$db->prepare("
            INSERT INTO loan_penalties
            (
                business_id,
                loan_id,
                schedule_id,
                penalty_type,
                penalty_base,
                rate,
                base_amount,
                penalty_amount,
                reason,
                created_by
            )
            VALUES
            (
                :business_id,
                :loan_id,
                :schedule_id,
                :penalty_type,
                :penalty_base,
                :rate,
                :base_amount,
                :penalty_amount,
                :reason,
                :created_by
            )
        ");

        $created=0;

        foreach($schedules as $schedule){
            $scheduleId=(int)$schedule['schedule_id'];
            $loanId=(int)$schedule['loan_id'];

            $totalDue=(float)$schedule['total_due'];
            $paidAmount=(float)$schedule['paid_amount'];

            $overdueAmount=max(
                0,
                $totalDue-$paidAmount
            );

            if($overdueAmount<=0){
                continue;
            }

            switch($penaltyBase){
                case 'principal':
                    $baseAmount=$this->getLoanPrincipal($loanId);
                    break;

                case 'total_due':
                    $baseAmount=$totalDue;
                    break;

                case 'overdue_amount':
                default:
                    $baseAmount=$overdueAmount;
                    break;
            }

            if($penaltyType==='percentage'){
                $penaltyAmount=round(
                    $baseAmount*($rate/100),
                    2
                );
            }else{
                $penaltyAmount=round($rate,2);
            }

            if($penaltyAmount<=0){
                continue;
            }

            $insert->execute([
                ':business_id'=>$businessId,
                ':loan_id'=>$loanId,
                ':schedule_id'=>$scheduleId,
                ':penalty_type'=>$penaltyType,
                ':penalty_base'=>$penaltyBase,
                ':rate'=>$rate,
                ':base_amount'=>$baseAmount,
                ':penalty_amount'=>$penaltyAmount,
                ':reason'=>'Automatic overdue penalty',
                ':created_by'=>$createdBy
            ]);

            $created++;
        }

        return $created;
    }

    private function getLoanPrincipal(int $loanId): float
    {
        $db=Database::getInstance();

        $stmt=$db->prepare("
            SELECT principal_amount
            FROM loans
            WHERE id=?
            LIMIT 1
        ");

        $stmt->execute([$loanId]);

        return (float)($stmt->fetchColumn()??0);
    }

    public function edit(): void
    {
        AuthMiddleware::requireLogin();

        $businessId=Auth::businessId();

        $id=(int)(
            $_GET['id']??
            $_POST['id']??
            0
        );

        if($id<=0){
            http_response_code(400);
            header('Content-Type: application/json');

            echo json_encode([
                'success'=>false,
                'message'=>'Invalid loan ID.'
            ]);

            exit;
        }

        $loanModel=new Loan();

        $loan=$loanModel->findByBusiness(
            $id,
            $businessId
        );

        if(!$loan){
            http_response_code(404);
            header('Content-Type: application/json');

            echo json_encode([
                'success'=>false,
                'message'=>'Loan not found.'
            ]);

            exit;
        }

        header('Content-Type: application/json');

        echo json_encode([
            'success'=>true,
            'loan'=>$loan
        ]);

        exit;
    }

    public function update(): void
    {
        AuthMiddleware::requireLogin();

        if($_SERVER['REQUEST_METHOD']!=='POST'){
            header('Location: index.php?url=loans');
            exit;
        }

        $businessId=Auth::businessId();

        $id=(int)(
            $_POST['id']??
            $_POST['loan_id']??
            0
        );

        if($id<=0){
            $_SESSION['loan_error']='Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel=new Loan();

        $loan=$loanModel->findByBusiness(
            $id,
            $businessId
        );

        if(!$loan){
            $_SESSION['loan_error']='Loan not found.';
            header('Location: index.php?url=loans');
            exit;
        }

        $borrowerId=(int)(
            $_POST['borrower_id']??
            $loan['borrower_id']??
            0
        );

        $categoryId=!empty($_POST['category_id'])
            ?(int)$_POST['category_id']
            :null;

        $principalAmount=(float)(
            $_POST['principal_amount']??
            $loan['principal_amount']??
            0
        );

        $interestRate=(float)(
            $_POST['interest_rate']??
            $loan['interest_rate']??
            0
        );

        $interestType=trim(
            $_POST['interest_type']??
            $loan['interest_type']??
            'flat'
        );

        $paymentType=trim(
            $_POST['payment_type']??
            $loan['payment_type']??
            'installment'
        );

        $term=(int)(
            $_POST['term']??
            $loan['term']??
            1
        );

        $termPeriod=trim(
            $_POST['term_period']??
            $loan['term_period']??
            'months'
        );

        $processingFee=(float)(
            $_POST['processing_fee']??
            $loan['processing_fee']??
            0
        );

        $releaseDate=!empty($_POST['release_date'])
            ?$_POST['release_date']
            :($loan['release_date']??date('Y-m-d'));

        $firstPaymentDate=!empty($_POST['first_payment_date'])
            ?$_POST['first_payment_date']
            :($loan['first_payment_date']??null);

        $status=trim(
            $_POST['status']??
            $loan['status']??
            'pending'
        );

        $purpose=trim(
            $_POST['purpose']??
            $loan['purpose']??
            ''
        );

        $notes=trim(
            $_POST['notes']??
            $loan['notes']??
            ''
        );

        if($borrowerId<=0){
            $_SESSION['loan_error']='Please select a borrower.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($principalAmount<=0){
            $_SESSION['loan_error']='Principal amount must be greater than zero.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($interestRate<0){
            $_SESSION['loan_error']='Interest rate cannot be negative.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($term<=0){
            $_SESSION['loan_error']='Loan term must be greater than zero.';
            header('Location: index.php?url=loans');
            exit;
        }

        $allowedInterestTypes=[
            'flat',
            'reducing_balance'
        ];

        if(!in_array($interestType,$allowedInterestTypes,true)){
            $_SESSION['loan_error']='Invalid interest type.';
            header('Location: index.php?url=loans');
            exit;
        }

        $allowedPaymentTypes=[
            'installment',
            'full_payment'
        ];

        if(!in_array($paymentType,$allowedPaymentTypes,true)){
            $_SESSION['loan_error']='Invalid payment type.';
            header('Location: index.php?url=loans');
            exit;
        }

        $allowedPeriods=[
            'days',
            'weeks',
            'every_15_days',
            'months',
            'years'
        ];

        if(!in_array($termPeriod,$allowedPeriods,true)){
            $_SESSION['loan_error']='Invalid loan term period.';
            header('Location: index.php?url=loans');
            exit;
        }

        if(!$loanModel->borrowerBelongsToBusiness($borrowerId,$businessId)){
            $_SESSION['loan_error']='Invalid borrower selected.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($paymentType==='full_payment'){
            try{
                $dueDate=new DateTime($releaseDate);

                switch($termPeriod){
                    case 'days':
                        $dueDate->modify("+{$term} days");
                        break;
                    case 'weeks':
                        $dueDate->modify("+{$term} weeks");
                        break;
                    case 'every_15_days':
                        $dueDate->modify("+".($term*15)." days");
                        break;
                    case 'months':
                        $dueDate->modify("+{$term} months");
                        break;
                    case 'years':
                        $dueDate->modify("+{$term} years");
                        break;
                }

                $firstPaymentDate=$dueDate->format('Y-m-d');
            }catch(Throwable $e){
                $_SESSION['loan_error']='Invalid release date.';
                header('Location: index.php?url=loans');
                exit;
            }
        }

        $totalInterest=0;

        if($interestType==='flat'){
            $totalInterest=
                $principalAmount*
                ($interestRate/100)*
                $term;
        }else{
            $periods=$term;

            if($periods>0){
                $periodicRate=$interestRate/100;

                if($periodicRate>0){
                    $factor=pow(
                        1+$periodicRate,
                        $periods
                    );

                    $payment=
                        $principalAmount*
                        (
                            $periodicRate*
                            $factor
                        )/
                        (
                            $factor-1
                        );

                    $totalInterest=
                        ($payment*$periods)-
                        $principalAmount;
                }
            }
        }

        $totalInterest=round($totalInterest,2);

        $totalPayable=round(
            $principalAmount+
            $totalInterest+
            $processingFee,
            2
        );

        $db=Database::getInstance();

        try{
            $db->beginTransaction();

            $updated=$loanModel->update(
                $id,
                $businessId,
                [
                    'borrower_id'=>$borrowerId,
                    'category_id'=>$categoryId,
                    'principal_amount'=>$principalAmount,
                    'interest_rate'=>$interestRate,
                    'interest_type'=>$interestType,
                    'payment_type'=>$paymentType,
                    'term'=>$term,
                    'term_period'=>$termPeriod,
                    'processing_fee'=>$processingFee,
                    'total_interest'=>$totalInterest,
                    'total_payable'=>$totalPayable,
                    'release_date'=>$releaseDate,
                    'first_payment_date'=>$firstPaymentDate,
                    'status'=>$status,
                    'purpose'=>$purpose,
                    'notes'=>$notes
                ]
            );

            if($updated===false){
                throw new Exception('Unable to update the loan.');
            }

            $stmt=$db->prepare("
                DELETE FROM loan_schedules
                WHERE loan_id=?
            ");

            $stmt->execute([$id]);

            $loanModel->generateSchedule(
                $id,
                $principalAmount,
                $totalInterest,
                $term,
                $termPeriod,
                $firstPaymentDate,
                $paymentType
            );

            if($db->inTransaction()){
                $db->commit();
            }

            $_SESSION['loan_success']='Loan updated successfully.';

            header('Location: index.php?url=loans');
            exit;
        }catch(Throwable $e){
            if($db->inTransaction()){
                $db->rollBack();
            }

            $_SESSION['loan_error']=$e->getMessage();

            header('Location: index.php?url=loans');
            exit;
        }
    }

    public function approve(): void
    {
        AuthMiddleware::requireLogin();

        $businessId=Auth::businessId();
        $id=(int)($_POST['id']??0);

        if($id<=0){
            $_SESSION['loan_error']='Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel=new Loan();

        $loan=$loanModel->findByBusiness(
            $id,
            $businessId
        );

        if(!$loan){
            $_SESSION['loan_error']='Loan not found.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($loan['status']!=='pending'){
            $_SESSION['loan_error']='Only pending loans can be approved.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel->update(
            $id,
            $businessId,
            ['status'=>'active']
        );

        $_SESSION['loan_success']='Loan approved successfully.';

        header('Location: index.php?url=loans');
        exit;
    }

    public function release(): void
    {
        AuthMiddleware::requireLogin();

        $businessId=Auth::businessId();
        $id=(int)($_POST['id']??0);

        if($id<=0){
            $_SESSION['loan_error']='Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel=new Loan();

        $loan=$loanModel->findByBusiness(
            $id,
            $businessId
        );

        if(!$loan){
            $_SESSION['loan_error']='Loan not found.';
            header('Location: index.php?url=loans');
            exit;
        }

        if(!in_array(
            $loan['status'],
            ['approved','pending'],
            true
        )){
            $_SESSION['loan_error']='This loan cannot be released.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel->update(
            $id,
            $businessId,
            [
                'status'=>'active',
                'release_date'=>date('Y-m-d')
            ]
        );

        $_SESSION['loan_success']='Loan released successfully.';

        header('Location: index.php?url=loans');
        exit;
    }

    public function penalty(): void
    {
        AuthMiddleware::requireLogin();

        $businessId=Auth::businessId();

        $loanId=(int)(
            $_GET['loan_id']??
            $_GET['id']??
            $_POST['id']??
            $_POST['loan_id']??
            0
        );

        if($loanId<=0){
            http_response_code(400);
            header('Content-Type: application/json');

            echo json_encode([
                'success'=>false,
                'message'=>'Invalid loan ID.'
            ]);

            exit;
        }

        $loanModel=new Loan();

        $loan=$loanModel->findByBusiness(
            $loanId,
            $businessId
        );

        if(!$loan){
            http_response_code(404);
            header('Content-Type: application/json');

            echo json_encode([
                'success'=>false,
                'message'=>'Loan not found.'
            ]);

            exit;
        }

        $schedules=$loanModel->getSchedule($loanId);
        $db=Database::getInstance();

        $stmt=$db->prepare("
            SELECT
                ls.id,
                ls.loan_id,
                ls.total_due,
                ls.paid_amount,
                ls.due_date,
                ls.status
            FROM loan_schedules ls
            WHERE ls.loan_id=?
            AND ls.due_date<CURDATE()
            AND ls.status IN ('pending','partial')
            AND (ls.total_due-ls.paid_amount)>0
            ORDER BY ls.due_date ASC,ls.id ASC
            LIMIT 1
        ");

        $stmt->execute([$loanId]);

        $recommendedSchedule=$stmt->fetch(PDO::FETCH_ASSOC);

        if($recommendedSchedule){
            $recommendedSchedule['remaining_amount']=max(
                0,
                (float)$recommendedSchedule['total_due']-
                (float)$recommendedSchedule['paid_amount']
            );
        }

        header('Content-Type: application/json');

        echo json_encode([
            'success'=>true,
            'loan'=>$loan,
            'schedules'=>$schedules,
            'recommended_schedule'=>$recommendedSchedule
        ]);

        exit;
    }

    public function storePenalty(): void
    {
        AuthMiddleware::requireLogin();

        if($_SERVER['REQUEST_METHOD']!=='POST'){
            header('Location: index.php?url=loans');
            exit;
        }

        $businessId=Auth::businessId();
        $user=Auth::user();

        $loanId=(int)(
            $_POST['loan_id']??0
        );

        $scheduleId=!empty($_POST['schedule_id'])
            ?(int)$_POST['schedule_id']
            :null;

        $penaltyType=trim(
            $_POST['penalty_type']??
            'fixed'
        );

        $penaltyBaseRate=(float)(
            $_POST['penalty_base_rate']??0
        );

        $reason=trim(
            $_POST['reason']??''
        );

        if($loanId<=0){
            $_SESSION['loan_error']='Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        $allowedPenaltyTypes=[
            'fixed',
            'percentage'
        ];

        if(!in_array(
            $penaltyType,
            $allowedPenaltyTypes,
            true
        )){
            $_SESSION['loan_error']='Invalid penalty type.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($penaltyBaseRate<=0){
            $_SESSION['loan_error']='Penalty rate or amount must be greater than zero.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel=new Loan();

        $loan=$loanModel->findByBusiness(
            $loanId,
            $businessId
        );

        if(!$loan){
            $_SESSION['loan_error']='Loan not found.';
            header('Location: index.php?url=loans');
            exit;
        }

        $db=Database::getInstance();

        try{
            $db->beginTransaction();

            if(!$scheduleId){
                $stmt=$db->prepare("
                    SELECT
                        id,
                        loan_id,
                        total_due,
                        paid_amount,
                        due_date,
                        status
                    FROM loan_schedules
                    WHERE loan_id=?
                    AND due_date<CURDATE()
                    AND status IN ('pending','partial')
                    AND (total_due-paid_amount)>0
                    ORDER BY due_date ASC,id ASC
                    LIMIT 1
                    FOR UPDATE
                ");

                $stmt->execute([
                    $loanId
                ]);

                $schedule=$stmt->fetch(PDO::FETCH_ASSOC);

                if(!$schedule){
                    throw new Exception(
                        'No overdue unpaid installment was found for this loan.'
                    );
                }

                $scheduleId=(int)$schedule['id'];
            }else{
                $stmt=$db->prepare("
                    SELECT
                        id,
                        loan_id,
                        total_due,
                        paid_amount,
                        due_date,
                        status
                    FROM loan_schedules
                    WHERE id=?
                    AND loan_id=?
                    LIMIT 1
                    FOR UPDATE
                ");

                $stmt->execute([
                    $scheduleId,
                    $loanId
                ]);

                $schedule=$stmt->fetch(PDO::FETCH_ASSOC);

                if(!$schedule){
                    throw new Exception(
                        'Selected schedule does not belong to this loan.'
                    );
                }
            }

            if(
                empty($schedule['due_date'])||
                $schedule['due_date']>=date('Y-m-d')
            ){
                throw new Exception(
                    'A penalty can only be added to an overdue installment.'
                );
            }

            if(!in_array(
                $schedule['status'],
                ['pending','partial'],
                true
            )){
                throw new Exception(
                    'This installment has already been paid.'
                );
            }

            $totalDue=(float)(
                $schedule['total_due']??0
            );

            $paidAmount=(float)(
                $schedule['paid_amount']??0
            );

            $baseAmount=max(
                0,
                $totalDue-$paidAmount
            );

            if($baseAmount<=0){
                throw new Exception(
                    'This installment has no remaining balance.'
                );
            }

            if($penaltyType==='percentage'){
                $penaltyAmount=round(
                    $baseAmount*
                    ($penaltyBaseRate/100),
                    2
                );
            }else{
                $penaltyAmount=round(
                    $penaltyBaseRate,
                    2
                );
            }

            if($penaltyAmount<=0){
                throw new Exception(
                    'Penalty amount must be greater than zero.'
                );
            }

            $stmt=$db->prepare("
                SELECT id
                FROM loan_penalties
                WHERE loan_id=?
                AND schedule_id=?
                AND business_id=?
                LIMIT 1
            ");

            $stmt->execute([
                $loanId,
                $scheduleId,
                $businessId
            ]);

            if($stmt->fetchColumn()){
                throw new Exception(
                    'A penalty has already been added to this installment.'
                );
            }

            $stmt=$db->prepare("
                INSERT INTO loan_penalties
                (
                    business_id,
                    loan_id,
                    schedule_id,
                    penalty_type,
                    penalty_base,
                    rate,
                    base_amount,
                    penalty_amount,
                    reason,
                    created_by
                )
                VALUES
                (
                    :business_id,
                    :loan_id,
                    :schedule_id,
                    :penalty_type,
                    :penalty_base,
                    :rate,
                    :base_amount,
                    :penalty_amount,
                    :reason,
                    :created_by
                )
            ");

            $stmt->execute([
                ':business_id'=>$businessId,
                ':loan_id'=>$loanId,
                ':schedule_id'=>$scheduleId,
                ':penalty_type'=>$penaltyType,
                ':penalty_base'=>'overdue_amount',
                ':rate'=>$penaltyBaseRate,
                ':base_amount'=>$baseAmount,
                ':penalty_amount'=>$penaltyAmount,
                ':reason'=>$reason!==''?$reason:null,
                ':created_by'=>$user['id']??null
            ]);

            $db->commit();

            $_SESSION['loan_success']=
                'Penalty added successfully to the overdue installment.';

            header('Location: index.php?url=loans');
            exit;
        }catch(Throwable $e){
            if($db->inTransaction()){
                $db->rollBack();
            }

            $_SESSION['loan_error']=$e->getMessage();

            header('Location: index.php?url=loans');
            exit;
        }
    }

    public function payment(): void
    {
        AuthMiddleware::requireLogin();

        $businessId=Auth::businessId();

        $id=(int)(
            $_GET['id']??0
        );

        if($id<=0){
            $_SESSION['loan_error']='Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanModel=new Loan();

        $loan=$loanModel->findByBusiness(
            $id,
            $businessId
        );

        if(!$loan){
            $_SESSION['loan_error']='Loan not found.';
            header('Location: index.php?url=loans');
            exit;
        }

        if(!in_array(
            $loan['status'],
            ['approved','active','overdue'],
            true
        )){
            $_SESSION['loan_error']='Payment cannot be made for this loan.';
            header('Location: index.php?url=loans');
            exit;
        }

        $schedule=$loanModel->getSchedule($id);
        $db=Database::getInstance();

        $stmt=$db->prepare("
            SELECT
                id,
                account_name,
                account_type,
                balance
            FROM accounts
            WHERE business_id=?
            AND status='active'
            ORDER BY account_name ASC
        ");

        $stmt->execute([
            $businessId
        ]);

        $accounts=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $payments=$loanModel->getPayments($id);

        $totalPaid=0;

        foreach($payments as $payment){
            if(($payment['status']??'posted')==='posted'){
                $totalPaid+=(float)(
                    $payment['amount']??0
                );
            }
        }

        $remainingBalance=max(
            0,
            (float)$loan['total_payable']-
            $totalPaid
        );

        $loan['total_paid']=$totalPaid;
        $loan['remaining_balance']=$remainingBalance;

        require APP_PATH . '/views/loans/payment.php';
    }

    public function storePayment(): void
    {
        AuthMiddleware::requireLogin();

        if($_SERVER['REQUEST_METHOD']!=='POST'){
            header('Location: index.php?url=loans');
            exit;
        }

        $businessId=Auth::businessId();
        $user=Auth::user();

        $loanId=(int)(
            $_POST['loan_id']??0
        );

        $scheduleId=!empty($_POST['schedule_id'])
            ?(int)$_POST['schedule_id']
            :null;

        $accountId=(int)(
            $_POST['account_id']??0
        );

        $amount=(float)(
            $_POST['amount']??0
        );

        $paymentDate=!empty($_POST['payment_date'])
            ?$_POST['payment_date']
            :date('Y-m-d');

        $notes=trim(
            $_POST['notes']??''
        );

        if($loanId<=0){
            $_SESSION['loan_error']='Invalid loan ID.';
            header('Location: index.php?url=loans');
            exit;
        }

        if($amount<=0){
            $_SESSION['loan_error']='Payment amount must be greater than zero.';
            header('Location: index.php?url=loans/payment&id='.$loanId);
            exit;
        }

        if($accountId<=0){
            $_SESSION['loan_error']='Please select an account.';
            header('Location: index.php?url=loans/payment&id='.$loanId);
            exit;
        }

        $loanModel=new Loan();

        $loan=$loanModel->findByBusiness(
            $loanId,
            $businessId
        );

        if(!$loan){
            $_SESSION['loan_error']='Loan not found.';
            header('Location: index.php?url=loans');
            exit;
        }

        if(!in_array(
            $loan['status'],
            ['approved','active','overdue'],
            true
        )){
            $_SESSION['loan_error']='Payment cannot be made for this loan.';
            header('Location: index.php?url=loans');
            exit;
        }

        $db=Database::getInstance();

        try{
            $db->beginTransaction();

            $stmt=$db->prepare("
                SELECT id,balance,status
                FROM accounts
                WHERE id=? AND business_id=?
                FOR UPDATE
            ");

            $stmt->execute([
                $accountId,
                $businessId
            ]);

            $account=$stmt->fetch(PDO::FETCH_ASSOC);

            if(!$account){
                throw new Exception('Selected account was not found.');
            }

            if($account['status']!=='active'){
                throw new Exception('Selected account is inactive.');
            }

            $stmt=$db->prepare("
                SELECT COALESCE(SUM(amount),0)
                FROM loan_payments
                WHERE loan_id=? AND business_id=? AND status='posted'
            ");

            $stmt->execute([
                $loanId,
                $businessId
            ]);

            $totalPaid=(float)(
                $stmt->fetchColumn()??0
            );

            $remainingBalance=max(
                0,
                (float)$loan['total_payable']-
                $totalPaid
            );

            if($amount>$remainingBalance+0.01){
                throw new Exception(
                    'Payment cannot be greater than the remaining loan balance of ₱'.
                    number_format($remainingBalance,2).
                    '.'
                );
            }

            if(abs($amount-$remainingBalance)<=0.01){
                $amount=$remainingBalance;
            }

            $principalAmount=$amount;
            $interestAmount=0;
            $penaltyAmount=0;

            if($scheduleId){
                $stmt=$db->prepare("
                    SELECT
                        id,
                        loan_id,
                        total_due,
                        paid_amount,
                        status,
                        principal_amount,
                        interest_amount
                    FROM loan_schedules
                    WHERE id=? AND loan_id=?
                    FOR UPDATE
                ");

                $stmt->execute([
                    $scheduleId,
                    $loanId
                ]);

                $schedule=$stmt->fetch(PDO::FETCH_ASSOC);

                if(!$schedule){
                    throw new Exception(
                        'Selected payment schedule was not found.'
                    );
                }

                $scheduleRemaining=max(
                    0,
                    (float)$schedule['total_due']-
                    (float)$schedule['paid_amount']
                );

                if($amount>$scheduleRemaining+0.01){
                    throw new Exception(
                        'Payment is greater than the remaining amount for this installment.'
                    );
                }

                $scheduleInterest=(float)(
                    $schedule['interest_amount']??0
                );

                $scheduleInterestRemaining=max(
                    0,
                    $scheduleInterest-
                    min(
                        $scheduleInterest,
                        (float)$schedule['paid_amount']
                    )
                );

                $interestAmount=min(
                    $amount,
                    $scheduleInterestRemaining
                );

                $principalAmount=max(
                    0,
                    $amount-$interestAmount
                );
            }

            $paymentNumber=
                'PAY-'.
                date('Ymd').
                '-'.
                strtoupper(
                    substr(
                        bin2hex(random_bytes(4)),
                        0,
                        6
                    )
                );

            $stmt=$db->prepare("
                INSERT INTO loan_payments
                (
                    business_id,
                    loan_id,
                    schedule_id,
                    account_id,
                    payment_number,
                    payment_date,
                    amount,
                    principal_amount,
                    interest_amount,
                    penalty_amount,
                    notes,
                    status,
                    created_by
                )
                VALUES
                (
                    :business_id,
                    :loan_id,
                    :schedule_id,
                    :account_id,
                    :payment_number,
                    :payment_date,
                    :amount,
                    :principal_amount,
                    :interest_amount,
                    :penalty_amount,
                    :notes,
                    'posted',
                    :created_by
                )
            ");

            $stmt->execute([
                ':business_id'=>$businessId,
                ':loan_id'=>$loanId,
                ':schedule_id'=>$scheduleId,
                ':account_id'=>$accountId,
                ':payment_number'=>$paymentNumber,
                ':payment_date'=>$paymentDate,
                ':amount'=>$amount,
                ':principal_amount'=>$principalAmount,
                ':interest_amount'=>$interestAmount,
                ':penalty_amount'=>$penaltyAmount,
                ':notes'=>$notes,
                ':created_by'=>$user['id']??null
            ]);

            if($scheduleId){
                $stmt=$db->prepare("
                    UPDATE loan_schedules
                    SET
                        paid_amount=paid_amount+:payment_amount,
                        status=
                            CASE
                                WHEN paid_amount+:status_amount>=total_due
                                THEN 'paid'
                                WHEN paid_amount+:partial_amount>0
                                THEN 'partial'
                                ELSE status
                            END,
                        paid_date=
                            CASE
                                WHEN paid_amount+:date_amount>=total_due
                                THEN :paid_date
                                ELSE paid_date
                            END
                    WHERE id=:schedule_id
                    AND loan_id=:schedule_loan_id
                ");

                $stmt->execute([
                    ':payment_amount'=>$amount,
                    ':status_amount'=>$amount,
                    ':partial_amount'=>$amount,
                    ':date_amount'=>$amount,
                    ':paid_date'=>$paymentDate,
                    ':schedule_id'=>$scheduleId,
                    ':schedule_loan_id'=>$loanId
                ]);
            }

            $stmt=$db->prepare("
                UPDATE accounts
                SET balance=balance+?
                WHERE id=? AND business_id=? AND status='active'
            ");

            $stmt->execute([
                $amount,
                $accountId,
                $businessId
            ]);

            $stmt=$db->prepare("
                SELECT COALESCE(SUM(amount),0)
                FROM loan_payments
                WHERE loan_id=? AND business_id=? AND status='posted'
            ");

            $stmt->execute([
                $loanId,
                $businessId
            ]);

            $newTotalPaid=(float)(
                $stmt->fetchColumn()??0
            );

            $newRemainingBalance=max(
                0,
                (float)$loan['total_payable']-
                $newTotalPaid
            );

            if($newRemainingBalance<=0.01){
                $stmt=$db->prepare("
                    UPDATE loans
                    SET status='completed'
                    WHERE id=? AND business_id=?
                ");

                $stmt->execute([
                    $loanId,
                    $businessId
                ]);
            }elseif($loan['status']==='approved'){
                $stmt=$db->prepare("
                    UPDATE loans
                    SET status='active'
                    WHERE id=? AND business_id=? AND status='approved'
                ");

                $stmt->execute([
                    $loanId,
                    $businessId
                ]);
            }

            $db->commit();

            $_SESSION['loan_success']=
                'Payment recorded successfully. Remaining balance: ₱'.
                number_format(
                    $newRemainingBalance,
                    2
                ).
                '.';

            header('Location: index.php?url=loans');
            exit;
        }catch(Throwable $e){
            if($db->inTransaction()){
                $db->rollBack();
            }

            $_SESSION['loan_error']=$e->getMessage();

            header(
                'Location: index.php?url=loans/payment&id='.$loanId
            );

            exit;
        }
    }

    public function payments(): void
    {
        AuthMiddleware::requireLogin();

        $businessId=Auth::businessId();
        $loanModel=new Loan();

        $payments=$loanModel->getAllPayments(
            $businessId
        );

        $totalPayments=0;
        $totalPrincipal=0;
        $totalInterest=0;
        $totalPenalty=0;

        foreach($payments as $payment){
            if(($payment['status']??'posted')==='posted'){
                $totalPayments+=(float)(
                    $payment['amount']??0
                );

                $totalPrincipal+=(float)(
                    $payment['principal_amount']??0
                );

                $totalInterest+=(float)(
                    $payment['interest_amount']??0
                );

                $totalPenalty+=(float)(
                    $payment['penalty_amount']??0
                );
            }
        }

        $success=$_SESSION['loan_success']??'';
        $error=$_SESSION['loan_error']??'';

        unset(
            $_SESSION['loan_success'],
            $_SESSION['loan_error']
        );

        $user=Auth::user();
        $business=Auth::business();
        $tenantRole=Auth::tenantRole();

        require APP_PATH . '/views/payments/index.php';
    }
}