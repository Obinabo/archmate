<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";

if (isset($_SESSION['email'])) { $email = $_SESSION['email']; }
else { redirect('./affiliate'); }

$q = "SELECT * FROM affiliate WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($con, $q);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($r);
$verified = $row['verified']; $paid = $row['paid'];
$ref_id = $row['ref_id']; $userUplink = $row['uplink']; $acct_type = $row['type'];

if ($verified === 'UNVERIFIED') redirect('./affiliate');
elseif ($verified === 'VERIFIED' && $paid === 'UNPAID') redirect('./payment-status');

$referral = '';
if (!empty($userUplink)) {
    $stmt1 = mysqli_prepare($con, "SELECT fname FROM affiliate WHERE ref_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt1, 's', $userUplink);
    mysqli_stmt_execute($stmt1);
    $r1 = mysqli_stmt_get_result($stmt1);
    if ($row2 = mysqli_fetch_assoc($r1)) $referral = $row2['fname'];
}

// Stats
$status = 'APPROVED';
$totalSales = 0; $totalEarnings = 0;
$qs = mysqli_prepare($con, "SELECT COUNT(*) c, COALESCE(SUM(commission),0) s FROM affiliatesales WHERE email = ? AND status = ?");
mysqli_stmt_bind_param($qs, 'ss', $email, $status);
mysqli_stmt_execute($qs);
if ($rs = mysqli_stmt_get_result($qs)) { $rr = mysqli_fetch_assoc($rs); $totalSales=(int)$rr['c']; $totalEarnings=(float)$rr['s']; }

$balance = isset($row['balance']) ? (float)$row['balance'] : $totalEarnings;
$refLink = (defined('URL') ? URL : '').'/dashboard/register?ref='.$ref_id;

$title = $row['fname'].' | Dashboard';
$pageTitle = 'Dashboard';
include "includes/header2.php";
include "includes/nav-menu.php";
?>

<div class="dash-hero">
  <div>
    <div class="section-label"><div class="section-label-line"></div><span>Welcome back</span></div>
    <h1 class="dash-hero-title">Hello, <em><?php echo htmlspecialchars($row['fname']); ?></em></h1>
    <p class="dash-hero-sub">Here's a snapshot of your realtor activity. <?php if($referral) echo 'Referred by <strong>'.htmlspecialchars($referral).'</strong>.'; ?></p>
  </div>
  <span class="dash-pill"><?php echo htmlspecialchars($acct_type ?: 'Standard'); ?> Realtor</span>
</div>

<div class="dash-stats">
  <div class="dash-stat">
    <div class="dash-stat-icon"><i class="fa-solid fa-handshake"></i></div>
    <div class="dash-stat-num"><?php echo $totalSales; ?></div>
    <div class="dash-stat-label">Approved Sales</div>
  </div>
  <div class="dash-stat">
    <div class="dash-stat-icon"><i class="fa-solid fa-coins"></i></div>
    <div class="dash-stat-num">&#8358;<?php echo number_format($totalEarnings); ?></div>
    <div class="dash-stat-label">Total Earnings</div>
  </div>
  <div class="dash-stat">
    <div class="dash-stat-icon"><i class="fa-solid fa-wallet"></i></div>
    <div class="dash-stat-num">&#8358;<?php echo number_format($balance); ?></div>
    <div class="dash-stat-label">Available Balance</div>
  </div>
  <div class="dash-stat dash-stat-accent">
    <div class="dash-stat-icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></div>
    <div class="dash-stat-label" style="color:var(--navy-deep);opacity:.7">Quick Action</div>
    <a href="./withdraw" class="dash-stat-cta">Request Withdrawal &rarr;</a>
  </div>
</div>

<div class="dash-grid-2">
  <section class="dash-panel">
    <div class="dash-panel-head">
      <h3>Your Referral Link</h3>
      <span>Share to earn 10% commission</span>
    </div>
    <div class="dash-ref-row">
      <input id="refLink" readonly value="<?php echo htmlspecialchars($refLink); ?>"/>
      <button class="btn-primary" onclick="navigator.clipboard.writeText(document.getElementById('refLink').value).then(()=>{this.textContent='Copied'})">Copy</button>
    </div>
    <div class="dash-share">
      <a target="_blank" rel="noopener" href="https://wa.me/?text=<?php echo urlencode($refLink); ?>"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
      <a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($refLink); ?>"><i class="fa-brands fa-facebook"></i> Facebook</a>
      <a target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($refLink); ?>"><i class="fa-brands fa-x-twitter"></i> Twitter</a>
    </div>
  </section>

  <section class="dash-panel dash-panel-dark">
    <div class="section-label" style="margin-bottom:1rem"><div class="section-label-line"></div><span style="color:var(--cyan)">Pro Tip</span></div>
    <h3 class="dash-panel-h3">Climb the leaderboard.</h3>
    <p>Top realtors this month earned over &#8358;1.2M in commissions. Share your link daily on WhatsApp groups for compounding results.</p>
    <a href="./payment-upgrade" class="btn-primary">Upgrade to Premium</a>
  </section>
</div>

<section class="dash-panel">
  <div class="dash-panel-head">
    <h3>Recent Sales</h3>
    <a href="./sales" class="view-all" style="color:var(--navy-mid)">View all &rarr;</a>
  </div>
  <div class="dash-table-wrap">
    <table class="dash-table">
      <thead><tr><th>#</th><th>Property</th><th>Amount</th><th>Commission</th><th>Status</th><th>Date</th></tr></thead>
      <tbody>
      <?php
        $stmt2 = mysqli_prepare($con, "SELECT * FROM affiliatesales WHERE email = ? ORDER BY id DESC LIMIT 5");
        mysqli_stmt_bind_param($stmt2, 's', $email);
        mysqli_stmt_execute($stmt2);
        $r2 = mysqli_stmt_get_result($stmt2);
        $i = 1;
        if ($r2 && mysqli_num_rows($r2) > 0) {
          while ($s = mysqli_fetch_assoc($r2)) {
            $st = strtolower($s['status']);
            echo '<tr>
              <td>'.$i++.'</td>
              <td>'.htmlspecialchars($s['title'] ?? $s['property'] ?? '—').'</td>
              <td>&#8358;'.number_format((float)($s['amount'] ?? 0)).'</td>
              <td>&#8358;'.number_format((float)($s['commission'] ?? 0)).'</td>
              <td><span class="dash-badge dash-badge-'.$st.'">'.htmlspecialchars($s['status']).'</span></td>
              <td>'.htmlspecialchars($s['date'] ?? '').'</td>
            </tr>';
          }
        } else {
          echo '<tr><td colspan="6" class="dash-empty">No sales yet — share your link to get started.</td></tr>';
        }
      ?>
      </tbody>
    </table>
  </div>
</section>

<?php include "includes/footer2.php"; ?>
