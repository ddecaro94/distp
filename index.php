<?php
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'https://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	setcookie("test_cookie_enabled", "test"); //test for cookies enabled
	header('Location: '.$uri.'/home');
	exit;	
?>
Something is wrong with the XAMPP installation :-(
