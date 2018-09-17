<?php require_once("session.php"); ?>
<?php require_once("functions.php"); ?>
<?php require_once("systemConstants.php"); ?>
<?php require_once("dbConnection.php"); ?>
<?php require_once("dbFunctions.php"); ?>

<?php
	find_logged_in_user();
	find_selected_user();
	$loggedId= (int) $loggedInUser["id"];
	$userId=0; //there is no user selected
	?>
	
<?php require_once("navigation.php"); ?>

	<section>
		<h1>Meet the Team</h1>
		<div class="gallery">
			<?php	echo display_users(); ?>
		</div>	
	</section>
	
<?php include("footer.php"); ?>

