<?php
// Shared shell for the realtor dashboard area (login, register, and authenticated pages)
// Expects $title to be set before include. Auth pages set $dashAuthShell = true to skip the sidebar.
if (!isset($title)) $title = (defined('SITE_TITLE') ? SITE_TITLE : 'Arch-Mate');
$__self = basename($_SERVER['PHP_SELF'], '.php');
function dActive($p){ global $__self; return ($__self === $p) ? ' active' : ''; }
$__bodyClass = isset($bodyClass) ? $bodyClass : 'dash-body';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($title); ?></title>
  <link rel="shortcut icon" href="assets/img/archmate.png" type="image/x-icon"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
  <link rel="stylesheet" href="assets/css/styles.css" />
<?php if (!empty($extraStyles) && is_array($extraStyles)) foreach ($extraStyles as $extraStyle) { ?>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($extraStyle); ?>" />
<?php } ?>
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
<body class="<?php echo htmlspecialchars($__bodyClass); ?>">
