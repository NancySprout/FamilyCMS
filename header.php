<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php find_logged_in_user();?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8">
<title>We Belong Here</title>
	<link rel="stylesheet" href="/styles/normalize.css">
	<link rel="stylesheet" href="/styles/main.css">
</head>
<body>
<header >
	<h1>We Belong Here</h1>
	<?php if($loggedInUser) { ?>
	<h1>Hello <?php echo $loggedInUser["name"]; }?></h1>
</header>