# Calendario ultra-detalado — El juramento de Valtherion (Laravel 12)

Regla: 1h diaria. Viernes: 3h.
Cierre SIEMPRE con bitácora + commit. Sin excusas.

Herramientas fijas:

- Trello (seguimiento diario)
- VSCode (código)
- Terminal (composer, npm, artisan, git)
- DBeaver (BD + ERD + export SQL)
- Figma o Penpot (diseño de pantallas y flujo)
- Photoshop (sprites + UI pixel)
- Recomendadas extra:
    - diagrams.net (si quieres un ER a mano además del de DBeaver)
    - Squoosh (optimizar PNG/WebP)
    - Postman/Insomnia (probar endpoints rápidos, opcional)

Estructura docs (desde el día 1):

- docs/bitacora/
- docs/GUIA_INSTALACION.md
- docs/GUIA_USUARIO.md
- docs/MODELO_BD.md
- docs/COMANDOS.md
- docs/CHECKLIST_ENTREGA.md
- docs/ERD/ (capturas/export de DBeaver)

Regla de bitácora:

- Cada día crear: docs/bitacora/YYYY-MM-DD.md (usando template)
- En la bitácora siempre: herramientas + comandos + archivos tocados + motivo + pruebas + resultado + siguiente paso

Convención commits:

- 1 commit mínimo por día (viernes: 2–4)
- Mensajes cortos y claros, estilo:
    - chore: ...
    - docs: ...
    - feat: ...
    - fix: ...
    - refactor: ...

Repo:

- https://github.com/kampexiii/proyectoElJuramentoDeValtherion

---

## Semana 0 — Arranque y base limpia (2026-01-16 a 2026-01-18)

### 2026-01-16 (Vie) — 3h — Proyecto + Schema Juego + Entrega DB

**Hecho:**

- Creación proyecto Laravel 12.
- Configuración de Git y ramas.
- Migraciones completas del juego (razas, items, misiones...).
- Seeders base (Archaon y datos mínimos).
- Generación de entregable automático (`docs/entregable_db.md`).

**Bitácora:** `docs/bitacora/2026-01-16.md`

### 2026-01-17 (Sáb) — 1h — Configuración Real + Auth (Breeze)

**Hecho:**

- Configuración real de `.env` (SQLite local).
- `php artisan migrate:fresh --seed` (verificado).
- Instalación de Laravel Breeze (Blade).
- Compilación de assets (`npm run build`).
- Docs operativos: `COMANDOS.md` y `CHECKLIST_ENTREGA.md`.

**Bitácora:** `docs/bitacora/2026-01-17.md`

### 2026-01-18 (Dom) — 1h — Reverb + Documentación Modelo

**Hecho:**

- Instalación de Laravel Reverb (Broadcasting).
- Documentación del modelo de datos (`docs/MODELO_BD.md`).
- Preparación carpeta ERD (`docs/ERD/`).
- Estructura base de Welcome público y Home privado modular por secciones (Bootstrap básico).
- Refactor de Welcome/Home: secciones con tono de crónica + estructura para logo en public/assets/brand/.
- Logo dentro del hero + footer welcome + limpieza de secciones no usadas.
- Ajuste final Semana 0 en calendario.

**Bitácora:** `docs/bitacora/2026-01-18.md`

---

## Semana 1 — Roles + Personaje + Inventario mínimo (2026-01-19 a 2026-01-25)

### 2026-01-19 (Lun) — 1h — Roles (admin/free/premium)

Trello:

- "Día 4 — Roles + seed admin"

Herramientas:

- VSCode, Terminal

Archivos a crear/tocar:

- database/migrations/xxxx_add_role_to_users_table.php (crear)
- database/seeders/DatabaseSeeder.php (tocar)
- database/seeders/AdminUserSeeder.php (crear)

Acciones:

- revisar users y SOLO crear migration si falta el campo
- Seed: crear admin por defecto (email fijo de clase)

Comandos:

- php artisan make:migration add_role_to_users_table --table=users
- php artisan make:seeder AdminUserSeeder
- php artisan migrate
- php artisan db:seed

Bitácora:

- docs/bitacora/2026-01-19.md

Commit:

- git add .
- git commit -m "creacion de roles y usuario admin por defecto"
- git push

---

### 2026-01-20 (Mar) — 1h — Middleware roles + rutas protegidas

Trello:

- "Día 5 — Middleware roles"

Archivos:

- app/Http/Middleware/EnsureRole.php (crear)
- app/Http/Kernel.php (tocar) o bootstrap/app.php (según Laravel 12)
- routes/web.php (tocar)

Acciones:

- Middleware para exigir role admin
- Crear ruta /admin (pantalla simple)

Comandos:

- php artisan make:middleware EnsureRole

Views:

- resources/views/admin/index.blade.php (crear)

Bitácora:

- docs/bitacora/2026-01-20.md

Commit:

- git add .
- git commit -m "middleware de roles y acceso a panel de admin"
- git push

---

### 2026-01-21 (Mié) — 1h — Characters (tabla + modelo)

Trello:

- "Día 6 — Character base"

Archivos:

- database/migrations/xxxx_create_characters_table.php (crear)
- app/Models/Character.php (crear)
- app/Http/Controllers/CharacterController.php (crear)
- routes/web.php (tocar)

Comandos:

- php artisan make:model Character -m
- php artisan make:controller CharacterController
- php artisan migrate

Views:

- resources/views/game/character/show.blade.php (crear)
- resources/views/game/character/create.blade.php (crear)

Bitácora:

- docs/bitacora/2026-01-21.md

Commit:

- git add .
- git commit -m "modelo de personaje y sus primeras pantallas"
- git push

---

### 2026-01-22 (Jue) — 1h — Stats (estructura) + validación

Trello:

- "Día 7 — Stats y validación personaje"

Archivos:

- app/Http/Requests/StoreCharacterRequest.php (crear)
- app/Http/Controllers/CharacterController.php (tocar)
- database/migrations/...create_characters_table.php (tocar si falta campo)
- docs/MODELO_BD.md (tocar: explicar stats_json)

Comandos:

- php artisan make:request StoreCharacterRequest

Bitácora:

- docs/bitacora/2026-01-22.md

Commit:

- git add .
- git commit -m "validacion de personajes y estructura de estadisticas"
- git push

---

### 2026-01-23 (Vie) — 3h — Items + inventario + seed

Trello:

- "Día 8 — Items + inventario"

Archivos:

- database/migrations/xxxx_create_items_table.php (crear)
- database/migrations/xxxx_create_character_items_table.php (crear)
- app/Models/Item.php (crear)
- app/Models/CharacterItem.php (crear o usar pivot sin modelo)
- app/Http/Controllers/InventoryController.php (crear)
- routes/web.php (tocar)
- database/seeders/ItemSeeder.php (crear)
- database/seeders/DatabaseSeeder.php (tocar)

Comandos:

- php artisan make:model Item -m
- php artisan make:migration create_character_items_table
- php artisan make:controller InventoryController
- php artisan make:seeder ItemSeeder
- php artisan migrate
- php artisan db:seed

Views:

- resources/views/game/inventory/index.blade.php (crear)

Bitácora:

- docs/bitacora/2026-01-23.md

Commits (2–3):

- "creacion de items e inventario"
- "pantallas de inventario con datos de prueba"
- git push

---

### 2026-01-24 (Sáb) — 1h — Base UI pixel + assets

Trello:

- "Día 9 — UI pixel base"

Herramientas:

- Photoshop (carpeta sprites), VSCode

Archivos:

- public/assets/sprites/.gitkeep (crear)
- public/assets/ui/.gitkeep (crear)
- resources/css/app.css (tocar: pixel css)
- resources/views/layouts/game.blade.php (crear)
- routes/web.php (tocar: usar layout game en pantallas de juego)

CSS obligatorio:

- image-rendering: pixelated;

Comandos:

- npm run build

Bitácora:

- docs/bitacora/2026-01-24.md

Commit:

- git add .
- git commit -m "diseño base del juego y renderizado de pixels"
- git push

---

### 2026-01-25 (Dom) — 1h — Dashboard del juego

Trello:

- "Día 10 — Home del juego"

Archivos:

- app/Http/Controllers/GameController.php (crear)
- routes/web.php (tocar)
- resources/views/game/dashboard.blade.php (crear)

Bitácora:

- docs/bitacora/2026-01-25.md

Commit:

- git add .
- git commit -m "dashboard principal del juego"
- git push

---

## Semana 2 — Misiones offline “elige tu destino” (2026-01-26 a 2026-02-01)

### 2026-01-26 (Lun) — 1h — Tablas misiones (core)

Trello:

- "Día 11 — Misiones schema"

Archivos:

- migrations:
    - create_missions_table.php
    - create_mission_nodes_table.php
    - create_mission_choices_table.php
    - create_mission_runs_table.php
- models:
    - Mission.php
    - MissionNode.php
    - MissionChoice.php
    - MissionRun.php

Comandos:

- php artisan make:model Mission -m
- php artisan make:model MissionNode -m
- php artisan make:model MissionChoice -m
- php artisan make:model MissionRun -m
- php artisan migrate

Docs:

- docs/MODELO_BD.md (tocar)

Bitácora:

- docs/bitacora/2026-01-26.md

Commit:

- git add .
- git commit -m "tablas y modelos para misiones offline"
- git push

---

### 2026-01-27 (Mar) — 1h — Flujo iniciar misión (run)

Trello:

- "Día 12 — Iniciar misión"

Archivos:

- app/Http/Controllers/MissionRunController.php (crear)
- routes/web.php (tocar)
- resources/views/game/missions/index.blade.php (crear)
- resources/views/game/missions/run.blade.php (crear)

Comandos:

- php artisan make:controller MissionRunController

Bitácora:

- docs/bitacora/2026-01-27.md

Commit:

- git add .
- git commit -m "flujo para empezar una mision"
- git push

---

### 2026-01-28 (Mié) — 1h — Pantalla nodo + elegir opción

Trello:

- "Día 13 — Opciones y progreso"

Archivos:

- MissionRunController.php (tocar: elegir choice y avanzar)
- run.blade.php (tocar)
- app/Http/Requests/ChooseMissionOptionRequest.php (crear)

Comandos:

- php artisan make:request ChooseMissionOptionRequest

Bitácora:

- docs/bitacora/2026-01-28.md

Commit:

- git add .
- git commit -m "opciones de mision y progreso entre nodos"
- git push

---

### 2026-01-29 (Jue) — 1h — Efectos por decisión (estado run)

Trello:

- "Día 14 — Efectos (heridas, veneno...)"

Archivos:

- database/migrations/...\_create_mission_runs_table.php (tocar: run_state_json)
- MissionRun.php (tocar: casts)
- MissionRunController.php (tocar: aplicar efectos)
- docs/MODELO_BD.md (tocar)

Bitácora:

- docs/bitacora/2026-01-29.md

Commit:

- git add .
- git commit -m "efectos de las decisiones guardados en el estado de la partida"
- git push

---

### 2026-01-30 (Vie) — 3h — Enemigo final + resolver misión + recompensa

Trello:

- "Día 15 — Final misión + rewards"

Archivos:

- migrations:
    - create_enemy_templates_table.php (crear)
- models:
    - EnemyTemplate.php (crear)
- servicios:
    - app/Services/MissionResolver.php (crear)
    - app/Services/RewardService.php (crear)
- MissionRunController.php (tocar: finalizar run)
- docs/MODELO_BD.md (tocar)

Comandos:

- php artisan make:model EnemyTemplate -m
- php artisan migrate

Bitácora:

- docs/bitacora/2026-01-30.md

Commits (2–3):

- "esquema para plantillas de enemigos"
- "sistema para resolver misiones y recompensas"
- git push

---

### 2026-01-31 (Sáb) — 1h — Seed de 1 misión completa (8–12 nodos)

Trello:

- "Día 16 — Seed misión demo"

Archivos:

- database/seeders/MissionDemoSeeder.php (crear)
- DatabaseSeeder.php (tocar)
- docs/GUIA_USUARIO.md (tocar: cómo jugar esa misión)

Comandos:

- php artisan make:seeder MissionDemoSeeder
- php artisan db:seed

Bitácora:

- docs/bitacora/2026-01-31.md

Commit:

- git add .
- git commit -m "datos de prueba para la mision de ejemplo"
- git push

---

### 2026-02-01 (Dom) — 1h — Historial misiones offline

Trello:

- "Día 17 — Historial misiones"

Archivos:

- app/Http/Controllers/MissionHistoryController.php (crear)
- routes/web.php (tocar)
- resources/views/game/missions/history.blade.php (crear)
- resources/views/game/missions/history_show.blade.php (crear)

Commit:

- git add .
- git commit -m "pantallas del historial de misiones"
- git push

Bitácora:

- docs/bitacora/2026-02-01.md

---

## Semana 3 — Chat Reverb + Premium A/B/C (2026-02-02 a 2026-02-08)

### 2026-02-02 (Lun) — 1h — Tablas chat

Trello:

- "Día 18 — Chat schema"

Archivos:

- migrations:
    - create_chat_rooms_table.php
    - create_chat_messages_table.php
- models:
    - ChatRoom.php
    - ChatMessage.php

Comandos:

- php artisan make:model ChatRoom -m
- php artisan make:model ChatMessage -m
- php artisan migrate

Commit:

- git add .
- git commit -m "base de datos para el chat"
- git push

Bitácora:

- docs/bitacora/2026-02-02.md

---

### 2026-02-03 (Mar) — 1h — Evento broadcasting (mensaje enviado)

Trello:

- "Día 19 — Evento chat realtime"

Archivos:

- app/Events/ChatMessageSent.php (crear)
- routes/channels.php (tocar: canal chat room)
- app/Http/Controllers/ChatController.php (crear)

Comandos:

- php artisan make:event ChatMessageSent

Commit:

- git add .
- git commit -m "envio de mensajes de chat por sala"
- git push

Bitácora:

- docs/bitacora/2026-02-03.md

---

### 2026-02-04 (Mié) — 1h — UI chat global (Blade)

Trello:

- "Día 20 — UI chat global"

Archivos:

- resources/views/game/chat/index.blade.php (crear)
- resources/js/echo.js o resources/js/bootstrap.js (tocar: Echo config)
- resources/js/app.js (tocar si hace falta)
- routes/web.php (tocar)

Comandos:

- npm run build

Commit:

- git add .
- git commit -m "interfaz del chat global con reverb"
- git push

Bitácora:

- docs/bitacora/2026-02-04.md

---

### 2026-02-05 (Jue) — 1h — Premium A: admin cambia role

Trello:

- "Día 21 — Premium manual"

Archivos:

- app/Http/Controllers/Admin/UserAdminController.php (crear)
- resources/views/admin/users/index.blade.php (crear)
- resources/views/admin/users/edit.blade.php (crear)
- routes/web.php (tocar: grupo /admin/users)
- app/Http/Middleware/EnsureRole.php (tocar si falta)

Commit:

- git add .
- git commit -m "admin puede cambiar roles de usuario a mano"
- git push

Bitácora:

- docs/bitacora/2026-02-05.md

---

### 2026-02-06 (Vie) — 3h — Premium por códigos + Temporadas (ranking mensual por raza + cofre)

Trello:

- "Día 22 — Premium por códigos + Ranking mensual"

Parte A — Premium por códigos (real)
Archivos:

- database/migrations/create_redeem_codes_table.php (si no existe)
- app/Models/RedeemCode.php
- app/Http/Controllers/RedeemCodeController.php
- resources/views/game/profile/redeem.blade.php
- routes/web.php

Reglas:

- Código con usos máximos
- Al canjear: usuario pasa a premium y se guarda cuándo + qué código
- El admin puede generar códigos desde BD o un seeder simple

Parte B — Temporada / ranking por raza + cofre al ganador
Archivos:

- app/Console/Commands/CloseSeason.php (crear)
- app/Services/SeasonService.php (crear)
- app/Services/ChestService.php (crear)
- docs/COMANDOS.md (añadir cómo ejecutarlo)
- docs/MODELO_BD.md (añadir nota si faltaba)

Objetivo mínimo:

- Calcular puntuación por raza del mes (lo que ya tengas modelado en BD)
- Detectar raza ganadora
- Generar cofre con 3 objetos (usando loot/rareza)
- Guardar el resultado (tabla winners / openings / rewards)
- Ejecutar manualmente el comando para probarlo (sin scheduler todavía)

Comandos:

- php artisan make:command CloseSeason
- php artisan close:season --month=2026-01 (o lo que uses)
- php artisan test (si metes un test simple opcional)

Commits:

- "feat: premium por codigos canjeables"
- "feat: ranking mensual por raza y cofre de recompensa"
- git push

Bitácora:

- docs/bitacora/2026-02-06.md

---

### 2026-02-07 (Sáb) — 1h — Navbar final + perfiles

Trello:

- "Día 23 — Navegación + perfil"

Archivos:

- resources/views/layouts/game.blade.php (tocar navbar)
- resources/views/game/profile/index.blade.php (crear)
- routes/web.php (tocar)

Commit:

- git add .
- git commit -m "menu de navegacion y area de perfil"
- git push

Bitácora:

- docs/bitacora/2026-02-07.md

---

### 2026-02-08 (Dom) — 1h — Tests mínimos (Auth + roles)

Trello:

- "Día 24 — Tests 1"

Archivos:

- tests/Feature/AuthAccessTest.php (crear)
- tests/Feature/AdminRoleTest.php (crear)

Comandos:

- php artisan test

Commit:

- git add .
- git commit -m "tests basicos de login y roles"
- git push

Bitácora:

- docs/bitacora/2026-02-08.md

---

## Semana 4 — Misiones online dúo (2026-02-09 a 2026-02-15)

### 2026-02-09 (Lun) — 1h — Tablas party

Trello:

- "Día 25 — Party schema"

Archivos:

- migrations:
    - create_parties_table.php
    - create_party_members_table.php
    - create_party_invites_table.php
- models:
    - Party.php, PartyMember.php, PartyInvite.php

Comandos:

- php artisan make:model Party -m
- php artisan make:model PartyMember -m
- php artisan make:model PartyInvite -m
- php artisan migrate

Commit:

- git add .
- git commit -m "tablas para grupos de 2 jugadores"
- git push

Bitácora:

- docs/bitacora/2026-02-09.md

---

### 2026-02-10 (Mar) — 1h — Crear party + invitar

Trello:

- "Día 26 — Crear party + invitar"

Archivos:

- app/Http/Controllers/PartyController.php (crear)
- routes/web.php (tocar)
- resources/views/game/party/index.blade.php (crear)
- resources/views/game/party/show.blade.php (crear)

Commit:

- git add .
- git commit -m "crear grupo e invitar a gente"
- git push

Bitácora:

- docs/bitacora/2026-02-10.md

---

### 2026-02-11 (Mié) — 1h — Aceptar invitación + sala party + chat room party

Trello:

- "Día 27 — Aceptar + chat party"

Archivos:

- PartyController.php (tocar)
- ChatController.php (tocar: room por party)
- migrations (si hace falta party_chat_room_id en parties)
- resources/views/game/party/show.blade.php (tocar)

Commit:

- git add .
- git commit -m "aceptar invitacion y chat de grupo"
- git push

Bitácora:

- docs/bitacora/2026-02-11.md

---

### 2026-02-12 (Jue) — 1h — Misión dúo (estructura run party)

Trello:

- "Día 28 — Run misión dúo"

Archivos:

- database/migrations/create_party_mission_runs_table.php (crear)
- app/Models/PartyMissionRun.php (crear)
- app/Http/Controllers/PartyMissionController.php (crear)
- resources/views/game/party/missions/run.blade.php (crear)
- app/Services/MissionResolver.php (tocar para modo party)

Commit:

- git add .
- git commit -m "estructura para jugar misiones en grupo"
- git push

Bitácora:

- docs/bitacora/2026-02-12.md

---

### 2026-02-13 (Vie) — 3h — Recompensas dúo + límites + tests

Trello:

- "Día 29 — Party misión completa"

Archivos:

- PartyMissionController.php (tocar)
- RewardService.php (tocar)
- tests/Feature/PartyFlowTest.php (crear)

Comandos:

- php artisan test

Commits:

- "recompensas y reglas de misiones en grupo"
- "tests de crear e invitar al grupo"
- git push

Bitácora:

- docs/bitacora/2026-02-13.md

---

### 2026-02-14 (Sáb) — 1h — UI party (limpia)

Trello:

- "Día 30 — UI party limpia"

Archivos:

- resources/views/game/party/index.blade.php (tocar)
- resources/views/game/party/show.blade.php (tocar)
- resources/css/app.css (tocar si hace falta)

Commit:

- git add .
- git commit -m "retoques en la interfaz del grupo"
- git push

Bitácora:

- docs/bitacora/2026-02-14.md

---

### 2026-02-15 (Dom) — 1h — Seed misión dúo (reutilizar)

Trello:

- "Día 31 — Seed misión dúo"

Archivos:

- database/seeders/PartyMissionDemoSeeder.php (crear) (si hace falta)
- docs/GUIA_USUARIO.md (tocar: cómo jugar dúo)

Commit:

- git add .
- git commit -m "contenido de prueba para mision en pareja"
- git push

Bitácora:

- docs/bitacora/2026-02-15.md

---

## Semana 5 — Duelos PvP estilo FF (2026-02-16 a 2026-02-22)

### 2026-02-16 (Lun) — 1h — Tabla duels

Trello:

- "Día 32 — Duels schema"

Archivos:

- database/migrations/create_duels_table.php (crear)
- app/Models/Duel.php (crear)

Comandos:

- php artisan make:model Duel -m
- php artisan migrate

Commit:

- git add .
- git commit -m "tablas para el sistema de duelos"
- git push

Bitácora:

- docs/bitacora/2026-02-16.md

---

### 2026-02-17 (Mar) — 1h — Desafiar + notificación simple

Trello:

- "Día 33 — Desafiar"

Archivos:

- app/Http/Controllers/DuelController.php (crear)
- routes/web.php (tocar)
- resources/views/game/duels/index.blade.php (crear)
- resources/views/game/duels/challenges.blade.php (crear)

Commit:

- git add .
- git commit -m "flujo para desafiar a otros jugadores"
- git push

Bitácora:

- docs/bitacora/2026-02-17.md

---

### 2026-02-18 (Mié) — 1h — Aceptar + pantalla combate

Trello:

- "Día 34 — Pantalla combate"

Archivos:

- resources/views/game/duels/fight.blade.php (crear)
- DuelController.php (tocar: aceptar y entrar)
- app/Services/DuelEngine.php (crear)

Commit:

- git add .
- git commit -m "pantalla de combate y motor de duelos"
- git push

Bitácora:

- docs/bitacora/2026-02-18.md

---

### 2026-02-19 (Jue) — 1h — Resolver turnos (fisico/magia/guardia) + resultado

Trello:

- "Día 35 — Resolver duelo"

Archivos:

- DuelEngine.php (tocar)
- Duel.php (tocar: casts result_json)
- DuelController.php (tocar: ejecutar acción)
- migrations (si falta campo status/winner_id)

Commit:

- git add .
- git commit -m "resolucion de duelos y guardar resultado final"
- git push

Bitácora:

- docs/bitacora/2026-02-19.md

---

### 2026-02-20 (Vie) — 3h — Historial + validaciones + tests

Trello:

- "Día 36 — Historial duelos + tests"

Archivos:

- resources/views/game/duels/history.blade.php (crear)
- DuelController.php (tocar)
- tests/Feature/DuelFlowTest.php (crear)

Comandos:

- php artisan test

Commits:

- "pantallas de historial de duelos"
- "tests de desafios y resultados de duelos"
- git push

Bitácora:

- docs/bitacora/2026-02-20.md

---

### 2026-02-21 (Sáb) — 1h — UI duelo (feedback visual)

Trello:

- "Día 37 — UI duelo pro"

Herramientas:

- Photoshop (iconos simples), VSCode

Archivos:

- resources/views/game/duels/fight.blade.php (tocar)
- public/assets/ui/ (añadir iconos/botones pixel)
- resources/css/app.css (tocar)

Commit:

- git add .
- git commit -m "mejoras visuales en el combate"
- git push

Bitácora:

- docs/bitacora/2026-02-21.md

---

### 2026-02-22 (Dom) — 1h — Día de bugs (limpieza)

Trello:

- "Día 38 — Fixes"

Acciones:

- Revisar misiones/chat/party/duels
- Arreglar 2–4 bugs máximo

Commit:

- git add .
- git commit -m "arreglos generales de estabilidad"
- git push

Bitácora:

- docs/bitacora/2026-02-22.md

---

## Semana 6 — Admin + Arte + Memoria + Entrega (2026-02-23 a 2026-02-28)

### 2026-02-23 (Lun) — 1h — Admin CRUD misiones (mínimo real)

Trello:

- "Día 39 — Admin misiones"

Archivos:

- app/Http/Controllers/Admin/MissionAdminController.php (crear)
- resources/views/admin/missions/index.blade.php (crear)
- resources/views/admin/missions/edit.blade.php (crear)
- routes/web.php (tocar)

Commit:

- git add .
- git commit -m "panel de admin para misiones"
- git push

Bitácora:

- docs/bitacora/2026-02-23.md

---

### 2026-02-24 (Mar) — 1h — Admin enemies + items + códigos

Trello:

- "Día 40 — Admin contenido"

Archivos:

- app/Http/Controllers/Admin/EnemyAdminController.php (crear)
- app/Http/Controllers/Admin/ItemAdminController.php (crear)
- app/Http/Controllers/Admin/RedeemCodeAdminController.php (crear)
- views admin correspondientes (crear)

Commit:

- git add .
- git commit -m "panel de admin para enemigos e items"
- git push

Bitácora:

- docs/bitacora/2026-02-24.md

---

### 2026-02-25 (Mié) — 1h — Pixel pack 1 (6 sprites) + optimización

Trello:

- "Día 41 — Sprite pack 1"

Herramientas:

- Photoshop, Squoosh

Archivos:

- public/assets/sprites/hero/
    - idle.png
    - atk_physical.png
    - atk_magic.png
    - guard.png
    - hit.png
    - defeated.png
- docs/GUIA_USUARIO.md (tocar: capturas)

Commit:

- git add .
- git commit -m "primer pack de sprites del heroe"
- git push

Bitácora:

- docs/bitacora/2026-02-25.md

---

### 2026-02-26 (Jue) — 1h — Documentación (casi final)

Trello:

- "Día 42 — Memoria armada"

Archivos:

- docs/GUIA_INSTALACION.md (tocar completo)
- docs/GUIA_USUARIO.md (tocar completo)
- docs/COMANDOS.md (repasar)
- docs/MODELO_BD.md (repasar)
- docs/CHECKLIST_ENTREGA.md (repasar)

Commit:

- git add .
- git commit -m "guias de instalacion y usuario terminadas"
- git push

Bitácora:

- docs/bitacora/2026-02-26.md

---

### 2026-02-27 (Vie) — 3h — Export SQL + revisión + ZIP final

Trello:

- "Día 43 — Export + entrega"

Herramientas:

- DBeaver, VSCode

Acciones:

1. Export SQL (DBeaver):

- Exportar: estructura + datos
- Guardar como: bd_proyecto_alumno.sql (en raíz del repo o carpeta entrega)

2. Revisión total:

- Login/registro
- Roles admin/free/premium
- Misión offline demo
- Party dúo
- Chat global y party
- Duelo desafiar + resultado + historial

3. Preparar carpeta entrega:

- entrega/
    - proyecto/ (zip o carpeta)
    - bd_proyecto_alumno.sql
    - memoria_proyecto.pdf (aquí luego)

Archivos a crear:

- entrega/README_ENTREGA.md (explica qué hay)
- bd_proyecto_alumno.sql (export)
- docs/ERD/erd_final.png (export de DBeaver)
- docs/ERD/erd_final.sql_notes.md (opcional)

Commits (2–4):

- "esquema ER final de la base de datos"
- "exportacion de la base de datos para entrega"
- "preparacion de carpetas para la entrega"
- git push

Bitácora:

- docs/bitacora/2026-02-27.md

---

### 2026-02-28 (Sáb) — 1h — Capturas + cerrar PDF memoria

Trello:

- "Día 44 — Memoria PDF final"

Herramientas:

- navegador + capturas, editor PDF

Acciones:

- Capturas:
    - /login
    - dashboard juego
    - personaje
    - inventario
    - misión offline
    - chat
    - party
    - duelos
    - admin
- Cerrar memoria_proyecto.pdf

Archivos:

- entrega/memoria_proyecto.pdf (añadir)
- docs/bitacora/2026-02-28.md

Commit:

- git add .
- git commit -m "memoria final en pdf y capturas"
- git push
