<?php 
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
include_once "includes/count.inc.php";
    if(!isset($_SESSION['id'])){
      redirect('index.php');
    }

    $title = 'Admin dashboard | '.SITE_NAME;
    include "includes/head.php"; 
?>

<section class="admin-section adm-section">
    <div class="adm-pagehead" data-aos="fade-up">
        <div>
            <span class="adm-eyebrow">Network</span>
            <h1 class="adm-pagehead__title">Registered <em>realtors</em></h1>
            <p class="adm-pagehead__lede">All realtors who have completed registration. Search by first name to filter.</p>
        </div>
        <form method="GET" action="" class="adm-search">
            <input type="text" name="search" class="ref-input adm-search__input" placeholder="Search realtors by name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
            <button type="submit" class="ref-button adm-btn adm-btn--primary"><i class="fa fa-search"></i> Search</button>
        </form>
    </div>

     <?php
        $limit = 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $qAccount = "SELECT * FROM affiliate";

        if (!empty($search)) {
            $qAccount .= " WHERE fname LIKE ?";
        }

        $qAccount .= " ORDER BY date DESC LIMIT ? OFFSET ?";
        
        $stmtAccount = mysqli_prepare($con, $qAccount);

        if (!empty($search)) {
            $likeSearch = "%" . $search . "%";
            mysqli_stmt_bind_param($stmtAccount, 'sii', $likeSearch, $limit, $offset);
        } else {
            mysqli_stmt_bind_param($stmtAccount, 'ii', $limit, $offset);
        }

        mysqli_stmt_execute($stmtAccount);
        $result = mysqli_stmt_get_result($stmtAccount);
        $numRows = mysqli_num_rows($result);

        if ($numRows > 0) {
            echo '
                <div class="table-container adm-table-wrap" data-aos="fade-up">
                    <table class="adm-table">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Sex</th>
                            <th>Bank</th>
                            <th>Account No.</th>
                            <th>Account Name</th>
                            <th>Balance</th>
                            <th>Email</th>
                            <th>Activation</th>
                            <th>Registered</th>
                            <th>View</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>';
            $sn = $offset + 1;
            while ($row2 = mysqli_fetch_array($result)) {
                $verBadge = strtoupper($row2['verified']) === 'VERIFIED' ? 'adm-badge--ok' : 'adm-badge--warn';
                $paidBadge = strtoupper($row2['paid']) === 'PAID' ? 'adm-badge--ok' : 'adm-badge--warn';
                echo '<tr>
                    <td>' . $sn . '</td>
                    <td><strong>' . $row2['fname'] . '</strong></td>
                    <td>' . $row2['uname'] . '</td>
                    <td>' . $row2['email'] . '</td>
                    <td>' . $row2['gender'] . '</td>
                    <td>' . $row2['bank'] . '</td>
                    <td>' . $row2['acct_no'] . '</td>
                    <td>' . $row2['acct_name'] . '</td>
                    <td>' . $row2['balance'] . '</td>
                    <td><span class="adm-badge ' . $verBadge . '">' . $row2['verified'] . '</span></td>
                    <td><span class="adm-badge ' . $paidBadge . '">' . $row2['paid'] . '</span></td>
                    <td>' . $row2['date'] . '</td>
                    <td><button type="button" onclick="window.location.href = \'./view-acc?id=' . $row2['id'] . '\';" class="del-button adm-chip adm-chip--edit">View</button></td>
                    <td><button type="button" onclick="window.location.href = \'./delete-acc?id=' . $row2['id'] . '\';" class="del-button adm-chip adm-chip--danger">Delete</button></td>
                    </tr>';
                $sn++;
            }
            echo '</tbody></table></div>';

            // Total count for pagination
            $countQuery = "SELECT COUNT(*) as total FROM affiliate";
            if (!empty($search)) {
                $countQuery .= " WHERE fname LIKE ?";
                $stmtCount = mysqli_prepare($con, $countQuery);
                mysqli_stmt_bind_param($stmtCount, 's', $likeSearch);
            } else {
                $stmtCount = mysqli_prepare($con, $countQuery);
            }

            mysqli_stmt_execute($stmtCount);
            $res = mysqli_stmt_get_result($stmtCount);
            $row = mysqli_fetch_assoc($res);
            $accountNumRows = $row['total'];

            $totalPages = ceil($accountNumRows / $limit);
            echo '<div class="history adm-pager" style="border: none;"><div class="right">Page ';
            for ($i = 1; $i <= $totalPages; $i++) {
                $queryStr = '?page=' . $i;
                if (!empty($search)) {
                    $queryStr .= '&search=' . urlencode($search);
                }
                echo '<a href="' . $queryStr . '" class="adm-pager__link">' . $i . '</a>';
            }
            echo '</div></div>';
        } else {
            echo '<div class="history adm-empty">
                <div class="left">
                    <h4>No registered realtors found.</h4>
                </div>
            </div>';
        }
    ?>        
</section>

<?php include "includes/foot.php"; ?>
