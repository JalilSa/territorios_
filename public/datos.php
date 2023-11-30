<?php
session_set_cookie_params(['secure' => true, 'httponly' => true]);
session_start();

include 'components/nav.php';
$config = parse_ini_file('../config.ini', true);

$conn = new mysqli(
    $config['database']['host'],
    $config['database']['user'],
    $config['database']['password'],
    $config['database']['dbname']
);


$sqlTest = "SELECT 1 FROM territorios LIMIT 1";
$resultTest = $conn->query($sqlTest);

if ($resultTest) {
    echo "<p>Conexión a la base de datos exitosa.</p>";
} else {
    echo "<p>Error en la conexión a la base de datos: " . $conn->error . "</p>";
}
if (!isset($_SESSION['usuario_id'])) {
    header("location: index.php");
    exit();
}



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