<?php
// Configuración de la base de datos
$servername = "localhost"; // Cambia si es necesario
$username = "root";        // Cambia si es necesario
$password = "";            // Cambia si es necesario
$dbname = "erronka0"; // Nombre de tu base de datos

// Crear la conexión a MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Recibir los datos enviados por JavaScript
$data = file_get_contents("php://input");
$decodedData = json_decode($data, true);

// Asegúrate de que los datos se han recibido correctamente
if ($decodedData) {
    $estado = $decodedData['valor'];
    $dispositivoID = $decodedData['dispositivoID'];
    $usuarioID = $decodedData['uid'];


    // Preparar la consulta SQL para insertar el dato
    $sql = "UPDATE dispositivo SET estado = !estado WHERE dispositivoID = :dispositivoID";
		$stmt = $con->prepare($sql);
		$stmt->bindParam(':dispositivoID', $dispositivoID);
		$stmt->execute();
		$sql2 = "INSERT INTO acciones (usuarioID, dispositivoID, estado) VALUES (:uid, :dispositivoID, :estado)";
		$stmt2 = $con->prepare($sql2);
		$stmt2->bindParam(':usuarioID', $usuarioID);
		$stmt2->bindParam(':dispositivoID', $dispositivoID);
		$stmt2->bindParam(':estado', $estado);
		

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Responder al cliente con un mensaje de éxito
        echo json_encode([
            "status" => "success",
            "mensaje" => "Dato guardado correctamente en la base de datos",
            "dato" => $valorRecibido
        ]);
    } else {
        // Si hubo un error al ejecutar la consulta
        echo json_encode([
            "status" => "error",
            "mensaje" => "Error al guardar el dato en la base de datos"
        ]);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
} else {
    // Si no se recibieron datos correctamente
    echo json_encode([
        "status" => "error",
        "mensaje" => "No se recibieron datos válidos"
    ]);
}
?>
