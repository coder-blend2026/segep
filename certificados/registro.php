<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$dbPath = __DIR__ . '/db.sqlite';
$db = new SQLite3($dbPath);
$db->exec('CREATE TABLE IF NOT EXISTS certificados (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT,
    carrera TEXT,
    nivel TEXT,
    folio TEXT UNIQUE,
    institucion TEXT
)');

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
        $nombre = trim((string)$_POST['nombre']);
        $carrera = trim((string)$_POST['carrera']);
        $nivel = trim((string)$_POST['nivel']);
        $folio = trim((string)$_POST['folio']);
        $institucion = trim((string)$_POST['institucion']);
        $editId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($editId > 0) {
            $stmt = $db->prepare('UPDATE certificados SET nombre=:n, carrera=:c, nivel=:l, folio=:f, institucion=:i WHERE id=:id');
            $stmt->bindValue(':id', $editId, SQLITE3_INTEGER);
        } else {
            $stmt = $db->prepare('INSERT INTO certificados (nombre, carrera, nivel, folio, institucion) VALUES (:n, :c, :l, :f, :i)');
        }
        
        $stmt->bindValue(':n', $nombre, SQLITE3_TEXT);
        $stmt->bindValue(':c', $carrera, SQLITE3_TEXT);
        $stmt->bindValue(':l', $nivel, SQLITE3_TEXT);
        $stmt->bindValue(':f', $folio, SQLITE3_TEXT);
        $stmt->bindValue(':i', $institucion, SQLITE3_TEXT);
        
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="auth-label">NOMBRE DEL ALUMNO</label>
                            <input type="text" name="nombre" class="form-control auth-input" required value="<?= $editRow ? htmlspecialchars($editRow['nombre']) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="auth-label">FOLIO ÚNICO</label>
                            <input type="text" name="folio" class="form-control auth-input" required value="<?= $editRow ? htmlspecialchars($editRow['folio']) : '' ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="auth-label">CARRERA</label>
                            <input type="text" name="carrera" class="form-control auth-input" required value="<?= $editRow ? htmlspecialchars($editRow['carrera']) : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">NIVEL</label>
                            <input type="text" name="nivel" class="form-control auth-input" required value="<?= $editRow ? htmlspecialchars($editRow['nivel']) : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="auth-label">INSTITUCIÓN</label>
                            <input type="text" name="institucion" class="form-control auth-input" required value="<?= $editRow ? htmlspecialchars($editRow['institucion']) : '' ?>">
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