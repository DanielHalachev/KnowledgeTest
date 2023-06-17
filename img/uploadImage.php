<?php
$targetDir = "..i/img/usr/"; // Directory where you want to save the uploaded image
$targetFile = basename($_FILES["profilePicture"]["name"]); // Path of the uploaded file

var_dump($targetFile);

var_dump($_FILES["profilePicture"]);
// Check if the file is actually an image
$check = getimagesize($_FILES["profilePicture"]["tmp_name"]);
if ($check === false) {
    echo "Error: File is not an image.";
    exit;
}

var_dump(move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetFile));
// // Move the uploaded file to the target directory
// if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetFile)) {
//     echo "Image uploaded successfully.";
// } else {
//     echo "Error uploading image.";
// }
?>

