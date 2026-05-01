# Manual de Usuario

**Proyecto:** Caminos de Oportunidades
**Hecho por:** Roger Malgrat y Pol Balaguer

---

## Índice

1. [Introducción](#1-introducción)
2. [Requisitos previos](#2-requisitos-previos)
3. [Descarga del proyecto](#3-descarga-del-proyecto)
4. [Configuración del backend (Laravel)](#4-configuración-del-backend-laravel)
5. [Configuración del frontend (Vue)](#5-configuración-del-frontend-vue)
6. [Iniciar el proyecto](#6-iniciar-el-proyecto)
7. [Acceso a la aplicación](#7-acceso-a-la-aplicación)
8. [Usuarios por defecto](#8-usuarios-por-defecto)
9. [Solución de problemas comunes](#9-solución-de-problemas-comunes)

---

## 1. Introducción

**Caminos de Oportunidades** es una aplicación web de tipo SPA (Single Page Application) desarrollada con **Laravel 12** en el backend y **Vue 3** en el frontend. La plataforma conecta candidatos y empresas, permitiendo gestionar perfiles de usuario, ofertas y procesos relacionados con oportunidades laborales.

Este manual está dirigido al usuario que necesita poner en marcha el proyecto en su entorno local por primera vez.

---

## 2. Requisitos previos

Antes de iniciar la instalación, asegúrate de tener instalado en tu equipo:

| Software       | Versión mínima | Notas |
|----------------|----------------|-------|
| **PHP**        | 8.2 o superior | Incluido en XAMPP |
| **Composer**   | 2.x            | Gestor de dependencias de PHP |
| **Node.js**    | 16 o superior  | Recomendado 18+ |
| **npm**        | 8 o superior   | Se instala junto con Node |
| **MySQL / MariaDB** | 5.7+ / 10.4+ | Incluido en XAMPP |
| **Git**        | Cualquiera     | Para clonar el repositorio |

> **Recomendación:** instala **XAMPP** para disponer de PHP, MySQL y phpMyAdmin de forma sencilla.

---

## 3. Descarga del proyecto

Abre una terminal y sitúate en la carpeta `htdocs` de XAMPP:

```bash
cd c:/xampp/htdocs
```

Clona el repositorio:

```bash
git clone <url-del-repositorio> caminosdeoportunidades
cd caminosdeoportunidades
```

---

## 4. Configuración del backend (Laravel)

### 4.1. Instalar dependencias de PHP

```bash
composer install
```

### 4.2. Crear el archivo de entorno

Copia el archivo `.env.example` y renómbralo como `.env`:

```bash
cp .env.example .env
```

### 4.3. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 4.4. Crear la base de datos

1. Inicia **Apache** y **MySQL** desde el panel de XAMPP.
2. Abre [phpMyAdmin](http://localhost/phpmyadmin) en tu navegador.
3. Crea una nueva base de datos llamada, por ejemplo, `caminos_oportunidades` (codificación `utf8mb4_unicode_ci`).

### 4.5. Configurar las credenciales en `.env`

Abre el archivo `.env` y ajusta los siguientes valores:

```dotenv
APP_NAME="Caminos de Oportunidades"
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=caminos_oportunidades
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

> **Nota:** en XAMPP, por defecto el usuario es `root` y la contraseña está vacía.

### 4.6. Ejecutar migraciones y seeders

Este paso crea todas las tablas necesarias y rellena la base de datos con usuarios, roles y datos de ejemplo:

```bash
php artisan migrate --seed
```

---

## 5. Configuración del frontend (Vue)

Instala las dependencias de JavaScript:

```bash
npm install
```

Si aparece algún aviso sobre paquetes opcionales o de rendimiento, puedes ignorarlo siempre que la instalación finalice correctamente.

---

## 6. Iniciar el proyecto

Para que la aplicación funcione necesitas mantener **dos terminales abiertas** al mismo tiempo, una para el backend y otra para el frontend.

### Terminal 1 — Backend (Laravel)

```bash
php artisan serve
```

Esto levanta el servidor de Laravel en `http://localhost:8000`.

### Terminal 2 — Frontend (Vite + Vue)

```bash
npm run dev
```

Esto inicia el servidor de desarrollo de Vite, que compila los archivos de Vue en tiempo real cada vez que detecta un cambio.

> **Importante:** no cierres ninguna de las dos terminales mientras estés trabajando con el proyecto.

---

## 7. Acceso a la aplicación

Una vez iniciados ambos servidores, abre tu navegador y entra en:

```
http://localhost:8000
```

Verás la página principal de **Caminos de Oportunidades**.

---

## 8. Usuarios por defecto

Los seeders crean varios usuarios listos para iniciar sesión:

| Rol       | Correo               | Contraseña |
|-----------|----------------------|------------|
| Admin     | `admin@demo.com`     | `12345678` |
| Usuario   | `user@demo.com`      | `12345678` |

Usa el usuario **Admin** para acceder al panel de administración y gestionar todos los recursos de la plataforma.

*Manual de Usuario — Caminos de Oportunidades*
*Hecho por Roger Malgrat y Pol Balaguer*
