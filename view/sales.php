<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";

if (isset($_SESSION['email'])) { $email = $_SESSION['email']; } else { redirect('./affiliate'); }
$stmt = mysqli_prepare($con, "SELECT * FROM affiliate WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $email); mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if ($row['verified']==='UNVERIFIED') redirect('./affiliate');
elseif ($row['verified']==='VERIFIED' && $row['paid']==='UNPAID') redirect('./payment-status');

$title='Sales | '.SITE_NAME; $pageTitle='Sales';
include "includes/header2.php"; include "includes/nav-menu.php";

$stmt1 = mysqli_prepare($con,"SELECT * FROM affiliatesales WHERE email = ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt1,'s',$email); mysqli_stmt_execute($stmt1);
$r1 = mysqli_stmt_get_result($stmt1);
?>
<div class="dash-hero">
  <div>
    <div class="section-label"><div class="section-label-line"></div><span>Activity</span></div>
    <h1 class="dash-hero-title">All <em>sales</em></h1>
    <p class="dash-hero-sub">Track every transaction — pending, approved or declined.</p>
  </div>
  <a href="./register-sale" class="btn-primary">Log a New Sale</a>
</div>

<section class="dash-panel">
  <div class="dash-table-wrap">
    <table class="dash-table">
      <thead><tr><th>#</th><th>Property</th><th>Buyer</th><th>Amount</th><th>Commission</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php
          $sn=1; $any=false;
          if ($r1) while ($s = mysqli_fetch_assoc($r1)) { $any=true; $st=strtolower($s['status']);
            echo '<tr>
              <td>'.$sn++.'</td>
              <td>'.htmlspecialchars($s['title'] ?? $s['property'] ?? '—').'</td>
              <td>'.htmlspecialchars($s['buyer'] ?? '—').'</td>
              <td>&#8358;'.number_format((float)($s['amount'] ?? 0)).'</td>
              <td>&#8358;'.number_format((float)($s['commission'] ?? 0)).'</td>
              <td><span class="dash-badge dash-badge-'.$st.'">'.htmlspecialchars($s['status']).'</span></td>
              <td>'.htmlspecialchars($s['date'] ?? '').'</td>
              <td><a href="./delete-sale?id='.(int)$s['id'].'" class="dash-action-del"><i class="fa-solid fa-trash"></i></a></td>
            </tr>';
          }
          if (!$any) echo '<tr><td colspan="8" class="dash-empty">No sales recorded yet.</td></tr>';
        ?>
      </tbody>
    </table>
  </div>
</section>

<?php include "includes/footer2.php"; ?>
