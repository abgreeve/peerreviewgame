<?php
include_once 'lib/generallib.php';
include_once 'session/lib.php';

if (isset($_POST['username'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];

	// $stayloggedin = null;
	// if (isset($_POST['stayloggedin'])) {
	// 	$stayloggedin = $_POST['stayloggedin'];
	// }

	// Sanitise username and password as this is where bad people try to get in.
	$username = strip_tags($username);
	$password = strip_tags($password);

	$authenticated = user_check($username, $password);
	if ($authenticated) {
		create_session($username);
		// if ($stayloggedin) {
		// 	create_login_cookie($username, $password);
		// }
		url_redirect('testpage.php');
	} else {
		url_redirect('index.php?e=1');
		// echo 'not authenticated';
	}
}

$header = new page_head('Peer review fun page!');

?>

<html>
<?php echo $header->out(); ?>
<body>
<h1>Login</h1>
    <form name="login" action="login.php" method="POST">
        <div>username:</div>
        <input type="text" name="username" />
        <div>password:</div>
        <input type="password" name="password" />
        <input type="submit" name="submit" value="Login">
    </form>
</body>
</html>