<?php

function getConnection() { //Revisar a futuro

    try {
        $conn = new PDO("mysql:host=localhost;dbname=erronka0", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES 'utf8'");
        return $conn;
    } catch(PDOException $e) {
        echo "Errorea: " . $e->getMessage();
    }
}