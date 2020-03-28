<?php

include('../config.php');
session_start();

$designId = $_POST['designId'];
$oldDesignName = $_POST['oldDesignName'];
$oldDesignType = $_POST['oldDesignType'];
$designName = $_POST['editBoardTitle'];
$rowHeight = $_POST['groupType_design'];

//update the design in the temp_design table
$query1 = "update temp_design set name='". $designName ."', rowHeight='". $rowHeight ."'";
$result1 = $conn -> query($query1);

//update the designName in the temp_wk_groups table
$query2 = "update temp_wk_groups set designName='". $designName ."' where designId = '". $designId ."'";
$result2 = $conn -> query($query2);

$_SESSION['designName'] = $designName;
$_SESSION['rowHeight'] = $rowHeight;

$true = "true";

if ($designName !== $oldDesignName || $rowHeight !== $oldDesignType) {
  $_SESSION['change'] = $true;
}

//echo $designId, $rowHeight, $designName;
header("Location: ../index.php");