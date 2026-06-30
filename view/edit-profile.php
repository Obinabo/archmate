<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";

$title = 'Edit Profile | ' . SITE_NAME;
$pageTitle = 'Edit Profile';
$extraStyles = ['assets/css/account-pages.css'];

if (!isset($_SESSION['email'])) {
    redirect('./affiliate');
}

$email = $_SESSION['email'];
$stmt = mysqli_prepare($con, "SELECT * FROM affiliate WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (($row['verified'] ?? '') === 'UNVERIFIED') {
    redirect('./affiliate?validate=no');
} elseif (($row['verified'] ?? '') === 'VERIFIED' && ($row['paid'] ?? '') === 'UNPAID') {
    redirect('./payment-status');
}

$msg = [];
$successmsg = '';
$msg2 = [];
$successmsg2 = '';
$msg3 = [];
$successmsg3 = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $fname = trim($_POST['fname'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $work = trim($_POST['work'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $bank = trim($_POST['bank'] ?? '');
    $acct_no = trim($_POST['acct_no'] ?? '');
    $acct_name = trim($_POST['acct_name'] ?? '');

    if ($fname === '') $msg[] = '<div class="error">Please enter your full name.</div>';
    if ($gender === '') $msg[] = '<div class="error">Please enter your gender.</div>';
    if ($work === '') $msg[] = '<div class="error">Please enter your occupation.</div>';
    if ($address === '') $msg[] = '<div class="error">Please enter your address.</div>';
    if ($bank === '') $msg[] = '<div class="error">Please enter your bank name.</div>';
    if ($acct_no === '') $msg[] = '<div class="error">Please enter your account number.</div>';
    if ($acct_name === '') $msg[] = '<div class="error">Please enter your account name.</div>';

    if (empty($msg)) {
        $q = "UPDATE affiliate SET fname = ?, gender = ?, work = ?, address = ?, bank = ?, acct_no = ?, acct_name = ? WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $q);
        mysqli_stmt_bind_param($stmt, 'ssssssss', $fname, $gender, $work, $address, $bank, $acct_no, $acct_name, $email);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) === 1) {
            $successmsg = '<div class="success">Profile successfully updated.</div>';
            $row['fname'] = $fname;
            $row['gender'] = $gender;
            $row['work'] = $work;
            $row['address'] = $address;
            $row['bank'] = $bank;
            $row['acct_no'] = $acct_no;
            $row['acct_name'] = $acct_name;
        } else {
            $msg[] = '<div class="error">No change was made.</div>';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-image'])) {
    if (empty($_FILES['file']['name'])) {
        $msg2[] = '<div class="error">Please choose a profile image.</div>';
    } else {
        $temp = $_FILES['file']['tmp_name'];
        $upload_dir = "uploads/";
        $file_name = basename($_FILES['file']['name']);
        $file_ext = explode('.', $file_name);
        $ext = strtolower(end($file_ext));
        $allowed = ['jpeg', 'jpg', 'png'];

        if (!in_array($ext, $allowed, true)) {
            $msg2[] = '<div class="error">Please select a PNG or JPG image.</div>';
        } elseif (uploadImage($con, $email, $upload_dir, $temp, $ext) === TRUE) {
            $successmsg2 = '<div class="success">Profile picture uploaded successfully.</div>';
            $stmt = mysqli_prepare($con, "SELECT * FROM affiliate WHERE email = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        } else {
            $msg2[] = '<div class="error">Failed to upload profile picture.</div>';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-password'])) {
    $upass = trim($_POST['upass'] ?? '');
    $upass2 = trim($_POST['upass2'] ?? '');

    if ($upass === '' || $upass2 === '') {
        $msg3[] = '<div class="error">Please enter and confirm your new password.</div>';
    } elseif ($upass !== $upass2) {
        $msg3[] = '<div class="error">Entered passwords do not match.</div>';
    } else {
        $pass = password_hash($upass, PASSWORD_DEFAULT);
        $q = "UPDATE affiliate SET pass = ? WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $q);
        mysqli_stmt_bind_param($stmt, 'ss', $pass, $email);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) === 1) {
            $successmsg3 = '<div class="success">Password changed successfully.</div>';
        } else {
            $msg3[] = '<div class="error">New password cannot be the same as the old one.</div>';
        }
    }
}

include "includes/header2.php";
include "includes/nav-menu.php";
?>

<div class="account-shell">
  <section class="account-hero">
    <div class="section-label"><div class="section-label-line"></div><span>Profile Settings</span></div>
    <h1 class="account-hero-title">Update your <em>profile details</em></h1>
    <p class="account-hero-sub">Keep your contact details, banking information, picture, and password current so your realtor account stays ready for use.</p>
  </section>

  <div class="account-grid">
    <section class="account-panel" style="grid-column: span 7;">
      <div class="account-panel-head">
        <div>
          <h3>Update profile</h3>
          <span>Only non-sensitive information can be edited here.</span>
        </div>
      </div>

      <?php foreach ($msg as $m) echo $m; ?>
      <?php if ($successmsg) echo $successmsg; ?>

      <div class="account-stack">
        <form action="" method="POST" enctype="multipart/form-data" class="account-stack">
          <div class="account-panel account-panel-dark" style="padding:1.2rem;">
            <div class="account-panel-head" style="margin-bottom:0.75rem;">
              <div>
                <h3 style="font-size:1.25rem;">Profile picture</h3>
                <span>Choose a clear image for your account avatar.</span>
              </div>
            </div>
            <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;">
              <img src="<?php echo htmlspecialchars($row['pic'] ?? 'assets/img/man.png'); ?>" alt="Profile picture" style="width:88px;height:88px;border-radius:20px;object-fit:cover;border:2px solid rgba(255,255,255,.14);" onerror="this.src='https://i.pravatar.cc/160'">
              <div class="account-upload" style="flex:1;min-width:220px;">
                <span class="auth-field" style="margin-bottom:0;">
                  <span>New image</span>
                  <input type="file" name="file" accept=".jpg,.jpeg,.png" />
                </span>
                <button type="submit" name="submit-image" class="btn-primary" style="margin-top:0.5rem;width:100%;">Upload Photo</button>
              </div>
            </div>
          </div>
        </form>

        <form action="" method="POST" class="auth-form auth-form-grid">
          <label class="auth-field auth-field-full">
            <span>Email</span>
            <input type="text" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>" disabled />
          </label>
          <label class="auth-field">
            <span>Username</span>
            <input type="text" value="<?php echo htmlspecialchars($row['uname'] ?? ''); ?>" disabled />
          </label>
          <label class="auth-field">
            <span>Phone</span>
            <input type="text" value="<?php echo htmlspecialchars($row['phone'] ?? ''); ?>" disabled />
          </label>
          <label class="auth-field">
            <span>Full name</span>
            <input type="text" name="fname" value="<?php echo htmlspecialchars($row['fname'] ?? ''); ?>" required />
          </label>
          <label class="auth-field">
            <span>Gender</span>
            <input type="text" name="gender" value="<?php echo htmlspecialchars($row['gender'] ?? ''); ?>" required />
          </label>
          <label class="auth-field">
            <span>Occupation</span>
            <input type="text" name="work" value="<?php echo htmlspecialchars($row['work'] ?? ''); ?>" required />
          </label>
          <label class="auth-field auth-field-full">
            <span>Address</span>
            <input type="text" name="address" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>" required />
          </label>
          <label class="auth-field">
            <span>Bank</span>
            <input type="text" name="bank" value="<?php echo htmlspecialchars($row['bank'] ?? ''); ?>" required />
          </label>
          <label class="auth-field">
            <span>Account number</span>
            <input type="text" name="acct_no" value="<?php echo htmlspecialchars($row['acct_no'] ?? ''); ?>" required />
          </label>
          <label class="auth-field auth-field-full">
            <span>Account name</span>
            <input type="text" name="acct_name" value="<?php echo htmlspecialchars($row['acct_name'] ?? ''); ?>" required />
          </label>
          <button type="submit" name="submit" class="btn-primary auth-submit auth-field-full">Update Account</button>
        </form>
      </div>
    </section>

    <aside class="account-panel account-panel-dark" style="grid-column: span 5;">
      <div class="account-panel-head">
        <div>
          <h3>Change password</h3>
          <span>Use a strong password that is different from your current one.</span>
        </div>
      </div>

      <?php foreach ($msg2 as $m) echo $m; ?>
      <?php if ($successmsg2) echo $successmsg2; ?>
      <?php foreach ($msg3 as $m) echo $m; ?>
      <?php if ($successmsg3) echo $successmsg3; ?>

      <form action="" method="POST" class="auth-form">
        <label class="auth-field">
          <span>New password</span>
          <input type="password" name="upass" required />
        </label>
        <label class="auth-field">
          <span>Confirm password</span>
          <input type="password" name="upass2" required />
        </label>
        <button type="submit" name="submit-password" class="btn-primary auth-submit">Change Password</button>
      </form>
    </aside>
  </div>
</div>

<?php include "includes/footer2.php"; ?>
