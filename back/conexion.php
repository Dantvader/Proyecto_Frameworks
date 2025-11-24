
<?php
// --- DATOS QUE DEBE RELLENAR ---
$servidor_ip = "192.168.100.128";  // Ejemplo: "192.168.1.10" de la vm en este caso
$nombre_bd   = "base_becas";  // Ejemplo: "proyecto_web_bd"
$usuario_bd  = "Admin1";
$password_bd = "12345";
$puerto      = 3306; // El puerto que configuraste del mysql

// DSN (Data Source Name)
$dsn = "mysql:host=$servidor_ip;port=$puerto;dbname=$nombre_bd;charset=utf8mb4";

try {
    $conexion = new PDO(
        "mysql:host=$servidor_ip;port=$puerto;dbname=$nombre_bd;charset=utf8mb4",
        $usuario_bd,
        $password_bd
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

/*
try {
    // Crear la instancia de PDO
    $pdo = new PDO($dsn, $usuario_bd, $password_bd);
    
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "¡Conexión exitosa a la base de datos!";

    // --- Ejemplo de cómo usar la conexión ---
    // $stmt = $pdo->query("SELECT * FROM tu_tabla");
    // while ($fila = $stmt->fetch()) {
    //     print_r($fila);
    // }

} catch (PDOException $e) {
    // Capturar cualquier error de conexión
    die("Error de conexión: " . $e->getMessage());
}

*/
?>