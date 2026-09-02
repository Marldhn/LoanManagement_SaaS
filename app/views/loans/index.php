<?php
$user=$user??Auth::user();
$business=$business??Auth::business();
$tenantRole=$tenantRole??Auth::tenantRole();
$currentUrl='loans';
$loans=$loans??[];
$borrowers=$borrowers??[];
$categories=$categories??[];
$accounts=$accounts??[];
$success=$success??($_SESSION['loan_success']??'');
$error=$error??($_SESSION['loan_error']??'');

if(!function_exists('formatPaymentMethod')){
    function formatPaymentMethod($value):string{
        return match(strtolower(trim((string)$value))){
            'full_payment'=>'Full Payment',
            'installment',''=>'Installment',
            default=>ucwords(str_replace('_',' ',$value))
        };
    }
}

$totalLoans=count($loans);
$activeLoans=$pendingLoans=0;
$totalPrincipal=$totalPayable=0;
$loanPaymentTotals=[];

foreach($loans as $loan){
    $id=(int)($loan['id']??0);
    if(!$id)continue;

    if(array_key_exists('total_paid',$loan))
        $loanPaymentTotals[$id]=(float)$loan['total_paid'];
    elseif(array_key_exists('paid_amount',$loan))
        $loanPaymentTotals[$id]=(float)$loan['paid_amount'];
    else
        $loanPaymentTotals[$id]=0;

    if(!empty($loan['payments'])&&is_array($loan['payments'])){
        $total=0;
        foreach($loan['payments'] as $payment)
            $total+=(float)($payment['amount']??$payment['payment_amount']??$payment['paid_amount']??$payment['total_amount']??0);
        $loanPaymentTotals[$id]=$total;
    }
}

foreach($loans as $loan){
    $status=strtolower($loan['status']??'pending');
    $activeLoans+=($status==='active');
    $pendingLoans+=($status==='pending');
    $totalPrincipal+=(float)($loan['principal_amount']??0);

    $id=(int)($loan['id']??0);
    $payable=(float)($loan['total_payable']??0);
    $paid=(float)($loanPaymentTotals[$id]??0);
    $totalPayable+=max(0,$payable-$paid);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Loans | Loan Management</title>
<link rel="stylesheet" href="assets/css/style.css">

<style>
.loan-decision-section{margin-top:25px;padding:18px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px}
.loan-decision-title{font-size:14px;font-weight:700;color:#374151;margin-bottom:12px}
.loan-decision-actions{display:flex;gap:12px;align-items:center}
.loan-decision-actions form,.loan-action-form{margin:0;padding:0}
.loan-decision-button{min-width:155px;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 18px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s}
.loan-decision-approve{background:#dcfce7;color:#166534;border:1px solid #86efac;box-shadow:0 0 12px rgba(34,197,94,.45)}
.loan-decision-approve:hover{background:#bbf7d0;box-shadow:0 0 20px rgba(34,197,94,.65);transform:translateY(-1px)}
.loan-decision-reject{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;box-shadow:0 0 12px rgba(239,68,68,.45)}
.loan-decision-reject:hover{background:#fecaca;box-shadow:0 0 20px rgba(239,68,68,.65);transform:translateY(-1px)}
.loan-decision-button.disabled,.loan-decision-button:disabled{background:#e5e7eb;color:#9ca3af;border-color:#d1d5db;box-shadow:none;cursor:not-allowed;opacity:.8;transform:none}

.modal-overlay{position:fixed;inset:0;width:100%;height:100%;background:rgba(0,0,0,.55);display:none;align-items:center;justify-content:center;z-index:9999;padding:20px;box-sizing:border-box}
.modal-overlay.active{display:flex}
.modal{width:100%;max-width:700px;max-height:92vh;overflow-y:auto;background:#fff;border-radius:12px;padding:25px;box-sizing:border-box;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.modal-small{max-width:550px}
.modal-header{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:25px}
.modal-header h2{margin:0 0 5px}
.modal-header p{margin:0;color:#6b7280}
.modal-close{border:0;background:transparent;font-size:28px;line-height:1;cursor:pointer;color:#6b7280}
.modal-close:hover{color:#111827}
.modal-footer{display:flex;justify-content:flex-end;gap:10px;margin-top:25px;flex-wrap:wrap}

.loan-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.loan-form-full{grid-column:1/-1}
.form-group{margin-bottom:0}
.form-group label{display:block;margin-bottom:7px;font-weight:600}
.form-group input,.form-group select,.form-group textarea{width:100%;box-sizing:border-box}
.account-balance-hint,.penalty-calculation-hint{display:block;margin-top:5px;font-size:12px;color:#6b7280}

.loan-summary-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px;margin-bottom:25px}
.loan-summary-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
.loan-summary-title{font-size:14px;color:#6b7280;margin-bottom:8px}
.loan-summary-value{font-size:25px;font-weight:700;color:#111827}

.loan-details-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}
.loan-detail-item{padding:14px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb}
.loan-detail-label{display:block;font-size:12px;color:#6b7280;margin-bottom:5px}
.loan-detail-value{font-weight:600;color:#111827}
.loan-detail-full{grid-column:1/-1}
.loan-due-date{display:inline-block;padding:5px 10px;border-radius:6px;background:#fef3c7;color:#92400e;font-size:12px;font-weight:600}
.loan-payment-schedule{margin-top:5px}
.loan-payment-schedule-title{font-size:13px;font-weight:700;color:#374151;margin-bottom:10px}
.loan-payment-schedule-list{margin:0;padding-left:20px;color:#374151}
.loan-payment-schedule-list li{margin-bottom:7px;line-height:1.5}
.loan-payment-schedule-empty{color:#9ca3af}

.loan-status{display:inline-block;padding:5px 10px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
.loan-status-pending{background:#fef3c7;color:#92400e}
.loan-status-approved{background:#dbeafe;color:#1e40af}
.loan-status-active{background:#dcfce7;color:#166534}
.loan-status-completed{background:#e0e7ff;color:#3730a3}
.loan-status-overdue{background:#fee2e2;color:#991b1b}
.loan-status-cancelled,.loan-status-rejected{background:#f3f4f6;color:#374151}

.payment-method{display:inline-block;padding:5px 9px;border-radius:6px;font-size:12px;font-weight:600;background:#f3f4f6;color:#374151}
.loan-number{font-weight:700}
.loan-actions{display:flex;gap:6px;flex-wrap:wrap}
.loan-action-menu{position:relative;display:inline-block}
.loan-action-button{width:36px;height:36px;border:1px solid #e5e7eb;background:#fff;border-radius:8px;font-size:22px;line-height:1;cursor:pointer;color:#374151;display:flex;align-items:center;justify-content:center;padding:0;position:relative;z-index:2}
.loan-action-button:hover{background:#f3f4f6}
.loan-action-dropdown{position:absolute;right:0;top:calc(100% + 6px);min-width:180px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.12);padding:6px;z-index:10000;display:none;box-sizing:border-box}
.loan-action-dropdown.active{display:block}
.loan-action-item{width:100%;display:flex;align-items:center;gap:10px;padding:10px 12px;border:0;background:transparent;color:#374151;text-decoration:none;font-size:14px;font-weight:500;border-radius:7px;cursor:pointer;box-sizing:border-box;text-align:left}
.loan-action-item:hover{background:#f3f4f6}
.loan-action-approve{color:#166534}
.loan-action-approve:hover{background:#dcfce7}
.loan-action-danger{color:#991b1b}
.loan-action-danger:hover{background:#fee2e2}
.loan-action-penalty{color:#92400e}
.loan-action-penalty:hover{background:#fef3c7}

.penalty-summary{padding:15px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;margin-bottom:20px}
.penalty-summary-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.penalty-summary-item{padding:10px;background:#fff;border-radius:7px;border:1px solid #f3f4f6}
.penalty-summary-label{display:block;font-size:12px;color:#6b7280;margin-bottom:4px}
.penalty-summary-value{font-weight:700;color:#111827}
.penalty-total-box{margin-top:18px;padding:15px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px}
.penalty-total-row{display:flex;justify-content:space-between;align-items:center;gap:15px}
.penalty-total-label{font-weight:600;color:#374151}
.penalty-total-value{font-size:20px;font-weight:800;color:#92400e}

@media(max-width:1100px){.loan-summary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:700px){
.loan-form-grid,.loan-details-grid,.penalty-summary-grid{grid-template-columns:1fr}
.loan-form-full,.loan-detail-full{grid-column:auto}
.loan-summary-grid{grid-template-columns:1fr}
.loan-decision-actions{flex-direction:column;align-items:stretch}
.loan-decision-actions form{width:100%}
.loan-decision-button{width:100%}
.modal-footer{justify-content:stretch}
}
</style>
</head>

<body>
<?php require APP_PATH.'/views/layouts/sidebar.php'; ?>

<div class="main-content">
<nav class="navbar">
<div class="page-title">Loans</div>
<div class="user-info">
<span class="user-name"><?=htmlspecialchars($user['full_name']??$user['username']??'User')?></span>
<span class="badge"><?=htmlspecialchars($tenantRole??'User')?></span>
</div>
</nav>

<div class="container">

<div class="page-header">
<div>
<h1>Loans</h1>
<p>Manage borrower loans, payments and loan status.</p>
</div>
<button type="button" class="btn btn-primary" onclick="openCreateLoanModal()">+ Create Loan</button>
</div>

<?php if($success): ?>
<div class="alert alert-success" style="margin-bottom:20px"><?=htmlspecialchars($success)?></div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger" style="margin-bottom:20px"><?=htmlspecialchars($error)?></div>
<?php endif; ?>

<div class="loan-summary-grid">

<div class="loan-summary-card">
<div class="loan-summary-title">Total Loans</div>
<div class="loan-summary-value"><?=number_format($totalLoans)?></div>
</div>

<div class="loan-summary-card">
<div class="loan-summary-title">Active Loans</div>
<div class="loan-summary-value"><?=number_format($activeLoans)?></div>
</div>

<div class="loan-summary-card">
<div class="loan-summary-title">Total Principal</div>
<div class="loan-summary-value">₱<?=number_format($totalPrincipal,2)?></div>
</div>

<div class="loan-summary-card">
<div class="loan-summary-title">Total Payable</div>
<div class="loan-summary-value">₱<?=number_format($totalPayable,2)?></div>
</div>

</div>

<?php if(empty($loans)): ?>

<div class="form-card" style="margin-top:20px">
<div class="empty-state">
<h3>No Loans Found</h3>
<p>You haven't created any loans yet.</p>
<br>
<button type="button" class="btn btn-primary" onclick="openCreateLoanModal()">Create Your First Loan</button>
</div>
</div>

<?php else: ?>

<div class="table-container" style="margin-top:20px">
<table>
<thead>
<tr>
<th>Loan Number</th>
<th>Borrower</th>
<th>Principal</th>
<th>Interest</th>
<th>Total Payable</th>
<th>Payment Method</th>
<th>Term</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php
$js=fn($v)=>htmlspecialchars(json_encode($v,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT),ENT_QUOTES,'UTF-8');
?>

<?php foreach($loans as $loan): ?>

<?php
$loanId=(int)($loan['id']??0);
$loanNumber=$loan['loan_number']??'';
$borrowerName=$loan['borrower_name']??$loan['full_name']??$loan['borrower']??'Unknown Borrower';
$principal=(float)($loan['principal_amount']??0);
$interest=(float)($loan['total_interest']??0);
$originalPayable=(float)($loan['total_payable']??0);
$paidAmount=(float)($loanPaymentTotals[$loanId]??0);
$payable=max(0,$originalPayable-$paidAmount);
$rate=(float)($loan['interest_rate']??0);
$interestType=$loan['interest_type']??'flat';
$term=(int)($loan['term']??1);
$termPeriod=$loan['term_period']??'months';
$paymentType=$loan['payment_type']??'installment';
$fee=(float)($loan['processing_fee']??0);
$releaseDate=$loan['release_date']??'';
$firstDate=$loan['first_payment_date']??'';
$status=strtolower($loan['status']??'pending');
$purpose=$loan['purpose']??'';
$notes=$loan['notes']??'';
$category=$loan['category_name']??'';
$scheduleId=(int)($loan['schedule_id']??0);
?>

<tr>

<td><span class="loan-number"><?=htmlspecialchars($loanNumber)?></span></td>
<td><?=htmlspecialchars($borrowerName)?></td>
<td><strong>₱<?=number_format($principal,2)?></strong></td>
<td>₱<?=number_format($interest,2)?></td>
<td><strong>₱<?=number_format($payable,2)?></strong></td>
<td><span class="payment-method"><?=htmlspecialchars(formatPaymentMethod($paymentType))?></span></td>
<td><?=$term?></td>
<td><span class="loan-status loan-status-<?=htmlspecialchars($status)?>"><?=htmlspecialchars(ucfirst($status))?></span></td>

<td>
<div class="loan-action-menu">

<button type="button" class="loan-action-button" onclick="toggleLoanActions(<?=$loanId?>)" aria-expanded="false" data-loan-id="<?=$loanId?>">⋮</button>

<div class="loan-action-dropdown" id="loan-actions-<?=$loanId?>">

<button type="button" class="loan-action-item" onclick="closeLoanActions();openLoanDetails(
<?=$loanId?>,
<?=$js($loanNumber)?>,
<?=$js($borrowerName)?>,
<?=$js($category)?>,
<?=$js($principal)?>,
<?=$js($rate)?>,
<?=$js($interestType)?>,
<?=$js($term)?>,
<?=$js($termPeriod)?>,
<?=$js($paymentType)?>,
<?=$js($fee)?>,
<?=$js($interest)?>,
<?=$js($payable)?>,
<?=$js($releaseDate)?>,
<?=$js($firstDate)?>,
<?=$js($status)?>,
<?=$js($purpose)?>,
<?=$js($notes)?>
)">
<span>👁</span> View Details
</button>

<button type="button" class="loan-action-item" onclick="closeLoanActions();openLoanEdit(
<?=$loanId?>,
<?=$js($borrowerName)?>,
<?=$js($category)?>,
<?=$js($principal)?>,
<?=$js($rate)?>,
<?=$js($interestType)?>,
<?=$js($term)?>,
<?=$js($termPeriod)?>,
<?=$js($paymentType)?>,
<?=$js($fee)?>,
<?=$js($releaseDate)?>,
<?=$js($firstDate)?>,
<?=$js($purpose)?>,
<?=$js($notes)?>
)">
<span>✏️</span> Edit
</button>

<a href="index.php?url=loans/payment&id=<?=$loanId?>" class="loan-action-item" onclick="closeLoanActions()">
<span>💵</span> Payment
</a>

<button type="button" class="loan-action-item loan-action-penalty" onclick="closeLoanActions();openPenaltyModal(
<?=$loanId?>,
<?=$js($loanNumber)?>,
<?=$js($borrowerName)?>,
<?=$scheduleId?>,
<?=$js($payable)?>
)">
<span>⚠️</span> Penalty
</button>

<!-- PERMANENT DELETE: AVAILABLE FOR ALL LOAN STATUSES -->
<form method="POST" action="index.php?url=loans/delete" onsubmit="return confirm('Are you sure you want to permanently delete this loan? This action cannot be undone.')">
<input type="hidden" name="id" value="<?=$loanId?>">
<button type="submit" class="loan-action-item loan-action-danger">
<span>🗑</span> Delete
</button>
</form>

</div>
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

<!-- EDIT LOAN MODAL -->

<div class="modal-overlay" id="editLoanModal" onclick="closeEditLoanModal(event)">
<div class="modal" onclick="event.stopPropagation()">

<div class="modal-header">
<div>
<h2>Edit Loan</h2>
<p>Update the loan information.</p>
</div>
<button type="button" class="modal-close" onclick="closeEditLoanModal()">&times;</button>
</div>

<form method="POST" action="index.php?url=loans/update" id="editLoanForm">
<input type="hidden" name="id" id="edit_loan_id">

<div class="loan-form-grid">

<div class="form-group">
<label>Borrower</label>
<input type="text" id="edit_borrower" readonly>
</div>

<div class="form-group">
<label>Loan Category</label>
<input type="text" id="edit_category" readonly>
</div>

<div class="form-group">
<label>Principal Amount</label>
<input type="number" id="edit_principal_amount" name="principal_amount" min=".01" step=".01" required>
</div>

<div class="form-group">
<label>Interest Rate (%)</label>
<input type="number" id="edit_interest_rate" name="interest_rate" min="0" step=".01" required>
</div>

<div class="form-group">
<label>Interest Type</label>
<select id="edit_interest_type" name="interest_type" required>
<option value="flat">Flat</option>
<option value="reducing_balance">Reducing Balance</option>
</select>
</div>

<div class="form-group">
<label>Payment Type</label>
<select id="edit_payment_type" name="payment_type" required>
<option value="installment">Installment</option>
<option value="full_payment">Full Payment</option>
</select>
</div>

<div class="form-group">
<label>Term</label>
<input type="number" id="edit_term" name="term" min="1" required>
</div>

<div class="form-group">
<label>Term Period</label>
<select id="edit_term_period" name="term_period" required>
<option value="days">Days</option>
<option value="weeks">Weeks</option>
<option value="every_15_days">Every 15 Days</option>
<option value="months">Months</option>
<option value="years">Years</option>
</select>
</div>

<div class="form-group">
<label>Processing Fee</label>
<input type="number" id="edit_processing_fee" name="processing_fee" min="0" step=".01">
</div>

<div class="form-group">
<label>Release Date</label>
<input type="date" id="edit_release_date" name="release_date">
</div>

<div class="form-group">
<label>First Payment Date</label>
<input type="date" id="edit_first_payment_date" name="first_payment_date">
</div>

<div class="form-group loan-form-full">
<label>Purpose</label>
<input type="text" id="edit_purpose" name="purpose" maxlength="255">
</div>

<div class="form-group loan-form-full">
<label>Notes</label>
<textarea id="edit_notes" name="notes" rows="4"></textarea>
</div>

</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" onclick="closeEditLoanModal()">Cancel</button>
<button type="submit" class="btn btn-primary">Save Changes</button>
</div>
</form>
</div>
</div>

<!-- CREATE LOAN MODAL -->

<div class="modal-overlay" id="createLoanModal" onclick="closeCreateLoanModal(event)">
<div class="modal" onclick="event.stopPropagation()">

<div class="modal-header">
<div>
<h2>Create Loan</h2>
<p>Create a new borrower loan.</p>
</div>
<button type="button" class="modal-close" onclick="closeCreateLoanModal()">&times;</button>
</div>

<form method="POST" action="index.php?url=loans/store" id="createLoanForm">

<div class="loan-form-grid">

<div class="form-group">
<label>Borrower</label>
<select id="borrower_id" name="borrower_id" required>
<option value="">Select Borrower</option>
<?php foreach($borrowers as $borrower):
$id=(int)($borrower['id']??0);
$name=$borrower['full_name']??trim(($borrower['first_name']??'').' '.($borrower['middle_name']??'').' '.($borrower['last_name']??''));
$name=$name?:($borrower['name']??'Borrower');
?>
<option value="<?=$id?>"><?=htmlspecialchars($name)?></option>
<?php endforeach; ?>
</select>
</div>

<div class="form-group">
<label>Loan Category</label>
<select id="category_id" name="category_id">
<option value="">Select Category</option>
<?php foreach($categories as $category):
$id=(int)($category['id']??0);
$name=$category['name']??$category['category_name']??'Category';
?>
<option value="<?=$id?>"><?=htmlspecialchars($name)?></option>
<?php endforeach; ?>
</select>
</div>

<div class="form-group">
<label>Account</label>
<select id="account_id" name="account_id" required>
<option value="">Select Account</option>
<?php foreach($accounts as $account):
$id=(int)($account['id']??0);
$name=$account['account_name']??'Account';
$balance=(float)($account['balance']??0);
?>
<option value="<?=$id?>" data-balance="<?=$balance?>"><?=htmlspecialchars($name)?> - ₱<?=number_format($balance,2)?></option>
<?php endforeach; ?>
</select>
<span class="account-balance-hint" id="accountBalanceHint">Select an account.</span>
</div>

<div class="form-group">
<label>Principal Amount</label>
<input type="number" id="principal_amount" name="principal_amount" min=".01" step=".01" required>
</div>

<div class="form-group">
<label>Interest Rate (%)</label>
<input type="number" id="interest_rate" name="interest_rate" min="0" step=".01" value="0.00" required>
</div>

<div class="form-group">
<label>Interest Type</label>
<select id="interest_type" name="interest_type" required>
<option value="flat">Flat</option>
<option value="reducing_balance">Reducing Balance</option>
</select>
</div>

<div class="form-group">
<label>Payment Type</label>
<select id="payment_type" name="payment_type" required>
<option value="installment">Installment</option>
<option value="full_payment">Full Payment</option>
</select>
</div>

<div class="form-group">
    <label>Term</label>
    <input type="number" id="term" name="term" min="1" value="1" required>
</div>

<div class="form-group">
<label>Term Period</label>
<select id="term_period" name="term_period" required>
<option value="days">Days</option>
<option value="weeks">Weeks</option>
<option value="every_15_days">Every 15 Days</option>
<option value="months" selected>Months</option>
<option value="years">Years</option>
</select>
</div>

<div class="form-group">
<label>Processing Fee</label>
<input type="number" id="processing_fee" name="processing_fee" min="0" step=".01" value="0.00">
</div>

<div class="form-group">
<label>Release Date</label>
<input type="date" id="release_date" name="release_date">
</div>

<div class="form-group">
<label>First Payment Date</label>
<input type="date" id="first_payment_date" name="first_payment_date">
<span class="account-balance-hint">Leave blank to calculate automatically.</span>
</div>

<div class="form-group loan-form-full">
<label>Purpose</label>
<input type="text" id="purpose" name="purpose" maxlength="255" placeholder="Example: Business capital">
</div>

<div class="form-group loan-form-full">
<label>Notes</label>
<textarea id="notes" name="notes" rows="4" placeholder="Additional notes..."></textarea>
</div>

</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" onclick="closeCreateLoanModal()">Cancel</button>
<button type="submit" class="btn btn-primary">Create Loan</button>
</div>

</form>
</div>
</div>

<!-- LOAN DETAILS MODAL -->

<div class="modal-overlay" id="loanDetailsModal" onclick="closeLoanDetails(event)">
<div class="modal" onclick="event.stopPropagation()">

<div class="modal-header">
<div>
<h2>Loan Details</h2>
<p>Complete information about this loan.</p>
</div>
<button type="button" class="modal-close" onclick="closeLoanDetails()">&times;</button>
</div>

<div class="loan-details-grid">

<?php
$details=[
'loan_number'=>'Loan Number','borrower'=>'Borrower','category'=>'Category',
'payment_method'=>'Payment Type','status'=>'Status','principal'=>'Principal Amount',
'interest_rate'=>'Interest Rate','interest_type'=>'Interest Type','term'=>'Term',
'due_date'=>'Due Date','processing_fee'=>'Processing Fee','total_interest'=>'Total Interest',
'total_payable'=>'Total Payable','release_date'=>'Release Date',
'first_payment_date'=>'First Payment Date','purpose'=>'Purpose','notes'=>'Notes'
];
foreach($details as $id=>$label):
?>

<div class="loan-detail-item <?=$id==='notes'?'loan-detail-full':''?>">
<span class="loan-detail-label"><?=$label?></span>
<span class="loan-detail-value" id="detail_<?=$id?>">-</span>
</div>

<?php endforeach; ?>

<div class="loan-detail-item loan-detail-full">
<span class="loan-detail-label">Payment Schedule</span>
<div class="loan-payment-schedule" id="detail_payment_schedule">
<span class="loan-payment-schedule-empty">No payment schedule available.</span>
</div>
</div>

</div>

<div class="modal-footer">

<form method="POST" action="index.php?url=loans/approve" id="approveLoanForm">
<input type="hidden" name="id" id="approve_loan_id">
<button type="submit" id="approveLoanButton" class="loan-decision-button loan-decision-approve">✓ Approve Loan</button>
</form>

<form method="POST" action="index.php?url=loans/reject" id="rejectLoanForm">
<input type="hidden" name="id" id="reject_loan_id">
<button type="submit" id="rejectLoanButton" class="loan-decision-button loan-decision-reject">✕ Reject Loan</button>
</form>

<button type="button" class="btn btn-secondary" onclick="closeLoanDetails()">Close</button>
</div>

</div>
</div>

<!-- PENALTY MODAL -->

<div class="modal-overlay" id="penaltyModal" onclick="closePenaltyModal(event)">
<div class="modal modal-small" onclick="event.stopPropagation()">

<div class="modal-header">
<div>
<h2>Apply Penalty</h2>
<p>Apply a penalty to this loan.</p>
</div>
<button type="button" class="modal-close" onclick="closePenaltyModal()">&times;</button>
</div>

<div class="penalty-summary">
<div class="penalty-summary-grid">

<div class="penalty-summary-item">
<span class="penalty-summary-label">Loan Number</span>
<span class="penalty-summary-value" id="penalty_loan_number">-</span>
</div>

<div class="penalty-summary-item">
<span class="penalty-summary-label">Borrower</span>
<span class="penalty-summary-value" id="penalty_borrower">-</span>
</div>

<div class="penalty-summary-item">
<span class="penalty-summary-label">Loan Payable</span>
<span class="penalty-summary-value" id="penalty_loan_payable">₱0.00</span>
</div>

<div class="penalty-summary-item">
<span class="penalty-summary-label">Schedule ID</span>
<span class="penalty-summary-value" id="penalty_schedule_display">Not selected</span>
</div>

</div>
</div>

<form method="POST" action="index.php?url=loans/penalty" id="penaltyForm">

<input type="hidden" name="loan_id" id="penalty_loan_id">

<div class="loan-form-grid">

<div class="form-group loan-form-full">
<label>Schedule ID</label>
<input type="number" name="schedule_id" id="penalty_schedule_id" min="1" required placeholder="Enter schedule ID">
<span class="penalty-calculation-hint">The penalty must be attached to a specific loan schedule.</span>
</div>

<div class="form-group">
<label>Penalty Type</label>
<select name="penalty_type" id="penalty_type" required>
<option value="fixed">Fixed Amount</option>
<option value="percentage">Percentage</option>
<option value="daily_fixed">Daily Fixed</option>
<option value="daily_percentage">Daily Percentage</option>
</select>
</div>

<div class="form-group">
<label>Rate / Amount</label>
<input type="number" name="penalty_rate" id="penalty_rate" min="0" step=".01" value="0" required>
<span class="penalty-calculation-hint" id="penaltyRateHint">Enter the fixed penalty amount.</span>
</div>

<div class="form-group">
<label>Base Amount</label>
<input type="number" name="base_amount" id="penalty_base_amount" min="0" step=".01" readonly>
</div>

<div class="form-group">
<label>Penalty Amount</label>
<input type="number" name="penalty_amount" id="penalty_amount" min=".01" step=".01" required>
</div>

<div class="form-group loan-form-full">
<label>Reason</label>
<textarea name="reason" id="penalty_reason" rows="3" required placeholder="Enter reason for penalty..."></textarea>
</div>

</div>

<div class="penalty-total-box">
<div class="penalty-total-row">
<span class="penalty-total-label">Calculated Penalty</span>
<span class="penalty-total-value" id="penalty_total_display">₱0.00</span>
</div>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" onclick="closePenaltyModal()">Cancel</button>
<button type="submit" class="btn btn-primary">Apply Penalty</button>
</div>

</form>
</div>
</div>

<script>
function formatPaymentMethod(v){
    v=String(v||'installment').toLowerCase();
    return v==='full_payment'?'Full Payment':v==='installment'?'Installment':formatText(v);
}

function formatMoney(v){
    return '₱'+Number(v||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
}

function formatText(v){
    if(!v)return'-';
    return String(v).replace(/_/g,' ').replace(/\b\w/g,x=>x.toUpperCase());
}

function escapeHtml(v){
    return String(v??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

function createStatusBadge(v){
    v=String(v||'pending').toLowerCase().trim();
    return `<span class="loan-status loan-status-${escapeHtml(v)}">${escapeHtml(formatText(v))}</span>`;
}

function openCreateLoanModal(){document.getElementById('createLoanModal')?.classList.add('active')}

function closeCreateLoanModal(e){
    if(e&&e.target!==e.currentTarget)return;
    document.getElementById('createLoanModal')?.classList.remove('active');
}

function closeEditLoanModal(e){
    if(e&&e.target!==e.currentTarget)return;
    document.getElementById('editLoanModal')?.classList.remove('active');
}

function closeLoanDetails(e){
    if(e&&e.target!==e.currentTarget)return;
    document.getElementById('loanDetailsModal')?.classList.remove('active');
}

function closePenaltyModal(e){
    if(e&&e.target!==e.currentTarget)return;
    document.getElementById('penaltyModal')?.classList.remove('active');
}

function toggleLoanActions(id){
    const dropdown=document.getElementById('loan-actions-'+id);
    if(!dropdown)return;

    const menu=dropdown.closest('.loan-action-menu');
    const button=menu?.querySelector('.loan-action-button');
    const open=dropdown.classList.contains('active');

    closeLoanActions();

    if(!open){
        dropdown.classList.add('active');
        button?.setAttribute('aria-expanded','true');
    }
}

function closeLoanActions(){
    document.querySelectorAll('.loan-action-dropdown.active').forEach(d=>{
        d.classList.remove('active');
        d.closest('.loan-action-menu')?.querySelector('.loan-action-button')?.setAttribute('aria-expanded','false');
    });
}

document.addEventListener('click',e=>{
    if(!e.target.closest('.loan-action-menu'))closeLoanActions();
});

function openLoanEdit(id,borrower,category,principal,rate,interestType,term,termPeriod,paymentType,fee,releaseDate,firstDate,purpose,notes){
    const modal=document.getElementById('editLoanModal');
    if(!modal)return;

    const set=(id,v)=>{
        const e=document.getElementById(id);
        if(e)e.value=v??'';
    };

    set('edit_loan_id',id);
    set('edit_borrower',borrower);
    set('edit_category',category);
    set('edit_principal_amount',principal);
    set('edit_interest_rate',rate);
    set('edit_interest_type',interestType||'flat');
    set('edit_payment_type',paymentType||'installment');
    set('edit_term',term||1);
    set('edit_term_period',termPeriod||'months');
    set('edit_processing_fee',fee||0);
    set('edit_release_date',releaseDate);
    set('edit_first_payment_date',firstDate);
    set('edit_purpose',purpose);
    set('edit_notes',notes);

    modal.classList.add('active');
}

document.getElementById('editLoanForm')?.addEventListener('submit',e=>{
    if(!document.getElementById('edit_loan_id')?.value){
        e.preventDefault();
        alert('No loan selected.');
    }else if(!confirm('Are you sure you want to save these changes?')){
        e.preventDefault();
    }
});

const accountSelect=document.getElementById('account_id');
const principalInput=document.getElementById('principal_amount');
const accountBalanceHint=document.getElementById('accountBalanceHint');

function updateAccountBalanceHint(){
    if(!accountSelect||!accountBalanceHint)return;

    const option=accountSelect.options[accountSelect.selectedIndex];

    if(!option?.value){
        accountBalanceHint.textContent='Select an account to see its available balance.';
        return;
    }

    accountBalanceHint.textContent='Available balance: '+formatMoney(option.dataset.balance);
}

accountSelect?.addEventListener('change',updateAccountBalanceHint);

document.getElementById('createLoanForm')?.addEventListener('submit',e=>{
    const option=accountSelect?.options[accountSelect.selectedIndex];

    if(!option?.value){
        e.preventDefault();
        alert('Please select a disbursement account.');
        accountSelect?.focus();
        return;
    }

    const balance=Number(option.dataset.balance||0);
    const principal=Number(principalInput?.value||0);

    if(principal<=0){
        e.preventDefault();
        alert('Principal amount must be greater than zero.');
        principalInput?.focus();
    }else if(principal>balance){
        e.preventDefault();
        alert('Insufficient balance in the selected account.\n\nAvailable balance: '+formatMoney(balance)+'\nPrincipal amount: '+formatMoney(principal));
        principalInput?.focus();
    }
});

const releaseDateInput=document.getElementById('release_date');
const firstPaymentInput=document.getElementById('first_payment_date');
const termPeriodInput=document.getElementById('term_period');

function formatDateForInput(d){
    return [d.getFullYear(),String(d.getMonth()+1).padStart(2,'0'),String(d.getDate()).padStart(2,'0')].join('-');
}

function parseLocalDate(v){
    if(!v)return null;
    const p=String(v).split('-');
    if(p.length!==3)return null;
    const d=new Date(Number(p[0]),Number(p[1])-1,Number(p[2]));
    return isNaN(d.getTime())?null:d;
}

function addPaymentPeriod(date,period,amount=1){
    const d=new Date(date.getTime());
    amount=Math.max(Number(amount)||1,1);
    period=String(period||'months').toLowerCase();

    if(period==='days')d.setDate(d.getDate()+amount);
    else if(period==='every_15_days')d.setDate(d.getDate()+amount*15);
    else if(period==='weeks')d.setDate(d.getDate()+amount*7);
    else if(period==='years')d.setFullYear(d.getFullYear()+amount);
    else{
        const day=d.getDate();
        d.setDate(1);
        d.setMonth(d.getMonth()+amount);
        d.setDate(Math.min(day,new Date(d.getFullYear(),d.getMonth()+1,0).getDate()));
    }

    return d;
}

function formatDisplayDate(d){
    return d?d.toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}):'-';
}

function updateAutomaticFirstPaymentDate(){
    if(!releaseDateInput||!firstPaymentInput||!termPeriodInput||!releaseDateInput.value||firstPaymentInput.value)return;

    const d=parseLocalDate(releaseDateInput.value);

    if(d)firstPaymentInput.value=formatDateForInput(addPaymentPeriod(d,termPeriodInput.value,1));
}

releaseDateInput?.addEventListener('change',updateAutomaticFirstPaymentDate);
termPeriodInput?.addEventListener('change',updateAutomaticFirstPaymentDate);

function calculateFinalDueDate(firstPaymentDateValue,term,termPeriod){
    if(!firstPaymentDateValue)return null;

    const firstDate=parseLocalDate(firstPaymentDateValue);
    if(!firstDate)return null;

    return addPaymentPeriod(firstDate,termPeriod,Math.max(Number(term||1),1)-1);
}

function generatePaymentSchedule(releaseDateValue,firstPaymentDateValue,term,termPeriod,paymentType,totalPayable){
    const container=document.getElementById('detail_payment_schedule');
    if(!container)return;

    let firstDue=firstPaymentDateValue?parseLocalDate(firstPaymentDateValue):null;

    if(!firstDue&&releaseDateValue){
        const release=parseLocalDate(releaseDateValue);
        if(release)firstDue=addPaymentPeriod(release,termPeriod,1);
    }

    if(!firstDue){
        container.innerHTML='<span class="loan-payment-schedule-empty">Release date is required to calculate the due date.</span>';
        return;
    }

    if(String(paymentType).toLowerCase()==='full_payment'){
        container.innerHTML='<div class="loan-payment-schedule-title">Full Payment Due Date</div><ol class="loan-payment-schedule-list"><li><strong>'+escapeHtml(formatDisplayDate(firstDue))+'</strong> — '+formatMoney(totalPayable)+'</li></ol>';
        return;
    }

    const count=Math.max(Number(term||1),1);
    const amount=Number(totalPayable||0)/count;
    let html='<div class="loan-payment-schedule-title">'+count+' Installment Payment'+(count!==1?'s':'')+'</div><ol class="loan-payment-schedule-list">';

    for(let i=1;i<=count;i++){
        const due=i===1?new Date(firstDue):addPaymentPeriod(firstDue,termPeriod,i-1);
        html+='<li><strong>'+escapeHtml(formatDisplayDate(due))+'</strong> — '+formatMoney(amount)+'</li>';
    }

    container.innerHTML=html+'</ol>';
}

function openLoanDetails(id,loanNumber,borrower,category,principal,interestRate,interestType,term,termPeriod,paymentType,fee,totalInterest,totalPayable,releaseDate,firstPaymentDate,status,purpose,notes){
    const set=(id,v)=>{
        const e=document.getElementById(id);
        if(e)e.textContent=v;
    };

    set('detail_loan_number',loanNumber||'-');
    set('detail_borrower',borrower||'-');
    set('detail_category',category||'-');
    set('detail_payment_method',formatPaymentMethod(paymentType));
    set('detail_principal',formatMoney(principal));
    set('detail_interest_rate',Number(interestRate||0).toFixed(2)+'%');
    set('detail_interest_type',formatText(interestType));
    set('detail_term',Number(term||0)+' '+formatText(termPeriod));
    set('detail_processing_fee',formatMoney(fee));
    set('detail_total_interest',formatMoney(totalInterest));
    set('detail_total_payable',formatMoney(totalPayable));
    set('detail_purpose',purpose||'-');
    set('detail_notes',notes||'-');

    let firstDue=firstPaymentDate?parseLocalDate(firstPaymentDate):null;

    if(!firstDue&&releaseDate){
        const release=parseLocalDate(releaseDate);
        if(release)firstDue=addPaymentPeriod(release,termPeriod,1);
    }

    const due=firstDue?calculateFinalDueDate(firstPaymentDate||formatDateForInput(firstDue),term,termPeriod):null;
    const dueElement=document.getElementById('detail_due_date');

    if(dueElement){
        dueElement.innerHTML=due?'<span class="loan-due-date">'+escapeHtml(formatDisplayDate(due))+'</span>':'-';
    }

    set('detail_release_date',releaseDate?formatDisplayDate(parseLocalDate(releaseDate)):'-');
    set('detail_first_payment_date',firstDue?formatDisplayDate(firstDue):'-');

    const statusElement=document.getElementById('detail_status');

    if(statusElement)statusElement.innerHTML=createStatusBadge(status);

    generatePaymentSchedule(releaseDate,firstPaymentDate,term,termPeriod,paymentType,totalPayable);

    document.getElementById('approve_loan_id').value=id;
    document.getElementById('reject_loan_id').value=id;

    const canApprove=String(status||'pending').toLowerCase().trim()==='pending';

    ['approveLoanButton','rejectLoanButton'].forEach(buttonId=>{
        const button=document.getElementById(buttonId);

        if(button){
            button.disabled=!canApprove;
            button.classList.toggle('disabled',!canApprove);
        }
    });

    document.getElementById('loanDetailsModal')?.classList.add('active');
}

function openPenaltyModal(loanId,loanNumber,borrower,scheduleId,loanPayable){
    const modal=document.getElementById('penaltyModal');
    if(!modal)return;

    document.getElementById('penalty_loan_id').value=loanId||'';
    document.getElementById('penalty_loan_number').textContent=loanNumber||'-';
    document.getElementById('penalty_borrower').textContent=borrower||'-';
    document.getElementById('penalty_loan_payable').textContent=formatMoney(loanPayable);

    const scheduleInput=document.getElementById('penalty_schedule_id');
    const scheduleDisplay=document.getElementById('penalty_schedule_display');

    if(scheduleInput)scheduleInput.value=scheduleId||'';
    if(scheduleDisplay)scheduleDisplay.textContent=scheduleId?String(scheduleId):'Not selected';

    document.getElementById('penalty_base_amount').value=Number(loanPayable||0).toFixed(2);
    updatePenaltyCalculation();
    modal.classList.add('active');
}

const penaltyTypeInput=document.getElementById('penalty_type');
const penaltyRateInput=document.getElementById('penalty_rate');
const penaltyBaseInput=document.getElementById('penalty_base_amount');
const penaltyAmountInput=document.getElementById('penalty_amount');
const penaltyTotalDisplay=document.getElementById('penalty_total_display');
const penaltyRateHint=document.getElementById('penaltyRateHint');

function updatePenaltyCalculation(){
    if(!penaltyTypeInput||!penaltyRateInput||!penaltyBaseInput||!penaltyAmountInput)return;

    const type=String(penaltyTypeInput.value||'fixed').toLowerCase();
    const rate=Number(penaltyRateInput.value||0);
    const base=Number(penaltyBaseInput.value||0);
    let amount=0;

    if(type==='percentage'){
        amount=base*(rate/100);
        penaltyRateHint.textContent='Percentage applied to the base amount.';
    }else if(type==='daily_percentage'){
        amount=base*(rate/100);
        penaltyRateHint.textContent='Percentage penalty. Days overdue can be handled by the backend.';
    }else if(type==='daily_fixed'){
        amount=rate;
        penaltyRateHint.textContent='Fixed penalty amount per overdue day.';
    }else{
        amount=rate;
        penaltyRateHint.textContent='Enter the fixed penalty amount.';
    }

    amount=Math.max(Number.isFinite(amount)?amount:0,0);
    penaltyAmountInput.value=amount.toFixed(2);

    if(penaltyTotalDisplay)penaltyTotalDisplay.textContent=formatMoney(amount);
}

penaltyTypeInput?.addEventListener('change',updatePenaltyCalculation);
penaltyRateInput?.addEventListener('input',updatePenaltyCalculation);
penaltyBaseInput?.addEventListener('input',updatePenaltyCalculation);

document.getElementById('penaltyForm')?.addEventListener('submit',e=>{
    const loanId=document.getElementById('penalty_loan_id');
    const scheduleId=document.getElementById('penalty_schedule_id');
    const reason=document.getElementById('penalty_reason');
    const amount=document.getElementById('penalty_amount');

    if(!loanId?.value){
        e.preventDefault();
        alert('No loan selected.');
        return;
    }

    if(!scheduleId?.value){
        e.preventDefault();
        alert('Please enter the loan schedule ID.');
        scheduleId?.focus();
        return;
    }

    if(!reason?.value.trim()){
        e.preventDefault();
        alert('Please enter the reason for the penalty.');
        reason?.focus();
        return;
    }

    if(!amount?.value||Number(amount.value)<=0){
        e.preventDefault();
        alert('Penalty amount must be greater than zero.');
        return;
    }

    if(!confirm('Are you sure you want to apply this penalty?'))e.preventDefault();
});

document.getElementById('approveLoanForm')?.addEventListener('submit',e=>{
    if(!document.getElementById('approve_loan_id')?.value){
        e.preventDefault();
        alert('No loan selected.');
    }else if(!confirm('Are you sure you want to approve this loan?\n\nOnce approved, the loan will proceed to the next stage.')){
        e.preventDefault();
    }
});

document.getElementById('rejectLoanForm')?.addEventListener('submit',e=>{
    if(!document.getElementById('reject_loan_id')?.value){
        e.preventDefault();
        alert('No loan selected.');
    }else if(!confirm('Are you sure you want to reject this loan?\n\nThis action cannot be easily undone.')){
        e.preventDefault();
    }
});

document.addEventListener('keydown',e=>{
    if(e.key==='Escape'){
        closeCreateLoanModal();
        closeEditLoanModal();
        closeLoanDetails();
        closePenaltyModal();
        closeLoanActions();
    }
});

updateAutomaticFirstPaymentDate();
updateAccountBalanceHint();
</script>

</body>
</html>