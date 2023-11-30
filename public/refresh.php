<?php
include_once '../src/db/config.php'; // Asegúrate de que este archivo contiene tus detalles de conexión a la base de datos

// Establecer la conexión a la base de datos
$config = parse_ini_file('../config.ini', true);

$conn = new mysqli(
    $config['database']['host'],
    $config['database']['user'],
    $config['database']['password'],
    $config['database']['dbname']
);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Fecha de hace un mes
$fechaLimite = new DateTime();
$fechaLimite->modify('-1 month');
$fechaLimite = $fechaLimite->format('Y-m-d');

// Preparar y ejecutar la consulta SQL
$sql = "UPDATE territorios SET estado = 'incompleto' WHERE ultima_fecha_completo <= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $fechaLimite);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Territorios actualizados correctamente.";
} else {
    echo "No se encontraron territorios para actualizar.";
}

// Cerrar la conexión
$stmt->close();
$conn->close();
header("Location: index.php");
?>