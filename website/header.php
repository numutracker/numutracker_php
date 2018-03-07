<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<meta name="viewport" content="width=device-width" />
    <title><?php echo $page_title; ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel='stylesheet' href='/web/desktop.css' type="text/css">
</head>

<body>
	
<div id="menu">
	<div id="menu_items">
		<a href="/" class="<?php if ($page_name == 'index') { echo "active"; }?>">numu</a>
		<?php if ($current_user_id > 0) { ?>
		<a href="/releases/yours" class="<?php if ($page_name == 'releases') { echo "active"; }?>">Releases</a>
		<a href="/artists/yours" class="<?php if ($page_name == 'artists') { echo "active"; }?>">Artists</a>
		<?php } else { ?>
		<a href="/releases/all" class="<?php if ($page_name == 'releases') { echo "active"; }?>">Releases</a>
		<a href="/artists/all" class="<?php if ($page_name == 'artists') { echo "active"; }?>">Artists</a>
		<?php } ?>
		<?php if ($current_user_id > 0) { ?><a href="/you/stats"  class="<?php if ($page_name == 'you') { echo "active"; }?>">You</a><?php } ?>
		<?php if ($current_user_id == 0) { ?><a href="/login"  class="<?php if ($page_name == 'login') { echo "active"; }?>">Login</a><?php } ?>
	</div>
</div>

<div id="sub_menu">
	<div id="sub_menu_items">
		<?php if ($page_name == 'releases') { ?>
			<a href="/releases/all" class="<?php if ($sub_page_name == 'releases_all') { echo "active"; }?>">All</a>
			<?php if ($current_user_id > 0) { ?> <a href="/releases/yours" class="<?php if ($sub_page_name == 'releases_yours') { echo "active"; }?>">Yours</a>
			<a href="/releases/upcoming" class="<?php if ($sub_page_name == 'releases_upcoming') { echo "active"; }?>">Your Upcoming</a><?php } else { ?>
			<a href="/releases/upcoming" class="<?php if ($sub_page_name == 'releases_upcoming') { echo "active"; }?>">Upcoming</a>
			<?php } ?>
		<?php } ?>
		<?php if ($page_name == 'artists') { ?>
			<a href="/artists/all" class="<?php if ($sub_page_name == 'artists_all') { echo "active"; }?>">All</a>
			<?php if ($current_user_id > 0) { ?><a href="/artists/yours" class="<?php if ($sub_page_name == 'artists_yours') { echo "active"; }?>">Yours</a><?php } ?>
			<a href="/artists/search" class="<?php if ($sub_page_name == 'artists_search') { echo "active"; }?>">Search</a>
		<?php } ?>
		<?php if ($page_name == 'index') { ?>
			<a href="/" class="<?php if ($sub_page_name == 'recent') { echo "active"; }?>">Home</a>
			<a href="/about" class="<?php if ($sub_page_name == 'about') { echo "active"; }?>">About Numu</a>
		<?php } ?>
		<?php if ($page_name == 'you') { ?>
			<a href="/you/stats" class="<?php if ($sub_page_name == 'stats') { echo "active"; }?>">Account</a>
			<a href="/you/filters" class="<?php if ($sub_page_name == 'filters') { echo "active"; }?>">Settings</a>
			<a href="/logout">Log Out</a>
		<?php } ?>
	</div>
</div>

<div id="main">
	<div id="content">