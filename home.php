<?php
// include 'jira-class.php';
include 'lib/generallib.php';
include 'session/sessioncheck.php';
include 'lib/navigation.php';
include 'classes/manager.php';
include 'classes/user.php';
include 'classes/card_manager.php';
include 'classes/user_manager.php';
// include_once 'classes/competition_time.php';
include_once 'database/database.class.php';

$navigation = new navigation('home.php');

$userid = get_userid();
$user = user::load_from_id($userid);

$manager = new manager();
$currentcompetition = $manager->get_active_competition_time();
$usermanager = new user_manager($userid);
$completedissues = $usermanager->get_completed_issues($currentcompetition);
$completedissuecount = count($completedissues);
// print_object($completedissues);
// print_object($user);
if (isset($_GET['issuedetails'])) {
    $data = array_map(function($issue) {
        $datedetails = new DateTime();
        $datedetails->setTimeStamp($issue->datecompleted);
        return [
            'MDL' => '<a href="https://tracker.moodle.org/browse/' . $issue->mdl . '">' . $issue->mdl . '</a>',
            'Summary' => $issue->summary,
            'Points' => $issue->points,
            'Date completed' => $datedetails->format('d-m-Y')
        ];
    }, $completedissues);
    $issuedata = new atable($data);
}


$header = new page_head('Home');
?>
<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out() ?>
    <h1>Home</h1>
    <p>Number of completed issues in this competition: <?php echo $completedissuecount; ?> &nbsp; <a href="home.php?issuedetails=1">Details</a></p>
    <?php
    if (isset($issuedata)) {
        echo $issuedata->out();
    }
    ?>
</body>
</html>