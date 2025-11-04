<?php
session_start();
// Only Allow Logged In Users
if (!(isset($_SESSION['loggedincinegang']) && $_SESSION['loggedincinegang'] == true)) {
    header("Location: ./login.php");
}

// Search
$resultFound = false;
$results = [];
if (isset($_GET['q']) && isset($_GET['o']) && isset($_GET['type'])) {
    include './db.php';

    $query = $_GET['q'];
    $option = $_GET['o'];
    $type = $_GET['type'];

    $query_array = explode(" ", $query);

    if ($query != "") {
        $table = "";
        switch ($type) {
            case "movie":
                $table = "movie";
                $id_field = "mid";
                $name_field = "movie_name";
                $poster_field = "movie_poster_link";
                $genre_field = "movie_genre";
                break;
            case "series":
                $table = "series";
                $id_field = "sid";
                $name_field = "series_name";
                $poster_field = "series_poster_link";
                $genre_field = "series_genre";
                break;
            case "anime":
                $table = "anime";
                $id_field = "aid";
                $name_field = "anime_name";
                $poster_field = "anime_poster_link";
                $genre_field = "anime_genre";
                break;
            case "cartoon":
                $table = "cartoon";
                $id_field = "cid";
                $name_field = "cartoon_name";
                $poster_field = "cartoon_poster_link";
                $genre_field = "cartoon_genre";
                break;
        }

        foreach ($query_array as $sub_query) {
            $column = ($option == "name") ? $name_field : $genre_field;
            $sql = "SELECT * FROM $table WHERE $column LIKE '%$sub_query%'";
            $db_results = $conn->query($sql);

            // Debugging output
            if (!$db_results) {
                echo "<p>Error executing query: " . $conn->error . "</p>";
                echo "<p>Query: $sql</p>";
            }

            if ($db_results && mysqli_num_rows($db_results) > 0) {
                while ($row = $db_results->fetch_assoc()) {
                    $results[] = $row;
                }
                $resultFound = true;
            }
        }
    }

    // Remove duplicates
    $results = array_unique($results, SORT_REGULAR);
}

function convertImgurLink($link) {
    if (preg_match('/^https:\/\/imgur\.com\/([a-zA-Z0-9]+)$/', $link, $matches)) {
        return 'https://i.imgur.com/' . $matches[1] . '.jpg';
    }
    return $link;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineGang</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="./media/images/CineGangLogo.png"/>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
</head>
<body>
<div class="main-content">
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
    <!-- Content Starts -->
    <div class="wrap">
        <form action="" method="get" class="search">
            <input type="search" class="search-box" name="q" placeholder="What are you looking for?" required>
            <select name="o" class="search-option">
                <option value="name" selected>Name</option>
                <option value="genre">Genre</option>
            </select>
            <select name="type" class="search-option">
                <option value="movie" selected>Movie</option>
                <option value="series">Series</option>
                <option value="anime">Anime</option>
                <option value="cartoon">Cartoon</option>
            </select>
            <button type="submit" class="search-btn">
                <i class="fa fa-search"></i>
            </button>
        </form>
    </div>
    <?php
    if (isset($_GET['q']) && isset($_GET['o']) && isset($_GET['type'])) {
        if ($resultFound) {
            echo "<div class=\"media-heading\">
                    <h2>" . ucfirst($type) . "s</h2>
                </div>";

            echo "<div class=\"media-items\">";
            foreach ($results as $item) {
                $id = $item[$id_field];
                $poster_link = convertImgurLink($item[$poster_field]);
                $name = ucwords($item[$name_field]);

                echo "<a href=\"./description.php?id=$id&type=$type\">
                        <div class=\"media-item\">
                            <img class=\"poster-image\" src=\"$poster_link\" alt=\"$name\">
                            <p class=\"media-name\">$name</p>
                        </div>";
                echo "</a>";

            }
            echo "</div>";
        } else {
            echo "<p>No results found</p>";
        }
    }
    ?>
    <!-- Content Ends -->
</div>
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
<!-- JS Scripts -->
<script src="./scripts/script.js"></script>
</body>
</html>

