<?php

ob_start();
require_once 'login.php';
ob_end_clean();

$con = getConnection();

$sql = "SELECT * FROM dispositivo";
$stmt = $con->prepare($sql);
$stmt->execute();
$todos = $stmt->fetchAll();

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
}
?>

<?php include 'header.php'; ?>

<html>
	<head>
		<title>Erronka 0 - Index</title>
	</head>
	<body>
		<h1>Erronka 0 - Index</h1>
		<?php echo "Kaixo"; ?>
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
	</body>
</html>