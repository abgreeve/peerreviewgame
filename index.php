<?php
include 'lib/generallib.php';
include 'database/database.class.php';


$header = new page_head('Peer review fun page!');

?>

<html>
<?php echo $header->out(); ?>
<body>
    <h1>Peer reviewing</h1>
    <form name="login" action="login.php" method="POST">
        <div>username:</div>
        <input type="text" name="username" />
        <div>password:</div>
        <input type="password" name="password" />
        <input type="submit" name="submit" value="Login">
    </form>
</body>
</html>