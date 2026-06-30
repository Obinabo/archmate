<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";
include_once "../phpmailer/mailer.php";

if (!isset($_SESSION['id'])) {
    redirect('index.php');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id) {
  redirect('index.php');
}

$title = 'Withdraw Review | ' . SITE_NAME;
include "includes/head.php";

$q = "SELECT * FROM withdraw WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($con, $q);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = $result ? mysqli_fetch_assoc($result) : null;

if (!$row) {
    echo '<div class="adm-alert adm-alert--error">Withdrawal request not found.</div>';
    include "includes/foot.php";
    exit;
}

$amount = (float)$row['amount'];
$email = $row['email'];

$qAccount = "SELECT * FROM affiliate WHERE email = ? LIMIT 1";
$stmtAccount = mysqli_prepare($con, $qAccount);
mysqli_stmt_bind_param($stmtAccount, 's', $email);
mysqli_stmt_execute($stmtAccount);
$resultAccount = mysqli_stmt_get_result($stmtAccount);
$row2 = $resultAccount ? mysqli_fetch_assoc($resultAccount) : null;

$currentbalance = (float)($row2['balance'] ?? 0);
$fname = $row2['fname'] ?? '';
$acct_no = $row2['acct_no'] ?? '';
$bank = $row2['bank'] ?? '';
$withdrawal = (int)($row2['withdrawal'] ?? 0);

$updatedwithdrawal = $withdrawal + $amount;
$updatedbalance = $currentbalance - $amount;

$msg = [];
$successmsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $status = trim($_POST['status'] ?? '');

    if ($status === '') {
        $msg[] = '<div class="adm-alert adm-alert--error">Please select a new status.</div>';
    } elseif (!in_array($status, ['APPROVED', 'REJECTED'], true)) {
        $msg[] = '<div class="adm-alert adm-alert--error">Please choose Approved or Rejected.</div>';
    }

    if (empty($msg)) {
        if ($status === 'APPROVED') {
            $qUpdate = "UPDATE withdraw SET status = ? WHERE id = ? LIMIT 1";
            $stmtUpdate = mysqli_prepare($con, $qUpdate);
            mysqli_stmt_bind_param($stmtUpdate, 'si', $status, $id);
            mysqli_stmt_execute($stmtUpdate);

            if (mysqli_stmt_affected_rows($stmtUpdate) === 1) {
                $qAccountUpdate = "UPDATE affiliate SET withdrawal = ?, balance = ? WHERE email = ? LIMIT 1";
                $stmtAccountUpdate = mysqli_prepare($con, $qAccountUpdate);
                mysqli_stmt_bind_param($stmtAccountUpdate, 'iis', $updatedwithdrawal, $updatedbalance, $email);
                mysqli_stmt_execute($stmtAccountUpdate);

                $subject = 'Update on your withdrawal request';
                $mail = '
                <html>
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                      body{font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.6;margin:0;background:#f7fbfd;color:#0b1f36}
                      .wrap{max-width:620px;margin:0 auto;padding:32px 20px}
                      .card{background:#fff;border:1px solid #dfeaf0;border-radius:18px;overflow:hidden}
                      .head{background:#071524;color:#e8f5fa;padding:24px 28px}
                      .body{padding:28px}
                    </style>
                  </head>
                  <body>
                    <div class="wrap">
                      <div class="card">
                        <div class="head"><strong>' . SITE_NAME . '</strong></div>
                        <div class="body">
                          <p>Hello ' . htmlspecialchars($fname) . ',</p>
                          <p>Your withdrawal request has been approved and processed. Kindly log in to your dashboard to view the updated balance.</p>
                        </div>
                      </div>
                    </div>
                  </body>
                </html>';
                sendEmail($email, $subject, $mail);

                $successmsg = '<div class="adm-alert adm-alert--success">Withdrawal approved. The account has been debited.</div>';
            } else {
                $msg[] = '<div class="adm-alert adm-alert--error">No change made to account.</div>';
            }
        } else {
            $qUpdate = "UPDATE withdraw SET status = ? WHERE id = ? LIMIT 1";
            $stmtUpdate = mysqli_prepare($con, $qUpdate);
            mysqli_stmt_bind_param($stmtUpdate, 'si', $status, $id);
            mysqli_stmt_execute($stmtUpdate);

            if (mysqli_stmt_affected_rows($stmtUpdate) === 1) {
                $subject = 'Update on your withdrawal request';
                $mail = '
                <html>
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                      body{font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.6;margin:0;background:#f7fbfd;color:#0b1f36}
                      .wrap{max-width:620px;margin:0 auto;padding:32px 20px}
                      .card{background:#fff;border:1px solid #dfeaf0;border-radius:18px;overflow:hidden}
                      .head{background:#071524;color:#e8f5fa;padding:24px 28px}
                      .body{padding:28px}
                    </style>
                  </head>
                  <body>
                    <div class="wrap">
                      <div class="card">
                        <div class="head"><strong>' . SITE_NAME . '</strong></div>
                        <div class="body">
                          <p>Hello ' . htmlspecialchars($fname) . ',</p>
                          <p>We have reviewed your withdrawal request and it was rejected. Please contact support if you need help.</p>
                        </div>
                      </div>
                    </div>
                  </body>
                </html>';
                sendEmail($email, $subject, $mail);

                $successmsg = '<div class="adm-alert adm-alert--success">Withdrawal rejected. The realtor has been notified.</div>';
            } else {
                $msg[] = '<div class="adm-alert adm-alert--error">No change made to account.</div>';
            }
        }
    }
}

$pendingStat = 'PENDING';
$qPending = "SELECT COUNT(*) AS total FROM withdraw WHERE status = ?";
$stmtPending = mysqli_prepare($con, $qPending);
mysqli_stmt_bind_param($stmtPending, 's', $pendingStat);
mysqli_stmt_execute($stmtPending);
$resultPending = mysqli_stmt_get_result($stmtPending);
$pendingRow = $resultPending ? mysqli_fetch_assoc($resultPending) : [];
$pendingTotal = (int)($pendingRow['total'] ?? 0);
?>

<div class="adm-pagehead">
  <div>
    <div class="adm-eyebrow">Payouts</div>
    <h1 class="adm-pagehead__title">Review <em>withdrawal</em> request</h1>
    <p class="adm-pagehead__lede">Check the request details, confirm the balance impact, and then approve or reject the payout.</p>
  </div>
  <a href="./withdraw" class="adm-btn adm-btn--ghost">Back to withdrawals</a>
</div>

<?php if ($msg) { echo '<div class="adm-formmsg">'; foreach ($msg as $item) echo $item; echo '</div>'; } ?>
<?php if ($successmsg) echo $successmsg; ?>

<div class="adm-kpis">
  <div class="adm-kpi">
    <div class="adm-kpi__label">Request amount</div>
    <div class="adm-kpi__value">&#8358;<?php echo number_format($amount); ?></div>
    <i class="fa-solid fa-money-bill-transfer adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi">
    <div class="adm-kpi__label">Current balance</div>
    <div class="adm-kpi__value">&#8358;<?php echo number_format($currentbalance); ?></div>
    <i class="fa-solid fa-wallet adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi adm-kpi--accent">
    <div class="adm-kpi__label">Pending queue</div>
    <div class="adm-kpi__value"><?php echo $pendingTotal; ?></div>
    <i class="fa-solid fa-clock-rotate-left adm-kpi__icon"></i>
  </div>
</div>

<div class="adm-grid-2">
  <section class="adm-card">
    <div class="adm-card__title">Request summary</div>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Field</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Email</td>
            <td><?php echo htmlspecialchars($email); ?></td>
          </tr>
          <tr>
            <td>Account name</td>
            <td><?php echo htmlspecialchars($fname ?: 'Unknown'); ?></td>
          </tr>
          <tr>
            <td>Account number</td>
            <td><?php echo htmlspecialchars($acct_no ?: 'Unknown'); ?></td>
          </tr>
          <tr>
            <td>Bank</td>
            <td><?php echo htmlspecialchars($bank ?: 'Unknown'); ?></td>
          </tr>
          <tr>
            <td>Status</td>
            <td><span class="adm-badge <?php echo strtolower((string)$row['status']) === 'pending' ? 'adm-badge--warn' : 'adm-badge--ok'; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
          </tr>
          <tr>
            <td>Amount</td>
            <td>&#8358;<?php echo number_format($amount); ?></td>
          </tr>
          <tr>
            <td>Updated balance if approved</td>
            <td>&#8358;<?php echo number_format($updatedbalance); ?></td>
          </tr>
          <tr>
            <td>Updated withdrawal total</td>
            <td><?php echo number_format($updatedwithdrawal); ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <aside class="adm-card adm-card--dark adm-card--note">
    <span class="adm-card__chip">Action Form</span>
    <h3>Process this request</h3>
    <p style="color:rgba(234,243,250,.8);margin:0 0 18px;line-height:1.6;">Set the final status only after confirming the payout and the updated account balance.</p>

    <form action="" method="POST" class="adm-form">
      <label class="adm-field">
        <span class="adm-field__label">Request amount</span>
        <div class="adm-field__wrap">
          <input type="text" value="<?php echo number_format($amount); ?>" disabled>
        </div>
      </label>

      <label class="adm-field">
        <span class="adm-field__label">Set status</span>
        <div class="adm-field__wrap adm-field__wrap--select">
          <select name="status" required>
            <option value="">Choose account status</option>
            <option value="APPROVED">APPROVED</option>
            <option value="REJECTED">REJECTED</option>
          </select>
        </div>
      </label>

      <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
      <button type="submit" name="submit" class="adm-btn adm-btn--primary adm-btn--block">Update Request</button>
    </form>
  </aside>
</div>

<?php include "includes/foot.php"; ?>
