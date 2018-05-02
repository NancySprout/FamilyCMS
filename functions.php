<?php require_once("../includes/dbFunctions.php"); ?>
<?php require_once("../includes/systemConstants.php"); ?>

<?php

//Initialize global user arrays
$selectedUser=array();
$loggedInUser=array();

function redirect_to($newLocation) {
	header("Location: ".$newLocation);
	exit;
}	

//Replace _ with space and add uppercase
function make_fieldname_text($fieldname) {
	$fieldname=str_replace("_", " ",$fieldname);
	$fieldname=ucfirst($fieldname);
	return $fieldname;		
}

function output_errors($errors=array()) {
	$errorList="";
	if(!empty($errors)){
		$errorList.="<div>";
		$errorList.="Please try to correct the following:";
		$errorList.="<ul>";
		foreach($errors as $key=>$error){
			$errorList.="<li>";
			$errorList.=htmlentities($error);
			$errorList.="</li>";		
		}			
		$errorList.="</ul></div>";
	}		
	return $errorList;
}		

function get_base_name(){
	//Make it lower case also
	$baseName = strtolower(pathinfo($_FILES["image_file"]["name"],PATHINFO_BASENAME));
	return $baseName;
}	

function generate_salt($length){
	//Not 100% unique, not 100% random, but good enough for salt
	//MD5 returns 32 characters
	$unique_random_string=md5(uniqid(mt_rand(),true));
	
	//valid characters for a salt are [a-zA-Z0-9./]
	//base64 code returns + instead of .
	$base64_string=base64_encode($unique_random_string);
	
	//But not '+' which is valid in base64 encoding
	$modified_base64_string=str_replace('+', '.' ,$base64_string);
	
	//Truncate string to the correct length
	$salt=substr($modified_base64_string,0,$length);
	
	return $salt;
}

function salt_and_encrypt($password) {
	//2y- use Blowfish, 10- cost parameter
	$hash_format="$2y$10$";
	$salt_length=22;
	$salt=generate_salt($salt_length);
	$format_and_salt=$hash_format.$salt;
	$hash=crypt($password,$format_and_salt);	
	return $hash;
}

//Create a safe user directory
function create_user_dir(){
	$safeUserDir=bin2hex(random_bytes(4));
	$oldDir=getcwd();
	chdir(FS_PATH);
	if(mkdir("$safeUserDir")) {
	chmod("$safeUserDir",0755); //owner=r,w,x  group=r,x  other=r,x
	chdir($oldDir);
	return 	$safeUserDir;
	} else {
		chdir($oldDir);
		error_log("create_user_dir: Could not create user directory in ".FS_PATH);
		return false;
	}
}

function delete_image_file($userId, $imageId){
	$userDir=get_user_dir($userId);
	$safeFileName=get_safe_file_name($imageId);
	$message="user_id: ".$userId." image_id: ".$imageId." user_dir: ".$userDir." safe_file_name: ".$safeFileName;
	if($userDir&&$safeFileName){
		
		//File destination
		$filePath=FS_PATH.$userDir."/".$safeFileName;

		//delete file from file system (FS))		
		$oldDir = getcwd(); // Save the current working directory
    	chdir(FS_PATH."/".$userDir);
    	if(unlink($safeFileName)) {
			chdir($oldDir); // Restore the old working directory    
			return true;  	
    	} else {
    		chdir($oldDir); // Restore the old working directory    
			error_log("delete_image_file: ".$safeFileName." could not be deleted.");  
			return false;  	
    	}		
	} else {
		error_log("delete_image_file: Could not retrieve parameters from db: ".$message);
		return false;
	}
}

//Delete all files for a specific user as well  as it's user directory
function delete_user_dir($userId) {
	$userDir=get_user_dir($userId);	
	if($userDir) {		
		//File destination
		$userPath=FS_PATH.$userDir;"some/dir/*.txt";
		array_map('unlink', glob(FS_PATH.$userDir."/*.*"));
		if(rmdir($userPath)) { 
			return true;
		} else return false;		
	} else {
		error_log("delete_user_dir: user_dir could not be found.");
		return false;
	}
}

//Retrieve a complete record (array) for selected user from db
function find_selected_user() {
	global $selectedUser;	
	if (isset($_GET["id"])){
		$userId=(int) ($_GET["id"]);
		$selectedUser=get_user($userId);
		$_SESSION["id"]=$userId;
		if(!$selectedUser) {
		error_log("find_selected_user: selected user not found for user_id=".$userId);
		}
	} elseif (isset($_SESSION["id"])){
		$userId=(int) ($_SESSION["id"]);
		$selectedUser=get_user($userId);
		if(!$selectedUser) {
		error_log("find_selected_user: selected user not found for user_id=".$userId);
		}
	}	else $selectedUser=null;	
}

//Retrieve a complete record (array) for current logged in user from db
function find_logged_in_user() {
	global $loggedInUser;	
	if (isset($_POST["logId"])) {
		$loggedInUser=get_user((int)$_POST["logId"]);
	} elseif (isset($_SESSION["logId"])) {
		$loggedInUser=get_user((int)$_SESSION["logId"]);					
	} else {
		$loggedInUser=null;	
	}
}

//Display the profile images of all users
function display_users() {
	global $loggedInUser;	
	$allUsers=get_all_users();
	$output="<ul>";
	while($user=mysqli_fetch_assoc($allUsers)) {
		$output.="<li><h3>";
		$output.=htmlentities($user["name"]);
		$output.="</h3><p>";
		$output.=htmlentities($user["description"]);
		$output.="</p><a href=\"";	
		$output.="show_user_images.php";
		$output.="?id=";
		$output.=urlencode($user["id"]);
		$output.="\">";
		$output.="<img src=\"";
		$output.=build_image_url($user["id"]);
		$output.="\" ";
		$output.="alt=";
		$output.=htmlentities($user["name"]);		
		$output.="></a></li>";
	}
	$output.="</ul>";
	mysqli_free_result($allUsers);
	return $output;
}
?>
