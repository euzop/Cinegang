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

$media_types = ["anime", "series", "movie", "cartoon"];
$media = [];

foreach ($media_types as $type) {
    $media[$type] = [];

    $sql = "SELECT * FROM $type";
    $db_results = $conn->query($sql);

    if (mysqli_num_rows($db_results) > 0) {
        while ($row = $db_results->fetch_assoc()) {
            // Convert Imgur link to direct image link
            $poster_field = $type . '_poster_link';
            if (isset($row[$poster_field])) {
                $row[$poster_field] = convertImgurLink($row[$poster_field]);
            }
            array_push($media[$type], $row);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineGang Watchlist</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="./media/images/CineGang-smolLogo.png"/>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/homepage.css">
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
        <!-- Media Types Start -->
        <div class="media-wrapper">
            <?php
            foreach ($media as $type => $content) {
                if (!empty($content)) {
                    $media_type = ucwords($type);
                    echo "<div class=\"media-heading\">
                            <h2>$media_type</h2>
                          </div>";

                    echo "<div class=\"media-items\">";
                    foreach ($content as $item) {
                        $id_field = ($type === 'movie') ? 'mid' : (($type === 'series') ? 'sid' : (($type === 'anime') ? 'aid' : 'cid'));
                        $name_field = ($type === 'movie') ? 'movie_name' : (($type === 'series') ? 'series_name' : (($type === 'anime') ? 'anime_name' : 'cartoon_name'));
                        $poster_field = ($type === 'movie') ? 'movie_poster_link' : (($type === 'series') ? 'series_poster_link' : (($type === 'anime') ? 'anime_poster_link' : 'cartoon_poster_link'));

                        $id = $item[$id_field];
                        $poster_link = $item[$poster_field];
                        $name = ucwords($item[$name_field]);

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
