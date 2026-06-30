<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "../phpmailer/mailer.phpif (isset($_SESSION['uname'])) {
    $uname = $_SESSION['uname'];
} else {
    redirect('./affiliate');
}

$stmt = mysqli_prepare($con, "SELECT * FROM affiliate WHERE uname = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $uname);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = $result ? mysqli_fetch_assoc($result) : null;

if (!$row || ($row['verified'] ?? '') === 'UNVERIFIED') {
    redirect('./affiliate');
}

$email = $row['email'] ?? '';
$successmsg = '';
$msg = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paid'])) {
    $paid = 'PENDING';
    if (empty($_FILES['file']['name'])) {
        $msg[] = '<div class="alert error">Please choose a profile image.</div>';
    } else {
        $temp = $_FILES['file']['tmp_name'];
        $upload_dir = "uploads/";
        $file_name = basename($_FILES['file']['name']);
        $file_ext = explode('.', $file_name);
        $ext = strtolower(end($file_ext));
        $allowed = ['jpeg', 'jpg', 'png'];

        if (!in_array($ext, $allowed, true)) {
            $msg[] = '<div class="alert error">Please select a PNG or JPG image.</div>';
        } elseif (uploadPayment($con, $email, $upload_dir, $temp, $ext) === TRUE) {
            $stmt2 = mysqli_prepare($con, "UPDATE affiliate SET paid = ? WHERE uname = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt2, 'ss', $paid, $uname);
            mysqli_stmt_execute($stmt2);

            $updateChanged = mysqli_stmt_affected_rows($stmt2);

            $subject = 'Confirm Payment by Realtor';
            $mail = '
                <html>
                    <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta http-equiv="x-ua-compatible" content="ie=edge">
                        <style>
                            html {background-color: rgb(206, 202, 202);}
                            body{font-family: Arial, Helvetica, sans-serif; font-size: 1em; background-color: rgb(252, 252, 252); line-height: 1.5; margin: 0 auto 0 auto; width: 100%;}
                            .header{background-color: rgb(37, 72, 175); padding-top: 20px; padding: 20px; text-align:center; display:flex;}
                            .container{padding: 10px; border-color: rgb(8, 102, 165); width: 100%; align-items: center;}
                            .footer{background-color: rgb(37, 72, 175); margin: 30px auto 0px auto; padding: 5px; -moz-box-align: center; -webkit-box-align: center; color: rgb(243, 146, 0);}
                            p {text-align: center; font-size: 1em}
                            h1{font-size: 2em; color: rgb(37, 72, 175); font-weight: bolder;}
                            h2{font-size: 1.5em; color: rgb(37, 72, 175); font-weight: bolder;}
                            .footer>.list{text-align: center; font-size: 0.7em; margin-top: 20px; padding: 20px; border-top: 1px solid rgb(201, 199, 199);}
                            .box1{margin-right: 20%;}
                            .box{width: 100%; flex-direction: column;}
                            #logo{width: 30%;}
                            a{color: #fff; text-decoration: none;}
                            a:visited{color:rgb(243, 146, 0);}
                            a:active{color:rgb(243, 146, 0)}
                            a:hover{color: #f39200;}
                            .button{
                                padding: 10px;
                                background-color: rgb(37, 72, 175);
                                color:rgb(255, 255, 255);
                                width:fit-content;
                                margin: 20px auto;
                                transition: 1s;
                                border-radius: 20px;
                            }
                            .button:hover{
                                background-color: rgb(16, 48, 143);
                                margin: 20px auto;
                            }
                            img{padding: 10px; box-shadow: -5px 5px 10px rgba(71, 71, 71, 0); margin: 5px;}
                            .text-black{color: rgb(27, 27, 27)}
                            .text-white{color: rgb(253, 252, 252)}
                            .text-bold{font-weight: bold;}
                        .footer>p{font-size: 0.8em;}
                            .welcome{padding: auto; margin: auto; box-shadow: -5px 5px 10px rgba(71, 71, 71, 0); width: 80%;}
                        </style>
                    </head>
                    <body>
                        <header>
                            <div class="header">
                                <div class="box"><a href="'.URL.'">Home</a></div>
                                </div>
                        </header>
                        <center>
                            <div id="logo"><a href="'.URL.'"><img src="'.URL.'/assets/img/archmate-logo.png" width="120px" height="40px" alt="logo" /></a></div>
                            <h1>Hello Admin,</h1>
                            <p> A realtor with username '.$row['uname'].' has made an account activation payment. Please visit the admin dashboard to verify this payment.</p>
                            <p>Thank you.</p>
                            </div>
                        </center>
                        <footer>
                            <div class="footer text-white">
                                <p class="text-bold">Address: '.ADDRESS.'.</p>
                                <!--<p class="text-bold">Phone: </p>-->
                                <p class="text-bold">Support Email:'.SITE_EMAIL.'</p>

                                <p>Kind Regards, '.SITE_TITLE.'</p>
                                <div class="list ">
                                    <p>'.SITE_TITLE.' Copyriight &#169; 2023</p>
                                </div>
                            </div>
                        </footer>
                    </body>
                </html>
            ';
            sendEmail(SITE_EMAIL, $subject, $mail);

            if ($updateChanged === 1) {
                $successmsg = '<div class="success alert">Thanks for your payment '.$row['uname'].', You\'ll be notified by mail when it is confirmed.</div>';
            } else {
                $successmsg = '<div class="success alert">Thanks for your payment '.$row['uname'].', your proof has been uploaded and your account is already pending review.</div>';
            }
        } else {
            $msg[] = '<div class="alert error">Failed to upload profile picture.</div>';
        }
    }
}

$title = 'Activate Account | '.SITE_NAME;
$pageTitle = 'Activate Account';
include "includes/header2.php";
?>

<div class="auth-shell">
  <aside class="auth-side">
    <a href="./" class="auth-brand"><div class="auth-brand-mark">AM</div><span>Arch-Mate</span></a>
    <div class="auth-side-inner">
      <div class="section-label" style="margin-bottom:1.5rem"><div class="section-label-line"></div><span>One last step</span></div>
      <h1 class="auth-hero-title">Activate &amp; <em>start earning</em></h1>
      <p class="auth-hero-sub">A one-time activation fee unlocks your dashboard, referral link, and access to every commission tier.</p>
      <ul class="auth-bullets">
        <li><i class="fa-solid fa-check"></i> Lifetime account access</li>
        <li><i class="fa-solid fa-check"></i> Personal referral link</li>
        <li><i class="fa-solid fa-check"></i> Sales &amp; earnings tracker</li>
      </ul>
    </div>
    <div class="auth-side-foot">&copy; <?php echo date('Y'); ?> Arch-Mate.</div>
  </aside>

  <main class="auth-main">
    <div class="auth-card">
      <div class="auth-card-head">
        <h2>Hello, <?php echo htmlspecialchars($row['fname']); ?></h2>
        <p>Pay the activation fee to <strong><?php echo defined('SITE_BANK') ? SITE_BANK : 'the account below'; ?></strong> and click the button to confirm.</p>
      </div>

      <?php
        if (!empty($msg)) {
            echo implode('<br/>', $msg);
        }
        if (isset($successmsg)) {
            echo $successmsg;
        }
      ?>

      <div class="ps-bank">
        <div class="ps-bank-row"><span>Bank Name</span><strong><?php echo defined('SITE_BANK_NAME') ? SITE_BANK_NAME : 'Contact admin'; ?></strong></div>
        <div class="ps-bank-row"><span>Account Number</span><strong><?php echo defined('SITE_BANK_NO') ? SITE_BANK_NO : 'â€”'; ?></strong></div>
        <div class="ps-bank-row"><span>Account Name</span><strong><?php echo defined('SITE_NAME') ? SITE_NAME : 'â€”'; ?></strong></div>
        <div class="ps-bank-row"><span>Activation Fee</span><strong style="color:var(--cyan)"><?php echo defined('SITE_FEE') ? SITE_FEE : 'â‚¦5,000'; ?></strong></div>
></div>
        <div class="ps-bank-row"><span>Activation Fee</span><strong style="color:var(--cyan)"><?php echo defined('SITE_FEE')?SITE_FEE:'₦5,000'; ?></strong></div>
      </div>

      <form method="POST" enctype="multipart/form-data" class="auth-form">
        <input type="hidden" name="paid" value="1"/>
        <label class="confirm-payment">
          <span>Upload Payment Proof</span>
          <input type="file" name="file" />
        </label>
        <button type="submit" class="btn-primary auth-submit">I have made the payment</button>
      </form>
      <a href="./affiliate" class="btn-outline auth-back">&larr; Sign in instead</a>
    </div>
  </main>
</div>

</body>
</html>
