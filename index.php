<?php
session_start();
require_once 'dbcreate.php';

$con = getConnection();

// Seleccionar todos los dispositivos
$sql = "SELECT * FROM dispositivo";
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
			$texto = "[" . $log['fecha'] . "] " . $uname['nombre'] . " ID: (" . $log['usuarioID'] . ") "  . $log['accion'] . " " . $dispositivo['tipo'] . " piso " . $dispositivo['piso'];
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
		<title>Erronka 0 - Index</title>
	</head>
	<body>
		<h1>Erronka 0 - Index</h1>
		<p>Dispositivos:</p>
		<?php foreach ($todos as $dispositivo) : ?>
			<p><?php echo $dispositivo['tipo'] . " piso: " . $dispositivo['piso'] . " - Estado: " . $dispositivo['estado']; ?></p>
			<?php if (isset($_SESSION['uname'])) : ?>
				<form action="index.php" method="post">
					<input type="hidden" name="id" value="<?php echo $dispositivo['dispositivoID']; ?>">
					<input type="hidden" name="uname" value="<?php echo $_SESSION['uname']; ?>">
					<input type="hidden" name="uid" value="<?php echo $_SESSION['userID']; ?>">
					<input type="hidden" name="estado" value="<?php echo $dispositivo['estado']; ?>">
					<input type="submit" name="toggle" value="Toggle">
				</form>
			<?php endif; ?>
		<?php endforeach; ?>
		<p>Últimas 10 acciones:</p>
		<?php foreach ($log as $logs) : ?>
			<p><?php echo "[" . $logs['fecha'] . "] " . $logs['usuarioID'] . " " . $logs['accion'] . " " . $logs['dispositivoID']; ?></p>
		<?php endforeach; ?>
		<form method="post" action="index.php">
			<input type="submit" name="Download" value="Download">
		</form>
	</body>
</html>