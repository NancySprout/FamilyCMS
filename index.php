<?php 

require_once("functions.php");

//Some user could be logged in
find_logged_in_user();
$loggedId= (int) $loggedInUser["id"];

//There is no user selected at this point
$userId=null; 

include("navigation.php"); ?>
	<section>
		<h1>Picture Gallery</h1>
		<!--Image gallery-->
		<div id="gallery">
			<?php	echo display_users(); ?>
		</div>	
	</section>
<?php include("footer.php"); ?>

