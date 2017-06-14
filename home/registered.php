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
    if(isset($_SESSION['username'])) 
        echo '<i class="fa fa-reply" aria-hidden="true" style="cursor:pointer" onclick="location.href='."'/logout'".'"></i>'.'Logout';
    else 
        echo '<i class="fa fa-sign-in" aria-hidden="true" style="cursor:pointer" onclick="document.getElementById('."'register-modal')".".style.display='block'".'"></i>Sign In';
?>