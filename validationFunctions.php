<?php
$errors=array();

function not_blank($value){
	return (isset($value)&&$value!=="");			
}

function check_required_fields($required_fields){
	global $errors;
	foreach($required_fields as $field){
		$value=trim($_POST[$field]);
		if(!not_blank($value)){
			$errors[$field]="Please enter a ".make_fieldname_text($field);
		}					
	}		
}

function check_max_field_lengths($maxFieldLengths=array()){
	global $errors;
	foreach($maxFieldLengths as $field=>$max){
		$value=trim($_POST[$field]);
		if(!(strlen($value)<=$max)) {
			$errors[$field]=make_fieldname_text($field)." is too long.";				
		}			
	}			
}

function check_min_field_lengths($minFieldLengths=array()){
	global $errors;
	foreach($minFieldLengths as $field=>$min){
		$value=trim($_POST[$field]);
		if(!(strlen($value)>=$min)) {
			$errors[$field]="The ".make_fieldname_text($field)." has to be at least {$min} characters long.";				
		}			
	}			
}

function prep_data($field) {
  $field = trim($field);
  $field = stripslashes($field);
  $field = htmlspecialchars($field);
  return $field;
}

function validate_email($email) {
	global $errors;
	
	 // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors["email"] = " The email address has an invalid format.";
      return false; 
	} else {
		return true;	
	}
}

//check the file type/extension for jpg,jpeg,jpe,png,gif,pdf and return it if valid
function check_image_extension(){
	global $errors;
	//Make it lower case
	$fileExtension = strtolower(pathinfo($_FILES["image_file"]["name"],PATHINFO_EXTENSION));
	
	if($fileExtension != "jpg" && $fileExtension != "png" && $fileExtension != "jpeg"&& $fileExtension != "gif" && $fileExtension != "jpe"&& $fileExtension != "bmp") {
		$errors["file_name_extention"]="This type of image file is not supported (Only jpg,png,jpeg,gif,jpe,bmp are supported)";
    	return null;
	} else {
		return $fileExtension;
	}	
}

//Test if string contains numbers, letters and _ only
function char_test($inputString){
	return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i",$inputString))) ? true : false;
}

function validate_password(){
	if(char_test($_POST["password"])){
		$errors["password"]="The password you typed contains invalid characters.";	
	}
}

function validate_image_name(){
	global $errors;
	//Check if file was uploaded with POST
	if (is_uploaded_file($_FILES["image_file"]["tmp_name"])) {
		
		//Replace spaces with _
		$imageName=str_replace(" ","_",basename($_FILES["image_file"]["name"]));
		
		//Make sure the name has numbers,letters and _ only	
		$nameOk=char_test($imageName);

		$ext=check_image_extension();

		if($nameOk&&$ext) {
			return $imageName;
		} else {
			$errors["file_name"]="This file name is invalid.";
			return null;		
		}
	} else{
		$errors["file_upload"]="This file was not uploaded properly.";	
	}
}

function check_image_parameters() {
	global $errors;
    $imageProperties = getimagesize($_FILES["image_file"]["tmp_name"]);
 
    //Supported file types: gif(1),jpeg(2),png(3),bmp(6),mpeg(17)
    if(in_array($imageProperties[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP,IMAGETYPE_ICO)))  {
        return $imageProperties;
    }
    	//Return null if this is not a supported image type
    	$errors["file_type"]="This file type is not supported.";
   	return null;
}
function authenticate_user($typedPassword) {
		global $errors;
		global $loggedInUser;
			
		if($loggedInUser) {
			$savedPassword=$loggedInUser["password"];
			$hash=crypt($typedPassword,$savedPassword);
			if($hash===$savedPassword) {
				return true;		
			}	else {
				return null;		
			}
		} else {
			return null;		
		}	
}
?>