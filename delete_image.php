<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/dbConnection.php"); ?>
<?php require_once("../includes/functions.php"); ?>

<?php 
find_selected_user();
find_logged_in_user();
if (!$loggedInUser) {
   redirect_to("logout.php");
  } 
  if (!$selectedUser) {
	redirect_to("main.php");  
  }    
  $loggedId= (int) $loggedInUser["id"];
  $userId=(int)$selectedUser["id"];
  
  if(isset($_GET["imageId"])){
  		$imageId=(int) $_GET["imageId"];
  		echo "Deleting: ".$imageId." for user: ".$selectedUser["name"];
		delete_image_file($userId,$imageId);
		delete_image_in_db($imageId);
	}
	redirect_to("show_user_images.php");

?>	

