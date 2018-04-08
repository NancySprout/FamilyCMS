<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/dbConnection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validationFunctions.php"); ?>
<?php require_once("../includes/dbFunctions.php"); ?>

<?php

find_logged_in_user();
if(!$loggedInUser) 
	redirect_to("logout.php");

if (isset($_POST['submit'])) {	

	//Validations - these functions will add errors to the global $errors-array
	
	$requiredFields = array("name","password","email");
	check_required_fields($requiredFields);
	
	$maxFieldLengths=array("name"=>60, "password"=>60, "email"=>255);
	check_max_field_lengths($maxFieldLengths);
	
	$minFieldLengths=array("password"=>6);
	check_min_field_lengths($minFieldLengths);
	
	if(validate_email($_POST["email"])){
		is_unique($_POST["email"]);	
	}
	
	validate_password();
	
	if(!empty($errors)) {
		//Put errors in session before redirecting to another page
		$_SESSION["errors"]=$errors;
		if(!isset($_SESSION["retry"])) {
			$_SESSION["retry"]=1;	
		} else {
			$retry=$_SESSION["retry"];	
			++$retry;	
			if($retry>2) {
				$_SESSION["retry"]=null;
				redirect_to("main.php");		
			}	
			$_SESSION["retry"]=$retry;
		}
		
	 }	else {
		$email=prep_sql_string($_POST["email"]);
		$name=prep_sql_string(prep_data($_POST["name"]));	
		$description=prep_sql_string(prep_data($_POST["desc"]));
		
		//Create a safe user directory
		$safeUserDir=prep_sql_string(create_user_dir());
		
		//encrypt password
		$password=salt_and_encrypt($_POST["password"]);	
			
		if(isset($_POST["admin"])){		
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
			redirect_to("main.php");
		}  
	}
} 

?>
<?php include("../includes/header.php"); ?>
<main>
<section>
	<h2>Add Someone Special</h2>
	<form action="new_user.php" method="post">	
			<label for="name">Name</label>
				<input type="text" name="name" value="" id="name" required>
			<label for="email">E-mail</label>	
				<input type="email" name="email" id="email" required>	
			<textarea name="desc">Knock your socks off...</textarea>		
			<label for="password">Password</label>
				<input type="password" name="password" id="password" required>			
			<label for="admin">Admin</label>	
				<input type="checkbox" name="admin" id="admin" value="1">									
			<br>
			<input type="submit" name="submit" value="Add this Person" >	
	</form>	
</section>
	<a href="main.php">Get Out</a>
</main>
<?php include("../includes/footer.php"); ?>