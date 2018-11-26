<?php
// include 'jira-class.php';
include 'lib/generallib.php';
include 'session/sessioncheck.php';
include 'lib/navigation.php';
// include 'classes/manager.php';
include 'classes/user.php';
// include 'classes/card_manager.php';
// include 'classes/user_manager.php';
// include_once 'classes/competition_time.php';
include_once 'database/database.class.php';

$navigation = new navigation('mydetails.php');
$database = new DB();

$userid = get_userid();
$user = user::load_from_id($userid, $database);

print_object($user);

$header = new page_head('My details');
?>
<html>
<?php echo $header->out(); ?>
<body>
    <?php echo $navigation->out() ?>
    <h1>My details</h1>

    <form>
        <div>Current password</div>
        <input type="password" name="currentpassword" />
        <div>New password</div>
        <input type="password" name="newpassword" />
        <input type="submit" value="Save">
    </form>

</body>
</html>