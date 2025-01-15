<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_GET['get_id'])){
   $playlist_id = $_GET['get_id'];
} else {
   $playlist_id = '';
   header('location:playlist.php');
}

if(isset($_POST['delete_notes'])){
   $delete_id = $_POST['notes_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   $verify_notes = $conn->prepare("SELECT * FROM `content2` WHERE id = ? LIMIT 1");
   $verify_notes->execute([$delete_id]);
   if($verify_notes->rowCount() > 0){
      $delete_notes_thumb = $conn->prepare("SELECT * FROM `content2` WHERE id = ? LIMIT 1");
      $delete_notes_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_notes_thumb->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files2/'.$fetch_thumb['thumb']);
      $delete_notes = $conn->prepare("SELECT * FROM `content2` WHERE id = ? LIMIT 1");
      $delete_notes->execute([$delete_id]);
      $fetch_notes = $delete_notes->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files2/'.$fetch_notes['notes']);
      $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
      $delete_likes->execute([$delete_id]);
      $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
      $delete_comments->execute([$delete_id]);
      $delete_content = $conn->prepare("DELETE FROM `content2` WHERE id = ?");
      $delete_content->execute([$delete_id]);
      $message[] = 'notes deleted!';
   } else {
      $message[] = 'notes already deleted!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlist Details</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contents">

   <h1 class="heading">Playlist Notes</h1>

   <div class="box-container">

   <?php
      $select_notes = $conn->prepare("SELECT * FROM `content2` WHERE tutor_id = ? AND playlist_id = ?");
      $select_notes->execute([$tutor_id, $playlist_id]);
      if($select_notes->rowCount() > 0){
         while($fetch_notes = $select_notes->fetch(PDO::FETCH_ASSOC)){ 
            $notes_id = $fetch_notes['id'];
   ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-dot-circle" style="<?php if($fetch_notes['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_notes['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_notes['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_notes['date']; ?></span></div>
         </div>
         <img src="../uploaded_files2/<?= $fetch_notes['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_notes['title']; ?></h3>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="notes_id" value="<?= $notes_id; ?>">
            <a href="update_content2.php?get_id=<?= $notes_id; ?>" class="option-btn">Update</a>
            <input type="submit" value="Delete" class="delete-btn" onclick="return confirm('Delete this notes?');" name="delete_notes">
         </form>
         <a href="view_content2.php?get_id=<?= $notes_id; ?>" class="btn">View Notes</a>
      </div>
   <?php
         }
      } else {
         echo '<p class="empty">No notes added yet! <a href="add_content2.php" class="btn" style="margin-top: 1.5rem;">Add Notes</a></p>';
      }
   ?>

   </div>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
