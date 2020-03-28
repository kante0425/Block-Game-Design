<?php

include('../config.php');

$id1 = $_POST['id1'];
$index1 = $_POST['index1'];
$id2 = $_POST['id2'];
$index2 = $_POST['index2'];
//$designId = $_POST['designId'];

//update the workingBoardOrder of first group in the temp_wk_groups table in database
$query1 = "update temp_wk_groups set workingBoardOrder='" . $index2 . "' where id='" . $id1 . "'";
$result1 = $conn -> query($query1);


//update the workingBoardOrder of second group in the temp_wk_groups table in database
$query2 = "update temp_wk_groups set workingBoardOrder='" . $index1 . "' where id='" . $id2 . "'";
$result2 = $conn -> query($query2);


//send WKgroup data which is rearranged in temp_wk_groups table in database
$query_getGroupWK = "select * from temp_wk_groups order by workingBoardOrder ASC";
$result_getGroupWK = $conn->query($query_getGroupWK);

$data = [];

while ($rowWK = mysqli_fetch_assoc($result_getGroupWK)) {

  $data[] = $rowWK;

}

echo json_encode($data);