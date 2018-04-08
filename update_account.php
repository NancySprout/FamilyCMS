<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/dbConnection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validationFunctions.php"); ?>
<?php require_once("../includes/dbFunctions.php"); ?>

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

if (isset($_POST['submit'])) {
	
		$requiredFields = array("name","old_password","email");
		check_required_fields($requiredFields);
		
		$maxFieldLengths=array("name"=>60, "old_password"=>60, "email"=>255);
		check_max_field_lengths($maxFieldLengths);
		
		//Validate new password if set
		if(isset($_POST["new_password"])) {
			if(char_test($_POST["new_password"])){
				$minFieldLengths=array("new_password"=>6);
				check_min_field_lengths($minFieldLengths);
			} else {
				$errors["new_password"]="The new password you provided contains invalid characters.";			
			} 
		}		
		
		validate_email($_POST["email"]);
		
		//Authenticate old password
			if(char_test($_POST["old_password"])) {
				$oldPassword=$_POST["old_password"];
				authenticate_user($userId,$oldPassword);
			} else {
				$errors["old_password"]="The old password you provided contains invalid characters.";		
			}
		
		if(!empty($errors)) {
			//Put errors in session before redirecting to another page
			$_SESSION["errors"]=$errors;	
		 }	else {
		 	
		 	$newName=prep_sql_string(prep_data($_POST["name"]));	 	
			$safeEmail=prep_sql_string($newEmail);
			
			//encrypt password
			$newPassword=prep_sql_string(salt_and_encrypt($_POST["new_password"]));		
			
			$query="UPDATE users SET ";
			$query.="name='{$newName}', ";
			$query.="password='{$newPassword}', ";
			$query.="email='{$safeEmail}' ";
			$query.="WHERE id={$userId} ";
			$query.="LIMIT 1";
			
			//Returns a resource object collection of db rows
			$result=mysqli_query($connection,$query);
			check_result($result,"update_account.php:Could not update user information: ".$selectedUser["name"]);
			if(!$result&&mysqli_affected_rows($connection)==1) 
				error_log("update_account.php: ".mysqli_error($connection));
			redirect_to("show_user_images.php");
		}
}
?>

<?php include("../includes/header.php"); ?>
<main>
	<?php echo get_message(); ?>
	<?php $errors=get_errors(); ?>
	<?php echo output_errors($errors); ?>
<section>
	<h2> Update account Information</h2>
	<form action="update_account.php" method="post">	
			<label>Your name:
				<input type="text" name="name" value="<?php echo $selectedUser["name"];?>">	</label>
			<label>Old password:	
				<input type="password" name="old_password" required></label>
			<label>New password:	
				<input type="password" name="new_password" value=""></label>
			<label>e-mail Address:	
				<input type="email" name="email" value="<?php echo $selectedUser["email"];?>"></label>
			<br>
			<input type="submit" name="submit" value="Update" >	
	</form>	
</section>
</main>
<?php include("../includes/footer.php"); ?>