<?php
  $hostname = "localhost";
  $username = "id21442292_chatapp";
  $password = "Rocketmychat@123";
  $dbname = "id21442292_chatapp";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }
?>
