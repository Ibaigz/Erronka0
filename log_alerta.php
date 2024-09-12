<?php
require_once 'connection/getConnection.php';

try {
    // Establecer conexión y comprobar
    $conn = getConnection();
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}

if(isset($_POST['uid'])) {
    // Preparar datos para introducir
    $uid = $_POST['uid'];
    $mensaje = "ha activado la alarma";
    // La ID de alarma es 8, pero por si cambia meto query
    $sql_alarma = "SELECT dispositivoID FROM dispositivo WHERE tipo = 'alarma'";
    $result = $conn->query($sql_alarma);
    $row = $result->fetch();
    $dispositivoID = $row['dispositivoID'];
    $query = "INSERT INTO acciones (usuarioID, dispositivoID, accion) VALUES (:uid, :dispositivoID, :mensaje)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':uid', $uid);
    $stmt->bindParam(':dispositivoID', $dispositivoID);
    $stmt->bindParam(':mensaje', $mensaje);
    $stmt->execute();
	//Ahora tenemos que apagar todos los dispositivos encendidos
	$sql = "SELECT dispositivoID FROM dispositivo WHERE estado = 1 AND tipo != 'alarma' AND tipo != 'telefono'";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$dispositivos = $stmt->fetchAll();
	foreach ($dispositivos as $dispositivo) {
		$sql = "UPDATE dispositivo SET estado = 0 WHERE dispositivoID = :dispositivoID";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':dispositivoID', $dispositivo['dispositivoID']);
		$stmt->execute();
		$sql = "INSERT INTO acciones (usuarioID, dispositivoID, accion) VALUES (:uid, :dispositivoID, 'apagado')";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':uid', $uid);
		$stmt->bindParam(':dispositivoID', $dispositivo['dispositivoID']);
		$stmt->execute();
	}
    $stmt = null;
    $conn = null;
} else {
    echo "Error: No se recibieron todos los datos necesarios.";
}
?>