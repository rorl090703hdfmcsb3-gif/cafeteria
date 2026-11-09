<?php
// Conexión a la base de datos
$conexion = new mysqli("mysql.railway.internal", "root", "ZcHPFMKuFaazOHjDkxkVnlSjhRBRynmu", "railway");
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

// 1. Lógica para Insertar Registro (Solo si viene el campo 'nombre' y NO es acción de eliminar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre"]) && (!isset($_POST['accion']) || $_POST['accion'] != 'eliminar')) {
    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");
    
    if ($nombre !== "" && $correo !== "" && $mensaje !== "") {
        if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            
            // Verificamos si el correo ya existe en la base de datos antes de insertar
            $check_sql = "SELECT correo FROM clientes WHERE correo = ? LIMIT 1";
            $check_stmt = $conexion->prepare($check_sql);
            $check_stmt->bind_param("s", $correo);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                // Si ya existe, guardamos un aviso para mostrar en pantalla
                $mensaje_alerta = "Este correo ya está registrado. No se permiten duplicados.";
            } else {
                // Si no existe, procedemos a realizar la inserción una sola vez
                $check_stmt->close();
                $sql = "INSERT INTO clientes (nombre, correo, mensaje) VALUES (?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("sss", $nombre, $correo, $mensaje);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
}

// 2. Lógica para Eliminar Registro (Solo si la acción es 'eliminar')
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $correo_eliminar = trim($_POST["correo"] ?? "");
    if (!empty($correo_eliminar)) {
        // Ejecuta la sentencia SQL DELETE para borrarlo físicamente de la tabla 'clientes'
        $sql = "DELETE FROM clientes WHERE correo = ?";
        $stmt = $conexion->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $correo_eliminar);
            $stmt->execute();
            $stmt->close();
        }
        // Recarga la página para actualizar la tabla con los datos actuales de la BD
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
