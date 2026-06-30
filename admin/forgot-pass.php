<?php 
    $successMsg = '';
    $successButton = '';
    include_once "../config/dbconfig.php";
    include_once "../config/func.inc.php";
    include_once "../phpmailer/mailer.php";
    $title = 'Reset Password | '.SITE_NAME; ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../assets/css/styles.css">
        <link rel="stylesheet" href="../assets/css/admin-styles.css">
        <link rel="stylesheet" href="../assets/css/aos.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
        <link rel="icon" href="../assets/img/archmate.png">
        <title><?php echo $title; ?></title>
    </head>
    <?php
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            if(isset($_GET['x'], $_GET['y']) && filter_var($_GET['x'], FILTER_VALIDATE_EMAIL) && (strlen($_GET['y'])) == 32 ){
            $email = mysqli_real_escape_string($con, $_GET['x']);
            }else{
            echo '
            <div class="adm-auth-fullmsg">
                <div class="adm-alert adm-alert--error"><h1>Invalid Link</h1>
                    <p>Redirecting...</p>
                </div>
            </div> 
            ';
            header('refresh:3; url=./login');
            }
            $msg = array();
            if(empty($_POST['password']) || empty($_POST['password2'])){
                $msg[] = '<div class="adm-alert adm-alert--error">Please select new password for your account</div>';
            }else if(empty($_POST['password']) !== empty($_POST['password2'])){
                $msg[] = '<div class="adm-alert adm-alert--error">The passwords do not match!</div>';
            }else{
                $pass = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['password'])));
                $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
            } 
            $q = "UPDATE account SET pass = ? WHERE email = ?";
            $stmt = mysqli_prepare($con, $q);
            mysqli_stmt_bind_param($stmt, 'ss', $hashedPasss, $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_affected_rows($stmt) == 1){
                mysqli_stmt_free_result($stmt);
                $successMsg = '<div class="adm-alert adm-alert--success">New Password Created</div>';
                $successButton = '<button type="button" onclick="window.location.href = \'./login\';" class="adm-auth__submit adm-auth__submit--ghost">Return to login</button>';
            }
        }
    ?>
    <body class="adm-auth-body">
        <main class="adm-auth">
            <aside class="adm-auth__art">
                <div class="adm-auth__art-inner">
                    <span class="adm-auth__chip">New Credential</span>
                    <h1 class="adm-auth__title">Set a fresh<br><em>passphrase.</em></h1>
                    <p class="adm-auth__lead">Choose a strong password — at least 12 characters, mixing case and symbols. This will replace your previous admin credential immediately.</p>
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
                        <span class="adm-auth__eyebrow">Step 2 of 2</span>
                        <h2>Reset Password</h2>
                        <p>Enter and confirm your new administrator password.</p>
                    </div>

                    <?php 
                        if (!empty($msg)) {   
                            echo '<div class="adm-auth__msg">'.implode('', $msg).'</div>';
                        } elseif(!empty($successMsg)){
                            echo '<div class="adm-auth__msg">'.$successMsg.'</div>';
                        }
                    ?>

                    <form action="" method="post" class="adm-auth__form">
                        <label class="adm-field">
                            <span class="adm-field__label">New Password</span>
                            <span class="adm-field__wrap">
                                <i class="fa fa-fingerprint"></i>
                                <input type="password" name="password" id="password" placeholder="Enter new password">
                                <span class="adm-field__eye" id="eye" onclick="toggleEye()"><i class="fa fa-eye-slash"></i></span>
                            </span>
                        </label>
                        <label class="adm-field">
                            <span class="adm-field__label">Confirm Password</span>
                            <span class="adm-field__wrap">
                                <i class="fa fa-fingerprint"></i>
                                <input type="password" name="password2" id="password2" placeholder="Confirm new password">
                            </span>
                        </label>
                        <button type="submit" class="adm-auth__submit">
                            <span>Update Password</span>
                            <i class="fa-solid fa-check"></i>
                        </button>
                        <?php if(!empty($successButton)){echo $successButton;}?>
                    </form>
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
        <script src="../assets/js/index.js"></script>
        <script src="../assets/js/aos.js"></script>
    </body>
    </html>
