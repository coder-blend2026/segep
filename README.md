# SIGED

## Ejecutar con Docker

Necesitas Docker Desktop con Docker Compose habilitado. Desde esta carpeta ejecuta:

```sh
docker compose up --build -d
```

La aplicación estará disponible en <http://localhost:8080>. La primera vez, crea
el usuario administrador con:

```sh
docker compose exec -u www-data web php certificados/crear_usuario_inicial.php
```

El script muestra las credenciales iniciales (`admin` / `Admin2026!`). Después
puedes iniciar sesión y gestionar certificados. Cambia la contraseña inicial
antes de exponer el sistema fuera de tu equipo.

La aplicación usa `certificados/db.sqlite` directamente; los datos se conservan
en ese archivo al detener o reconstruir el contenedor. Se crea una copia de
respaldo antes de aplicar cambios a la estructura de la base. Para detener la aplicación:

```sh
docker compose down
```

La consulta de folios está disponible en `documentos.php` y se procesa en el
servidor con PHP; los datos mostrados corresponden a los certificados guardados
en la base local.

Para borrar también la base de datos y sus registros:

```sh
docker compose down -v
```
