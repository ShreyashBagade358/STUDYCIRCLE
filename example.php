<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$select_likes->execute([$user_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

$select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <!-- quick select section starts  -->

    <section class="quick-select">

        <h1 class="heading">quick options</h1>

        <div class="box-container">

            <?php
            if ($user_id != '') {
            ?>
                <div class="box">
                    <h3 class="title">likes and comments</h3>
                    <p>total likes : <span><?= $total_likes; ?></span></p>
                    <a href="likes.php" class="inline-btn">view likes</a>
                    <p>total comments : <span><?= $total_comments; ?></span></p>
                    <a href="comments.php" class="inline-btn">view comments</a>
                    <p>saved playlist : <span><?= $total_bookmarked; ?></span></p>
                    <a href="bookmark.php" class="inline-btn">view bookmark</a>
                </div>
            <?php
            } else {
            ?>
                <div class="box" style="text-align: center;">
                    <h3 class="title">please login or register</h3>
                    <div class="flex-btn" style="padding-top: .5rem;">
                        <a href="login.php" class="option-btn">login</a>
                        <a href="register.php" class="option-btn">register</a>
                    </div>
                </div>
            <?php
            }
            ?>

            <div class="box tutor">
                <h3 class="title">Become a Tutor</h3>
                <p>Become Tutorer </p>
                <a href="admin/register.php" class="inline-btn">Get Started</a>
            </div>

        </div>

    </section>

    <!-- quick select section ends -->

    <!-- videos section starts  -->

    <section class="videos">

        <h1 class="heading">More Videos</h1>

        <div class="box-container">

            <?php
            $select_videos = $conn->prepare("SELECT * FROM `videos` WHERE status = ? ORDER BY date DESC LIMIT 6");
            $select_videos->execute(['active']);
            if ($select_videos->rowCount() > 0) {
                while ($fetch_video = $select_videos->fetch(PDO::FETCH_ASSOC)) {
                    $video_id = $fetch_video['id'];

                    $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
                    $select_tutor->execute([$fetch_video['tutor_id']]);
                    $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
            ?>
                    <div class="box">
                        <div class="tutor">
                            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
                            <div>
                                <h3><?= $fetch_tutor['name']; ?></h3>
                                <span><?= $fetch_video['date']; ?></span>
                            </div>
                        </div>
                        <video controls width="100%">
                            <source src="uploaded_files/<?= $fetch_video['video_file']; ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <h3 class="title"><?= $fetch_video['title']; ?></h3>
                        <a href="video_details.php?get_id=<?= $video_id; ?>" class="inline-btn">view video</a>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">no videos added yet!</p>';
            }
            ?>

        </div>

        <div class="more-btn">
            <a href="videos.php" class="inline-option-btn">view more</a>
        </div>

    </section>

    <!-- footer section starts  -->
    <?php include 'components/footer.php'; ?>
    <!-- footer section ends -->

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>