<?php
// assign_interview.php
session_start();
header('Content-Type: application/json; charset=utf-8');

require 'db.php';

// Permisos: solo admin suplente puede asignar
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin_suplente') {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Recibir JSON
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos JSON inválidos']);
    exit;
}

$estudiante_id = intval($input['estudiante_id'] ?? 0);
$expediente = $input['expediente'] ?? '';
$fecha_hora_str = $input['fecha_hora'] ?? ''; // esperar: 'HH:MM-dd-mm-yy'
$asignado_por = intval($_SESSION['usuario']['id'] ?? 0);

// Validaciones básicas
if ($estudiante_id <= 0 || empty($expediente) || empty($fecha_hora_str)) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

// Parsear formato: "HH:MM-dd-mm-yy" -> convertir a YYYY-MM-DD HH:MM:00
// asumimos que dd-mm-yy: si yy < 70 -> 20yy, else 19yy (o mejor manejar con 20yy)
$parts = explode('-', $fecha_hora_str);
if (count($parts) !== 3) {
    http_response_code(400);
    echo json_encode(['error' => 'Formato de fecha incorrecto']);
    exit;
}
$hora = trim($parts[0]);        // HH:MM
$dia = intval($parts[1]);
$mes = intval($parts[2]);
$anio2 = intval($parts[3] ?? null); // si preferiste usar 4 partes
// pero según tu formato: Hora 00:00 - 23:59-dd-mm-yy, mejor recibiremos con separador ' ' o distinto.
// Para seguridad, aceptemos un formato ISO si el frontend lo envía.

try {
    // Intento convertir con DateTime (admitimos dos formatos comunes)
    if (preg_match('/^\d{2}:\d{2}-\d{2}-\d{2}-\d{2}$/', $fecha_hora_str)) {
        // caso improbable; mejor que el frontend envíe 'HH:MM-dd-mm-yy' -> transformamos:
        list($hhmm, $dd, $mm, $yy) = explode('-', $fecha_hora_str);
        $yy = intval($yy);
        $fullYear = ($yy < 100) ? (2000 + $yy) : $yy;
        $datetime = DateTime::createFromFormat('H:i-d-m-Y', "$hhmm-$dd-$mm-$fullYear");
    } else {
        // Segundo intento: si frontend envía "HH:MM-dd-mm-yy" (3 partes)
        if (preg_match('/^(\d{2}:\d{2})-(\d{2})-(\d{2})-(\d{2})$/', $fecha_hora_str, $m)) {
            $hhmm = $m[1]; $dd = $m[2]; $mm = $m[3]; $yy = $m[4];
            $fullYear = 2000 + intval($yy);
            $datetime = DateTime::createFromFormat('H:i-d-m-Y', "$hhmm-$dd-$mm-$fullYear");
        } else {
            // fallback: accept ISO datetime from frontend
            $datetime = new DateTime($fecha_hora_str);
        }
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Fecha inválida: '.$e->getMessage()]);
    exit;
}

if (!$datetime) {
    http_response_code(400);
    echo json_encode(['error' => 'No se pudo interpretar la fecha']);
    exit;
}

$fecha_sql = $datetime->format('Y-m-d H:i:s');

try {
    $stmt = $pdo->prepare("INSERT INTO Entrevistas (estudiante_id, expediente, fecha_hora, asignado_por) VALUES (?, ?, ?, ?)");
    $stmt->execute([$estudiante_id, $expediente, $fecha_sql, $asignado_por]);

    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId(), 'fecha_sql' => $fecha_sql]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin_suplente') {
  http_response_code(403);
  echo json_encode(['error' => 'No autorizado']);
  exit;
}
