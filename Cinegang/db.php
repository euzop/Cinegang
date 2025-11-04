<?php
  $conn = new mysqli("localhost", "root", "", "cinegang");
  if($conn -> connect_error) {
      die("Connection failed: " . $conn -> connect_error);
   }
?>