<?php
include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
    exit();
}

$message = array();

if (isset($_POST['submit'])) {

    $id = unique_id();
    $status = $_POST['status'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $playlist = $_POST['playlist'];

    $thumb = $_FILES['thumb']['name'];
    $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
    $rename_thumb = unique_id() . '.' . $thumb_ext;
    $thumb_size = $_FILES['thumb']['size'];
    $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
    $thumb_folder = '../uploaded_files2/' . $rename_thumb;

    $notes_title = $_FILES['notes']['name'];
    $notes_ext = pathinfo($notes_title, PATHINFO_EXTENSION);
    $rename_notes = unique_id() . '.' . $notes_ext;
    $notes_tmp_name = $_FILES['notes']['tmp_name'];
    $notes_folder = '../uploaded_files2/' . $rename_notes;

    if ($thumb_size > 2000000) {
        $message[] = 'Image size is too large!';
    } else {
        $add_content2 = $conn->prepare("INSERT INTO `content2` (id, tutor_id, playlist_id, title, description, notes, thumb, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $add_content2->execute([$id, $tutor_id, $playlist, $title, $description, $rename_notes, $rename_thumb, $status]);
        move_uploaded_file($thumb_tmp_name, $thumb_folder);
        move_uploaded_file($notes_tmp_name, $notes_folder);
        $message[] = 'New content uploaded!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="video-form">

        <h1 class="heading">Upload Content</h1>
        <?php if (!empty($message) && is_array($message)) : ?>
            <div class="message">
                <?php foreach ($message as $msg) : ?>
                    <p><?= $msg ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <p><span>*</span> Status</p>
            <select name="status" class="box" required>
                <option value="" selected disabled>-- Select Status</option>
                <option value="active">Active</option>
                <option value="deactive">Deactive</option>
            </select>
            <p><span>*</span> Title</p>
            <input type="text" name="title" maxlength="100" required placeholder="Enter Title" class="box">
            <p><span>*</span> Description</p>
            <textarea name="description" class="box" required placeholder="Write Description" maxlength="1000" cols="30" rows="10"></textarea>
            <p><span>*</span> Playlist</p>
            <select name="playlist" class="box" required>
                <option value="" disabled selected>-- Select Playlist</option>
                <?php
                $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
                $select_playlists->execute([$tutor_id]);
                if ($select_playlists->rowCount() > 0) {
                    while ($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)) {
                ?>
                        <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
                <?php
                    }
                } else {
                    echo '<option value="" disabled>No playlist created yet!</option>';
                }
                ?>
            </select>
            <p><span>*</span> Select Thumbnail</p>
            <input type="file" name="thumb" accept="image/*" required class="box">
            <p><span>*</span> Select Notes</p>
            <input type="file" name="notes" accept=".pdf" required class="box">
            <input type="submit" value="Upload Content" name="submit" class="btn">
        </form>

    </section>
    <?php include '../components/footer.php'; ?>

    <script src="../js/admin_script.js"></script>

</body>

</html>
