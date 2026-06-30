<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "../phpmailer/mailer.php";
$title = "Arch-Mate Realtor | Create Account";

$uplink = isset($_GET['ref']) ? $_GET['ref'] : (isset($_POST['ref']) ? mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['ref']))) : NULL);

if (!isset($_SESSION['turningImg'])) {
    $_SESSION['turningImg'] = substr(date('ym') * rand(2000,5000), 0, 6);
}
$turningImg = $_SESSION['turningImg'];

$msg = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $msg = array();
      if(empty($_POST['fname'])){
          $msg[] = '<div class="error">Please enter your fullname</div>';
      }else{
          $fname = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['fname'])));
      }
      if(empty($_POST['uname'])){
          $msg[] = '<div class="error">Please enter your username</div>';
      }else{
          $uname = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['uname'])));
      }
      if(empty($_POST['email'])){
          $msg[] = '<div class="error">Please enter your email address</div>';
      }else{
          $email = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['email'])));
      }
      if(empty($_POST['pass']) || empty($_POST['pass2'])){
          $msg[] = '<div class="error">Please enter your password</div>';
      }elseif($_POST['pass'] != $_POST['pass2']){
          $msg[] = '<div class="error">Entered Passwords does not match</div>';
      }else{
          $pass = mysqli_real_escape_string($con, trim(htmlspecialchars(password_hash($_POST['pass'], PASSWORD_DEFAULT))));
      }
      if(empty($_POST['phone'])){
          $msg[] = '<div class="error">Please enter your phone number</div>';
      }else{
          $phone = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['phone'])));
      }
      if(empty($_POST['work'])){
          $msg[] = '<div class="error">Please enter your date of birth</div>';
      }else{
          $work = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['work'])));
      }
      if(empty($_POST['address'])){
          $msg[] = '<div class="error">Please enter your address</div>';
      }else{
          $addr = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['address'])));
      }
      if(empty($_POST['sex'])){
          $msg[] = '<div class="error">Please choose your sex</div>';
      }else{
          $sex = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['sex'])));
      }
      if(empty($_POST['bank'])){
          $msg[] = '<div class="error">Please enter your bank name</div>';
      }else{
          $bank = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['bank'])));
      }
      if(empty($_POST['acct_no'])){
          $msg[] = '<div class="error">Please enter your account number</div>';
      }else{
          $acct_no = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['acct_no'])));
      }
      if(empty($_POST['acct_name'])){
          $msg[] = '<div class="error">Please enter your account name</div>';
      }else{
          $acct_name = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['acct_name'])));
      }
      // Validate bot protection code
      if ($_POST['turn'] !== $_SESSION['turningImg']) {
          $msg[] = '<div class="error">Invalid validation code.</div>';
      }
      // $ref_id = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['ref_id'])));
      $type = 'AFFILIATE';
      $verified = "UNVERIFIED";
      $earnings = 0;
      $withdrawal = 0;
      $balance = 0;
      $paid = "UNPAID";
      $ref_id = 'ARCH'.substr(date("Hid") * rand(1000, 5000), 0, 4);
      $pic = 'view/uploads/user.png';
      $date = date("d/m/Y");
      $unique_string = md5(uniqid(rand(), true));
      $activation_link = URL.'/verify?x='.urlencode($email).'&y='.$unique_string;
      if(empty($msg)){
          $successmsg = '';
          if(checkUser($con, $email, $uname, $phone) !== TRUE){
              if (createUser($con, $uname, $fname, $email, $pic, $phone, $addr, $sex, $work, $acct_no, $acct_name, $bank, $ref_id, $uplink, $pass, $verified, $paid, $earnings, $withdrawal, $balance, $type, $date) === TRUE) {
                  $subject = 'Confirm Email To Complete Registration';
                  $mail = '
                  <html>
                      <head>
                          <meta charset="utf-8">
                      
                          <meta name="viewport" content="width=device-width, initial-scale=1.0">
                          <meta http-equiv="x-ua-compatible" content="ie=edge">
                          <style>
                              body{font-family: Arial, Helvetica, sans-serif; font-size: 1em; line-height: 1.5; margin: 0 auto 0 auto; width: 100%;}
                              .header{background-color: #01b5ec; padding-top: 20px; padding: 20px; text-align:center; display:flex; color: #000}
                              .container{padding: 10px; border-color: rgb(8, 102, 165); width: 100%; align-items: center;}
                              .footer{background-color: #01b5ec; margin: 30px auto 0px auto; padding: 5px; -moz-box-align: center; -webkit-box-align: center; color: rgb(243, 146, 0); }
                              p {text-align: center; font-size: 1em}
                              h1{font-size: 2em; color: #01b5ec; font-weight: bolder;}
                              h2{font-size: 1.5em; color: #01b5ec; font-weight: bolder;}
                              .footer>.list{text-align: center; font-size: 0.7em; margin-top: 20px; padding: 20px; border-top: 1px solid rgb(201, 199, 199);}
                              .box1{margin-right: 20%;}
                              .box{width: 100%; flex-direction: column;}
                              #logo{width: 30%;}
                              a{color: #01b5ec; text-decoration: none;}
                              a:visited{color:rgb(37, 72, 175);}
                              a:hover{color: hsl(228, 15%, 50%);}
                              .button{
                                      display: inline-block;
                                      background: linear-gradient(101deg,#04c4ff,#04c4ff);
                                      color: #fff;
                                      padding: 14px 28px;
                                      border-radius: .5rem;
                                      font-size: var(--normal-font-size);
                                      font-weight: var(--font-medium);
                                      box-shadow: 0 4px 8px hsla(228, 66%, 45%, .45);
                                      transition: .3s;
                                      cursor: pointer;
                                  }
                              .button:hover{
                                  box-shadow: 0 4px 12px hsla(228, 66%, 45%, .25);
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
                              <h1>Hello '.$uname.',</h1>
                              <p>Welcome to '.SITE_NAME.', We\'re excited to have you join us and take the first step toward exploring your personalized dashboard.  </p>
                              <p>Before you get started, please confirm your email address to activate your account. This helps us ensure the security of your information and provide you with the best experience. </p>
                              
                              <a class="button" href="'.$activation_link.'">Verify Email</a>
                              <p>Or</p>
                              <p>Copy and paste the below link into your browser address bar</p>
                              <a href="'.$activation_link.'">'.$activation_link.'</a>
                              </div>
                          </center>
                          <footer>
                              <div class="footer text-white">
                                  <p class="text-bold">Address: '.ADDRESS.'.</p>
                                  <!--<p class="text-bold">Phone: </p>-->
                                  <p class="text-bold">Support Email:'.SITE_EMAIL.'</p>

                                  <p>Kind Regards, '.SITE_TITLE.'</p>
                                  <div class="list ">
                                      <p>'.SITE_TITLE.' Copyriight &#169; 2024</p>
                                  </div>
                              </div>
                          </footer>
                      </body>
                  </html>';
                  sendEmail($email, $subject, $mail);
                  $successmsg = '<div class="success">Account successfully created, kindly check your email for verification message...</div>';
              }else{
                  $msg[] = '<div class="error">Unable to process this request at this time..
                  <br>Please try again later.</div>';
              }
          }else{
              $msg[] = '<div class="error">The email, phone number or username you entered is already in use.</div>';
          }
      }
  }

  include 'includes/header2.php';
?>

<div class="auth-shell auth-shell-wide">
  <aside class="auth-side">
    <a href="../" class="auth-brand"><div class="auth-brand-mark">AM</div><span>Arch-Mate</span></a>
    <div class="auth-side-inner">
      <div class="section-label" style="margin-bottom:1.5rem"><div class="section-label-line"></div><span>Join the Network</span></div>
      <h1 class="auth-hero-title">Become an <em>Arch-Mate</em> Realtor</h1>
      <p class="auth-hero-sub">Earn 10% direct commission on every property sold through your unique referral link. Build a downline. Withdraw on demand.</p>
      <div class="auth-stats">
        <div><strong>10%</strong><span>Direct commission</span></div>
        <div><strong>24h</strong><span>Payout window</span></div>
        <div><strong>500+</strong><span>Active realtors</span></div>
      </div>
    </div>
    <div class="auth-side-foot">&copy; <?php echo date('Y'); ?> Arch-Mate.</div>
  </aside>

  <main class="auth-main">
    <div class="auth-card auth-card-wide">
      <div class="auth-card-head">
        <h2>Create your realtor account</h2>
        <p>Already onboarded? <a href="./affiliate">Sign in &rarr;</a></p>
      </div>

      <?php if (!empty($msg)) foreach ($msg as $m) echo $m; ?>

      <form method="POST" class="auth-form auth-form-grid">
        <label class="auth-field"><span>Full Name</span><input type="text" name="fname" required/></label>
        <label class="auth-field"><span>Username</span><input type="text" name="uname" required/></label>
        <label class="auth-field"><span>Email</span><input type="email" name="email" required/></label>
        <label class="auth-field"><span>Phone</span><input type="tel" name="phone" required/></label>
        <label class="auth-field"><span>Occupation</span><input type="text" name="work" required/></label>
        <label class="auth-field"><span>Sex</span>
          <select name="sex" required><option value="">Select…</option><option>Male</option><option>Female</option></select>
        </label>
        <label class="auth-field auth-field-full"><span>Address</span><input type="text" name="address" required/></label>
        <label class="auth-field"><span>Bank Name</span><input type="text" name="bank" required/></label>
        <label class="auth-field"><span>Account Number</span><input type="text" name="acctNo" required/></label>
        <label class="auth-field"><span>Password</span><input type="password" name="pass" required/></label>
        <label class="auth-field"><span>Confirm Password</span><input type="password" name="pass2" required/></label>
        <label class="auth-field"><span>Referral</span><input type="text" name="ref" value="<?php echo htmlspecialchars($uplink ?? ''); ?>" disabled/></label>
        <label class="auth-field auth-field-full"><span>Verification Code <em>(<?php echo $turningImg; ?>)</em></span><input type="text" name="captcha" required/></label>

        <div class="auth-field-full">
          <label class="auth-check"><input type="checkbox" required/> I agree to the <a href="../terms" class="auth-link">Terms</a> &amp; <a href="../privacy" class="auth-link">Privacy Policy</a>.</label>
        </div>
        <button type="submit" class="btn-primary auth-submit auth-field-full">Create Account</button>
      </form>

      <a href="../" class="btn-outline auth-back">&larr; Back to website</a>
    </div>
  </main>
</div>

</body>
</html>
