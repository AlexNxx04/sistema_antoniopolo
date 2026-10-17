# 🚀 Sistema Web para el Control Operativo de Unidades de Transporte Terrestre

Este proyecto de titulación para la **Universidad Estatal de Bolívar** (Cantón Guaranda) consiste en una plataforma web empresarial diseñada para optimizar, auditar y monitorear el flujo logístico y operativo de las unidades de transporte terrestre. El núcleo del sistema se basa en la validación y control geográfico mediante puntos de geolocalización establecidos de manera estratégica en la provincia.

---

## 🛠️ Stack Tecnológico & Arquitectura

El sistema fue construido siguiendo las mejores prácticas de la ingeniería de software, asegurando modularidad, mantenibilidad y escalabilidad:

*   **Backend (Core):** Laravel (PHP) bajo el patrón de diseño estricto **Modelo-Vista-Controlador (MVC)**, garantizando una separación clara de la lógica de negocio y las interfaces.
*   **Frontend y Estilos:** Tailwind CSS y Blade UI, empaquetados e integrados eficientemente a través de **Vite** para lograr tiempos de carga optimizados y una experiencia mobile-first fluida.
*   **Gestión de Base de Datos:** Motor relacional **SQL** administrado mediante migraciones y seeders nativos de Laravel, aplicando restricciones de integridad (Foreign Keys) y optimización de consultas.
*   **Modelado del Sistema:** Arquitectura respaldada formalmente mediante diagramación estructural y Casos de Uso integrados directamente en el flujo del repositorio.

---

## 📈 Características y Funcionalidades Principales

*   **📍 Control por Geolocalización:** Gestión y auditoría de rutas mediante la verificación de unidades en coordenadas y puntos de control previamente establecidos.
*   **🔒 Seguridad y Roles:** Autenticación robusta y protección de endpoints mediante middlewares nativos contra vulnerabilidades web comunes (CSRF, XSS, inyecciones SQL).
*   **📊 Migraciones Modulares:** Estructura de base de datos versionada en código, permitiendo la alteración y adición ágil de columnas y llaves foráneas (`add_column.php`, `add_fk.php`).
*   **🔔 Sistema de Notificaciones Operativas:** Lógica interna optimizada para el procesamiento y corrección de alertas operativas dentro de los grupos de transporte.

---

## 📁 Guía de Estructura del Repositorio

Para facilitar la auditoría técnica del código, los componentes principales se distribuyen de la siguiente manera:

*   📂 `/app/Http/Controllers`: Contiene la lógica y el comportamiento de los endpoints y controladores del sistema.
*   📂 `/app/Models`: Definición de las entidades relacionales y las conexiones de la base de datos.
*   📂 `/routes`: Definición y enrutamiento limpio de los componentes de la plataforma (`web.php`, `api.php`).
*   📂 `/database/migrations`: Scripts SQL automatizados para el despliegue ordenado de las tablas del sistema.
*   📂 `/resources`: Vistas interactivas, componentes Blade y archivos de estilo CSS.
