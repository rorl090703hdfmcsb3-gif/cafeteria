<?php
// Configuración de conexión a Alwaysdata
$host = 'mysql-aguilacoffe.alwaysdata.net'; 
$db   = 'aguilacoffe_cafeteria';        
$user = 'aguilacoffe_admin';                    
$pass = 'k3y2dDUNA73iRT9G';                
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 1. LÓGICA PARA INSERTAR DATOS (Nombre, Correo, Mensaje)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre  = trim($_POST['nombre']);
        $correo  = trim($_POST['correo']);
        $mensaje = trim($_POST['mensaje']);

        if (!empty($nombre) && !empty($correo) && !empty($mensaje)) {
            $sql = "INSERT INTO mensajes (nombre, correo, mensaje) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $correo, $mensaje]);
            
            // Redirigir para evitar reenvíos duplicados
            header("Location: index.php");
            exit;
        }
    }

    // 2. LÓGICA PARA ELIMINAR DATOS
    if (isset($_GET['eliminar'])) {
        $id = $_GET['eliminar'];
        
        $sql = "DELETE FROM mensajes WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        // Redirigir para limpiar la URL
        header("Location: index.php");
        exit;
    }
?>
