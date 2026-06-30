<?php
// Determine current page for active nav highlighting
$__self = basename($_SERVER['PHP_SELF'], '.php');
function navActive($page){
  global $__self;
  return ($__self === $page) ? ' active' : '';
}

function propertyMediaSrc($rawImg, $prefix = ''){
  $rawImg = trim((string)$rawImg);
  if ($rawImg === '') return '';

  $decoded = json_decode($rawImg, true);
  $path = $rawImg;

  if (is_array($decoded) && !empty($decoded)) {
    $imageExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    foreach ($decoded as $item) {
      $item = trim((string)$item);
      if ($item === '') continue;
      $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
      if (in_array($ext, $imageExts, true)) {
        $path = $item;
        break;
      }
      if ($path === $rawImg) {
        $path = $item;
      }
    }
  }

  return $prefix . ltrim($path, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?php echo SITE_NAME; ?> — Premium real estate, land, and homes across Anambra, Enugu and Asaba. Your best partner in real estate." />
  <link rel="shortcut icon" href="assets/img/archmate.png" type="image/x-icon"/>
  <link href="assets/boxicons%402.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
  <link rel="stylesheet" href="assets/css/styles.css?v=3" />
  <title><?php echo SITE_TITLE; ?></title>
  <!-- Smartsupp Live Chat script -->
  <script type="text/javascript">
  var _smartsupp = _smartsupp || {};
  _smartsupp.key = 'eff75b10ff8915a0cd5ef150e1fa2809ecb38315';
  window.smartsupp||(function(d) {
    var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
    s=d.getElementsByTagName('script')[0];c=d.createElement('script');
    c.type='text/javascript';c.charset='utf-8';c.async=true;
    c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
  })(document);
  </script>
  <noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>

</head>
<body>

<!--==================== HEADER ====================-->
<header class="header" id="header">
  <nav class="nav container" role="navigation">
    <a href="./" class="nav-logo" aria-label="<?php echo SITE_NAME_SHORT; ?> home">
      <div class="nav-logo-mark">AM</div>
      <div class="nav-logo-text"><?php echo SITE_NAME_SHORT; ?></div>
    </a>

    <div class="nav-links" id="nav-menu">
      <a href="./" class="nav-link<?php echo navActive('index').navActive('home'); ?>">Home</a>
      <a href="./properties" class="nav-link<?php echo navActive('properties'); ?>">Properties</a>
      <div class="nav-dropdown-wrap">
        <button type="button" class="nav-link nav-dropdown-trigger<?php echo navActive('properties'); ?>" data-division-toggle aria-haspopup="true" aria-expanded="false">
          Our Divisions <i class="bx bx-chevron-down"></i>
        </button>
        <div class="nav-dropdown" data-division-dropdown>
          <a href="./properties?division=abuja#dividion-tabs" data-division-target="abuja">Archmate Abuja Division</a>
          <a href="./properties?division=south_east#dividion-tabs" data-division-target="south_east">Archmate South East Division</a>
        </div>
      </div>
      <a href="./about" class="nav-link<?php echo navActive('about'); ?>">About</a>
      <a href="./contact" class="nav-link<?php echo navActive('contact'); ?>">Contact</a>
    </div>

    <a href="./contact" class="nav-cta">Get Started</a>
    <i class="bx bx-menu nav-toggle" id="nav-toggle" aria-label="Open menu"></i>
  </nav>
</header>
