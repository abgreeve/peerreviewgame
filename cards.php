<?php
include 'lib/generallib.php';
include 'classes/card_manager.php';
include 'database/database.class.php';
include_once 'lib/navigation.php';

$navigation = new navigation('cards.php');
$database = new DB();
$manager = new card_manager($database);


if (isset($_POST['loadcards'])) {
    $manager->load_new_cards();
}

$cards = $manager->get_current_cards();

$table = new atable($cards);

$header = new page_head('Card page');
?>

<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out() ?>
    <h1>Cards</h1>
    <form name="load-cards" action="cards.php" method="POST">
    <button name="loadcards" value="loadcards">Load cards</button>
    </form>

    <?php echo $table->out(); ?>

</body>
</html>