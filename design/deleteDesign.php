<?php

include('../config.php');
session_start();

$designId = $_POST['designId'];

//format temp_design table in database
$query1 = "delete from temp_design";
$result1 = $conn->query($query1);

//format temp_wk_groups table in database
$query2 = "delete from temp_wk_groups";
$result2 = $conn->query($query2);

if ($designId != 0) {
//delete the design in the design table in the database
  $query3 = "delete from design where id='" . $designId . "'";
  $result3 = $conn->query($query3);

  //delete the wk_groups in the wk_groups table where designId = $designId in the database
  $query4 = "delete from wk_groups where designId='" . $designId . "'";
  $result4 = $conn->query($query4);
}

if (isset($_SESSION['designName'])) {
  unset($_SESSION['designName']);
}
if (isset($_SESSION['designId'])) {
  unset($_SESSION['designId']);
}
if (isset($_SESSION['rowHeight'])) {
  unset($_SESSION['rowHeight']);
}

echo true;