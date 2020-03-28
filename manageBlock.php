<?php
include('config.php');
session_start();

$blockId = $_POST['hiddenBlockId'];
$oldBlockName = $_POST['hiddenBlockName'];
$blockName = $_POST['blockName'];
$replaceBlock = $_FILES['file_replaceBlock'];

$target_parent_dir = "uploads/";

$extensions_array = array("svg");

//update Block name in the blocks table in database
if ($oldBlockName !== $blockName) {
  $query = "update blocks set name='" . $blockName . "' where id='" . $blockId . "'";
  $result = $conn->query($query);
  if ($result) {
    echo "The groupName updated successfully.";
  }
}

if (isset($_FILES['file_replaceBlock'])) {
  $select_query = "select * from blocks where id = '".$blockId."'";

  $row = $conn -> query($select_query) -> fetch_assoc();

  $oldIconPath = $row['iconPath'];
//get directory of the old icon
  $iconSplit = preg_split("#/#", $oldIconPath);

  $oldIconName = $iconSplit[count($iconSplit) - 1];

  $iconFolder = str_replace($oldIconName, '', $oldIconPath);//here

  $newIconName = str_replace(' ', '', basename($replaceBlock['name']));

  $target_file_new_block = $iconFolder.$newIconName;

  $newBlockFileType = strtolower(pathinfo($target_file_new_block, PATHINFO_EXTENSION));

  if (!in_array($newBlockFileType, $extensions_array)) {
    $_SESSION['message'] = "Only svg files allowed.";
  } else {
    //Upload new block
    if (!file_exists($target_file_new_block)) {
      move_uploaded_file($replaceBlock['tmp_name'], $target_file_new_block);
    }

    //Update block table in database
    $update_query = "update blocks set iconPath = '".$target_file_new_block."' where id='".$blockId."'";
    if ($conn -> query($update_query) === TRUE) {
      echo "The block updated successfully.";
    } else {
      echo "Error updating record: ".$conn -> error;
    }

    $_SESSION['message'] = 'success';

  }
}
echo true;
header("Location: ./index.php");