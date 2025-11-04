<?php
session_start();
include 'db.php';

// Ensure the session variable is set correctly
if (!isset($_SESSION['loggedincinegang']) || $_SESSION['loggedincinegang'] != true) {
    header("Location: ./login.php");
    exit();
}

$uid = $_SESSION['uid'];

// Function to convert Imgur page link to direct image link
function convertImgurLink($link) {
    if (preg_match('/^https:\/\/imgur\.com\/([a-zA-Z0-9]+)$/', $link, $matches)) {
        return 'https://i.imgur.com/' . $matches[1] . '.jpg';
    }
    return $link;
}

// Fetch user details
$sql = "SELECT profile_pic, nickname, name, bio, email, mobile FROM users WHERE uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$stmt->bind_result($profile_pic, $nickname, $name, $bio, $email, $mobile);
$stmt->fetch();
$stmt->close();

// Check if the profile_pic URL is an Imgur page link and convert it to a direct image link
$profile_pic = convertImgurLink($profile_pic);

// Set a default profile picture if none is provided
if (empty($profile_pic)) {
    $profile_pic = 'avatar.jpg'; // Replace with the actual path to your default profile picture
}

// Fetch media counts
$counts = [
    'movies' => 0,
    'series' => 0,
    'animes' => 0,
    'cartoons' => 0,
];

$sql = "SELECT media_id FROM review WHERE user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->bind_result($media_id);

while ($stmt->fetch()) {
    if ($media_id >= 10001 && $media_id < 20000) {
        $counts['movies']++;
    } elseif ($media_id >= 20001 && $media_id < 30000) {
        $counts['series']++;
    } elseif ($media_id >= 30001 && $media_id < 40000) {
        $counts['animes']++;
    } elseif ($media_id >= 40001 && $media_id < 50000) {
        $counts['cartoons']++;
    }
}

$stmt->close();

// Fetch recently watched media for the logged-in user
$recent_media = [];
$sql = "
    SELECT r.media_id, r.datetime, 
           COALESCE(m.movie_name, s.series_name, a.anime_name, c.cartoon_name) AS media_name,
           COALESCE(m.movie_poster_link, s.series_poster_link, a.anime_poster_link, c.cartoon_poster_link) AS poster_link,
           CASE 
               WHEN r.media_id >= 10001 AND r.media_id < 20000 THEN 'movie'
               WHEN r.media_id >= 20001 AND r.media_id < 30000 THEN 'series'
               WHEN r.media_id >= 30001 AND r.media_id < 40000 THEN 'anime'
               WHEN r.media_id >= 40001 AND r.media_id < 50000 THEN 'cartoon'
           END AS media_type
    FROM review r
    LEFT JOIN movie m ON r.media_id = m.mid
    LEFT JOIN series s ON r.media_id = s.sid
    LEFT JOIN anime a ON r.media_id = a.aid
    LEFT JOIN cartoon c ON r.media_id = c.cid
    WHERE r.user = ?
    ORDER BY r.datetime DESC
    LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $row['poster_link'] = convertImgurLink($row['poster_link']);
    $recent_media[] = $row;
}

$stmt->close();

// Handle form submission
$success = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $media_type = $_POST['form_option'];
    $name = $_POST['form_name'];
    $year = $_POST['form_year'];
    $genre = $_POST['form_genre'];
    $description = $_POST['form_description'];
    $poster_link = $_POST['form_poster_link'];
    $cover_link = $_POST['form_cover_link'];

    if ($media_type == 'Movie') {
        $sql = "INSERT INTO movie (movie_name, movie_year, movie_genre, movie_description, movie_poster_link, movie_cover_link) 
                VALUES (?, ?, ?, ?, ?, ?)";
    } elseif ($media_type == 'Series') {
        $sql = "INSERT INTO series (series_name, series_year, series_genre, series_description, series_poster_link, series_cover_link) 
                VALUES (?, ?, ?, ?, ?, ?)";
    } elseif ($media_type == 'Anime') {
        $sql = "INSERT INTO anime (anime_name, anime_year, anime_genre, anime_description, anime_poster_link, anime_cover_link) 
                VALUES (?, ?, ?, ?, ?, ?)";
    } elseif ($media_type == 'Cartoon') {
        $sql = "INSERT INTO cartoon (cartoon_name, cartoon_year, cartoon_genre, cartoon_description, cartoon_poster_link, cartoon_cover_link) 
                VALUES (?, ?, ?, ?, ?, ?)";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $year, $genre, $description, $poster_link, $cover_link);
    $success = $stmt->execute();
    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CineGang</title>
    <link rel="shortcut icon" type="image/png" href="media/images/CineGang-smolLogo.png"/>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/profile.css">
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<header class="site-header">
    <div class="wrapper site-header__wrapper">
        <div class="site-header__start">
            <a href="./homepage.php" class="brand"><img src="./media/images/CineGangLogo.png" alt="Logo"></a>
        </div>
        <div class="site-header__middle">
            <nav class="nav">
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
            <a class="button" href="./handlers/logoutHandler.php">Logout</a>
        </div>
    </div>
</header>

<main class="profile-container">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="profile" class="profile_pic">
                        <h5 class="my-0"><?php echo htmlspecialchars($nickname); ?></h5>
                        <p class="text-muted mb-1">@<?php echo htmlspecialchars($name); ?></p>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($bio); ?></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <p class="mb-0">Movies Watched</p>
                                <span><?php echo $counts['movies']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <p class="mb-0">Series Watched</p>
                                <span><?php echo $counts['series']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <p class="mb-0">Animes Watched</p>
                                <span><?php echo $counts['animes']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <p class="mb-0">Cartoons Watched</p>
                                <span><?php echo $counts['cartoons']; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <nav class="navbar navbar-expand-lg navbar-light rounded-navbar">
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="./p_watchlist.php">Watchlist</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./p_review.php">Reviews</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./p_movie.php">Movie</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./p_series.php">Series</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./p_anime.php">Anime</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./p_cartoon.php">Cartoon</a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <hr class="row mb-15" style="border-top: 1px solid #47282a;">
                <nav class="navbar navbar-expand-lg navbar-light rounded-navbar custom-navbar py-8">
                    <div class="collapse navbar-collapse" id="custom-navbarNav">
                        <ul class="navbar-nav">
                            <form method="POST" action="">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="form_option" class="custom-label">Visual Media</label>
                                        <select class="form-control" id="form_option" name="form_option">
                                            <option>Choose option</option>
                                            <option value="Movie">Movie</option>
                                            <option value="Series">Series</option>
                                            <option value="Anime">Anime</option>
                                            <option value="Cartoon">Cartoon</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="form_name" class="custom-label">Show Name</label>
                                        <input type="text" class="form-control custom-input" id="form_name" name="form_name" placeholder="Enter Name">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="form_year" class="custom-label">Year</label>
                                        <input type="text" class="form-control custom-input" id="form_year" name="form_year" placeholder="Enter Year">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="form_genre" class="custom-label">Genre</label>
                                        <input type="text" class="form-control custom-input" id="form_genre" name="form_genre" placeholder="Enter Genre">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="form_description" class="custom-label">Description</label>
                                        <input type="text" class="form-control custom-input" id="form_description" name="form_description" placeholder="Write Description...">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="form_poster_link" class="custom-label">Poster Link</label>
                                        <input type="text" class="form-control custom-input" id="form_poster_link" name="form_poster_link" placeholder="Enter Link">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="form_cover_link" class="custom-label">Cover Link</label>
                                        <input type="text" class="form-control custom-input" id="form_cover_link" name="form_cover_link" placeholder="Enter Link">
                                    </div>
                                </div>
                                <div class="form-group col-md-12 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary">Add to Database</button>
                                </div>
                            </form>
                        </ul>
                    </div>
                </nav>
                <hr class="row mb-0" style="border-top: 1px solid #47282a;">
                <h6>Recently Watched</h6>
                <hr class="col12" style="border-top: 1px solid #FFFFFF;">
                <div class="row">
                    <?php foreach ($recent_media as $media) : ?>
                        <div class="col-sm-3">
                            <div class="media-wrapper">
                                <a href="description.php?id=<?php echo htmlspecialchars($media['media_id']); ?>&type=<?php echo htmlspecialchars($media['media_type']); ?>">
                                    <img class='poster' src="<?php echo htmlspecialchars($media['poster_link']); ?>" alt="<?php echo htmlspecialchars($media['media_name']); ?>">
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>
    <img src="./media/images/CineGangLogo.png" alt="CineGang Logo">
    <h3 style="color: #ffffff;"> &copy; Made by
        <a href="https://www.facebook.com/Jaysooony" target="_blank" style="color: #ffffff;">Jayson Yap</a>,
        <a href="https://www.facebook.com/carljustin808" target="_blank" style="color: #ffffff;">Carl Cueto</a>, and
        <a href="https://www.facebook.com/justine1251" target="_blank" style="color: #ffffff;">Justine Morales</a>
    </h3>
</footer>

<?php if ($success): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (Notification.permission === "granted") {
                new Notification("Success", {
                    body: "The show has been successfully added to the database.",
                    icon: "path/to/success-icon.png" // You can set a custom icon here
                });
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        new Notification("Success", {
                            body: "The show has been successfully added to the database.",
                            icon: "path/to/success-icon.png" // You can set a custom icon here
                        });
                    }
                });
            }
        });
    </script>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const navToggle = document.querySelector(".nav__toggle");
        const navWrapper = document.querySelector(".nav__wrapper");

        navToggle.addEventListener("click", function() {
            if (navWrapper.classList.contains("active")) {
                this.setAttribute("aria-expanded", "false");
                this.setAttribute("aria-label", "menu");
                navWrapper.classList.remove("active");
            } else {
                navWrapper.classList.add("active");
                this.setAttribute("aria-label", "close menu");
                this.setAttribute("aria-expanded", "true");
            }
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
