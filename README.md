# El Juramento de Valtherion 🛡️⚔️

> RPG narrativo en web desarrollado con **Laravel 12**, enfocado en la toma de decisiones, progresión de personajes y combates por turnos con estética Pixel Art.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://www.php.net/)

## 📌 Sobre el Proyecto

**El Juramento de Valtherion** es un juego web donde cada decisión cuenta. Los jugadores exploran un mundo de fantasía, mejoran su equipo y se enfrentan a desafíos que cambian según sus elecciones previas.

### ✨ Funcionalidades Principales

-   **Misiones con Ramificaciones**: Elige tu camino y asume las consecuencias.
-   **Sistema de Personajes**: Atributos, niveles y gestión de inventario.
-   **Combates por Turnos**: Sistema táctico estilo RPG clásico.
-   **Mensajería en Tiempo Real**: Chat global y de grupo usando Laravel Reverb.
-   **Modos de Juego**: Experiencia offline (historia) y online (misiones en pareja y duelos PvP).
-   **Panel de Administración**: Gestión completa de contenido (usuarios, ítems, misiones).

---

## 🚀 Inicio Rápido

### Requisitos Técnicos

-   **PHP 8.2+**
-   **Composer** (gestor de dependencias PHP)
-   **Node.js 20+** y **NPM** (para assets y chat en vivo)
-   **MySQL / MariaDB** (base de datos)
-   **DBeaver** (recomendado para ver la BD)

### Instalación en Local

1. **Clonar el proyecto:**
    ```bash
    git clone https://github.com/kampexiii/proyectoElJuramentoDeValtherion.git
    cd proyectoElJuramentoDeValtherion
    ```
2. **Instalar dependencias:**
    ```bash
    composer install
    npm install
    ```
3. **Configurar el entorno:**
    - Copia `.env.example` a `.env`.
    - Crea una base de datos llamada `valtherion`.
    - Configura las credenciales de la BD en el archivo `.env`.
4. **Levantar el proyecto:**
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    npm run dev
    php artisan serve
    ```

---

## 📂 Estructura del Proyecto

Para entender cómo está organizado el desarrollo, consulta la carpeta de **[Documentación](./docs/)**, que incluye:

-   **[Calendario de Trabajo](./docs/calendario.md)**: Planificación de 6 semanas.
-   **[Modelo de Base de Datos](./docs/MODELO_BD.md)**: Tablas y relaciones.
-   **[Guía de Instalación Detallada](./docs/GUIA_INSTALACION.md)**.

---

## 🛠️ Tecnologías Utilizadas

-   **Backend**: Laravel 12 (PHP)
-   **Frontend**: Blade, CSS (Pixel Art focus), JavaScript (Vite)
-   **Real-time**: Laravel Reverb + Laravel Echo
-   **Auth**: Laravel Breeze
-   **Diseño**: Figma, Photoshop

---

## 👨‍💻 Autor

Proyecto desarrollado por **Kampexiii** como proyecto final para 2º DAW.

-   GitHub: [@kampexiii](https://github.com/kampexiii)
