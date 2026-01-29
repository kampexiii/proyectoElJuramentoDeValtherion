# Calendario intensivo — El Juramento de Valtherion (Laravel 12)

Entrega oficial: **viernes 13 de febrero de 2026**
Objetivo principal: **sitio completo (API + web) listo como tarde el lunes 2 de febrero de 2026**.

## Reglas clave (no negociables)

- Zona logueada **sin scroll vertical ni horizontal**.
- **Doble navbar** (superior e inferior) en TODO lo logueado.
- **SEO-first** y **responsive-first**.
- **No tocar** `.env` ni `database/database.sqlite`.
- **No crear páginas ni layouts nuevos**: solo completar el `<main>` de cada sección existente.
- **Admin** debe mostrar bloque visible de sesiones/cookies.
- Cierre diario: **bitácora + commit + push**.

---

## Estado base (fuentes revisadas)

- Calendario original: `docs/calendario.md`.
- Bitácoras: `docs/bitacora/*.md`.
- Documentos: `docs/COMANDOS.md`, `docs/CHECKLIST_ENTREGA.md`, `docs/MODELO_BD.md`, `docs/entregable_db.md`.

---

## Plan intensivo (del 2026-01-29 al 2026-02-02)

### 2026-01-29 (Jueves)

**Objetivo del día**
- Dejar patrón UI completo para zona logueada y cerrar **Home + Perfil + Ajustes** sin scroll.

**Secciones a cerrar**
- Home
- Perfil
- Ajustes

**Archivos a tocar (solo main + estilos necesarios)**
- `resources/views/game/home/index.blade.php`
- `resources/views/game/perfil.blade.php`
- `resources/views/game/ajustes.blade.php`
- `resources/css/game/components/home_index.css`
- `resources/css/game/theme.css` (si hay nuevos componentes)

**Datos mínimos + fallback**
- Mostrar resumen de personaje si existe (tabla `characters`), fallback con placeholders reales.
- Mantener crónica mensual actual sin romper layout.

**Checklist de pruebas**
- Mobile: sin scroll vertical/horizontal en las 3 secciones.
- Navbars superior e inferior visibles siempre.
- Textos y botones sin solape.

**Hecho hoy**
- Base de personaje (crear/editar/borrar) y bloqueo de secciones sin personaje.

**Cierre obligatorio**
- Bitácora del día.
- Commit/push con mensaje claro en castellano.

---

### 2026-01-30 (Viernes)

**Objetivo del día**
- Completar **Tienda + Inventario** con paneles reales (cards/tabla/acciones) sin scroll.

**Secciones a cerrar**
- Tienda
- Inventario

**Archivos a tocar**
- `resources/views/game/tienda.blade.php`
- `resources/views/game/inventario.blade.php`
- `resources/css/game/theme.css` (componentes compartidos)

**Datos mínimos + fallback**
- Si hay `items` y `character_items`, listar 4–6; si no, dataset local dummy (sin BD).
- Botones de acción visibles pero sin lógica avanzada.

**Checklist de pruebas**
- Mobile sin scroll.
- Cards/tabla con truncado correcto.
- Navbars visibles.

**Cierre obligatorio**
- Bitácora del día.
- Commit/push.

---

### 2026-01-31 (Sábado)

**Objetivo del día**
- Completar **Misiones** con pestañas internas y resolver **Recompensas** como tab interno (no ruta nueva).

**Secciones a cerrar**
- Misiones
- Recompensas (tab interna de Misiones)

**Archivos a tocar**
- `resources/views/game/misiones.blade.php`
- `resources/css/game/theme.css`

**Datos mínimos + fallback**
- Si no hay tablas de misiones, listar 3–5 misiones ficticias.
- Recompensas como panel de “cofres/objetos” con placeholders reales.

**Checklist de pruebas**
- Tabs sin scroll.
- Recompensas integradas sin ruta nueva.
- Navbars visibles.

**Cierre obligatorio**
- Bitácora del día.
- Commit/push.

---

### 2026-02-01 (Domingo)

**Objetivo del día**
- Completar **Peleas** (Batallas/Duelos/Historial como tabs) y **Chat**.

**Secciones a cerrar**
- Peleas
- Chat

**Archivos a tocar**
- `resources/views/game/peleas.blade.php`
- `resources/views/game/chat.blade.php`
- `resources/css/game/theme.css`

**Datos mínimos + fallback**
- Listados ficticios de combates y sala de chat con mensajes dummy.

**Checklist de pruebas**
- Mobile sin scroll.
- Tabs y paneles sin desbordes.
- Navbars visibles.

**Cierre obligatorio**
- Bitácora del día.
- Commit/push.

---

### 2026-02-02 (Lunes)

**Objetivo del día**
- Completar **Admin** con bloque visible de **sesiones/cookies** y revisión global “sin scroll”.

**Secciones a cerrar**
- Admin
- Revisión de todas las secciones logueadas

**Archivos a tocar**
- `resources/views/admin/index.blade.php`
- `routes/web.php` (solo si hace falta para datos de sesión)

**Datos mínimos + fallback**
- Mostrar últimos accesos de `sessions` si existe, o mensaje claro de “sin datos”.
- Mostrar cookie de sesión si existe (`request()->cookie(...)`).

**Checklist de pruebas**
- Admin sin scroll, doble navbar visible.
- Home/Tienda/Inventario/Misiones/Peleas/Chat/Perfil/Ajustes sin scroll.

**Cierre obligatorio**
- Bitácora del día.
- Commit/push.

---

## Plan documentación y cierre (del 2026-02-03 al 2026-02-13)

### 2026-02-03 (Martes)
- Memoria: índice y estructura.
- Archivos: `docs/MEMORIA.md`.

### 2026-02-04 (Miércoles)
- Memoria: objetivos, alcance, requisitos, tecnologías.

### 2026-02-05 (Jueves)
- Memoria: arquitectura, rutas y estructura de carpetas.
- Completar `docs/GUIA_INSTALACION.md` y `docs/GUIA_USUARIO.md` si existen.

### 2026-02-06 (Viernes)
- ERD + export SQL.
- Evidencias en `docs/ERD/`.

### 2026-02-07 (Sábado)
- Capturas completas (landing, auth, juego, admin).
- Guardar en `docs/capturas/`.

### 2026-02-08 (Domingo)
- Checklist de entrega final y comandos usados.
- Archivos: `docs/CHECKLIST_ENTREGA.md`, `docs/COMANDOS.md`.

### 2026-02-09 (Lunes)
- Revisión funcional completa y listado de incidencias.

### 2026-02-10 (Martes)
- Memoria final y consistencia de estilo.

### 2026-02-11 (Miércoles)
- Paquete final y estructura de entrega.

### 2026-02-12 (Jueves)
- Buffer para correcciones finales y re‑export SQL si procede.

### 2026-02-13 (Viernes)
- Cierre final, checklist OK y entrega oficial.

---

## Lista final P0 de entrega (imprescindible)

- [ ] Main completo en Home.
- [ ] Main completo en Tienda.
- [ ] Main completo en Inventario.
- [ ] Main completo en Misiones + Recompensas (tab interna).
- [ ] Main completo en Peleas.
- [ ] Main completo en Chat.
- [ ] Main completo en Perfil.
- [ ] Main completo en Ajustes.
- [ ] Admin con bloque visible de sesiones/cookies.
- [ ] Zona logueada sin scroll y doble navbar visible.
- [ ] Bitácoras al día + commits en castellano.
