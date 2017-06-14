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
    setcookie("test_cookie_enabled", "test"); //test for cookies enabled
?>

<?php
    session_start(); //start session when cookies are shown as enabled
    if (!isset($_COOKIE['test_cookie_enabled'])) {
        echo("<h2>Error, cookies not enabled. Try reloading the page.</h2><p>If the error persists, check your browser settings.</p>");
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
    
?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Programmazione Distribuita Homepage - 06/2017</title>
        <meta http-equiv="pragma" content="no-cache" />
        <link rel="stylesheet" type="text/css" href="/css/body.css">
        <link rel="stylesheet" type="text/css" href="/css/sidenav.css">
        <link rel="stylesheet" type="text/css" href="/css/modal.css">
        <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css">
        <script src="/scripts/sidenav.js"></script>
        <script src="/scripts/offers.js"></script>
        <script src="/scripts/modal.js"></script>

        <script>
            <!--
            function hide(id, time) {
                setTimeout(function () {
                    document.getElementById(id).style.display = 'none';
                }, time);
            }
            -->

        </script>
        <noscript>
            <b style="color: red">Your browser does not support JavaScript! The website will not work correctly.</b>
        </noscript>
    </head>

    <body>

        <div id="menu" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="/">Home</a>
            <a href="javascript:void(0)" onclick="document.getElementById('offer-modal').style.display='block'">Offer</a>

        </div>

        <div id="main">
            <div style="display: flex; flex-direction: row; align-content: space-between; justify-content: space-between">
                <div style="font-size:30px;cursor:pointer;" onclick="openNav()"><i class="fa fa-bars" aria-hidden="true"></i> Menu</div>
                <div style="display: flex; flex-direction: row; align-content: space-around; justify-content: space-around;">
                    <div class="login-button">
                        <?php require_once('logged_in.php');?>
                    </div>
                    <div class="login-button">
                        <?php require_once('registered.php');?>
                    </div>
                </div>
            </div>
        </div>


        <div id="object" style="width:100%; height:100%; display: flex; flex-direction: column; align-content: center;">
            <?php require_once("message.php");?>
            <img src="/img/object_on_sale.jpg" style="max-width:50%; max-height:40%; margin: auto;" onload="getCurrentHighestOffer(); getYourMaxOffer('<?php require_once("get_user.php");?>');">
            <div style="font-size:20px; align-self: center;">
                <i style="font-size:20px; align-self: center;">An amazing Indian Chief Vintage 2015, super low mileage, in the outstainding Willow Green/Cream tone.</i>
            </div>

            <span id="prices" style="display: flex; flex-direction: row; align-content: space-around; justify-content: space-around;"></span>
        </div>


        <div id="login-modal" class="modal">
            <form class="modal-content animate" action="/login/" method="POST">
                <div class="imgcontainer">
                    <span onclick="document.getElementById('login-modal').style.display='none'" class="close" title="Close Modal">&times;</span>
                </div>

                <div class="container">
                    <label><b>Username</b></label>
                    <input type="email" placeholder="Enter Username" name="username" required>

                    <label><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="password" pattern="^(?=.*\d)(?=.*[A-Za-z])(?!.*\s).*$" required>

                    <button type="submit">Login</button>

                </div>

                <div class="container" style="background-color:#f1f1f1">
                    <button type="button" onclick="document.getElementById('login-modal').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </form>
        </div>

        <div id="register-modal" class="modal">
            <form class="modal-content animate" action="/register/" method="POST">
                <div class="imgcontainer">
                    <span onclick="document.getElementById('register-modal').style.display='none'" class="close" title="Close Modal">&times;</span>
                </div>

                <div class="container">
                    <label><b>Username</b></label>
                    <input type="email" placeholder="Enter Username" name="username" required>

                    <label><b>Password<small style="color:gray"> (at least one letter and one number)</small></b></label>
                    <input type="password" placeholder="Enter Password" name="password" pattern="^(?=.*\d)(?=.*[A-Za-z])(?!.*\s).*$" required>

                    <button type="submit">Register</button>

                </div>

                <div class="container" style="background-color:#f1f1f1">
                    <button type="button" onclick="document.getElementById('register-modal').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </form>
        </div>

        <div id="offer-modal" class="modal">
            <form class="modal-content animate" action="/offers/" method="POST">
                <div class="imgcontainer">
                    <span onclick="document.getElementById('offer-modal').style.display='none'" class="close" title="Close Modal">&times;</span>
                </div>

                <div class="container">
                    <label><b>New Offer</b></label>
                    <input type="number" placeholder="Enter value greater than current offer" name="offer" min="1" value="1" step=".01" required>
                    <button type="submit">Send Offer</button>
                </div>

                <div class="container" style="background-color:#f1f1f1">
                    <button type="button" onclick="document.getElementById('offer-modal').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </form>
        </div>

    </body>

    </html>