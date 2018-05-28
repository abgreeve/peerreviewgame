<?php
// include 'jira-class.php';
include 'lib/generallib.php';
include 'session/sessioncheck.php';
include 'lib/navigation.php';
include 'classes/manager.php';
include 'classes/card_manager.php';
include_once 'database/database.class.php';

$navigation = new navigation('management.php');
admin_check();

$manager = new manager();
$cardmanager = new card_manager();

$competitiondetails = $manager->get_active_competition_time();

$currentteams = $manager->get_current_teams();

$cardset = $cardmanager->get_card_set($competitiondetails);
// print_object($cardset);
// print_object($currentteams);

// $completedissues = $manager->get_hq_completed_issues();

// $table = new atable($completedissues);
$header = new page_head('Management page');
?>

<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out(); ?>
    <h1>Management</h1>

    <h2><?php echo $competitiondetails->get_title() ?></h2>
    <p>Current competition period: <?php echo $competitiondetails->get_starttime() ?> - <?php echo $competitiondetails->get_endtime() ?> <a href="timeedit.php">edit</a></p>

    <h3>Current active cards</h3>
    <?php
        if (!empty($cardset)) {
            foreach ($cardset as $card) {
                echo $card->get_name();
                echo '<br />';
            }
        } else {
            echo '<p>No cards selected for this competition. <a href="compcards.php?id=' . $competitiondetails->get_id() . '">Change</a></p>';
        }
    ?>



    <!-- <?php //echo $table->out(); ?> -->
</body>
</html>