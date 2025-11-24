<?php
session_start();
require __DIR__. "/conexion.php";

// Obtener datos del formulario
$tipo = $_POST["usuario"];       // administrador, administrador_sub, estudiantes
$id   = $_POST["idusuario"];
$pass = $_POST["password"];

// Verificar campos vacíos
if (empty($tipo) || empty($id) || empty($pass)) {
    die("Faltan datos. <a href='../front/index.html'>Volver</a>");
}

// Sanitizar tipo para evitar inyección (solo permitir esas tablas)
$tablas_validas = ["administrador", "administrador_sub", "estudiantes"];
if (!in_array($tipo, $tablas_validas)) {
    die("Tipo de usuario inválido.");
}

// Preparar consulta
$sql = "SELECT * FROM $tipo WHERE id = :id AND contrasena = :pass";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(":id", $id);
$stmt->bindParam(":pass", $pass);
$stmt->execute();

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Inicio de sesión correcto
    $_SESSION["id"] = $usuario["id"];
    $_SESSION["nombre"] = $usuario["nombre"];
    $_SESSION["tipo"] = $tipo;

    // Redirección según tipo
    if ($tipo === "administrador") {
        header("Location: ../front/menuAdmin.html");
        exit();
    } elseif ($tipo === "administrador_sub") {
        header("Location: ../front/menuAdminSup.html");
        exit();
    } else {
        header("Location: ../front/menuUser.html");
        exit();
    }

} else {
    echo "Credenciales incorrectas. <a href='../front/index.html'>Intentar de nuevo</a>";
}
?>
