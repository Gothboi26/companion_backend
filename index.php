<?php
// Basic redirect or some logic to direct to a main page
header("Access-Control-Allow-Origin: https://seniorcare-flt3.onrender.com/");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");
header('Location: login.php'); // Or any page you want to load first
exit;
?>
