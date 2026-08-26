
<?php
$penalties=$penalties??[];
$totalPenalties=(float)($totalPenalties??0);
$thisMonthPenalties=(float)($thisMonthPenalties??0);
$penaltyCount=(int)($penaltyCount??0);

function penaltyMoney(float $amount):string
{
    return '₱'.number_format($amount,2);
}

function penaltyTypeLabel(?string $type):string
{
    return match($type){
        'fixed'=>'Fixed',
        'percentage'=>'Percentage',
        default=>ucfirst(str_replace('_',' ',$type??''))
    };
}

function penaltyBaseLabel(?string $base):string
{
    return match($base){
        'principal'=>'Principal',
        'total_due'=>'Total Due',
        'overdue_amount'=>'Overdue Amount',
        default=>ucfirst(str_replace('_',' ',$base??''))
    };
}

function penaltyStatusClass(?string $status):string
{
    return match($status){
        'overdue'=>'status-overdue',
        'partial'=>'status-partial',
        'paid'=>'status-paid',
        default=>'status-pending'
    };
}

function escapeHtml($value):string
{
    return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');
}
?>

<style>
.penalty-page{padding:24px}
.penalty-header{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:24px}
.penalty-header-left h1{margin:0;font-size:26px;font-weight:700}
.penalty-header-left p{margin:6px 0 0;color:#6b7280;font-size:14px}
.penalty-primary-btn{display:inline-flex;align-items:center;gap:8px;padding:11px 16px;border:none;border-radius:8px;background:#111827;color:#fff;text-decoration:none;font-size:14px;font-weight:600;cursor:pointer;transition:background .2s ease,transform .2s ease}
.penalty-primary-btn:hover{background:#000;transform:translateY(-1px)}

.penalty-stat-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-bottom:24px}
.penalty-stat-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.penalty-stat-label{font-size:13px;color:#6b7280;margin-bottom:8px}
.penalty-stat-value{font-size:25px;font-weight:700;color:#111827}

.penalty-table-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.penalty-table-header{padding:18px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center}
.penalty-table-title{font-size:16px;font-weight:700;color:#111827}
.penalty-table-wrapper{width:100%;overflow-x:auto}
.penalty-table{width:100%;border-collapse:collapse;min-width:1050px}
.penalty-table th{background:#f9fafb;color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;padding:13px 16px;text-align:left;white-space:nowrap;border-bottom:1px solid #e5e7eb}
.penalty-table td{padding:15px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.penalty-table tbody tr:hover{background:#fafafa}
.penalty-table tbody tr:last-child td{border-bottom:none}

.penalty-loan-number{font-weight:700;color:#111827}
.penalty-borrower{font-weight:600;color:#111827}
.penalty-muted{color:#9ca3af}
.penalty-money{font-weight:700;color:#111827}
.penalty-type{display:inline-flex;padding:5px 9px;border-radius:6px;font-size:11px;font-weight:600;background:#f3f4f6;color:#374151;white-space:nowrap}
.penalty-status{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:11px;font-weight:600}
.status-overdue{background:#fef2f2;color:#b91c1c}
.status-partial{background:#fffbeb;color:#b45309}
.status-paid{background:#ecfdf5;color:#047857}
.status-pending{background:#f3f4f6;color:#4b5563}

.penalty-action{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid #e5e7eb;border-radius:7px;color:#374151;text-decoration:none;transition:background .2s ease,border-color .2s ease}
.penalty-action:hover{background:#f3f4f6;border-color:#d1d5db}

.penalty-empty{padding:60px 20px;text-align:center}
.penalty-empty-icon{width:50px;height:50px;margin:0 auto 14px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:22px;color:#6b7280}
.penalty-empty h3{margin:0 0 6px;font-size:16px;color:#111827}
.penalty-empty p{margin:0;font-size:13px;color:#9ca3af}

@media(max-width:900px){
    .penalty-stat-grid{grid-template-columns:1fr}
    .penalty-header{align-items:flex-start;flex-direction:column}
    .penalty-page{padding:16px}
}
</style>

<div class="penalty-page">

    <div class="penalty-header">
        <div class="penalty-header-left">
            <h1>Penalties</h1>
            <p>View and manage loan penalties.</p>
        </div>

        <div>
            <a href="index.php?url=penalties/create" class="penalty-primary-btn">
                <span>+</span>
                Add Penalty
            </a>
        </div>
    </div>

    <div class="penalty-stat-grid">

        <div class="penalty-stat-card">
            <div class="penalty-stat-label">Total Penalties</div>
            <div class="penalty-stat-value">
                <?= penaltyMoney($totalPenalties) ?>
            </div>
        </div>

        <div class="penalty-stat-card">
            <div class="penalty-stat-label">This Month</div>
            <div class="penalty-stat-value">
                <?= penaltyMoney($thisMonthPenalties) ?>
            </div>
        </div>

        <div class="penalty-stat-card">
            <div class="penalty-stat-label">Penalty Records</div>
            <div class="penalty-stat-value">
                <?= number_format($penaltyCount) ?>
            </div>
        </div>

    </div>

    <div class="penalty-table-card">

        <div class="penalty-table-header">
            <div class="penalty-table-title">Penalty Records</div>
        </div>

        <?php if(empty($penalties)): ?>

            <div class="penalty-empty">
                <div class="penalty-empty-icon">₱</div>
                <h3>No penalties found</h3>
                <p>There are currently no penalty records for this business.</p>
            </div>

        <?php else: ?>

            <div class="penalty-table-wrapper">

                <table class="penalty-table">

                    <thead>
                        <tr>
                            <th>Loan</th>
                            <th>Borrower</th>
                            <th>Installment</th>
                            <th>Due Date</th>
                            <th>Type</th>
                            <th>Base</th>
                            <th>Rate</th>
                            <th>Base Amount</th>
                            <th>Penalty</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach($penalties as $penalty): ?>

                            <?php
                            $dueDate=$penalty['due_date']??null;
                            $createdAt=$penalty['created_at']??null;
                            $rate=(float)($penalty['rate']??0);
                            $penaltyId=(int)($penalty['id']??0);
                            $scheduleStatus=$penalty['schedule_status']??'pending';
                            $penaltyType=$penalty['penalty_type']??null;
                            ?>

                            <tr>

                                <td>
                                    <div class="penalty-loan-number">
                                        <?= escapeHtml($penalty['loan_number']??'-') ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="penalty-borrower">
                                        <?= escapeHtml($penalty['borrower_name']??'-') ?>
                                    </div>
                                </td>

                                <td>
                                    <?php if(isset($penalty['installment_number'])): ?>
                                        #<?= escapeHtml($penalty['installment_number']) ?>
                                    <?php else: ?>
                                        <span class="penalty-muted">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($dueDate): ?>
                                        <?= date('M d, Y',strtotime($dueDate)) ?>
                                    <?php else: ?>
                                        <span class="penalty-muted">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="penalty-type">
                                        <?= escapeHtml(penaltyTypeLabel($penaltyType)) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= escapeHtml(
                                        penaltyBaseLabel(
                                            $penalty['penalty_base']??null
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?php if($penaltyType==='percentage'): ?>
                                        <?= number_format($rate,2) ?>%
                                    <?php else: ?>
                                        <?= penaltyMoney($rate) ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= penaltyMoney(
                                        (float)($penalty['base_amount']??0)
                                    ) ?>
                                </td>

                                <td>
                                    <span class="penalty-money">
                                        <?= penaltyMoney(
                                            (float)($penalty['penalty_amount']??0)
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="penalty-status <?= escapeHtml(
                                        penaltyStatusClass($scheduleStatus)
                                    ) ?>">
                                        <?= escapeHtml(ucfirst($scheduleStatus)) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if($createdAt): ?>
                                        <?= date('M d, Y',strtotime($createdAt)) ?>
                                    <?php else: ?>
                                        <span class="penalty-muted">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a
                                        href="index.php?url=penalties/view&id=<?= $penaltyId ?>"
                                        class="penalty-action"
                                        title="View penalty"
                                    >👁</a>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>
`