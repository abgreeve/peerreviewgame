<?php

include 'lib/generallib.php';
include 'classes/manager.php';
include 'classes/card_manager.php';
include 'database/database.class.php';
include 'lib/navigation.php';

include_once 'classes/competition_time.php';

$navigation = new navigation('game.php');
$database = new DB();
$manager = new manager($database);
// Get competition window.
$competition = $manager->get_active_competition_time();
$competitiontitle = !empty($competition) ? $competition->get_title() : 'No competition';
$teams = $manager->get_current_teams($competition);

// Don't leave this here.
function print_team_card($team) {
    $html = '<div class="card" style="width: 18rem;">';
    $html .= '<div class="card-header">';
    $html .= $team->get_fortress_name();
    $html .= '</div>';
    $html .= '<div class="card-body">';
    $html .= '<h6 class="card-subtitle mb-2 text-muted">' . $team->get_team_name() . '</h6>';
    $html .= '<p class="card-text">Current hit points: ' . $team->get_hp() . '</p>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<br />';
    echo $html;
}

// Team information - Fortress name, HP.

$header = new page_head('Game page');
?>

<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out(); ?>
    <h1>Game stuff is here!</h1>

    <h2><?php echo $competitiontitle; ?></h2>

    <?php
        if (empty($teams)) {
            echo '<p>There are no teams set for this competition period.</p>';
        } else {
            foreach ($teams as $team) {
                print_team_card($team);
            }
        }
    ?>

</body>
</html>