<?php
include("config.php");
session_start();

$target_parent_dir = "uploads/";

$groupName = str_replace(' ', '', $_POST['groupName']);
$groupName = str_replace('-', '', $groupName);
$groupName = str_replace('(', '', $groupName);
$groupName = str_replace(')', '', $groupName);
$num_new = (isset($_POST['num_new'])) ? $_POST['num_new'] : 0;
$groupIcon = $_FILES['groupIcon'];
$groupType = $_POST['groupType'];

//multiple blocks list

$countBlocks = 0;

if (isset($_FILES['file_uploadBlock'])) {
  $uploadBlocks = $_FILES['file_uploadBlock'];
  $countBlocks = count($uploadBlocks['name']);
}

if (isset($_FILES['new'])) {
  $uploadBlocks = $_FILES['new'];
  $countBlocks = count($uploadBlocks['name']);
}

$temporary_target_file = $target_parent_dir . basename($groupIcon['name']);
$groupIconFileType = strtolower(pathinfo($temporary_target_file, PATHINFO_EXTENSION));

$extensions_array = array("jpg", "jpeg", "png", "PNG", "gif", "svg");

if (!in_array($groupIconFileType, $extensions_array)) {
//  $_SESSION['message'] = "Only jpg, jpeg, png, PNG, gif, and svg files allowed.";
} else {

  //make a directory named after groupName
  $groupDirectory = $target_parent_dir . $groupType;

  //groupIcon Upload
  $groupIconName = str_replace(' ', '', basename($groupIcon['name']));//remove whitespace
  $groupIconName = str_replace('-', '', $groupIconName);
  $groupIconName = str_replace('(', '', $groupIconName);
  $groupIconName = str_replace(')', '', $groupIconName);

  $groupIconFileName = $groupName . $groupIconName;
  $target_file_groupIcon = $groupDirectory . "/icons/" . $groupIconFileName;
  if (!file_exists($target_file_groupIcon)) {
    move_uploaded_file($groupIcon['tmp_name'], $target_file_groupIcon);
  }

  //insert New Block Group into database blockgroup table
  //Convert to base64
  $iconName = $groupIcon['name'];

  //get numbers of existing groups in the blockgroup table in database
  $num_query = "select id from blockgroup";
  $num_result = $conn->query($num_query);
  $num_ex_group = count($num_result->fetch_all());
  $toolboxOrder = $num_ex_group + 5;

  //Insert record
  $query = "insert into blockgroup(name, type, iconName, iconPath, toolboxOrder) values ('"
    . $groupName . "','" . $groupType . "','" . $iconName . "','" . $target_file_groupIcon . "', '" . $toolboxOrder . "')";

  $result = $conn->query($query);

//Upload and Insert Blocks
  if ($countBlocks > 0) {
    for ($i = 0; $i < $countBlocks; $i++) {
      $blockIconName = str_replace(' ', '', basename($uploadBlocks['name'][$i]));//remove whitespace
      $blockIconName = str_replace('-', '', $blockIconName);
      $blockIconName = str_replace('(', '', $blockIconName);
      $blockIconName = str_replace(')', '', $blockIconName);
      $blockIconFileName = $groupName . $blockIconName;
      $target_file_block = $target_parent_dir . $groupType . "/blocks/" . $blockIconFileName;
      $uploadBlockFileType = strtolower(pathinfo($target_file_block, PATHINFO_EXTENSION));
      if (!in_array($uploadBlockFileType, $extensions_array)) {
//        $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG, GIF, and SVG files are allowed.";
      } else {

        //Upload blocks
        if (!file_exists($target_file_block)) {
          move_uploaded_file($uploadBlocks['tmp_name'][$i], $target_file_block);
        }

        //Get Group Id and name from blockgroup table
        $query_get = "select id, name from blockgroup order by id DESC";
        $result = $conn->query($query_get);
        $row = $result->fetch_assoc();
        $groupId = $row['id'];

        //get block numbers of current group from blocks table
        $query_num = "select id from blocks where groupId = '" . $groupId . "'";
        $blockNumbers = $conn->query($query_num)->num_rows + 1;
        $blockName = $groupName . "-" . $blockNumbers;

        //Insert Group Id and name into blocks table
        $query_insert = "insert into blocks(name, iconPath, groupId, groupName) values ('" . $blockName . "', '" . $target_file_block . "', '" . $groupId . "', '" . $groupName . "')";
        $result_insert = $conn->query($query_insert);
      }
    }
  }
}

//send new group to the html page
$query2 = "select * from blockgroup order by toolboxOrder ASC";
$result2 = $conn->query($query2);

$groupData = [];

while ($rowGroup = mysqli_fetch_assoc($result2)) {
  $groupData[] = $rowGroup;
}

//echo $countBlocks;
echo json_encode($groupData);