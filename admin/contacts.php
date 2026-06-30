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

<section class="admin-section adm-section">
    <div class="adm-pagehead" data-aos="fade-up">
        <div>
            <span class="adm-eyebrow">Inbox</span>
            <h1 class="adm-pagehead__title">All <em>contacts</em></h1>
            <p class="adm-pagehead__lede">Visitor enquiries and support requests submitted through the public site.</p>
        </div>
    </div>

    <?php
        $limit = 20;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
     
        $qCont .= " ORDER BY date DESC LIMIT $limit OFFSET $offset";
        $stmtCont = mysqli_prepare($con, $qCont);
        mysqli_stmt_execute($stmtCont);
        $result = mysqli_stmt_get_result($stmtCont);
        $numRows = mysqli_num_rows($result);
        
        if($numRows > 0){
            echo '<div class="quotes adm-msggrid">';
            while($row2 = mysqli_fetch_array($result)){
                echo '<article class="item adm-msg" data-aos="fade-up">
                    <div class="left adm-msg__meta">
                        <div class="section">
                            <h4>From</h4>
                            <p class="adm-msg__name">'.$row2['name'].'</p>
                        </div>
                        <div class="section">
                            <h4>Email</h4>
                            <p><a href="mailto:'.$row2['email'].'">'.$row2['email'].'</a></p>
                        </div>
                        <div class="section">
                            <h4>Phone</h4>
                            <p>'.$row2['phone'].'</p>
                        </div>
                        <div class="section">
                            <h4>Received</h4>
                            <p>'.$row2['date'].'</p>
                        </div>
                    </div>
                    <div class="right adm-msg__body">
                        <div class="section">
                            <h4>Message</h4>
                            <p class="white">'.$row2['notes'].'</p>
                        </div>
                        <a class="del-button adm-chip adm-chip--danger" href="./rem-contact?id='.$row2['id'].'"><i class="fa fa-trash"></i> Delete</a>
                    </div>
                </article>';
            }
            echo '</div>';

            $totalPages = ceil($contNumRows / $limit);
            echo '<div class="history adm-pager" style="border: none;">';
                    for($i = 1; $i <= $totalPages; $i++){
                        echo '<div class="right white"><a href="?page=' . $i . '" class="adm-pager__link">' . $i . '</a></div>';
                    }
            echo '</div>';
        }else{
            echo '<div class="history adm-empty">
                    <div class="left">
                        <h4>No contacts yet — your inbox is clear.</h4>
                    </div>
                </div>';
        }
    ?>    
</section>

<?php include "includes/foot.php"; ?>
