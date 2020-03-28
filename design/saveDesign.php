<?php

include('../config.php');
session_start();

$temp_design_id = $_POST['designId'];

//get the temp_design in database
$query1 = "select * from temp_design";
$result1 = $conn -> query($query1) -> fetch_assoc();

//add new design
if ($temp_design_id == 0) {
  
  //insert the temp_design into the design table in database
  $query2 = "insert into design(name, rowHeight) values ('".$result1['name']."', '".$result1['rowHeight']."')";
  $result2 = $conn -> query($query2);

  //get the id of the last inserted design from the design table in database
  $get_query = "select * from design order by id DESC";
  $result_query = $conn -> query($get_query) -> fetch_assoc();
  $temp_design_id = $result_query['id'];

  $_SESSION['designId'] = $temp_design_id;
  $_SESSION['designName'] = $result1['name'];
  $_SESSION['rowHeight'] = $result1['rowHeight'];

  //update temp_design table
  $query = "update temp_design set designId = '". $temp_design_id ."' where designId = 0";
  $result = $conn -> query($query);

  //update temp_wk_groups table
  $query5 = "update temp_wk_groups set designId = '". $temp_design_id ."' where designId = 0";
  $result5 = $conn -> query($query5);

} else {//resave the existing design in the wk_groups table in database

  //update the design table
  $query6 = "update design set name = '". $result1['name'] ."', rowHeight = '". $result1['rowHeight'] ."' where id='". $temp_design_id ."'";
  $result6 = $conn -> query($query6);

  //delete the past information of the design in the wk_groups
  $query3 = "delete from wk_groups where designId = '" . $temp_design_id . "'";
  $result3 = $conn->query($query3);

}

//get all information from temp_wk_groups table in database
$query4 = "select * from temp_wk_groups";
$result4 = $conn->query($query4);

//insert temp_wk_groups data into wk_groups table in database
while ($row4 = mysqli_fetch_assoc($result4)) {
  $query5 = "insert into wk_groups(type, groupId, groupName, groupIconPath, designId, designName, workingBoardOrder) values ('" .
    $row4['type'] . "', '" . $row4['groupId'] . "', '" . $row4['groupName'] . "', '" . $row4['groupIconPath'] . "', '" . $temp_design_id . "',
               '" . $row4['designName'] . "', '" . $row4['workingBoardOrder'] . "')";
  $result5 = $conn->query($query5);
}

////format temp_design table in database
//$query6 = "delete from temp_design";
//$result6 = $conn -> query($query6);
//
////format temp_wk_groups table in database
//$query7 = "delete from temp_wk_groups";
//$result7 = $conn -> query($query7);
//
//if (isset($_SESSION['designName'])) {
//  unset($_SESSION['designName']);
//}
//if (isset($_SESSION['designId'])) {
//  unset($_SESSION['designId']);
//}
//if (isset($_SESSION['rowHeight'])) {
//  unset($_SESSION['rowHeight']);
//}

echo true;

