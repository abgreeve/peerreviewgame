<?php
include 'jira-class.php';
include 'lib/generallib.php';
include 'classes/manager.php';
include 'database/database.class.php';
include_once 'lib/navigation.php';

$navigation = new navigation('reviewlist.php');
$manager = new manager();

$manager->update_issues();
$manager->update_plugin_issues();

$manager->check_for_peer_reviewed_issues();
$manager->check_for_peer_reviewed_plugin_issues();
// TODO: Do the same for plugins.
$issues = $manager->get_issues();
$pluginissues = $manager->get_plugin_issues();
// print_object($pluginissues);
// 
// $manager->get_other_issues();

$table = new atable($issues);
$plugintable = new atable($pluginissues);
$header = new page_head('Issues to be reviewed.');
?>

<html>
<?php echo $header->out(); ?>
<body>
	<?php echo $navigation->out(); ?>
    <h1>Peer reviewing</h1>
	<?php echo $table->out(); ?>
    <h2>Contrib issues</h2>
    <?php echo $plugintable->out(); ?>
</body>
</html>