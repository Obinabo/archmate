<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "../phpmailer/mailer.php";
include_once "includes/count.inc.php";

if (!isset($_SESSION['id'])) {
    redirect('index.php');
}

$title = 'Activate Accounts | ' . SITE_NAME;
include "includes/head.php";

$msg = [];
$successmsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['acct_name'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($id === '') {
        $msg[] = '<div class="adm-alert adm-alert--error">Please select an account.</div>';
    }
    if ($status === '') {
        $msg[] = '<div class="adm-alert adm-alert--error">Please select a status for the account.</div>';
    } elseif ($status === 'PENDING') {
        $msg[] = '<div class="adm-alert adm-alert--error">This account is already on pending status.</div>';
    }

    if (empty($msg)) {
        $q = "UPDATE affiliate SET paid = ? WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $q);
        mysqli_stmt_bind_param($stmt, 'ss', $status, $id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) === 1) {
            $successmsg = '<div class="adm-alert adm-alert--success">Account status updated successfully.</div>';

            $qAccount = "SELECT * FROM affiliate WHERE email = ? LIMIT 1";
            $stmtAccount = mysqli_prepare($con, $qAccount);
            mysqli_stmt_bind_param($stmtAccount, 's', $id);
            mysqli_stmt_execute($stmtAccount);
            $row3 = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtAccount));

            if ($row3 && !empty($row3['uplink'])) {
                $q4 = "SELECT * FROM affiliate WHERE ref_id = ? LIMIT 1";
                $stmt4 = mysqli_prepare($con, $q4);
                mysqli_stmt_bind_param($stmt4, 's', $row3['uplink']);
                mysqli_stmt_execute($stmt4);
                $row4 = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt4));

                if ($row4) {
                    $ref_email = $row4['email'];
                    $ref_uname = $row4['uname'];
                    $fname = $row3['fname'];
                    $ref_balance = 1000 + (int)$row4['balance'];
                    $ref_earnings = 1000 + (int)$row4['earnings'];

                    $q5 = "UPDATE affiliate SET earnings = ?, balance = ? WHERE email = ? LIMIT 1";
                    $stmt5 = mysqli_prepare($con, $q5);
                    mysqli_stmt_bind_param($stmt5, 'iis', $ref_earnings, $ref_balance, $ref_email);
                    mysqli_stmt_execute($stmt5);

                    if (mysqli_stmt_affected_rows($stmt5) === 1) {
                        $subject2 = 'Earning Via Referral Commission';
                        $mail2 = '
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
                                  <p>Hello ' . htmlspecialchars($ref_uname) . ',</p>
                                  <p>You have earned a referral commission of &#8358;1000 for referring ' . htmlspecialchars($fname) . ' into the realtor program.</p>
                                </div>
                              </div>
                            </div>
                          </body>
                        </html>';
                        sendEmail($ref_email, $subject2, $mail2);
                    }
                }
            }
        } else {
            $msg[] = '<div class="adm-alert adm-alert--error">No change was made to the account.</div>';
        }
    }
}

mysqli_data_seek($rPaid, 0);
?>

<div class="adm-pagehead">
  <div>
    <div class="adm-eyebrow">Realtor Network</div>
    <h1 class="adm-pagehead__title">Activate <em>accounts</em></h1>
    <p class="adm-pagehead__lede">Review pending activation payments, update the account status, and keep the referral commission flow consistent.</p>
  </div>
  <a href="./realtors" class="adm-btn adm-btn--ghost">View all realtors</a>
</div>

<?php if ($msg) { echo '<div class="adm-formmsg">'; foreach ($msg as $item) echo $item; echo '</div>'; } ?>
<?php if ($successmsg) echo $successmsg; ?>

<div class="adm-kpis">
  <div class="adm-kpi">
    <div class="adm-kpi__label">Pending payments</div>
    <div class="adm-kpi__value"><?php echo (int)$paidNumRows; ?></div>
    <i class="fa-solid fa-user-clock adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi">
    <div class="adm-kpi__label">Total accounts</div>
    <div class="adm-kpi__value"><?php echo (int)$accountNumRows; ?></div>
    <i class="fa-solid fa-users adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi adm-kpi--accent">
    <div class="adm-kpi__label">Next action</div>
    <div class="adm-kpi__value">PAID</div>
    <i class="fa-solid fa-circle-check adm-kpi__icon"></i>
  </div>
</div>

<div class="adm-grid-2">
  <section class="adm-card">
    <div class="adm-card__title">Accounts waiting for payment confirmation</div>
    <p>To see user's payment proof, click on the account name.</p>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Referral ID</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($paidNumRows > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($rPaid)): ?>
              <tr onclick="window.location.href='./view-acc?id=<?php echo urlencode($row['id']); ?>'" style="cursor:pointer;">
                <td><?php echo htmlspecialchars($row['fname']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['ref_id']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4"><div class="adm-empty"><h4>No accounts are pending payment confirmation.</h4></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <aside class="adm-card adm-card--dark adm-card--note">
    <span class="adm-card__chip">Update Form</span>
    <h3>Change account status</h3>
    <p style="color:rgba(234,243,250,.8);margin:0 0 18px;line-height:1.6;">Choose the account and mark it as paid once you have confirmed the activation payment.</p>

    <form method="POST" class="adm-form">
      <label class="adm-field">
        <span class="adm-field__label">Account to update</span>
        <div class="adm-field__wrap adm-field__wrap--select">
          <select name="acct_name" required>
            <option value="">Select account</option>
            <?php mysqli_data_seek($rPaid, 0); while ($row = mysqli_fetch_assoc($rPaid)): ?>
              <option value="<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['fname'] . ' - ' . $row['email']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </label>

      <label class="adm-field">
        <span class="adm-field__label">Status</span>
        <div class="adm-field__wrap adm-field__wrap--select">
          <select name="status" required>
            <option value="">Choose status</option>
            <option value="PAID">PAID</option>
            <option value="PENDING">NOT PAID</option>
          </select>
        </div>
      </label>

      <p class="adm-pagehead__lede" style="color:rgba(234,243,250,.7);margin:0;">Kindly ensure you have received the payment before confirming the account.</p>
      <button type="submit" name="submit" class="adm-btn adm-btn--primary adm-btn--block">Update Account</button>
    </form>
  </aside>
</div>

<?php include "includes/foot.php"; ?>
