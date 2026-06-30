<?php
session_start();
include_once "../config/dbconfig.php";
include_once "../config/func.inc.php";
if(isset($_SESSION['email'])){
    $_SESSION = array(); 
    session_destroy();
    redirect('./affiliate');
}else{
    redirect('./affiliate');
}

