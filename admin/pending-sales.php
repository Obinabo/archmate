
<?php 
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";
include_once "../phpmailer/mailer.php";
    if(!isset($_SESSION['id'])){
    //$id = $_SSESSION['id']; 
        redirect('index.php');
    }
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        echo '<div class="error">You have accessed this page in error!</div>';
        redirect("index.php");
    }
    $title = 'Admin dashboard | '.SITE_NAME;
    include "includes/head.php"; 

    $q = "SELECT * FROM affiliatesales WHERE id = ?";
    $stmt = mysqli_prepare($con, $q);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);
    $id = $row['id'];
    $amount = $row['payment'];

    $indirect_percent = 5;
    $indirect_commission = ($indirect_percent / 100) * $amount;

    $percentage = 10;
    $commission = ($percentage / 100) * $amount;
    $earnings = $commission + $_SESSION['earnings'];
    $balance = $commission + $_SESSION['balance'];

    $email = $_SESSION['email'];
    $fname = $_SESSION['fname'];

    if(($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST['submit']))){
        $successmsg = '';
        $msg = array();
        if(empty($_POST['status'])){
            $msg[] = '<div class="error">Please select new status</div>';
        }else{
            $status = mysqli_real_escape_string($con, trim(htmlspecialchars($_POST['status'])));
        }
        if(empty($msg)){
            if($status === 'APPROVED'){
                $q3 = "UPDATE affiliatesales SET commission = ?, status = ? WHERE id = ?";
                $stmt3 = mysqli_prepare ($con, $q3);
                mysqli_stmt_bind_param($stmt3, 'isi', $commission, $status, $id);
                mysqli_stmt_execute($stmt3);
                mysqli_stmt_store_result($stmt3);
                if(mysqli_stmt_affected_rows($stmt3)){
                    
                    $q2 = "UPDATE affiliate SET earnings = ?, balance = ? WHERE email = ? LIMIT 1";
                    $stmt2 = mysqli_prepare ($con, $q2);
                    mysqli_stmt_bind_param($stmt2, 'sss', $earnings, $balance, $email);
                    mysqli_stmt_execute($stmt2);
                    mysqli_stmt_store_result($stmt2);
                    $subject = 'Update on your earnings';
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
                                <h1>Hello '.$fname.',</h1>
                                <p>Your recent sale recording has been updated. kindly login to your realtor dashboard and check your earnings.</p>
                                                            
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
                    $successmsg = '<div class="success">New status set for account... <br>Realtor has been credited with commission</div>';
                    if(isset($_SESSION['ref_type']) && isset($_SESSION['email'])){
                        $ref_type = $_SESSION['ref_type'];
                        $ref_email = $_SESSION['ref_email'];

                        $q4 = "SELECT * FROM affiliate WHERE email = ? LIMIT 1";
                        $stmt4 = mysqli_prepare ($con, $q4);
                        mysqli_stmt_bind_param($stmt4, 's', $ref_email);
                        mysqli_stmt_execute($stmt4);
                        $r4 = mysqli_stmt_get_result($stmt4);
                        if($row4 = mysqli_fetch_array($r4)){
                            $indirect_earnings = $row4['earnings'] + $indirect_commission; 
                            $indirect_balance = $row4['balance'] + $indirect_commission;
                            $q5 = "UPDATE affiliate SET earnings = ?, balance = ? WHERE email = ? LIMIT 1";
                            $stmt5 = mysqli_prepare ($con, $q5);
                            mysqli_stmt_bind_param($stmt5, 'sss', $indirect_earnings, $indirect_balance, $ref_email);
                            mysqli_stmt_execute($stmt5);
                            mysqli_stmt_store_result($stmt5);

                            $subject4 = 'Update on your earnings via referral';
                            $mail4 = '
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
                                        <h1>Hello '.$row4['fname'].',</h1>
                                        <p>Your account has been credited with indirect commission earned from your referral\'s property sale. kindly login to your realtor dashboard and check your earnings.</p>
                                                                    
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
                            sendEmail($ref_email, $subject4, $mail4);
                        }
                    }
                }else{
                    $msg[] = '<div class="error">No change made to account</div>';
                }
            }elseif($status === 'REJECTED'){
                $q = "UPDATE affiliatesales SET status = ? WHERE id = ?";
                $stmt = mysqli_prepare ($con, $q);
                mysqli_stmt_bind_param($stmt, 'si', $status, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_affected_rows($stmt)){
                    $subject = 'Update on your earnings';
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
                                <h1>Hello '.$fname.',</h1>
                                <p>We are sad to inform you that your sale recording has been rejected. Kindly reach out to the realtors\' liason officer whose phone number is on your dashboard for assistance.</p>
                                                            
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
                    $successmsg = '<div class="success">New status set for account... <br>Realtor will be notified of the rejection</div>';
            
                }
            }else{
                $msg[] = '<div class="error">Kindly set account to either Approved or rejected</div>';
            }
        }

    }
?>

<div class="admin-section">
    <div class="balance">
            <div class="left">
                <p><span class="bold-amount">Update Sale Recording</span></p>
                <p> </p>
            </div>
            <div class="right">
                <p></p>
            </div> 
    </div>
    <p>Update this sale record made by <?php echo $fname;?>.</p>
    
    <div class="reg-container2">
        <?php 
            if (!empty($msg)) {   
                echo implode('<br/>', $msg);
            }
            if(isset($successmsg)){echo $successmsg;}
        ?>
        <h3 class="align-center"></h3>
        <form action="" method="POST">
            <p class="align-left" style="font-size: 0.7; margin-bottom: -5px"> Title</p>
            <span class="input-container"><input type="text" name="title" value="<?php echo $row['title']?>" disabled></span>
            <p class="align-left" style="font-size: 0.7; margin-bottom: -5px"> Payment
            <span class="input-container"><input type="text" name="price" value="<?php echo $row['payment'];?>" disabled></span>
            <p class="align-left" style="font-size: 0.7; margin-bottom: -5px">Description</p>
            <span class="input-container"><input type="text" name="location" value="<?php echo $row['description']?>" disabled></span>
            <p class="align-left" style="font-size: 0.7; margin-bottom: -5px">Direct Commission to user (10%)</p>
            <span class="input-container"><input type="text" name="commission" value="<?php echo $row['commission']?>" disabled></span>
            <p class="align-left" style="font-size: 0.7; margin-bottom: -5px">Date Entered</p>
            <span class="input-container"><input type="text" name="location" value="<?php echo $row['date']?>" disabled></span>
            <p class="align-left" style="font-size: 0.7; margin-bottom: -5px">Change sales status to either approve or reject this record.</p>
            <span class="input-container"><select name="status" id="email" style="width: 100%;" required>
                    <option value="">Choose Account Status</option>
                    <option value="PENDING">PENDING</option>
                    <option value="APPROVED">APPROVED</option>
                    <option value="REJECTED">REJECTED</option>
                </select></span>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <button type="submit" name="submit" class="submit-button white">Update Sales</button>
        </form>
    </div> 
</div>
<script>
     tinymce.init({
        selector: 'textarea#default',
    });
</script>
<?php include "includes/foot.php"; ?>