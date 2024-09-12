<?php
include "./connection/getConnection.php";
include "dbcreate.php";
session_start();
require_once 'dbcreate.php';

$con = getConnection();

// Seleccionar las últimas 10 acciones
$sql = "SELECT * FROM acciones ORDER BY actionID DESC LIMIT 10";
$stmt = $con->prepare($sql);
$stmt->execute();
$log = $stmt->fetchAll();

//Hacer el log más legible
foreach ($log as $key => $logs) {
    $sql = "SELECT nombre FROM users WHERE usuarioID = :uid";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':uid', $logs['usuarioID']);
    $stmt->execute();
    $uname = $stmt->fetch();
    $log[$key]['usuarioID'] = $uname['nombre'];
    $sql2 = "SELECT tipo, piso FROM dispositivo WHERE dispositivoID = :dispositivoID";
    $stmt2 = $con->prepare($sql2);
    $stmt2->bindParam(':dispositivoID', $logs['dispositivoID']);
    $stmt2->execute();
    $dispositivo = $stmt2->fetch();
    $log[$key]['dispositivoID'] = $dispositivo['tipo'] . " piso " . $dispositivo['piso'];
}

header('Content-Type: application/json');
echo json_encode($log);
?>
