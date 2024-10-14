<?php 
session_start();

if (isset($_SESSION['user_account_id']) && !empty($_SESSION['user_account_id'])) {
    header('Location: apps.php');
    exit;
}

?>