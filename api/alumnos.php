<?php
header("Content-Type: application/json");
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data["nombre"];
$usuario = $data["usuario"];
$clave = $data["clave"];
$correo = $data["correo"];
$carrera = $data["carrera"];

$sql = "INSERT INTO alumnos (nombre, usuario, clave, correo, carrera) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $nombre, $usuario, $clave, $correo, $carrera);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "Alumno agregado correctamente"]);
} else {
    echo json_encode(["success" => false, "mensaje" => "Error al registrar alumno"]);
}
?>
