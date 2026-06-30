<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "../phpmailer/mailer.php";

$title = 'Forgot Password | ' . SITE_NAME;
$pageTitle = 'Forgot Password';
$bodyClass = 'dash-body auth-page';
$extraStyles = ['assets/css/account-pages.css'];

$msg = [];
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg[] = '<div class="error">Please enter a valid email address.</div>';
    } else {
        $email = mysqli_real_escape_string($con, $email);
        $stmt = mysqli_prepare($con, "SELECT * FROM affiliate WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $db_email = $row['email'];
            $uname = $row['uname'];
            $unique_string = md5(uniqid((string)rand(), true));
            $forgot_link = URL . '/forgot-pass?x=' . urlencode($db_email) . '&y=' . $unique_string;
            $subject = '[' . SITE_NAME_SHORT . '] Password Recovery';

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
                  .btn{display:inline-block;background:#00c5dc;color:#071524 !important;text-decoration:none;font-weight:700;padding:14px 26px;border-radius:999px}
                  .muted{color:#4a7090}
                </style>
              </head>
              <body>
                <div class="wrap">
                  <div class="card">
                    <div class="head"><strong>' . SITE_NAME . '</strong></div>
                    <div class="body">
                      <p>Hello ' . htmlspecialchars($uname) . ',</p>
                      <p>Use the button below to reset your password.</p>
                      <p><a class="btn" href="' . $forgot_link . '">Reset Password</a></p>
                      <p class="muted">If the button does not work, copy and paste this link into your browser:</p>
                      <p class="muted">' . $forgot_link . '</p>
                    </div>
                  </div>
                </div>
              </body>
            </html>';

            sendEmail($db_email, $subject, $mail);
            $successMsg = '<div class="success">Check your email for the password reset link.</div>';
        } else {
            $msg[] = '<div class="error">No account matches that email address.</div>';
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
        <span>Password Help</span>
      </div>
      <h1 class="auth-hero-title">Recover your <em>account access</em></h1>
      <p class="auth-hero-sub">We will send a secure reset link to your registered email address. Follow the link to create a new password.</p>
      <div class="account-steps">
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-envelope"></i></div>
          <div>
            <h4>1. Enter email</h4>
            <p>Use the email linked to your realtor account.</p>
          </div>
        </div>
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-link"></i></div>
          <div>
            <h4>2. Open the reset link</h4>
            <p>We will send a link that takes you to the password reset form.</p>
          </div>
        </div>
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-lock"></i></div>
          <div>
            <h4>3. Create a new password</h4>
            <p>Choose something secure, then sign back in to continue.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="auth-side-foot">&copy; <?php echo date('Y'); ?> Arch-Mate.</div>
  </aside>

  <main class="auth-main">
    <div class="auth-card">
      <div class="auth-card-head">
        <h2>Forgot your password?</h2>
        <p>Enter your email and we will send a reset link.</p>
      </div>

      <?php foreach ($msg as $m) echo $m; ?>
      <?php if ($successMsg) echo $successMsg; ?>

      <form method="post" class="auth-form">
        <label class="auth-field">
          <span>Email address</span>
          <input type="email" name="email" placeholder="you@example.com" required />
        </label>
        <button type="submit" class="btn-primary auth-submit">Send Reset Link</button>
      </form>

      <a href="./affiliate" class="btn-outline auth-back">&larr; Back to sign in</a>
    </div>
  </main>
</div>

</body>
</html>
