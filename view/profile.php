<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";

if (isset($_SESSION['email'])) { $email = $_SESSION['email']; } else { redirect('./affiliate'); }
$stmt = mysqli_prepare($con, "SELECT * FROM affiliate WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $email); mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if ($row['verified'] === 'UNVERIFIED') redirect('./affiliate');
elseif ($row['verified']==='VERIFIED' && $row['paid']==='UNPAID') redirect('./payment-status');

$title = 'Profile | '.SITE_NAME; $pageTitle='My Profile';
include "includes/header2.php"; include "includes/nav-menu.php";
?>
<div class="profile-hero">
  <img src="<?php echo !empty($row['pic'])?htmlspecialchars($row['pic']):'../assets/img/man.png'; ?>" onerror="this.src='https://i.pravatar.cc/200'" alt="" class="profile-avatar"/>
  <div>
    <div class="section-label"><div class="section-label-line"></div><span><?php echo htmlspecialchars($row['type'] ?? 'Realtor'); ?></span></div>
    <h1 class="dash-hero-title"><?php echo htmlspecialchars($row['fname']); ?></h1>
    <p class="dash-hero-sub">@<?php echo htmlspecialchars($row['uname']); ?> &middot; <?php echo htmlspecialchars($row['email']); ?></p>
    <a href="./edit-profile" class="btn-primary" style="margin-top:1rem">Edit Profile</a>
  </div>
</div>

<div class="dash-grid-2">
  <section class="dash-panel">
    <h3 class="dash-panel-h3">Personal Details</h3>
    <ul class="profile-list">
      <li><span>Full Name</span><strong><?php echo htmlspecialchars($row['fname']); ?></strong></li>
      <li><span>Username</span><strong><?php echo htmlspecialchars($row['uname']); ?></strong></li>
      <li><span>Email</span><strong><?php echo htmlspecialchars($row['email']); ?></strong></li>
      <li><span>Phone</span><strong><?php echo htmlspecialchars($row['phone']); ?></strong></li>
      <li><span>Gender</span><strong><?php echo htmlspecialchars($row['gender'] ?? '—'); ?></strong></li>
      <li><span>Occupation</span><strong><?php echo htmlspecialchars($row['work'] ?? '—'); ?></strong></li>
      <li><span>Address</span><strong><?php echo htmlspecialchars($row['address'] ?? '—'); ?></strong></li>
    </ul>
  </section>
  <section class="dash-panel">
    <h3 class="dash-panel-h3">Banking & Referral</h3>
    <ul class="profile-list">
      <li><span>Bank</span><strong><?php echo htmlspecialchars($row['bank'] ?? '—'); ?></strong></li>
      <li><span>Account No.</span><strong><?php echo htmlspecialchars($row['acct_no'] ?? '—'); ?></strong></li>
      <li><span>Referral ID</span><strong><?php echo htmlspecialchars($row['ref_id']); ?></strong></li>
      <li><span>Account Type</span><strong><?php echo htmlspecialchars($row['type'] ?? 'Standard'); ?></strong></li>
      <li><span>Status</span><strong><span class="dash-badge dash-badge-approved"><?php echo htmlspecialchars($row['verified']); ?></span></strong></li>
      <li><span>Payment</span><strong><span class="dash-badge dash-badge-<?php echo strtolower($row['paid']); ?>"><?php echo htmlspecialchars($row['paid']); ?></span></strong></li>
    </ul>
  </section>
</div>

<?php include "includes/footer2.php"; ?>
