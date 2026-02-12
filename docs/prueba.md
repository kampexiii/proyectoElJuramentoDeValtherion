Instrucciones detalladas para pruebas en red local (LAN)
para cuando tengamos que implementarlo para poder probarlo publicamente

ACTÚA COMO: DevOps/Full-Stack (Laravel + Vite) para pruebas en red local (LAN).
OBJETIVO: que mi proyecto “El Juramento de Valtherion” funcione en OTROS ordenadores de la misma red (cable o Wi-Fi) SIN desplegar en internet.

CONDICIONES

- Entorno: Windows + XAMPP (opcional) + Laravel (php artisan) + SQLite.
- Red: aula (cable) o casa (Wi-Fi), todos en la MISMA red/subred.
- Necesito poder abrir el sitio desde otros PCs con una URL tipo: http://IP_DE_MI_PC:8000
- Debe funcionar con assets (CSS/JS) sin fallar.
- Quiero 2 modos: (A) Modo estable “clase” (build) y (B) Modo dev con Vite (HMR) solo si lo pido.

REGLAS DURAS

1. NO se despliega a internet. SOLO LAN.
2. NO improvisar: escribir EXACTAMENTE los comandos y cambios.
3. Si hay decisiones, priorizar “modo estable” (npm run build).
4. Avisar de bloqueos típicos: firewall, aislamiento de clientes, subred distinta, puertos bloqueados, SQLite locked.
5. Todo debe acabar con un CHECKLIST final para validar en 3 minutos.

========================
FASE 0 — AUDITORÍA DE RED
========================
PASO 0.1 — Obtener IP local del PC servidor
En Windows (PowerShell):

- Ejecutar: ipconfig
- Guardar el “IPv4 Address”, por ejemplo: 192.168.1.50

PASO 0.2 — Confirmar que el otro PC puede ver al servidor
Desde el otro PC (PowerShell):

- Ejecutar: ping 192.168.1.50
  Resultados:
- Si responde -> OK
- Si NO responde -> explicar:
  a) “Aislamiento de clientes” en red del instituto (muy común)
  b) Firewall del PC servidor bloqueando ICMP/puerto
  c) Subred distinta (ej. 192.168.0.x vs 192.168.1.x)
  -> Solución: probar con hotspot móvil o pedir a red del centro “sin aislamiento”.

========================
FASE 1 — MODO ESTABLE (RECOMENDADO) SIN VITE DEV SERVER
(Lo más fiable para clase: sin problemas de assets)
========================
PASO 1.1 — Ajustar APP_URL para LAN
En el archivo .env del proyecto:

- Cambiar/añadir:
  APP_URL=http://192.168.1.50:8000

NOTA:

- Si hoy lo pruebas en casa y mañana en clase, volverás a cambiar la IP.
- No usar localhost, porque los demás PCs no lo resuelven en tu máquina.

PASO 1.2 — Limpiar cachés de Laravel
En la raíz del proyecto:

- Ejecutar:
  php artisan optimize:clear

PASO 1.3 — Compilar assets de producción

- Ejecutar:
  npm install
  npm run build

PASO 1.4 — Levantar Laravel accesible por la red

- Ejecutar:
  php artisan serve --host=0.0.0.0 --port=8000

IMPORTANTE:

- Mantener esta terminal abierta.
- Esto convierte TU PC en servidor para el resto de PCs.

PASO 1.5 — Firewall Windows (IMPRESCINDIBLE EN AULA)
Si aparece popup:

- Permitir en redes privadas.

Si NO aparece o no entra nadie:

- Abrir “Firewall de Windows con seguridad avanzada”
- Reglas de entrada -> Nueva regla:
    - Tipo: Puerto
    - TCP
    - Puerto local específico: 8000
    - Permitir conexión
    - Perfil: Privado (y Dominio si el instituto usa dominio)
    - Nombre: “Laravel 8000 LAN”

PASO 1.6 — Acceso desde otros PCs
En un PC de la red, abrir navegador:

- URL:
  http://192.168.1.50:8000

PASO 1.7 — Verificación rápida de assets
En el PC cliente:

- Abrir F12 -> Network
- Confirmar que cargan:
  /build/assets/... (si usas Vite build)
  Si falla:
- Revisar que hiciste npm run build
- Revisar APP_URL correcto
- Revisar que no hay mixed content si usas https (NO usar https en LAN)

========================
FASE 2 — MODO DEV (SOLO SI NECESITO HOT RELOAD)
(Esto puede fallar en aulas; usar solo si lo pido)
========================
PASO 2.1 — Laravel server accesible

- Ejecutar:
  php artisan serve --host=0.0.0.0 --port=8000

PASO 2.2 — Vite dev server accesible en LAN

- Ejecutar:
  npm run dev -- --host 0.0.0.0 --port=5173

PASO 2.3 — Configurar HMR para que el cliente cargue desde tu IP
Editar vite.config.js y asegurar:

- server.host = true
- server.hmr.host = “192.168.1.50”
  Ejemplo:
  server: {
  host: true,
  port: 5173,
  hmr: { host: "192.168.1.50" }
  }

PASO 2.4 — Abrir firewall para Vite

- Crear regla de entrada TCP 5173 igual que antes.

PASO 2.5 — Probar desde otro PC

- Abrir:
  http://192.168.1.50:8000
- Si CSS/JS no cargan:
    - revisar consola (requests a 5173)
    - confirmar firewall 5173
    - confirmar vite.config.js hmr.host

========================
FASE 3 — SQLITE Y CONCURRENCIA (IMPORTANTE PARA AULA)
========================
RIESGO:

- Muchos PCs haciendo acciones = muchos writes.
- SQLite puede dar “database is locked”.

MEDIDAS:

1. Mantener polling a 2s o 3s (NO menos).
2. Reducir escrituras innecesarias:
    - no guardar logs redundantes cada tick
3. Si aparece “locked”:
    - aumentar intervalo de polling
    - revisar transacciones largas
    - (si el aula es grande) considerar migrar a MySQL solo para demo, pero NO es requisito aquí.

========================
FASE 4 — CHECKLIST FINAL (3 MINUTOS)
========================

1. IP del servidor:
    - ipconfig -> IPv4 = ****\_\_\_\_****
2. Ping desde cliente:
    - ping IP -> responde (sí/no)
3. Laravel:
    - php artisan serve --host=0.0.0.0 --port=8000
4. Modo estable:
    - npm run build (hecho)
5. Firewall:
    - puerto 8000 abierto (sí/no)
    - (si dev) puerto 5173 abierto (sí/no)
6. Cliente abre:
    - http://IP:8000
7. Assets:
    - /build/assets cargan sin 404 (sí/no)
8. Login:
    - probar crear sala / entrar (sí/no)
9. Polling:
    - verificar que el turno se actualiza sin recargar manualmente (sí/no)

SALIDA ESPERADA

- Cualquier PC de la misma red abre la web con mi IP.
- Puede loguearse y usar la app.
- En batallas, se actualiza solo por polling.
- En modo estable, no hay problemas de Vite.

COMMIT (SI APLICA DOCUMENTARLO)
docs(dev): add LAN testing steps for classroom and home network
