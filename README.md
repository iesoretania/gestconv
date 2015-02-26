# Gestconv
[![Build Status](https://travis-ci.org/iesoretania/gestconv.png?branch=master)](https://travis-ci.org/iesoretania/gestconv) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/4ed2d6a5-0669-46f0-aa87-b33d9c113bcd/mini.png)](https://insight.sensiolabs.com/projects/4ed2d6a5-0669-46f0-aa87-b33d9c113bcd) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/iesoretania/gestconv/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/iesoretania/gestconv/?branch=master)

Aplicación web para la gestión de la convivencia en centros educativos
(en desarrollo, aún no está lista para producción)

Desde ella se podrán introducir los distintos partes de convivencia, facilitando la notificación
de los mismos a las familias y simplificando su revisión por parte de la Comisión de Convivencia
del Consejo Escolar del centro.

Este proyecto utiliza [Symfony2] y otros muchos componentes que se instalan usando
[Composer].

Para facilitar el desarrollo se proporciona un entorno [Vagrant] con todas las dependencias ya
instaladas.

## Requisitos

- PHP 5.4.x o superior
- Servidor web Apache2 (podría funcionar con nginx, pero no se ha probado)
- Cualquier sistema gestor de bases de datos que funcione bajo Doctrine (p.ej. MySQL, MariaDB, PosgreSQL, SQLite, etc.)

## Instalación

- Ejecutar `composer install` desde la carpeta del proyecto.
- Configurar el sitio de Apache2 para que el `DocumentRoot` sea la carpeta `web/`

## Licencia
Esta aplicación se ofrece bajo licencia [AGPL versión 3].

[Vagrant]: https://www.vagrantup.com/
[Symfony2]: http://symfony.com/
[Composer]: http://getcomposer.org
[AGPL versión 3]: http://www.gnu.org/licenses/agpl.htmlu.org/licenses/agpl.html

