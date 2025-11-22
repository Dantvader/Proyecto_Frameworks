<?php
require_once "conexion.php";

$tipo = $_POST['usuario'];
$id = $_POST['idusuario'];
$pass = $_POST['password'];

$tablas_validas = [
    "Administrador" => "administrador",
    "Administrador suplente" => "administrador_sub",
    "Estudiante" => "estudiantes"
];

if (!array_key_exists($tipo, $tablas_validas)) {
    die("Tipo de usuario inválido.");
}

$tabla = $tablas_validas[$tipo];

$stmt = $pdo->prepare("SELECT * FROM $tabla WHERE id = ? AND contrasena = ?");
$stmt->execute([$id, $pass]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    if ($tipo === "Administrador") {
        header("Location: menuAdmin.html");
        exit;
    }
    if ($tipo === "Administrador suplente") {
        header("Location: menuAdminSup.html");
        exit;
    }
    if ($tipo === "Estudiante") {
        header("Location: menuUser.html");
        exit;
    }
} else {
    echo "Credenciales incorrectas.";
}
?>

