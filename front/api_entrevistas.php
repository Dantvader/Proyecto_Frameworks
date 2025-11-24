<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Incluir el archivo de conexión existente
include 'conexion.php';

// Determinar la acción basada en los parámetros
$action = $_GET['action'] ?? '';

try {
    switch($action) {
        case 'get_estudiantes':
            getEstudiantes();
            break;
            
        case 'get_entrevistas':
            getEntrevistas();
            break;
            
        case 'save_entrevista':
            saveEntrevista();
            break;
            
        default:
            echo json_encode(["success" => false, "message" => "Acción no válida"]);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

function getEstudiantes() {
    global $pdo;
    
    $sql = "SELECT ID, Nombre FROM Estudiantes ORDER BY Nombre";
    $stmt = $pdo->query($sql);
    
    $estudiantes = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $estudiantes[] = $row;
    }
    
    echo json_encode(["success" => true, "estudiantes" => $estudiantes]);
}

function getEntrevistas() {
    global $pdo;
    
    $start_date = $_GET['start_date'] ?? date('Y-m-d');
    $end_date = $_GET['end_date'] ?? date('Y-m-d', strtotime('+7 days'));

    $sql = "SELECT ID, Nombre_Estudiante, Nombre_Administrador, Fecha 
            FROM Entrevistas 
            WHERE Fecha BETWEEN ? AND ? 
            ORDER BY Fecha";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$start_date, $end_date]);
    
    $entrevistas = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $entrevistas[] = $row;
    }
    
    echo json_encode(["success" => true, "entrevistas" => $entrevistas]);
}

function saveEntrevista() {
    global $pdo;
    
    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true);

    $nombre_estudiante = $input['Nombre_Estudiante'] ?? '';
    $nombre_administrador = $input['Nombre_Administrador'] ?? '';
    $fecha = $input['Fecha'] ?? '';

    if (empty($nombre_estudiante) || empty($nombre_administrador) || empty($fecha)) {
        echo json_encode(["success" => false, "message" => "Datos incompletos"]);
        exit;
    }

    $sql = "INSERT INTO Entrevistas (Nombre_Estudiante, Nombre_Administrador, Fecha) 
            VALUES (?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre_estudiante, $nombre_administrador, $fecha]);
    
    echo json_encode(["success" => true, "message" => "Entrevista guardada exitosamente"]);
}
?>