<?php require_once("session.php");
require_once("dbConnection.php");
require_once("functions.php");
require_once("validationFunctions.php");
require_once("dbFunctions.php");

find_selected_user();
find_logged_in_user();

if ((!$loggedInUser)||(!$selectedUser)) {
	echo "Exit"; //For Ajax
  	//redirect_to("logout.php");
} else {  

	$loggedId= (int) $loggedInUser["id"];
	$userId=(int)$selectedUser["id"];
	//If this is not an admin or the owner of this account get out
	if($loggedInUser["admin"]||$userId==$loggedId) {
		if (isset($_POST['submit'])) {
			
			$maxFieldLengths=array("name"=>20, "password"=>20, "email"=>20, "description"=>400);
			check_max_field_lengths($maxFieldLengths);
		
			//Use empty() only with arrays		
			if(!empty($errors)) {	
				//Put errors in session before redirecting to another page
				$_SESSION["errors"]=$errors;	
				echo output_errors($errors); //For Ajax
			 }	else {
			 	
				//Update name if it is set
				if(isset($_POST["name"])&&$_POST["name"]!="") {
					$newName=prep_sql_string(prep_data($_POST["name"]));
					$query="UPDATE users SET ";
					$query.="name='{$newName}' ";
					$query.="WHERE id={$userId} ";
					$query.="LIMIT 1";	
					$result=mysqli_query($connection,$query);
					check_result($result,"update_account.php: Could not update user name for: ".$selectedUser["name"].$query); 
				}
			
				//Update new password if it is set
				if(isset($_POST["password"])&&$_POST["password"]!=""){
					$minFieldLengths=array("password"=>8);
					if(char_test($_POST["password"])&&check_min_field_lengths($minFieldLengths)){
						//encrypt password
						$newPassword=prep_sql_string(salt_and_encrypt($_POST["password"]));
						$query="UPDATE users SET ";
						$query.="password='{$newPassword}' ";
						$query.="WHERE id={$userId} ";
						$query.="LIMIT 1";	
						$result=mysqli_query($connection,$query);
						check_result($result,"update_account.php: Could not update password for: ".$selectedUser["name"]);	
					} else {
						$errors["password"]="The new password contains invalid characters. Please try again.";			
					} 
				}		
			
				//Validate email if it is set
				if(isset($_POST["email"])&&$_POST["email"]!=""){
			 		if(validate_email($_POST["email"])&&is_unique($_POST["email"])){
						$safeEmail=prep_sql_string($_POST["email"]);
						$query="UPDATE users SET ";
						$query.="email='{$safeEmail}' ";
						$query.="WHERE id={$userId} ";
						$query.="LIMIT 1";
						$result=mysqli_query($connection,$query);
						check_result($result,"update_account.php: Could not update email for: ".$selectedUser["name"].$query);
			 		}
				}
			
				//Update description if it is set
				if(isset($_POST["description"])&&$_POST["description"]!=""){
					$description=prep_sql_string(prep_data($_POST["description"])); 
					$query="UPDATE users SET ";
					$query.="description='{$description}' ";
					$query.="WHERE id={$userId} ";
					$query.="LIMIT 1";	
					$result=mysqli_query($connection,$query);
					check_result($result,"update_account.php: Could not update description for: ".$selectedUser["name"]);
				}
				// For Ajax
				if(!$errors){
					echo "Success";
				} else {
					echo output_errors($errors); 
				}
				//redirect_to("show_user_images.php");
			}
		} else {
			echo "Exit"; //For Ajax
			//redirect_to("logout.php");	
		}
	}
}
?>