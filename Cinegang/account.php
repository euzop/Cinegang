<?php
session_start();
include 'db.php';

if (!(isset($_SESSION['loggedincinegang']) && $_SESSION['loggedincinegang'] == true)) {
    header("Location: ./login.php");
    exit();
}

$uid = $_SESSION['uid'];

$sql = "SELECT * FROM users WHERE uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['account_form'])) {
    $name = $_POST['name'];
    $nickname = $_POST['nickname'];
    $profile_pic = $_POST['profile_pic'];
    $bio = $_POST['bio'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $current_password = $_POST['password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!preg_match('/^09\d{9}$/', $mobile)) {
        $errors['mobile'] = "Phone number must start with 09 and be exactly 11 digits.";
    }

    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $errors['new_password'] = "New passwords do not match.";
        } else {
            $sql = "UPDATE users SET name=?, nickname=?, profile_pic=?, bio=?, mobile=?, email=?, password=? WHERE uid=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $name, $nickname, $profile_pic, $bio, $mobile, $email, $new_password, $uid);
            if (!$stmt->execute()) {
                $errors[] = "Error updating user information: " . $conn->error;
            } else {
                header("Location: ./profile.php");
                exit();
            }
            $stmt->close();
        }
    } else {
        $sql = "UPDATE users SET name=?, nickname=?, profile_pic=?, bio=?, mobile=?, email=? WHERE uid=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $nickname, $profile_pic, $bio, $mobile, $email, $uid);
        if (!$stmt->execute()) {
            $errors[] = "Error updating user information: " . $conn->error;
        } else {
            header("Location: ./profile.php");
            exit();
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
    <link rel="shortcut icon" type="image/png" href="./media/images/CineGang-smolLogo.png"/>
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous" />
</head>
<body>

<!-- Header Start -->
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
<!-- Header End -->
<!-- Content Starts -->
<div class="account-box">
    <form action="account.php" method="POST" class="account-box-form">
        <h3 class="account-box-title">Profile Update</h3>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($user['name']) ?>" required />
        </div>
        <div class="form-group">
            <label for="nickname">Nickname</label>
            <input type="text" name="nickname" id="nickname" placeholder="Enter your nickname" value="<?php echo htmlspecialchars($user['nickname']) ?>" />
        </div>
        <div class="form-group">
            <label for="profile_pic">Profile Picture (Imgur URL)</label>
            <input type="url" name="profile_pic" id="profile_pic" placeholder="Enter Imgur link" value="<?php echo htmlspecialchars($user['profile_pic']) ?>" />
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea name="bio" id="bio" placeholder="Write a short bio"><?php echo htmlspecialchars($user['bio']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="mobile">Mobile Number</label>
            <input type="text" name="mobile" id="mobile" placeholder="Mobile number" value="<?php echo htmlspecialchars($user['mobile']) ?>" required />
            <?php if (!empty($errors['mobile'])): ?>
                <span class="error-message"><?php echo $errors['mobile']; ?></span>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Email address" value="<?php echo htmlspecialchars($user['email']) ?>" required />
        </div>
        <div class="form-group">
            <label for="password">Current Password</label>
            <input type="password" name="password" id="password" autocomplete="current-password" placeholder="Enter current password" />
            <?php if (!empty($errors['password'])): ?>
                <span class="error-message"><?php echo $errors['password']; ?></span>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" autocomplete="new-password" placeholder="Enter new password" />
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password" placeholder="Confirm new password" />
            <?php if (!empty($errors['new_password'])): ?>
                <span class="error-message"><?php echo $errors['new_password']; ?></span>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary" name="account_form" value="Update">
            Update
        </button>
    </form>
</div>
<footer class="footer-container">
    <img src="./media/images/CineGangLogo.png" alt="CineGang Logo">
    <h3>&copy; Made by
        <a href="https://www.facebook.com/Jaysooony" target="_blank">Jayson Yap</a>,
        <a href="https://www.facebook.com/carljustin808" target="_blank">Carl Cueto</a>, and
        <a href="https://www.facebook.com/justine1251" target="_blank">Justine Morales</a>
    </h3>
</footer>
<script src="./scripts/script.js"></script>
</body>
</html>
