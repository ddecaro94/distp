<?php
function force_https(){
	$uri = 'https://'. $_SERVER['HTTP_HOST'];
		if ($_SERVER['HTTPS'] != "on") {
			header("Location:".$uri.".");
			exit;
		}
	}
?>