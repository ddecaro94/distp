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
    switch ($_SERVER['REQUEST_METHOD']) {
    case "GET": //get
        if((count($_GET) > 0) && !isset($_GET['THR'])) {
            http_response_code(400);
            exit();
        }
        if(isset($_GET['THR'])) {
            if(isset($_SESSION['username'])) {
                $user = $_SESSION['username'];
                $res = get_thr($user);
                $highest = get_current_bid();
                if ($res != -2) {
                    echo '{"user":"'.$user.'", "amount":'.$res.', "highestBidder":"'.$highest['username'].'"}';
                }
            } else {
                http_response_code(403);
                exit();
            }
        }
        else {
            $user = '';
            $res = get_current_bid($user);
            if ($res == -1) {
                http_response_code(500);
            } else {
                echo '{"user":"'.$res['username'].'", "amount":'.$res['amount'].'}';
            }
        }
        break;
    case "POST":
        if(!isset($_SESSION['username'])) {
            header('Location: '.'/home?msg='.md5("9")); //unable to post offer, not logged in
            exit();
        } else {
            if(!isset($_POST['offer'])) {
                header('Location: '.'/home?msg='.md5("-1")); //generic error
                exit();
            } else {
                //handle new offer;
                $res = update_thr($_POST['offer']);
                switch($res){
                case -2: 
                    header('Location: '.'/home?msg='.md5("11")); //error updating thr
                    break;                   
                case -1: 
                    header('Location: '.'/home?msg='.md5("-1")); //generic error
                    break;
                case 0: 
                    header('Location: '.'/home?msg='.md5("10")); //new offer smaller than bid
                    break;
                case 1: 
                    header('Location: '.'/home?msg='.md5("12")); //new offer sent, return to home and update values through ajax
                    break;
                default:
                    header('Location: '.'/home?msg='.md5("-1")); //generic error
                    break;
                }
            }
        }
        break;
    default:
        http_response_code(405); //method not allowed
        header('Location: '.'/error-pages/405.html');
    }
?>
<?php
function get_current_bid()
{
    //returns current bid
    $mysqli = @mysqli_connect("localhost", "dist_prog", "dist_prog", "dist_prog");
    if (!$mysqli) {
        return -1;
    } else {
        if ($mysqli->connect_errno) return -1;
    }
    $sql = "SELECT t.user_id, t.amount FROM current_bid AS t";
    $query = mysqli_query($mysqli, $sql);
    if ($query) { //the query was executed
        $record = $query->fetch_assoc();
        if ($record['user_id'] != NULL) {
            $sql = "SELECT t.username FROM users AS t WHERE user_id ='".$record['user_id']."'";
            $query = mysqli_query($mysqli, $sql);
            $user_record = $query->fetch_assoc();
            if ($query && isset($user_record['username']))
                $record['username'] = $user_record['username'];
            else
                $record['username'] = "";
        }
            
    }
    else {
        $err = mysqli_error($mysqli);
        return -1;
    }
    mysqli_close($mysqli);
    return $record;
}
?>
<?php
function get_thr($user)
{
    //returns current bid
    $mysqli = @mysqli_connect("localhost", "dist_prog", "dist_prog", "dist_prog");
    if (!$mysqli) {
        return -2;
    } else {
        if ($mysqli->connect_errno) return -1;
    }
    $_SESSION['username'] = mysqli_real_escape_string($mysqli, $_SESSION['username']);
    $sql = "SELECT t.thr FROM thr_users AS t WHERE t.user_id = (SELECT user_id FROM users WHERE username ='".$_SESSION['username']."')";
    $query = mysqli_query($mysqli, $sql);
    if ($query) { //the query was executed
        $record = $query->fetch_assoc();
        mysqli_close($mysqli);
        if(isset($record['thr']))
            return $record['thr'];
        else
            return -1;
    }
    else {
        mysqli_close($mysqli);
        return -2;
    }
}
?>
<?php
function update_thr($new_amount)
{
    mysqli_report(MYSQLI_REPORT_STRICT); // Traps all mysqli error and throws exceptions
    try {
        $mysqli = @mysqli_connect("localhost", "dist_prog", "dist_prog", "dist_prog");

        $_SESSION['username'] = mysqli_real_escape_string($mysqli, $_SESSION['username']);
        $new_amount = mysqli_real_escape_string($mysqli, $new_amount);
    
        try {
            mysqli_query($mysqli, "START TRANSACTION;"); //begin critical section

            $sql = "SELECT t.user_id, t.amount FROM current_bid AS t FOR UPDATE"; //lock every other transaction on bid table
            $query = mysqli_query($mysqli, $sql);
            
            $record = $query->fetch_assoc();
            if(isset($record['amount'])) {//current bid is existing
                if($new_amount > $record['amount']){ //new offer is valid
                    $sql = "UPDATE thr_users AS t SET t.thr = ".$new_amount." WHERE t.user_id = (SELECT user_id FROM users WHERE username ='".$_SESSION['username']."')";
                    $update = mysqli_query($mysqli, $sql);
                    if (!$mysqli->affected_rows) { //thr_i updated, now update bid
                        mysqli_close($mysqli);
                        mysqli_report(MYSQLI_REPORT_OFF);
                        return -1; //error, offering as much as current thr
                    }
                } else {
                    mysqli_close($mysqli);
                    mysqli_report(MYSQLI_REPORT_OFF);
                    return 0; //new offer smaller than bid
                }
            }
        } catch(Exception $not_exceeded) {
            mysqli_close($mysqli);
            mysqli_report(MYSQLI_REPORT_OFF);
            return -2; //error updating thr
        }
                    
        //mysqli_query($mysqli, "START TRANSACTION;"); //transaction used to update current bid
        $sql = "SELECT t.user_id, t.thr, t.tms_created, r.amount FROM thr_users AS t, current_bid as r WHERE t.thr = (SELECT MAX(thr) FROM thr_users) ORDER BY TMS_CREATED ASC FOR UPDATE";//cartesian query to lock current bid value
        $result_set = mysqli_query($mysqli, $sql); //select resultset of user ids having max thr

        if($result_set->num_rows > 1){//more than one user found, choose the first because offered first
            $winner_user = $result_set->fetch_assoc();
            $sql = "UPDATE current_bid AS t SET t.user_id = ".$winner_user['user_id'].", t.amount = ".$winner_user['thr'];
        } else { //found just one row, set bid as maximum of others thr plus 0.1
            $winner_user = $result_set->fetch_assoc();
            $sql = "UPDATE current_bid AS t SET t.user_id = ".$winner_user['user_id'].", t.amount = IF(((SELECT MAX(r.thr) FROM thr_users AS r WHERE r.user_id <>".$winner_user['user_id'].") > 1), ((SELECT MAX(r.thr) FROM thr_users AS r WHERE r.user_id <>".$winner_user['user_id'].")  + 0.01), t.amount) ";/////CORREGGERE - SE IL MASSIMO DEGLI ALTRI E' 0 SETTA 0.01
        }
        $bid_update = mysqli_query($mysqli, $sql); //execute update
        if($bid_update){
            mysqli_query($mysqli, "COMMIT;");
            mysqli_close($mysqli);
            mysqli_report(MYSQLI_REPORT_OFF);
            return 1; //posted
        }
    } catch(Exception $e) {
        mysqli_close($mysqli);
        mysqli_report(MYSQLI_REPORT_OFF);
        return -1;
    }
    mysqli_close($mysqli);
    mysqli_report(MYSQLI_REPORT_OFF);
    return -1;

}
?>
