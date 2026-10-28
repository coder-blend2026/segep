<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: certificados/registro.php');
    exit;
}
$error = '';
$dbPath = __DIR__ . '/certificados/db.sqlite';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new SQLite3($dbPath);
    $user = trim((string) ($_POST['username'] ?? ''));
    $pass = (string) ($_POST['password'] ?? '');
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE username = :u');
    $stmt->bindValue(':u', $user, SQLITE3_TEXT);
    $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    if ($res && password_verify($pass, $res['password'])) {
        $_SESSION['user'] = $res['username'];
        header('Location: certificados/registro.php');
        exit;
    } else {
        $error = 'Credenciales incorrectas';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGED - Acceso Administrativo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-guinda: #621132;
            --color-dorado: #D4C19C;
            --color-dorado-oscuro: #B38E5D;
            --bg-gris: #f4f4f4;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-gris);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-wrap {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .auth-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-top: 5px solid var(--color-guinda);
            text-align: center;
        }

        .auth-card h1 {
            color: var(--color-guinda);
            font-size: 24px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .container-form { text-align: left; }

        .auth-label {
            display: block;
            font-weight: bold;
            color: var(--color-guinda);
            margin-bottom: 8px;
            font-size: 13px;
        }

        /* Contenedor relativo para el input de password */
        .password-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .auth-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        /* Estilo del botón del ojo */
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: var(--color-guinda);
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background-color: var(--color-guinda);
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            transition: background 0.3s;
        }

        .btn-primary:hover { background-color: #4a0d26; }

        .msg-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            margin-top: 15px;
            text-align: center;
        }

        .auth-links {
            margin-top: 25px;
            font-size: 13px;
        }

        .auth-links a {
            color: var(--color-dorado-oscuro);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="auth-wrap">
        <div class="auth-card">
            <h1>SIGED</h1>
            <p class="subtitle">Acceso al Sistema de Gestión</p>
            
            <div class="container-form">
                <form method="post" action="login.php">
                    <label class="auth-label" for="username">USUARIO</label>
                    <input class="auth-input" style="margin-bottom:20px;" id="username" type="text" name="username" required>

                    <label class="auth-label" for="password">CONTRASEÑA</label>
                    <div class="password-wrapper">
                        <input class="auth-input" id="password" type="password" name="password" required>
                        <i class="fa-regular fa-eye toggle-password" id="eye-icon"></i>
                    </div>

                    <button class="btn-primary" type="submit">Entrar al Sistema</button>
                </form>

                <?php if ($error !== ''): ?>
                    <p class="msg-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="auth-links">
                <a href="index.html">Volver al inicio</a>
                <span style="color: #ccc; margin: 0 10px;">|</span>
                <a href="certificados/registro.php">Registros</a>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eye-icon');

        eyeIcon.addEventListener('click', () => {
            // Alternar el tipo de input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Alternar el icono (ojo abierto / ojo tachado)
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>