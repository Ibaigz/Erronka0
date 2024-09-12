<?php
include "./connection/getConnection.php";
include "dbcreate.php";
session_start();

if (!isset($_SESSION['userID'])) {
  header('Location: login.php');
  exit;
}
require_once 'dbcreate.php';

$con = getConnection();

// Seleccionar todos los dispositivos
$sql = "SELECT * FROM dispositivo";
$stmt = $con->prepare($sql);
$stmt->execute();
$todos = $stmt->fetchAll();




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

          </div>
        </div>



      </section>
      <section id="s2">
        <div class="fondoP">
          <h1>PANEL DE CONTROL</h1>
        </div>
        <form action="">
          <div class="botones">
            <div id="myBombilla" class="fondoBoton <?php echo $todos[0]['estado'] == 0 ? '' : 'changed'; ?>">
              LUCES
              <img id="bombilla" src="./media/bombilla.png" alt="" />
            </div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div class="fondoBoton">LUCES</div>
            <div id="myLogs" class="fondoBoton">LOGS</div>
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
    // JavaScript para controlar la bombilla y la alarma
    let planoOn = document.getElementById("planoOn");
    let planoOff = document.getElementById("planoOff");
    let bombillaClicked = false;
    let miDato;
    const logDiv = document.querySelector(".logText");

    document.getElementById("myBombilla").addEventListener("click", function() {
      // Cambiar el estado del botón de luz
      this.classList.toggle("changed");

      // Alternar la acción según el estado del bombilla
      let action = this.classList.contains('changed') ? 'turnOn' : 'turnOff';

      // Actualizar la visibilidad de las imágenes
      planoOn.style.display = planoOn.style.display === "block" ? "none" : "block";
      planoOff.style.display = planoOff.style.display === "none" ? "block" : "none";

      actualizarLogs(action);
    });

    function actualizarLogs(action) {
      // Enviar datos al servidor
      console.log("Enviando datos al servidor...");
      let miDato = new URLSearchParams();
      miDato.append("valor", action);
      miDato.append("dispositivoID", 1); // Ajusta según sea necesario
      miDato.append("uid", <?php echo json_encode($_SESSION['userID']); ?>);

      fetch('procesar.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: miDato.toString()

        })
        .then(response => response.text())
        .then(data => {
          console.log('Respuesta del servidor:', data);
          repoblar();
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    // Función para repoblar los logs
    function repoblar() {
      console.log("Repoblando...");
      fetch('get_logs.php')
        .then(response => response.json())
        .then(data => {
          const logDiv = document.querySelector(".logText");
          logDiv.innerHTML = "";
          data.forEach(log => {
            logDiv.insertAdjacentHTML('beforeend', `> [${log['fecha']}] ${log['usuarioID']} ${log['accion']} ${log['dispositivoID']}<br>`);
          });
        })
        .catch(error => {
          console.error('Error al obtener los logs:', error);
        });
    }
    // Fin de la función para repoblar los logs

    // JavaScript para controlar la alarma
    let alarma = document.getElementById("alarmBlock");
    let alarmActive = false;
    let emergencia = document.getElementById("emergencia");
    let x = document.getElementById("x");


    document.getElementById("myAlarma").addEventListener("click", function() {
      alarma.classList.toggle("changedAlarma");

      if (emergencia.style.display === "block") {
        emergencia.style.display = "none";
      } else {
        emergencia.style.display = "block";
      }

      x.style.display = "block";

      if (!alarmActive) {
        let miDato2 = new URLSearchParams();
        miDato2.append("uid", <?php echo json_encode($_SESSION['userID']); ?>);

        fetch('log_alerta.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: miDato2.toString()
          })
          .then(response => response.text())
          .then(data => {
            console.log('Respuesta del servidor:', data);
          })
          .catch(error => {
              console.error('Error:', error);
            }
            //repoblar();
          );
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
    // Fin del código de la alarma

	// Inicio descargar logs

	document.getElementById("myLogs").addEventListener("click", function() {
		downloadLogs();
	});

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
	// Fin descargar logs


    Window.onload = repoblar();
  </script>
</body>

</html>