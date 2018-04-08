<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/dbConnection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/dbFunctions.php"); ?>
<?php require_once("../includes/systemConstants.php"); ?>

<?php 
find_selected_user();
find_logged_in_user();

 if (!$loggedInUser) {
   redirect_to("logout.php");
  } 
  if (!$selectedUser) {
	redirect_to("main.php");  
  }    
  $loggedId= (int) $loggedInUser["id"];
  $userId=(int)$selectedUser["id"];
?>
	
<?php include("../includes/header.php"); ?>
<main>
	<h2><?php echo htmlentities($selectedUser["name"])."'s Pictures"?></h2>
	<nav>
		<a href="main.php">Go Back</a>
		<a href="logout.php">Logout</a>
		<?php
			//if this user is logged in 
		if(($loggedId==$userId)||$loggedInUser["admin"]){ ?>
		<form  enctype="multipart/form-data" action="load_image.php" method="post">
			<input type="file" name="image_file">
			<textarea name="desc">Knock your socks off...</textarea>
			<input type="checkbox" name="profile" value="1">
			<input type="submit" name="submit" value="Load Image">
		</form>
		<a href="update_account.php">Profile</a>
	<?php } //if this user is logged in
		//Show this only if the logged in user is an admin and this is not his account
		//An admin cannot delete his own account!
	 if($loggedInUser["admin"]&($loggedId!=$userId)){ ?>
		<a href="delete_user.php">This Person is Leaving</a>
			<?php	 } //if this is an admin	 ?>
	</nav>	
	 <h3>About Me</h3> 
	 
	 <?php 	if($loggedId==$userId){ ?>
	 <p contenteditable="true"><?php echo htmlentities($selectedUser["description"])?></p>
	 <?php } else { ?>
			 <p><?php echo htmlentities($selectedUser["description"])?></p>
	 <?php } 
	 	
	$query = "SELECT * ";
	$query.= "FROM images ";
	$query.= "WHERE user_id = {$userId}; ";
	$result = mysqli_query($connection,$query);
	
	check_result($result,"show_user_images.php:Could not retrieve image entries for user: ".$selectedUser["name"]);?>
	<section class="imagesGroup">
	<?php
	while($row = mysqli_fetch_assoc($result)) {
		$imageId=(int) $row["image_id"];
		$imageName=htmlentities($row["image_name"]);
		$safeFileName=htmlentities($row["safe_file_name"]);	
		$time=htmlentities($row["timestamp"]);
	 	$imageUrl=PICTURE_ROOT.htmlentities($selectedUser["user_dir"])."/".$safeFileName;?>	
	  	<div class="image">
			<img src="<?php echo $imageUrl?>" width= "200" height="200" alt="<?php echo $imageName?>" />
			<p><?php echo "File: ".$imageName?><br><?php echo "Dated: ".date("d:m:Y H:i",strtotime($time))?></p>
				
		<?php //If this is an admin or this is the logged in user's account	
			if($loggedInUser["admin"]||($loggedId==$userId)){?>
			<p contenteditable="true"><?php echo htmlentities($row["description"])?></p>
			<a href="delete_image.php?imageId=<?php echo $imageId?>">Delete</a>
		<?php } else { ?>
				<p><?php echo htmlentities($row["description"])?></p>
		</div>
		<?php	}
		} //while
			mysqli_free_result($result);?>
		</section>	
</main>
<?php include("../includes/footer.php"); ?>

