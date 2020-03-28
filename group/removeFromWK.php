<?php

include('../config.php');

$WKgroupId = $_POST['WKgroupId'];
$wkOrder = $_POST['removeIndex'];
$designId = $_POST['designId'];

//delete WKgroup from temp_wk_groups table in database
$del_query = "delete from temp_wk_groups where id='". $WKgroupId ."'";
$del_result = $conn -> query($del_query);

//update the workingBoardOrder items in temp_wk_groups table in database
$query2 = "update temp_wk_groups set workingBoardOrder= workingBoardOrder - 1 where workingBoardOrder>'".$wkOrder."'";
$result2 = $conn -> query($query2);

//send the updated groupWKs information from temp_wk_groups table in database
$query3 = "select * from temp_wk_groups where designId='" . $designId . "' order by workingBoardOrder ASC";
$result3 = $conn -> query($query3);
//sleep(0.1);

$data = [];
while ($rowWK = mysqli_fetch_assoc($result3)) {

  $data[] = $rowWK;

}

echo json_encode($data);