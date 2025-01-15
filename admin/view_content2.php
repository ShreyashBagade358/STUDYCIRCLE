<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
} else {
   $get_id = '';
   header('location:contents.php');
}

if(isset($_POST['delete_notes'])){
   $delete_id = $_POST['notes_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $delete_notes = $conn->prepare("SELECT notes FROM `content2` WHERE id = ? LIMIT 1");
   $delete_notes->execute([$delete_id]);
   $fetch_notes = $delete_notes->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files2/'.$fetch_notes['notes']);

   $delete_content = $conn->prepare("DELETE FROM `content2` WHERE id = ?");
   $delete_content->execute([$delete_id]);
   header('location:contents.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Notes</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>


<section class="view-content">

   <?php
      $select_content = $conn->prepare("SELECT * FROM `content2` WHERE id = ? AND tutor_id = ?");
      $select_content->execute([$get_id, $tutor_id]);
      if($select_content->rowCount() > 0){
         while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
            $notes_id = $fetch_content['id'];
   ?>
   <div class="container">
      <div class="notes-preview">
         <object data="../uploaded_files2/<?= $fetch_content['notes']; ?>" type="application/pdf" width="100%" height="500">
            <p>Your browser does not support PDFs. You can <a href="../uploaded_files2/<?= $fetch_content['notes']; ?>">download the PDF</a> instead.</p>
         </object>
      </div>
      <form action="" method="post">
         <div class="flex-btn">
            <input type="hidden" name="notes_id" value="<?= $notes_id; ?>">
            <input type="submit" value="Delete Notes" class="delete-btn" onclick="return confirm('Delete these notes?');" name="delete_notes">
         </div>
      </form>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">No notes added yet! <a href="add_notes.php" class="btn" style="margin-top: 1.5rem;">Add Notes</a></p>';
      }
   ?>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
