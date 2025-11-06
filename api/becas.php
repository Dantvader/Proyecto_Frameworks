<?php
header("Content-Type: application/json");
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$id_admin = $data["id_admin"];
$accion = $data["accion"]; // "insertar", "actualizar", "eliminar"
$id_alumno = $data["id_alumno"];
$estado = $data["estado"];
$fecha = $data["fecha_renovacion"];

// Verificar categoría
$check = $conn->prepare("SELECT categoria FROM administradores WHERE id_admin=?");
$check->bind_param("i", $id_admin);
$check->execute();
$res = $check->get_result()->fetch_assoc();

if (!$res || $res["categoria"] != "superior") {
    echo json_encode(["error" => "No tienes permisos suficientes"]);
    exit;
}

switch ($accion) {
    case "insertar":
        $sql = "INSERT INTO becas (id_alumno, estado, fecha_renovacion) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $id_alumno, $estado, $fecha);
        break;
    case "actualizar":
        $id_beca = $data["id_beca"];
        $sql = "UPDATE becas SET estado=?, fecha_renovacion=? WHERE id_beca=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $estado, $fecha, $id_beca);
        break;
    case "eliminar":
        $id_beca = $data["id_beca"];
        $sql = "DELETE FROM becas WHERE id_beca=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_beca);
        break;
    default:
        echo json_encode(["error" => "Acción no válida"]);
        exit;
}

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "No se pudo ejecutar la acción"]);
}
?>
