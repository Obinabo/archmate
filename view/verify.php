<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";

$title = 'Email Verification | ' . SITE_NAME;
$pageTitle = 'Email Verification';
$bodyClass = 'dash-body auth-page';
$extraStyles = ['assets/css/account-pages.css'];

$msg = [];
$successMsg = '';

if (isset($_GET['x'], $_GET['y']) && filter_var($_GET['x'], FILTER_VALIDATE_EMAIL) && strlen($_GET['y']) === 32) {
    $email = mysqli_real_escape_string($con, $_GET['x']);
    $verified = 'VERIFIED';
    $stmt = mysqli_prepare($con, "UPDATE affiliate SET verified = ? WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $verified, $email);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) === 1) {
        $successMsg = '<div class="success">Email verified successfully. You can now sign in.</div>';
    } else {
        $msg[] = '<div class="error">Unable to verify this account right now.</div>';
    }
} else {
    $msg[] = '<div class="error">This verification link is invalid or has expired.</div>';
}

include "includes/header2.php";
?>

<div class="auth-shell auth-shell-wide">
  <aside class="auth-side">
    <a href="./affiliate" class="auth-brand">
      <div class="auth-brand-mark">AM</div>
      <span>Arch-Mate</span>
    </a>
    <div class="auth-side-inner">
      <div class="section-label" style="margin-bottom:1.5rem">
        <div class="section-label-line"></div>
        <span>Security Check</span>
      </div>
      <h1 class="auth-hero-title">Confirm your <em>email address</em></h1>
      <p class="auth-hero-sub">Email verification helps us keep your realtor account safe and ensures your dashboard is ready for use.</p>
      <div class="account-steps">
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-shield-halved"></i></div>
          <div>
            <h4>Account protection</h4>
            <p>Verification confirms that your details belong to you.</p>
          </div>
        </div>
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-check"></i></div>
          <div>
            <h4>Fast access</h4>
            <p>Once verified, you can continue to the sign-in page.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="auth-side-foot">&copy; <?php echo date('Y'); ?> Arch-Mate.</div>
  </aside>

  <main class="auth-main">
    <div class="auth-card">
      <div class="auth-card-head">
        <h2>Email verification</h2>
        <p>We are checking your activation link now.</p>
      </div>

      <?php foreach ($msg as $m) echo $m; ?>
      <?php if ($successMsg) echo $successMsg; ?>

      <p class="account-note">If you just verified your account, continue to the sign-in page and use the same email and password you registered with.</p>
      <a href="./affiliate" class="btn-primary auth-submit">Go to Sign In</a>
      <a href="../" class="btn-outline auth-back">&larr; Back to website</a>
    </div>
  </main>
</div>

</body>
</html>
