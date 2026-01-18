# Bitácora de Comandos del Proyecto

## 2026-01-16 (Viernes)

```powershell
git init
git add .
git commit -m "inicializacion de proyecto Servidor"
git branch -M main
git remote add origin https://github.com/kampexiii/proyectoElJuramentoDeValtherion.git
git push -u origin main
git checkout -b "viernes-16"
git checkout -b "domingo-18"
mkdir docs
mkdir docs/bitacora

# Base de datos (migrations + seeders + sqlite/mysql config)
php artisan migrate
php artisan db:seed

# Generación del entregable (migrations/seeders/models)
PowerShell -NoProfile -ExecutionPolicy Bypass -File "scripts\generate_entregable_db.ps1"
```

## 2026-01-17 (Sábado)

```powershell
# Configuración inicial .env (SQLite)
copy .env.example .env
php artisan key:generate
# (Editar manualmente .env: DB_CONNECTION=sqlite, DB_DATABASE=...etc)
# (Crear database/database.sqlite si no existe)

# Migración limpia y seed
php artisan migrate:fresh --seed

# Instalación de Breeze (Auth)
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build
php artisan migrate
```

## 2026-01-18 (Domingo)

```powershell
# Instalación de Broadcasting (Reverb)
php artisan install:broadcasting --reverb
npm install
npm run build

# Ejecución/Pruebas
php artisan reverb:start
php artisan serve
```
