<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Proceso de instalación del proyecto
   
### Clonar el Repositorio de Git

Para clonar el repositorio de Git, ejecuta el siguiente comando en tu terminal:

```bash
git clone https://github.com/alkemyTech/MSM-PHP-T3.git
```
### Acceder a la carpeta del Proyecto

Usá el comando cd para moverte al directorio recién clonado:

```bash
cd MSM-PHP-T3
```

### Instalar Dependencias

Ejecutá el siguiente comando desde la terminal para instalar todas las dependencias de Laravel y las bibliotecas de terceros necesarias:

```bash
composer install
```
Asegurate de tener Composer instalado en tu sistema antes de ejecutar este comando.

### Copiar el Archivo de Configuración .env

Laravel utiliza un archivo de configuración .env para almacenar variables de entorno y configuraciones específicas del entorno. Podés copiar el archivo de ejemplo .env.example a un nuevo archivo .env:

```bash
cp .env.example .env
```

Luego, abrí el archivo .env y configurá las variables de entorno según tu entorno de desarrollo, como la configuración de la base de datos.

### Generar una Clave de Aplicación

Laravel utiliza una clave de aplicación para cifrar los datos. Ejecutá el siguiente comando para generar una nueva clave de aplicación:

```bash
php artisan key:generate
```

### Configurar la Base de Datos

Abrí el archivo .env y configurá la conexión a la base de datos con los detalles correspondientes, como el nombre de la base de datos, el usuario y la contraseña.

### Ejecutar las Migraciones de la Base de Datos

Para crear las tablas de la base de datos, ejecutá las migraciones de Laravel con el siguiente comando:

php artisan migrate
Iniciar el Servidor de Desarrollo

Podés iniciar un servidor de desarrollo de Laravel con el siguiente comando:

```bash
php artisan serve
```

Esto iniciará el servidor de desarrollo en http://localhost:8000/ (si es que no configuraste otro puerto), y vas a poder acceder a tu aplicación a través de un navegador web.