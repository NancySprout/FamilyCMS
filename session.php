<?php
	session_start();

	function get_message() {
			if(isset($_SESSION["message"])) {
			$output= "<div class=\"message\">";
			$output.= htmlentities($_SESSION["message"]);
			$output.= "</div>";
			//Clear the message after it has been displayed
			$_SESSION["message"]=null;
			//If there were no message this variable will be "empty" 
			return $output;
		}		
	}	
			
	function get_errors() { 
		if(isset($_SESSION["errors"])) {
			$errors=$_SESSION["errors"];
			$_SESSION["errors"]=null;
			//If there were no errors this variable would be "empty"
			return $errors;
		}		
	}
?>