<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "../phpmailer/mailer.php";

$title = 'Register Sale | ' . SITE_NAME;
$pageTitle = 'Register Sale';
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

$msg = [];
$successmsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $saleTitle = trim($_POST['title'] ?? '');
    $payment = trim($_POST['payment'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($saleTitle === '') {
        $msg[] = '<div class="error">Please enter the property title.</div>';
    }
    if ($payment === '') {
        $msg[] = '<div class="error">Please enter the sale amount.</div>';
    }
    if ($description === '') {
        $msg[] = '<div class="error">Please enter a description.</div>';
    }
    if (empty($_FILES['file']['name'])) {
        $msg[] = '<div class="error">Please choose a property image.</div>';
    }

    $commission = 0;
    $status = 'PENDING';
    $date = date("d/m/Y h:i:s");
    $max_size = 5 * 1024 * 1024;
    $admin_email = SITE_EMAIL_2;

    if (empty($msg) && $_FILES['file']['size'] > $max_size) {
        $msg[] = '<div class="error">Choose an image smaller than 5MB.</div>';
    }

    if (empty($msg) && !empty($_FILES['file']['name'])) {
        $temp = $_FILES['file']['tmp_name'];
        $upload_dir = "uploads/";
        $file_name = basename($_FILES['file']['name']);
        $file_ext = explode('.', $file_name);
        $ext = strtolower(end($file_ext));
        $allowed = ['jpeg', 'jpg', 'png'];

        if (!in_array($ext, $allowed, true)) {
            $msg[] = '<div class="error">Please select a PNG or JPG image.</div>';
        } elseif (createSale($con, $upload_dir, $temp, $ext, $email, $saleTitle, $description, $payment, $commission, $status, $date) === TRUE) {
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
                      <p>A realtor has recorded a new sale. Please log in to the admin dashboard and verify the record.</p>
                    </div>
                  </div>
                </div>
              </body>
            </html>';
            sendEmail($admin_email, $subject, $mail);
            $successmsg = '<div class="success">Sale record created successfully. The admin team will review it shortly.</div>';
        } elseif (empty($msg)) {
            $msg[] = '<div class="error">Failed to create record.</div>';
        }
    }
}

include "includes/header2.php";
include "includes/nav-menu.php";
?>

<div class="account-shell">
  <section class="account-hero">
    <div class="section-label"><div class="section-label-line"></div><span>Sales Log</span></div>
    <h1 class="account-hero-title">Record a <em>new sale</em></h1>
    <p class="account-hero-sub">Capture the property name, sale value, description, and a clear image. We keep the form simple so it is quick to submit from any device.</p>
  </section>

  <?php foreach ($msg as $m) echo $m; ?>
  <?php if ($successmsg) echo $successmsg; ?>

  <div class="account-grid">
    <section class="account-panel" style="grid-column: span 7;">
      <div class="account-panel-head">
        <div>
          <h3>Sale details</h3>
          <span>Fill in the details below to register the sale.</span>
        </div>
      </div>

      <form action="" method="POST" enctype="multipart/form-data" class="auth-form auth-form-grid">
        <label class="auth-field auth-field-full">
          <span>Your email</span>
          <input type="text" value="<?php echo htmlspecialchars($email); ?>" disabled />
        </label>
        <label class="auth-field">
          <span>Property title</span>
          <input type="text" name="title" placeholder="E.g. Prime City Estate" required />
        </label>
        <label class="auth-field">
          <span>Sale amount</span>
          <input type="number" name="payment" placeholder="E.g. 2000000" required />
        </label>
        <label class="auth-field auth-field-full account-upload">
          <span>Property image</span>
          <input type="file" name="file" accept=".jpg,.jpeg,.png" required />
        </label>
        <label class="auth-field auth-field-full">
          <span>Description</span>
          <textarea name="description" rows="6" placeholder="Write a short description of the sale..." required></textarea>
        </label>
        <button type="submit" name="submit" class="btn-primary auth-submit auth-field-full">Create Sale</button>
      </form>
    </section>

    <aside class="account-panel account-panel-dark" style="grid-column: span 5;">
      <div class="account-panel-head">
        <div>
          <h3>Before you submit</h3>
          <span>Small checks help keep the records clean.</span>
        </div>
      </div>

      <div class="account-steps">
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-image"></i></div>
          <div>
            <h4>Use a clear image</h4>
            <p>Choose a well-lit property photo in JPG or PNG format.</p>
          </div>
        </div>
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-money-bill"></i></div>
          <div>
            <h4>Enter the correct amount</h4>
            <p>Record the exact sale value so commissions stay accurate.</p>
          </div>
        </div>
        <div class="account-step">
          <div class="account-step-icon"><i class="fa-solid fa-clock"></i></div>
          <div>
            <h4>Submit once</h4>
            <p>The admin team will verify the sale after it is submitted.</p>
          </div>
        </div>
      </div>
    </aside>
  </div>
</div>

<?php include "includes/footer2.php"; ?>
