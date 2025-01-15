<?php
// Include the database connection script
include '../project/components/connect.php';

// Check if user_id is set in cookies
if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
} else {
   // If user_id is not set, redirect to login page
   header('location:login.php');
   exit; // Terminate script execution after redirection
}

// Check if get_id is set in the URL
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
} else {
   // If get_id is not set, redirect to contents page
   header('location:contents.php');
   exit; // Terminate script execution after redirection
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
   <link rel="stylesheet" href="../project/css/style.css">

</head>
<body>

<?php include '../project/components/user_header.php'; ?>


<section class="view-content">

   <?php
      // Prepare and execute the SQL query to fetch the notes
      $select_content = $conn->prepare("SELECT * FROM `content2` WHERE id = ?");
      $select_content->execute([$get_id]);

      // Check if notes exist for the given user and id
      if($select_content->rowCount() > 0){
         // Loop through each fetched note
         while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
            // Display the PDF using <object> tag
   ?>
   <div class="container">
      <div class="notes-preview">
         <!-- Make sure the path to the PDF file is correct -->
         
         <object data="../project/uploaded_files2/<?= $fetch_content['notes']; ?>" type="application/pdf" width="100%" height="500">
            <p>Your browser does not support PDFs. You can <a href="../project/uploaded_files2/<?= $fetch_content['notes']; ?>">download the PDF</a> instead.</p>
         </object>
      </div>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">No notes added yet!</p>';
      }
   ?>

</section>

<?php include '../project/components/footer.php'; ?>
















<script src="../project/js/admin_script.js"></script>

</body>
</html>
