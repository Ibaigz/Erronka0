<?php
session_start();

if (isset($_SESSION['uname'])) {
	header("Location: index.php");
}

function getConnection() { //Revisar a futuro
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "erronka0";
    $conn = null;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES 'utf8'");
        return $conn;
    } catch(PDOException $e) {
        echo "Errorea: " . $e->getMessage();
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $uname = htmlspecialchars($_POST['uname']);
        $pass = htmlspecialchars($_POST['pass']);
        $conn = getConnection();
		
        if ($conn == null) {
            echo "<script>alert('Errorea datu-basearekin konexioa sortzean')</script>";
        }

        // Preparar y ejecutar consulta preparada
        $sql = "SELECT * FROM users WHERE username = :uname AND password = :pass"; //Revisar nombre tabla
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':uname', $uname);
		$stmt->bindParam(':pass', $pass);
        $stmt->execute();

        // Si se encuentra un usuario, verificar la contraseña
        if ($stmt->rowCount() == 1) {
			$_SESSION['uname'] = $uname;
			header("Location: index.php");
		} else {
			echo "<script>alert('Erabiltzailea ez da existitzen')</script>";
		}
    }
}
?>

<html>
    <head>
        <title>Erronka 0 - Login</title>
    </head>
    <body>
        <h1>Erronka 0 - Login</h1>
        <form action="login.php" method="post">
            <label for="uname">Erabiltzailea:</label>
            <input type="text" id="uname" name="uname" required><br><br>
            <label for="pass">Pasahitza:</label>
            <input type="password" id="pass" name="pass" required><br><br>
            <input type="submit" name="login" value="Sartu">
        </form>
		<?php if (isset($_SESSION['uname'])): ?>
			<a href="index.php">Atzera</a>
		<?php endif; ?>
    </body>
</html>
