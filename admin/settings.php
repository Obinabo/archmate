<?php 
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";
    if(isset($_SESSION['id'])){
    //$id = $_SSESSION['id'];
    }else{
        redirect('index.php');
    }

    $title = 'Admin dashboard | '.SITE_NAME;
    include "includes/head.php"; 
?>

<div class="admin-section">
    <div class="balance" style="background: var(--dark-pink);">
        <div class="left">
            <p><span class="bold-amount">Settings</span></p>
            <p> </p>
        </div>
        <div class="right">
            <p></p>
        </div> 
    </div>
    
    <p class="white">Here, you can edit all component layers of the front end</p>
    <p class="white">This feature is loading...</p>
    <br><br>
    

</div>


<?php include "includes/foot.php"; ?>
