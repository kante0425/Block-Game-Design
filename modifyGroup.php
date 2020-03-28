<?php
include('config.php');
session_start();

$countBlocks = 0;
//about Groups
$groupId = $_POST['groupId'];
$groupName = $_POST['groupName'];
$oldGroupName = $_POST['oldGroupName'];
if (isset($_FILES['groupIcon'])) {
  $groupIcon = $_FILES['groupIcon'];
}
$groupType = $_POST['groupType'];
//multiple blocks list
if (isset($_FILES['file_uploadBlock'])) {
  $uploadBlocks = $_FILES['file_uploadBlock'];
  $countBlocks = count($uploadBlocks['name']);
} else {
  $countBlocks = 0;
}

//about Blocks
$blockId = $_POST['hiddenBlockId'];
$oldBlockName = $_POST['hiddenBlockName'];
$blockName = $_POST['blockName'];

if (isset($_FILES['file_replaceBlock'])) {
  $replaceBlock = $_FILES['file_replaceBlock'];
}

//if (isset($_FILES['replace'])) {
//  $replaceBlock = $_FILES['replace'];
//  $countReplace = count($replaceBlock['name']);
//}

//if (isset($_POST['replaceId'])) {
//  $replaceIds = $_POST['replaceId'];
//  $countReplaceId = count($replaceIds);
//}

$num_manage = (isset($_POST['num_manage'])) ? $_POST['num_manage'] : 0;
if (isset($_FILES['manage'])) {
  $uploadBlocks = $_FILES['manage'];
  $countBlocks = count($uploadBlocks['name']);
}

$target_parent_dir = "uploads/";

$extensions_array = array("jpg", "jpeg", "png", "PNG", "gif", "svg");

//update groupName
if ($groupName !== $oldGroupName) {
//update groupName in Database****************
  $query = "update blockgroup set name='".$groupName."' where id='".$groupId."'";
  $result = $conn -> query($query);
}

//update groupIcon
if (isset($_FILES['groupIcon'])) {
  $temporary_target_file = $target_parent_dir . basename($groupIcon['name']);
//to get the extension of selected file
  $groupIconFileType = strtolower(pathinfo($temporary_target_file, PATHINFO_EXTENSION));
  if (!in_array($groupIconFileType, $extensions_array)) {
//    $_SESSION['message'] = "Only jpg, jpeg, png, PNG, gif, and svg files allowed.";
  } else {

//groupIcon Upload
    $groupIconName = str_replace(' ', '', basename($groupIcon['name']));
    $groupIconName = str_replace('-', '', $groupIconName);
    $groupIconName = str_replace('(', '', $groupIconName);
    $groupIconName = str_replace(')', '', $groupIconName);
    $groupIconFileName = $groupName . $groupIconName;
    $target_file_groupIcon = $target_parent_dir.$groupType."/icons/".$groupIconFileName;
    move_uploaded_file($groupIcon['tmp_name'], $target_file_groupIcon);//done

//update groupIcon in database********
    $query = "update blockgroup set iconPath='".$target_file_groupIcon."' where id='".$groupId."'";
    $result = $conn -> query($query);
  }
}

//Upload Blocks and save them into database
if ($countBlocks>0) {
  for ($i = 0;$i < $countBlocks;$i++) {
    $blockIconName = str_replace(' ', '', basename($uploadBlocks['name'][$i]));//remove whitespace
    $blockIconName = str_replace('-', '', $blockIconName);
    $blockIconName = str_replace('(', '', $blockIconName);
    $blockIconName = str_replace(')', '', $blockIconName);

    $blockIconFileName = $groupName . $blockIconName;
    $target_file_block = $target_parent_dir . $groupType . "/blocks/" . $blockIconFileName;
    $uploadBlockFileType = strtolower(pathinfo($target_file_block, PATHINFO_EXTENSION));
    if (!in_array($uploadBlockFileType, $extensions_array)) {
      $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG, GIF, and SVG files are allowed.";
    } else {

      //Upload blocks
      move_uploaded_file($uploadBlocks['tmp_name'][$i], $target_file_block);

      //get block numbers of current group from blocks table
      $query_num = "select id from blocks where groupId = '".$groupId."'";
      $blockNumbers = $conn -> query($query_num) -> num_rows + 1;
      $blockName = $groupName . "-" . $blockNumbers;

      //Insert Group Id and name into blocks table
      $query_insert = "insert into blocks(name, iconPath, groupId, groupName) values ('".$blockName."', '".$target_file_block."', '".$groupId."', '".$groupName."')";
      $result_insert = $conn -> query($query_insert);
    }
  }
}

//ABOUT BLOCK MANAGEMENT
//update Block name in the blocks table in database
if ($oldBlockName !== $blockName) {
  $query = "update blocks set name='" . $blockName . "' where id='" . $blockId . "'";
  $result = $conn->query($query);

}

//replace BLOCKs
if (isset($_FILES['file_replaceBlock'])) {

//  for ($a = 0; $a < $countReplaceId; $a++) {
    $select_query = "select * from blocks where id = '".$blockId."'";

    $row = $conn -> query($select_query) -> fetch_assoc();

    $oldIconPath = $row['iconPath'];

    //get directory of the old icon
    $iconSplit = preg_split("#/#", $oldIconPath);

    $oldIconName = $iconSplit[count($iconSplit) - 1];

    $iconFolder = str_replace($oldIconName, '', $oldIconPath);//here

    $newIconName = str_replace(' ', '', basename($replaceBlock['name']));
    $newIconName = str_replace('-', '', $newIconName);
    $newIconName = str_replace('(', '', $newIconName);
    $newIconName = str_replace(')', '', $newIconName);

    $target_file_new_block = $iconFolder.$newIconName;

    $newBlockFileType = strtolower(pathinfo($target_file_new_block, PATHINFO_EXTENSION));

    if (!in_array($newBlockFileType, $extensions_array)) {
//    $_SESSION['message'] = "Only svg files allowed.";
    } else {
      //Upload new block
      if (!file_exists($target_file_new_block)) {
        move_uploaded_file($replaceBlock['tmp_name'], $target_file_new_block);
      }

      //Update block table in database
      $update_query = "update blocks set iconPath = '".$target_file_new_block."' where id='".$blockId."'";
      $update_result = $conn -> query($update_query);

    }
//  }
}

//send new group to the html page
$query1 = "select * from blockgroup order by toolboxOrder ASC";
$result1 = $conn -> query($query1);

$groupData = [];

while ($row1 = mysqli_fetch_assoc($result1)) {
  $groupData[] = $row1;
}

echo json_encode($groupData);