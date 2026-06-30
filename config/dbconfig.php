<?php
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', '');
if (!defined('DB_NAME')) define('DB_NAME', '');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('URL')) define('URL', 'https://arch-mategroup.com');
if (!defined('SITE_NAME')) define('SITE_NAME', 'Arch-mate Estate and Homes Limited');
if (!defined('SITE_NAME_SHORT')) define('SITE_NAME_SHORT', 'Arch-Mate');
if (!defined('SITE_TITLE')) define('SITE_TITLE', 'Arch-mate Estate and Homes Limited');
if (!defined('ADDRESS')) define('ADDRESS', '2nd Floor at Plot 20/22 Awka Shopping Complex Obi Okoli Junction Awka, Anambra State');
if (!defined('PHONE_NO')) define('PHONE_NO', '+2348104449543');
if (!defined('SITE_EMAIL')) define('SITE_EMAIL', 'support@arch-mategroup.com');
if (!defined('SITE_EMAIL_2')) define('SITE_EMAIL_2', 'archmateestateandhomesltd@gmail.com');
if (!defined('SITE_BANK_NAME')) define('SITE_BANK_NAME', 'United Bank for Africa (UBA)');
if (!defined('SITE_BANK_NO')) define('SITE_BANK_NO', '0123456789');
if (!defined('SITE_FEE')) define('SITE_FEE', '₦3,000');
$con = @mysqli_connect(DB_HOST, DB_USER, DB_PASS);
if (!$con) {
    echo "Error connecting to the database";
} else {
    mysqli_select_db($con, DB_NAME);
}

if (phpversion() < 7.2) {
    exit('PHP Version 7 Required');
}
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    // last request was more than 1 hr ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy(); 
}
// ini_set('session.gc_maxlifetime', 3600);
// ini_set('session.cookie_lifetime', 3600);