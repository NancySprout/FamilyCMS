<?php include("header.php");?>

	<header>
		<!-- Hamburger menu icon -->
		<div id="menuIcon" onclick="openDropDown()">
  			<div class="bar1"></div>
  			<div class="bar2"></div>
  			<div class="bar3"></div>
		</div>
		<?php
		//Logged in user info 
		if($loggedId!=0) {?><p class="loggedInAs"><?php echo "<span>You are logged in as: </span>".$loggedInUser["name"]; ?></p><?php }?>
		<nav>
			<?php 
			//Strip first forward slash
			$currentScript=substr($_SERVER["SCRIPT_NAME"],1);
			//If you are not home already-show home button	
			if($currentScript!="main.php") { ?>
				<a class="button" href="main.php">Home</a>	
			<?php	} else { ?>
				<a class="button" href="http://micheledupreez.ca">Back</a>	
			<?php } 
			 //If there is a logged in user
			if($loggedInUser) { ?>
					<!-- Login or logout - button -->
					<a class="button" href="logout.php">Logout</a>
					<?php  //Load image - button
					 if((($loggedId==$userId)||$loggedInUser["admin"])&$userId!=0) { ?>
					<button class="button" type="button" onclick="showLoadImageForm()">New picture</button>		
					<!-- Update profile- button -->
					<button class="button" onclick="showUpdateAccountForm()">Update profile</button>
					<?php 	} 			
			} else {?>	
				<button class="button" type="button" onclick="showLoginForm()">Log In</button>	
			<?php }?>
				
			<?php //Create a new user - button
			if($loggedInUser["admin"]) { ?>
					<button class="button" type="button" onclick="showAddUserForm()">Add someone</button>
			<?php }	?>	
					
			<?php //Delete user - button
			//An admin cannot delete his/her own account!
			if($loggedInUser["admin"]&($loggedId!=$userId)&($userId!=0)) { ?>
				<a class="button" href="delete_user.php" onclick="confirm('Are you sure you want to remove this user?')">Remove this user</a>
			<?php	} ?>								
		</nav>	
		<!--Forms are not displayed by default-->	
		<div id="loginForm" class="popupForm">
			<form action="login.php" method="post">
				<label for="email1">E-mail:</label>
				<input type="email" name="email" id="email1" placeholder="Enter email" value="" autofocus required>
				<label for="passw">Password:</label>
				<input type="password" name="password" id="passw" placeholder="Enter password" maxlength="20" value="" required>
				<input type="submit" name="submit" value="Login" >
				<button type="button" class="cancelButton" onclick="this.parentElement.parentElement.style.display = 'none'">Cancel</button>
			</form>
		</div>	
		
		<div id="loadImageForm" class="popupForm">
			<form  enctype="multipart/form-data" action="load_image.php" method="post">
				<label for="file">Get a new picture:</label>
				<input type="file" name="image_file" id="file" title="Supported file types: jpg,png,jpeg,gif,jpe,bmp" required>
				<label for="desc">All about this picture:</label>	
					<textarea name="desc" id="desc" rows="5" cols="20" size="100" placeholder="Something about this picture..."></textarea>
				<label for="profile">Make this my profile picture:</label>	
					<input type="checkbox" id="profile" name="profile" value="1"  checked="checked">
				<input type="submit" name="submit" value="Load it">
				<button type="button" class="cancelButton" onclick="this.parentElement.parentElement.style.display = 'none'">Cancel</button>
			</form>
		</div>		
		
		<div id="addUserForm" class="popupForm">
			<form action="new_user.php" method="post">	
				<label for="name">First name:</label>
					<input type="text" name="name" value="" id="name" size="20" required autofocus>
				<label for="email2">E-mail:</label>	
					<input type="email" name="email" id="email2" required>	
				<label for="password">Password:</label>
					<input type="password" name="password" id="password" size="20" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$" title="Minimum 8 characters, at least one uppercase letter, one lowercase letter and one number" required>
				<label for="admin">Administrator:</label>	
					<input type="checkbox" name="admin" id="admin" value="1">	
				<label for="desc">All about me:</label>	
					<textarea name="desc" id="desc" rows="5" size="400" placeholder="Knock your socks off..."></textarea>				
					<input type="submit" name="submit" value="Add this Person" >	
				<button type="button" class="cancelButton" onclick="this.parentElement.parentElement.style.display = 'none'">Cancel</button>
			</form>	
		</div>	
			
		<div id="updateAccountForm" class="popupForm">		
			<form action="update_account.php" method="post">	
				<label for="name">First name:</label>
					<input type="text" name="name" id="name" size="20" placeholder="<?php echo $selectedUser["name"];?>" value="<?php echo $selectedUser["name"];?>">
				<label for="psw">New password:</label>
					<input type="password" id="pws" value="" required autofocus >
				<label for="repeat">Repeat new password:</label>
					<input type="password" name="password" id="repeat" size="20" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$" title="Minimum eight characters, at least one uppercase letter, one lowercase letter and one number" value="" onkeyUp="checkPassword(this)">
					<div id="passwordMatch"></div>	
				<label for="email3">E-mail address:</label>
					<input type="email" name="email" id="email3" placeholder="<?php echo $selectedUser["email"];?>" value="<?php echo $selectedUser["email"];?>">
				<label for="desc">All about me:</label>
					<textarea name="desc" id="desc" rows="10" cols="40" size="400" placeholder="<?php echo $selectedUser["description"];?>" value="<?php echo $selectedUser["description"];?>"></textarea>
					<input type="submit" name="submit" value="Update" >
					<button type="button" class="cancelButton" onclick="this.parentElement.parentElement.style.display = 'none'">Cancel</button>	
			</form>		
		</div>		
	</header>
	
<script>

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

function showAddUserForm() {
	document.getElementById("addUserForm").style.display="block";
}

function showLoginForm() {
	document.getElementById("loginForm").style.display="block";
}

function showLoadImageForm() {
	document.getElementById("loadImageForm").style.display="block";
}

function showUpdateAccountForm() {
	document.getElementById("updateAccountForm").style.display="block";
}

</script>