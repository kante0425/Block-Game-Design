<?php

include('../config.php');

$id1 = $_POST['id1'];
$index1 = $_POST['index1'];
$id2 = $_POST['id2'];
$index2 = $_POST['index2'];

//update the groups between the two groups in the blockgroup table in database
$query1 = "update blockgroup set toolboxOrder = toolboxOrder +1 where toolboxOrder >='". $index2 ."' and toolboxOrder <'" . $index1 . "'";
$result1 = $conn -> query($query1);

//update the toolboxOrder selected group in the blockgroup table in database
$query2 = "update blockgroup set toolboxOrder='". $index2 ."' where id='". $id1 ."'";
$result2 = $conn -> query($query2);

//send the updated group information from blockgroup table in database
$query3 = "select * from blockgroup order by toolboxOrder ASC";
$result3 = $conn -> query($query3);

$data = "";
while ($row = mysqli_fetch_assoc($result3)) {
  $iconPath = $row['iconPath'];
  $groupId = $row['id'];
  $order = (int)$row['toolboxOrder'];
  $data .= "<span class='plus first_icons TB' data-id='" . $groupId . "' data-no='" . $order . "' onclick='showGroupController(" . $groupId . ", " . $order . ")' 
      style='background-image: url(" . $iconPath . ")' ondblclick='addToWK(" . $groupId . ")'></span>";
}

for ($i = 1; $i <= 500; $i++) {
  $data .= "<span class='plus addGroup' style='background-image: url(" . "assets/img/ASSETS/ICONAsset178.svg" . ")'></span>";
}

echo $data;