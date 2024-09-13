<?php
session_start();
if (!isset($_SESSION['userID'])){
	header('Location: login.php');
	exit;
}
require_once 'dbcreate.php';

$con = getConnection();

// Seleccionar todos los dispositivos
$sql = "SELECT * FROM dispositivo WHERE tipo != 'Alarma' AND tipo != 'Telefono'";
$stmt = $con->prepare($sql);
$stmt->execute();
$todos = $stmt->fetchAll();
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


// POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['toggle'])) {
		$dispositivoID = $_POST['id'];
		$username = $_POST['uname'];
		$uid = $_SESSION['userID'];
		$estado = $_POST['estado'];
		if ($estado == 0) {
			$accion = "encender";
		} else {
			$accion = "apagar";
		}
		$sql = "UPDATE dispositivo SET estado = !estado WHERE dispositivoID = :dispositivoID";
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':dispositivoID', $dispositivoID);
		$stmt->execute();
		$sql2 = "INSERT INTO acciones (usuarioID, dispositivoID, accion) VALUES (:uid, :dispositivoID, :accion)";
		$stmt2 = $con->prepare($sql2);
		$stmt2->bindParam(':uid', $uid);
		$stmt2->bindParam(':dispositivoID', $dispositivoID);
		$stmt2->bindParam(':accion', $accion);
		$stmt2->execute();
		header("Location: index.php");
		exit();
	}
	else if (isset($_POST['Download'])) {
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
	}
}
?>

<?php include 'header.php'; ?>

<html>
	<head>
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" type="text/css" href="header.css">
		<title>Erronka 0 - Index</title>
	</head>
	<body>
		<h1>Erronka 0 - Index</h1>

	<div class="w3-half">
		<div class="w3-button w3-white w3-ripple"  onclick="window.location.href='plano1.php'">
  				<img src="./media/planoD.png" alt="Alps">
  			<div class="w3-container w3-center">
    			<p>Acceder al Plano 1</p>
  			</div>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-button w3-white w3-ripple" onclick="window.location.href='plano2.php'">
  				<img src="./media/planoD.png" alt="Alps">
  			<div class="w3-container w3-center">
    			<p>Acceder al Plano 2</p>
  			</div>
		</div>
	</div>
	
	<footer class="footer">
        <div class="footer-section">
            <h4>Descargar Logs:</h4>
            <p>Haz clic en el botón para descargar los logs del sistema.</p>
			<form method="post" action="index.php">
				<input type="submit" name="Download" value="LOGS" class="download-btn">
			</form>
        </div>
        <div class="footer-section">
            <h4>Contacto:</h4>
            <p>Email: idazkaria@fpTXurdinaga.com</p>
        </div>
		<div class="footer-section">
			<img src="./media/Logo.png" alt="">
		</div>
    </footer>
	</body>

</html>