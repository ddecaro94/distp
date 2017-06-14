<?php
    try {
		require("../force_https.php");
        force_https();
    } catch (Exception $exc) {
        http_response_code(500);
        header('Location: '.'/error-pages/500.html'); //https unreachable
        exit();
    }
?>

<?php

	require("validate_form_input.php");

	session_start();

    if (isset($_SESSION['CREATED'])) {
        if (time() - $_SESSION['CREATED'] > 30) {
        // session started more than 30 minutes ago
            session_destroy();
            header('Location: '.'/home?msg='.md5("8")); //inactivity too long
            exit();
        } else {
            $_SESSION['CREATED'] = time();
        }
    }
    if(!isset($_SESSION['username'])) {
		if (!check_email($_POST['username'])) {
			header('Location: '.'/home?msg='.md5("3")); //email not valid
			exit();
		}
		if (!check_password($_POST['password'])) {
			header('Location: '.'/home?msg='.md5("4")); //password not valid
			exit();
		}
        $inserted = register($_POST['username'], $_POST['password']);
		switch($inserted) {
		case -1:
			header('Location: '.'/home?msg='.md5("2")); //user existing
			break;
		case 1:
			header('Location: '.'/home?msg='.md5("1")); //user registered
			break;
		default:
			header('Location: '.'/home?msg='.md5("-1")); //generic error
			break;
		}
    } else {
		header('Location: '.'/home?msg='.md5("7")); //user already logged in
	}
?>

<?php
function register($user, $password)
{
    //returns current bid
    $mysqli = @mysqli_connect("localhost", "dist_prog", "dist_prog", "dist_prog");
    if (!$mysqli) {
        return;
    } else {
        if ($mysqli->connect_errno) return;
		$user = mysqli_real_escape_string($mysqli, $user);
    	$password = mysqli_real_escape_string($mysqli, $password);
		
		$insert = "INSERT INTO users (username, password) VALUES ('".$user."','".md5($password)."')"; //atomically insert new user
		$added = mysqli_query($mysqli, $insert);
		if ($added) {//returns false if not inserted
			return $mysqli->affected_rows; //returns 1 for success (username is unique)
		} else {
			return -1; //-1 for user already existing
		}
	}
	mysqli_close($mysqli);
    
    return;
}
?>
