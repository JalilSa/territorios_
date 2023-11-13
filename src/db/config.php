<?php
// src/db/config.php

// Ruta absoluta o relativa al archivo config.ini
$config = parse_ini_file('../config.ini', true);

// Crear una conexión con la base de datos
$conn = new mysqli(
    $config['database']['host'],
    $config['database']['user'],
    $config['database']['password'],
    $config['database']['dbname']
);

// Verificar si la conexión falló
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
