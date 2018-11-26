<?php
include 'jira-class.php';
include 'lib/generallib.php';
include 'classes/manager.php';
include 'database/database.class.php';
include_once 'lib/navigation.php';

$navigation = new navigation('reviewlist.php');
$database = new DB();
$manager = new manager($database);

$issuecount = $manager->update_issues();
$manager->update_plugin_issues();

$manager->check_for_peer_reviewed_issues();
$manager->check_for_peer_reviewed_plugin_issues();
// TODO: Do the same for plugins.
$issues = $manager->get_issues();
$pluginissues = $manager->get_plugin_issues();
//
// $manager->get_other_issues();

$table = new atable($issues);
$table->set_headers(array_keys($issues[0]));
$plugintable = new atable($pluginissues);
$plugintable->set_headers(array_keys($pluginissues[0]));
$header = new page_head('Issues to be reviewed.');
?>

<html>
<?php echo $header->out(); ?>
<body>
	<?php echo $navigation->out(); ?>
    <h1>Peer reviewing</h1>
    <p>Issue count: <?php echo $issuecount; ?></p>
	<?php echo $table->out(); ?>
    <h2>Contrib issues</h2>
    <?php echo $plugintable->out(); ?>
</body>
</html>