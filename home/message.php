<?php
    try {
        force_https();
    } catch (Exception $exc) {
        http_response_code(500);
        header('Location: '.'/error-pages/500.html'); //https unreachable
        exit();
    }
?>

<?php
if(isset($_GET['msg'])){
    switch ($_GET['msg']) {
        case md5("0"):
            if(isset($_SESSION['username']))
                $text = 'Welcome, '. $_SESSION['username'];
            else
                $text = 'Wooops... an error occurred :(';
            $color = 'black';
            break;
        case md5("1"):
            $text = 'Registration successful! Please login with your credentials.';
            $color = 'green';
            break;
        case md5("2"):
            $text = 'Unable to sign in, username in use.';
            $color = 'blue';
            break;
        case md5("3"):
            $text = 'Unable to sign in, email not valid.';
            $color = 'blue';
            break;
        case md5("4"):
            $text = 'Unable to sign in, password not valid.';
            $color = 'blue';
            break;
        case md5("5"):
            $text = 'Login unsuccessful... Username does not exist.';
            $color = 'red';
            break;
        case md5("6"):
            $text = 'Login unsuccessful... Wrong username/password.';
            $color = 'red';
            break;
        case md5("7"):
            $text = 'User already logged in.';
            $color = 'green';
            break;
        case md5("8"):
            $text = 'Inactivity too long, please repeat login.';
            $color = 'blue';
            break;
        case md5("9"):
            $text = 'Unable to post new offer, user not logged in!';
            $color = 'red';
            break;
        case md5("10"):
            $text = 'Unable to post new offer, smaller than current bid.';
            $color = 'orange';
            break;
        case md5("11"):
            $text = 'An error has occurred, unable to post your offer...';
            $color = 'red';
            break;
        case md5("12"):
            $text = 'New offer successfully sent!';
            $color = 'green';
            break;
        default:
            $text = 'Wooops... an error occurred :(';
            $color = 'black';
            break;
    }
    
    //echo '<p id="msg" style="font-size:30px; align-self: center; color: '.$color.'" onload="'.$disappear_message.'">'.$text.'</p>';
    echo '<div style="font-size:20px; align-self: center;"><p id="msg" style="align-self: center; color: '.$color.'" >'.$text.'</p></div>';
    echo '<script>var time = 3000; hide("msg", time)</script>';
}
?>