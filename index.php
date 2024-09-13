<?php
session_start();
if (!isset($_SESSION['userID'])){
	header('Location: login.php');
	exit;
}
require_once 'dbcreate.php';
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
			<form method="post" action="javascript:downloadLogs()">
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

	<script>
		function downloadLogs() {
     		fetch('log_download.php')
        	.then(response => response.blob())
        	.then(blob => {
          		const url = window.URL.createObjectURL(blob);
          		const a = document.createElement('a');
          		a.style.display = 'none';
          		a.href = url;
          		a.download = 'logs.txt';
          		document.body.appendChild(a);
          		a.click();
          		window.URL.revokeObjectURL(url);
        	})
        	.catch(error => console.error('Error al descargar los logs:', error));
    }
	</script>

</html>