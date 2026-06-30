<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";

$title = 'Settings | ' . SITE_NAME;
$pageTitle = 'Settings';
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

if ($verified === 'UNVERIFIED') {
    redirect('./affiliate?validate=no');
} elseif ($verified === 'VERIFIED' && $paid === 'UNPAID') {
    redirect('./payment-status');
}

include "includes/header2.php";
include "includes/nav-menu.php";
?>

<div class="account-shell">
  <section class="account-hero">
    <div class="section-label"><div class="section-label-line"></div><span>Account Tools</span></div>
    <h1 class="account-hero-title">Keep your <em>account tidy</em></h1>
    <p class="account-hero-sub">Use this space to move quickly between your profile, security settings, and other account actions.</p>
  </section>

  <div class="account-grid">
    <section class="account-panel" style="grid-column: span 7;">
      <div class="account-panel-head">
        <div>
          <h3>Profile and security</h3>
          <span>Make updates or review your account details.</span>
        </div>
      </div>

      <div class="account-action-list">
        <div class="account-action">
          <div>
            <strong>Profile</strong>
            <span>View your public account details and contact information.</span>
          </div>
          <a href="./profile" class="btn-outline">Open</a>
        </div>
        <div class="account-action">
          <div>
            <strong>Edit profile</strong>
            <span>Update your personal details, banking info, or password.</span>
          </div>
          <a href="./edit-profile" class="btn-primary">Open</a>
        </div>
        <div class="account-action">
          <div>
            <strong>Forgot password</strong>
            <span>Request a reset if you need to change your login password.</span>
          </div>
          <a href="./forgot" class="btn-outline">Reset</a>
        </div>
      </div>
    </section>

    <aside class="account-panel account-panel-dark" style="grid-column: span 5;">
      <div class="account-panel-head">
        <div>
          <h3>Quick snapshot</h3>
          <span>Account state at a glance.</span>
        </div>
      </div>

      <div class="account-bank">
        <div class="account-bank-row"><span>Full name</span><strong><?php echo htmlspecialchars($row['fname'] ?? ''); ?></strong></div>
        <div class="account-bank-row"><span>Email</span><strong><?php echo htmlspecialchars($row['email'] ?? ''); ?></strong></div>
        <div class="account-bank-row"><span>Status</span><strong><?php echo htmlspecialchars($verified ?: 'UNVERIFIED'); ?></strong></div>
        <div class="account-bank-row"><span>Payment</span><strong><?php echo htmlspecialchars($paid ?: 'UNPAID'); ?></strong></div>
      </div>
    </aside>
  </div>
</div>

<?php include "includes/footer2.php"; ?>
