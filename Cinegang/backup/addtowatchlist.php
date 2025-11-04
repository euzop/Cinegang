<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['add_to_watchlist']) && $data['add_to_watchlist'] && isset($data['media_id'])) {
        $userId = $_SESSION['uid'];
        $userName = $_SESSION['username']; // Assuming this is set when the user logs in
        $mediaId = (int)$data['media_id'];

        // Insert into watchlist logic here
        $sql = "INSERT INTO watchlist (media_id, uid, user) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iis", $mediaId, $userId, $userName);
            if ($stmt->execute()) {
                $response = ['status' => 'success'];
            } else {
                $response = ['status' => 'error', 'message' => $stmt->error];
            }
            $stmt->close();
        } else {
            $response = ['status' => 'error', 'message' => $conn->error];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Invalid input.'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method.'];
}

echo json_encode($response);
?>
