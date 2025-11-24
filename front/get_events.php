<?php
// get_events.php
session_start();
header('Content-Type: application/json; charset=utf-8');
require 'db.php';

$stmt = $pdo->query("SELECT E.id, E.estudiante_id, E.expediente, E.fecha_hora, S.nombre
                     FROM Entrevistas E
                     LEFT JOIN Estudiantes S ON E.estudiante_id = S.id
                     ORDER BY E.fecha_hora ASC");

$rows = $stmt->fetchAll();

$events = [];
foreach ($rows as $r) {
    // Para FullCalendar: { id, title, start }
    $events[] = [
      'id' => $r['id'],
      'title' => $r['nombre'] . ' (' . $r['expediente'] . ')',
      'start' => $r['fecha_hora'],
      // puedes añadir más datos
      'extendedProps' => [
        'estudiante_id' => $r['estudiante_id'],
        'expediente' => $r['expediente']
      ]
    ];
}

echo json_encode($events);
