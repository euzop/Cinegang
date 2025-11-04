<?php
session_start();
if (!isset($_SESSION['loggedincinegang']) || $_SESSION['loggedincinegang'] != true) {
    header("Location: ./login.php");
    exit();
}

include 'db.php';

// Function to convert Imgur page link to direct image link
function convertImgurLink($link) {
    if (preg_match('/^https:\/\/imgur\.com\/([a-zA-Z0-9]+)$/', $link, $matches)) {
        return 'https://i.imgur.com/' . $matches[1] . '.jpg';
    }
    return $link;
}

// Ensure the session variable is set correctly
if (isset($_SESSION['uid'])) {
    $user_id = $_SESSION['uid'];
} else {
    // Handle the error if the session variable is not set
    echo "User not logged in correctly.";
    exit();
}

// Prepare SQL query to fetch reviews
$sql = "SELECT media_id, review_desc, star FROM review WHERE uid = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
} else {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($media_id, $review_desc, $star);

    $reviews = [];
    while ($stmt->fetch()) {
        $reviews[] = ['media_id' => $media_id, 'review_desc' => $review_desc, 'star' => $star];
    }
    $stmt->close();
}

$media_details = [];
foreach ($reviews as $r) {
    $media_id = $r['media_id'];
    if ($media_id >= 10001 && $media_id < 20000) {
        $sql = "SELECT mid as id, movie_name as name, movie_year as year, movie_genre as genre, movie_poster_link as poster, movie_cover_link as cover, movie_description as description FROM movie WHERE mid = ?";
    } elseif ($media_id >= 20001 && $media_id < 30000) {
        $sql = "SELECT sid as id, series_name as name, series_year as year, series_genre as genre, series_poster_link as poster, series_cover_link as cover, series_description as description FROM series WHERE sid = ?";
    } elseif ($media_id >= 30001 && $media_id < 40000) {
        $sql = "SELECT aid as id, anime_name as name, anime_year as year, anime_genre as genre, anime_poster_link as poster, anime_cover_link as cover, anime_description as description FROM anime WHERE aid = ?";
    } elseif ($media_id >= 40001 && $media_id < 50000) {
        $sql = "SELECT cid as id, cartoon_name as name, cartoon_year as year, cartoon_genre as genre, cartoon_poster_link as poster, cartoon_cover_link as cover, cartoon_description as description FROM cartoon WHERE cid = ?";
    } else {
        continue;
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        continue;
    }
    $stmt->bind_param("i", $media_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $media_details[$media_id] = $result->fetch_assoc();
    $stmt->close();
}

// Convert Imgur links to direct image links
foreach ($media_details as $key => $media) {
    if (isset($media['poster'])) {
        $media_details[$key]['poster'] = convertImgurLink($media['poster']);
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Reviews - CineGang</title>
    <link rel="shortcut icon" type="image/png" href="media/images/CineGang-smolLogo.png"/>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/p_review.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
                        if (isset($_SESSION['uid'])) {
                            if ($_SESSION['uid'] == 1) {
                                echo '<li class="nav__item"><a href="./admin.php">Admin</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </nav>
            </div>
            <div class="site-header__end">
                <a class="button" href="./logout.php">Logout</a>
            </div>
        </div>
    </header>
    <!-- Header End -->

    <main class="review-container">
        <div class="container py-5">
            <?php foreach ($reviews as $r): ?>
                <?php
                $media_id = $r['media_id'];
                if (!isset($media_details[$media_id])) {
                    continue;
                }
                $media = $media_details[$media_id];
                ?>
                <div class="description-content">
                    <div class="film-poster">
                        <img src="<?php echo htmlspecialchars($media['poster']); ?>" alt="<?php echo htmlspecialchars($media['name']); ?>">
                    </div>
                    <div class="film-details">
                        <h2>
                            <?php echo htmlspecialchars($media['name']); ?>
                        </h2>
                        <p><?php echo htmlspecialchars($media['description']); ?></p>
                        <div class="rating-system">
                            <span>Your Rating: <?php echo htmlspecialchars($r['star']); ?> stars</span>
                            <p><?php echo htmlspecialchars($r['review_desc']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
<footer>
    <img src="media/images/CineGangLogo.png" alt="CineGang Logo">
    <h3 style="color: #ffffff;">&copy; Made by
        <a href="https://www.facebook.com/Jaysooony" target="_blank" style="color: #ffffff;">Jayson Yap</a>,
        <a href="https://www.facebook.com/carljustin808" target="_blank" style="color: #ffffff;">Carl Cueto</a>, and
        <a href="https://www.facebook.com/justine1251" target="_blank" style="color: #ffffff;">Justine Morales</a>
    </h3>
</footer>

<!-- Include Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
