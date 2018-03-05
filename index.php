<?php
include 'jira-class.php';
include 'lib/generallib.php';
include 'classes/manager.php';
include 'database/database.class.php';


$manager = new manager();
// print_object($manager->update_issues());
// $manager->update_issues();
// print_object($manager->get_all_active_mdls());

// $manager->check_for_peer_reviewed_issues();
$issues = $manager->get_issues();
// print_object($issues);

$table = new atable($issues);
?>

<html>
<head>
<title>Peer review welcome page.</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>
	<h1>Peer reviewing</h1>
	<a href="scores.php">Scores</a> | <a href="management.php">Management</a>
	<?php echo $table->out(); ?>
</body>
</html>