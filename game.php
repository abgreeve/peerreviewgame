<?php

include 'lib/generallib.php';
include 'classes/manager.php';
include 'classes/card_manager.php';
include 'database/database.class.php';
include 'lib/navigation.php';

$navigation = new navigation('game.php');
$manager = new manager();
// Get competition window.
$competition = $manager->get_active_competition_time();
$teams = $manager->get_current_teams($competition);

// Team information - Fortress name, HP.

$header = new page_head('Game page');
?>

<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out(); ?>
    <h1>Game stuff is here!</h1>

    <h2><?php echo $competition->get_title(); ?></h2>

    <div class="card" style="width: 18rem;">
        <div class="card-header">
            <?php echo $teams[0]->get_fortress_name(); ?>
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted"><?php echo $teams[0]->get_team_name(); ?></h6>
            <p class="card-text">Current hit points: <?php echo $teams[0]->get_hp(); ?></p>
        </div>
    </div>
    <br />
    <div class="card" style="width: 18rem;">
        <div class="card-header">
            <?php echo $teams[1]->get_fortress_name(); ?>
        </div>
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted"><?php echo $teams[1]->get_team_name(); ?></h6>
            <p class="card-text">Current hit points: <?php echo $teams[1]->get_hp(); ?></p>
        </div>
    </div>

</body>
</html>