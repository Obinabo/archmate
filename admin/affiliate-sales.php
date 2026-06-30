<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";

if (!isset($_SESSION['id'])) {
    redirect('index.php');
}

$title = 'Sales by Realtors | ' . SITE_NAME;
include "includes/head.php";

$limit = 12;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$qSales = "
    SELECT a.id, a.email, a.title, a.payment, a.commission, a.img, a.status, a.date,
           af.fname
    FROM affiliatesales a
    LEFT JOIN affiliate af ON af.email = a.email
    ORDER BY a.date DESC
    LIMIT $limit OFFSET $offset
";
$stmtSales = mysqli_prepare($con, $qSales);
mysqli_stmt_execute($stmtSales);
$r = mysqli_stmt_get_result($stmtSales);
$numRows = $r ? mysqli_num_rows($r) : 0;

$qPending = "SELECT COUNT(*) AS total FROM affiliatesales WHERE status = 'PENDING'";
$rPending = mysqli_query($con, $qPending);
$pendingRow = $rPending ? mysqli_fetch_assoc($rPending) : [];
$pendingRows = (int)($pendingRow['total'] ?? 0);

$qCommission = "SELECT COALESCE(SUM(commission), 0) AS total FROM affiliatesales";
$rCommission = mysqli_query($con, $qCommission);
$commissionRow = $rCommission ? mysqli_fetch_assoc($rCommission) : [];
$commissionTotal = (float)($commissionRow['total'] ?? 0);
?>

<div class="adm-pagehead">
  <div>
    <div class="adm-eyebrow">Realtor Sales</div>
    <h1 class="adm-pagehead__title">Sales <em>submitted</em> by realtors</h1>
    <p class="adm-pagehead__lede">Review uploaded sales, confirm the details, and open the approval screen when a record is ready to move forward.</p>
  </div>
  <a href="./withdraw" class="adm-btn adm-btn--ghost">View withdrawals</a>
</div>

<div class="adm-kpis">
  <div class="adm-kpi">
    <div class="adm-kpi__label">Total sales</div>
    <div class="adm-kpi__value"><?php echo (int)$salesNumRows; ?></div>
    <i class="fa-solid fa-chart-line adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi">
    <div class="adm-kpi__label">Pending review</div>
    <div class="adm-kpi__value"><?php echo $pendingRows; ?></div>
    <i class="fa-solid fa-hourglass-half adm-kpi__icon"></i>
  </div>
  <div class="adm-kpi adm-kpi--accent">
    <div class="adm-kpi__label">Commission total</div>
    <div class="adm-kpi__value">&#8358;<?php echo number_format($commissionTotal); ?></div>
    <i class="fa-solid fa-coins adm-kpi__icon"></i>
  </div>
</div>

<div class="adm-salegrid">
  <?php if ($numRows > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($r)): ?>
      <article class="adm-sale">
        <div class="adm-sale__media" style="background-image:url('../<?php echo htmlspecialchars($row['img']); ?>')"></div>
        <div class="adm-sale__body">
          <span class="adm-badge <?php echo strtolower($row['status']) === 'approved' ? 'adm-badge--ok' : 'adm-badge--warn'; ?>">
            <?php echo htmlspecialchars($row['status']); ?>
          </span>
          <h3 class="adm-sale__title"><?php echo htmlspecialchars($row['title']); ?></h3>
          <dl class="adm-sale__meta">
            <div>
              <dt>Sold by</dt>
              <dd><?php echo htmlspecialchars($row['fname'] ?: $row['email']); ?></dd>
            </div>
            <div>
              <dt>Payment</dt>
              <dd>&#8358;<?php echo number_format((float)$row['payment']); ?></dd>
            </div>
            <div>
              <dt>Commission</dt>
              <dd>&#8358;<?php echo number_format((float)$row['commission']); ?></dd>
            </div>
            <div>
              <dt>Date</dt>
              <dd><?php echo htmlspecialchars($row['date']); ?></dd>
            </div>
          </dl>
          <a href="./pending-sales?id=<?php echo urlencode($row['id']); ?>" class="adm-btn adm-btn--primary adm-btn--block">Approve / Reject</a>
        </div>
      </article>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="adm-empty" style="grid-column:1/-1;">
      <h4>No sales yet</h4>
      <p>Sales submitted by realtors will appear here once they come in.</p>
    </div>
  <?php endif; ?>
</div>

<?php
$totalPages = max(1, (int)ceil($salesNumRows / $limit));
if ($totalPages > 1) {
    echo '<div class="adm-pager">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<a class="adm-pager__link" href="?page=' . $i . '">' . $i . '</a>';
    }
    echo '</div>';
}
?>

<?php include "includes/foot.php"; ?>
