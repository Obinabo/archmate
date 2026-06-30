<!--==================== HERO ====================-->
<section class="hero" id="home">
  <div class="hero-left">
    <div class="hero-eyebrow">
      <div class="hero-eyebrow-line"></div>
      <span>Premium Real Estate · Nigeria</span>
    </div>
    <h1 class="hero-title">Explore<br><em>Prime</em><br>Real Estate<br>Deals Today</h1>
    <p class="hero-subtitle">
      Effortlessly discover a diverse selection of properties tailored to your needs.
      Say goodbye to the challenges of land hunting and embrace a seamless experience.
    </p>

    <form action="./properties" method="GET" class="hero-search">
      <i class="bx bxs-map"></i>
      <input type="text" name="q" placeholder="Search by location..." aria-label="Search properties by location" />
      <button type="submit" class="btn-primary">Search</button>
    </form>

    <div class="hero-actions">
      <a href="./properties" class="btn-primary">Browse Properties</a>
      <a href="./about" class="btn-ghost">Our Story
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </a>
    </div>
  </div>

  <div class="hero-right">
    <div class="hero-image-wrap">
      <img src="assets/img/group_pic.jpg" alt="Arch-Mate team & estate" onerror="this.src='https://images.unsplash.com/photo-1613977257592-4871e5fcd7c4?w=1100&q=80'"/>
      <div class="hero-image-overlay">
        <div class="hero-stat-row">
          <div><div class="hero-stat-num">70+</div><div class="hero-stat-label">Properties Listed</div></div>
          <div><div class="hero-stat-num">100+</div><div class="hero-stat-label">Satisfied Clients</div></div>
          <div><div class="hero-stat-num">150+</div><div class="hero-stat-label">Affiliate Partners</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!--==================== MARQUEE ====================-->
<div class="marquee-bar" aria-hidden="true">
  <div class="marquee-track">
    <?php
      $marquee = ['Prime Land','Estate Homes','Secured Investment','Anambra · Enugu · Asaba','Best Partner in Real Estate','AMGC Security','Stable Pricing'];
      for ($i=0; $i<2; $i++) {
        foreach ($marquee as $m) {
          echo '<div class="marquee-item"><span>'.htmlspecialchars($m).'</span><div class="marquee-dot"></div></div>';
        }
      }
    ?>
  </div>
</div>

<!--==================== FEATURED PROPERTIES (editorial grid) ====================-->
<section class="properties" id="properties">
  <div class="container">
    <div class="section-header-row reveal">
      <div>
        <div class="section-label"><div class="section-label-line"></div><span>Portfolio</span></div>
        <h2 class="section-title">Featured<br><em>Properties</em></h2>
      </div>
      <a href="./properties" class="view-all">View All Properties &rarr;</a>
    </div>

    <div class="properties-grid reveal">
      <?php
        $qProp = "SELECT * FROM properties ORDER BY DATE DESC LIMIT 5";
        $resProp = mysqli_query($con, $qProp);
        if ($resProp && mysqli_num_rows($resProp) > 0) {
          $i = 0;
          while ($row = mysqli_fetch_assoc($resProp)) {
            $featureClass = ($i === 0) ? ' is-feature' : '';
            $tag = !empty($row['tag']) ? '<div class="prop-tag">'.htmlspecialchars($row['tag']).'</div>' : '';
            $thumbSrc = propertyMediaSrc($row['img'] ?? '', '');
            echo '
            <a href="./property?id='.urlencode($row['id']).'" class="prop-card'.$featureClass.'">
              <img src="'.htmlspecialchars($thumbSrc).'" alt="'.htmlspecialchars($row['title']).'" loading="lazy"/>
              <div class="prop-overlay">
                <div class="prop-location">'.htmlspecialchars($row['location']).'</div>
                <div class="prop-name">'.htmlspecialchars($row['title']).'</div>
                <div class="prop-price">'.htmlspecialchars($row['price']).'</div>
              </div>
              '.$tag.'
            </a>';
            $i++;
          }
        } else {
          // Fallback placeholders if DB empty
          $fallback = [
            ['Isuaniocha, Awka North', "Kevin's Villa Estate", '₦17,000,000', 'Best Value', 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1000&q=75', true],
            ['Awba Ofemili, Awka North', 'GreenFarm Layout', '₦1,300,000', '', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=700&q=75', false],
            ['Ogwashi-Uku, Asaba', 'Terra Verde Estate', '₦1,500,000', '', 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=700&q=75', false],
            ['Ogbeke Nike, Enugu', 'Citadel City Estate', '₦3,000,000', 'Pre-Launch', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=75', false],
            ['Awka Capital Territory', 'Redan City Eastern Estate', '₦30,000,000', 'Premium', 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=700&q=75', false],
          ];
          foreach ($fallback as $f) {
            $cls = $f[5] ? ' is-feature' : '';
            $tag = $f[3] ? '<div class="prop-tag">'.htmlspecialchars($f[3]).'</div>' : '';
            echo '<a href="./properties" class="prop-card'.$cls.'"><img src="'.$f[4].'" alt="'.htmlspecialchars($f[1]).'" loading="lazy"/><div class="prop-overlay"><div class="prop-location">'.$f[0].'</div><div class="prop-name">'.$f[1].'</div><div class="prop-price">'.$f[2].'</div></div>'.$tag.'</a>';
          }
        }
      ?>
    </div>
  </div>
</section>

<!--==================== LATEST POSTS ====================-->
<?php
  if (!function_exists('homePostExcerpt')) {
    function homePostExcerpt(string $body, int $limit = 120): string
    {
      $text = trim(preg_replace('/\s+/', ' ', strip_tags($body)));
      if ($text === '') {
        return '';
      }

      if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) <= $limit) {
          return $text;
        }
        return rtrim(mb_substr($text, 0, $limit - 1)) . '...';
      }

      if (strlen($text) <= $limit) {
        return $text;
      }

      return rtrim(substr($text, 0, $limit - 1)) . '...';
    }
  }

  if (!function_exists('homePostDate')) {
    function homePostDate(string $rawDate): string
    {
      $rawDate = trim($rawDate);
      if ($rawDate === '') {
        return '';
      }

      $formats = ['d/m/Y h:i:s', 'd/m/Y H:i:s', 'Y-m-d H:i:s', 'Y-m-d'];
      foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $rawDate);
        if ($date instanceof DateTime) {
          return $date->format('M j, Y');
        }
      }

      $timestamp = strtotime($rawDate);
      if ($timestamp !== false) {
        return date('M j, Y', $timestamp);
      }

      return $rawDate;
    }
  }

  $latestPosts = [];
  $latestPostsSql = "SELECT id, title, subtitle, img, body, date FROM posts ORDER BY STR_TO_DATE(date, '%d/%m/%Y %h:%i:%s') DESC, id DESC LIMIT 4";
  $latestPostsRes = mysqli_query($con, $latestPostsSql);
  if ($latestPostsRes) {
    while ($row = mysqli_fetch_assoc($latestPostsRes)) {
      $latestPosts[] = $row;
    }
  }
?>

<section style="background:#fff">
  <div class="container">
    <div class="section-header-row reveal">
      <div>
        <div class="section-label"><div class="section-label-line"></div><span>Latest Posts</span></div>
        <h2 class="section-title">Fresh <em>updates</em> from Arch-Mate</h2>
      </div>
      <a href="./posts" class="view-all">View all posts &rarr;</a>
    </div>

    <?php if (!empty($latestPosts)): ?>
      <div class="blog-feature-grid reveal">
        <?php foreach ($latestPosts as $index => $post): ?>
          <?php
            $thumb = propertyMediaSrc($post['img'] ?? '', '');
            if ($thumb === '') {
              $thumb = 'assets/img/archmate-logo.png';
            }
            $date = homePostDate((string)($post['date'] ?? ''));
            $excerpt = homePostExcerpt((string)($post['body'] ?? ''), $index === 0 ? 150 : 110);
          ?>
          <a href="./blog?id=<?php echo urlencode($post['id']); ?>" class="blog-feature-card<?php echo $index === 0 ? ' is-featured' : ''; ?>">
            <img src="<?php echo htmlspecialchars($thumb); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy">
            <div class="blog-feature-overlay">
              <?php if ($date !== ''): ?>
                <span class="blog-feature-date"><?php echo htmlspecialchars($date); ?></span>
              <?php endif; ?>
              <h3><?php echo htmlspecialchars($post['title']); ?></h3>
              
              <?php if ($excerpt !== ''): ?>
                <p class="blog-feature-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div style="padding:4rem 0;text-align:center;color:var(--text-muted);font-family:var(--serif);font-style:italic;font-size:1.2rem;">
        No posts have been published yet.
      </div>
    <?php endif; ?>
  </div>
</section>

<!--==================== ABOUT (split editorial) ====================-->
<div class="about" id="about">
  <div class="about-image-group reveal">
    <img class="about-img-main" src="assets/img/group_pic2.jpg" alt="Arch-Mate team" onerror="this.src='https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=800&q=75'">
    <img class="about-img-accent" src="assets/img/value.jpg" alt="Signing property documents" onerror="this.src='https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=500&q=75'">
    <div class="about-number-badge"><span class="num">3+</span><span class="lbl">Years of Trust</span></div>
  </div>
  <div class="about-content reveal">
    <div class="section-label"><div class="section-label-line"></div><span>About Us</span></div>
    <h2 class="section-title">Premier Partners<br>in <em>Real Estate</em><br>Excellence</h2>
    <p class="about-body">
      "Arch" — Chief, Highest, Best. "Mate" — Colleague, Partner. Together, we are your <strong>Best Partner</strong>
      in Nigerian real estate. We collaborate with the finest professionals to deliver exceptional service and peace
      of mind to clients worldwide.
    </p>
    <div class="about-values">
      <div class="value-item">
        <div class="value-icon"><i class="bx bxs-shield-alt-2"></i></div>
        <div class="value-title">AMGC Security</div>
        <div class="value-desc">Dedicated personnel protecting all estates and investments.</div>
      </div>
      <div class="value-item">
        <div class="value-icon"><i class="bx bx-time-five"></i></div>
        <div class="value-title">Fast Response</div>
        <div class="value-desc">Issues resolved quickly, at the right time, every time.</div>
      </div>
      <div class="value-item">
        <div class="value-icon"><i class="bx bx-line-chart"></i></div>
        <div class="value-title">Stable Pricing</div>
        <div class="value-desc">No surprise price changes after your commitment.</div>
      </div>
      <div class="value-item">
        <div class="value-icon"><i class="bx bxs-file-doc"></i></div>
        <div class="value-title">Full Documentation</div>
        <div class="value-desc">All documents handed over completely, zero complications.</div>
      </div>
    </div>
    <a href="./about" class="btn-primary">Our Full Story</a>
  </div>
</div>


<!--==================== ABOUT (split editorial) ====================-->
<div class="about" id="about">
  <div class="about-image-group reveal">
    <iframe class="about-img-main" width="100%" height="auto" src="https://www.youtube.com/embed/Ia8teAphxpU" title="The official Launching of our Kevin&#39;s Villa Estate Isuaniocha Awka Capital Territory, Anambra State" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    <!-- <div class="about-number-badge"><span class="num">3+</span><span class="lbl">Years of Trust</span></div> -->
  </div>
  <div class="about-content reveal">
    <div class="section-label"><div class="section-label-line"></div><span>Awka Capital Territory</span></div>
    <h2 class="section-title">The Official Launch of <em>Kevin’s Villa Estate,</em><br>  Isuaniocha</h2>
    <p class="about-body">
      The official launch of Kevin’s Villa Estate, Isuaniocha, Awka Capital Territory, marks the beginning of a new standard in secure, strategic, and future-focused real estate investment in Anambra State.
    </p>
    <p class="about-body">
      Designed for investors, homeowners, and visionaries seeking lasting value, Kevin’s Villa Estate combines prime location, accessibility, infrastructure readiness, and long-term appreciation potential in one exceptional development.
    </p>
  </div>
</div>

<!--==================== QUOTE FORM ====================-->
<?php
$alerts = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-quote'])) {
  $name     = trim($_POST['name']     ?? '');
  $email    = trim($_POST['email']    ?? '');
  $interest = trim($_POST['interest'] ?? '');
  $budget   = trim($_POST['budget']   ?? '');
  $notes    = trim($_POST['notes']    ?? '');
  if (!$name)                              $alerts[] = 'Please enter your name.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $alerts[] = 'Please enter a valid email.';
  if (!$interest)                          $alerts[] = 'Please select an interest.';
  if (!$budget)                            $alerts[] = 'Please enter your budget.';

  if (empty($alerts)) {
    $n = mysqli_real_escape_string($con, $name);
    $e = mysqli_real_escape_string($con, $email);
    $in= mysqli_real_escape_string($con, $interest);
    $b = mysqli_real_escape_string($con, $budget);
    $no= mysqli_real_escape_string($con, $notes);
    $d = date('Y-m-d H:i:s');
    if (function_exists('createQuote')) {
      createQuote($con, $n, $e, $in, $b, $no, $d);
    } else {
      @mysqli_query($con, "INSERT INTO quotes (name,email,interest,budget,notes,date) VALUES ('$n','$e','$in','$b','$no','$d')");
    }
    $success = 'Quote submitted successfully — our team will reach out shortly.';
  }
}
?>

<section class="contact-split" id="quote">
  <div class="contact-left">
    <div class="section-label"><div class="section-label-line"></div><span>Get in Touch</span></div>
    <h2 class="section-title">Tell Us About<br>Your <em>Dream Property</em></h2>
    <p class="contact-desc">
      Share what you want and your budget. Our advisors will hand-pick options that match — and walk you through every step.
    </p>
    <div class="contact-info">
      <div class="contact-item">
        <div class="contact-icon-wrap"><i class="bx bxs-phone-call"></i></div>
        <div><div class="contact-item-label">Call / WhatsApp</div><div class="contact-item-val"><?php echo PHONE_NO; ?></div></div>
      </div>
      <div class="contact-item">
        <div class="contact-icon-wrap"><i class="bx bxs-envelope"></i></div>
        <div><div class="contact-item-label">Email</div><div class="contact-item-val"><?php echo SITE_EMAIL_2; ?></div></div>
      </div>
      <div class="contact-item">
        <div class="contact-icon-wrap"><i class="bx bxs-map"></i></div>
        <div><div class="contact-item-label">Office</div><div class="contact-item-val"><?php echo ADDRESS; ?></div></div>
      </div>
    </div>
  </div>

  <div class="contact-right">
    <h3>Send a Quote Request</h3>
    <?php foreach ($alerts as $a) echo '<div class="alert error">'.htmlspecialchars($a).'</div>'; ?>
    <?php if ($success) echo '<div class="alert success">'.htmlspecialchars($success).'</div>'; ?>
    <form action="#quote" method="POST">
      <div class="form-row">
        <div class="form-group"><label>Full Name</label><input type="text" name="name" placeholder="Your name" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" placeholder="you@email.com" required></div>
      </div>
      <div class="form-group">
        <label>Interest</label>
        <select name="interest" required>
          <option value="">Select property type...</option>
          <option>Land</option><option>Apartments</option><option>Houses</option>
        </select>
      </div>
      <div class="form-group"><label>Budget Range</label><input type="text" name="budget" placeholder="e.g. ₦1,500,000 – ₦5,000,000" required></div>
      <div class="form-group"><label>Message</label><textarea name="notes" placeholder="Tell us exactly what you're looking for..."></textarea></div>
      <button type="submit" name="submit-quote" class="btn-primary" style="width:100%">Submit Enquiry</button>
    </form>
  </div>
</section>

<!--==================== TESTIMONIALS ====================-->
<section class="testimonials" id="testimonial">
  <div class="container">
    <div class="section-label reveal"><div class="section-label-line"></div><span>Testimonials</span></div>
    <h2 class="section-title reveal" style="color:#E8F5FA;">Heard From<br><em>Satisfied Clients</em></h2>
    <div class="test-grid">
      <?php
        $testimonials = [
          ['Chloe A. Offiong','Entrepreneur','Working with Arch-Mate has been an absolute pleasure. They guided us through every step with professionalism and attention to detail.','assets/img/testimonials/portrait1.jpg'],
          ['Jeff','Entrepreneur','Arch-Mate truly lives up to its name as the best partner in real estate. Tailored solutions matched our needs, and the security is unparalleled.','assets/img/testimonials/portrait2.jpg'],
          ['Chukwuebuka O.','Entrepreneur','They made the complex world of real estate simple and stress-free. The AMGC/AMEH security team was a huge plus for our peace of mind.','assets/img/testimonials/portrait3.jpg'],
        ];
        foreach ($testimonials as $t) {
          $initials = '';
          foreach (explode(' ', $t[0]) as $p) $initials .= strtoupper(substr($p,0,1));
          $initials = substr($initials, 0, 2);
          echo '<div class="test-card reveal">
            <div class="test-quote-mark">&ldquo;</div>
            <p class="test-text">'.htmlspecialchars($t[2]).'</p>
            <div class="test-author">
              <div class="test-avatar"><img src="'.$t[3].'" alt="'.htmlspecialchars($t[0]).'" onerror="this.parentNode.innerHTML=\''.$initials.'\'"></div>
              <div><div class="test-name">'.htmlspecialchars($t[0]).'</div><div class="test-role">'.htmlspecialchars($t[1]).'</div></div>
            </div>
          </div>';
        }
      ?>
    </div>
  </div>
</section>

<!--==================== TEAM ====================-->
<section class="team" id="team">
  <div class="container">
    <div class="section-header-row reveal">
      <div>
        <div class="section-label reveal"><div class="section-label-line"></div><span>Our Team</span></div>
        <h2 class="section-title reveal">The People<br>Behind <em><?php echo SITE_NAME_SHORT; ?></em></h2>
      </div>
      <a href="./about#team" class="view-all">View All &rarr;</a>
    </div>
    <div class="team-grid">
      <?php
        $team = [
          ['Raphael C. Okonkwo','CEO / MD','assets/img/team/ceo_pic-1.jpg'],
          ['Mbam Abraham','Managing Director','assets/img/team/managing_director.jpeg'],
          ['Folorunsho Feyisike','Business Development Manager','assets/img/team/business_development_manager.jpeg'],
          ['Jaachi D Ebeku','Marketing Director','assets/img/team/marketing_director.jpeg'],
          ['Amandianaeze Chukwuemeka','Administrative Director','assets/img/team/administrative-director.jpeg'],
          ['Isineyi Chidimma Janefrances', 'Human Resources Manager', 'assets/img/team/hr.jpeg'],
        ];
        foreach ($team as $m) {
          echo '<div class="team-card reveal">
            <img class="team-photo" src="'.$m[2].'" alt="'.htmlspecialchars($m[0]).'" loading="lazy"/>
            <div class="team-name">'.htmlspecialchars($m[0]).'</div>
            <div class="team-role">'.htmlspecialchars($m[1]).'</div>
          </div>';
        }
      ?>
    </div>
  </div>
</section>
