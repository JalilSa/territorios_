<?php
session_start();

include 'components/nav.php';

if (!isset($_SESSION['usuario_id'])) {
    header("location: index.php");
    exit();
}

include_once '../src/db/config.php'; // Incluye la configuración de la base de datos

$sql = "SELECT id_territorio, nombre_territorio, estado, ultima_fecha_completo, ultima_fecha_asignado FROM territorios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Estado</th><th>Última Fecha Completo</th><th>Última Fecha Asignado</th></tr>";
    while($row = $result->fetch_assoc()) {
        $estadoTexto = $row["estado"];
        switch ($estadoTexto) {
            case 'completo':
                $colorFondo = 'green';
                break;
            case 'parcial':
                $colorFondo = 'yellow';
                break;
            default: // 'incompleto'
                $colorFondo = 'red';
                break;
        }

        $fechaCompleto = $row["ultima_fecha_completo"] ? date('Y-m-d', strtotime($row["ultima_fecha_completo"])) : 'N/A';
        $fechaAsignado = $row["ultima_fecha_asignado"] ? date('Y-m-d', strtotime($row["ultima_fecha_asignado"])) : 'N/A';
        
        echo "<tr style='background-color: $colorFondo'><td>" . $row["id_territorio"] . "</td><td>" . $row["nombre_territorio"] . "</td><td>" . $estadoTexto . "</td><td>" . $fechaCompleto . "</td><td>" . $fechaAsignado . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No hay datos para mostrar";
}
$conn->close();
?>
