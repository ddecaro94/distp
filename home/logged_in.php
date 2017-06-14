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
        echo '<i class="fa fa-user" aria-hidden="true" style="cursor:pointer" onclick="location.href='."'/home'".'"></i>'.$_SESSION['username'];
    else 
        echo '<i class="fa fa-lock" aria-hidden="true" style="cursor:pointer" onclick="document.getElementById('."'login-modal')".".style.display='block'".'"></i>Login';
?>