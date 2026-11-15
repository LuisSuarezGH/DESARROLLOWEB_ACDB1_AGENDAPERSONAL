# Desarrollo WEB - ACDB1
# Agenda Personal de Contactos

## Descripción

Aplicación web desarrollada en PHP y MySQL que permite
a los usuarios registrarse, iniciar sesión y administrar
su propia agenda personal de contactos.

El sistema implementa autenticación, manejo de sesiones
y restricciones de acceso para garantizar la privacidad
de la información de cada usuario.

## Tecnologías utilizadas

- PHP 8
- MySQL / MariaDB
- HTML5
- CSS3
- JavaScript
- XAMPP
- Visual Studio Code

## Requisitos

- PHP 8 o superior.
- Apache.
- MySQL.
- XAMPP.
- Navegador web.

## Instalación

1. Instalar XAMPP.
2. Iniciar Apache y MySQL. (veririfcar puertos disponibles)
3. Copiar la carpeta ACDB1.
4. Abrir phpMyAdmin.
5. Importar el archivo database.sql ubicado en la carpeta config/database.sql
6. Configurar las credenciales en config/database.php.
7. Abrir el navegador.
8. Ingresar a(revisar direccion en la cual se descargo la carpeta ACDB1):

http://localhost/ACDB1/

## Funcionalidades

- Registro de usuarios.
- Inicio de sesión.
- Cierre de sesión.
- Registro de contactos.
- Edición de contactos.
- Eliminación de contactos.
- Protección de páginas privadas.
- Separación de contactos por usuario.

## Seguridad

- password_hash().
- password_verify().
- Consultas preparadas PDO.
- Validación de formularios en PHP.
- Manejo de sesiones.
- Protección CSRF.
- Verificación de propiedad de contactos.
- Escape de datos HTML.
