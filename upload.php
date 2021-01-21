<?php
session_start();
require_once('pdo.php');
if(! isset($_SESSION['user_id'])){
  die("Access Denied");
}
$target_dir = "uploads/";
$target_file = $target_dir . basename(time().$_SESSION['user_id'].$_FILES["fileToUpload"]["name"]);
##i,e file name will be = time() + $_SESSION['user_id]+original file name
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    echo "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars( basename( time().$_SESSION['user_id'].$_FILES["fileToUpload"]["name"])). " has been uploaded.";
    $smt = $pdo->prepare("SELECT * FROM  `profilePic` WHERE `user_id` = :u ");
    $smt->execute(array( 
        ':u' => $_SESSION['user_id'],
    ));
    $tempData = $smt -> fetch(PDO::FETCH_ASSOC);
    if($tempData == NULL){
      $smt = $pdo->prepare("INSERT INTO `profilePic` (`id`, `user_id`, `image_url`) VALUES (NULL, :u , :i) ");
                    $smt->execute(array( 
                        ':u' => $_SESSION['user_id'],
                        ':i' => basename( time().$_SESSION['user_id'].$_FILES["fileToUpload"]["name"])
                    ));
    }else{
      $smt = $pdo->prepare("UPDATE `profilePic` SET `image_url` = :i WHERE `user_id` = :u");
      $smt->execute(array( 
          ':i' => basename(time(). $_SESSION['user_id'].$_FILES["fileToUpload"]["name"]),
          ':u' => $_SESSION['user_id']
      ));
    }
    
    header("Location: ./index.php");
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}
?>