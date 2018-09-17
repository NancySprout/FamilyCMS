<?php 
require_once("session.php"); 
require_once("dbConnection.php"); 
require_once("functions.php"); 

function prep_sql_string($string){
	global $connection;
	//Do string prep to avoid sql injection
	return mysqli_real_escape_string($connection, $string);	
}

function check_result($result,$message){ 
	if(!$result){
		error_log($message,0);	
		die("SQL query failed:".$message);
	}
}	

//Check if email is already in use
function is_unique($email) {
		global $connection;
		global $errors;
		
		$safeEmail=prep_sql_string($email);
		
		$query="SELECT * FROM users ";
		$query.="WHERE email='{$safeEmail}' LIMIT 1";

		//Returns a resource object collection of db rows
		$result=mysqli_query($connection,$query);
		if($row =mysqli_fetch_assoc($result)) {
			if($row["email"]==$safeEmail){
				$errors["email"]="Email address ".$row["email"]." is already in use.";
				mysqli_free_result($result);	
				return false;
			} else {
				return true;		
			}
		} else { 
			return true;
		}
}		

//Retrieve all the image ids for a specific user
function get_image_ids($userId) {
	global $connection;
	
	$query="SELECT  image_id";
	$query.="FROM images ";
	$query.="WHERE user_id={$userId}";

	$imageIdSet=mysqli_query($connection,$query);
	//Test query
	check_result($imageIdSet,"get_image_ids: Could not get image_id for user_id=".$userId);
	if($imageIdSet)	{
		return $imageIdSet;
	} else {
		return null;
	}
}

//Retrieve the user directory for a specific user
function get_user_dir($userId){

	global $connection;
	
	$query="SELECT user_dir ";
	$query.="FROM users ";
	$query.="WHERE id={$userId}";
	$message=$query;
	
	$userDirSet=mysqli_query($connection,$query);
	//Test query
	check_result($userDirSet,"get_user_dir: Could not get user_dir for id=".$userId);
	
	if($userDir=mysqli_fetch_assoc($userDirSet)) {
		return $userDir["user_dir"];	
		} else {
			return null;
		}
}

//Retrieve the safe file name for a specific image
function get_safe_file_name($imageId){
	global $connection;
	
	$query="SELECT safe_file_name ";
	$query.="FROM images ";
	$query.="WHERE image_id={$imageId} LIMIT 1";
	
	$fileNameSet=mysqli_query($connection,$query);
	//Test query
	check_result($fileNameSet,"get_safe_file_name: Could not get safe_file_name for image_id=".$imageId);	
					
	if($fileName=mysqli_fetch_assoc($fileNameSet)) {	
		return $fileName["safe_file_name"];	
	} else {
	 	return null;
	}
}

//Retrieve the name for a specific user
function get_name($userId){
	global $connection;
	
	$query="SELECT name ";
	$query.="FROM users ";
	$query.="WHERE id={$userId} LIMIT 1";
	
	$userIdSet=mysqli_query($connection,$query);
	check_result($userIdSet,"get_name: Could not get name for id=".$userId);	
					
	if($row=mysqli_fetch_assoc($userIdSet)) {	
		return $row["name"];	
	} else {
	 	return null;
	}	

}

//Retrieve the email address for a specific user
function get_email($userId){
	global $connection;
	
	$query="SELECT email ";
	$query.="FROM users ";
	$query.="WHERE id={$userId} LIMIT 1";
	
	$emailSet=mysqli_query($connection,$query);
	check_result($emailSet,"get_email: Could not get email for id=".$userId);	
					
	if($row=mysqli_fetch_assoc($emailSet)) {	
		return $row["email"];	
	} else {
	 	return null;
	}	

}

//Retrieve the profile image id for a specific user
function get_profile_image_id($userId){
	global $connection;
	
	$query="SELECT profile_image_id ";
	$query.="FROM users ";
	$query.="WHERE id={$userId} LIMIT 1";
	
	$result=mysqli_query($connection,$query);
	//Test query
	check_result($result,"get_profile_image_id: Could not get profile_image_id for id=".$userId);	
					
	if($row=mysqli_fetch_assoc($result)) {	
		return $row["profile_image_id"];	
	} else {
	 	return null;
	}	
}

function unset_profile_image($userId){
	global $connection;
	
	$query="UPDATE users SET ";
	$query.="profile_image_id=NULL ";
	$query.="WHERE id={$userId} ";
	
	$result=mysqli_query($connection,$query);
			
	if($result) {
		return true;
	}  else {	
		return false;
	}
}

//Update user with new profile image id
function update_profile_image($userId, $imageId){
	global $connection;
	
	//User may have only one profile image, remove existing ones first
	unset_profile_image($userId);
	
	$query="UPDATE users SET ";
	$query.="profile_image_id={$imageId} ";
	$query.="WHERE id={$userId} ";
	$query.="LIMIT 1";
	
	$result=mysqli_query($connection,$query);
	check_result($result,"update_profile_image: Could not update profile image for user_id=".$userId.": ".mysqli_error($connection));	
					
	if($result&&mysqli_affected_rows($connection)==1) {
		return true;
	}  else {	
		return false;
	}
}

//Retrieve all the user records from db
function get_all_users() {
		global $connection;
		
		$query="SELECT * ";
		$query.="FROM users ";
		$query.="ORDER BY name ASC";

		$userSet=mysqli_query($connection,$query);
		check_result($userSet,"get_all_users: Could not get users from db.");	
		return $userSet;
}

//Retrieve user record for one specific user by user id
function get_user($userId) {
		global $connection;
		$query="SELECT * ";
		$query.="FROM users ";
		$query.="WHERE id={$userId} LIMIT 1";

		$userSet=mysqli_query($connection,$query);
		check_result($userSet,"get_user: Could not get user info for id=".$userId);	
		if($userArray = mysqli_fetch_assoc($userSet)) {
 			return $userArray;
		}else {
			return null;	
		}
}
//Retrieve user record by email
function get_user_by_email($email) {
		global $connection;
		$safeEmail=prep_sql_string($email);
		$query="SELECT * ";
		$query.="FROM users ";
		$query.="WHERE email='{$safeEmail}' LIMIT 1";

		$userSet=mysqli_query($connection,$query);
		//I am not logging errors for invalid email authentications
		if($userArray = mysqli_fetch_assoc($userSet)) {
 			return $userArray;
		}else {
			return null;	
		}
}

//Retrieve a specific image record from database
function get_image($imageId) {
	global $connection;
	
	$query = "SELECT * ";
	$query.= "FROM images ";
	$query.= "WHERE image_id = {$imageId} LIMIT 1; ";
	
	$result = mysqli_query($connection,$query);
	check_result($result,"get_image: Could not get image info for image_id=".$imageId);
	
	if($row = mysqli_fetch_assoc($result)) {
 		return $row;
	}else {
		return null;	
	}
}

//Build a profile image url for display
function build_image_url($userId){	
	$userArray=get_user($userId);	
	if($userArray["profile_image_id"]) {
		if($safeFileName=get_safe_file_name($userArray["profile_image_id"])){
			return PICTURE_ROOT.$userArray["user_dir"]."/".$safeFileName;
		} else {
			return DEFAULT_IMAGE;	
		}
	} else {
		//If a user profile image is not set, use the application default
		return DEFAULT_IMAGE;		
	}			
}

function delete_image_in_db($imageId){
		global $connection;
		
		//Perform delete from db
		$query="DELETE FROM images ";
		$query.="WHERE image_id={$imageId} LIMIT 1";	
	
		$result=mysqli_query($connection,$query);

		if($result&&mysqli_affected_rows($connection)==1) {
			return true;
		}	else {
			error_log("delete_image_in db: ImageId: ".$imageId." could not be deleted in db: ".mysqli_error($connection),0);
			return false;		
		}
}

function delete_all_images_in_db($userId){
	global $connection;
	//Perform delete from db
	$query="DELETE FROM images ";
	$query.="WHERE user_id={$userId}";	
	
	$result=mysqli_query($connection,$query);
	if($result) return true;
	else return false;		
}

function delete_user($userId){
	global $connection;
	//Perform delete from db
	$query="DELETE FROM users ";
	$query.="WHERE id={$userId}";	
	
	$result=mysqli_query($connection,$query);
	if($result)	return true;
	else return false;		
}
?>