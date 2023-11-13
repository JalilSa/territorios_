<?php
session_start();
if (!isset($_SESSION['usuario_id'])){
    header("location: index.php");
exit();
}
include 'components/nav.php';
include_once '../src/db/config.php'; // Incluye la configuración de la base de datos

// Función para mostrar un mensaje de error
function mostrarError($mensaje) {
    echo "<script>alert('$mensaje');</script>";
}

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_territorio = $_POST['id_territorio'];
    $estado_territorio = $_POST['estado_territorio']; // Este es ahora un valor ENUM (cadena)
    $fecha_completado = $_POST['fecha_completado'];

    // Lógica para establecer la fecha de completado
    if (empty($fecha_completado)) {
        if ($estado_territorio == 'completo') {
            $fecha_completado = date('Y-m-d'); // Usa la fecha actual si está completo
        } else {
            $fecha_completado = NULL; // Deja la fecha como NULL si no está completo
        }
    } else {
        if (new DateTime($fecha_completado) > new DateTime()) {
            mostrarError("No se puede agregar una fecha futura");
            return; // Detiene la ejecución del script
        }
    }

    // Preparar y ejecutar la consulta SQL
    if ($fecha_completado) {
        $stmt = $conn->prepare("UPDATE territorios SET estado = ?, ultima_fecha_completo = ? WHERE id_territorio = ?");
        $stmt->bind_param("ssi", $estado_territorio, $fecha_completado, $id_territorio);
    } else {
        $stmt = $conn->prepare("UPDATE territorios SET estado = ? WHERE id_territorio = ?");
        $stmt->bind_param("si", $estado_territorio, $id_territorio);
    }
    
    $stmt->execute();
    $stmt->close();
}



if ($_SESSION['tipo_usuario']== 'admin'){
    if(isset($_POST['actualizar_fecha_asignacion'])) {
        $id_territorio = $_POST['id_territorio_fecha'];
        $fecha_asignacion = $_POST['fecha_asignacion'];

        $stmt = $conn->prepare("UPDATE territorios SET ulrima_fecha_asignado = ? WHERE id_territorio = ?");
        $stmt->bind_param("si",$fecha_asignacion,$id_territorio);
        $stmt->execute();
        $stmt->close();
    }
}

// Obtener la lista de territorios
$result = $conn->query("SELECT id_territorio, nombre_territorio FROM territorios");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Territorios</title>
    <!-- Tus estilos y scripts aquí -->
</head>
<body>

<h2>Editar Territorios</h2>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label for="id_territorio">Selecciona un territorio:</label>
    <select name="id_territorio" required>
    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<option value='" . $row["id_territorio"] . "'>" . $row["nombre_territorio"] . "</option>";
            }
        }
        ?>
    </select>
    <br><br>

    <label for="estado_territorio">Estado del Territorio:</label>
    <select name="estado_territorio" required>
    <option value="completo">Completo</option>
    <option value="incompleto">Incompleto</option>
    <option value="parcial">Parcial</option>
</select>

    <br><br>

    <label for="fecha_completado">Fecha de completado (opcional):</label>
    <input type="date" name="fecha_completado">
    <br><br>
    <input type="submit" value="Actualizar Estado">
</form>





<?php if ($_SESSION['tipo_usuario'] == 'admin'): ?>
    <h2>Cambiar Fecha de Asignación de Territorio</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="hidden" name="actualizar_fecha_asignacion" value="1">
        <label for="id_territorio_fecha">Selecciona un territorio:</label>
        <select name="id_territorio_fecha" required>
            <?php
            // Reutiliza la consulta de territorios existente
            $result->data_seek(0); // Reinicia el puntero del resultado
            while($row = $result->fetch_assoc()) {
                echo "<option value='" . $row["id_territorio"] . "'>" . $row["nombre_territorio"] . "</option>";
            }
            ?>
        </select>
        <br><br>
        <label for="fecha_asignacion">Nueva Fecha de Asignación:</label>
        <input type="date" name="fecha_asignacion" required>
        <br><br>
        <input type="submit" value="Actualizar Fecha">
    </form>
<?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
