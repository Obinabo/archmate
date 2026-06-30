<?php 
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";
    if(!isset($_SESSION['id'])){
        redirect('index.php');
    }

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        echo '<div class="adm-alert adm-alert--error">You have accessed this page in error!</div>';
        redirect("index.php");
    }

    $title = 'Admin dashboard | '.SITE_NAME;
    include "includes/head.php";

    $q = "SELECT * FROM affiliate WHERE id = ?";
    $stmt = mysqli_prepare($con, $q);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    if(mysqli_num_rows($r) > 0){
        $row = mysqli_fetch_assoc($r);
        $email = $row['email'];
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $successmsg = '';
        if(isset($_POST['submit']) && $_POST['submit'] == 'delete'){

            $q4 = "DELETE FROM withdraw WHERE email = ?";
            $stmt4 = mysqli_prepare($con, $q4);
            mysqli_stmt_bind_param($stmt4, 's', $email);
            mysqli_stmt_execute($stmt4);

            $q5 = "DELETE FROM affiliatesales WHERE email = ?";
            $stmt5 = mysqli_prepare($con, $q5);
            mysqli_stmt_bind_param($stmt5, 's', $email);
            mysqli_stmt_execute($stmt5);

            $q6 = "DELETE FROM affiliate WHERE email = ?";
                $stmt6 = mysqli_prepare($con, $q6);
                mysqli_stmt_bind_param($stmt6, 's', $email);
                mysqli_stmt_execute($stmt6);
            if(mysqli_stmt_affected_rows($stmt6) == 1){
                $successmsg = '<div class="adm-alert adm-alert--success">Account successfully deleted. Redirecting...</div>';            
            }else{
                $msg[] = '<div class="adm-alert adm-alert--error">Unable to delete account, try again later</div>';
            }
        }elseif(isset($_POST['submit']) && $_POST['submit'] == 'cancel'){
            redirect('./affiliates');
        }
    }
?>

<section class="admin-section">
    <div class="adm-confirm adm-confirm--danger">
        <header class="adm-confirm__head">
            <span class="adm-confirm__chip">Destructive Action</span>
            <h1 class="adm-confirm__title">Delete Realtor Account</h1>
            <p class="adm-confirm__lead">This permanently removes the realtor and all associated withdrawal and sales records. This action cannot be undone.</p>
        </header>

        <div class="adm-confirm__body">
            <?php 
                if (!empty($msg)) {   
                    echo implode('', $msg);
                }
                if(isset($successmsg)){echo $successmsg; redirect('./affiliates', 3);}
            ?>

            <div class="adm-confirm__target">
                <div class="adm-confirm__avatar"><i class="fa-solid fa-user-xmark"></i></div>
                <div>
                    <span class="adm-confirm__label">Account holder</span>
                    <h3 class="adm-confirm__name"><?php echo isset($row['fname']) ? htmlspecialchars($row['fname']) : 'Unknown'; ?></h3>
                    <p class="adm-confirm__sub"><?php echo isset($email) ? htmlspecialchars($email) : ''; ?></p>
                </div>
            </div>

            <ul class="adm-confirm__bullets">
                <li><i class="fa-solid fa-circle-exclamation"></i> Withdrawal history will be wiped.</li>
                <li><i class="fa-solid fa-circle-exclamation"></i> Affiliate sales records will be removed.</li>
                <li><i class="fa-solid fa-circle-exclamation"></i> The account credentials will be permanently revoked.</li>
            </ul>

            <form action="" method="POST" class="adm-confirm__actions">
                <input type="hidden" name="id" value="<?php echo $id;?>" />
                <button type="submit" name="submit" value="cancel" class="adm-btn adm-btn--ghost">Cancel</button>
                <button type="submit" name="submit" value="delete" class="adm-btn adm-btn--danger">
                    <i class="fa-solid fa-trash-can"></i> Delete Account
                </button>
            </form>
        </div>
    </div>
</section>

<?php include "includes/foot.php"; ?>
