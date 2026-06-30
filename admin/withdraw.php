<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";

if (!isset($_SESSION['id'])) {
    redirect('index.php');
}

$title = 'Withdrawals | ' . SITE_NAME;
include "includes/head.php";

$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$qWithdraw = "SELECT * FROM withdraw ORDER BY date DESC LIMIT $limit OFFSET $offset";
$stmtWithdraw = mysqli_prepare($con, $qWithdraw);
mysqli_stmt_execute($stmtWithdraw);
$r = mysqli_stmt_get_result($stmtWithdraw);
$numRows = $r ? mysqli_num_rows($r) : 0;

$limit2 = 10;
$page2 = max(1, (int)($_GET['page2'] ?? 1));
$offset2 = ($page2 - 1) * $limit2;
$stat = 'PENDING';
$qWithdraw2 = "SELECT * FROM withdraw WHERE status = ? ORDER BY date DESC LIMIT $limit2 OFFSET $offset2";
$stmtWithdraw2 = mysqli_prepare($con, $qWithdraw2);
mysqli_stmt_bind_param($stmtWithdraw2, 's', $stat);
mysqli_stmt_execute($stmtWithdraw2);
$r2 = mysqli_stmt_get_result($stmtWithdraw2);
$numRows2 = $r2 ? mysqli_num_rows($r2) : 0;

$qPendingCount = "SELECT COUNT(*) AS total FROM withdraw WHERE status = 'PENDING'";
$rPendingCount = mysqli_query($con, $qPendingCount);
$pendingCountRow = $rPendingCount ? mysqli_fetch_assoc($rPendingCount) : [];
$pendingTotal = (int)($pendingCountRow['total'] ?? 0);
?>

<div class="adm-pagehead">
  <div>
    <div class="adm-eyebrow">Payouts</div>
    <h1 class="adm-pagehead__title">Review <em>withdrawals</em></h1>
    <p class="adm-pagehead__lede">Track all payout requests and keep the pending queue easy to scan and process.</p>
  </div>
  <a href="./dashboard" class="adm-btn adm-btn--ghost">Back to dashboard</a>
</div>

<div class="adm-kpis">
  <div class="adm-kpi">
    <div class="adm-kpi__label">All withdrawals</div>
    <div class="adm-kpi__value"><?php echo (int)$withdrawNumRows; ?></div>
    <i class="fa-solid fa-wallet adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi">
    <div class="adm-kpi__label">Pending queue</div>
    <div class="adm-kpi__value"><?php echo $pendingTotal; ?></div>
    <i class="fa-solid fa-clock-rotate-left adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi adm-kpi--accent">
    <div class="adm-kpi__label">Review first</div>
    <div class="adm-kpi__value">PENDING</div>
    <i class="fa-solid fa-triangle-exclamation adm-kpi__icon"></i>
  </div>
</div>

<div class="adm-grid-2">
  <section class="adm-card">
    <div class="adm-card__title">All withdrawal requests</div>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Email</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($numRows > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($r)): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>&#8358;<?php echo number_format((float)$row['amount']); ?></td>
                <td>
                  <span class="adm-badge <?php echo strtolower($row['status']) === 'pending' ? 'adm-badge--warn' : 'adm-badge--ok'; ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4"><div class="adm-empty"><h4>No withdrawals yet.</h4></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php
    $totalPages = max(1, (int)ceil($withdrawNumRows / $limit));
    if ($totalPages > 1) {
        echo '<div class="adm-pager">';
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a class="adm-pager__link" href="?page=' . $i . '">' . $i . '</a>';
        }
        echo '</div>';
    }
    ?>
  </section>

  <aside class="adm-card adm-card--dark adm-card--note">
    <span class="adm-card__chip">Pending queue</span>
    <h3>Requests waiting for action</h3>
    <p style="color:rgba(234,243,250,.8);margin:0 0 18px;line-height:1.6;">Approve or reject requests from the pending list. Keep an eye on the balance before confirming any payout.</p>

    <div class="adm-table-wrap" style="box-shadow:none;border-color:rgba(255,255,255,.08);">
      <table class="adm-table" style="min-width:0;">
        <thead>
          <tr>
            <th>Email</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($numRows2 > 0): ?>
            <?php while ($row2 = mysqli_fetch_assoc($r2)): ?>
              <tr>
                <td><?php echo htmlspecialchars($row2['email']); ?></td>
                <td>&#8358;<?php echo number_format((float)$row2['amount']); ?></td>
                <td><a class="adm-chip adm-chip--edit" href="./pending-withdraw?id=<?php echo urlencode($row2['id']); ?>">Review</a></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="3"><div class="adm-empty"><h4>No pending withdrawals.</h4></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php
    $totalPages2 = max(1, (int)ceil($pendingTotal / $limit2));
    if ($totalPages2 > 1) {
        echo '<div class="adm-pager">';
        for ($i = 1; $i <= $totalPages2; $i++) {
            echo '<a class="adm-pager__link" href="?page2=' . $i . '">' . $i . '</a>';
        }
        echo '</div>';
    }
    ?>
  </aside>
</div>

<?php include "includes/foot.php"; ?>
