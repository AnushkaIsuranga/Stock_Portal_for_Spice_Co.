<?php
    require 'config.php';

    //Returning to the login/home page
    $_SESSION = [];
    session_unset();
    session_destroy();
    header("Location: login.php");
?>