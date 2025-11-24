<?php
// get_students.php hector .. elimina si no funciona
session_start();
header('Content-Type: application/json; charset=utf-8');

require 'db.php';

// Opcional: si quieres filtrar
$stmt = $pdo->query("SELECT id, nombre, expediente, imagen_url FROM Estudiantes ORDER BY nombre");
$students = $stmt->fetchAll();

echo json_encode($students);
