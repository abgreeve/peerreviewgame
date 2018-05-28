<?php
require_once 'lib.php';

session_start();

if (!isset($_SESSION['username'])) {
	if (isset($_COOKIE['username'])) {
		$_SESSION['username'] = $_COOKIE['username'];
	} else {
		url_redirect('login.php');
	}
}

?>