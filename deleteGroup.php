<?php

include('config.php');

$groupId = $_POST['groupId'];
$toolboxOrder = $_POST['toolboxOrder'];

//find whether the group is in the wk_groups
$query = "select id from wk_groups where groupId = '". $groupId ."'";
$result = $conn -> query($query);
$num = count($result -> fetch_all());

if ($num !== 0) {
//  echo 'false';
  $designNames = [];

  $query4 = "select * from wk_groups where groupId = '". $groupId ."' order by designId";
  $result4 = $conn -> query($query4);

  while ($rowNum = mysqli_fetch_assoc($result4)) {
    $new = true;
    $designName = $rowNum['designName'];
    if (count($designNames) > 0) {
      foreach ($designNames as $val) {
        if ($designName == $val) {
          $new = false;
        }
      }
      if ($new) {
          array_push($designNames, $designName);
      }
    } else {
      array_push($designNames, $designName);
    }

  }

  $data = [];
  $data['designNames'] = $designNames;
  $data['success'] = false;

  echo json_encode($data);

} else {
  //delete blockGroup from blockgroup table
  $group_query = "delete from blockgroup where id='".$groupId."'";
  $group_result = $conn -> query($group_query);

//update toolBoxOrder in the blockgroup table after deleting the blockgroup
  $query1 = "update blockgroup set toolboxOrder = toolboxOrder -1 where toolboxOrder >'". $toolboxOrder ."'";
  $result1 = $conn -> query($query1);

//delete blocks from blocks table where gruopId = $groupId
  $block_query = "delete from blocks where groupId='".$groupId."'";
  $block_result = $conn -> query($block_query);

//send the updated group information from blockgroup table in database
  $query3 = "select * from blockgroup order by toolboxOrder ASC";
  $result3 = $conn -> query($query3);

  $data1 = "";
  while ($row = mysqli_fetch_assoc($result3)) {
    $iconPath = $row['iconPath'];
    $groupId = $row['id'];
    $order = (int)$row['toolboxOrder'];
    $data1 .= "<span class='plus first_icons TB' data-id='" . $groupId . "' data-no='" . $order . "' onclick='showGroupController(" . $groupId . ", " . $order . ")' 
      style='background-image: url(" . $iconPath . ")' ondblclick='addToWK(" . $groupId . ")'></span>";
  }

  for ($i = 1; $i <= 500; $i++) {
    $data1 .= "<span class='plus addGroup' onclick='addBlockGroup()' style='background-image: url(" . "assets/img/ASSETS/ICONAsset178.svg" . ")'></span>";
  }

  $push_data = [];
  $push_data['success'] = true;
  $push_data['data'] = $data1;

  echo json_encode($push_data);
}


