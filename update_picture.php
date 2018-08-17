<?php require_once("session.php"); ?>
<?php require_once("dbConnection.php"); ?>
<?php require_once("functions.php"); ?>
<?php require_once("validationFunctions.php"); ?>
<?php require_once("dbFunctions.php"); ?>

<?php

if (isset($_POST['submit'])) {
	
		$requiredFields = array("imageId","description");
		check_required_fields($requiredFields);
		
		$maxFieldLengths=array("description"=>100);
		check_max_field_lengths($maxFieldLengths);	
		
		if(!empty($errors)) {
			//Put errors in session before redirecting to another page
			$_SESSION["errors"]=$errors;	
			error_log("update_picture.php: ".$errors,0);	
		 }	else {
			$imageId = (int)$_POST["imageId"];		 	
			$description=prep_sql_string(prep_data($_POST["description"]));		
			$query="UPDATE images SET ";
			$query.="description='{$description}' ";
			$query.="WHERE image_id={$imageId} ";
			$query.="LIMIT 1";
			die("SQL query failed.");
			$result=mysqli_query($connection,$query);
			check_result($result,"update_picture.php:".mysqli_error($connection));
			redirect_to("show_user_images.php");
		}
}
