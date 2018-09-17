<?php require_once("session.php");
require_once("dbConnection.php");
require_once("functions.php");
require_once("validationFunctions.php");
require_once("dbFunctions.php");

find_logged_in_user();
if(!$loggedInUser) {
	echo "Exit"; //For Ajax
	//redirect_to("logout.php");
} else {
	if (isset($_POST['submit'])) {	
	
		//Validations - these functions will add errors to the global $errors-array
		
		$requiredFields = array("name","password","email");
		check_required_fields($requiredFields);
		
		$maxFieldLengths=array("name"=>20, "password"=>20, "email"=>20);
		check_max_field_lengths($maxFieldLengths);
		
		$minFieldLengths=array("password"=>8);
		check_min_field_lengths($minFieldLengths);
		
		if(validate_email($_POST["email"])){
			is_unique($_POST["email"]);	
		}
		
		validate_password();
		
		if(!empty($errors)) {
			//Put errors in session before redirecting to another page
			$_SESSION["errors"]=$errors;
			echo output_errors($errors);	
		 }	else {
			$email=prep_sql_string($_POST["email"]);
			$name=prep_sql_string(prep_data($_POST["name"]));	
			$description=prep_sql_string(prep_data($_POST["desc"]));
			
			//Create a safe user directory
			$safeUserDir=prep_sql_string(create_user_dir());
			
			//encrypt password
			$password=salt_and_encrypt($_POST["password"]);	
				
			if(!empty($_POST["admin"])){		
				$adminId=(int)($_POST["admin"]);
				$query="INSERT INTO users (";
				$query.="name, password, email, description, user_dir, admin";
				$query.=") VALUES (";
				$query.=" '{$name}', '{$password}', '{$email}', '{$description}', '{$safeUserDir}', $adminId";
				$query.=")";
			} else {
				$query="INSERT INTO users (";
				$query.="name, password, email, description, user_dir";
				$query.=") VALUES (";
				$query.=" '{$name}', '{$password}', '{$email}', '{$description}', '{$safeUserDir}'";
				$query.=")";			
			}
			
			$result=mysqli_query($connection,$query);
			
			check_result($result,"new_user.php:".mysqli_error($connection));
			
			if($result&&mysqli_affected_rows($connection)==1) {
				echo "Success";
			}  
		}
	} 
}
?>