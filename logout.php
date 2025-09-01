<?php
session_start();
include 'dbconnection.php';
unset($_SESSION['user_logado']);
session_destroy();
header("Location: ".BASE_URL . "login");
?>