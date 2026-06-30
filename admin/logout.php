<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
if(isset($_SESSION['id'])){
    $_SESSION = array(); 
    session_destroy();
    redirect('index.php');
}else{
    redirect('index.php');
}

?>