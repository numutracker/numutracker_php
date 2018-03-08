<?php
	
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

session_start();

    require_once 'classes/database.php';
    $db = Database::getInstance();
    $mysqli = $db->getConnection(); 

$path_parts = explode('/', $_GET['path']);

if (isset($_GET['page'])) {
	if ($_GET['page'] != '') {
		$page = $mysqli->real_escape_string($_GET['page']_;
	}
}
if (!isset($page)) {
	$page = 1;
}
if ($page < 1) {
	$page = 1;
}

	require_once 'classes/release.php';
	require_once 'classes/artist.php';
	require_once 'classes/data.php';
	


	
if (isset($_SESSION['user_id'])) {
	$current_user_id = $_SESSION['user_id'];
	$current_user_email = $_SESSION['user_email'];

} else {
	$current_user_id = 0;
	$current_user_email = '';
}

if ($_GET['path'] == '') {
	require_once('home.php');
}

if ($_GET['path'] == 'about') {
	require_once('about.php');
}

if ($_GET['path'] == 'login') {
	require_once('login.php');
}

if ($_GET['path'] == 'logout') {
	require_once('logout.php');
}

if ($_GET['path'] == 'you') {
	require_once('you.php');
}

if ($_GET['path'] == 'you/stats') {
	require_once('you-stats.php');
}

if ($_GET['path'] == 'you/filters') {
	require_once('you-filters.php');
}

if ($_GET['path'] == 'releases/yours') {
	require_once('releases-yours.php');
}

if ($_GET['path'] == 'releases/all') {
	require_once('releases-all.php');
}

if ($_GET['path'] == 'releases/upcoming') {
	require_once('releases-upcoming.php');
}

if ($_GET['path'] == 'artists/yours') {
	require_once('artists-yours.php');
}

if ($_GET['path'] == 'artists/all') {
	require_once('artists-all.php');
}

if ($_GET['path'] == 'artists/search') {
	require_once('artists-search.php');
}

if ($_GET['path'] == 'artists') {
	require_once('artists-search.php');
}

if ($path_parts[0] == 'artist') {
	require_once('artist-single.php');
}

if ($path_parts[0] == 'release') {
	require_once('release-single.php');
}

if ($path_parts[0] == 'support') {
	require_once('support.php');
}



?>