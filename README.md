#### 🚍 Sistema Web y PWA para el Control Operativo de Unidades de Transporte Terrestre

Plataforma (**Web + Progressive Web App**) desarrollada para la **Universidad Estatal de Bolívar** (Cantón Guaranda) para el monitoreo y control en tiempo real de flotas de transporte terrestre con soporte **Offline-First**. 

#### 🛠 Stack Tecnológico & Arquitectura

* **Backend:** Laravel (PHP) con patrón **MVC** y APIs RESTful.
* **Frontend & PWA:** TypeScript, Blade UI, Tailwind CSS, Service Workers y almacenamiento local empaquetados con **Vite**.
* **Tiempo Real:** WebSockets para transmisión de estados y geolocalización.
* **Base de Datos:** Motor relacional **SQL** con migraciones y claves foráneas.

#### 🚀 Características Principales

* **📍 Geolocalización y Modo Offline-First:** Almacenamiento local de coordenadas sin conexión a internet y sincronización automática al reconectar.
* **⚡ Monitoreo en Tiempo Real (WebSockets):** Actualización instantánea de salidas y novedades en el panel central.
* **🔒 Seguridad:** Control de roles (administradores, despachadores, conductores) mediante middlewares de Laravel (CSRF, XSS).
* **🔔 Notificaciones:** Alertas automatizadas para seguimiento de unidades.

#### 📐 Diagrama de Casos de Uso

 

#### 📁 Estructura del Repositorio

* app/Http/Controllers/ — Lógica de control y sincronización de rutas.
* app/Models/ — Modelos Eloquent y relaciones.
* routes/ — Rutas web y endpoints API.
* database/migrations/ — Esquema de tablas y puntos de control.
* resources/ — Vistas Blade, TypeScript/PWA y estilos Tailwind.

#### ⚙️ Instalación Local

bash

git clone https://github.com/AlexNxx04/sistema_antoniopolo.git
cd sistema_antoniopolo
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate
npm run build && php artisan serve
