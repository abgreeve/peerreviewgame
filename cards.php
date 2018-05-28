<?php
include 'lib/generallib.php';
include 'classes/card_manager.php';
include 'database/database.class.php';

$manager = new card_manager();


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
    <?php include 'lib/navigation.php' ?>
    <h1>Cards</h1>
    <form name="load-cards" action="cards.php" method="POST">
    <button name="loadcards" value="loadcards">Load cards</button>
    </form>

    <?php echo $table->out(); ?>

</body>
</html>