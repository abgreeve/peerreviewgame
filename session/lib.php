<?php

include_once dirname(dirname(__FILE__)) . '/database/database.class.php';
include_once dirname(dirname(__FILE__)) . '/lib/generallib.php';


function user_check($username, $password) {
    $DB = new DB();
    $recordcount = $DB->count_records('users', array('username' => $username, 'password' => md5($password)));
    if ($recordcount == 1) {
    	return true;
    } else {
    	return false;
    }
}

function admin_check() {
    // No session.
    if (!isset($_SESSION['username'])) {
        url_redirect('index.php');
    }
    $username = $_SESSION['username'];
    $DB = new DB();
    $records = $DB->get_records('users', ['username' => $username]);
    $user = array_shift($records);
    if ($user->accesslevel != 1) {
        url_redirect('index.php');
    }
}

function user_cookie_check($username, $password) {
    $DB = new DB();
    $recordcount = $DB->count_records('users', array('email' => $username, 'password' => $password));
    if ($recordcount == 1) {
        return true;
    } else {
        return false;
    }
}

function create_session($username) {
	session_start();
	$_SESSION['username'] = $username;
}

function get_userid() {
    // session_start();
    if (!isset($_SESSION['username'])) {
        // Try the cookie for the username.
        if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
            // Check everything is valid.
            if (user_cookie_check($_COOKIE['username'], $_COOKIE['password'])) {
                $_SESSION['username'] = $_COOKIE['username'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    $username = $_SESSION['username'];
    $DB = new DB();
    $userid = $DB->get_fields('users', 'id', array('username' => $username));
    return $userid[0]->id;
}

function update_session($username) {
    $_SESSION['username'] = $username;
}

function create_login_cookie($username, $password) {
    setcookie('username', $username, time()+60*60*24*30);
    setcookie('password', md5($password), time()+60*60*24*30);
}

function destroy_cookies() {
    setcookie('username', '', time() - 3600);
    setcookie('password', '', time() - 3600);    
}
?>