<?php
session_start();
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

$media_types = ["movie"];
$media = [];

foreach ($media_types as $type) {
    $media[$type] = [];

    // Prepare SQL query to fetch watched movie for the logged-in user
    $sql = "SELECT a.mid, a.movie_name, a.movie_poster_link 
            FROM movie a 
            JOIN review r ON a.mid = r.media_id 
            WHERE r.uid = ? AND r.media_id >= 10001 AND r.media_id < 20000";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    } else {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            // Convert Imgur link to direct image link
            $poster_field = $type . '_poster_link';
            if (isset($row[$poster_field])) {
                $row[$poster_field] = convertImgurLink($row[$poster_field]);
            }
            array_push($media[$type], $row);
        }
        $stmt->close();
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
    <title>CineGang</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="./media/images/CineGang-smolLogo.png"/>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/p_movie.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
</head>
<body>
<div class="homepage-wrapper">
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
                <a class="button" href="./handlers/logoutHandler.php">Logout</a>
            </div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Content Wrapper Start -->
    <div class="content-wrapper">
        <div class="container py-5">
            <div class="media-wrapper">
                <?php
                foreach ($media as $type => $content) {
                    if ($type === 'movie' && !empty($content)) {
                        $media_type = ucwords($type);
                        echo "<div class=\"media-heading\">
                    <h2>Watched {$media_type}</h2>
                  </div>";

                        echo "<div class=\"media-items\">";
                        foreach ($content as $item) {
                            $id = $item['mid'];
                            $poster_link = $item['movie_poster_link'];
                            $name = ucwords($item['movie_name']);

                            echo "<a href=\"./description.php?id=$id&type=$type\">
                      <img class=\"poster-image\" src=\"$poster_link\" alt=\"$name\"> 
                      </a>";
                        }
                        echo "</div>";
                    }
                }
                ?>
            </div>

            <!-- Media Types End -->
        </div>
        <!-- Content Wrapper End -->

        <!-- Footer Start -->
        <footer>
            <img src="./media/images/CineGangLogo.png" alt="CineGang Logo">
            <h3 style="color: #ffffff;">&copy; Made by
                <a href="https://www.facebook.com/Jaysooony" target="_blank" style="color: #ffffff;">Jayson Yap</a>,
                <a href="https://www.facebook.com/carljustin808" target="_blank" style="color: #ffffff;">Carl Cueto</a>, and
                <a href="https://www.facebook.com/justine1251" target="_blank" style="color: #ffffff;">Justine Morales</a>
            </h3>
        </footer>
        <!-- Footer End -->
    </div>
    <!-- JS Scripts -->
    <script src="./scripts/script.js"></script>
</body>
</html>
