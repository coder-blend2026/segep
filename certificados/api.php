<?php
// API para registrar y buscar certificados
session_start();
require_once __DIR__ . '/database.php';
$db = openCertificatesDatabase();

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE username = :u');
    $stmt->bindValue(':u', $data['username'], SQLITE3_TEXT);
    $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    if ($res && password_verify($data['password'], $res['password'])) {
        $_SESSION['user'] = $res['username'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas']);
    }
    exit;
}

if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'register_cert') {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'error' => 'No autenticado']);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $studentName = trim(implode(' ', array_filter([
        (string) ($data['nombres'] ?? $data['nombre'] ?? ''),
        (string) ($data['primer_apellido'] ?? ''),
        (string) ($data['segundo_apellido'] ?? ''),
    ], static fn ($part) => trim($part) !== '')));
    $fields = [
        'nombre' => $studentName,
        'nombres' => (string) ($data['nombres'] ?? $data['nombre'] ?? ''),
        'primer_apellido' => (string) ($data['primer_apellido'] ?? ''),
        'segundo_apellido' => (string) ($data['segundo_apellido'] ?? ''),
        'tipo' => (string) ($data['tipo'] ?? 'GENERAL'),
        'tipo_certificacion' => (string) ($data['tipo_certificacion'] ?? $data['nivel'] ?? ''),
        'tipo_certificado' => (string) ($data['tipo_certificado'] ?? ''),
        'carrera' => (string) ($data['carrera'] ?? ''),
        'nivel' => (string) ($data['tipo_certificacion'] ?? $data['nivel'] ?? ''),
        'folio' => (string) ($data['folio'] ?? ''),
        'institucion' => (string) ($data['institucion'] ?? ''),
        'fecha_certificado' => (string) ($data['fecha_certificado'] ?? ''),
        'promedio' => (string) ($data['promedio'] ?? ''),
        'promedio_texto' => (string) ($data['promedio_texto'] ?? ''),
        'estatus' => (string) ($data['estatus'] ?? ''),
        'cert_tipo_id' => (string) ($data['cert_tipo_id'] ?? ''),
        'periodo_inicio' => (string) ($data['periodo_inicio'] ?? ''),
        'periodo_fin' => (string) ($data['periodo_fin'] ?? ''),
        'tipo_perfil_laboral' => (string) ($data['tipo_perfil_laboral'] ?? ''),
        'nombre_perfil_laboral' => (string) ($data['nombre_perfil_laboral'] ?? ''),
    ];
    $columns = array_keys($fields);
    $placeholders = array_map(static fn ($column) => ':' . $column, $columns);
    $stmt = $db->prepare('INSERT INTO certificados (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')');
    foreach ($fields as $field => $value) {
        $stmt->bindValue(':' . $field, $value, SQLITE3_TEXT);
    }
    $ok = $stmt->execute();
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Folio duplicado o error de datos']);
    }
    exit;
}

if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'buscar_folio') {
    $folio = trim((string) ($_GET['folio'] ?? ''));
    $stmt = $db->prepare('SELECT * FROM certificados WHERE folio = :f COLLATE NOCASE');
    $stmt->bindValue(':f', $folio, SQLITE3_TEXT);
    $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    if ($res) {
        echo json_encode(['success' => true, 'data' => $res]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No encontrado']);
    }
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
