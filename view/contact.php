<?php 
include "../config/dbconfig.php";
include "../config/func.inc.php";

$title = 'Contact | '.SITE_NAME;
$alerts = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-quote'])) {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $interest = trim($_POST['interest'] ?? '');
    $budget   = trim($_POST['budget'] ?? '');
    $notes    = trim($_POST['notes'] ?? '');

    if ($name === '') {
        $alerts[] = 'Please enter your name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alerts[] = 'Please enter a valid email.';
    }
    if ($interest === '') {
        $alerts[] = 'Please select an interest.';
    }
    if ($notes === '') {
        $alerts[] = 'Please enter a message.';
    }

    if (empty($alerts)) {
        $name = mysqli_real_escape_string($con, $name);
        $email = mysqli_real_escape_string($con, $email);
        $interest = mysqli_real_escape_string($con, $interest);
        $budget = mysqli_real_escape_string($con, $budget);
        $notes = mysqli_real_escape_string($con, $notes);
        $date = date('Y-m-d H:i:s');

        if (createQuote($con, $name, $email, $interest, $budget, $notes, $date) === TRUE) {
            $success = 'We\'ve received your submission. Someone will reach out to you soon.';
        } else {
            $alerts[] = 'Unable to submit your message right now.';
        }
    }
}

include "includes/header.php"; 

?>
<section class="page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <div class="section-label"><div class="section-label-line"></div><span>Contact</span></div>
      <h1 class="section-title">Let's Find Your<br><em>Dream Property</em></h1>
      <p class="page-hero-sub">
        Whether you're a first-time buyer or a seasoned investor, our team is ready to guide you every step of the way.
      </p>
    </div>
  </div>
</section>

<div class="contact-split" id="mapform">
  <div class="contact-left">
    <div class="section-label"><div class="section-label-line"></div><span>Reach Us</span></div>
    <h2 class="section-title">Talk to an<br><em>Advisor</em></h2>
    <p class="contact-desc">Pick the channel that suits you. We respond on the same business day, every day.</p>
    <div class="contact-info">
      <div class="contact-item">
        <div class="contact-icon-wrap"><i class="bx bxs-phone-call"></i></div>
        <div><div class="contact-item-label">Call</div><div class="contact-item-val"><a href="tel:<?php echo PHONE_NO; ?>" style="color:inherit"><?php echo PHONE_NO; ?></a></div></div>
      </div>
      <div class="contact-item">
        <div class="contact-icon-wrap"><i class="bx bxl-whatsapp"></i></div>
        <div><div class="contact-item-label">WhatsApp</div><div class="contact-item-val"><a href="https://wa.me/<?php echo preg_replace('/\D/','',PHONE_NO); ?>" style="color:inherit" target="_blank" rel="noopener">Chat now</a></div></div>
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
    <h3>Send Us a Message</h3>
    <?php foreach ($alerts as $a) echo '<div class="alert error">'.htmlspecialchars($a).'</div>'; ?>
    <?php if ($success) echo '<div class="alert success">'.htmlspecialchars($success).'</div>'; ?>
    <form action="#mapform" method="POST">
      <div class="form-row">
        <div class="form-group"><label>Full Name</label><input type="text" name="name" placeholder="Your name" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" placeholder="you@email.com" required></div>
      </div>
      <div class="form-group">
        <label>Interest</label>
        <select name="interest">
          <option value="General">General enquiry</option>
          <option>Land</option><option>Apartments</option><option>Houses</option>
        </select>
      </div>
      <div class="form-group"><label>Budget (optional)</label><input type="text" name="budget" placeholder="e.g. ₦1,500,000 – ₦5,000,000"></div>
      <div class="form-group"><label>Message</label><textarea name="notes" placeholder="How can we help?" required></textarea></div>
      <button type="submit" name="submit-quote" class="btn-primary" style="width:100%">Send Message</button>
    </form>
  </div>
</div>

<!-- Map embed -->
<section style="padding:0;background:var(--offwhite)">
  <iframe
    src="https://www.google.com/maps?q=<?php echo urlencode(ADDRESS); ?>&output=embed"
    width="100%" height="450" style="border:0;display:block;filter:grayscale(15%)" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
    title="<?php echo SITE_NAME_SHORT; ?> office location"></iframe>
</section>

<?php include "includes/footer.php"; ?>
