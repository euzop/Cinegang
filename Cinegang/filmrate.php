<?php
session_start();
include 'db.php';

if (isset($_POST['user_id']) && isset($_POST['film_id']) && isset($_POST['type']) && isset($_POST['rating'])) {
    $user_id = $_POST['user_id'];
    $film_id = $_POST['film_id'];
    $type = $_POST['type'];
    $rating = $_POST['rating'];

    $sql = "INSERT INTO ratings (user_id, film_id, type, rating) VALUES ($user_id, $film_id, '$type', $rating)
            ON DUPLICATE KEY UPDATE rating=$rating";
    if ($conn->query($sql) === TRUE) {
        echo "Rating submitted.";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid parameters.";
}
?>
