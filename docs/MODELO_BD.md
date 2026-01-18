# Modelo de Base de Datos - El Juramento de Valtherion

## Descripción General

Este documento describe las tablas principales del juego según las migraciones existentes.

> **Nota 1:** Los stats finales de un personaje se calculan sumando: Base de raza + Bonus de personaje + Equipo (items) + Montura (si está montado).
> **Nota 2:** El sistema de Loot funciona por pesos (weights). A mayor peso, más probabilidad de aparición.
> **Nota 3:** Las monturas marcadas como `mountable` deben ser típicamente legendarias y de nivel alto.

## Tablas Principales

### users

- **Qué es:** Usuarios del sistema.
- **Claves:** `id`, `email`.
- **Campos importantes:** `plan` (free/premium), `premium_granted_at`.
- **Relaciones:** Tiene muchos `characters`.

### rarities

- **Qué es:** Define la rareza de items y entidades (Común, Raro, Épico, Legendario).
- **Relaciones:** Usada por `items`, `animals`, etc.

### races

- **Qué es:** Razas disponibles para los personajes (Humanos, Orcos, Elfos, etc.).
- **Campos importantes:** Stats base (`base_strength`, `base_agility`, etc.), `access` (free/premium).
- **Relaciones:** Un personaje pertenece a una raza.

### heroes

- **Qué es:** Héroes únicos del lore (ej. Archaon).
- **Relaciones:** Puede vincularse a eventos o recompensas especiales.

### characters

- **Qué es:** El personaje jugable del usuario.
- **Campos importantes:** `name`, `level`, `xp`, `bonuses` (stats extra), `active_animal_id` (montura actual).
- **Relaciones:** Pertenece a `user`, tiene una `race`.

### items

- **Qué es:** Objetos del juego (armas, armaduras, pociones).
- **Relaciones:** Tiene `rarity`. Puede ser equipado o estar en inventario.

### character_items (Inventario)

- **Qué es:** Tabla pivote/inventario. Relaciona personajes con items.
- **Campos importantes:** `quantity`, `is_equipped`.

### equipment_slots

- **Qué es:** Definición de slots de equipo (Cabeza, Pecho, Arma, etc.).

### character_equipment

- **Qué es:** Relación específica de qué item está en qué slot para un personaje.

### animals

- **Qué es:** Bestiario (monturas y mascotas).
- **Campos importantes:** `is_mountable`, stats propios.
- **Relaciones:** Tiene `rarity`.

### character_animals

- **Qué es:** Animales obtenidos por el personaje.

### loot_tables / loot_entries

- **Qué es:** Sistema de drop. `loot_tables` define grupos de loot, `loot_entries` define los items y sus probabilidades (peso).

### missions / mission_nodes / mission_choices

- **Qué es:** Sistema de misiones. Nodos (pasos) y elecciones dentro de la misión.

### mission_runs / mission_run_choices

- **Qué es:** Historial de ejecución de misiones por parte de un personaje y sus decisiones.

### seasons / season_race_rankings / season_race_winners

- **Qué es:** Sistema de temporadas competitivas, rankings por raza y ganadores históricos.

### chests / chest_openings / chest_opening_rewards

- **Qué es:** Sistema de cofres (loot boxes). Aperturas y recompensas obtenidas.

### matches / match_participants / match_turns

- **Qué es:** Sistema de combate por turnos. Registro de partidas, participantes y log de turnos.

### premium_codes / premium_code_redemptions

- **Qué es:** Sistema de códigos promocionales para obtener estatus premium o recompensas.
