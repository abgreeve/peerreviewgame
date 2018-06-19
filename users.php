<?php
// include 'jira-class.php';
include 'lib/generallib.php';
include 'classes/manager.php';
include 'classes/reward_manager.php';
include 'classes/card_manager.php';
include 'database/database.class.php';
include_once 'lib/navigation.php';

$navigation = new navigation('users.php');

// Should go in a lib / class somewhere.
$DB = new DB();
$users = $DB->get_records('users');

$userid = null;
$issuecount = null;
$inventory = null;

if (isset($_POST['userid'])) {
    $userid = $_POST['userid'];
    $manager = new manager();
    $competition = $manager->get_active_competition_time();
    $rewardmanager = new reward_manager();
    $issues = $rewardmanager->get_unclaimed_reward_issues($userid, $competition);
    $issuecount = count($issues);
    if ($issuecount) {
        $issue = array_shift($issues);
        $params = 'userid=' . $userid . '&issueid=' . $issue->id .'&issuetype=1';
    }

    $gold = 0;
    // Get inventory.
    foreach ($users as $user) {
        if ($user->id == $userid) {
            $gold = $user->gold;
        }
    }
    // This also should be in a class / method somewhere.
    $sql = "SELECT c.id, c.cardname, COUNT(ui.cardid) as amount
              FROM user_inventory ui
              JOIN cards c ON c.id = ui.cardid
             WHERE ui.userid = :userid
             GROUP BY ui.cardid";
    $records = $DB->execute_sql($sql, ['userid' => $userid]);
    $cardmanager = new card_manager();
    $cards = array_map(function($record) use ($cardmanager) {
        return [
            'card' => $cardmanager->get_card($record->cardname, $record->id),
            'amount' => $record->amount
        ];
    }, $records);
    $inventory = ['gold' => $gold, 'cards' => $cards];
    // print_object($gold);
    // print_object($records);
    // print_object($inventory);

}
if (isset($_GET['userid'])) {
    $userid = $_GET['userid'];
    $issuetype = $_GET['issuetype'];
    $issueid = $_GET['issueid'];
    $manager = new manager();
    $rewardmanager = new reward_manager();
    $competition = $manager->get_active_competition_time();
    $rewards = $rewardmanager->get_reward($issuetype, $issueid, $userid, $competition);
    print_object($rewards);
    $issues = $rewardmanager->get_unclaimed_reward_issues($userid, $competition);
    $issuecount = count($issues);
    if ($issuecount) {
        $issue = array_shift($issues);
        $params = 'userid=' . $userid . '&issueid=' . $issue->id .'&issuetype=1';
    }
}

// $table = new atable($users);
$header = new page_head('User page');
?>

<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out() ?>
    <h1>Temp user page</h1>

    <form name="userselect" action="users.php" method="POST">
        <select name="userid">
            <?php
            foreach ($users as $user) {
                $selected = ($user->id == $userid) ? 'selected' : '';
                echo '<option value="' . $user->id . '" ' . $selected . '>' . $user->displayname . '</option>';
            }
            ?>
        </select>
        <input type="submit" name="submit" value="Go" />
    </form>

    <p>Number of chests to open: <?php echo $issuecount; ?></p>

    <?php if ($issuecount) { ?>
        <div class="card" style="width: 18em;">
            <div class="card-header">
                Chest
            </div>
            <div class="card-body">
                <p>There is treasure inside.</p>
                <a href="users.php?<?php echo $params; ?>" class="btn btn-secondary">Open</a>
            </div>
        </div>
    <?php } ?>

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