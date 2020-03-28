<?php

include('../config.php');

$groupId = $_POST['groupId'];
$designId = $_POST['designId'];
$designName = $_POST['designName'];

$type = 'group';
if ($groupId == 'c1') {
  $type = 'controller';
  $groupId = 1;
}
if ($groupId == 'c2') {
  $type = 'controller';
  $groupId = 2;
}
if ($groupId == 'c3') {
  $type = 'controller';
  $groupId = 3;
}
if ($groupId == 'c4') {
  $type = 'controller';
  $groupId = 4;
}


if ($type == 'controller') {
  //get iconPath information of controller from controllers table in database
  $query1 = "select * from controllers where id='". $groupId ."'";
  $row1 = $conn -> query($query1) -> fetch_assoc();
  $groupIconPath = $row1['iconPath'];
  $groupName = $row1['name'];
}
else {
  //get the group information from blockgroup table in database
  $get_query = "select * from blockgroup where id='" . $groupId . "'";
  $get_result = $conn->query($get_query);
  $row = $get_result->fetch_assoc();

  $groupName = $row['name'];
  $groupIconPath = $row['iconPath'];
}

//get the number of existing WKgroups where designId=$designId in temp_wk_groups table in database
$num_query = "select id from temp_wk_groups where designId='" . $designId . "'";
$num_result = $conn->query($num_query);
sleep(0.1);
$num_ex_WKgroup = count($num_result->fetch_all());
$WKorder = $num_ex_WKgroup + 1;

//insert the group into temp_wk_groups table in database
$insert_query = "insert into temp_wk_groups(type, groupId, groupName, groupIconPath, designId, designName, workingBoardOrder) values('". $type ."',
 '" . $groupId . "', '" . $groupName . "', '" . $groupIconPath . "', '" . $designId . "', '" . $designName . "', '" . $WKorder . "')";
$insert_result = $conn->query($insert_query);

//send the updated groupWKs information from temp_wk_groups table in database
$query3 = "select * from temp_wk_groups where designId='" . $designId . "' order by workingBoardOrder ASC";
$result3 = $conn->query($query3);
sleep(0.1);

$data = [];
while ($rowWK = mysqli_fetch_assoc($result3)) {

  $data[] = $rowWK;

}

echo json_encode($data);