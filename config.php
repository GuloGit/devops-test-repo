<?php
    $db_host = 'db';
    $db_port = '3306';
    $db_name = 'test_zadanie';
    $db_user = 'root';
    $db_pass = 'root';

    try {
        $dbh = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
