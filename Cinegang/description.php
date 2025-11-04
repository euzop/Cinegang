<?php
session_start();
if (!isset($_SESSION['loggedincinegang']) || $_SESSION['loggedincinegang'] !== true) {
    header("Location: ./login.php");
    exit();
}

include 'db.php';

// Retrieve user's details from the database
$userId = $_SESSION['uid'];
$userSql = "SELECT name FROM users WHERE uid = ?";
$userStmt = $conn->prepare($userSql);
if ($userStmt) {
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        $username = $user['name'];
    } else {
        echo "User not found.";
        exit();
    }
    $userStmt->close();
} else {
    echo "Failed to prepare SQL statement: " . $conn->error;
    exit();
}

// Function to convert Imgur page link to direct image link
function convertImgurLink($link) {
    if (preg_match('/^https:\/\/imgur\.com\/([a-zA-Z0-9]+)$/', $link, $matches)) {
        return 'https://i.imgur.com/' . $matches[1] . '.jpg';
    }
    return $link;
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review'])) {
    $mediaId = (int)$_POST['media_id'];
    $reviewText = $_POST['review'];
    $rating = (float)$_POST['star']; // Ensure this matches the name attribute in the form

    // Check if the user has already reviewed this media
    $checkSql = "SELECT * FROM review WHERE media_id = ? AND uid = ?";
    $checkStmt = $conn->prepare($checkSql);
    if ($checkStmt) {
        $checkStmt->bind_param("ii", $mediaId, $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            $reviewError = "You have already submitted a review for this media.";
            $checkStmt->close();
        } else {
            $checkStmt->close();

            // Proceed to insert the review
            $insertSql = "INSERT INTO review (media_id, uid, user, datetime, star, review_desc) VALUES (?, ?, ?, NOW(), ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt) {
                $insertStmt->bind_param("iisss", $mediaId, $userId, $username, $rating, $reviewText);
                if ($insertStmt->execute()) {
                    $reviewSuccess = true;
                } else {
                    $reviewError = "Failed to submit review: " . $insertStmt->error;
                }
                $insertStmt->close();
            } else {
                $reviewError = "Failed to prepare SQL statement: " . $conn->error;
            }
        }
    } else {
        $reviewError = "Failed to prepare SQL statement: " . $conn->error;
    }
}

// Handle watchlist submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_watchlist'])) {
    $mediaId = (int)$_POST['media_id'];

    // Check if the user has already added this media to the watchlist
    $checkSql = "SELECT * FROM watchlist WHERE media_id = ? AND uid = ?";
    $checkStmt = $conn->prepare($checkSql);
    if ($checkStmt) {
        $checkStmt->bind_param("ii", $mediaId, $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            $watchlistError = "This media is already in your watchlist.";
            $checkStmt->close();
        } else {
            $checkStmt->close();

            // Proceed to insert the watchlist entry
            $insertSql = "INSERT INTO watchlist (media_id, uid, user) VALUES (?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt) {
                $insertStmt->bind_param("iis", $mediaId, $userId, $username);
                if ($insertStmt->execute()) {
                    $watchlistSuccess = true;
                } else {
                    $watchlistError = "Failed to add to watchlist: " . $insertStmt->error;
                }
                $insertStmt->close();
            } else {
                $watchlistError = "Failed to prepare SQL statement: " . $conn->error;
            }
        }
    } else {
        $watchlistError = "Failed to prepare SQL statement: " . $conn->error;
    }
}

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = (int)$_GET['id'];
    $type = $_GET['type'];

    // Prepare SQL query based on type
    if ($type === 'anime') {
        $sql = "SELECT * FROM anime WHERE aid = ?";
    } elseif ($type === 'series') {
        $sql = "SELECT * FROM series WHERE sid = ?";
    } elseif ($type === 'movie') {
        $sql = "SELECT * FROM movie WHERE mid = ?";
    } elseif ($type === 'cartoon') {
        $sql = "SELECT * FROM cartoon WHERE cid = ?";
    } else {
        echo "Invalid type parameter.";
        exit();
    }

    // Execute SQL query
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "SQL statement preparation failed: " . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $db_results = $stmt->get_result();

    // Check if the film exists
    if ($db_results->num_rows > 0) {
        $film = $db_results->fetch_assoc();
        $poster_field = ($type === 'anime') ? 'anime_poster_link' : (($type === 'series') ? 'series_poster_link' : (($type === 'movie') ? 'movie_poster_link' : 'cartoon_poster_link'));
        $name_field = ($type === 'anime') ? 'anime_name' : (($type === 'series') ? 'series_name' : (($type === 'movie') ? 'movie_name' : 'cartoon_name'));
        $description_field = ($type === 'anime') ? 'anime_description' : (($type === 'series') ? 'series_description' : (($type === 'movie') ? 'movie_description' : 'cartoon_description'));
        $film['poster_link'] = convertImgurLink($film[$poster_field]);
    } else {
        echo "Film not found.";
        exit();
    }
} else {
    echo "Invalid parameters. Please check if 'id' and 'type' are set correctly in the URL.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($film[$name_field]); ?> - CineGang</title>
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/description.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="description-wrapper">
    <!-- Header Start -->
    <header class="site-header">
        <div class="wrapper site-header__wrapper">
            <div class="site-header__start">
                <a href="./homepage.php" class="brand"><img src="./media/images/CineGangLogo.png" alt="Logo"></a>
            </div>
            <div class="site-header__middle">
                <nav class="nav">
                    <button class="nav__toggle" aria-expanded="false" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <ul class="nav__wrapper">
                        <li class="nav__item"><a href="./homepage.php">Home</a></li>
                        <li class="nav__item"><a href="./search.php">Search</a></li>
                        <li class="nav__item"><a href="./profile.php">Profile</a></li>
                        <li class="nav__item"><a href="./account.php">Account</a></li>
                        <?php
                        if (isset($_SESSION['uid']) && $_SESSION['uid'] == 1) {
                            echo '<li class="nav__item"><a href="./admin.php">Admin</a></li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
            <div class="site-header__end">
                <a class="button" href="./handlers/logoutHandler.php">Logout</a>
            </div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Film Details Start -->
    <main class="profile-container">
        <div class="container py-5">
            <div class="description-content">
                <div class="film-poster">
                    <img src="<?php echo htmlspecialchars($film['poster_link']); ?>" alt="<?php echo htmlspecialchars($film[$name_field]); ?>">
                </div>
                <div class="film-details">
                    <h2>
                        <?php echo htmlspecialchars($film[$name_field]); ?>
                        <form action="" method="POST" id="watchlistForm" style="display: inline;">
                            <input type="hidden" name="media_id" value="<?php echo htmlspecialchars($id); ?>">
                            <button type="submit" name="add_to_watchlist" class="watchlist-button">Add to Watchlist</button>
                        </form>
                    </h2>
                    <p><?php echo htmlspecialchars($film[$description_field]); ?></p>
                    <div class="rating-system">
                        <span>Rate this:</span>
                        <i class="fas fa-star" data-value="1"></i>
                        <i class="fas fa-star" data-value="2"></i>
                        <i class="fas fa-star" data-value="3"></i>
                        <i class="fas fa-star" data-value="4"></i>
                        <i class="fas fa-star" data-value="5"></i>
                    </div>
                    <div class="review-section">
                        <h3>Write a Review</h3>
                        <?php if (isset($reviewSuccess) && $reviewSuccess) { ?>
                            <div class="alert alert-success" role="alert">
                                Review submitted successfully!
                            </div>
                        <?php } elseif (isset($reviewError)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($reviewError); ?>
                            </div>
                        <?php } ?>
                        <form action="" method="POST" id="reviewForm">
                            <input type="hidden" name="media_id" value="<?php echo htmlspecialchars($id); ?>">
                            <textarea name="review" id="review" rows="5" placeholder="Write your review here..." required></textarea>
                            <input type="hidden" name="star" id="rating" value="0"> <!-- Ensure name attribute is "star" -->
                            <button type="submit">Submit Review</button>
                        </form>
                        <div id="reviewMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Film Details End -->

    <!-- Footer Start -->
    <footer>
        <img src="./media/images/CineGangLogo.png" alt="CineGang Logo">
        <h3 style="color: #ffffff;"> &copy; Made by
            <a href="https://www.facebook.com/Jaysooony" target="_blank" style="color: #ffffff;">Jayson Yap</a>,
            <a href="https://www.facebook.com/carljustin808" target="_blank" style="color: #ffffff;">Carl Cueto</a>, and
            <a href="https://www.facebook.com/justine1251" target="_blank" style="color: #ffffff;">Justine Morales</a>
        </h3>
    </footer>
    <!-- Footer End -->
</div>
<!-- JS Scripts -->
<script>
    document.querySelectorAll('.rating-system .fa-star').forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-value');
            document.querySelector('#rating').value = rating;  // Set the value of the hidden input
            document.querySelectorAll('.rating-system .fa-star').forEach(s => {
                s.style.color = s.getAttribute('data-value') <= rating ? 'gold' : 'gray';
            });
        });
    });
</script>
</body>
</html>
