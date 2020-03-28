<?php

include('../config.php');
session_start();

$newBoardTitle = $_POST['newBoardTitle'];

$rowHeight = $_POST['groupType_design'];

//insert new Design into temp_design table in Database
$insert_query = "insert into temp_design(designId, name, rowHeight) values (0, '". $newBoardTitle . "', '" . $rowHeight . "')";
$insert_result = $conn -> query($insert_query);
if ($insert_result) {
  echo "The new temp_design was successfully added.";
}

//get the Design Id from temp_design table in Database
$get_query = "select id from temp_design where name='". $newBoardTitle ."' and rowHeight='". $rowHeight ."' order by id DESC";
$get_result = $conn -> query($get_query);
$row = $get_result -> fetch_assoc();
$designId = $row['designId'];

//echo $designId;
$_SESSION['designId'] = 0;
$_SESSION['designName'] = $newBoardTitle;
$_SESSION['rowHeight'] = $rowHeight;
//$_SESSION['new'] = 'new';
header("Location: ../index.php");