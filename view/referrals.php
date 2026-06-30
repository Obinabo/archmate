<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";

$title = 'Referrals | ' . SITE_NAME;
$pageTitle = 'Referrals';
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
$ref_id = $row['ref_id'] ?? '';

if ($verified === 'UNVERIFIED') {
    redirect('./affiliate?validate=no');
} elseif ($verified === 'VERIFIED' && $paid === 'UNPAID') {
    redirect('./payment-status');
}

$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$refLink = URL . '/auth?ref=' . $ref_id;
$stmt1 = mysqli_prepare($con, "SELECT * FROM affiliate WHERE uplink = ? ORDER BY id DESC LIMIT $limit OFFSET $offset");
mysqli_stmt_bind_param($stmt1, 's', $ref_id);
mysqli_stmt_execute($stmt1);
$result = mysqli_stmt_get_result($stmt1);
$pageRows = $result ? mysqli_num_rows($result) : 0;

$stmtCount = mysqli_prepare($con, "SELECT COUNT(*) AS total FROM affiliate WHERE uplink = ?");
mysqli_stmt_bind_param($stmtCount, 's', $ref_id);
mysqli_stmt_execute($stmtCount);
$countRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCount));
$numRows = (int)($countRow['total'] ?? 0);

include "includes/header2.php";
include "includes/nav-menu.php";
?>

<div class="account-shell">
  <section class="account-hero">
    <div class="section-label"><div class="section-label-line"></div><span>Your Network</span></div>
    <h1 class="account-hero-title">Track and share your <em>referral link</em></h1>
    <p class="account-hero-sub">Invite new realtors with your personal link and follow the people who join through your network.</p>
  </section>

  <div class="account-grid">
    <section class="account-panel" style="grid-column: span 8;">
      <div class="account-panel-head">
        <div>
          <h3>Referral link</h3>
          <span>Share this link to bring people into your downline.</span>
        </div>
      </div>

      <div class="account-copy-row">
        <input id="textToCopy" readonly value="<?php echo htmlspecialchars($refLink); ?>" />
        <button type="button" class="btn-primary" onclick="copyReferral()">Copy Link</button>
      </div>

      <div class="account-divider"></div>

      <div class="dash-share">
        <a target="_blank" rel="noopener" href="https://wa.me/?text=<?php echo urlencode($refLink); ?>"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
        <a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($refLink); ?>"><i class="fa-brands fa-facebook"></i> Facebook</a>
        <a target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($refLink); ?>"><i class="fa-brands fa-x-twitter"></i> X</a>
      </div>

      <div class="account-divider"></div>

      <div class="account-table-wrap">
        <table class="dash-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Date Joined</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($numRows > 0): ?>
              <?php $sn = 1; while ($ref = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?php echo $sn++; ?></td>
                  <td><?php echo htmlspecialchars($ref['fname']); ?></td>
                  <td><?php echo htmlspecialchars($ref['email']); ?></td>
                  <td><?php echo htmlspecialchars($ref['date']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="4" class="dash-empty">No referrals yet. Share your link to start building your network.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php
      $totalPages = max(1, (int)ceil($numRows / $limit));
      if ($totalPages > 1) {
          echo '<div class="account-note">Page ';
          for ($i = 1; $i <= $totalPages; $i++) {
              echo '<a class="auth-link" style="margin-right:0.75rem" href="?page=' . $i . '">' . $i . '</a>';
          }
          echo '</div>';
      }
      ?>
    </section>

    <aside class="account-panel account-panel-dark" style="grid-column: span 4;">
      <div class="account-panel-head">
        <div>
          <h3>Referral summary</h3>
          <span>Quick snapshot of your network.</span>
        </div>
      </div>

      <div class="dash-stats" style="grid-template-columns:1fr;">
        <div class="dash-stat" style="margin:0;">
          <div class="dash-stat-icon"><i class="fa-solid fa-user-group"></i></div>
          <div class="dash-stat-num"><?php echo $numRows; ?></div>
          <div class="dash-stat-label">Total referrals</div>
        </div>
      </div>

      <div class="account-divider" style="background:rgba(255,255,255,.12);"></div>

      <div class="account-note" style="color:var(--light-text);">
        Keep sharing your referral link consistently. The more active your network becomes, the more opportunities you create for future commission.
      </div>
    </aside>
  </div>
</div>

<script>
function copyReferral() {
  const input = document.getElementById('textToCopy');
  input.select();
  input.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(input.value);
}
</script>

<?php include "includes/footer2.php"; ?>
