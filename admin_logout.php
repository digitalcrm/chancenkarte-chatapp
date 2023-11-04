<?php 
  session_start();
  $_SESSION['admin_id'];
  session_unset();
  session_destroy();
  $_SESSION[''];

    header("location: admin_login.php");

?>

<?php include_once "header.php"; ?>
