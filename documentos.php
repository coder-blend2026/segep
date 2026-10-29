<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once __DIR__ . '/certificados/database.php';

$folioInput = $_GET['folio'] ?? '';
$folio = is_string($folioInput) ? strtoupper(trim($folioInput)) : '';
$certificado = null;
$error = null;
$buscar = array_key_exists('folio', $_GET);

if ($buscar && $folio !== '') {
    $db = openCertificatesDatabase();
    $stmt = $db->prepare('SELECT * FROM certificados WHERE folio = :folio COLLATE NOCASE LIMIT 1');
    if ($stmt === false) {
        throw new RuntimeException('No fue posible preparar la consulta del folio.');
    }
    $stmt->bindValue(':folio', $folio, SQLITE3_TEXT);
    $result = $stmt->execute();

    if ($result === false) {
        throw new RuntimeException('No fue posible consultar el folio.');
    }

    $certificado = $result->fetchArray(SQLITE3_ASSOC) ?: null;
    if ($certificado === null) {
        $error = 'La información solicitada no se encuentra en nuestros registros.';
    }
} elseif ($buscar) {
    $error = 'Ingresa un folio para realizar la búsqueda.';
}

$e = static fn ($value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$nombre = trim((string) ($certificado['nombre'] ?? ''));
if ($nombre === '' && $certificado !== null) {
    $nombre = trim(implode(' ', array_filter([
        $certificado['nombres'] ?? '',
        $certificado['primer_apellido'] ?? '',
        $certificado['segundo_apellido'] ?? '',
    ], static fn ($part): bool => trim((string) $part) !== '')));
}
$nivel = $certificado['tipo_certificacion'] ?? $certificado['nivel'] ?? '';
$detalles = [
    'Tipo de registro' => $certificado['tipo'] ?? '',
    'Tipo de documento' => $certificado['tipo_certificado'] ?? '',
    'Nivel de estudios' => $nivel,
    'Carrera' => $certificado['carrera'] ?? '',
    'Institución emisora' => $certificado['institucion'] ?? '',
    'Fecha de registro SIGED' => $certificado['fecha_certificado'] ?? '',
    'Promedio / resultado' => $certificado['promedio'] ?? '',
    'Texto del resultado' => $certificado['promedio_texto'] ?? '',
    'Estatus del documento' => $certificado['estatus'] ?? '',
    'ID tipo de certificado' => $certificado['cert_tipo_id'] ?? '',
    'Inicio del periodo de estudios' => $certificado['periodo_inicio'] ?? '',
    'Fin del periodo de estudios' => $certificado['periodo_fin'] ?? '',
    'Tipo de perfil laboral' => $certificado['tipo_perfil_laboral'] ?? '',
    'Perfil laboral' => $certificado['nombre_perfil_laboral'] ?? '',
];
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEP | SIGED</title>
    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/styles/main.css" rel="stylesheet">
    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/images/favicon.ico" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/gm/accesibilidad/css/gobmx-accesibilidad.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar-settings.css">
    <link rel="stylesheet" href="css/global_siged.css">
    <link rel="stylesheet" href="css/documentos.css?v=20261029-2">
    <style>
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .btn-width { width: 100%; }
        .detail-value { overflow-wrap: anywhere; }
    </style>
</head>
<body class="<?= !$buscar ? 'search-empty' : '' ?>">
    <div id="div-sub-navbar">
        <?php require __DIR__ . '/sub-navbar_siged.html'; ?>
    </div>

    <div>
        <img class="img-background" src="images/bnr_documentos.html" alt="">
    </div>

    <div class="container container-top">
        <div class="row align-items-center">
            <div class="column col-lg-12">
                <h3>Sistema de Información y Gestión Educativa</h3>
                <h2 class="mt-1">Validación de documentos académicos electrónicos</h2>
            </div>
        </div><br><br>
    </div>

    <div class="container bc_title" style="margin-top: 0; margin-bottom: -3vh">
        <div class="row">
            <div class="col-sm-8">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index-2.html">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="index-2.html#consultas">Consultas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Documentos académicos electrónicos</li>
                </ol>
            </div>
        </div>
    </div>
    <br>
    <main id="search">
        <div class="container container-form mt-2">
            <form class="form" method="get" action="/documentos.php">
                <div class="row">
                    <div class="column col-lg-6">
                        <div class="form-group mb-2">
                            <label for="folio" class="sr-only">Folio</label>
                            <input id="folio" name="folio" type="text" placeholder="Ingrese el folio" class="form-control mt-3" value="<?= $e($folio) ?>" required>
                        </div>
                    </div>
                    <div class="column col-lg-3 col-md-6 col-sm-6">
                        <button id="btn-buscar" type="submit" class="btn btn-active btn-width mt-2">Buscar</button>
                    </div>
                    <div class="column col-lg-3 col-md-6 col-sm-6">
                        <a id="btn-restablecer" href="/documentos.php" class="btn btn-secondary active btn-width mt-2">Restablecer</a>
                    </div>
                </div>
            </form>

            <div class="mt-3">
                <a href="archivos/UbicaFolio.pdf" class="btn-link link-color" target="_blank" rel="noopener" title="Ubica el folio en tu certificado">Ubica el folio en tu certificado</a>
            </div>
        </div>

        <?php if (!$buscar || $folio === ''): ?>
            <div class="container mt-3" id="notice-certificates">
                <div class="alert alert-info" role="status">
                    <p><i class="bi bi-info-circle" aria-hidden="true"></i> <span>Los certificados estarán disponibles para su consulta un día después de su emisión.</span></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error !== null): ?>
            <section class="container mt-4" aria-live="polite">
                <div class="top-buffer-submenu">
                    <p class="titulo_subtexto">Información no encontrada</p>
                    <hr class="red">
                    <section style="margin: 20px;">
                        <div class="alert alert-danger">
                            <p><strong><?= $e($error) ?></strong></p>
                            <?php if ($folio !== ''): ?>
                                <p>Verifica los datos que ingresaste y vuelve a intentarlo.</p>
                            <?php endif; ?>
                            <a class="btn btn-danger bnt-nvo-intento" href="documentos.php">Volver a intentarlo</a>
                        </div>
                    </section>
                    <?php if ($folio !== ''): ?>
                        <p class="sr-only">Folio consultado: <?= $e($folio) ?></p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($certificado !== null): ?>
            <div class="container" id="divResultsWithData">
                <section id="sectionResultWithData" style="margin-top: 5vh" aria-live="polite">
                    <div class="mb-5">
                        <div class="row">
                            <div class="column col-12">
                                <p class="titulo_subtexto" title="Detalle del documento">Detalle del documento</p>
                                <hr class="red">
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <?php
                            $detalles = ['Nombre' => $nombre] + $detalles;
                            $detalles['Folio'] = $certificado['folio'] ?? '';
                            foreach ($detalles as $etiqueta => $valor):
                                if (trim((string) $valor) === '') {
                                    continue;
                                }
                            ?>
                                <div class="column col-lg-3 col-md-12 mb-3"><strong><?= $e($etiqueta) ?>:</strong></div>
                                <div class="column col-lg-9 col-md-12 mb-3 detail-value"><?= $e($valor) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div id="informacion">
                        <hr class="red">
                        <div class="alert alert-info alert-info-certificado">
                            <p><strong>¡Atención!</strong> La información de los certificados mostrada en este portal es resultado de la información que envían las autoridades educativas. Si no coincide con tu documento, ponte en contacto con la autoridad de tu centro escolar.</p>
                            <p>La información presentada es de carácter informativo y sirve para verificar la validez del documento impreso. El documento oficial es el que entrega tu centro escolar.</p>
                        </div>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </main>
  
    <footer class="main-footer" role="contentinfo" style="background-color:#12322b; color:#fff; padding:2rem 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <strong>Secretaría de Educación Pública</strong><br>
                    <span>Sistema de Información y Gestión Educativa (SIGED)</span>
                </div>
                <div class="col-md-6 text-md-right">
                    <a href="https://www.gob.mx/sep" target="_blank" rel="noopener" style="color:#fff;">Portal de la SEP</a>
                    <span aria-hidden="true">&nbsp;|&nbsp;</span>
                    <a href="https://www.gob.mx/" target="_blank" rel="noopener" style="color:#fff;">gob.mx</a>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://framework-gb.cdn.gob.mx/gm/v3/assets/js/gobmx.js"></script>
    <script src="https://framework-gb.cdn.gob.mx/gm/accesibilidad/js/gobmx-accesibilidad.min.js"></script>
</body>
</html>
