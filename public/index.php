<?php
session_start();
include 'components/nav.php';
include_once '../src/db/config.php'; 

if (isset($_SESSION['usuario_id'])) {
    header("Location: datos.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contraseña = $_POST['contraseña'];

    $stmt = $conn->prepare("SELECT id_usuario, tipo, contraseña FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($contraseña, $row['contraseña'])) {
            $_SESSION['usuario_id'] = $row['id_usuario'];
            $_SESSION['tipo_usuario'] = $row['tipo'];
            header("Location: datos.php");
            exit();
        } else {
            echo "Usuario o contraseña incorrectos.";
        }
    } else {
        echo "Usuario o contraseña incorrectos.";
    }

    $stmt->close();
}

$conn->close();
?>



<!DOCTYPE html>

<html>
<head>
    <title>Iniciar Sesión</title>
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>

    <form action="index.php" method="post">
        <div class="form-group">
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>
        </div>

        <div class="form-group">
            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" required>
        </div>

        <button type="submit">Iniciar Sesión</button>
    </form>
</div>

</body>
</html>
