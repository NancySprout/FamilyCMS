<?php 

include("header.php");?>
	<header>
		<!-- Hamburger menu icon -->
		<div id="menuIcon" onclick="openDropDown()">
  			<div class="bar1"></div>
  			<div class="bar2"></div>
  			<div class="bar3"></div>
		</div>
		<?php
		//Logged in user info 
		if($loggedInUser) {?><p id="loggedInAs"><?php echo "Hello ".$loggedInUser["name"]; ?></p><?php }?>
		<nav>
			<?php 
			//Strip first forward slash
			$currentScript=substr($_SERVER["SCRIPT_NAME"],1);
			//If you are not home already-show home button	
			if($currentScript!="index.php") { ?>
				<a class="button" href="index.php" onclick="closeDropDown()">Home</a>	
			<?php	}
			
			 //If there is a logged in user show "logout" or else show "login" button
			if($loggedInUser) { ?>
					<a class="button" href="logout.php">Logout</a>
			<?php	} else {?>	
				<button class="button" type="button" onclick="showLoginForm(),closeDropDown()">Log In</button>	
			<?php }	
			
			//Load image - button
			//If the selected user is logged in or the logged in user is an admin
			 if($selectedUser&&($loggedInUser["admin"]||($userId==$loggedId))) { ?>
			 	<!-- Load new picture button -->
				<button class="button" type="button" onclick="showLoadImageForm(),closeDropDown()">New picture</button>		
				<!-- Update profile- button -->
				<button class="button" onclick="showUpdateAccountForm(),closeDropDown()">Update profile</button>
			<?php }	
			
			//Create a new user - button
			if($loggedInUser["admin"]) { ?>
				<button class="button" type="button" onclick="showAddUserForm(),closeDropDown()">Add someone</button>
			<?php }	
			
			//Delete user button
			//An admin cannot delete his/her own account
			if($selectedUser&&$loggedInUser["admin"]&&($loggedId!=$userId)) { ?>
				<button class="button" type="button" onclick="document.getElementById('deleteAlert').style.display='block',closeDropDown()">Remove this user</button>
			<?php	} ?>								
		</nav>
			
		<!--Moved these divs and forms out of the <nav> to position relative to header-->
		<!--These are not displayed by default-->	
		
		<!-- Delete user popup-->
		<div id="deleteAlert">
			<p>Are you sure you want to permanently delete this user?</p>
			<div id="OkCancel">
				<a class="button" href="delete_user.php" onclick="document.getElementById('deleteAlert').style.display = 'none'">OK</a>
				<button class="cancelButton" type="button" onclick="document.getElementById('deleteAlert').style.display = 'none'">Cancel</button>
			</div>
		</div>
		
		<!--Login form-->
		<div id="loginForm" class="popupForm">
			<form action="" method="">
				<label for="email1">E-mail:</label>
				<input type="email" name="email" id="email1" size="20" placeholder="Enter email" value="" required>
				<label for="passw">Password:</label>
				<input type="password" name="password" id="passw" size="20" placeholder="Enter password" maxlength="20" value="" onkeydown="pressEnter(event,this.nextElementSibling)" required>
				<input type="button" name="submit" value="Login" onclick="submitForm(this.parentElement,'login.php')">
				<button type="button" class="cancelButton" onclick="cancelForm(this.parentElement)">Cancel</button>
				<div class="status"></div>
			</form>
		</div>	
		
		<div id="loadImageForm" class="popupForm">
			<form  enctype="multipart/form-data" action="load_image.php" method="post" onsubmit="return validateFile(this)">
				<label for="file">Get a new picture:</label>
				<input type="file" name="image_file" id="file" title="Please select an image file" accept="image/jpg,image/png,image/jpeg,image/gif,image/jpe,image/bmp" required>
				<label for="desc">All about this picture:</label>	
					<textarea name="desc" id="desc" rows="5" cols="20" size="100" placeholder="Something about this picture..." onkeydown="pressEnter(event,this.nextElementSibling)"></textarea>
				<label for="profile">Make this my profile picture:</label>	
					<input type="checkbox" id="profile" name="profile" value="1">
				<input type="submit" name="submit" value="Load it">
				<button type="button" class="cancelButton" onclick="this.parentElement.parentElement.style.display = 'none'">Cancel</button>
				<div class="status"></div>	
			</form>
		</div>		
		
		<div id="addUserForm" class="popupForm">
			<form action="" method="">	
				<label for="name">First name:</label>
					<input type="text" name="name" value="" id="name" size="20" required>
				<label for="email2">E-mail:</label>	
					<input type="email" name="email" id="email2" size="20" required>	
				<label for="password">Password:</label>
					<input type="password" name="password" id="password" size="20" value="" required>
				<label for="admin">Administrator:</label>	
					<input type="checkbox" name="admin" id="admin" >	
				<label for="desc">All about me:</label>	
					<textarea name="desc" id="desc" rows="5" size="400" placeholder="Knock your socks off..." ></textarea>
					<input type="button" name="submit" value="Add this Person" onclick="submitForm(this.parentElement,'new_user.php')">
				<button type="button" class="cancelButton" onclick="cancelForm(this.parentElement)">Cancel</button>
				<div class="status"></div>	
			</form>	
		</div>	
		
		<?php 	if($selectedUser&&($loggedInUser["admin"]||($userId==$loggedId))) { ?>	
		<!--The update_account.php script only updates fields that are filled out-->	
		<div id="updateAccountForm" class="popupForm">		
			<form action="" method="">	
				<label for="name">First name:</label>
					<input type="text" name="name" id="name" size="20" placeholder="<?php echo $selectedUser["name"];?>" onkeydown="pressEnter(event,this.nextElementSibling)">
				<label for="passw">New password:</label>
					<input type="password" name="password" id="passw" size="20" onkeydown="pressEnter(event,this.nextElementSibling)">
				<label for="email3">E-mail address:</label>
					<input type="email" name="email" id="email3" size="20" placeholder="<?php echo $selectedUser["email"];?>" onkeydown="pressEnter(event,this.nextElementSibling)">
				<label for="desc">All about me:</label>
					<textarea name="description" id="desc" rows="10" cols="40" size="400" value="" placeholder="<?php echo $selectedUser["description"];?>" ></textarea>
					<input type="button" name="submit" value="Update" onclick="submitForm(this.parentElement,'update_account.php')">	
					<button type="button" class="cancelButton" onclick="cancelForm(this.parentElement)">Cancel</button>	
					<div class="status"></div>		
			</form>
		</div>
		<?php } ?>		
	</header>
	
<script>

function validateFile(form) {
	var inputFile=document.getElementById("file").files[0];
	var statusBox=form.querySelector("div");
	
	//Check for size under 1Mb
	if(inputFile.size>1050000) {
		//Get the div that will contain the status message - the form has only one.
		statusBox.innerHTML="*The file size should be under 1Mb.";
		return false;
	}
	//Check file extension- jpg,png,jpeg,gif,jpe,bmp
	if(!inputFile.name.match(/.(jpg|jpeg|png|gif|jpe|bmp)$/i)) {
		statusBox.innerHTML="*Unsupported file type! Only jpg,png,jpeg,gif,jpe or bmp are supported.";
		return false;
	}
}

//This function triggers the onclick event on the submit/ok buttons when you press ENTER after filling out the last/some input field of a form.
function pressEnter(event,submitButton) {
    var x = event.keyCode;
    if (x == 13) {  // 13 is the Enter key
        submitButton.click();
    }
}

function cancelForm(form) {
	//Reset the form
	form.reset();
	//Reset status message
	form.getElementsByClassName("status")[0].innerHTML="";
	//Hide form div
	form.parentElement.style.display = 'none';
}

//For future use
function checkPassword(input) {
	var inputLength=input.value.length;
	var password=document.getElementById("pws").value;
	if (password.substr(0,inputLength)!=input.value) {
		document.getElementById("passwordMatch").innerHTML="Passwords don't match";	
	} else {
		document.getElementById("passwordMatch").innerHTML="";	
	}	
}

function openDropDown() {
	document.getElementById("menuIcon").classList.toggle("cross");
	document.querySelector("nav").classList.toggle("expand");
}

function closeDropDown() {
	document.getElementById("menuIcon").classList.remove("cross");
	document.querySelector("nav").classList.remove("expand");
}

function showAddUserForm() {
	var form=document.getElementById("addUserForm");
	form.style.display="block";
	form.querySelector("input:first-of-type").focus();
}

function showLoginForm() {
	var form=document.getElementById("loginForm");
	form.style.display="block";
	form.querySelector("input:first-of-type").focus();
}

function showLoadImageForm() {
	var form=document.getElementById("loadImageForm");
	form.style.display="block";
	form.querySelector("input:first-of-type").focus();
}

function showUpdateAccountForm() {
	var form=document.getElementById("updateAccountForm");
	form.style.display="block";
	form.querySelector("input:first-of-type").focus();
}

function submitForm(form,phpFile) {
	//Get all the input fields of the form
	var inputs = form.querySelectorAll("*[name]");
	var statusBox=form.querySelector("div");
	var formData=[]; //Not a FormData object!

   for(var i=0; i<inputs.length; i++) {
   	formData.push(inputs[i].name+"="+ inputs[i].value);
   }
   var dataString=formData.join("&");
   var http;
   if(window.XMLHttpRequest) {
      http=new XMLHttpRequest;
   }
   else {
       http=new ActiveXObject("Microsoft.XMLHTTP");
   }
   http.open("POST", phpFile, true);
   http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
   http.onreadystatechange  = function() {
	 if (this.readyState == 4 && this.status == 200) {
	 	var response=this.responseText.toString().trim();
	  	if(response==="Success"||response==="Exit") {
	  		//Reload page
	  		location.reload();
	  	} else {
	  		//Display error message
	  		 statusBox.innerHTML="*"+this.responseText;
	  	}
	 } else {
	  	 statusBox.innerHTML="Waiting...";
	 }
	}
	http.send(encodeURI(dataString)); 
}

</script>