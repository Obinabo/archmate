<?php 
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
$title = 'Delete Sale | '.SITE_NAME;

if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}else{
    redirect('./affiliate');
}

include "includes/header2.php";
$q = "SELECT * FROM affiliate WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare ($con, $q);
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($r);
$verified = $row['verified'];
$paid = $row['paid'];
include "includes/nav-menu.php";

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        echo '<div class="error">You have accessed this page in error!</div>';
        redirect("./dashboard");
    }

    $q = "SELECT * FROM withdraw WHERE id = ?";
    $stmt = mysqli_prepare($con, $q);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    if(mysqli_num_rows($r) > 0){
        $row = mysqli_fetch_assoc($r);
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $successmsg = '';
        if(isset($_POST['submit']) && $_POST['submit'] == 'delete'){
            $q6 = "DELETE FROM withdraw WHERE id = ?";
                $stmt6 = mysqli_prepare($con, $q6);
                mysqli_stmt_bind_param($stmt6, 'i', $id);
                mysqli_stmt_execute($stmt6);
            if(mysqli_stmt_affected_rows($stmt6) == 1){
                $successmsg = '<div class="success"> withdrawal record successfully Deleted, Refreshing...</div>';            
                // $successbtn = '<button type="button" onclick="window.location.href = \'./properties\';" class="submit-button align-center white" style="background: green;">Return To Properties Page</button>';
            }else{
                $msg[] = '<div class="error">No changes made</div>';
            }
        }elseif(isset($_POST['submit']) && $_POST['submit'] == 'cancel'){
            redirect('./sales');
        }
    }
?>

<div class="admin-section">
<h3> Delete Withdrawal Record</h3><hr>
    <button type="button" onclick="window.location.href = './sales';" class="button3" style="margin: 10px auto; width: fit-content">Back to Sales</button>

    <p>Clicking on DELETE will automatically remove this withdrawal and its record from the database...</p>
    <p>Your account balance will not be affected by a DELETE action.</p>
    
            <div class="container2">
                <?php 
                    if (!empty($msg)) {   
                        echo implode('<br/>', $msg);
                    }
                    if(isset($successmsg)){echo $successmsg; redirect('./withdraw', 3);}
                echo '<h3 align-center">Withdrawal Amount: '.$row['amount'].' </h3>';
                ?>
                <h3 class="align-center"></h3>
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?php echo $id;?>" />
                    <button type="submit" name="submit" value="delete" class="submit-button white">Continue</button><br>
                    <button type="submit" name="submit" value="cancel" class="button2 white">Cancel</button>
                </form>
           
    </div>

</div>


<?php include "includes/footer2.php"; ?>
