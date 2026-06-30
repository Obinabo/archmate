<?php 
include "../config/dbconfig.php";
include "includes/header.php"; 
?>
    
<section class="page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <div class="section-label"><div class="section-label-line"></div><span>About <?php echo SITE_NAME_SHORT; ?></span></div>
      <h1 class="section-title">Built on Trust.<br>Powered by <em>People</em>.</h1>
      <p class="page-hero-sub">
        We exist to make the incredible affordable. Every parcel of land, every home, every signed document
        is treated like our own — because for our clients, it is everything.
      </p>
    </div>
  </div>
</section>
<!-- Message from the CEO -->
<div class="about">
  <div class="about-image-group">
    <img class="about-img-main" src="assets/img/team/ceo_pic-1.jpg" alt="Arch-Mate CEO" onerror="this.src='https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=900&q=75'">
    <div class="about-number-badge"><span class="num">CEO</span><span class="lbl">of Archmate</span></div>
  </div>
  <div class="about-content">
    <div class="section-label"><div class="section-label-line"></div><span>From Our CEO</span></div>
    <h2 class="section-title"></h2>
    <p class="about-body">
        Welcome to <em><?php echo SITE_NAME_SHORT; ?></em>, a company founded by Ambassador Raphael Chukwuebuka Okonkwo on December 11, 2020. Initially established as Arch-Mate Global Company, we have since evolved and expanded our expertise into the real estate sector, rebranding as Arch-Mate Estate and Homes Ltd on May 3, 2024.<br>
        
    </p>
    <p class="about-body" style="margin-top:-1rem">
      With four years of dedicated service and experience, our journey reflects our commitment to excellence and innovation in the real estate industry. At Arch-Mate Estate and Homes Ltd, we pride ourselves on delivering exceptional services and creating lasting value for our clients. Join us as we continue to shape the future of real estate with integrity, passion, and a client-centric approach.
    </p>
    
  </div>
</div>
<!-- Story / values split -->
<div class="about">
  <div class="about-image-group">
    <img class="about-img-main" src="assets/img/group_pic2.jpg" alt="The Arch-Mate team" onerror="this.src='https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=900&q=75'">
    <img class="about-img-accent" src="assets/img/value.jpg" alt="Property handover" onerror="this.src='https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=500&q=75'">
    <div class="about-number-badge"><span class="num">3+</span><span class="lbl">Years of Trust</span></div>
  </div>
  <div class="about-content">
    <div class="section-label"><div class="section-label-line"></div><span>Our Story</span></div>
    <h2 class="section-title">More Than<br>Real Estate — a <em>Partnership</em></h2>
    <p class="about-body">
      The name <strong>Arch-Mate</strong> combines two significant words: <em>Arch</em>, meaning Chief, Highest, or Best,
      and <em>Mate</em>, referring to a colleague or partner. Together, they symbolise "Best Partners" — reflecting our
      commitment to being your premier partner in real estate.
    </p>
    <p class="about-body" style="margin-top:-1rem">
      At <?php echo SITE_NAME_SHORT; ?>, we collaborate with the finest professionals to deliver exceptional service and peace of
      mind worldwide. Our AMGC/AMEH security factor integrates dedicated personnel to guarantee the safety of our
      estates, investors, and investments.
    </p>
    <div class="about-values">
      <div class="value-item"><div class="value-icon"><i class="bx bx-refresh"></i></div><div class="value-title">Change</div><div class="value-desc">We continually ask how we can make a difference for every stakeholder.</div></div>
      <div class="value-item"><div class="value-icon"><i class="bx bx-bolt-circle"></i></div><div class="value-title">Speed</div><div class="value-desc">We act quickly to fix problems at the right time, every time.</div></div>
      <div class="value-item"><div class="value-icon"><i class="bx bx-heart"></i></div><div class="value-title">Love</div><div class="value-desc">Every client we work with is treated like family — without exception.</div></div>
      <div class="value-item"><div class="value-icon"><i class="bx bxs-shield-alt-2"></i></div><div class="value-title">Security</div><div class="value-desc">AMGC personnel protecting every estate and investor we serve.</div></div>
    </div>
  </div>
</div>

<!-- Value accordion / why us -->
<section class="value-section">
  <div class="container">
    <div class="value-grid">
      <img class="value-img reveal" src="assets/img/value.jpg" alt="Why Arch-Mate" onerror="this.src='https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=900&q=75'"/>
      <div class="reveal">
        <div class="section-label"><div class="section-label-line"></div><span>Why Choose Us</span></div>
        <h2 class="section-title">The Value<br>We Give <em>to You</em></h2>
        <p class="about-body">We're always ready to help by providing the best service for you. We believe a good land to build on can change your life for the better.</p>
        <div class="value-accordion">
          <div class="value-accordion-item open">
            <div class="value-accordion-header">
              <i class="bx bxs-shield-alt-2 value-accordion-icon"></i>
              <div class="value-accordion-title">Best interest rates on the market</div>
              <div class="value-accordion-arrow"><i class="bx bxs-down-arrow"></i></div>
            </div>
            <div class="value-accordion-content"><p class="value-accordion-description">Our interest rates are considerate, never overbearing — undoubtedly the best you can get.</p></div>
          </div>
          <div class="value-accordion-item">
            <div class="value-accordion-header">
              <i class="bx bx-line-chart value-accordion-icon"></i>
              <div class="value-accordion-title">Stable prices, guaranteed</div>
              <div class="value-accordion-arrow"><i class="bx bxs-down-arrow"></i></div>
            </div>
            <div class="value-accordion-content"><p class="value-accordion-description">You get stable pricing. We guarantee no price changes on your property due to unexpected costs.</p></div>
          </div>
          <div class="value-accordion-item">
            <div class="value-accordion-header">
              <i class="bx bxs-dollar-circle value-accordion-icon"></i>
              <div class="value-accordion-title">Best prices on the market</div>
              <div class="value-accordion-arrow"><i class="bx bxs-down-arrow"></i></div>
            </div>
            <div class="value-accordion-content"><p class="value-accordion-description">The price we provide is the best for you — because we make the incredible affordable.</p></div>
          </div>
          <div class="value-accordion-item">
            <div class="value-accordion-header">
              <i class="bx bxs-lock-alt value-accordion-icon"></i>
              <div class="value-accordion-title">Security of your data</div>
              <div class="value-accordion-arrow"><i class="bx bxs-down-arrow"></i></div>
            </div>
            <div class="value-accordion-content"><p class="value-accordion-description">All your data is 100% safe. Your documents are handed over to you completely, with no issues.</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Team -->
<section class="team" id="team">
  <div class="container">
    <div class="section-label"><div class="section-label-line"></div><span>Our Team</span></div>
    <h2 class="section-title">The People<br>Behind <em><?php echo SITE_NAME_SHORT; ?></em></h2>
    <div class="team-grid">
      <?php
        $team = [
          ['Raphael C. Okonkwo','CEO / MD','assets/img/team/ceo_pic-1.jpg'],
          ['Mbam Abraham','Managing Director','assets/img/team/managing_director.jpeg'],
          ['Folorunsho Feyisike','Business Development Manager','assets/img/team/business_development_manager.jpeg'],
          ['Jaachi D Ebeku','Marketing Director','assets/img/team/marketing_director.jpeg'],
          ['Amandianaeze Chukwuemeka','Administrative Director','assets/img/team/administrative-director.jpeg'],
          ['Isineyi Chidimma Janefrances', 'Human Resources Manager', 'assets/img/team/hr.jpeg'],
          ['Chinechelem Modestus', 'Solar Power Solution / Content Manager', 'assets/img/team/solar_power.jpeg'],
          ['Onuh Perpetual Oluoma','Secretary Anambra Branch', 'assets/img/team/secretary_anambra.jpeg'],
        ];
        foreach ($team as $m) {
          echo '<div class="team-card"><img class="team-photo" src="'.$m[2].'" alt="'.htmlspecialchars($m[0]).'" loading="lazy"/><div class="team-name">'.htmlspecialchars($m[0]).'</div><div class="team-role">'.htmlspecialchars($m[1]).'</div></div>';
        }
      ?>
    </div>
  </div>
</section>

<!-- FAQs -->
<section class="value-section" id="faqs" style="background:var(--offwhite)">
  <div class="container" style="max-width:900px">
    <div class="section-label"><div class="section-label-line"></div><span>FAQs</span></div>
    <h2 class="section-title" style="margin-bottom:3rem">Questions, <em>Answered</em></h2>
    <div class="value-accordion">
      <?php
        $faqs = [
          ['How do I verify a property before buying?', 'Every Arch-Mate listing comes pre-verified with full documentation. Our team will walk you through title checks, surveys, and on-site visits before any commitment.'],
          ['Do you offer instalment payment plans?', 'Yes — most of our estates support flexible 3, 6, and 12-month plans. Talk to an advisor for specific terms on the property you\'re interested in.'],
          ['What is AMGC security?', 'AMGC (Arch-Mate Guard Corps) is our dedicated estate-security arm. Trained personnel patrol our estates 24/7 to protect investors and residents.'],
          ['Can I sell my Arch-Mate property later?', 'Absolutely. We offer a resale assistance program to help existing clients list and move their property at fair market value.'],
        ];
        foreach ($faqs as $i => $f) {
          $open = $i === 0 ? ' open' : '';
          echo '<div class="value-accordion-item'.$open.'">
            <div class="value-accordion-header">
              <i class="bx bx-question-mark value-accordion-icon"></i>
              <div class="value-accordion-title">'.htmlspecialchars($f[0]).'</div>
              <div class="value-accordion-arrow"><i class="bx bxs-down-arrow"></i></div>
            </div>
            <div class="value-accordion-content"><p class="value-accordion-description">'.htmlspecialchars($f[1]).'</p></div>
          </div>';
        }
      ?>
    </div>
  </div>
</section>
<?php include "includes/footer.php"; ?>