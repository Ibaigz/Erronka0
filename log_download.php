<?php
require_once 'connection/getConnection.php';

$con = getConnection();
$sql = "SELECT * FROM acciones";
$stmt = $con->prepare($sql);
$stmt->execute();
$logs = $stmt->fetchAll();
$filename = "logs.csv";
$fp = fopen('php://output', 'w');
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);
$query_nombre = "SELECT nombre FROM users";
$stmt = $con->prepare($query_nombre);
$stmt->execute();
$nombre = $stmt->fetchAll();
$query_dispositivo = "SELECT tipo, piso FROM dispositivo";
$stmt = $con->prepare($query_dispositivo);
$stmt->execute();
$dispositivo = $stmt->fetchAll();
foreach ($logs as $log) {
	if ($dispositivo[$log['dispositivoID'] - 1]['tipo'] != "Alarma") {
	$texto = "[" . $log['fecha'] . "] - " . $nombre[$log['usuarioID'] - 1]['nombre'] . " ID: (" . $log['usuarioID'] . ") - " . $log['accion'] . " " . $dispositivo[$log['dispositivoID'] - 1]['tipo'] . " piso " . $dispositivo[$log['dispositivoID'] - 1]['piso'];
	}else{
		$texto = "[" . $log['fecha'] . "] - " . $nombre[$log['usuarioID'] - 1]['nombre'] . " ID: (" . $log['usuarioID'] . ") - " . $log['accion'] . " " . $dispositivo[$log['dispositivoID'] - 1]['tipo'];
	}
	fputcsv($fp, array($texto));
}
fclose($fp);
exit();
?>