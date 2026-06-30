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
            <span class="adm-eyebrow">Quotes</span>
            <h1 class="adm-pagehead__title">All <em>Quotes</em></h1>
            <p class="adm-pagehead__lede">
                Potential customer quotes are submitted through the public site.<br>
                When visitors request for Quotes, They need explanation for some specified products    
            </p>
        </div>
    </div>
    <?php
        $limit = 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1; //current page number
        $offset = ($page - 1) * $limit; //calculate offset
     
        $qQuote .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";
        $stmtCont = mysqli_prepare($con, $qQuote);
        // mysqli_stmt_bind_param($stmtStat, 's', $pend);
        mysqli_stmt_execute($stmtQuote);
        $result = mysqli_stmt_get_result($stmtQuote);
        $numRows = mysqli_num_rows($result);
        
        if($numRows > 0){
            echo'<div class="quotes adm-msggrid">';
            while($row2 = mysqli_fetch_array($result)){
                echo '<article class="item adm-msg" data-aos="fade-up">
                    <div class="left adm-msg__meta">
                        <div class="section">
                            <h4>Name of Sender</h4>
                            <p class="adm-msg__name">'.$row2['name'].'</p>
                        </div>  
                        <div class="section">
                            <h4>Email of Sender</h4>
                            <p class="adm-msg__email">'.$row2['email'].'</p>
                        </div>
                        <div class="section">
                            <h4>Budget</h4>
                            <p class="adm-msg__name">'.$row2['budget'].'</p>
                        </div>
                        <div class="section">
                            <h4>Interests</h4>
                            <p class="adm-msg__name">'.$row2['interest'].'</p>
                        </div>
                        <div class="section">
                            <h4>Date Sent</h4>
                            <p class="adm-msg__date">'.$row2['date'].'</p>
                        </div>
                    </div>  
                
                    <div class="right adm-msg__body">
                        <div class="section">
                            <h4>Message</h4>
                            <p class="white">'.$row2['notes'].'</p>
                        </div>
                        <a class ="del-button adm-chip adm-chip--danger" href="./rem-quote?id='.$row2['id'].'" style="color: var(--white-color);"> Delete</a>
                    </div>
                </article>';
            }
            echo '</div>';
            
            $totalPages = ceil($quoteNumRows / $limit);
            echo '<div class="history" style="border: none;">';
                    for($i = 1; $i <= $totalPages; $i++){
                        echo '<div class="right white">Page <a href="?page=' . $i . '">' . $i . '</a></div>';
                    }
            echo '<div>';
        }else{
            echo '<div class="history adm-empty">
                <div class="left">
                    <h4>No Quotes Here...</h4>
                </div>
            <div>';
        }
    ?>    

</section>


<?php include "includes/foot.php"; ?>
