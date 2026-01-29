# Calendario ultra-detalado — El juramento de Valtherion (Laravel 12)

Nota (2026-01-29): Se dejó la sección "Crónica Mensual" en Home con ranking real y fallback ordenado por nombre de raza (opción A).

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

[x] Creación proyecto Laravel 12.
[x] Configuración de Git y ramas.
[x] Migraciones completas del juego (razas, items, misiones...).
[x] Seeders base (Archaon y datos mínimos).
[x] Generación de entregable automático (`docs/entregable_db.md`).

**Bitácora:** `docs/bitacora/2026-01-16.md`

### 2026-01-17 (Sáb) — 1h — Configuración Real + Auth (Breeze)

**Hecho:**

[x] Configuración real de `.env` (SQLite local).
[x] `php artisan migrate:fresh --seed` (verificado).
[x] Instalación de Laravel Breeze (Blade).
[x] Compilación de assets (`npm run build`).
[x] Docs operativos: `COMANDOS.md` y `CHECKLIST_ENTREGA.md`.

**Bitácora:** `docs/bitacora/2026-01-17.md`

### 2026-01-18 (Dom) — 1h — Reverb + Documentación Modelo

**Hecho:**

[x] Instalación de Laravel Reverb (Broadcasting).
[x] Documentación del modelo de datos (`docs/MODELO_BD.md`).
[x] Preparación carpeta ERD (`docs/ERD/`).
[x] Estructura base de Welcome público y Home privado modular por secciones (Bootstrap básico).
[x] Refactor de Welcome/Home: secciones con tono de crónica + estructura para logo en public/assets/brand/.
[x] Logo dentro del hero + footer welcome + limpieza de secciones no usadas.
[x] Ajuste final Semana 0 en calendario.

**Bitácora:** `docs/bitacora/2026-01-18.md`

---

## Semana 1 — Roles + Personaje + Inventario mínimo (2026-01-19 a 2026-01-25)

### 2026-01-19 (Lun) — 1h — Roles (admin/free/premium)

**Hecho:**

[x] Trello: "Día 4 — Roles + seed admin"
[x] revisión de users y SOLO creación de migration si falta el campo
[x] Seed: crear admin por defecto (email fijo de clase)
[x] Comandos: migration, seeder, migrate, seed
[x] Git add, commit, push

**Bitácora:** `docs/bitacora/2026-01-19.md`

**Commit:** "creacion de roles y usuario admin por defecto"

---

### 2026-01-20 (Mar) — 1h — Middleware roles + rutas protegidas + Home Juego Base

**Hecho:**

[x] Middleware para exigir role admin (`EnsureRole`).
[x] Registro de alias de middleware en `bootstrap/app.php`.
[x] Creación de ruta `/admin` y panel administrativo mínimo.
[x] Ajuste estético de Login/Register (Modo oscuro + Logo + Castellano).
[x] **Home Juego Base**: Estructura fija (no-scroll) con doble navbar (superior e inferior).
[x] **Navegación**: Implementación de iconos y rutas placeholder para Tienda, Inventario, Perfil, Misiones, Peleas y Chat.

**Bitácora:** `docs/bitacora/2026-01-20.md`

**Commit:** "middleware de roles, acceso a panel de admin y home juego base"

---

### 2026-01-23 (Vie) — 3h — Characters + Stats + Base Items

Trello:

[ ] "Día 6 — Character base"
[ ] "Día 7 — Stats y validación personaje"
[ ] "Día 8 — Items core"

Archivos:

[ ] database/migrations/xxxx_create_characters_table.php
[ ] app/Models/Character.php
[ ] app/Http/Controllers/CharacterController.php
[ ] app/Http/Requests/StoreCharacterRequest.php
[ ] database/migrations/xxxx_create_items_table.php
[ ] app/Models/Item.php

Acciones:

[ ] Crear modelo Character y su migración
[ ] Crear controller para gestión de personajes
[ ] Crear Request para validación de creación (stats_json)
[ ] Crear tablas base para items
[ ] Definir rutas de creación de personajes
[ ] Crear public/css/landing-theme.css con tokens claros y componentes
[ ] Enlazar CSS en layouts/guest.blade.php después de Bootstrap
[ ] Aplicar clases section-panel y race-\* en razas_del_viejo_mundo.blade.php
[ ] Implementar sistema de theming dual (claro "Crónica Antigua" + oscuro "Abismo")
[ ] Actualizar bitácora del día
[ ] Commit + push

Comandos:

[ ] php artisan make:model Character -m
[ ] php artisan make:controller CharacterController
[ ] php artisan make:request StoreCharacterRequest
[ ] php artisan make:model Item -m
[ ] php artisan migrate
[ ] git add .
[ ] git commit -m "Landing: paleta clara fija + legibilidad en cards"
[ ] git push

Bitácora:

[ ] docs/bitacora/2026-01-23.md

Commit:

[ ] "modelo de personaje y validacion de estadisticas"
[ ] "base de datos para items e inventario"

---

### 2026-01-24 (Sáb) — 1h — Inventario + Seed Items

Trello:

[ ] "Día 8 — Inventario + seed"

Archivos:

[ ] database/migrations/xxxx_create_character_items_table.php
[ ] app/Models/CharacterItem.php
[ ] app/Http/Controllers/InventoryController.php
[ ] database/seeders/ItemSeeder.php

Comandos:

[ ] php artisan make:migration create_character_items_table
[ ] php artisan make:controller InventoryController
[ ] php artisan make:seeder ItemSeeder
[ ] php artisan migrate
[ ] php artisan db:seed

Views:

[ ] resources/views/game/inventory/index.blade.php

Bitácora:

[ ] docs/bitacora/2026-01-24.md

**Actualización (2026-01-23, tarde) — Landing: Razas**

- [x] `public/css/landing-theme.css` creado/extendido con tokens y estilos para `race-*`.
- [x] Partial moderno `resources/views/guest/sections/_razas_grid.blade.php` creado.
- [x] `resources/views/guest/welcome.blade.php` actualizado para incluir el nuevo partial.
- [x] Se reemplazó/neutralizó el partial legacy `razas_del_viejo_mundo.blade.php` para evitar duplicados.
- [x] Ajustes de accesibilidad: `alt` en avatars, `aria-label` en badge premium, focus-visible en cards.
- [x] Cachés limpiadas (`view:clear`, `cache:clear`, `route:clear`, `config:clear`) y assets compilados (`npm run build`).

Commit:

[ ] "inventario funcional y seeders de items"

---

### 2026-01-25 (Dom) — 1h — Base UI pixel + assets

Trello:

[ ] "Día 9 — UI pixel base"

Herramientas:

[ ] Photoshop (carpeta sprites), VSCode

Archivos:

[ ] public/assets/sprites/.gitkeep (crear)
[ ] public/assets/ui/.gitkeep (crear)
[ ] resources/css/app.css (tocar: pixel css)
[ ] resources/views/layouts/game.blade.php (crear)

Bitácora:

[ ] docs/bitacora/2026-01-25.md

Commit:

[ ] "diseño base del juego y renderizado de pixels"

---

### 2026-01-26 (Lun) — 1h — Dashboard del juego

Trello:

[ ] "Día 10 — Home del juego"

Archivos:

[ ] app/Http/Controllers/GameController.php (crear)
[ ] routes/web.php (tocar)
[ ] resources/views/game/dashboard.blade.php (crear)

Bitácora:

[ ] docs/bitacora/2026-01-26.md

Commit:

[ ] "dashboard principal del juego"

---

## Semana 2 — Misiones offline “elige tu destino” (2026-01-27 a 2026-02-02)

### 2026-01-27 (Mar) — 1h — Tablas misiones (core)

Trello:

[ ] "Día 11 — Misiones schema"

Archivos:

[ ] migrations (misiones, nodos, opciones, runs)
[ ] models (Mission, MissionNode, MissionChoice, MissionRun)

Comandos:

[ ] php artisan make:model Mission -m
[ ] php artisan make:model MissionNode -m
[ ] php artisan make:model MissionChoice -m
[ ] php artisan make:model MissionRun -m
[ ] php artisan migrate

Bitácora:

[ ] docs/bitacora/2026-01-27.md

Commit:

[ ] "tablas y modelos para misiones offline"

---

### 2026-01-28 (Mié) — 1h — Flujo iniciar misión (run)

Trello:

[ ] "Día 12 — Iniciar misión"

Archivos:

[ ] app/Http/Controllers/MissionRunController.php
[ ] resources/views/game/missions/index.blade.php
[ ] resources/views/game/missions/run.blade.php

Bitácora:

[ ] docs/bitacora/2026-01-28.md

Commit:

[ ] "flujo para empezar una mision"

---

### 2026-01-29 (Jue) — 1h — Pantalla nodo + elegir opción

Trello:

[ ] "Día 13 — Opciones y progreso"

Archivos:

[ ] MissionRunController.php (lógica avanzar)
[ ] app/Http/Requests/ChooseMissionOptionRequest.php

Bitácora:

[ ] docs/bitacora/2026-01-29.md

Commit:

[ ] "opciones de mision y progreso entre nodos"

- Tablas `seasons`/`season_race_rankings`/`season_race_winners` + comandos `season:ensure-current` / `season:close` (migraciones añadidas y verificadas localmente)
- Crónica Mensual: la sección del landing (`guest.sections.cronica_mensual`) ahora muestra la clasificación real del mes anterior sacada de la BD y la raza ganadora si existe.

---

### 2026-01-30 (Vie) — 3h — Efectos + Enemigo final + Recompensas

Trello:

[ ] "Día 14 — Efectos (heridas, veneno...)"
[ ] "Día 15 — Final misión + rewards"

Archivos:

[ ] database/migrations/create_enemy_templates_table.php
[ ] app/Models/EnemyTemplate.php
[ ] app/Services/MissionResolver.php
[ ] app/Services/RewardService.php

Comandos:

[ ] php artisan make:model EnemyTemplate -m
[ ] php artisan migrate

Bitácora:

[ ] docs/bitacora/2026-01-30.md

Commit:

[ ] "efectos de decisiones y sistema de resolucion de misiones"

---

### 2026-01-31 (Sáb) — 1h — Seed de 1 misión completa (8–12 nodos)

Trello:

[ ] "Día 16 — Seed misión demo"

Archivos:

[ ] database/seeders/MissionDemoSeeder.php

Bitácora:

[ ] docs/bitacora/2026-01-31.md

Commit:

[ ] "datos de prueba para la mision de ejemplo"

---

### 2026-02-01 (Dom) — 1h — Historial misiones offline

Trello:

[ ] "Día 17 — Historial misiones"

Archivos:

[ ] app/Http/Controllers/MissionHistoryController.php
[ ] resources/views/game/missions/history.blade.php

Bitácora:

[ ] docs/bitacora/2026-02-01.md

Commit:

[ ] "pantallas del historial de misiones"

---

## Semana 3 — Chat Reverb + Premium A/B/C (2026-02-03 a 2026-02-09)

### 2026-02-03 (Mar) — 1h — Tablas chat

Trello:

[ ] "Día 18 — Chat schema"

Archivos:

[ ] migrations (chat_rooms, chat_messages)
[ ] models (ChatRoom, ChatMessage)

Bitácora:

[ ] docs/bitacora/2026-02-03.md

Commit:

[ ] "base de datos para el chat"

---

### 2026-02-04 (Mié) — 1h — Evento broadcasting (mensaje enviado)

Trello:

[ ] "Día 19 — Evento chat realtime"

Archivos:

[ ] app/Events/ChatMessageSent.php
[ ] app/Http/Controllers/ChatController.php

Bitácora:

[ ] docs/bitacora/2026-02-04.md

Commit:

[ ] "envio de mensajes de chat por sala"

---

### 2026-02-05 (Jue) — 1h — UI chat global (Blade)

Trello:

[ ] "Día 20 — UI chat global"

Archivos:

[ ] resources/views/game/chat/index.blade.php
[ ] resources/js/echo.js

Bitácora:

[ ] docs/bitacora/2026-02-05.md

Commit:

[ ] "interfaz del chat global con reverb"

---

### 2026-02-06 (Vie) — 3h — Premium A/B/C + Ranking

Trello:

[ ] "Día 21 — Premium manual"
[ ] "Día 22 — Premium por códigos + Ranking mensual"

Archivos:

[ ] app/Models/RedeemCode.php
[ ] app/Console/Commands/CloseSeason.php
[ ] app/Services/SeasonService.php

Bitácora:

[ ] docs/bitacora/2026-02-06.md

Commit:

[ ] "sistema premium y ranking mensual con cofres"

---

### 2026-02-07 (Sáb) — 1h — Navbar final + perfiles

Trello:

[ ] "Día 23 — Navegación + perfil"

Archivos:

[ ] resources/views/layouts/game.blade.php
[ ] resources/views/game/profile/index.blade.php

Bitácora:

[ ] docs/bitacora/2026-02-07.md

Commit:

[ ] "menu de navegacion y area de perfil"

---

### 2026-02-08 (Dom) — 1h — Tests mínimos (Auth + roles)

Trello:

[ ] "Día 24 — Tests 1"

Archivos:

[ ] tests/Feature/AuthAccessTest.php
[ ] tests/Feature/AdminRoleTest.php

Bitácora:

[ ] docs/bitacora/2026-02-08.md

Commit:

[ ] "tests basicos de login y roles"

---

## Semana 4 — Misiones online dúo (2026-02-09 a 2026-02-15)

### 2026-02-09 (Lun) — 1h — Tablas party

Trello:

[ ] "Día 25 — Party schema"

Archivos:

[ ] migrations (parties, members, invites)
[ ] models (Party, PartyMember, PartyInvite)

Bitácora:

[ ] docs/bitacora/2026-02-09.md

Commit:

[ ] "tablas para grupos de 2 jugadores"

---

### 2026-02-10 (Mar) — 1h — Crear party + invitar

Trello:

[ ] "Día 26 — Crear party + invitar"

Archivos:

[ ] app/Http/Controllers/PartyController.php
[ ] resources/views/game/party/index.blade.php

Bitácora:

[ ] docs/bitacora/2026-02-10.md

Commit:

[ ] "crear grupo e invitar a gente"

---

### 2026-02-11 (Mié) — 1h — Aceptar invitación + sala party

Trello:

[ ] "Día 27 — Aceptar + chat party"

Archivos:

[ ] ChatController.php (room por party)
[ ] resources/views/game/party/show.blade.php

Bitácora:

[ ] docs/bitacora/2026-02-11.md

Commit:

[ ] "aceptar invitacion y chat de grupo"

---

### 2026-02-12 (Jue) — 1h — Misión dúo (estructura run party)

Trello:

[ ] "Día 28 — Run misión dúo"

Archivos:

[ ] app/Models/PartyMissionRun.php
[ ] app/Http/Controllers/PartyMissionController.php

Bitácora:

[ ] docs/bitacora/2026-02-12.md

Commit:

[ ] "estructura para jugar misiones en grupo"

---

### 2026-02-13 (Vie) — 3h — Recompensas dúo + límites + tests

Trello:

[ ] "Día 29 — Party misión completa"

Archivos:

[ ] RewardService.php (modo party)
[ ] tests/Feature/PartyFlowTest.php

Bitácora:

[ ] docs/bitacora/2026-02-13.md

Commit:

[ ] "recompensas y reglas de misiones en grupo con tests"

---

### 2026-02-14 (Sáb) — 1h — UI party (limpia)

Trello:

[ ] "Día 30 — UI party limpia"

Bitácora:

[ ] docs/bitacora/2026-02-14.md

Commit:

[ ] "retoques en la interfaz del grupo"

---

### 2026-02-15 (Dom) — 1h — Seed misión dúo (reutilizar)

Trello:

[ ] "Día 31 — Seed misión dúo"

Bitácora:

[ ] docs/bitacora/2026-02-15.md

Commit:

[ ] "contenido de prueba para mision en pareja"

---

## Semana 5 — Duelos PvP estilo FF (2026-02-16 a 2026-02-22)

### 2026-02-16 (Lun) — 1h — Tabla duels

Trello:

[ ] "Día 32 — Duels schema"

Archivos:

[ ] migrations (duels)
[ ] models (Duel)

Bitácora:

[ ] docs/bitacora/2026-02-16.md

Commit:

[ ] "tablas para el sistema de duelos"

---

### 2026-02-17 (Mar) — 1h — Desafiar + notificación simple

Trello:

[ ] "Día 33 — Desafiar"

Archivos:

[ ] app/Http/Controllers/DuelController.php
[ ] resources/views/game/duels/index.blade.php

Bitácora:

[ ] docs/bitacora/2026-02-17.md

Commit:

[ ] "flujo para desafiar a otros jugadores"

---

### 2026-02-18 (Mié) — 1h — Aceptar + pantalla combate

Trello:

[ ] "Día 34 — Pantalla combate"

Archivos:

[ ] resources/views/game/duels/fight.blade.php
[ ] app/Services/DuelEngine.php

Bitácora:

[ ] docs/bitacora/2026-02-18.md

Commit:

[ ] "pantalla de combate y motor de duelos"

---

### 2026-02-19 (Jue) — 1h — Resolver turnos + resultado

Trello:

[ ] "Día 35 — Resolver duelo"

Archivos:

[ ] DuelEngine.php (lógica turnos)

Bitácora:

[ ] docs/bitacora/2026-02-19.md

Commit:

[ ] "resolucion de duelos y guardar resultado final"

---

### 2026-02-20 (Vie) — 3h — Historial + validaciones + tests

Trello:

[ ] "Día 36 — Historial duelos + tests"

Archivos:

[ ] resources/views/game/duels/history.blade.php
[ ] tests/Feature/DuelFlowTest.php

Bitácora:

[ ] docs/bitacora/2026-02-20.md

Commit:

[ ] "historial de duelos y tests de combate"

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
  [ ] Arreglar 2–4 bugs máximo

Commit:

[ ] "arreglos generales de estabilidad"

Bitácora:

[ ] docs/bitacora/2026-02-22.md

---

## Semana 6 — Admin + Arte + Memoria + Entrega (2026-02-23 a 2026-02-28)

### 2026-02-23 (Lun) — 1h — Admin CRUD misiones

Trello:

[ ] "Día 39 — Admin misiones"

Bitácora:

[ ] docs/bitacora/2026-02-23.md

Commit:

[ ] "panel de admin para misiones"

---

### 2026-02-24 (Mar) — 1h — Admin contenido (enemies, items)

Trello:

[ ] "Día 40 — Admin contenido"

Bitácora:

[ ] docs/bitacora/2026-02-24.md

Commit:

[ ] "panel de admin para enemigos e items"

---

### 2026-02-25 (Mié) — 1h — Pixel pack 1

Trello:

[ ] "Día 41 — Sprite pack 1"

Bitácora:

[ ] docs/bitacora/2026-02-25.md

Commit:

[ ] "primer pack de sprites del heroe"

---

### 2026-02-26 (Jue) — 1h — Memoria (borrador)

Trello:

[ ] "Día 42 — Memoria armada"

Bitácora:

[ ] docs/bitacora/2026-02-26.md

Commit:

[ ] "guias de instalacion y usuario terminadas"

---

### 2026-02-27 (Vie) — 3h — Entrega Final (Export SQL + Revisión + ZIP)

Trello:

[ ] "Día 43 — Export + entrega"

Acciones:

[ ] Export SQL con estructura y datos
[ ] Revisión total de flujos
[ ] Preparar carpeta entrega

Bitácora:

[ ] docs/bitacora/2026-02-27.md

---

### 2026-02-28 (Sáb) — 1h — Memoria PDF final

Trello:

[ ] "Día 44 — Memoria PDF final"

Acciones:

[ ] Capturas de todas las secciones
[ ] Cerrar PDF oficial

Bitácora:

[ ] docs/bitacora/2026-02-28.md
