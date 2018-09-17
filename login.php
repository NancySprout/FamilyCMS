<?php require_once("session.php");
require_once("dbConnection.php");
require_once("functions.php");
require_once("validationFunctions.php");

if (isset($_POST['submit'])) {	
	
	//Validations - these functions will add errors to the global $errors-array
	$requiredFields = array("email","password");
	check_required_fields($requiredFields);	
	validate_email($_POST["email"]);
	validate_password();	
	
	if(empty($errors)) {
		$loggedInUser=get_user_by_email($_POST["email"]);
		if($loggedInUser) {
			$typedPassword=$_POST["password"];
			if(!authenticate_user($typedPassword)) {
				echo "You entered invalid information";	
			} else {
				$_SESSION["logId"]=$loggedInUser["id"];
				echo "Success";
			}
		} else echo "You entered invalid information";
	} else echo output_errors($errors);
}
?>



