<?php
// Include the file containing database connection details
include '../components/connect.php';

// Check if the tutor_id cookie is set
if(isset($_COOKIE['tutor_id'])){
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    // Redirect to login page if tutor_id cookie is not set
    header('location:login.php');
}

// Check if the get_id parameter is set in the URL
if(isset($_GET['get_id'])){
    $get_id = $_GET['get_id'];
} else {
    // Redirect to playlist.php if get_id parameter is not set
    header('location:playlist.php');
}

// Check if the form is submitted
if(isset($_POST['submit'])){
    // Retrieve form data and sanitize inputs
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

    // Prepare and execute SQL query to update playlist details
    $update_playlist = $conn->prepare("UPDATE `playlist` SET title = ?, description = ?, status = ? WHERE id = ?");
    $update_playlist->execute([$title, $description, $status, $get_id]);

    // Retrieve old and new thumbnail image details
    $old_image = filter_var($_POST['old_image'], FILTER_SANITIZE_STRING);
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];

    // Check if a new thumbnail image is uploaded
    if(!empty($image)){
        // Sanitize and process uploaded image
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = uniqid().'.'.$ext; // Generating a unique identifier for image renaming
        $image_folder = '../uploaded_files/'.$rename;

        // Move uploaded image to the appropriate directory
        move_uploaded_file($image_tmp_name, $image_folder);

        // Update thumbnail in the database
        $update_image = $conn->prepare("UPDATE `playlist` SET thumb = ? WHERE id = ?");
        $update_image->execute([$rename, $get_id]);

        // Delete old thumbnail image if it exists and is different from the new one
        if($old_image != '' && $old_image != $rename){
            unlink('../uploaded_files/'.$old_image);
        }
    } 

    // Redirect user with a message indicating successful playlist update
    header('location:update_playlist.php?get_id='.$get_id.'&message=playlist updated!');
}

// Check if the delete button is clicked
if(isset($_POST['delete'])){
    // Retrieve playlist ID and sanitize input
    $delete_id = filter_var($_POST['playlist_id'], FILTER_SANITIZE_STRING);

    // Prepare and execute SQL queries to delete playlist and related data
    $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
    $delete_playlist_thumb->execute([$delete_id]);
    $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
    unlink('../uploaded_files/'.$fetch_thumb['thumb']);
    $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
    $delete_bookmark->execute([$delete_id]);
    $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
    $delete_playlist->execute([$delete_id]);

    // Redirect user to playlists.php after deletion
    header('location:playlists.php');
}

// Fetch playlist details based on the provided get_id parameter
$select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?");
$select_playlist->execute([$get_id]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Playlist</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="playlist-form">
   <h1 class="heading">Update Playlist</h1>
   <?php
      // Check if playlist exists
      if($select_playlist->rowCount() > 0){
         while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_playlist['id'];
            $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_videos->execute([$playlist_id]);
            $total_videos = $count_videos->rowCount();
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_playlist['thumb']; ?>">
      <p>Playlist Status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $fetch_playlist['status']; ?>" selected><?= $fetch_playlist['status']; ?></option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>
      <p>Playlist Title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="Enter Playlist Title" value="<?= $fetch_playlist['title']; ?>" class="box">
      <p>Playlist Description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="Write Description" maxlength="1000" cols="30" rows="10"><?= $fetch_playlist['description']; ?></textarea>
      <p>Playlist Thumbnail <span>*</span></p>
      <div class="thumb">
         <span><?= $total_videos; ?></span>
         <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
      </div>
      <input type="file" name="image" accept="image/*" class="box">
      <input type="submit" value="Update Playlist" name="submit" class="btn">
      <div class="flex-btn">
         <input type="submit" value="Delete" class="delete-btn" onclick="return confirm('Delete this playlist?');" name="delete">
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">View Playlist</a>
      </div>
   </form>
   <?php
      } 
   } else {
      // Display a message if no playlist is found
      echo '<p class="empty">No playlist added yet!</p>';
   }
   ?>
</section>

<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
</body>
</html>
