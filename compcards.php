<?php
// include 'jira-class.php';
include 'lib/generallib.php';
include 'classes/competition_time.php';
include 'classes/card_manager.php';
include 'database/database.class.php';

// $manager = new manager();
$database = new DB();
$cardmanager = new card_manager($database);

if (isset($_GET['id'])) {
    $competition = competition_time::load_from_id($_GET['id']);
    // print_object($competition);
} else if (isset($_POST['competitionid'])) {
    print_object($_POST);
    $cardmanager->add_active_cards($_POST['competitionid'], $_POST['cards']);
    $competition = competition_time::load_from_id($_POST['competitionid']);
} else {
    // Perhaps redirect back to management page.

}

$cards = $cardmanager->get_current_cards();

$alteredcards = array_map(function($card) {
    $card['Select'] = '<input type="checkbox" name="cards[]" value="'. $card['id'] .'" />';
    unset($card['id']);
    return $card;
}, $cards);
// print_object($alteredcards);

$table = new atable($alteredcards);

$header = new page_head('Cards for the competition');
?>

<html>
<?php echo $header->out(); ?>
<body>
    <?php include 'lib/navigation.php' ?>
    <h1>Card details -- <?php echo $competition->get_title();  ?></h1>

    <form action="compcards.php" method="POST">
        <?php echo $table->out(); ?>
        <input type="hidden" name="competitionid" value="<?php echo $competition->get_id(); ?>" />
        <input type="submit" name="submit" value="Save" />
    </form>

</body>
</html>