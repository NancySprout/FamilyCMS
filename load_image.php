<?php require_once("session.php");
require_once("dbConnection.php");
require_once("functions.php");
require_once("validationFunctions.php");
require_once("systemConstants.php");

find_selected_user();
find_logged_in_user();

if (!$loggedInUser) {
  	redirect_to("logout.php");
  	if(!$selectedUser) redirect_to("index.php");
} else {  
	$loggedId= (int) $loggedInUser["id"];
	$userId=(int)$selectedUser["id"];
	
	if (isset($_POST['submit'])) {
		
		$maxFieldLengths=array("desc"=>100);
		check_max_field_lengths($maxFieldLengths);	
		$imageName=validate_image_name();
		
		if(!empty($errors)) {
			//Put errors in session before redirecting to another page
			$_SESSION["errors"]=$errors;	
			//echo output_errors($errors);				
		 }	else {		
			$description=prep_sql_string(prep_data($_POST["desc"]));
					
			//Get image properties/parameters in an array
			$imagePropertyArray=check_image_parameters();
			if(isset($imagePropertyArray)) {
	
				//Prepare property data for database storage
				$width=(int) $imagePropertyArray[0];
				$height=(int) $imagePropertyArray[1];
				$imageType=(int) $imagePropertyArray[2];
							
				//Get actual file extension
				$fileExt=check_image_extension();			
				
				$safeFileName=bin2hex(random_bytes(4)).".".$fileExt;
		
				//Build SQL query
				$query="INSERT INTO images (";
				$query.=" user_id, width, height, description, type, safe_file_name ";
				$query.=") VALUES (";
				$query.=" {$userId},{$width}, {$height}, '{$description}', {$imageType}, '{$safeFileName}' ";
				$query.=")";
				
				$result=mysqli_query($connection,$query);
				$imageId=mysqli_insert_id($connection);
				
				check_result($result,"load_image.php:Could not load image: ".mysqli_error($connection));
									
				//Create destination path +filename for file system storage	
				$destinationFile=FS_PATH.$selectedUser["user_dir"]."/".$safeFileName;				
				$myFile = fopen($destinationFile, "w+");
				
				//Move the file from temp directory to data storage
				if(move_uploaded_file($_FILES["image_file"]["tmp_name"],$destinationFile)) {
					chmod($destinationFile,0755); // owner=r,w,x group=r,x other=r,x
					fclose($myFile);
				} else {
					fclose($myFile);
					error_log("load_image.php: ".$destinationFile." could not be loaded.");
					$errors["file_error"]="Your image file could not be stored on the web server.";
					//echo output_errors($errors);	
				}
				
				//Update user with profile image id if it exists
				if(isset($_POST["profile"])&&$_POST["profile"]!="") {		
					update_profile_image($userId, $imageId);
				}
				echo output_errors($errors);				
			} else {
				$errors["file_error"]="Could not determine file properties.";		
			}
		}
		redirect_to("show_user_images.php");	
	}
}
?>
