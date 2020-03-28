<?php
include('config.php');
session_start();

$blockId = $_POST['blockId'];

//get block data from blocks table in DB
$query = "select * from blocks where id='" . $blockId . "'";
$result = $conn -> query($query);
$blockData = $result -> fetch_assoc();

echo json_encode($blockData);