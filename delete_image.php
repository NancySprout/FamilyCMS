<?php require_once("session.php");
require_once("dbConnection.php");
require_once("functions.php");
 
find_selected_user();
find_logged_in_user();

if (!$loggedInUser) {
   redirect_to("logout.php");
  } 
  if (!$selectedUser) {
	redirect_to("index.php");  
  }    
  $loggedId= (int)$loggedInUser["id"];
  $userId=(int)$selectedUser["id"];
  
  if(isset($_GET["imageId"])&&$_GET["imageId"]!=""){
  		$imageId=(int)$_GET["imageId"];
		delete_image_file($userId,$imageId);
		delete_image_in_db($imageId);
	}
	redirect_to("show_user_images.php");
?>	

