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

    $q = "SELECT * FROM quotes WHERE id = ?";
    $stmt = mysqli_prepare($con, $q);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    if(mysqli_num_rows($r) > 0){
        $row = mysqli_fetch_assoc($r);
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $successmsg = '';
        $successbtn = '';
        if(isset($_POST['submit']) && $_POST['submit'] == 'delete'){
            $q6 = "DELETE FROM quotes WHERE id = ?";
                $stmt6 = mysqli_prepare($con, $q6);
                mysqli_stmt_bind_param($stmt6, 'i', $id);
                mysqli_stmt_execute($stmt6);
            if(mysqli_stmt_affected_rows($stmt6) == 1){
                redirect('./quotes');
                // $successmsg = '<div class="success"> Quote successfully Deleted</div>';            
                // $successbtn = '<button type="button" onclick="window.location.href = \'./quotes\';" class="submit-button align-center white" style="background: green;">Return To Quotes Page</button>';
            }else{
                $msg[] = '<div class="adm-alert adm-alert--error">No changes made</div>';
            }
        }elseif(isset($_POST['submit']) && $_POST['submit'] == 'cancel'){
            redirect('./quotes');
        }
    }
?>

<section class="admin-section">
    <div class="adm-confirm adm-confirm--danger">
        <header class="adm-confirm__head">
            <span class="adm-confirm__chip">Destructive Action</span>
            <h1 class="adm-confirm__title">Delete Selected Quote</h1>
            <p class="adm-confirm__lead">Clicking on DELETE will automatically remove this quote from the database. </p>
        </header>

        <div class="adm-confirm__body">
            <?php 
                if (!empty($msg)) {   
                    echo implode('<br/>', $msg);
                }
                if(isset($successmsg)){echo $successmsg;}
            ?>
            <div class="adm-confirm__target">
                <div class="adm-confirm__avatar"><i class="fa-solid fa-quote-left"></i></div>
                <div>
                    <span class="adm-confirm__label">Quote</span>
                    <h3 class="adm-confirm__name"><?php echo isset($row['name']) ? htmlspecialchars($row['name']) : 'Untitled listing'; ?></h3>
                </div>
            </div>
            <form action="" method="POST" class="adm-confirm__actions">
                <input type="hidden" name="id" value="<?php echo $id;?>" />
                <button type="submit" name="submit" value="delete" class="adm-btn adm-btn--danger"><i class="fa-solid fa-trash-can"></i> Delete</button>
                <button type="submit" name="submit" value="cancel" class="adm-btn adm-btn--ghost">cancel</button>
                <?php if(isset($successbtn)){echo $successbtn;} ?>
            </form>
        </div>      
    </div>
</section>


<?php include "includes/foot.php"; ?>
