<?php
    //Database Configuration
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'spiceco';

    //Create Connection
    $conn = new mysqli($host, $username, $password, $database);

    //Connection error display
    if($conn -> connect_error){
        die("connection Failed." . $mysqli -> connect_error);
    }

    return $conn;
