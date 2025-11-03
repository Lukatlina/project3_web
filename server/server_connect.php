<?php

$serverName = "127.0.0.1";
$userName = "lunamoon";
$server_pw = "digda1210";


$conn = mysqli_connect( $serverName , $userName , $server_pw , "weverse" );

if (!$conn) {
  $error = "" .mysqli_connect_error();
  die("Database connection failed: " . mysqli_connect_error());
}

?>