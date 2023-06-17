<?php
$targetFile = "usr/" . basename($_FILES["profilePicture"]["name"]); // Path of the uploaded file

var_dump($targetFile);

// Check if the file is actually an image
$check = getimagesize($_FILES["profilePicture"]["tmp_name"]);
if ($check === false) {
    echo "Error: File is not an image.";
    exit;
}

// Move the uploaded file to the target directory
if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetFile)) {
    echo "Image uploaded successfully.";
} else {
    echo "Error uploading image.";
}
?>

