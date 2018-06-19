<?php
include 'lib/generallib.php';
include 'classes/manager.php';
include 'lib/navigation.php';
include_once 'database/database.class.php';

$navigation = new navigation('timeedit.php');

$manager = new manager();

if (isset($_POST['title'])) {
    $title = $_POST['title'];
    $starttime = $_POST['starttime'];
    $endtime = $_POST['endtime'];
    $manager->save_completion_time($title, $starttime, $endtime, $_POST['id']);
}

$title = '';
$starttime = '';
$endtime = '';
$id = null;
if (isset($_GET['id'])) {
    // Fetch this record.
    $competitionrecord = competition_time::load_from_id($_GET['id']);
    $title = $competitionrecord->get_title();
    $starttime = $competitionrecord->get_starttime();
    $endtime = $competitionrecord->get_endtime();
    $id = $competitionrecord->get_id();
}

$timeperiods = $manager->get_all_competition_periods();
$table = new atable($timeperiods);
$header = new page_head('Competition time');
?>
<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out(); ?>
    <h1>Competition periods</h1>
    <h2>Add competition time</h2>
    <form name="time-form" action="timeedit.php" method="POST">
        <div>Title</div>
        <input type="text" name="title" value="<?php echo $title; ?>" />
        <div>Start time (day - month - year e.g. 01-01-2000): </div>
        <input type="text" name="starttime" value="<?php echo $starttime; ?>" />
        <div>Finish time(day - month - year e.g. 01-01-2000): </div>
        <input type="text" name="endtime" value="<?php echo $endtime; ?>" />
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="submit" name="submit">
    </form>
    <?php echo $table->out(); ?>
</body>
</html>