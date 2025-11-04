<?php
session_start();
include 'db.php'; // Make sure to include your database connection

// Ensure the user is logged in
if (!isset($_SESSION['loggedincinegang']) || $_SESSION['loggedincinegang'] != true) {
    header("Location: ./login.php");
    exit();
}

$uid = $_SESSION['uid'];

// Define the target directory for uploads
$target_dir = "uploads/"; // Make sure this directory exists and is writable
$target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        exit();
    }
}

// Limit the file size (5MB in this case)
if ($_FILES["profile_pic"]["size"] > 5000000) {
    echo "Sorry, your file is too large.";
    exit();
}

// Allow certain file formats
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    exit();
}

// Attempt to move the uploaded file to the target directory
if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
    // Update the database with the new profile picture path
    $sql = "UPDATE users SET profile_pic = ? WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $target_file, $uid);

    if ($stmt->execute()) {
        // Redirect back to profile page or give success message
        header("Location: profile.php?uploadsuccess=1");
        exit();
    } else {
        echo "Error updating profile picture.";
    }
    $stmt->close();
} else {
    echo "Sorry, there was an error uploading your file.";
}

$conn->close();
?>
