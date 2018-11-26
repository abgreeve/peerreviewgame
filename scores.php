<?php
include 'jira-class.php';
include 'lib/generallib.php';
include 'classes/manager.php';
include 'database/database.class.php';
include 'lib/navigation.php';

$navigation = new navigation('scores.php');
$database = new DB();
$manager = new manager($database);

if (isset($_GET['id'])) {
    $competitiontime = competition_time::load_from_id($_GET['id']);
    $issuescores = $manager->get_issue_scores($competitiontime);
    $contribscores = $manager->get_contrib_scores($competitiontime);

} else {
    $issuescores = $manager->get_issue_scores();
    // print_object($issuescores);
    $contribscores = $manager->get_contrib_scores();
}

$cscores = [];
foreach ($contribscores as $contribscore) {
    $cscores[$contribscore->username] = ['displayname' => $contribscore->displayname, 'Points' => $contribscore->Points];
}

$iscores = [];
foreach ($issuescores as $issuescore) {
    $contribpoints = 0;
    $total = $issuescore->Points;
    if (array_key_exists($issuescore->username, $cscores)) {
        $contribpoints = $cscores[$issuescore->username]['Points'];
        $total = $total + $contribpoints;
        unset($cscores[$issuescore->username]);
    }
    $iscores[$issuescore->username] = ['displayname' => $issuescore->displayname, 'MDL Points' => $issuescore->Points, 'Contrib Points' => $contribpoints, 'total' => $total];
}

// Make sure we include people that only did contrib reviews.
foreach ($cscores as $username => $cscore) {
    $iscores[$username] = ['displayname' => $cscore['displayname'], 'MDL Points' => 0, 'Contrib Points' => $cscore['Points'], 'total' => $cscore['Points']];
}

// Let's do this by time periods!
// Get the time periods.
$comptimes = $manager->get_all_competition_periods(false);

$html  = '';
foreach ($comptimes as $timeperiod) {
    $html .= '<a href="scores.php?id=' . $timeperiod->get_id() . '">' . $timeperiod->get_title() . '</a>';
    $html .= '<br />';
}

$tablestring = '';
if (!empty($iscores)) {
    $table = new atable($iscores);
    $table->set_headers(['Display Name', 'MDL Points', 'Contrib Points', 'Total']);
    $tablestring = $table->out();
}
$header = new page_head('Peer review - Scores');
?>

<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out() ?>
    <h1>Scores</h1>
    <br />
    <?php echo $html; ?>
    <?php
        if (isset($competitiontime)) {
            echo '<h2>' . $competitiontime->get_title() . '</h2>';
            echo '<p>For the competition time between ' . $competitiontime->get_starttime() . ' and ' . $competitiontime->get_endtime() . '</p>';
        }
    ?>
    <?php echo $tablestring; ?>
</body>
</html>