<?php
// API para registrar y buscar certificados
session_start();
$db = new SQLite3('db.sqlite');

// Crear tabla si no existe
$db->exec('CREATE TABLE IF NOT EXISTS certificados (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT,
    carrera TEXT,
    nivel TEXT,
    folio TEXT UNIQUE,
    institucion TEXT
)');

// Crear tabla de usuarios
$db->exec('CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT
)');

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
    $stmt = $db->prepare('INSERT INTO certificados (nombre, carrera, nivel, folio, institucion) VALUES (:n, :c, :l, :f, :i)');
    $stmt->bindValue(':n', $data['nombre'], SQLITE3_TEXT);
    $stmt->bindValue(':c', $data['carrera'], SQLITE3_TEXT);
    $stmt->bindValue(':l', $data['nivel'], SQLITE3_TEXT);
    $stmt->bindValue(':f', $data['folio'], SQLITE3_TEXT);
    $stmt->bindValue(':i', $data['institucion'], SQLITE3_TEXT);
    $ok = $stmt->execute();
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Folio duplicado o error de datos']);
    }
    exit;
}

if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'buscar_folio') {
    $folio = $_GET['folio'] ?? '';
    $stmt = $db->prepare('SELECT * FROM certificados WHERE folio = :f');
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
