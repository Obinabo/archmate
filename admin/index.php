<?php 
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
$title = 'Admin | '.SITE_NAME;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $msg = array();
    if(empty($_POST['uid'])){
        $msg[] = '<div class="adm-alert adm-alert--error">Please enter admin email or username</div>';
    }else{
        $uid = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['uid'])));
        $uid = strip_tags($uid);
    }    
    if(empty($_POST['password'])){
        $msg[] = '<div class="adm-alert adm-alert--error">Please enter your Password</div>';
    }else{
        $pass = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['password'])));
        $pass = md5(strip_tags($pass));
    }
    if(empty($msg)){
        $q = "SELECT * FROM admin WHERE email = ? OR uname = ? LIMIT 1";
        $stmt = mysqli_prepare ($con, $q);
        if($stmt){
            mysqli_stmt_bind_param($stmt, 'ss', $uid,  $uid);
            mysqli_stmt_execute($stmt);
            $r = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($r)){ 
                $pass2 = $row['pass'];
                if ($pass !== $pass2) {  
                    $msg[] = '<div class="adm-alert adm-alert--error">Incorrect admin password</div>';
                }else{
                    $_SESSION['id'] = $row['id'];
                    redirect('./dashboard');
                }
            }else {
                $msg[] = '<div class="adm-alert adm-alert--error">Incorrect admin email</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
    <link rel="stylesheet" href="../assets/css/swiper-bundle.min.css" />
    <link rel="icon" href="../assets/img/archmate.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <title><?php echo $title; ?></title>
</head>
<body class="adm-auth-body">
    <main class="adm-auth">
        <aside class="adm-auth__art">
            <div class="adm-auth__art-inner">
                <span class="adm-auth__chip">Arch-Mate Control</span>
                <h1 class="adm-auth__title">Steward the<br><em>Estate.</em></h1>
                <p class="adm-auth__lead">Sign in to manage realtors, listings, and platform operations from one command surface.</p>
                <ul class="adm-auth__list">
                    <li><i class="fa-solid fa-shield-halved"></i> Encrypted admin session</li>
                    <li><i class="fa-solid fa-chart-line"></i> Live affiliate intelligence</li>
                    <li><i class="fa-solid fa-bolt"></i> Instant property moderation</li>
                </ul>
            </div>
            <div class="adm-auth__halo"></div>
        </aside>

        <section class="adm-auth__panel">
            <div class="adm-auth__panel-inner">
                <a href="./" class="adm-auth__brand">
                    <img src="../assets/img/archmate.png" alt="Arch-mate logo">
                    <span>Arch-Mate <em>Admin</em></span>
                </a>

                <div class="adm-auth__heading">
                    <span class="adm-auth__eyebrow">Restricted Access</span>
                    <h2>Admin Control Panel</h2>
                    <p>Authenticate with your administrator credentials to continue.</p>
                </div>

                <?php if (!empty($msg)) { echo '<div class="adm-auth__msg">'.implode('', $msg).'</div>'; } ?>

                <form action="" method="POST" class="adm-auth__form" id="form">
                    <label class="adm-field">
                        <span class="adm-field__label">Email or Username</span>
                        <span class="adm-field__wrap">
                            <i class="fa-regular fa-circle-user"></i>
                            <input type="text" name="uid" id="email" placeholder="admin@arch-mate.com" required>
                        </span>
                    </label>
                    <label class="adm-field">
                        <span class="adm-field__label">Password</span>
                        <span class="adm-field__wrap">
                            <i class="fa fa-fingerprint"></i>
                            <input type="password" name="password" id="password" placeholder="••••••••" required>
                            <span class="adm-field__eye" id="eye" onclick="toggleEye()"><i class="fa-regular fa-eye"></i></span>
                        </span>
                    </label>
                    <div class="adm-auth__row">
                        <a class="adm-auth__link" href="./forgot">Forgot password?</a>
                    </div>
                    <button class="adm-auth__submit" type="submit">
                        <span>Enter Dashboard</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <p class="adm-auth__foot">Protected area — all activity is logged.</p>
            </div>
        </section>
    </main>

    <script>
        function toggleEye(){
            var passInput = document.getElementById('password');
            var eyeIcon = document.getElementById('eye');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.innerHTML = '<i class="fa-regular fa-eye-slash"></i>';
            } else {
                passInput.type = 'password';
                eyeIcon.innerHTML = '<i class="fa-regular fa-eye"></i>';
            }
        }
    </script>
</body>
</html>
