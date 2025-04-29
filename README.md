# REST Wallet Service

Este proyecto es el **puente REST** que consume el **servicio SOAP** de la billetera virtual. Usa Laravel + Sail y expone endpoints REST que interactúan exclusivamente con el SOAP Server.

## 📦 Tecnologías usadas

- PHP 8.4 con Laravel
- Laravel Sail (Docker)


## 🚀 Instalación y Puesta en Marcha

```bash
# Clona el repositorio
git clone https://github.com/tuusuario/rest-wallet.git
cd rest-wallet

# Copia el archivo .env de ejemplo
cp .env.example .env

# (opcional) Modifica APP_PORT=9001 si estás usando otro puerto
# Verifica que la variable SOAP_SERVER_URL esté apuntando al puerto correcto, por defecto:
 SOAP_SERVER_URL=http://host.docker.internal:9000/soap/server

# Levanta el entorno con Sail
./vendor/bin/sail up -d

# Instala dependencias
./vendor/bin/sail composer install

# Genera la clave de la app
./vendor/bin/sail artisan key:generate

# Verifica que esté funcionando en: http://localhost:9001
```

## 📡 Endpoints REST Disponibles

- `POST /api/registro-cliente`
- `POST /api/recargar-billetera`
- `POST /api/generar-compra`
- `POST /api/confirmar-compra`
- `POST /api/consultar-saldo`

Todos los endpoints se conectan internamente al servicio SOAP.

## 🧪 Tests

```bash
./vendor/bin/sail artisan test
```

## 📁 Estructura del Proyecto

- `app/Http/Controllers/Api`: Controladores que reciben las solicitudes del cliente y llaman al servicio SOAP.
- `app/Services/SoapClientService.php`: Cliente SOAP centralizado.
- `routes/api.php`: Rutas REST.

## 📝 Notas

- El único componente con acceso directo a la base de datos es el servicio SOAP.
- Este REST actúa como *pasarela* y no realiza validaciones ni persistencias directamente.

## 🧠 Buenas Prácticas

- Manejo de errores con estructura estándar `{ codigo, mensaje, data }`.
- Separación de responsabilidades.
- Uso de Laravel Sail para portabilidad.

---

## 🧑‍💻 Autor

**Miguel Villalba**  
Backend Developer - Prueba Técnica ePayco  
✉️ mike.mavc27@gmail.com

---

## 📄 Licencia

Este proyecto está bajo licencia MIT.
