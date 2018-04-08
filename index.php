<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/dbConnection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validationFunctions.php"); ?>

<?php
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
			if (!authenticate_user($typedPassword)){
				redirect_to("logout.php");		
			} else {
				$_SESSION["logId"]=$loggedInUser["id"];
				redirect_to("main.php");
			}
		}
	}
	redirect_to("logout.php");
}
?>
<?php include("../includes/header.php"); ?>
<main>
	<form class="login" action="index.php" method="post">	
			<label>E-mail
				<input type="email" name="email" value="">
			</label>
			<label>Password
				<input type="password" name="password" value="">
			</label><br><br>
			<label>
				<input type="submit" name="submit" value="In Here" >
			</label>
	</form>
</main>
<?php include("../includes/footer.php"); ?>


