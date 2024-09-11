<?php

require_once 'connection/getConnection.php';

function testConnection() { //Revisar a futuro
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "erronka0";
    $conn = null;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES 'utf8'");
        $conn = null;
		return true;
    } catch(PDOException $e) {
        echo "Errorea: " . $e->getMessage();
		return false;
    }
	return false;
}

function createDB() {
	$conn = new PDO("mysql:host=localhost", "root", "");
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->exec("SET NAMES 'utf8'");
	$sql = "CREATE DATABASE IF NOT EXISTS erronka0";
	$conn->exec($sql);
	$conn = null;
}

function createUsersTable() {
	$conn = getConnection();
	$sql = "CREATE TABLE IF NOT EXISTS users (
		usuarioID INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		nombre VARCHAR(30) NOT NULL,
		password VARCHAR(30) NOT NULL
	)";
	$conn->exec($sql);
	$conn = null;
}

function createDispositivoTable() {
	$conn = getConnection();
	$sql = "CREATE TABLE IF NOT EXISTS dispositivo (
		dispositivoID INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		tipo VARCHAR(30) NOT NULL,
		piso INT(6) NOT NULL,
		estado BOOLEAN NOT NULL
	)";
	$conn->exec($sql);
	$conn = null;
}

function createAccionesTable() {
	$conn = getConnection();
	$sql = "CREATE TABLE IF NOT EXISTS acciones (
		actionID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		usuarioID INT(6) NOT NULL,
		dispositivoID INT(6) NOT NULL,
		accion VARCHAR(30) NOT NULL,
		fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		FOREIGN KEY (usuarioID) REFERENCES users(usuarioID) ON DELETE CASCADE,
		FOREIGN KEY (dispositivoID) REFERENCES dispositivo(dispositivoID) ON DELETE CASCADE
	)";
	$conn->exec($sql);
	$conn = null;
}

function insertUsers() {
	$conn = getConnection();
	$sql = "INSERT INTO users (nombre, password) VALUES
		('Ibai', '123123'),
		('Igotz', '123123'),
		('Jon', '123123')";
	$conn->exec($sql);
	$conn = null;
}

function inserDispositivos() {
	$conn = getConnection();
	$sql = "INSERT INTO dispositivo (tipo, piso, estado) VALUES
		('Luz', 1, 0),
		('Luz', 2, 0),
		('Router', 1, 1),
		('Router', 2, 1),
		('Calefaccion', 1, 0),
		('Calefaccion', 2, 0)";
	$conn->exec($sql);
	$conn = null;
}

function insertAcciones() {
	$conn = getConnection();
	$sql = "INSERT INTO acciones (usuarioID, dispositivoID, accion) VALUES
		(1, 1, 'encender'),
		(1, 2, 'encender'),
		(2, 3, 'apagar'),
		(2, 4, 'apagar'),
		(3, 5, 'encender'),
		(3, 6, 'encender'),
		(1, 5, 'apagar'),
		(2, 6, 'apagar')";
	$conn->exec($sql);
	$conn = null;
}

if (!testConnection()) {
	createDB();
	createUsersTable();
	insertUsers();
	createDispositivoTable();
	inserDispositivos();
	sleep(5);
	createAccionesTable();
	insertAcciones();
}
else {
	$conn = null;
}