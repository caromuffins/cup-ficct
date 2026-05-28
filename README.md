# Sistema CUP FICCT - UAGRM

Sistema web de gestión del Curso de Ubicación Profesional (CUP) de la Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones, Universidad Autónoma Gabriel René Moreno.

---

## Requisitos previos

Antes de comenzar, asegurate de tener instalado en tu computadora:

| Herramienta | Versión recomendada | Descarga |
|---|---|---|
| PHP | 8.2 o superior | https://www.php.net/downloads |
| Composer | 2.x | https://getcomposer.org |
| Node.js | 18 o superior | https://nodejs.org |
| PostgreSQL | 15 o superior | https://www.postgresql.org/download |
| Git | cualquier versión reciente | https://git-scm.com |

---

## Instalación paso a paso

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio>
cd cup-ficct
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias JavaScript

```bash
npm install
```

### 4. Configurar el archivo de entorno

Copiá el archivo de ejemplo y editalo con tus datos:

```bash
cp .env.example .env
```

Luego abrí `.env` y completá estas variables:

```env
APP_NAME="Sistema CUP FICCT"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cup_ficct
DB_USERNAME=tu_usuario_postgres
DB_PASSWORD=tu_contraseña_postgres

STRIPE_KEY=pk_test_...        # tu clave pública de Stripe
STRIPE_SECRET=sk_test_...     # tu clave secreta de Stripe
```

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 6. Crear la base de datos

Abrí pgAdmin o psql y ejecutá:

```sql
CREATE DATABASE cup_ficct;
```

### 7. Ejecutar las migraciones y seeders

```bash
php artisan migrate --seed
```

### 8. Crear el enlace de almacenamiento

```bash
php artisan storage:link
```

### 9. Compilar los assets

```bash
npm run build
```

---

## Ejecutar el proyecto

Necesitás abrir **dos terminales** al mismo tiempo:

**Terminal 1 — servidor PHP:**
```bash
php artisan serve
```

**Terminal 2 — compilación de assets en tiempo real (solo en desarrollo):**
```bash
npm run dev
```

Luego abrí el navegador en: **http://127.0.0.1:8000**

---

## Credenciales de prueba

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | admin@cup.com | password |
| Postulante | postulante@test.com | password |

> Si el seeder no crea estos usuarios, registrate como postulante desde la página de registro.

---

## Pago con Stripe (modo prueba)

Para probar el flujo de pago usá esta tarjeta de prueba:

| Campo | Valor |
|---|---|
| Número | `4242 4242 4242 4242` |
| Vencimiento | cualquier fecha futura (ej. `12/29`) |
| CVC | cualquier 3 dígitos (ej. `123`) |
| Nombre | cualquier valor |

---

## Tecnologías utilizadas

- **Backend:** Laravel 13 (PHP 8.2)
- **Frontend:** Blade + Tailwind CSS
- **Base de datos:** PostgreSQL
- **Pagos:** Stripe Checkout
- **Autenticación:** Laravel Breeze

---

## Solución de problemas comunes

**Error: `php_pgsql` extension not found**
Habilitá la extensión en tu `php.ini`:
```ini
extension=pdo_pgsql
extension=pgsql
```

**Error: `Key not found` o página en blanco**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**Error de permisos en storage**
```bash
php artisan storage:link
```
