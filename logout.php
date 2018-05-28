<?php
include_once 'session/lib.php';
include_once 'lib/generallib.php';


// unset($_SESSION['username']);
session_start();
session_destroy();
destroy_cookies();
url_redirect('index.php');
// header('Location: /mylist/index.php');

?>