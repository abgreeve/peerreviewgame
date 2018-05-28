<?php

include 'lib/generallib.php';
include 'classes/manager.php';
include 'database/database.class.php';

$id = 0;
$date = null;
if (isset($_GET['id'])) {
	$id = $_GET['id'];
} else if (isset($_POST['id'])) {
	$id = $_POST['id'];
	$date = $_POST['reviewdate'];
}

$manager = new manager();
$issue = $manager->get_issue($id);
// print_object($issue);
if (isset($date)) {
	// change to timestamp.
	$dateobject = new DateTime($date);
	$issue->set_date_sent_for_review($dateobject->getTimestamp());
	$issue->save();
	header('Location: http://localhost/peerreview/index.php');
}

$reviewdate = new DateTime();
$reviewdate->setTimestamp($issue->get_date_sent_for_review());
$datestring = $reviewdate->format('Y-m-d');

?>

<html>
<head>
<title>Peer review edit page</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>
	<?php include 'lib/navigation.php' ?>
	<h1>Edit stuff</h1>
	<form name="issueedit" method="POST" action="edit.php">
		<div>Summary</div>
		<div><input type="text" name="summary" value="<?php echo $issue->get_summary(); ?>" /></div>
		<div>Date up for peer review</div>
		<div><input type="text" name="reviewdate" value="<?php echo $datestring; ?>" /></div>
		<input type="hidden" name="id" value="<?php echo $issue->get_id(); ?>" />
		<input type="submit" name="submit" value="submit" />
	</form>
</body>
</html>