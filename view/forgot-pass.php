<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";

$title = 'Reset Password | ' . SITE_NAME;
$pageTitle = 'Reset Password';
$bodyClass = 'dash-body auth-page';
$extraStyles = ['assets/css/account-pages.css'];

$msg = [];
$successMsg = '';
$linkValid = false;
$email = '';

if (isset($_GET['x'], $_GET['y']) && filter_var($_GET['x'], FILTER_VALIDATE_EMAIL) && strlen($_GET['y']) === 32) {
    $email = mysqli_real_escape_string($con, $_GET['x']);
    $linkValid = true;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg[] = '<div class="error">This reset link is invalid or has expired.</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $linkValid) {
    $password = trim($_POST['password'] ?? '');
    $password2 = trim($_POST['password2'] ?? '');

    if ($password === '' || $password2 === '') {
        $msg[] = '<div class="error">Please enter and confirm your new password.</div>';
    } elseif ($password !== $password2) {
        $msg[] = '<div class="error">The passwords do not match.</div>';
    } else {
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($con, "UPDATE affiliate SET pass = ? WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $hashedPass, $email);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) === 1) {
            $successMsg = '<div class="success">Password updated successfully. You can sign in again now.</div>';
        } else {
            $msg[] = '<div class="error">Unable to update your password right now.</div>';
        }
    }
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
        <span>Reset Flow</span>
      </div>
      <h1 class="auth-hero-title">Create a <em>new password</em></h1>
      <p class="auth-hero-sub">Choose a strong password you have not used before. Once updated, you can return to the realtor portal and continue working.</p>
      <ul class="auth-bullets">
        <li><i class="fa-solid fa-check"></i> Keep it at least 8 characters</li>
        <li><i class="fa-solid fa-check"></i> Use a mix of letters and numbers</li>
        <li><i class="fa-solid fa-check"></i> Save it somewhere secure</li>
      </ul>
    </div>
    <div class="auth-side-foot">&copy; <?php echo date('Y'); ?> Arch-Mate.</div>
  </aside>

  <main class="auth-main">
    <div class="auth-card">
      <div class="auth-card-head">
        <h2>Reset your password</h2>
        <p>Enter a new password for your realtor account.</p>
      </div>

      <?php foreach ($msg as $m) echo $m; ?>
      <?php if ($successMsg) echo $successMsg; ?>

      <?php if ($linkValid): ?>
        <form method="POST" class="auth-form">
          <label class="auth-field">
            <span>New password</span>
            <input type="password" name="password" placeholder="Enter new password" required />
          </label>
          <label class="auth-field">
            <span>Confirm password</span>
            <input type="password" name="password2" placeholder="Confirm password" required />
          </label>
          <button type="submit" class="btn-primary auth-submit">Update Password</button>
        </form>
      <?php else: ?>
        <p class="account-note">This reset link could not be verified. Please request a fresh password reset from the login page.</p>
      <?php endif; ?>

      <a href="./affiliate" class="btn-outline auth-back">&larr; Back to sign in</a>
    </div>
  </main>
</div>

</body>
</html>
