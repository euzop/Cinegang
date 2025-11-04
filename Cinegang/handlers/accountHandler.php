<?php
session_start();
include '../db.php';

// Ensure user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: ../login.php");
    exit();
}

// Get user ID from session
$uid = $_SESSION['uid'];

// Initialize variables with default or empty values
$name = "";
$nickname = "";
$email = "";
$bio = "";
$password = "";
$confirmpassword = "";
$profile_pic = "";
$mobile = "";

// Check if form fields are set before assigning values
if (isset($_POST['name'])) {
    $name = strtolower(trim($_POST['name']));
}
if (isset($_POST['nickname'])) {
    $nickname = trim($_POST['nickname']);
}
if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
}
if (isset($_POST['bio'])) {
    $bio = trim($_POST['bio']);
}
if (isset($_POST['password'])) {
    $password = trim($_POST['password']);
}
if (isset($_POST['confirmpassword'])) {
    $confirmpassword = trim($_POST['confirmpassword']);
}
if (isset($_POST['profile_pic'])) {
    $profile_pic = trim($_POST['profile_pic']);
}
if (isset($_POST['mobile'])) {
    $mobile = trim($_POST['mobile']);
}

// Validate phone number format
if (!empty($mobile) && !preg_match("/^[0-9]{10}$/", $mobile)) {
    $_SESSION['message'] = "Phone number should be a 10-digit numeric value.";
    header("Location: ../account.php?error=1");
    exit();
}

// Validate passwords
if (!empty($password) && $password === $confirmpassword) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
} else {
    $hashed_password = null;
}

// Handle file upload if present
if (!empty($_FILES['profile_upload']['name'])) {
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES['profile_upload']['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a real image
    $check = getimagesize($_FILES['profile_upload']['tmp_name']);
    if ($check !== false) {
        // Move the file
        if (move_uploaded_file($_FILES['profile_upload']['tmp_name'], $target_file)) {
            $profile_pic = $target_file;
        }
    }
}

// Prepare SQL update statement
$sql = "UPDATE users SET name=?, nickname=?, email=?, bio=?, profile_pic=?, mobile=?";
$params = [$name, $nickname, $email, $bio, $profile_pic, $mobile];

// Include password in the update if it's set
if ($hashed_password) {
    $sql .= ", password=?";
    $params[] = $hashed_password;
}

$sql .= " WHERE uid=?";
$params[] = $uid;

$stmt = $conn->prepare($sql);

// Determine parameter types
$types = str_repeat("s", count($params) - 1) . "i"; // All strings except the last parameter (uid) which is an integer

// Bind parameters
$stmt->bind_param($types, ...$params);

// Execute the statement
if ($stmt->execute()) {
    $_SESSION['message'] = "Account updated successfully!";
    header("Location: ../account.php?success=1");
} else {
    $_SESSION['message'] = "Error updating account: " . $stmt->error;
    header("Location: ../account.php?error=1");
}

// Close the statement and connection
$stmt->close();
$conn->close();
exit();
?>
