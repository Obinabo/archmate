<?php
$qAdmin = "SELECT * FROM admin";
$stmtAdmin = mysqli_prepare($con, $qAdmin);
mysqli_stmt_execute($stmtAdmin);
$rAdmin = mysqli_stmt_get_result($stmtAdmin);
$adminNumRows = mysqli_num_rows($rAdmin);

$qAccount = "SELECT * FROM affiliate";
$stmtAccount = mysqli_prepare($con, $qAccount);
mysqli_stmt_execute($stmtAccount);
$rAccount = mysqli_stmt_get_result($stmtAccount);
$accountNumRows = mysqli_num_rows($rAccount);

$qQuote = "SELECT * FROM quotes";
$stmtQuote = mysqli_prepare($con, $qQuote);
mysqli_stmt_execute($stmtQuote);
$rQuote = mysqli_stmt_get_result($stmtQuote);
$quoteNumRows = mysqli_num_rows($rQuote);


$qCont = "SELECT * FROM contact";
$stmtCont = mysqli_prepare($con, $qCont);
mysqli_stmt_execute($stmtCont);
$rCont = mysqli_stmt_get_result($stmtCont);
$contNumRows = mysqli_num_rows($rCont);

$qPost = "SELECT * FROM posts";
$stmtPost = mysqli_prepare($con, $qPost);
mysqli_stmt_execute($stmtPost);
$rPost = mysqli_stmt_get_result($stmtPost);
$postNumRows = mysqli_num_rows($rPost);

$qProp = "SELECT * FROM properties";
$stmtProp = mysqli_prepare($con, $qProp);
mysqli_stmt_execute($stmtProp);
$rProp = mysqli_stmt_get_result($stmtProp);
$propNumRows = mysqli_num_rows($rProp);

$qSales = "SELECT * FROM affiliatesales";
$stmtSales = mysqli_prepare($con, $qSales);
mysqli_stmt_execute($stmtSales);
$rSales = mysqli_stmt_get_result($stmtSales);
$salesNumRows = mysqli_num_rows($rSales);

$qWithdraw = "SELECT * FROM withdraw";
$stmtWithdraw = mysqli_prepare($con, $qWithdraw);
mysqli_stmt_execute($stmtWithdraw);
$rWithdraw = mysqli_stmt_get_result($stmtWithdraw);
$withdrawNumRows = mysqli_num_rows($rWithdraw);

// $stat = "PENDING";
// $qWithdraw2 = "SELECT * FROM withdraw WHERE status = ?";
// $stmtWithdraw2 = mysqli_prepare($con, $qWithdraw2);
// mysqli_stmt_bind_param($stmtWithdraw2, 's', $stat);
// mysqli_stmt_execute($stmtWithdraw2);
// $rWithdraw2 = mysqli_stmt_get_result($stmtWithdraw2);
// $withdrawNumRows2 = mysqli_num_rows($rWithdraw2);

$ver = 'UNVERIFIED';
$qPend = "SELECT * FROM affiliate WHERE verified = ?";
$stmtPend = mysqli_prepare($con, $qPend);
mysqli_stmt_bind_param($stmtPend, 's', $ver);
mysqli_stmt_execute($stmtPend);
$rPend = mysqli_stmt_get_result($stmtPend);
$pendNumRows = mysqli_num_rows($rPend);

$paid = 'PENDING';
$qPaid = "SELECT * FROM affiliate WHERE paid = ?";
$stmtPaid = mysqli_prepare($con, $qPaid);
mysqli_stmt_bind_param($stmtPaid, 's', $paid);
mysqli_stmt_execute($stmtPaid);
$rPaid = mysqli_stmt_get_result($stmtPaid);
$paidNumRows = mysqli_num_rows($rPaid);

// $cat2 = 'FEATURED';
// $qFeatProp = "SELECT * FROM properties WHERE category = ?";
// $stmtFeatProp = mysqli_prepare($con, $qFeatProp);
// mysqli_stmt_bind_param($stmtFeatProp, 's', $cat2);
// mysqli_stmt_execute($stmtFeatProp);
// $rFeatProp = mysqli_stmt_get_result($stmtFeatProp);
// $featPropNumRows = mysqli_num_rows($rFeatProp);

// $active = 'ACTIVE';
// $qActive = "SELECT * FROM account WHERE status = ?";
// $stmtActive = mysqli_prepare($con, $qActive);
// mysqli_stmt_bind_param($stmtActive, 's', $active);
// mysqli_stmt_execute($stmtActive);
// $rActive = mysqli_stmt_get_result($stmtActive);
// $activeNumRows = mysqli_num_rows($rActive);


// $susp = 'SUSPENDED';
// $qSusp = "SELECT * FROM account WHERE status = ?";
// $stmtSusp = mysqli_prepare($con, $qSusp);
// mysqli_stmt_bind_param($stmtSusp, 's', $susp);
// mysqli_stmt_execute($stmtSusp);
// $rSusp = mysqli_stmt_get_result($stmtSusp);
// $suspNumRows = mysqli_num_rows($rSusp);

?>