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
        $userid = login($_POST['username'], $_POST['password']);
        if($userid > 0) {
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['CREATED'] = time();
            header('Location: '.'/home?msg='.md5("0")); //user logged in
        }
        else {
            switch($userid) {
            case -1: //wrong pass
                header('Location: '.'/home?msg='.md5("6"));
                break;
            case -2: //no user
                header('Location: '.'/home?msg='.md5("5"));
                break;
            default:
                header('Location: '.'/home?msg='.md5("-1")); //geenric error
                break;
            }
        }
    } else {
        header('Location: '.'/home?msg='.md5("7")); //you are already logged in
    }
?>

<?php
function login($user, $password)
{
    //returns current bid
    $mysqli = @mysqli_connect("localhost", "dist_prog", "dist_prog", "dist_prog");
    if (!$mysqli) {
        return -3; //generic error
    } else {
        if ($mysqli->connect_errno) return -3;  //generic error
    }
    $user = mysqli_real_escape_string($mysqli, $user);
    $password = mysqli_real_escape_string($mysqli, $password);
    $sql = "SELECT t.user_id FROM users AS t WHERE username ='".$user."'";
    $user_exists = mysqli_query($mysqli, $sql);
    if ($user_exists->num_rows > 0) { //the user exists
        $sql = "SELECT t.user_id FROM users AS t WHERE username ='".$user."' and password ='".md5($password)."'";
        $login = mysqli_query($mysqli, $sql);
        $record = $login->fetch_assoc();
        if ($record['user_id'] != NULL)
            return $record['user_id'];
        else
            return -1; //error user/psw
    }
    else {
        return -2; //no user
    }
    mysqli_close($mysqli);
    return;
}
?>