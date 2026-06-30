<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";

if (!isset($_SESSION['id'])) {
    redirect('index.php');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
  $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id) {
  redirect('index.php');
}

$title = 'View Account | ' . SITE_NAME;
include "includes/head.php";

$q = "SELECT * FROM affiliate WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($con, $q);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
$row = $r ? mysqli_fetch_assoc($r) : null;

if (!$row) {
    ?>
    <div class="adm-pagehead">
      <div>
        <div class="adm-eyebrow">Realtor Profile</div>
        <h1 class="adm-pagehead__title">Account <em>not found</em></h1>
        <p class="adm-pagehead__lede">The requested account could not be loaded.</p>
      </div>
      <a href="./realtors" class="adm-btn adm-btn--ghost">Back to Realtors</a>
    </div>
    <div class="adm-empty">
      <h4>No account matches this record.</h4>
    </div>
    <?php
    include "includes/foot.php";
    exit;
}

$userUplink = $row['uplink'] ?? '';
$ref_id = $row['ref_id'] ?? '';
$paidStatus = strtoupper(trim((string)($row['paid'] ?? '')));
$typeStatus = strtoupper(trim((string)($row['type'] ?? '')));
$paymentProof = trim((string)($row['payment_upload'] ?? ''));
$upgradeProof = trim((string)($row['upgrade_upload'] ?? ''));
$paymentProofSrc = $paymentProof !== '' ? '../' . ltrim($paymentProof, '/') : '';
$upgradeProofSrc = $upgradeProof !== '' ? '../' . ltrim($upgradeProof, '/') : '';

$uplink_fname = '';
if (!empty($userUplink)) {
    $q3 = "SELECT fname FROM affiliate WHERE ref_id = ? LIMIT 1";
    $stmt3 = mysqli_prepare($con, $q3);
    mysqli_stmt_bind_param($stmt3, 's', $userUplink);
    mysqli_stmt_execute($stmt3);
    $r3 = mysqli_stmt_get_result($stmt3);
    if ($row3 = mysqli_fetch_assoc($r3)) {
        $uplink_fname = $row3['fname'];
    }
}

$q4 = "SELECT id, fname, email FROM affiliate WHERE uplink = ? ORDER BY id";
$stmt4 = mysqli_prepare($con, $q4);
mysqli_stmt_bind_param($stmt4, 's', $ref_id);
mysqli_stmt_execute($stmt4);
$r4 = mysqli_stmt_get_result($stmt4);
$refNumRows = $r4 ? mysqli_num_rows($r4) : 0;

$verBadge = strtoupper((string)($row['verified'] ?? '')) === 'VERIFIED' ? 'adm-badge--ok' : 'adm-badge--warn';
$paidBadge = $paidStatus === 'PAID' ? 'adm-badge--ok' : 'adm-badge--warn';
$typeBadge = $typeStatus === 'GOLD' ? 'adm-badge--ok' : 'adm-badge--warn';
?>

<div class="adm-pagehead">
  <div>
    <div class="adm-eyebrow">Realtor Profile</div>
    <h1 class="adm-pagehead__title">View <em>account</em></h1>
    <p class="adm-pagehead__lede">Inspect the realtor details, review the account status, and examine the uploaded payment proof from one screen.</p>
  </div>
  <a href="./realtors" class="adm-btn adm-btn--ghost">Back to Realtors</a>
</div>

<div class="adm-kpis">
  <div class="adm-kpi">
    <div class="adm-kpi__label">Account Type</div>
    <div class="adm-kpi__value"><?php echo htmlspecialchars($row['type'] ?? ''); ?></div>
    <i class="fa-solid fa-layer-group adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi">
    <div class="adm-kpi__label">Account Activation</div>
    <div class="adm-kpi__value"><?php echo htmlspecialchars($row['paid'] ?? ''); ?></div>
    <i class="fa-solid fa-money-check-dollar adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi adm-kpi--accent">
    <div class="adm-kpi__label">Referrals</div>
    <div class="adm-kpi__value"><?php echo (int)$refNumRows; ?></div>
    <i class="fa-solid fa-users adm-kpi__icon"></i>
  </div>
</div>

<div class="adm-grid-2">
  <section class="adm-card">
    <div class="adm-card__title">Account details</div>
    <div style="display:flex; flex-flow: row wrap; align-items:start;">
      <div style="border-radius:18px;overflow:hidden;background:var(--adm-paper);border:1px solid var(--adm-line);aspect-ratio:1/1;width:100%;">
        <img src="../<?php echo htmlspecialchars($row['pic']); ?>" alt="Profile picture of <?php echo htmlspecialchars($row['fname']); ?>" style="width:100%;height:100%;object-fit:cover;display:block;">
      </div>

      <div style="display:grid; gap:14px; width: 100%; margin-top:14px;">
        <div class="adm-table-wrap">
          <table class="adm-table" style="min-width:0;">
            <tbody>
              <tr><td>Name of Realtor</td><td><?php echo htmlspecialchars($row['fname'] ?? ''); ?></td></tr>
              <tr><td>Email</td><td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td></tr>
              <tr><td>Username</td><td><?php echo htmlspecialchars($row['uname'] ?? ''); ?></td></tr>
              <tr><td>Phone</td><td><?php echo htmlspecialchars($row['phone'] ?? ''); ?></td></tr>
              <tr><td>Gender</td><td><?php echo htmlspecialchars($row['gender'] ?? ''); ?></td></tr>
              <tr><td>Occupation</td><td><?php echo htmlspecialchars($row['work'] ?? ''); ?></td></tr>
              <tr><td>Address</td><td><?php echo htmlspecialchars($row['address'] ?? ''); ?></td></tr>
              <tr><td>Uplink</td><td><?php echo htmlspecialchars($uplink_fname !== '' ? $uplink_fname : 'No uplink found'); ?></td></tr>
            </tbody>
          </table>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:8px;">
          <span class="adm-badge <?php echo $verBadge; ?>"><?php echo htmlspecialchars($row['verified'] ?? ''); ?></span>
          <span class="adm-badge <?php echo $paidBadge; ?>"><?php echo htmlspecialchars($row['paid'] ?? ''); ?></span>
          <span class="adm-badge <?php echo $typeBadge; ?>"><?php echo htmlspecialchars($row['type'] ?? ''); ?></span>
        </div>
      </div>
    </div>
  </section>

    <aside class="adm-card adm-card--dark adm-card--note">
    <span class="adm-card__chip">Payment proof</span>
    <h3>Inspect uploaded payment proof</h3>
    <p style="color:rgba(234,243,250,.8);margin:0 0 18px;line-height:1.6;">This panel displays any uploaded payment-related proofs using the same relative path pattern as the profile image.</p>

    <?php if (!empty($paymentProof) || !empty($upgradeProof)): ?>
        <div class="adm-media-preview">
          <p class="adm-media-preview__label">Uploaded proofs</p>
          <div class="adm-media-preview__grid" style="grid-template-columns:repeat(2, minmax(0, 1fr));">
            <?php if (!empty($paymentProof)): ?>
              <div class="adm-media-thumb" style="aspect-ratio:4/5;">
                <img src="<?php echo htmlspecialchars($paymentProofSrc); ?>" alt="Payment proof for <?php echo htmlspecialchars($row['fname']); ?>">
                <span class="adm-media-thumb__badge">Account Activation</span>
              </div>
            <?php endif; ?>
            <?php if (!empty($upgradeProof)): ?>
              <div class="adm-media-thumb" style="aspect-ratio:4/5;">
                <img src="<?php echo htmlspecialchars($upgradeProofSrc); ?>" alt="Upgrade proof for <?php echo htmlspecialchars($row['fname']); ?>">
                <span class="adm-media-thumb__badge">Account Upgrade</span>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div style="margin-top:14px;display:grid;gap:8px;">
          <?php if (!empty($paymentProof)): ?>
            <p style="margin:0;color:rgba(234,243,250,.82);font-size:13px;line-height:1.6;word-break:break-word;">
              Activation Payment: <?php echo htmlspecialchars(basename($paymentProof)); ?>
            </p>
          <?php endif; ?>
          <?php if (!empty($upgradeProof)): ?>
            <p style="margin:0;color:rgba(234,243,250,.82);font-size:13px;line-height:1.6;word-break:break-word;">
              Upgrade Payment: <?php echo htmlspecialchars(basename($upgradeProof)); ?>
            </p>
          <?php endif; ?>
        </div>
    <?php else: ?>
      <div class="adm-alert adm-alert--success">
        No payment proof has been uploaded for this account yet.
      </div>
    <?php endif; ?>
  </aside>
</div>

<div class="adm-grid-2" style="margin-top:18px;">
  <section class="adm-card">
    <div class="adm-card__title">Bank & wallet details</div>
    <div class="adm-table-wrap">
      <table class="adm-table" style="min-width:0;">
        <tbody>
          <tr><td>Bank</td><td><?php echo htmlspecialchars($row['bank'] ?? ''); ?></td></tr>
          <tr><td>Account Name</td><td><?php echo htmlspecialchars($row['acct_name'] ?? ''); ?></td></tr>
          <tr><td>Account Number</td><td><?php echo htmlspecialchars($row['acct_no'] ?? ''); ?></td></tr>
          <tr><td>Total Earnings</td><td><?php echo htmlspecialchars($row['earnings'] ?? ''); ?></td></tr>
          <tr><td>Total Withdrawal</td><td><?php echo htmlspecialchars($row['withdrawal'] ?? ''); ?></td></tr>
          <tr><td>Total Balance</td><td><?php echo htmlspecialchars($row['balance'] ?? ''); ?></td></tr>
        </tbody>
      </table>
    </div>
  </section>

  <aside class="adm-card">
    <div class="adm-card__title">Referrals</div>
    <?php if ($refNumRows > 0): ?>
      <div class="adm-table-wrap">
        <table class="adm-table" style="min-width:0;">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody>
            <?php $sn = 1; while ($row2 = mysqli_fetch_assoc($r4)): ?>
              <tr>
                <td><?php echo (int)$sn; ?></td>
                <td><?php echo htmlspecialchars($row2['fname']); ?></td>
                <td><?php echo htmlspecialchars($row2['email']); ?></td>
              </tr>
            <?php $sn++; endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="adm-empty">
        <h4>No referrals yet.</h4>
      </div>
    <?php endif; ?>
  </aside>
</div>

<?php include "includes/foot.php"; ?>
