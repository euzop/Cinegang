<?php
session_start();
require 'db.php'; // Adjust this path as needed to ensure it points to your database connection script

// Check if the request is coming from a logged-in user
if (!isset($_SESSION['loggedincinegang']) || $_SESSION['loggedincinegang'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to add items to your watchlist.']);
    exit;
}

// Extract POST data
$userId = $_SESSION['uid'];
$user = $_SESSION['user']; // Ensure this session variable is set during login
$mediaId = isset($_POST['media_id']) ? (int)$_POST['media_id'] : null;
$mediaType = isset($_POST['type']) ? $_POST['type'] : '';

// Validate input
if (is_null($mediaId) || empty($mediaType)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid media ID or type.']);
    exit;
}

// Determine the correct media ID column name based on media type
$mediaIdColumn = '';
switch ($mediaType) {
    case 'anime':
        $mediaIdColumn = 'aid';
        break;
    case 'series':
        $mediaIdColumn = 'sid';
        break;
    case 'movie':
        $mediaIdColumn = 'mid';
        break;
    case 'cartoon':
        $mediaIdColumn = 'cid';
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid media type.']);
        exit;
}

// Check if the media is already in the watchlist
$checkSql = "SELECT * FROM watchlist WHERE media_id = ? AND uid = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ii", $mediaId, $userId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
if ($checkResult->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'This media is already in your watchlist.']);
    exit;
}

// Insert into watchlist
$insertSql = "INSERT INTO watchlist (media_id, uid, user) VALUES (?, ?, ?)";
$insertStmt = $conn->prepare($insertSql);
if (!$insertStmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement: ' . $conn->error]);
    exit;
}
$insertStmt->bind_param("iis", $mediaId, $userId, $user);
if ($insertStmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Added to watchlist successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add to watchlist: ' . $insertStmt->error]);
}
$insertStmt->close();
?>