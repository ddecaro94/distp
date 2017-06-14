<?php
    function check_email($username){
        if(filter_var($username, FILTER_VALIDATE_EMAIL)){
            return 1;
        } else return 0;
    }
    function check_password($psw){
        $regex = "/^(?=.*\d)(?=.*[A-Za-z])(?!.*\s).*$/";
        return preg_match($regex, $psw);
    }
?>