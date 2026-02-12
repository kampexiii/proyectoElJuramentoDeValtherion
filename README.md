# El Juramento de Valtherion 🛡️⚔️

> RPG narrativo en web desarrollado con **Laravel 12**, enfocado en la toma de decisiones, progresion de personajes y combates por turnos con estetica Pixel Art.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://www.php.net/)

## 📌 Sobre el Proyecto

**El Juramento de Valtherion** es un juego web donde cada decision cuenta. Los jugadores exploran un mundo de fantasia, progresan mediante estadisticas y recursos, mejoran su equipamiento y se enfrentan a misiones narrativas ramificadas que desembocan en combates PvE contra jefes finales.  
Ademas, el proyecto incluye un panel de administracion para gestionar el contenido del juego (misiones, nodos, elecciones, bosses, items y usuarios) sin depender de cambios directos en base de datos.

### ✨ Funcionalidades Principales

- **Misiones con ramificaciones (grafo)**: sistema por nodos y elecciones, con rutas distintas segun decisiones.
- **Sistema de personaje (1:1 con usuario)**: creacion obligatoria, estadisticas, progresion y estado jugable persistente.
- **Razas con restricciones por rol/plan**: seleccion durante creacion y reparto inicial de puntos bajo reglas definidas.
- **Combates por turnos (PvE)**: boss final asociado a mision + resolucion y recompensas.
- **Inventario + consumibles**: objetos obtenidos o comprados, consulta de propiedades y acciones.
- **Equipamiento por slots**: bonificaciones reales sobre estadisticas al equipar.
- **Tienda**: catalogo con rarezas, precios y bonificaciones integrado con inventario.
- **Mensajeria en tiempo real**: chat interno usando **Laravel Reverb** + **Laravel Echo** (cuando el servicio esta activo).
- **PvP**: modulo competitivo integrado en la zona privada.
- **Panel de administracion**: CRUD completo de contenido + control de publicacion y validacion de coherencia (grafo de misiones).

## ✅ Nota tecnica (try/catch y excepciones en Laravel)

En Laravel, la gestion de errores y excepciones ya viene centralizada mediante el manejador global del framework (Handler), por lo que **no es necesario** envolver toda la aplicacion en `try/catch` como en PHP "plano".  
Se utiliza `try/catch` unicamente cuando interesa controlar un caso concreto (mensaje especifico, fallback, rollback manual, etc.). El resto queda cubierto por la gestion estandar de excepciones y logs de Laravel.

---

## 🚀 Inicio Rapido

### Requisitos Tecnicos

- **PHP 8.2+**
- **Composer** (gestor de dependencias PHP)
- **Node.js 20+** y **NPM** (para assets y front)
- **Extensiones SQLite en PHP**: `pdo_sqlite` y `sqlite3`
- **XAMPP** (opcional, recomendado en Windows) o PHP CLI operativo
- **Navegador moderno** (Chrome/Edge/Firefox)

> Base de datos: **SQLite (fichero portable)**, no MySQL.

### Instalacion en Local (Clonado)

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
    ```bash
    copy .env.example .env
    php artisan key:generate
    ```
4. **Configurar SQLite (en .env):**
    ```bash
    DB_CONNECTION=sqlite
    DB_DATABASE=database/db_proyecto_sevillano.sqlite
    ```
5. **Crear el fichero si no existe:**
    ```powershell
    New-Item -ItemType File .\database\db_proyecto_sevillano.sqlite -Force
    ```
6. **Migraciones y seeders (recomendado para evaluacion):**
    ```bash
    php artisan migrate:fresh --seed
    php artisan optimize:clear
    ```
7. **Assets:**
    - Modo evaluacion (sin procesos en segundo plano):
        ```bash
        npm run build
        ```
    - Modo desarrollo (hot reload):
        ```bash
        npm run dev
        ```
8. **Levantar el proyecto:**
    ```bash
    php artisan serve
    ```
9. **Abrir en navegador:**
    ```text
    http://127.0.0.1:8000
    ```

### Laravel Reverb (Chat en tiempo real)

Para probar el chat en tiempo real, arrancar Reverb en otra terminal:

```bash
php artisan reverb:start
```

### 👤 Usuarios de prueba (seeders)

**Admin**

- Email: pablosevillanoa90@gmail.com
- Password: 12345678
- Role: admin
- Plan: premium

**Jugador**

- Registro libre desde la aplicacion.

## 📂 Estructura / Documentacion

Para ver la planificacion, diagramas, bitacora y documentacion tecnica, consulta:

- /docs/ (bitacora, diagramas, material de memoria y soporte del proyecto)

## 🛠️ Tecnologias Utilizadas

- Backend: Laravel 12 (PHP)
- Frontend: Blade + Tailwind CSS + JavaScript (Vite)
- Real-time: Laravel Reverb + Laravel Echo
- Auth: Laravel Breeze
- BD: SQLite (fichero portable)
- Diseno: Figma, Photoshop
- Control de versiones: Git / GitHub

## 👨‍💻 Autor

Proyecto desarrollado por Kampexiii como proyecto final para 2º DAW.

GitHub: @kampexiii
