<?php

include('../config.php');

$id1 = $_POST['id1'];
$index1 = $_POST['index1'];
$id2 = $_POST['id2'];
$index2 = $_POST['index2'];

//update the toolboxOrder of first group in the blockgroup table in database
$query1 = "update blockgroup set toolboxOrder='" . $index2 . "' where id='" . $id1 . "'";
$result1 = $conn -> query($query1);


//update the toolboxOrder of second group in the blockgroup table in database
$query2 = "update blockgroup set toolboxOrder='" . $index1 . "' where id='" . $id2 . "'";
$result2 = $conn -> query($query2);


//send group data which is rearranged in blockgroup table in database
$query_getGroup = "select * from blockgroup order by toolboxOrder ASC";
$result_getGroup = $conn->query($query_getGroup);

$data = "";
while ($row = mysqli_fetch_assoc($result_getGroup)) {
  $iconPath = $row['iconPath'];
  $groupId = $row['id'];
  $order = (int)$row['toolboxOrder'];
  $data .= "<span class='plus first_icons TB' data-id='" . $groupId . "' data-no='" . $order . "' onclick='showGroupController(" . $groupId . ", " . $order . ")' 
      style='background-image: url(" . $iconPath . ")' ondblclick='addToWK(" . $groupId . ")'></span>";
}

for ($i = 1; $i <= 500; $i++) {
   $data .= "<span class='plus addGroup' onclick='addBlockGroup()' style='background-image: url(" . "assets/img/ASSETS/ICONAsset178.svg" . ")'></span>";
}

echo $data;
