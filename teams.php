<?php
include 'lib/generallib.php';
include 'classes/manager.php';
include 'database/database.class.php';
include_once 'lib/navigation.php';

$navigation = new navigation('teams.php');
$database = new DB();
$manager = new manager($database);

if (isset($_POST['teamname'])) {
    $team = new team($_POST['teamname'], $_POST['competitionid'], $_POST['fortressname'], $_POST['hitpoints']);
    $team->save();
    $team->add_team_members($_POST['teammembers']);
}

$competition = null;
$teamdetails = null;
if (isset($_POST['compid'])) {
    // Get team details for this comp.
    $competition = competition_time::load_from_id($_POST['compid']);
    $teamdetails = $manager->get_current_teams($competition);
}

$competitions = $manager->get_all_competition_periods(false);
$hqusers = $manager->get_hq_members();

$header = new page_head('Team page');
?>

<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out() ?>
    <h1>Teams</h1>
    <form name="team-form" action="teams.php" method="POST">
        <div>Team name</div>
        <input type="text" name="teamname" />
        <div>Competition</div>
        <select name="competitionid">
            <?php
            foreach ($competitions as $competition) {
                echo '<option value="' . $competition->get_id() . '">' . $competition->get_title() . '</option>';
            }
            ?>
        </select>
        <div>Team members</div>
        <select multiple name="teammembers[]" size="10">
            <?php
            foreach ($hqusers as $user) {
                echo '<option value="' . $user->id . '">' . $user->displayname . '</option>';
            }
            ?>
        </select>
        <div>Fortress Name</div>
        <input type="text" name="fortressname" />
        <div>Hit points</div>
        <input type="text" name="hitpoints" />
        <br />
        <input type="submit" name="submit">
    </form>
    <form name="comp-form" action="teams.php" method="POST">
       <select name="compid">
            <?php
            foreach ($competitions as $competition) {
                echo '<option value="' . $competition->get_id() . '">' . $competition->get_title() . '</option>';
            }
            ?>
            <input type="submit" name="compsubmit" value="Go">
        </select>
    </form>
    <?php
        if (isset($teamdetails)) {
            $data = array_map(function($detail) {
                $params = $detail->get_all_params();
                $teammembers = array_map(function($user) {
                    return $user->displayname;
                }, $detail->get_team_members());
                $params['Team members'] = implode(', ', $teammembers);
                $params['Edit'] = '<a href="teams.php?teamid=' . $params['id'] . '">Edit</a>';
                unset($params['id']);
                unset($params['competition id']);
                return $params;
            }, $teamdetails);
            $table = new atable($data);
            echo $table->out();
        }
    ?>
</body>
</html>
