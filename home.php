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
$database = new DB();

$userid = get_userid();
$user = user::load_from_id($userid, $database);

$manager = new manager($database);
$currentcompetition = $manager->get_active_competition_time();
$usermanager = new user_manager($userid, $database);
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
    if (!empty($data)) {
        $issuedata = new atable($data);
        $issuedata->set_headers(array_keys($data[0]));
    }
}

$inventory = $user->get_inventory();

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
    <h2>Inventory</h2>
    <?php if ($inventory) { ?>
        <div class="card" style="width: 25em;">
            <div class="card-header">
                Inventory
            </div>
            <div class="card-body">
                <h5>Gold <?php echo $inventory['gold'] ?></h5>
                <ul class="list-group list-group-flush">
                <?php
                    foreach ($inventory['cards'] as $card) {
                        echo '<li class="list-group-item">' . $card['card']->get_name() . ' (' . $card['amount'] . ')';
                    }
                ?>
                </ul>
            </div>
        </div>

    <?php } ?>

</body>
</html>