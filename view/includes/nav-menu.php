<?php
// Sidebar + topbar for authenticated dashboard pages.
// $row should be the affiliate row (from the calling page).
$displayName = isset($row['fname']) ? htmlspecialchars($row['fname']) : 'Realtor';
$displayPic  = !empty($row['pic']) ? htmlspecialchars($row['pic']) : 'assets/img/man.png';
?>
<div class="dash-shell">
  <aside class="dash-side" id="dashSide">
    <a href="../" class="dash-brand">
      <div class="dash-brand-mark">AM</div>
      <div class="dash-brand-text">Arch-Mate<br><span>Realtor</span></div>
    </a>
    <nav class="dash-nav">
      <a href="./dashboard" class="dash-nav-link<?php echo dActive('dashboard'); ?>"><i class="fa-solid fa-grip"></i><span>Dashboard</span></a>
      <a href="./sales" class="dash-nav-link<?php echo dActive('sales').dActive('register-sale').dActive('delete-sale'); ?>"><i class="fa-solid fa-handshake"></i><span>Sales</span></a>
      <a href="./earnings" class="dash-nav-link<?php echo dActive('earnings'); ?>"><i class="fa-solid fa-coins"></i><span>Earnings</span></a>
      <a href="./withdraw" class="dash-nav-link<?php echo dActive('withdraw').dActive('delete-withdraw'); ?>"><i class="fa-solid fa-money-bill-transfer"></i><span>Withdrawals</span></a>
      <a href="./profile" class="dash-nav-link<?php echo dActive('profile').dActive('edit-profile'); ?>"><i class="fa-solid fa-user"></i><span>Profile</span></a>
      <a href="./payment-upgrade" class="dash-nav-link<?php echo dActive('payment-upgrade'); ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Upgrade</span></a>
      <a href="./referrals" class="dash-nav-link<?php echo dActive('referrals'); ?>"><i class="fa-solid fa-user-friends"></i><span>Referrals</span></a>
      <a href="https://chat.whatsapp.com/LSICet80PpqFnM2IHtB7kf" class="dash-nav-link" target="_blank"><i class="fa-brands fa-whatsapp"></i><span>Join Group</span></a>
      <a href="./settings" class="dash-nav-link<?php echo dActive('settings'); ?>"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
    </nav>
    <a href="./logout" class="dash-logout"><i class="fa-solid fa-right-from-bracket"></i><span>Sign Out</span></a>
  </aside>

  <div class="dash-main">
    <header class="dash-topbar">
      <button class="dash-burger" id="dashBurger" aria-label="Toggle menu"><i class="fa-solid fa-bars"></i></button>
      <div class="dash-topbar-title"><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard'; ?></div>
      <a href="./profile" class="dash-user">
        <img src="<?php echo $displayPic; ?>" onerror="this.src='https://i.pravatar.cc/80'" alt=""/>
        <div class="dash-user-name"><?php echo $displayName; ?></div>
      </a>
    </header>

    <main class="dash-content">
<script>
const dashBurger = document.getElementById('dashBurger');
const dashSide = document.getElementById('dashSide');

dashBurger?.addEventListener('click', (e) => {
  e.stopPropagation();
  dashSide?.classList.toggle('is-open');
});

document.body.addEventListener('click', (e) => {
  if (!dashSide?.classList.contains('is-open')) return;
  const clickedInsideSide = dashSide.contains(e.target);
  const clickedBurger = dashBurger?.contains(e.target);
  if (!clickedInsideSide && !clickedBurger) {
    dashSide.classList.remove('is-open');
  }
});
</script>
