<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "../phpmailer/mailer.php";
include_once "includes/count.inc.php";

if (!isset($_SESSION['id'])) {
    redirect('index.php');
}

$title = 'Account Upgrade Request | ' . SITE_NAME;
include "includes/head.php";

$pendingType = 'PENDING';
$q = "SELECT * FROM affiliate WHERE type = ? ORDER BY id";
$stmt = mysqli_prepare($con, $q);
mysqli_stmt_bind_param($stmt, 's', $pendingType);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
$numRows = $r ? mysqli_num_rows($r) : 0;

$msg = [];
$successmsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $acctName = trim($_POST['acct_name'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($acctName === '') {
        $msg[] = '<div class="adm-alert adm-alert--error">Please select an account.</div>';
    }
    if ($status === '') {
        $msg[] = '<div class="adm-alert adm-alert--error">Please choose a target status.</div>';
    } elseif (!in_array($status, ['AFFILIATE', 'GOLD'], true)) {
        $msg[] = '<div class="adm-alert adm-alert--error">Please choose either Affiliate or Gold.</div>';
    }

    if (empty($msg)) {
        $qUpdate = "UPDATE affiliate SET type = ? WHERE email = ? LIMIT 1";
        $stmtUpdate = mysqli_prepare($con, $qUpdate);
        mysqli_stmt_bind_param($stmtUpdate, 'ss', $status, $acctName);
        mysqli_stmt_execute($stmtUpdate);

        if (mysqli_stmt_affected_rows($stmtUpdate) === 1) {
            $qAccount = "SELECT * FROM affiliate WHERE email = ? LIMIT 1";
            $stmtAccount = mysqli_prepare($con, $qAccount);
            mysqli_stmt_bind_param($stmtAccount, 's', $acctName);
            mysqli_stmt_execute($stmtAccount);
            $resultAccount = mysqli_stmt_get_result($stmtAccount);
            $row3 = $resultAccount ? mysqli_fetch_assoc($resultAccount) : null;

            $fname = $row3['fname'] ?? '';
            $userUplink = $row3['uplink'] ?? '';

            $accountLabel = $status === 'GOLD' ? 'Gold' : 'Affiliate';
            $subject = 'Account upgraded to ' . $accountLabel;
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
                      <p>Your account upgrade request has been processed. Your dashboard now reflects your ' . strtolower($accountLabel) . ' status.</p>
                    </div>
                  </div>
                </div>
              </body>
            </html>';
            sendEmail($acctName, $subject, $mail);

            if (!empty($userUplink)) {
                $qRef = "SELECT * FROM affiliate WHERE ref_id = ? LIMIT 1";
                $stmtRef = mysqli_prepare($con, $qRef);
                mysqli_stmt_bind_param($stmtRef, 's', $userUplink);
                mysqli_stmt_execute($stmtRef);
                $resultRef = mysqli_stmt_get_result($stmtRef);
                $row4 = $resultRef ? mysqli_fetch_assoc($resultRef) : null;

                if ($row4) {
                    $commission = $status === 'GOLD' ? 4000 : 1000;
                    $ref_email = $row4['email'];
                    $ref_uname = $row4['uname'];
                    $ref_balance = (int)$row4['balance'] + $commission;
                    $ref_earnings = (int)$row4['earnings'] + $commission;

                    $qRefUpdate = "UPDATE affiliate SET earnings = ?, balance = ? WHERE email = ? LIMIT 1";
                    $stmtRefUpdate = mysqli_prepare($con, $qRefUpdate);
                    mysqli_stmt_bind_param($stmtRefUpdate, 'iis', $ref_earnings, $ref_balance, $ref_email);
                    mysqli_stmt_execute($stmtRefUpdate);

                    if (mysqli_stmt_affected_rows($stmtRefUpdate) === 1) {
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
                                  <p>You have earned a referral commission of &#8358;' . number_format($commission) . ' for referring ' . htmlspecialchars($fname) . ' into the realtor program.</p>
                                </div>
                              </div>
                            </div>
                          </body>
                        </html>';
                        sendEmail($ref_email, $subject2, $mail2);
                    }
                }
            }

            $successmsg = '<div class="adm-alert adm-alert--success">Selected account has been upgraded.</div>';
        } else {
            $msg[] = '<div class="adm-alert adm-alert--error">No change made to account.</div>';
        }
    }
}

if ($r) {
    mysqli_data_seek($r, 0);
}
?>

<div class="adm-pagehead">
  <div>
    <div class="adm-eyebrow">Realtor Network</div>
    <h1 class="adm-pagehead__title">Account <em>upgrade</em> requests</h1>
    <p class="adm-pagehead__lede">Review pending upgrade requests, move the realtor into the correct plan, and keep the referral bonus flow in sync.</p>
  </div>
  <a href="./dashboard" class="adm-btn adm-btn--ghost">Back to dashboard</a>
</div>

<?php if ($msg) { echo '<div class="adm-formmsg">'; foreach ($msg as $item) echo $item; echo '</div>'; } ?>
<?php if ($successmsg) echo $successmsg; ?>

<div class="adm-kpis">
  <div class="adm-kpi">
    <div class="adm-kpi__label">Pending upgrades</div>
    <div class="adm-kpi__value"><?php echo (int)$numRows; ?></div>
    <i class="fa-solid fa-user-clock adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi">
    <div class="adm-kpi__label">Plan options</div>
    <div class="adm-kpi__value">2</div>
    <i class="fa-solid fa-layer-group adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi adm-kpi--accent">
    <div class="adm-kpi__label">Referral bonus</div>
    <div class="adm-kpi__value">&#8358;4k</div>
    <i class="fa-solid fa-coins adm-kpi__icon"></i>
  </div>
</div>

<div class="adm-grid-2">
  <section class="adm-card">
    <div class="adm-card__title">Accounts waiting for upgrade review</div>
    <p>To see upgrade payment proof, click on the account name.</p>
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
          <?php if ($numRows > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($r)): ?>
              <tr onclick="window.location.href='./view-acc?id=<?php echo urlencode($row['id']); ?>'" style="cursor:pointer;">
                <td><?php echo htmlspecialchars($row['fname']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['ref_id']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4">
                <div class="adm-empty">
                  <h4>No accounts are currently waiting for upgrade.</h4>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <aside class="adm-card adm-card--dark adm-card--note">
    <span class="adm-card__chip">Update Form</span>
    <h3>Set the new plan</h3>
    <p style="color:rgba(234,243,250,.8);margin:0 0 18px;line-height:1.6;">Select the account and choose the plan it should move into after payment has been confirmed.</p>

    <form method="POST" class="adm-form">
      <label class="adm-field">
        <span class="adm-field__label">Account to upgrade</span>
        <div class="adm-field__wrap adm-field__wrap--select">
          <select name="acct_name" required>
            <option value="">Select account</option>
            <?php
            if ($r) {
                mysqli_data_seek($r, 0);
                while ($row = mysqli_fetch_assoc($r)):
            ?>
              <option value="<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['fname'] . ' - ' . $row['email']); ?></option>
            <?php
                endwhile;
            }
            ?>
          </select>
        </div>
      </label>

      <label class="adm-field">
        <span class="adm-field__label">Target status</span>
        <div class="adm-field__wrap adm-field__wrap--select">
          <select name="status" required>
            <option value="">Choose status</option>
            <option value="AFFILIATE">AFFILIATE</option>
            <option value="GOLD">GOLD</option>
          </select>
        </div>
      </label>

      <p class="adm-pagehead__lede" style="color:rgba(234,243,250,.7);margin:0;">Make sure the payment has been received before you confirm the upgrade.</p>
      <button type="submit" name="submit" class="adm-btn adm-btn--primary adm-btn--block">Upgrade Account</button>
    </form>
  </aside>
</div>

<?php include "includes/foot.php"; ?>
