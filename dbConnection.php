<?php require_once("../includes/systemConstants.php"); ?>
<?php require_once("../includes/functions.php"); ?>

<?php	
	//Using constants for these are better since they never change

	global $connection;
	
	//Create a batabase connection
	$connection = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
	
	//Check if the connection was successful
	//mysqli_connect_errno returns 0 if no errors
	if(mysqli_connect_errno()){
		//Quit everything and get out of there
		log_custom_error("Database connection failed: ".mysqli_connect_errno().": ".mysqli_connect_error());
	}
		
?>