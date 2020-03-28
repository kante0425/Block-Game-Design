<?php

include('../config.php');

$id1 = $_POST['id1'];
$index1 = $_POST['index1'];
$id2 = $_POST['id2'];
$index2 = $_POST['index2'];
$designId = $_POST['designId'];

//update the groupWKs between the two groupWKs in the temp_wk_groups table in database
$query1 = "update temp_wk_groups set workingBoardOrder = workingBoardOrder -1 where designId='". $designId ."' and workingBoardOrder >'". $index1 ."' and workingBoardOrder <='" . $index2 . "'";
$result1 = $conn -> query($query1);
sleep(0.1);

//update the workingBoardOrder of first group in the temp_wk_groups table in database
$query2 = "update temp_wk_groups set workingBoardOrder='". $index2 ."' where id='". $id1 ."'";
$result2 = $conn -> query($query2);

//send the updated groupWKs information from temp_wk_groups table in database
$query3 = "select * from temp_wk_groups where designId='" . $designId . "' order by workingBoardOrder ASC";
$result3 = $conn -> query($query3);
sleep(0.1);

$data = [];
while ($rowWK = mysqli_fetch_assoc($result3)) {

  $data[] = $rowWK;

}

echo json_encode($data);