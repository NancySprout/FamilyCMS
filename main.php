<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/functions.php"); ?>

<?php include("../includes/header.php"); ?>

<?php find_logged_in_user(); 

if(!$loggedInUser){
	redirect_to("logout.php");
}?>

<main>
	<header>
		<?php
		echo output_errors();
		echo get_message();?>
	</header>
	<nav>
	<?php		
		 if($loggedInUser["admin"]){ ?>
		<a href="new_user.php">Add Someone Special</a>		
		<?php } ?>	
		<a href="logout.php">Logout</a>
	</nav>
	<section>
		<?php	echo display_users(); ?>
	</section>
</main>

<?php include("../includes/footer.php"); ?>