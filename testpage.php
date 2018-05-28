<?php
include 'lib/generallib.php';
include 'session/sessioncheck.php';
include 'lib/navigation.php';
include_once 'database/database.class.php';

$navigation = new navigation('testpage.php');

$header = new page_head('Test page');

?>

<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out(); ?>
    <h1>We're in!</h1>
</body>
</html>