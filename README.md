<div align="center">

# Juego Móvil Valtherion

Base del futuro juego móvil de El Juramento de Valtherion.

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Status](https://img.shields.io/badge/status-reconversion%20movil-2ea44f?style=for-the-badge)](#estado)

</div>

## Sobre el proyecto

Este repositorio parte de una base web RPG y se orientará a una experiencia móvil. Es distinto de `El-Juramento-Valtherion-Universo-Steam`, que conserva el universo y la línea de juego de escritorio.

Repositorio:

- https://github.com/kampexiii/proyectoElJuramentoDeValtherion

## Sistemas existentes

- Misiones ramificadas.
- Personaje y progresión.
- Combate PvE por turnos.
- Inventario, equipamiento y tienda.
- Panel de administración de contenido.

## Stack

| Área | Tecnología |
| --- | --- |
| Backend | Laravel |
| Frontend/build | Vite |
| Datos | MySQL/MariaDB |
| Objetivo futuro | Juego móvil |

## Inicio rapido

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

## Roadmap

- Definir loop jugable móvil.
- Separar narrativa, combate y progresión.
- Diseñar pantallas móviles.
- Decidir motor o enfoque técnico futuro.

## Seguridad

- No subir `.env`.
- Mantener datos demo limpios.

## Estado

Base técnica pendiente de reconversión a juego móvil.
