<?php

function openCertificatesDatabase(): SQLite3
{
    $dbPath = getenv('DB_PATH') ?: __DIR__ . '/db.sqlite';
    $db = new SQLite3($dbPath);
    $db->busyTimeout(5000);

    $db->exec('CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT
    )');

    $db->exec('CREATE TABLE IF NOT EXISTS certificados (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT,
        nombres TEXT,
        primer_apellido TEXT,
        segundo_apellido TEXT,
        tipo TEXT,
        tipo_certificacion TEXT,
        tipo_certificado TEXT,
        carrera TEXT,
        nivel TEXT,
        folio TEXT UNIQUE,
        institucion TEXT,
        fecha_certificado TEXT,
        promedio TEXT,
        promedio_texto TEXT,
        estatus TEXT,
        cert_tipo_id TEXT,
        periodo_inicio TEXT,
        periodo_fin TEXT,
        tipo_perfil_laboral TEXT,
        nombre_perfil_laboral TEXT
    )');

    // Agrega los campos que falten en bases ya existentes sin borrar registros.
    $columns = [];
    $result = $db->query('PRAGMA table_info(certificados)');
    while ($column = $result->fetchArray(SQLITE3_ASSOC)) {
        $columns[$column['name']] = true;
    }

    $newColumns = [
        'nombres' => 'TEXT',
        'primer_apellido' => 'TEXT',
        'segundo_apellido' => 'TEXT',
        'tipo' => 'TEXT',
        'tipo_certificacion' => 'TEXT',
        'tipo_certificado' => 'TEXT',
        'fecha_certificado' => 'TEXT',
        'promedio' => 'TEXT',
        'promedio_texto' => 'TEXT',
        'estatus' => 'TEXT',
        'cert_tipo_id' => 'TEXT',
        'periodo_inicio' => 'TEXT',
        'periodo_fin' => 'TEXT',
        'tipo_perfil_laboral' => 'TEXT',
        'nombre_perfil_laboral' => 'TEXT',
    ];
    foreach ($newColumns as $name => $type) {
        if (!isset($columns[$name])) {
            $db->exec("ALTER TABLE certificados ADD COLUMN {$name} {$type}");
        }
    }

    return $db;
}
