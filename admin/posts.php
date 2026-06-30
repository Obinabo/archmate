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
            <!-- <span class="adm-eyebrow">Inventory</span> -->
            <h1 class="adm-pagehead__title">All <em>Posts</em></h1>
            <p class="adm-pagehead__lede">Preview and remove posts. Changes are reflected on the public site instantly.</p>
        </div>
        <a href="./create-post" class="adm-btn adm-btn--primary"><i class="fa fa-plus"></i> Create post</a>
    </div>

    <?php
        $limit = 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1; //current page number
        $offset = ($page - 1) * $limit; //calculate offset
     
        $qPost .= " ORDER BY date DESC LIMIT $limit OFFSET $offset";
        $stmtPost = mysqli_prepare($con, $qPost);
        mysqli_stmt_execute($stmtPost);
        $result = mysqli_stmt_get_result($stmtPost);
        $numRows = mysqli_num_rows($result);
        
        if($numRows > 0){
            echo '
                <div class="table-container adm-table-wrap" data-aos="fade-up">
                    <table class="adm-table">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Post Title</th>
                            <th>Subtitle</th>
                            <th>Body</th>
                            <th>Date</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>';
                        $sn = 1;
                        while($row2 = mysqli_fetch_array($result)){
                            echo '<tr>
                            <td>'.$sn.'</td>
                            <td><strong>'.$row2['title'].'</strong></td>
                            <td>'.$row2['subtitle'].'</td>
                            <td>'.substr(strip_tags($row2['body']), 0, 50).'...</td>
                            <td>'.$row2['date'].'</td>
                            <td><a class="del-button adm-chip adm-chip--danger" href="./delete-post?id='.$row2['id'].'"><i class="fa fa-trash"></i> Delete</a></td>
                            </tr>';
                            $sn++;
                        }
                    echo '</tbody></table>
                    </div>';
            $totalPages = ceil($postNumRows / $limit);
            echo '<div class="history adm-pager" style="border: none;">';
                    for($i = 1; $i <= $totalPages; $i++){
                        echo '<div class="right white"><a href="?page=' . $i . '" class="adm-pager__link">' . $i . '</a></div>';
                    }
            echo '</div>';
        }else{
            echo '<div class="history adm-empty">
                    <div class="left">
                        <h4>No posts yet — create your first post.</h4>
                    </div>
                </div>';
        }
    ?>
</section>

<?php include "includes/foot.php"; ?>
