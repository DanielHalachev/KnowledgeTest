<?php
include_once "DatabaseHandler.php";
$code = $_GET['code'];

$testExists = DatabaseHandler::testExists($code);

$response = array('exists' => $testExists);

header('Content-Type: application/json');
echo json_encode($response);
?>

