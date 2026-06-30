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
    <!-- HERO / GREETING -->
    <div class="adm-hero" data-aos="fade-up">
        <div class="adm-hero__meta">
            <span class="adm-eyebrow">Console · <?php echo date('l, j M Y'); ?></span>
            <h1 class="adm-hero__title">Hello, <em>Admin.</em></h1>
            <p class="adm-hero__lede">A live snapshot of everything moving across Arch-Mate today — from realtors and listings to quotes and conversations.</p>
        </div>
        <div class="adm-hero__cta">
            <a href="./create-properties" class="adm-btn adm-btn--primary"><i class="fa fa-plus"></i> Create listing</a>
            <a href="./properties" class="adm-btn adm-btn--primary">Manage properties</a>
        </div>
    </div>

    <!-- KPI GRID -->
    <div class="adm-kpis" data-aos="fade-up" data-aos-delay="80">
        <a href="" id="noClick" class="adm-kpi">
            <span class="adm-kpi__label">Admin Users</span>
            <span class="adm-kpi__value"><?php echo $adminNumRows; ?></span>
            <i class="fa-solid fa-shield-halved adm-kpi__icon"></i>
        </a>
        <a href="./quotes" class="adm-kpi adm-kpi--accent">
            <span class="adm-kpi__label">Quotes</span>
            <span class="adm-kpi__value"><?php echo $quoteNumRows; ?></span>
            <i class="fa-solid fa-quote-left adm-kpi__icon"></i>
        </a>
        <a href="./properties" class="adm-kpi">
            <span class="adm-kpi__label">Properties</span>
            <span class="adm-kpi__value"><?php echo $propNumRows; ?></span>
            <i class="fa-solid fa-door-open adm-kpi__icon"></i>
        </a>
        <a href="./contacts" class="adm-kpi">
            <span class="adm-kpi__label">Contacts</span>
            <span class="adm-kpi__value"><?php echo $contNumRows; ?></span>
            <i class="fa-solid fa-address-card adm-kpi__icon"></i>
        </a>
        <a href="./posts" id="noClick" class="adm-kpi">
            <span class="adm-kpi__label">Posts</span>
            <span class="adm-kpi__value"><?php echo $postNumRows; ?></span>
            <i class="fa-solid fa-book-open adm-kpi__icon"></i>
        </a>
        <a href="./realtors" class="adm-kpi">
            <span class="adm-kpi__label">Realtors</span>
            <span class="adm-kpi__value"><?php echo $accountNumRows; ?></span>
            <i class="fa-solid fa-users adm-kpi__icon"></i>
        </a>
    </div>

    <!-- SECONDARY GRID -->
    <div class="adm-grid-2" data-aos="fade-up" data-aos-delay="160">
        <div class="adm-card">
            <h3 class="adm-card__title">Pending attention</h3>
            <ul class="adm-list">
                <li><span>Unverified accounts</span><strong><?php echo $pendNumRows; ?></strong></li>
                <li><span>Pending payments</span><strong><?php echo $paidNumRows; ?></strong></li>
                <li><span>Withdrawal requests</span><strong><?php echo $withdrawNumRows; ?></strong></li>
                <li><span>Realtor sales logged</span><strong><?php echo $salesNumRows; ?></strong></li>
            </ul>
        </div>
        <div class="adm-card adm-card--dark">
            <h3 class="adm-card__title">Quick actions</h3>
            <div class="adm-quick">
                <a href="./create-properties"><i class="fa fa-plus"></i> New property</a>
                <a href="./pending-acc"><i class="fa-solid fa-user-check"></i> Activate accounts</a>
                <a href="./withdraw"><i class="fa-solid fa-money-bill-transfer"></i> Process withdrawals</a>
                <a href="./contacts"><i class="fa-solid fa-envelope-open-text"></i> Read messages</a>
            </div>
        </div>
    </div>
</section>

<?php include "includes/foot.php"; ?>
