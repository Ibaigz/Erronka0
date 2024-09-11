<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "erronka0";

// Crear la conexión a MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Recibir los datos enviados por JavaScript usando $_POST
if (isset($_POST['valor']) && isset($_POST['dispositivoID']) && isset($_POST['uid'])) {
    $estado = $_POST['valor'];
    $dispositivoID = $_POST['dispositivoID'];
    $usuarioID = $_POST['uid'];

    // Preparar la consulta SQL para actualizar el estado del dispositivo
    $sql = "UPDATE dispositivo SET estado = !estado WHERE dispositivoID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Error en la consulta: " . $conn->error;
        exit;
    }

    $stmt->bind_param("i", $dispositivoID);

    // Ejecutar la consulta de actualización
    if ($stmt->execute()) {
        // Preparar la consulta SQL para insertar en la tabla acciones
        $sql2 = "INSERT INTO acciones (usuarioID, dispositivoID, accion) VALUES (?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);

        if ($stmt2 === false) {
            echo "Error en la segunda consulta: " . $conn->error;
            exit;
        }

        $stmt2->bind_param("iis", $usuarioID, $dispositivoID, $estado);

        // Ejecutar la consulta de inserción
        if ($stmt2->execute()) {
            // Responder al cliente con un mensaje de éxito
            echo "Dato guardado correctamente en la base de datos";
        } else {
            // Si hubo un error al ejecutar la consulta de inserción
            echo "Error al guardar el dato en la tabla acciones: " . $stmt2->error;
        }

        $stmt2->close();
    } else {
        // Si hubo un error al ejecutar la consulta de actualización
        echo "Error al actualizar el estado del dispositivo: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Error: No se recibieron todos los datos necesarios.";
}
?>