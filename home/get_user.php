<?php
    try {
        force_https();
    } catch (Exception $exc) {
        http_response_code(500);
        header('Location: '.'/error-pages/500.html'); //https unreachable
        exit();
    }

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
    if (isset($_SESSION['username'])) {
        echo $_SESSION['username'];
    } else echo "";
?>