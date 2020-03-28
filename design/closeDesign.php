<?php

include('../config.php');
session_start();

//format temp_design table in database
$query6 = "delete from temp_design";
$result6 = $conn -> query($query6);

//format temp_wk_groups table in database
$query7 = "delete from temp_wk_groups";
$result7 = $conn -> query($query7);

if (isset($_SESSION['designName'])) {
  unset($_SESSION['designName']);
}
if (isset($_SESSION['designId'])) {
  unset($_SESSION['designId']);
}
if (isset($_SESSION['rowHeight'])) {
  unset($_SESSION['rowHeight']);
}
if (isset($_SESSION['change'])) {
  unset($_SESSION['change']);
}

echo isset($_SESSION['designName']);