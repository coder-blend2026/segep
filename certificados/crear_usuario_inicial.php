<?php
/**
 * Ejecutar UNA VEZ (CLI o navegador) para crear el usuario administrador.
 * Ejemplo: php crear_usuario_inicial.php
 * Luego elimina este archivo en producción por seguridad.
 */
$dbPath = getenv('DB_PATH') ?: __DIR__ . '/db.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec('CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT
)');

$username = 'admin';
$plainPassword = 'Admin2026!';

$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE username = :u');
$stmt->execute([':u' => $username]);
$exists = $stmt->fetch(PDO::FETCH_ASSOC);

$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

if ($exists) {
    $upd = $pdo->prepare('UPDATE usuarios SET password = :p WHERE username = :u');
    $upd->execute([':p' => $hash, ':u' => $username]);
    echo "Usuario '{$username}' ya existía. Se actualizó la contraseña.\n";
} else {
    $ins = $pdo->prepare('INSERT INTO usuarios (username, password) VALUES (:u, :p)');
    $ins->execute([':u' => $username, ':p' => $hash]);
    echo "Usuario creado correctamente.\n";
}

echo "\nCredenciales:\n";
echo "  Usuario:     {$username}\n";
echo "  Contraseña:  {$plainPassword}\n";
echo "\nEntra en login.php y luego en certificados/registro.php para dar de alta folios.\n";
