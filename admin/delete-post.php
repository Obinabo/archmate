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

    $q = "SELECT * FROM posts WHERE id = ?";
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
            $q6 = "DELETE FROM posts WHERE id = ?";
                $stmt6 = mysqli_prepare($con, $q6);
                mysqli_stmt_bind_param($stmt6, 'i', $id);
                mysqli_stmt_execute($stmt6);
            if(mysqli_stmt_affected_rows($stmt6) == 1){
                redirect('./posts');
            }else{
                $msg[] = '<div class="adm-alert adm-alert--error">No changes made</div>';
            }
        }elseif(isset($_POST['submit']) && $_POST['submit'] == 'cancel'){
            redirect('./posts');
        }
    }
?>

<section class="admin-section">
    <div class="adm-confirm adm-confirm--danger">
        <header class="adm-confirm__head">
            <span class="adm-confirm__chip">Destructive Action</span>
            <h1 class="adm-confirm__title">Delete Post</h1>
            <p class="adm-confirm__lead">Removing this post will detach it from the public site immediately. Saved searches and inbound enquiries linked to it will no longer resolve.</p>
        </header>

        <div class="adm-confirm__body">
            <?php 
                if (!empty($msg)) {   
                    echo implode('', $msg);
                }
                if(isset($successmsg)){echo $successmsg;}
            ?>

            <div class="adm-confirm__target">
                <div class="adm-confirm__avatar"><i class="fa-solid fa-house-circle-xmark"></i></div>
                <div>
                    <span class="adm-confirm__label">Post</span>
                    <h3 class="adm-confirm__name"><?php echo isset($row['title']) ? htmlspecialchars($row['title']) : 'Untitled post'; ?></h3>
                    <p class="adm-confirm__sub">ID #<?php echo (int)$id; ?></p>
                </div>
            </div>

            <ul class="adm-confirm__bullets">
                <li><i class="fa-solid fa-circle-exclamation"></i> Post will be removed from the database.</li>
                <li><i class="fa-solid fa-circle-exclamation"></i> Public URLs will return not-found.</li>
                <li><i class="fa-solid fa-circle-exclamation"></i> This action cannot be reversed.</li>
            </ul>

            <form action="" method="POST" class="adm-confirm__actions">
                <input type="hidden" name="id" value="<?php echo $id;?>" />
                <button type="submit" name="submit" value="cancel" class="adm-btn adm-btn--ghost">Cancel</button>
                <button type="submit" name="submit" value="delete" class="adm-btn adm-btn--danger">
                    <i class="fa-solid fa-trash-can"></i> Delete Post
                </button>
                <?php if(isset($successbtn)){echo $successbtn;} ?>
            </form>
        </div>
    </div>
</section>

<?php include "includes/foot.php"; ?>
