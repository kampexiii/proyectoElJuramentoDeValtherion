# Resumen de tests (12-02-2026)

## Contexto

Se ajustaron los tests para alinearlos con las validaciones actuales del proyecto (consentimiento de cookies/terminos y reglas de perfil) y se configuro SQLite en memoria para el entorno de testing.

## Cambios principales

- Tests de login/registro: se incluyen `accept_cookies` y `accept_terms`.
- Tests de login: se fuerza una ruta protegida antes del login para respetar el `intended`.
- Tests de perfil: el nombre de usuario cumple el patron permitido (solo letras, numeros, \_ y -).
- Entorno de tests: `DB_CONNECTION=sqlite` y `DB_DATABASE=:memory:` en `phpunit.xml`.

## Tests nuevos

- `tests/Feature/BaselineAccessTest.php`: cobertura minima de accesos publicos/privados, admin, limite de personaje y misiones publicadas.

## Resultado

- `php artisan test`: 35 passed, 0 failed.

## Notas

- No se ejecuta Reverb/WebSockets en los tests.
- Las migraciones se ejecutan automaticamente con `RefreshDatabase`.
