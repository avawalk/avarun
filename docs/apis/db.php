<?php
  // set default timezone
  date_default_timezone_set('Asia/Bangkok');

  $host = 'localhost';
  $dbname = 'avarun';
  $username = 'root';     // TODO update config
  $password = 'password'; // TODO update config

  try {
      $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
      die("Database connection failed: " . $e->getMessage());
  }
?>
