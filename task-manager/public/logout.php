<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
session_unset();   
session_destroy();  

// Redirect to login page
header("Location: login.php");
exit();
