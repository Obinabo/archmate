<?php
session_start();
include "../config/dbconfig.php";
include "../config/func.inc.php";
$title = "Arch-Mate Realtor | Sign In";

if (isset($_SESSION['email'])) { redirect('./dashboard'); }

$msg = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['uid'])) {
        $msg[] = '<div class="error">Please enter your email or mobile number</div>';
    } else {
        $logid = strip_tags(mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['uid']))));
    }
    if (empty($_POST['pass'])) {
        $msg[] = '<div class="error">Please enter your Password</div>';
    } else {
        $pass = strip_tags(mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['pass']))));
    }
    if (empty($msg)) {
        $q = "SELECT * FROM affiliate WHERE email = ? OR phone = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $q);
        mysqli_stmt_bind_param($stmt, 'ss', $logid, $logid);
        mysqli_stmt_execute($stmt);
        $r = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($r)) {
            if (password_verify($pass, $row['pass'])) {
                $verified = $row['verified']; $paid = $row['paid'];
                if ($verified === 'UNVERIFIED') {
                    $msg[] = '<div class="error">Please check your email for the verification link.</div>';
                } elseif ($paid === 'PENDING') {
                    $msg[] = '<div class="error">Please hold while we confirm your payment.</div>';
                } elseif ($verified === 'VERIFIED' && $paid === 'UNPAID') {
                    $_SESSION['uname'] = $row['uname'];
                    redirect('./payment-status');
                } elseif ($paid === 'PAID' && $verified === 'VERIFIED') {
                    $_SESSION['email'] = $row['email'];
                    redirect('./dashboard');
                }
            } else {
                $msg[] = '<div class="error">Incorrect password</div>';
            }
        } else {
            $msg[] = '<div class="error">Email or mobile number isn\'t registered with us.</div>';
        }
    }
}
include "includes/header2.php";
?>

<div class="auth-shell">
  <aside class="auth-side">
    <a href="./" class="auth-brand">
      <div class="auth-brand-mark">AM</div>
      <span>Arch-Mate</span>
    </a>
    <div class="auth-side-inner">
      <div class="section-label" style="margin-bottom:1.5rem"><div class="section-label-line"></div><span>Realtor Portal</span></div>
      <h1 class="auth-hero-title">Welcome <em>back.</em></h1>
      <p class="auth-hero-sub">Track your sales, manage referrals, and watch your portfolio grow — all from one elegant cockpit.</p>
      <ul class="auth-bullets">
        <li><i class="fa-solid fa-check"></i> Real-time commission tracking</li>
        <li><i class="fa-solid fa-check"></i> Instant withdrawal requests</li>
        <li><i class="fa-solid fa-check"></i> Premium client referral tools</li>
      </ul>
    </div>
    <div class="auth-side-foot">&copy; <?php echo date('Y'); ?> Arch-Mate.</div>
  </aside>

  <main class="auth-main">
    <div class="auth-card">
      <div class="auth-card-head">
        <h2>Sign in to your dashboard</h2>
        <p>New to Arch-Mate? <a href="./auth">Become a Realtor &rarr;</a></p>
      </div>

      <?php if (!empty($msg)) foreach ($msg as $m) echo $m; ?>

      <form method="POST" class="auth-form">
        <label class="auth-field">
          <span>Email or Phone</span>
          <input type="text" name="uid" placeholder="you@example.com" required/>
        </label>
        <label class="auth-field">
          <span>Password</span>
          <input type="password" name="pass" placeholder="••••••••" required/>
        </label>
        <div class="auth-row-between">
          <label class="auth-check"><input type="checkbox"/> Remember me</label>
          <a href="./forgot" class="auth-link">Forgot password?</a>
        </div>
        <button type="submit" class="btn-primary auth-submit">Sign In</button>
      </form>

      <div class="auth-divider"><span>or</span></div>
      <a href="./" class="btn-outline auth-back">&larr; Back to website</a>
    </div>
  </main>
</div>

</body>
</html>
