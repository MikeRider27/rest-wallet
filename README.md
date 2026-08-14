# REST Wallet Service

Este proyecto es el **puente REST** que consume el **servicio SOAP** de la billetera virtual (`soap-wallet`) y lo expone como API REST para `billetera-virtual` (React). Usa Laravel + Sail.

## 📦 Tecnologías usadas

- PHP 8.4 con Laravel 12
- Laravel Sail (Docker)
- Sin base de datos: es un proxy sin estado (session/cache/queue en memoria)

## 🏗️ Arquitectura interna

```
billetera-virtual (localhost:3000)
        │  POST /api/... (CORS habilitado solo para FRONTEND_URL)
        ▼
Controllers (Api\ClienteController, BilleteraController, CompraController)
        │  forwardToSoap() en el Controller base
        ▼
SoapClientService                    ← agrega header X-API-KEY, timeout
        │  POST SOAP_SERVER_URL
        ▼
soap-wallet (localhost:9002)
```

- `Controller::forwardToSoap()` centraliza el llamado: si `soap-wallet` no responde (caído, timeout, credencial inválida), devuelve `{codigo: '99', mensaje, data: null}` con status `502` en vez de dejar que la excepción reviente como un 500 crudo de Laravel.
- `SoapClientService` arma el `SoapClient` con el header `X-API-KEY` (debe coincidir con el `SOAP_API_KEY` configurado en `soap-wallet`) y loguea cualquier falla antes de convertirla en `SoapGatewayException`.
- Los controllers reciben `SoapClientService` por inyección de dependencias (no `new SoapClientService()`), lo que permite mockearlo en tests.

## 🚀 Instalación y Puesta en Marcha

```bash
# Clona el repositorio
git clone https://github.com/MikeRider27/rest-wallet.git
cd rest-wallet

# Copia el archivo .env de ejemplo
cp .env.example .env
```

Variables relevantes en `.env`:

```env
APP_PORT=9001

# Debe apuntar al soap-wallet real y coincidir con su SOAP_API_KEY
SOAP_SERVER_URL=http://host.docker.internal:9002/soap/server
SOAP_API_KEY=changeme
SOAP_TIMEOUT=10

# Origen permitido por CORS (billetera-virtual)
FRONTEND_URL=http://localhost:3000
```

```bash
# Levanta el entorno con Sail
./vendor/bin/sail up -d

# Instala dependencias
./vendor/bin/sail composer install

# Genera la clave de la app
./vendor/bin/sail artisan key:generate

# Verifica que esté funcionando en: http://localhost:9001
```

> No hace falta configurar ni migrar ninguna base de datos: `rest-wallet` no persiste nada, solo reenvía a `soap-wallet`.

## 📡 Endpoints REST Disponibles

Públicos:

- `POST /api/registro-cliente` — ahora requiere `password` (mínimo 6 caracteres) además de `documento`/`nombre`/`email`/`celular`.
- `POST /api/login` — `{ documento, password }` → `{ codigo, mensaje, data: { token, documento, nombre, celular } }`.

Protegidos (requieren header `Authorization: Bearer <token>`, el que devuelve `/api/login`):

- `POST /api/recargar-billetera` — `{ monto }`
- `POST /api/consultar-saldo` — sin body
- `POST /api/generar-compra` — `{ montoCompra }`
- `POST /api/confirmar-compra` — `{ sessionId, token }` (`token` acá es el código OTP de 6 dígitos de la compra, no el token de sesión)

Sin el header `Authorization`, los endpoints protegidos devuelven `401` con `{codigo: '99', mensaje: 'No autenticado', data: null}` — este chequeo es solo de presencia; la validez real del token la resuelve `soap-wallet` (rest-wallet sigue sin base de datos propia).

Todos devuelven el mismo contrato `{codigo, mensaje, data}` que expone `soap-wallet`. Si el servicio SOAP no responde, el endpoint devuelve `502` con `{codigo: '99', mensaje: '...', data: null}`.

## 🧪 Tests

```bash
./vendor/bin/sail artisan test
```

`tests/Feature/WalletApiTest.php` mockea `SoapClientService` (sin red real) y cubre, por cada endpoint: que los parámetros se reenvían en el orden correcto hacia `soap-wallet`, validación de campos requeridos (422), y el caso de `soap-wallet` caído (502).

## 📁 Estructura del Proyecto

- `app/Http/Controllers/Api`: Controllers que reciben las solicitudes REST y llaman al servicio SOAP vía `forwardToSoap()` (definido en `app/Http/Controllers/Controller.php`). Incluye `AuthController` (login).
- `app/Http/Middleware/RequireBearerToken.php`: exige el header `Authorization: Bearer` en los endpoints protegidos.
- `app/Services/SoapClientService.php`: Cliente SOAP centralizado (auth, timeout, logging).
- `app/Exceptions/SoapGatewayException.php`: excepción uniforme para cualquier falla de comunicación con `soap-wallet`.
- `config/cors.php`: habilita CORS solo para `FRONTEND_URL`.
- `routes/api.php`: Rutas REST (registradas vía `bootstrap/app.php`).

## 📝 Notas

- El único componente con acceso directo a la base de datos es `soap-wallet`. Este REST actúa como *pasarela* y no realiza persistencia propia.
- `soap-wallet` exige el header `X-API-KEY` desde la versión con autenticación — `SOAP_API_KEY` acá debe ser exactamente el mismo valor configurado allá.

## 🧠 Buenas Prácticas

- Manejo de errores homogéneo con estructura estándar `{ codigo, mensaje, data }`, incluso ante fallas de red hacia `soap-wallet`.
- Inyección de dependencias en los controllers (testeable sin red real).
- CORS restringido al origen real del frontend.
- Sin dependencias de base de datos innecesarias.
- Registro de rutas por el mecanismo estándar de Laravel 12 (`bootstrap/app.php`), sin providers legados.
- Uso de Laravel Sail para portabilidad.

---

## 🧑‍💻 Autor

**Miguel Villalba**
Backend Developer - Prueba Técnica ePayco
✉️ mike.mavc27@gmail.com

---

## 📄 Licencia

Este proyecto está bajo licencia MIT.
