<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <link rel="stylesheet" href="../assets/fonts/fontawesome-free-6.2.0-web/css/all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../assets/img/archmate.png">
    <title><?php echo $title; ?></title>
    <script src="https://cdn.tiny.cloud/1/sqz8hroyq1xdjdg6fkgkehf7svkdc8kduewa6ecbe2tqlhao/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="adm-body">
    <!-- ADMIN TOPBAR -->
    <header class="adm-topbar header2">
        <div class="left adm-topbar__left">
            <?php
                if(isset($_SESSION['id'])){
                    echo '<button id="mobile-icon" class="adm-burger" aria-label="Toggle menu"><i class="fa-solid fa-bars"></i></button>';
                }
            ?>
            <a href="./" class="adm-brand">
                <img src="../assets/img/archmate-logo.png" class="logo adm-brand__logo" alt="Arch-Mate">
                <span class="adm-brand__tag">Console</span>
            </a>
        </div>

        <div class="right adm-topbar__right">
            <?php
                if(isset($_SESSION['id'])){
                    echo '<a id="red-button" href="./dashboard" class="adm-pill"><span>Admin</span> <i class="fa-solid fa-user"></i></a>';
                }
            ?>
        </div>

        <div class="reg-tool adm-popover align-center">
            <a href="./logout"><i class="fa fa-power-off"></i> Logout</a>
        </div>
    </header>

    <?php
        if(isset($_SESSION['id'])){
            echo '<aside class="admin-cont adm-sidebar align-left">
                <p class="adm-sidebar__label">Workspace</p>
                <a href="./dashboard"><i class="fa-solid fa-house-user"></i> <span>Overview</span></a>
                <a href="./create-properties"><i class="fa-solid fa-plus"></i> <span>New Listing</span></a>
                <a href="./quotes"><i class="fa-solid fa-quote-left"></i> <span>Quotes</span></a>
                <a href="./properties"><i class="fa-solid fa-door-open"></i> <span>Properties</span></a>
                <a href="./posts"><i class="fa-solid fa-file"></i> <span>Posts</span></a>
                <a href="./contacts"><i class="fa-solid fa-address-card"></i> <span>Contacts</span></a>
                <a href="./settings"><i class="fa-solid fa-gear"></i> <span>Settings</span></a>

                <p class="adm-sidebar__label">Realtor Network</p>
                <a href="./realtors"><i class="fa-solid fa-users"></i> <span>Realtors</span></a>
                <a href="./pending-acc"><i class="fa-solid fa-user-check"></i> <span>Activate Accounts</span></a>
                <a href="./affiliate-sales"><i class="fa-solid fa-chart-line"></i> <span>Sales by Realtors</span></a>
                <a href="./withdraw"><i class="fa-solid fa-money-bill-transfer"></i> <span>Withdrawals</span></a>
                <a href="./pending-upgrade"><i class="fa-solid fa-crown"></i> <span>Account Upgrade</span></a>
            </aside>';
        }
    ?>

    <main class="adm-main">
