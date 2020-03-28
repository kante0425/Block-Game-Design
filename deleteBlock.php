<?php

include('config.php');

$blockId = $_POST['blockId'];

//delete block from blocks table in database
$query = "delete from blocks where id='".$blockId."'";
$result = $conn -> query($query);
if ($result) {
  echo "The block was deleted successfully.";
} else {
  echo "The block was not deleted.";
}