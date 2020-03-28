<?php

include('../config.php');
session_start();

$openDesignId = $_POST['openDesignId'];

//format temp_design table in database
$query6 = "delete from temp_design";
$result6 = $conn -> query($query6);

//format temp_wk_groups table in database
$query7 = "delete from temp_wk_groups";
$result7 = $conn -> query($query7);

//get Information of openDesign from design table in DB
$get_query = "select * from design where id='" . $openDesignId . "'";
$result = $conn->query($get_query);
$row = $result->fetch_assoc();

$_SESSION['designId'] = $openDesignId;
$_SESSION['designName'] = $row['name'];
$_SESSION['rowHeight'] = $row['rowHeight'];

//insert the design into temp_design table in DB
$query1 = "insert into temp_design(designId, name, rowHeight) values ('" . $openDesignId . "', '" . $row['name'] . "', '" . $row['rowHeight'] . "')";
$result1 = $conn->query($query1);

//get the WKgroup information where designId=$openDesignId from wk_groups table in database
$query2 = "select * from wk_groups where designId='" . $openDesignId . "'";
$result2 = $conn->query($query2);

//insert the WKgroups information into temp_wk_groups table in database
while ($row2 = mysqli_fetch_assoc($result2)) {
  $query3 = "insert into temp_wk_groups(type, groupId, groupName, groupIconPath, designId, designName, workingBoardOrder) values ('".
            $row2['type']."', '".$row2['groupId']."', '".$row2['groupName']."', '".$row2['groupIconPath']."', '".$row2['designId']."', 
            '".$row2['designName']."', '".$row2['workingBoardOrder']."')";
  $result3 = $conn -> query($query3);
}

header("Location: ../index.php");