<?php
require_once "CSVUploader.php";
$csvUploader = new CSVUploader();

if (isset($_FILES['csvFile'])) {
    $result = $csvUploader->processCSV($_FILES['csvFile']);
    echo $result;
} else {
    echo 'No file uploaded.';
}
?>
