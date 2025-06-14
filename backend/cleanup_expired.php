<?php
include 'db.php';

// Mark expired vehicles unavailable
$conn->query("
  UPDATE vehicles
  SET available = 0
  WHERE NOW() > expiry
    AND available = 1
");
$conn->close();
