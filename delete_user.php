<?php require_once("session.php");
require_once("dbConnection.php");
require_once("functions.php");
 
find_selected_user();
find_logged_in_user();

//Get out if things are fishy
if ((!$loggedInUser)||(!$selectedUser)) {
	echo "Exit"; //For Ajax
   //redirect_to("logout.php");
  } else {   

	//Now we can assume everything is fine 
	$loggedId= (int) $loggedInUser["id"];
	$userId=(int)$selectedUser["id"]; 
	
	//A logged in admin should not be able to delete itself! 
	//There always needs to be at least one admin in the system.
	if($loggedId==$userId){
		$errors["admin"]="Please do not delete your own account if you are an admin!";
		$_SESSION["errors"]=$errors;
		redirect_to("index.php");
	} else {
		delete_user_dir($userId);
		delete_all_images_in_db($userId);
		delete_user($userId);
		redirect_to("index.php");
	}
}
?>	