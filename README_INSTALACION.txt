ENCUÉNTRALO — INSTALACIÓN LOCAL CON XAMPP
==========================================

BASE DE DATOS EXACTA: encuentralo_db
ARCHIVO PARA IMPORTAR: encuentralo_db.sql

REQUISITOS
- XAMPP con Apache + MySQL/MariaDB
- Navegador Chrome o Edge
- Internet durante el análisis con IA (MobileNet/TensorFlow.js se cargan desde CDN)

PASO 1 — COPIAR EL SITIO
1. Descomprime la carpeta Encuentralo_Sitio_Web_Completo.
2. Copia la carpeta completa dentro de C:\\xampp\\htdocs\\
3. Debe quedar: C:\\xampp\\htdocs\\Encuentralo_Sitio_Web_Completo\\

PASO 2 — INICIAR SERVICIOS
1. Abre XAMPP Control Panel.
2. Pulsa Start en Apache.
3. Pulsa Start en MySQL.

PASO 3 — CREAR E IMPORTAR LA BASE DE DATOS
1. Abre http://localhost/phpmyadmin
2. Ve a la pestaña “Importar”.
3. Selecciona el archivo encuentralo_db.sql incluido en esta carpeta.
4. Pulsa “Importar/Continuar”.
El script crea automáticamente la base de datos llamada encuentralo_db, sus tablas y datos de demostración.

PASO 4 — ABRIR EL SITIO
Abre: http://localhost/Encuentralo_Sitio_Web_Completo/

CUENTAS DE DEMOSTRACIÓN
Administrador:
  correo: admin@encuentralo.edu.co
  contraseña: Admin123*

Usuario:
  correo: gabriela@uniclaretiana.edu.co
  contraseña: Encuentra123

También puedes crear usuarios nuevos desde “Crear cuenta”.

CONFIGURACIÓN DE MYSQL
El sitio viene preparado para el XAMPP estándar:
  host: localhost
  base: encuentralo_db
  usuario: root
  contraseña: vacía
Si tu MySQL tiene otra contraseña, edita config/config.php y cambia DB_PASS.

QUÉ FUNCIONA REALMENTE
- Página pública informativa.
- Registro e inicio/cierre de sesión con contraseñas cifradas mediante password_hash.
- Roles de usuario y administrador.
- Publicación real de objetos perdidos y encontrados.
- Carga real de imágenes JPG/PNG/WEBP al servidor.
- Guardado persistente de usuarios, reportes y comentarios en MySQL.
- Feed de publicaciones hechas por todos los usuarios.
- Buscador y filtro perdido/encontrado.
- Comentarios en cada publicación.
- Perfil con los reportes del usuario.
- Panel administrativo con estadísticas y moderación.
- Protección visual de reportes marcados como documentos/datos sensibles.
- Coincidencias automáticas entre reportes por categoría, color, lugar y fecha.
- Reconocimiento visual real con TensorFlow.js + MobileNet al seleccionar una imagen. El resultado y porcentaje se guardan en MySQL.
- Diseño responsive para computador y celular.
- Protección CSRF, consultas preparadas PDO y validación de archivos.

NOTA SOBRE LA IA
MobileNet se ejecuta en el navegador del usuario. La primera vez necesita Internet porque las librerías y el modelo se cargan desde jsDelivr. La IA sugiere qué aparece en la imagen; la verificación final siempre es humana. Esto coincide con la lógica definida para Encuéntralo.

PARA PUBLICARLO EN INTERNET
El mismo código puede instalarse en un hosting PHP/MySQL. Debes crear una base MySQL, importar encuentralo_db.sql y modificar config/config.php con las credenciales del hosting. También debes ajustar BASE_URL si cambia el nombre de la carpeta o se instala en la raíz del dominio.
