<?php
header("Content-Type: application/json");
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$tipo = $data["tipo"]; // "alumno" o "admin"
$usuario = $data["usuario"];
$clave = $data["clave"];

if ($tipo === "alumno") {
    $sql = "SELECT id_alumno, nombre, correo, carrera FROM alumnos WHERE usuario=? AND clave=?";
} else if ($tipo === "admin") {
    $sql = "SELECT id_admin, nombre, categoria FROM administradores WHERE usuario=? AND clave=?";
} else {
    echo json_encode(["error" => "Tipo de usuario inválido"]);
    exit;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario, $clave);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "data" => $row]);
} else {
    echo json_encode(["success" => false, "mensaje" => "Credenciales inválidas"]);
}
?>
