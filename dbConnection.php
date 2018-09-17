<?php require_once("systemConstants.php");
require_once("functions.php");
global $connection;
	
	//Create a batabase connection
	$connection = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
	
	//Check if the connection was successful
	//mysqli_connect_errno returns 0 if no errors
	if(mysqli_connect_errno()){
		//Quit everything and get out of there
		echo "Database connection failed: ".DB_SERVER.DB_USER.DB_PASS.DB_NAME.mysqli_connect_error();
		error_log("Database connection failed: ".mysqli_connect_errno().": ".mysqli_connect_error(),0);
	}
?>