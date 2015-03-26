# ![Gestconv](/src/AppBundle/Resources/public/images/titulo.png)
[![Build Status](https://travis-ci.org/iesoretania/gestconv.svg?branch=master)](https://travis-ci.org/iesoretania/gestconv) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/4ed2d6a5-0669-46f0-aa87-b33d9c113bcd/mini.png)](https://insight.sensiolabs.com/projects/4ed2d6a5-0669-46f0-aa87-b33d9c113bcd) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/iesoretania/gestconv/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/iesoretania/gestconv/?branch=master)
[![Codacy Badge](https://www.codacy.com/project/badge/aeb2f9910f8e4120b76957664d86baa4)](https://www.codacy.com/public/lrlopez/gestconv)

Aplicación web para la gestión de la convivencia en centros educativos
(en desarrollo, aún no está lista para producción). Actualmente se encuentra desplegada en pruebas
en el I.E.S. Oretania de Linares (Jaén, España).

Desde ella se podrán introducir los distintos partes de convivencia, facilitando la notificación
de los mismos a las familias y simplificando su revisión por parte de la Comisión de Convivencia
del Consejo Escolar del centro.

Este proyecto utiliza [Symfony2] y otros muchos componentes que se instalan usando
[Composer] y [Bower].

Para facilitar el desarrollo se proporciona un entorno [Vagrant] con todas las dependencias ya
instaladas.

## Requisitos

- PHP 5.4.x o superior
- Servidor web Apache2 (podría funcionar con nginx, pero no se ha probado)
- Cualquier sistema gestor de bases de datos que funcione bajo Doctrine (p.ej. MySQL, MariaDB, PosgreSQL, SQLite, etc.)
- PHP [Composer]
- [Bower]

## Instalación

- Ejecutar `composer install` desde la carpeta del proyecto.
- Ejecutar `bower install`.
- Configurar el sitio de Apache2 para que el `DocumentRoot` sea la carpeta `web/`
- Modificar el fichero `parameters.yml` con los datos de acceso al sistema gestor de bases de datos.
- Ejecutar `app/console assets:install` para completar la instalación de los recursos en la carpeta `web/`.
- Para crear la base de datos: `app/console doctrine:database:create`.
- Para crear las tablas: `app/console doctrine:schema:create`.
- Para insertar los datos iniciales: `app/console doctrine:fixtures:load`.

## Importación de datos de alumnado y profesorado

La aplicación permite una importación en lote de todo el alumnado y profesorado del centro a partir de la
información extraída de la plataforma Séneca de la Junta de Andalucía.

## Entorno de desarrollo

Para poder ejecutar la aplicación en un entorno de desarrollo basta con tener [Vagrant] instalado junto con [VirtualBox]
y ejecutar el comando `vagrant up`. La aplicación será accesible desde la dirección http://192.168.33.10/

## Licencia
Esta aplicación se ofrece bajo licencia [AGPL versión 3].

[Vagrant]: https://www.vagrantup.com/
[VirtualBox]: https://www.virtualbox.org
[Symfony2]: http://symfony.com/
[Composer]: http://getcomposer.org
[AGPL versión 3]: http://www.gnu.org/licenses/agpl.html
[Bower]: http://bower.io/
