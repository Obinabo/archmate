<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "../phpmailer/mailer.php";

$title = 'Account Upgrade | ' . SITE_NAME;
$pageTitle = 'Account Upgrade';
$extraStyles = ['assets/css/account-pages.css'];

if (!isset($_SESSION['email'])) {
  redirect('./affiliate');
}

$email = $_SESSION['email'];
$msg = [];
$successmsg = '';

$stmt = mysqli_prepare($con, "SELECT * FROM affiliate WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$verified = $row['verified'] ?? '';
$paid = $row['paid'] ?? '';

if ($verified === 'UNVERIFIED') {
  redirect('./affiliate?validate=no');
} elseif ($verified === 'VERIFIED' && $paid === 'UNPAID') {
  redirect('./payment-status');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paid'])) {
  $pending = 'PENDING';
  if (empty($_FILES['file']['name'])) {
    $msg[] = '<div class="alert error">Please choose a profile image.</div>';
  } else {
      $temp = $_FILES['file']['tmp_name'];
      $upload_dir = "uploads/";
      $file_name = basename($_FILES['file']['name']);
      $file_ext = explode('.', $file_name);
      $ext = strtolower(end($file_ext));
      $allowed = ['jpeg', 'jpg', 'png'];

      if (!in_array($ext, $allowed, true)) {
        $msg[] = '<div class="alert error">Please select a PNG or JPG image.</div>';
      } elseif (uploadUpgrade($con, $email, $upload_dir, $temp, $ext) === TRUE) {
        $stmt2 = mysqli_prepare($con, "UPDATE affiliate SET type = ? WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt2, 'ss', $pending, $email);
        mysqli_stmt_execute($stmt2);

        $updateChanged = mysqli_stmt_affected_rows($stmt2);
        
        $subject = 'Confirm Account Upgrade Payment by Realtor';
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
                .muted{color:#4a7090}
              </style>
            </head>
            <body>
              <div class="wrap">
                <div class="card">
                  <div class="head"><strong>' . SITE_NAME . '</strong></div>
                  <div class="body">
                    <p>Hello Admin,</p>
                    <p>A realtor with username ' . htmlspecialchars($row['uname']) . ' has submitted payment for an account upgrade. Please verify the payment from the admin dashboard.</p>
                  </div>
                </div>
              </div>
            </body>
          </html>
        ';
        sendEmail(SITE_EMAIL, $subject, $mail);

        if ($updateChanged === 1) {
          $successmsg = '<div class="success alert">Thanks for your payment '.$row['uname'].', You\'ll be notified by mail when it is confirmed.</div>';
        } else {
          $successmsg = '<div class="success alert">Thanks for your payment '.$row['uname'].', your proof has been uploaded and your account is already pending review.</div>';
        }
      } else {
        $msg[] = '<div class="alert error">No change was made to your account.</div>';
      }
  }
}

include "includes/header2.php";
include "includes/nav-menu.php";
?>

<div class="account-shell">
  <section class="account-hero">
    <div class="section-label"><div class="section-label-line"></div><span>Upgrade Path</span></div>
    <h1 class="account-hero-title">Move to <em>Gold Status</em></h1>
    <p class="account-hero-sub">Pay the upgrade fee once, notify our team, and unlock the next tier of your realtor account. We keep the process simple and easy to verify.</p>
  </section>

  <?php if (!empty($msg)) foreach ($msg as $m) echo $m; ?>
  <?php if ($successmsg) echo $successmsg; ?>

  <div class="account-grid">
    <section class="account-panel" style="grid-column: span 7;">
      <div class="account-panel-head">
        <div>
          <h3>Why upgrade?</h3>
          <span>Gold status gives you more visibility and referral potential.</span>
        </div>
      </div>

      <div class="account-steps">
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-percent"></i></div>
          <div>
            <h4>Indirect commission</h4>
            <p>Earn from the sales made by people you refer into the platform.</p>
          </div>
        </div>
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-bolt"></i></div>
          <div>
            <h4>Priority support</h4>
            <p>Upgraded members get faster attention and more direct support.</p>
          </div>
        </div>
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-chart-line"></i></div>
          <div>
            <h4>Stronger listings</h4>
            <p>Present yourself as a premium realtor to potential buyers and referrals.</p>
          </div>
        </div>
      </div>

      <div class="account-divider"></div>

      <form method="post" enctype="multipart/form-data" class="auth-form">
        <label class="confirm-payment">
          <span>Upload Payment Proof</span>
          <input type="file" name="file" />
        </label>
        <button type="submit" name="paid" class="btn-primary auth-submit">I have made the payment</button>
        <p class="account-note" style="margin-top:0.85rem;">Please make payment before clicking the button so the admin team can confirm it promptly.</p>
      </form>
    </section>

    <aside class="account-panel account-panel-dark" style="grid-column: span 5;">
      <div class="account-panel-head">
        <div>
          <h3>Payment details</h3>
          <span>Send the upgrade fee to the account below.</span>
        </div>
      </div>

      <div class="account-bank">
        <div class="account-bank-row"><span>Amount</span><strong>&#8358;17,000</strong></div>
        <div class="account-bank-row"><span>Bank</span><strong><?php echo htmlspecialchars(SITE_BANK_NAME); ?></strong></div>
        <div class="account-bank-row"><span>Account name</span><strong>Arch-mate Estate And Homes LTD</strong></div>
        <div class="account-bank-row"><span>Account number</span><strong><?php echo htmlspecialchars(SITE_BANK_NO); ?></strong></div>
      </div>

      <div class="account-divider" style="background:rgba(255,255,255,.12);"></div>

      <div class="account-note" style="color:var(--light-text);">
        Once payment is confirmed, your account will move into the upgrade queue and the admin team will notify you by email.
      </div>
    </aside>
  </div>
</div>

<?php include "includes/footer2.php"; ?>
