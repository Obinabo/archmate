<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "../phpmailer/mailer.php";

$title = 'Withdraw | ' . SITE_NAME;
$pageTitle = 'Withdrawals';
$extraStyles = ['assets/css/account-pages.css'];

if (!isset($_SESSION['email'])) {
    redirect('./affiliate');
}

$email = $_SESSION['email'];
$stmt = mysqli_prepare($con, "SELECT * FROM affiliate WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$verified = $row['verified'] ?? '';
$paid = $row['paid'] ?? '';
$bal = (float)($row['balance'] ?? 0);

if ($verified === 'UNVERIFIED') {
    redirect('./affiliate');
} elseif ($verified === 'VERIFIED' && $paid === 'UNPAID') {
    redirect('./payment-status');
}

$msg = [];
$successmsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $amount = trim($_POST['amount'] ?? '');

    if ($amount === '' || !is_numeric($amount)) {
        $msg[] = '<div class="error">Please enter a valid withdrawal amount.</div>';
    } else {
        $amount = (int)$amount;
    }

    if (empty($msg) && $amount > $bal) {
        $msg[] = '<div class="error">Sorry, you have insufficient balance.</div>';
    }
    if (empty($msg) && $amount < 1000) {
        $msg[] = '<div class="error">The minimum withdrawal amount is &#8358;1,000.</div>';
    }

    if (empty($msg)) {
        $status = 'PENDING';
        $date = date("d-m-Y");
        if (withdraw($con, $email, $amount, $status, $date) === TRUE) {
            $subject = 'Verify Property Sale by User';
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
                      <p>Hello Admin,</p>
                      <p>A user has made a withdrawal request. Please review it in the admin dashboard and approve or reject the request.</p>
                    </div>
                  </div>
                </div>
              </body>
            </html>';
            sendEmail(SITE_EMAIL_2, $subject, $mail);
            $successmsg = '<div class="success">Your withdrawal request has been sent and is being processed.</div>';
        } else {
            $msg[] = '<div class="error">Failed to create request.</div>';
        }
    }
}

$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$stmtWithdraw = mysqli_prepare($con, "SELECT * FROM withdraw WHERE email = ? ORDER BY id DESC LIMIT $limit OFFSET $offset");
mysqli_stmt_bind_param($stmtWithdraw, 's', $email);
mysqli_stmt_execute($stmtWithdraw);
$result = mysqli_stmt_get_result($stmtWithdraw);
$pageRows = $result ? mysqli_num_rows($result) : 0;

$stmtCount = mysqli_prepare($con, "SELECT COUNT(*) AS total FROM withdraw WHERE email = ?");
mysqli_stmt_bind_param($stmtCount, 's', $email);
mysqli_stmt_execute($stmtCount);
$countRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCount));
$withdrawal_numrows = (int)($countRow['total'] ?? 0);

include "includes/header2.php";
include "includes/nav-menu.php";
?>

<div class="account-shell">
  <section class="account-hero">
    <div class="section-label"><div class="section-label-line"></div><span>Withdrawals</span></div>
    <h1 class="account-hero-title">Manage your <em>payout requests</em></h1>
    <p class="account-hero-sub">Track previous requests, review your current balance, and submit a new withdrawal when you are ready.</p>
  </section>

  <?php foreach ($msg as $m) echo $m; ?>
  <?php if ($successmsg) echo $successmsg; ?>

  <div class="account-grid">
    <section class="account-panel" style="grid-column: span 8;">
      <div class="account-panel-head">
        <div>
          <h3>Withdrawal history</h3>
          <span>You have made <?php echo $withdrawal_numrows; ?> request(s) in total.</span>
        </div>
      </div>

      <div class="account-table-wrap">
        <table class="dash-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($withdrawal_numrows > 0): ?>
              <?php while ($row2 = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row2['date']); ?></td>
                  <td>&#8358;<?php echo number_format((float)$row2['amount']); ?></td>
                  <td><span class="dash-badge dash-badge-<?php echo strtolower($row2['status']); ?>"><?php echo htmlspecialchars($row2['status']); ?></span></td>
                  <td><a class="dash-action-del" href="./delete-withdraw?id=<?php echo urlencode($row2['id']); ?>">Delete</a></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="4" class="dash-empty">No withdrawals yet. Submit your first request when ready.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php
      $totalPages = max(1, (int)ceil($withdrawal_numrows / $limit));
      if ($totalPages > 1) {
          echo '<div class="account-note">Page ';
          for ($i = 1; $i <= $totalPages; $i++) {
              echo '<a class="auth-link" style="margin-right:0.75rem" href="?page=' . $i . '">' . $i . '</a>';
          }
          echo '</div>';
      }
      ?>
    </section>

    <aside class="account-panel account-panel-dark" style="grid-column: span 4;">
      <div class="account-panel-head">
        <div>
          <h3>Request payout</h3>
          <span>Minimum withdrawal: &#8358;1,000</span>
        </div>
      </div>

      <div class="account-bank">
        <div class="account-bank-row"><span>Available balance</span><strong>&#8358;<?php echo number_format($bal); ?></strong></div>
        <div class="account-bank-row"><span>Account status</span><strong><?php echo htmlspecialchars($verified ?: 'UNVERIFIED'); ?></strong></div>
        <div class="account-bank-row"><span>Payment state</span><strong><?php echo htmlspecialchars($paid ?: 'UNPAID'); ?></strong></div>
      </div>

      <div class="account-divider" style="background:rgba(255,255,255,.12);"></div>

      <form method="POST" class="auth-form">
        <label class="auth-field">
          <span>Amount to withdraw</span>
          <input type="number" name="amount" placeholder="E.g. 5000" required />
        </label>
        <button type="submit" name="submit" class="btn-primary auth-submit">Send Request</button>
      </form>
    </aside>
  </div>
</div>

<?php include "includes/footer2.php"; ?>
