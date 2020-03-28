<?php

include('config.php');

$groupId = $_POST['groupId'];

//get group data from blockgroup table in DB
$query = "select * from blockgroup where id='" . $groupId . "'";
$result = $conn->query($query);
$groupData = $result->fetch_assoc();

//get block data from blocks table in DB
$query_block = "select * from blocks where groupId='" . $groupId . "'";
$blockResult = $conn->query($query_block);

$blockData = [];

$k = 0;
while ($rowBlock = mysqli_fetch_assoc($blockResult)) {
  $blockData[] = $rowBlock;
  $k++;
}

//$row_num_blocks = count($blockResult->fetch_all());

$data = [];
$data['groupData'] = $groupData;
$data['num'] = $k;
$data['blockData'] = $blockData;

echo json_encode($data);
