<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/database.php';
$db = openCertificatesDatabase();

$msg = '';
$msgType = 'success';
$editRow = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $delId = (int) $_POST['delete_id'];
        $stmt = $db->prepare('DELETE FROM certificados WHERE id = :id');
        $stmt->bindValue(':id', $delId, SQLITE3_INTEGER);
        $stmt->execute();
        header('Location: registro.php');
        exit;
    } elseif (isset($_POST['save'])) {
        $fields = [
            'nombres' => trim((string) ($_POST['nombres'] ?? $_POST['nombre'] ?? '')),
            'primer_apellido' => trim((string) ($_POST['primer_apellido'] ?? '')),
            'segundo_apellido' => trim((string) ($_POST['segundo_apellido'] ?? '')),
            'tipo' => trim((string) ($_POST['tipo'] ?? 'GENERAL')),
            'tipo_certificacion' => trim((string) ($_POST['tipo_certificacion'] ?? $_POST['nivel'] ?? '')),
            'tipo_certificado' => trim((string) ($_POST['tipo_certificado'] ?? '')),
            'folio' => trim((string) ($_POST['folio'] ?? '')),
            'carrera' => trim((string) ($_POST['carrera'] ?? '')),
            'institucion' => trim((string) ($_POST['institucion'] ?? '')),
            'fecha_certificado' => trim((string) ($_POST['fecha_certificado'] ?? '')),
            'promedio' => trim((string) ($_POST['promedio'] ?? '')),
            'promedio_texto' => trim((string) ($_POST['promedio_texto'] ?? '')),
            'estatus' => trim((string) ($_POST['estatus'] ?? '')),
            'cert_tipo_id' => trim((string) ($_POST['cert_tipo_id'] ?? '')),
            'periodo_inicio' => trim((string) ($_POST['periodo_inicio'] ?? '')),
            'periodo_fin' => trim((string) ($_POST['periodo_fin'] ?? '')),
            'tipo_perfil_laboral' => trim((string) ($_POST['tipo_perfil_laboral'] ?? '')),
            'nombre_perfil_laboral' => trim((string) ($_POST['nombre_perfil_laboral'] ?? '')),
        ];
        $fields['nombre'] = trim(implode(' ', array_filter([
            $fields['nombres'],
            $fields['primer_apellido'],
            $fields['segundo_apellido'],
        ], static fn ($part) => $part !== '')));
        $fields['nivel'] = $fields['tipo_certificacion'];
        $editId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($editId > 0) {
            $assignments = array_map(static fn ($field) => "{$field} = :{$field}", array_keys($fields));
            $stmt = $db->prepare('UPDATE certificados SET ' . implode(', ', $assignments) . ' WHERE id = :id');
            $stmt->bindValue(':id', $editId, SQLITE3_INTEGER);
        } else {
            $columns = array_keys($fields);
            $placeholders = array_map(static fn ($field) => ':' . $field, $columns);
            $stmt = $db->prepare('INSERT INTO certificados (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')');
        }

        foreach ($fields as $field => $value) {
            $stmt->bindValue(':' . $field, $value, SQLITE3_TEXT);
        }
        
        if ($stmt->execute()) {
            header('Location: registro.php?ok=1');
            exit;
        } else {
            $msg = 'Error: Folio duplicado o datos inválidos.';
            $msgType = 'error';
        }
    }
}

if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM certificados WHERE id = :id');
    $stmt->bindValue(':id', (int)$_GET['edit'], SQLITE3_INTEGER);
    $editRow = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
}

$rows = [];
$res = $db->query('SELECT * FROM certificados ORDER BY id DESC');
while ($row = $res->fetchArray(SQLITE3_ASSOC)) { $rows[] = $row; }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Certificados - SIGED</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-guinda: #621132;
            --color-dorado: #D4C19C;
            --color-dorado-oscuro: #B38E5D;
        }
        body { background-color: #f8f9fa; font-family: 'Montserrat', sans-serif; }
        
        /* Header Estilo Gobierno */
        .navbar-siged {
            background-color: var(--color-guinda);
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .main-container { margin-top: 40px; }

        /* Tabla Estilizada */
        .card-table {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .thead-siged {
            background-color: var(--color-guinda);
            color: white;
        }
        .btn-nuevo {
            background-color: var(--color-dorado-oscuro);
            color: white;
            font-weight: bold;
            border: none;
        }
        .btn-nuevo:hover { background-color: var(--color-guinda); color: white; }

        /* Modal Personalizado */
        .modal-header-siged {
            background-color: var(--color-guinda);
            color: white;
            border-bottom: 3px solid var(--color-dorado);
        }
        .auth-label { font-weight: bold; color: var(--color-guinda); font-size: 13px; }
        .auth-input { border-radius: 4px; margin-bottom: 15px; }
        
        .btn-save { background-color: var(--color-guinda); color: white; border: none; }
        .btn-save:hover { background-color: #4a0d26; color: white; }
    </style>
</head>
<body>

<nav class="navbar-siged d-flex justify-content-between align-items-center">
    <div>
        <h4 class="m-0">SIGED | Panel de Control</h4>
        <small>Usuario: <?= htmlspecialchars($_SESSION['user']) ?></small>
    </div>
    <div>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
    </div>
</nav>

<div class="container main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: var(--color-guinda);">Listado de Certificados</h2>
        <button class="btn btn-nuevo" data-toggle="modal" data-target="#modalCertificado">
            <i class="fas fa-plus"></i> Nuevo Certificado
        </button>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success">Operación realizada con éxito.</div>
    <?php endif; ?>

    <div class="card card-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-siged">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Alumno</th>
                        <th>Carrera</th>
                        <th>Folio</th>
                        <th>Institución</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><strong><?= htmlspecialchars($r['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($r['carrera']) ?></td>
                        <td><code style="color: var(--color-guinda)"><?= htmlspecialchars($r['folio']) ?></code></td>
                        <td><?= htmlspecialchars($r['institucion']) ?></td>
                        <td class="text-center">
                            <a href="registro.php?edit=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('¿Eliminar registro?');">
                                <input type="hidden" name="delete_id" value="<?= $r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCertificado" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-siged">
                <h5 class="modal-title"><?= $editRow ? 'Editar Certificado' : 'Registrar Nuevo Certificado' ?></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?php if ($editRow): ?>
                        <input type="hidden" name="id" value="<?= $editRow['id'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="save" value="1">
                    
                    <h6 class="mb-3">Datos del alumno</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="auth-label">NOMBRES</label>
                            <input type="text" name="nombres" class="form-control auth-input" required value="<?= htmlspecialchars($editRow['nombres'] ?? $editRow['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">PRIMER APELLIDO</label>
                            <input type="text" name="primer_apellido" class="form-control auth-input" value="<?= htmlspecialchars($editRow['primer_apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">SEGUNDO APELLIDO</label>
                            <input type="text" name="segundo_apellido" class="form-control auth-input" value="<?= htmlspecialchars($editRow['segundo_apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>

                    <h6 class="mb-3 mt-2">Datos del certificado</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="auth-label">FOLIO ÚNICO</label>
                            <input type="text" name="folio" class="form-control auth-input" required value="<?= htmlspecialchars($editRow['folio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">TIPO DE REGISTRO</label>
                            <select name="tipo" class="form-control auth-input">
                                <option value="GENERAL" <?= ($editRow['tipo'] ?? '') === 'IEMS' ? '' : 'selected' ?>>General</option>
                                <option value="IEMS" <?= ($editRow['tipo'] ?? '') === 'IEMS' ? 'selected' : '' ?>>IEMS</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">TIPO DE DOCUMENTO</label>
                            <input type="text" name="tipo_certificado" class="form-control auth-input" placeholder="Certificado de estudios" value="<?= htmlspecialchars($editRow['tipo_certificado'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">TIPO DE ESTUDIOS / NIVEL</label>
                            <input type="text" name="tipo_certificacion" class="form-control auth-input" required value="<?= htmlspecialchars($editRow['tipo_certificacion'] ?? $editRow['nivel'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">CARRERA / ESTUDIOS</label>
                            <input type="text" name="carrera" class="form-control auth-input" required value="<?= htmlspecialchars($editRow['carrera'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">INSTITUCIÓN EMISORA</label>
                            <input type="text" name="institucion" class="form-control auth-input" required value="<?= htmlspecialchars($editRow['institucion'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">FECHA DE REGISTRO</label>
                            <input type="date" name="fecha_certificado" class="form-control auth-input" value="<?= htmlspecialchars($editRow['fecha_certificado'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">PROMEDIO / RESULTADO</label>
                            <input type="text" name="promedio" class="form-control auth-input" value="<?= htmlspecialchars($editRow['promedio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">PROMEDIO EN TEXTO</label>
                            <input type="text" name="promedio_texto" class="form-control auth-input" value="<?= htmlspecialchars($editRow['promedio_texto'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="auth-label">ESTATUS DEL DOCUMENTO</label>
                            <input type="text" name="estatus" class="form-control auth-input" placeholder="Vigente" value="<?= htmlspecialchars($editRow['estatus'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="auth-label">ID DEL TIPO DE CERTIFICADO</label>
                            <input type="text" name="cert_tipo_id" class="form-control auth-input" value="<?= htmlspecialchars($editRow['cert_tipo_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="auth-label">PERIODO DE INICIO</label>
                            <input type="text" name="periodo_inicio" class="form-control auth-input" value="<?= htmlspecialchars($editRow['periodo_inicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="auth-label">PERIODO DE FIN</label>
                            <input type="text" name="periodo_fin" class="form-control auth-input" value="<?= htmlspecialchars($editRow['periodo_fin'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="auth-label">TIPO DE PERFIL LABORAL</label>
                            <input type="text" name="tipo_perfil_laboral" class="form-control auth-input" value="<?= htmlspecialchars($editRow['tipo_perfil_laboral'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="auth-label">NOMBRE DEL PERFIL LABORAL</label>
                            <input type="text" name="nombre_perfil_laboral" class="form-control auth-input" value="<?= htmlspecialchars($editRow['nombre_perfil_laboral'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-save"><?= $editRow ? 'Guardar Cambios' : 'Registrar Certificado' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Si estamos editando, abrir el modal automáticamente
    <?php if ($editRow): ?>
        $('#modalCertificado').modal('show');
    <?php endif; ?>
    
    // Limpiar URL al cerrar modal de edición
    $('#modalCertificado').on('hidden.bs.modal', function () {
        if (window.location.search.includes('edit=')) {
            window.location.href = 'registro.php';
        }
    });
</script>

</body>
</html>
