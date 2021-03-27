<?php
    try {
        $connection = new PDO('mysql:host=localhost;dbname=airlinedb', "root", "");
    } catch (PDOException $e) {
        print "Error!: Could not connect to database. Please try reloading the page <br/>";
        die();
    }
?>