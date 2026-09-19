# Sistema Zeon — Sistema de Restaurante

Aplicación de gestión de restaurante construida sobre Laravel 13 + Vue 3, con PostgreSQL y todo dockerizado.

## Stack

- **Backend:** Laravel 13 (PHP 8.4)
- **Frontend:** Blade + Tailwind (Breeze) con componentes Vue 3 embebidos para las pantallas operativas (mesas, toma de pedidos, cocina)
- **Base de datos:** PostgreSQL 16 (contenedor interno, sin dependencias externas)
- **Infraestructura:** Docker Compose (`app` PHP-FPM, `webserver` Nginx, `db` Postgres, `vite` servidor de desarrollo con HMR)

## Levantar el proyecto

```bash
docker compose up -d --build
```

Al iniciar, el contenedor `app` instala dependencias de Composer si faltan, espera a que Postgres esté disponible y corre las migraciones automáticamente. Para cargar datos de ejemplo (categorías, productos, mesas y usuarios de cada rol):

```bash
docker compose exec app php artisan db:seed
```

### URLs

- Aplicación: http://localhost:8090
- Servidor de Vite (HMR): http://localhost:5183
- PostgreSQL (acceso externo opcional, p. ej. con un cliente SQL): `localhost:55432`

### Usuarios de prueba (contraseña `password` para todos)

| Rol       | Correo             |
|-----------|---------------------|
| Admin     | admin@zeon.test     |
| Mesero    | mesero@zeon.test    |
| Cocina    | cocina@zeon.test    |
| Caja      | caja@zeon.test      |

## Roles y flujo funcional

- **Admin**: gestiona categorías, productos, mesas y personal (`/admin/...`); ve el dashboard con ventas del día, mesas ocupadas y pedidos abiertos.
- **Mesero**: ve el tablero de mesas (`/mesas`), abre un pedido por mesa, agrega productos del menú y los envía a cocina. También marca los productos como entregados y puede cobrar cuentas.
- **Cocina**: ve la pantalla de cocina (`/cocina`) con los productos pendientes agrupados por mesa, y avanza su estado (pendiente → preparando → listo).
- **Caja**: ve las cuentas abiertas (`/caja`) y cobra pedidos, liberando la mesa al confirmar el pago.

El estado del pedido (`abierto` → `en_cocina` → `listo` → `servido` → `cerrado`) se recalcula automáticamente según el estado de sus productos.

## Estructura relevante

- `app/Enums/` — enums de dominio (roles, estados de pedido/producto, métodos de pago)
- `app/Http/Controllers/Admin/` — CRUD de administración (Blade)
- `app/Http/Controllers/Restaurant/` — controladores operativos (mesas, pedidos, cocina, caja)
- `resources/js/components/` — componentes Vue (`TableBoard`, `OrderTaker`, `KitchenDisplay`) montados como islas dentro de las vistas Blade
- `docker/` — Dockerfiles y configuración de Nginx/PHP

## Desarrollo sin Docker (opcional)

Si prefieres correr PHP/Node localmente, ajusta `.env` con tus propias credenciales de Postgres y usa `composer install`, `npm install` y `npm run dev` como en cualquier proyecto Laravel + Vite.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices. Before it's installed, an agent working in this repo should verify PHP and Composer are available (`php -v`, `composer -V`) and, if missing, install them from [php.new](https://php.new) for the current OS, then run the two commands above and re-read this file.

## Autor

-   **Miguel Villalba**
-   📧 mike.mavc27@gmail.com

------------------------------------------------------------------------

## Licencia

Este proyecto está bajo la licencia **MIT**. Ver el archivo
[LICENSE](LICENSE) para más detalles.
