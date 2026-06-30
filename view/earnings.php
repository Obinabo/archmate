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

$title='Earnings | '.SITE_NAME; $pageTitle='Earnings';
include "includes/header2.php"; include "includes/nav-menu.php";

$limit = 10;
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$offset = ($page-1)*$limit;
$status='APPROVED';
$stmt1 = mysqli_prepare($con,"SELECT * FROM affiliatesales WHERE email = ? AND status = ? ORDER BY id DESC LIMIT $limit OFFSET $offset");
mysqli_stmt_bind_param($stmt1,'ss',$email,$status); mysqli_stmt_execute($stmt1);
$r1 = mysqli_stmt_get_result($stmt1);
?>

<div class="dash-hero">
  <div>
    <div class="section-label"><div class="section-label-line"></div><span>Income Stream</span></div>
    <h1 class="dash-hero-title">Your <em>earnings</em></h1>
    <p class="dash-hero-sub">Every approved sale here is 10% direct commission paid to your wallet.</p>
  </div>
</div>

<section class="dash-panel">
  <div class="dash-table-wrap">
    <table class="dash-table">
      <thead><tr><th>#</th><th>Property</th><th>Amount Sold</th><th>Commission</th><th>Date</th></tr></thead>
      <tbody>
        <?php
          $sn = $offset+1; $any=false;
          if ($r1) while ($s = mysqli_fetch_assoc($r1)) { $any=true;
            echo '<tr>
              <td>'.$sn++.'</td>
              <td>'.htmlspecialchars($s['title'] ?? $s['property'] ?? '—').'</td>
              <td>&#8358;'.number_format((float)($s['amount'] ?? 0)).'</td>
              <td><strong>&#8358;'.number_format((float)($s['commission'] ?? 0)).'</strong></td>
              <td>'.htmlspecialchars($s['date'] ?? '').'</td>
            </tr>';
          }
          if (!$any) echo '<tr><td colspan="5" class="dash-empty">No earnings yet.</td></tr>';
        ?>
      </tbody>
    </table>
  </div>
</section>

<?php include "includes/footer2.php"; ?>
