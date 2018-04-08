<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/dbConnection.php"); ?>
<?php require_once("../includes/functions.php"); ?>

<?php 
find_selected_user();
find_logged_in_user();

//Get out if things are fishy
if (!$loggedInUser) {
   redirect_to("logout.php");
  } 
if (!$selectedUser) {
	redirect_to("main.php");  
}   

//Now we can assume everything is fine 
$loggedId= (int) $loggedInUser["id"];
$userId=(int)$selectedUser["id"]; 

//A logged in admin should not be able to delete itself! 
//There always needs to be at least one admin in the system.
if($loggedId==$userId){
	$_SESSION["message"]="Please do not delete your own admin account";
	redirect_to("main.php");
}

delete_user_dir($userId);
delete_all_images_in_db($userId);
delete_user($userId);
redirect_to("main.php");

?>	