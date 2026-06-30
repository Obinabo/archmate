<?php
$successMsg = '';
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "../phpmailer/mailer.php";
$title = 'Forgot Password | '.SITE_NAME; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <link rel="icon" href="../assets/img/rocklink.png">
    <title><?php echo $title; ?></title>
</head>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $msg = array();
    if(!empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $email = mysqli_real_escape_string($con, trim($_POST['email']));
    }else{
        $msg[] = '<div class="adm-alert adm-alert--error">Please enter your email</div>';
    }
    if(empty($msg)){
        $q = "SELECT * FROM admin where email = ?";
        $stmt = mysqli_prepare($con, $q);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $r = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($r)){ 
            $db_email = $row['email'];
            $uname = $row['uname'];
            $unique_string = md5(uniqid(rand(), true));
            $forgot_link = URL.'/admin/forgot-pass?x='.urlencode($db_email).'&y='.$unique_string;
            $subject = '['.SITE_NAME_SHORT.'] Password Recovery';
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
                        .footer{background-color: rgb(217, 28, 41); margin: 30px auto 0px auto; padding: 5px; -moz-box-align: center; -webkit-box-align: center; color: rgb(243, 146, 0); }
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
                        .button{padding: 10px; background-color: rgb(37, 72, 175); color:rgb(255, 255, 255); width:fit-content; margin: 20px auto; transition: 1s; border-radius: 20px;}
                        .button:hover{background-color: rgb(16, 48, 143); margin: 20px auto;}
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
                        <div id="logo"><a href="'.URL.'"><img src="'.URL.'/assets/img/metro-logo-black.png" width="120px" height="40px" alt="logo" /></a></div>          
                        <h1>Hello Admin,</h1>
                        <p>Please click the button below to reset your '.SITE_NAME_SHORT.' password</p>
                        <a class="button" href="'.$forgot_link.'"></a>
                        <p>Or</p>
                        <p>Copy and paste the below link into your browser address bar</p>
                        <a href=""></a>
                        </div>
                    </center>
                    <footer>
                        <div class="footer text-white">
                            <p class="text-bold">Address: '.ADDRESS.'.</p>
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
            sendEmail($db_email, $subject, $mail);
            $successMsg = '<div class="adm-alert adm-alert--success"><i class="fa fa-circle-check"></i> Check your email for the password reset link.</div>';  
        }else{
            $msg[] = '<div class="adm-alert adm-alert--error">No such email exists on our servers</div>';
        }
    }
}
?>

<body class="adm-auth-body">
    <main class="adm-auth">
        <aside class="adm-auth__art adm-auth__art--alt">
            <div class="adm-auth__art-inner">
                <span class="adm-auth__chip">Recovery</span>
                <h1 class="adm-auth__title">Forgotten<br><em>not lost.</em></h1>
                <p class="adm-auth__lead">We'll dispatch a secure recovery link to the administrator email tied to this control panel.</p>
            </div>
            <div class="adm-auth__halo"></div>
        </aside>

        <section class="adm-auth__panel">
            <div class="adm-auth__panel-inner">
                <a href="./index" class="adm-auth__brand">
                    <img src="../assets/img/archmate.png" alt="Archmate logo">
                    <span>Arch-Mate <em>Admin</em></span>
                </a>

                <div class="adm-auth__heading">
                    <span class="adm-auth__eyebrow">Step 1 of 2</span>
                    <h2>Recover Admin Password</h2>
                    <p>Enter the administrator email address and we'll send a single-use reset link.</p>
                </div>

                <?php
                    if (!empty($msg)) {   
                        echo '<div class="adm-auth__msg">'.implode('', $msg).'</div>';
                    }else if(!empty($successMsg)){
                        echo '<div class="adm-auth__msg">'.$successMsg.'</div>';
                    }
                ?>

                <form action="" method="post" class="adm-auth__form">
                    <label class="adm-field">
                        <span class="adm-field__label">Admin Email</span>
                        <span class="adm-field__wrap">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="text" name="email" id="email" placeholder="admin@arch-mate.com" required>
                        </span>
                    </label>
                    <button type="submit" class="adm-auth__submit">
                        <span>Send Recovery Link</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>

                <p class="adm-auth__foot">Remembered it? <a href="./index" class="adm-auth__link">Return to login</a></p>
            </div>
        </section>
    </main>

    <script src="../assets/js/aos.js"></script>
    <script src="../assets/js/index.js"></script>
</body>
</html>
