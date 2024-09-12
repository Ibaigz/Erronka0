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
foreach ($logs as $log) {
	//Buscar todos los nombres para añadirlos al log
	$sql = "SELECT nombre FROM users WHERE usuarioID = :uid";
	$stmt = $con->prepare($sql);
	$stmt->bindParam(':uid', $log['usuarioID']);
	$stmt->execute();
	$uname = $stmt->fetch();
	//Buscar info de los dispositivos
	$sql2 = "SELECT tipo, piso FROM dispositivo WHERE dispositivoID = :dispositivoID";
	$stmt2 = $con->prepare($sql2);
	$stmt2->bindParam(':dispositivoID', $log['dispositivoID']);
	$stmt2->execute();
	$dispositivo = $stmt2->fetch();
	if ($log['accion'] == "encender" || $log['accion'] == "apagar") {
		$texto = "[" . $log['fecha'] . "] " . $uname['nombre'] . " ID: (" . $log['usuarioID'] . ") "  . $log['accion'] . " " . $dispositivo['tipo'] . " piso " . $dispositivo['piso'];
	}
	else if ($log['accion'] == "activar alarma" || $log['accion'] == "llamar emergencias") {
		$texto = "[" . $log['fecha'] . "] " . $uname['nombre'] . " ID: (" . $log['usuarioID'] . ") "  . $log['accion'];
	}
	fputcsv($fp, array($texto));
}
fclose($fp);
exit();
?>