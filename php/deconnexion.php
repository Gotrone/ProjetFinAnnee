<?php
  session_start();
  session_destroy();
  header('Location: ../portail.php');
  exit();
 ?>
