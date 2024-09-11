<?php
include "./connection/getConnection.php";
include "dbcreate.php";
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

?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./plano1.css" />
  <title>Document</title>
</head>

<body>
  <header>MENU</header>
  <main>

    <div class="container">
      <section id="section1">

        <img
          id="<?php echo $todos[0]['estado'] == 0 ? "planoOff" : "planoOn" ?>"
          class="plano"
          src="./media/Plano1_Off.jpg"
          alt="" />
        <img
          id="<?php echo $todos[0]['estado'] == 0 ? "planoOn" : "planoOff" ?>"
          class="plano"
          src="./media/Plano1_On.jpg"
          alt="" />


        <div class="logTitle">
          Historial de Logs

          <div class="logText">
            <?php foreach ($log as $logs) : ?>
              <?php echo "> [" . $logs['fecha'] . "] " . $logs['usuarioID'] . " " . $logs['accion'] . " " . $logs['dispositivoID'] . "<br>";  ?>
            <?php endforeach; ?>
          </div>
        </div>



      </section>
      <section id="s2">
        <div class="fondoP">
          <h1>PANEL DE CONTROL</h1>
        </div>
        <form action="">
          <div class="botones">
            <div id="myBombilla" class="fondoBoton <?php echo $todos[0]['estado'] == 0 ? '' : 'changed'  ?>">
              LUCES
              <img id="bombilla" src="./media/bombilla.png" alt="" />
            </div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div onclick="textoEscrito()" id="myAlarma" class="fondoBoton">ALARMA</div>
          </div>
        </form>
      </section>


      <div id="alarmBlock"></div>

      <div id="emergencia"></div>
      <button onclick="cerrar()" id="x">X</button>
    </div>
  </main>
  <script>
    let planoOn = document.getElementById("planoOn");
    let planoOff = document.getElementById("planoOff");
    let bombillaClicked = false;
    let miDato;
    document.getElementById("myBombilla").addEventListener("click", function() {
      // BotonEncendido();


      // function BotonEncendido() {
        this.classList.toggle("changed");

      // }
      planoOn.style.display = planoOn.style.display === "block" ? "none" : "block";
      planoOff.style.display = planoOff.style.display === "none" ? "block" : "none";

      let miDato = new URLSearchParams();
      bombillaClicked = <?php echo $todos[0]['estado'] == 0 ? "false" : "true"; ?>;



      miDato.append("valor", bombillaClicked ? "apagado" : "encendido");
      miDato.append("dispositivoID", 1);
      miDato.append("uid", <?php echo $_SESSION['userID']; ?>);


      fetch('procesar.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded' // Enviar como form
          },
          body: miDato.toString() // Enviar la cadena como form-urlencoded
        })
        .then(response => response.text()) // Interpretar la respuesta como texto
        .then(data => {
          console.log('Respuesta del servidor:', data);
        })
        .catch(error => {
          console.error('Error:', error);
        });
    });
    let alarma = document.getElementById("alarmBlock");
    let alarmActive = false;
    let emergencia = document.getElementById("emergencia");
    let x = document.getElementById("x");

    //Envio de datos a la base de datos

    document.getElementById("myAlarma").addEventListener("click", function() {
      alarma.classList.toggle("changedAlarma");

      if (emergencia.style.display === "block") {
        emergencia.style.display = "none";
      } else {
        emergencia.style.display = "block";
      }

      x.style.display = "block";

      if (!alarmActive) {
        alarma.style.display = "block";
        alarma.classList.add("fading");
        alarmActive = true;
        setTimeout(() => {
          alarma.classList.remove("fading"); // Detiene la animación
          alarma.style.display = "none"; // Oculta el div
          alarmActive = false;
        }, 25000);
      } else {
        alarma.style.display = "none";
        alarma.classList.remove("fading");
        alarmActive = false;
      }
    });

    function cerrar() {
      emergencia.style.display = "none";
      x.style.display = "none";
      setTimeout(() => {
        location.reload();
      }, 1000);
    }

    function textoEscrito() {
      const div = document.getElementById("emergencia");
      let texto = "LLamando a los servicios de emergencia                <br><br>Reiniciando sistema de seguridad...                      <br><br>Aspersores en marcha                  <br><br>Apagando calefaccion...                  <br><br>Reiniciando routers...           <br><br>Guardando y descargando datos...";

      function efectoTextTyping(elemento, texto, i = 0) {
        if (texto.substring(i, i + 4) === "<br>") {
          elemento.innerHTML += "<br>";
          i += 4;
        } else {
          elemento.innerHTML += texto[i];
          i++;
        }

        if (i >= texto.length) return;

        setTimeout(() => efectoTextTyping(elemento, texto, i), 100);
      }

      efectoTextTyping(div, texto);
    }
  </script>
</body>

</html>