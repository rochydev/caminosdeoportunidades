# Caminos de Oportunidades

Portal de empleo enfocado en la inclusión laboral. La plataforma conecta a
**personas candidatas** (incluyendo perfiles con algún tipo de diversidad
funcional) con **empresas** que publican ofertas, marcando cuáles están
adaptadas y a qué tipo de adaptación responden.

El proyecto está construido como una SPA: el backend es una API REST en
**Laravel 12** y el frontend una aplicación **Vue 3** servida con Vite.
Ambos viven en el mismo repositorio para simplificar el despliegue en
entornos de prácticas (XAMPP).

> Trabajo de aula desarrollado por **Roger Malgrat** y **Pol Balaguer**.

---

## Índice

1. [Stack y dependencias](#stack-y-dependencias)
2. [Puesta en marcha](#puesta-en-marcha)
3. [Estructura del repositorio](#estructura-del-repositorio)
4. [Modelo de dominio](#modelo-de-dominio)
5. [Autenticación y autorización](#autenticación-y-autorización)
6. [Rutas de la API](#rutas-de-la-api)
7. [Frontend: rutas, layouts y stores](#frontend-rutas-layouts-y-stores)
8. [Catálogos y seeders](#catálogos-y-seeders)
9. [Comandos útiles](#comandos-útiles)
10. [Convenciones y notas](#convenciones-y-notas)

---

## Stack y dependencias

### Backend

- **PHP 8.2+** y **Laravel 12** (sí, aunque la base original venía de un
  proyecto Laravel 10, el `composer.json` declara `laravel/framework: ^12.0`).
- **Laravel Sanctum** para autenticación SPA basada en cookies.
- **spatie/laravel-permission** para roles y permisos.
- **spatie/laravel-medialibrary** para gestionar imágenes de usuario, imágenes
  de ofertas y CVs en PDF.
- **rakutentech/laravel-request-docs** para tener una vista de las rutas
  disponibles durante el desarrollo.
- **orangehill/iseed** para regenerar seeders desde la BBDD cuando hace falta.

### Frontend

- **Vue 3** (Composition API, `<script setup>`).
- **Vite** como bundler.
- **Pinia** + `pinia-plugin-persistedstate` para los stores (auth, idioma,
  histórico de búsqueda…).
- **Vue Router 4** con *guards* por rol.
- **PrimeVue 4** + **Tailwind CSS 4** + `tailwindcss-primeui` para la UI.
- **@casl/ability** + **@casl/vue** para mostrar/ocultar acciones en el panel
  de administración según los permisos efectivos del usuario.
- **Axios** para las llamadas HTTP, **yup** para validaciones,
  **vue-i18n** para traducciones (es / en / fr), **vue-sweetalert2** para
  diálogos y **Quill** para el editor de descripciones de ofertas.

---

## Puesta en marcha

Está pensado para correr en **XAMPP**. Si no usas XAMPP, basta con tener PHP
8.2+, Composer, Node 18+ y un MySQL/MariaDB accesible.

### 1. Clonar el repositorio

Sitúate en `htdocs` (o donde prefieras) y clona el proyecto:

```bash
cd c:/xampp/htdocs
git clone <url-del-repositorio> caminosdeoportunidades
cd caminosdeoportunidades
```

### 2. Configurar el backend

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Crea la base de datos desde phpMyAdmin (por ejemplo
`caminos_oportunidades`, codificación `utf8mb4_unicode_ci`) y ajusta el
`.env`:

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

Las migraciones crean toda la estructura (incluyendo las tablas de Spatie y
las del dominio de empleo) y los seeders dejan datos de prueba listos:

```bash
php artisan migrate --seed
```

Si en algún momento quieres empezar de cero sin tocar la base manualmente:

```bash
php artisan migrate:fresh --seed
```

### 3. Configurar el frontend

```bash
npm install
```

### 4. Levantar la aplicación

En dos terminales distintas:

```bash
# Terminal 1 — API y rutas web de Laravel
php artisan serve

# Terminal 2 — Vite (HMR de Vue)
npm run dev
```

La SPA se sirve a través de Laravel en `http://localhost:8000`. Vite no se
abre como página independiente; solo provee los assets compilados con
hot-reload.

### Usuarios de prueba

El seeder `UsersTableSeeder` crea estos usuarios (todos con contraseña
`12345678`):

| Rol       | Email                  | Notas                          |
|-----------|------------------------|--------------------------------|
| admin     | `admin@demo.com`       | Acceso completo al panel admin |
| candidate | `pol@demo.com`         |                                |
| candidate | `roger@demo.com`       |                                |
| candidate | `arnau@demo.com`       |                                |
| candidate | `pau@demo.com`         |                                |
| company   | `rrhh@mercadona.com`   |                                |
| company   | `rrhh@inditex.com`     |                                |
| company   | `rrhh@telefonica.com`  |                                |

El seeder `CompaniesAndOffersSeeder` añade además empresas extra (ONCE,
fundaciones, etc.) con sus ofertas asociadas.

---

## Estructura del repositorio

```
caminosdeoportunidades/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/   # Controladores REST (Job offers, CV, perfiles...)
│   │   ├── Requests/          # Form Requests (validación)
│   │   └── Resources/         # API Resources (transformación a JSON)
│   ├── Models/                # Eloquent (User, JobOffer, CandidateCv, ...)
│   └── Notifications/         # Reset de contraseña personalizado
├── config/                    # Configuración de Laravel + paquetes
├── database/
│   ├── migrations/            # Esquema de BBDD
│   └── seeders/               # Roles, permisos, usuarios, catálogos, ofertas
├── docs/                      # Manual de usuario y otras notas
├── resources/
│   ├── js/                    # SPA Vue
│   └── views/                 # Blade mínimo (sólo el <html> que monta la SPA)
├── routes/
│   ├── api.php                # Toda la API REST
│   ├── web.php                # Catch-all que devuelve la SPA
│   ├── auth.php               # Login/logout/register para Sanctum
│   └── passwords.php          # Reseteo de contraseña
└── public/                    # Document root servido por Apache/Artisan
```

---

## Modelo de dominio

Las entidades principales son:

- **User** — usuario base con `name`, `surname1`, `surname2`, `alias`,
  `email`, `status`. Tiene relaciones `hasOne` con `CandidateProfile`,
  `CandidateCv` y `CompanyProfile`, y `hasMany` con `JobOffer`,
  `Application`, `Notification` y `AuditLog`. Implementa `HasMedia` de
  Spatie con dos colecciones: `images/users` (avatar) y `cvs` (un único PDF).
- **CandidateProfile** — datos extendidos del candidato (incluye relación
  con `DisabilityType`, ya que el portal pone foco en accesibilidad).
- **CandidateCv** — currículum del candidato (clave primaria `user_id`,
  uno por usuario). Tiene `hasMany` `CvExperience` y `CvEducation`.
- **CompanyProfile** — ficha pública de la empresa (CIF, sector, web,
  descripción, etc.).
- **JobOffer** — oferta publicada por una empresa. Referencia
  `JobCategory`, `ContractType`, `WorkdayType`, `ModalityType`, una imagen
  de cabecera vía media library, y pivotes con `Tag` y `DisabilityType`
  (esta última para indicar a qué adaptaciones responde la oferta).
  El campo `status` distingue entre `DRAFT`, `PUBLISHED`, etc., y solo las
  publicadas se sirven en el endpoint público.
- **Application** — candidatura de un usuario a una oferta. Lleva el
  `status` del proceso y notas internas de la empresa.
- **Notification / AuditLog** — pensadas para trazabilidad y avisos
  internos (no expuestas todavía a una UI completa).

### Catálogos

Tablas pequeñas con valores fijos que alimentan los desplegables de las
ofertas y los filtros del buscador público:

- `JobCategory` — sector profesional.
- `ContractType` — tipo de contrato.
- `WorkdayType` — jornada.
- `ModalityType` — presencial / híbrido / remoto.
- `DisabilityType` — tipos de adaptación / diversidad funcional.

Todos se siembran en `CatalogSeeder` y se exponen como endpoints públicos
de solo lectura para que el frontend los cargue al arrancar.

---

## Autenticación y autorización

### Sanctum (autenticación SPA por cookie)

El frontend hace `axios.get('/sanctum/csrf-cookie')` antes del login y a
partir de ahí todas las llamadas a `/api/*` viajan con la cookie de sesión.
No se usan tokens personales: el flujo es el típico de SPA del mismo
dominio.

Para que la cookie de Sanctum sea válida hay que tener bien configurado
`SANCTUM_STATEFUL_DOMAINS` y `APP_URL` en el `.env`.

### Spatie permission (autorización híbrida)

Este es uno de los puntos sensibles del proyecto, porque tuvo una revisión
explícita: **no basta con `auth:sanctum`** para las rutas específicas de
un rol. El esquema final, declarado en [`routes/api.php`](routes/api.php),
es **híbrido**:

- **Zona admin** (usuarios, categorías, roles, permisos) → middleware
  `permission:` por acción. Ejemplos:
  - `permission:user-list`, `user-create`, `user-edit`, `user-delete`
  - `permission:category-list`, `category-create`, `category-edit`, `category-delete`
  - `permission:role-list`, `role-create`, `role-edit`, `role-delete`
  - `permission:permission-list`, `permission-create`, `permission-edit`, `permission-delete`

- **Zona de dominio** (escritura de ofertas, CV, perfiles) → middleware
  `role:`:
  - `role:company|admin` para crear/editar/borrar ofertas, gestionar imagen
    de oferta o listar candidaturas por oferta.
  - `role:candidate|admin` para crear/editar el CV, gestionar experiencias
    y educación, listar las propias candidaturas, etc.

- **Lecturas compartidas** (listar y ver detalle de ofertas o
  candidaturas estando autenticado) → solo `auth:sanctum`.

- **Operaciones de pertenencia** (editar el propio usuario o avatar) →
  `auth:sanctum` + comprobación de propiedad en el controlador. Por
  ejemplo, en `UserController::update` se valida que:
  ```
  auth()->id() === $user->id  ||  auth()->user()->can('user-edit')
  ```
  Y solo se llama a `syncRoles()` si el solicitante tiene `user-edit`, para
  que un candidato no pueda auto-ascenderse pasando un `role_id` en el
  cuerpo.

> Los permisos se definen sobre el guard `web`, pero funcionan con Sanctum
> porque al pasar por `auth:sanctum` el guard activo queda fijado y la
> comprobación se hace vía Gates internos de Spatie.
>
> Los nombres de los permisos (`user-*`, `role-*`, `permission-*`,
> `category-*`) coinciden uno a uno con las acciones de CASL en el panel
> admin del frontend — por eso **no se deben renombrar** sin tocar también
> el cliente.

### Roles

Solo hay tres, creados en `RolesTableSeeder` con guard `web`:

| Rol         | Para qué sirve                                                  |
|-------------|-----------------------------------------------------------------|
| `admin`     | Acceso total al panel `/admin` y a todas las acciones.          |
| `candidate` | Puede tener perfil, CV, candidaturas. Navega ofertas.           |
| `company`   | Puede tener perfil de empresa y publicar/editar ofertas.        |

### CASL en el frontend

Al iniciar sesión, el frontend hace `GET /api/abilities`, que devuelve el
array plano de nombres de permisos que el usuario tiene por sus roles.
Ese listado alimenta la `ability` de CASL (`resources/js/services/ability.js`)
y los componentes usan `<Can I="user-create">…</Can>` o equivalentes
para mostrar/ocultar botones.

Esto es **solo presentacional**: la verificación real sigue ocurriendo en
el backend.

---

## Rutas de la API

Todas viven en [`routes/api.php`](routes/api.php). El prefijo `/api` lo
añade el `RouteServiceProvider`.

### Públicas (sin autenticación)

| Método | Ruta                          | Descripción                                       |
|--------|-------------------------------|---------------------------------------------------|
| GET    | `/api/public/job-offers`      | Listado paginado de ofertas `PUBLISHED` con filtros (search, city, category_id, contract_type_id, modality_id, workday_type_id, is_adapted). |
| GET    | `/api/public/job-offers/{id}` | Detalle de una oferta publicada.                  |
| GET    | `/api/category-list`          | Categorías del antiguo blog (legacy de la base).  |
| GET    | `/api/disability-types`       | Catálogo de tipos de diversidad.                  |
| GET    | `/api/job-categories`         | Catálogo de categorías de empleo.                 |
| GET    | `/api/contract-types`         | Catálogo de tipos de contrato.                    |
| GET    | `/api/workday-types`          | Catálogo de jornadas.                             |
| GET    | `/api/modality-types`         | Catálogo de modalidades (presencial, remoto…).    |

### Autenticadas

Todas requieren `auth:sanctum`. Las que llevan rol/permiso se anotan en la
columna “Protección extra”.

#### Perfil propio

| Método | Ruta                  | Protección extra                       |
|--------|-----------------------|----------------------------------------|
| GET    | `/api/user`           | —                                      |
| GET    | `/api/user/signin`    | —                                      |
| PUT    | `/api/user`           | —                                      |
| PUT    | `/api/users/{user}`   | Propiedad o `user-edit` (controlador)  |
| POST   | `/api/users/updateimg`| Propiedad o `user-edit` (controlador)  |
| GET    | `/api/abilities`      | —                                      |

#### Administración

| Método | Ruta                              | Permiso                  |
|--------|-----------------------------------|--------------------------|
| GET    | `/api/users`                      | `user-list`              |
| POST   | `/api/users`                      | `user-create`            |
| GET    | `/api/users/{user}`               | `user-list`              |
| DELETE | `/api/users/{user}`               | `user-delete`            |
| GET    | `/api/categories`                 | `category-list`          |
| POST   | `/api/categories`                 | `category-create`        |
| PUT    | `/api/categories/{category}`      | `category-edit`          |
| DELETE | `/api/categories/{category}`      | `category-delete`        |
| GET    | `/api/roles`, `/api/role-list`    | `role-list`              |
| POST   | `/api/roles`                      | `role-create`            |
| PUT    | `/api/roles/{role}`               | `role-edit`              |
| DELETE | `/api/roles/{role}`               | `role-delete`            |
| GET    | `/api/role-permissions/{id}`      | `role-list`              |
| PUT    | `/api/role-permissions`           | `role-edit`              |
| GET    | `/api/permissions`                | `permission-list`        |
| POST   | `/api/permissions`                | `permission-create`      |
| PUT    | `/api/permissions/{permission}`   | `permission-edit`        |
| DELETE | `/api/permissions/{permission}`   | `permission-delete`      |

#### Ofertas de empleo

| Método | Ruta                                 | Protección                        |
|--------|--------------------------------------|-----------------------------------|
| GET    | `/api/job-offers`                    | `auth:sanctum`                    |
| GET    | `/api/job-offers/recommended`        | `role:candidate|admin`            |
| GET    | `/api/job-offers/{jobOffer}`         | `auth:sanctum`                    |
| POST   | `/api/job-offers`                    | `role:company|admin`              |
| PUT    | `/api/job-offers/{jobOffer}`         | `role:company|admin`              |
| DELETE | `/api/job-offers/{jobOffer}`         | `role:company|admin`              |
| POST   | `/api/job-offers/{jobOffer}/image`   | `role:company|admin`              |
| DELETE | `/api/job-offers/{jobOffer}/image`   | `role:company|admin`              |

> La ruta `/recommended` se declara **antes** de `/{jobOffer}` para que el
> router no la trate como un id. Lo mismo aplica a las rutas
> `my-candidatures` y `by-offer` dentro de candidaturas.

#### Perfiles de empresa y candidato

| Método | Ruta                                          | Protección                |
|--------|-----------------------------------------------|---------------------------|
| GET    | `/api/company-profiles`                       | `auth:sanctum`            |
| GET    | `/api/company-profiles/{userId}`              | `auth:sanctum`            |
| POST   | `/api/company-profiles`                       | `role:company|admin`      |
| PUT    | `/api/company-profiles/{userId}`              | `role:company|admin`      |
| GET    | `/api/candidate-profiles`                     | `auth:sanctum`            |
| GET    | `/api/candidate-profiles/{candidateProfile}`  | `auth:sanctum`            |
| POST   | `/api/candidate-profiles`                     | `role:candidate|admin`    |
| PUT    | `/api/candidate-profiles/{candidateProfile}`  | `role:candidate|admin`    |

#### CV del candidato

Todo el bloque va dentro de `Route::middleware('role:candidate|admin')`:

| Método | Ruta                                  |
|--------|---------------------------------------|
| GET    | `/api/candidate-cv`                   |
| POST   | `/api/candidate-cv` (upsert)          |
| POST   | `/api/cv-experiences`                 |
| PUT    | `/api/cv-experiences/{experience}`    |
| DELETE | `/api/cv-experiences/{experience}`    |
| POST   | `/api/cv-educations`                  |
| PUT    | `/api/cv-educations/{education}`      |
| DELETE | `/api/cv-educations/{education}`      |

#### Candidaturas

| Método | Ruta                                          | Protección              |
|--------|-----------------------------------------------|-------------------------|
| GET    | `/api/job-applications/my-candidatures`       | `role:candidate|admin`  |
| GET    | `/api/job-applications/by-offer/{offerId}`    | `role:company|admin`    |
| GET    | `/api/job-applications`                       | `auth:sanctum`          |
| GET    | `/api/job-applications/{jobApplication}`      | `auth:sanctum`          |
| POST   | `/api/job-applications`                       | `auth:sanctum`          |
| PUT    | `/api/job-applications/{jobApplication}`      | `auth:sanctum`          |

---

## Frontend: rutas, layouts y stores

Punto de entrada: [`resources/js/app.js`](resources/js/app.js), que monta
`main.vue` y registra los plugins (PrimeVue, Pinia, Router, i18n, CASL,
FontAwesome, Quill).

### Layouts

Cada zona tiene su layout en [`resources/js/layouts/`](resources/js/layouts/):

- `GuestLayout.vue` — navbar y footer públicos.
- `UserLayout.vue` — panel del candidato (`/app/...`).
- `CompanyLayout.vue` — panel de empresa (`/empresa/...`).
- `AdminLayout.vue` — panel admin (`/admin/...`).

### Rutas y guards

Definidas en [`resources/js/routes/routes.js`](resources/js/routes/routes.js).
Hay tres guards:

- `requireLogin` — fuerza login y, si el usuario es `company` (y no admin),
  lo redirige a `/empresa` para evitar que aterrice en una zona que no le
  toca.
- `requireCompany` — exige rol `company` o `admin`.
- `requireAdmin` — exige rol `admin`. Si es company lo manda a `/empresa`;
  si es candidato a `/app`.

Mapa rápido:

| Path                | Zona      | Acceso                |
|---------------------|-----------|-----------------------|
| `/`                 | Pública   | Cualquiera            |
| `/ofertas`          | Pública   | Cualquiera            |
| `/ofertas/:id`      | Pública   | Cualquiera            |
| `/proposito`        | Pública   | Cualquiera            |
| `/contacto`         | Pública   | Cualquiera            |
| `/login`            | Pública   | Solo invitados        |
| `/login/empresa`    | Pública   | Solo invitados        |
| `/register`         | Pública   | Solo invitados        |
| `/app`              | Candidato | `requireLogin`        |
| `/app/profile`      | Candidato | `requireLogin`        |
| `/app/cv`           | Candidato | `requireLogin`        |
| `/app/candidaturas` | Candidato | `requireLogin`        |
| `/empresa`          | Empresa   | `requireCompany`      |
| `/empresa/ofertas`  | Empresa   | `requireCompany`      |
| `/admin/...`        | Admin     | `requireAdmin`        |

### Stores (Pinia)

- `store/auth.js` — usuario autenticado y helpers (`getUser`, `is(role)`,
  `logout`). Está marcado como `persist: true`.
- `store/lang.js` — idioma activo de la SPA.
- `store/style.js` — preferencias visuales (modo oscuro, sidebar abierto…).
- `store/searchHistory.js` — historial reciente del buscador público.

### Composables

En [`resources/js/composables/`](resources/js/composables/) hay un composable
por recurso de la API (`useJobOffers`, `useJobApplications`,
`useCandidateCv`, `useCandidateProfile`, `useCompanyProfile`,
`usePublicOffers`, `users`, `roles`, `permissions`, `categories`, etc.).
Cada uno encapsula la llamada Axios + el manejo de errores y se consume
directamente en las vistas con `const { items, fetch, save } = useX()`.

---

## Catálogos y seeders

El orden de ejecución de `DatabaseSeeder` es:

1. `RolesTableSeeder` — crea `admin`, `candidate`, `company`.
2. `PermissionsTableSeeder` — inserta los permisos (`user-*`, `role-*`,
   `permission-*`, `category-*`, además de otros legacy como `post-*`,
   `task-*`, `course-*`, `exercise-*` que se mantienen para no romper
   IDs ya cableados en el frontend).
3. `RoleHasPermissionsTableSeeder` — asocia permisos a roles.
4. `UsersTableSeeder` — usuarios de prueba (ver tabla más arriba).
5. `CategoriesTableSeeder` — categorías del blog heredado.
6. `MediaTableSeeder` — algunas imágenes de ejemplo (Spatie MediaLibrary).
7. `CatalogSeeder` — rellena `disability_type`, `job_category`,
   `contract_type`, `workday_type`, `modality_type`.
8. `DataSeeder` — datos auxiliares varios.
9. `CompaniesAndOffersSeeder` — empresas extra (ONCE, fundaciones…) y sus
   ofertas, varias marcadas como adaptadas.

> Si añades empresas o usuarios desde la app y luego quieres que esos datos
> queden en el seeder, puedes usar `iseed` (`php artisan iseed users`) y
> reemplazar el seeder generado.

---

## Comandos útiles

```bash
# Limpiar y rehacer la base con datos de prueba
php artisan migrate:fresh --seed

# Listar todas las rutas (útil para revisar middlewares aplicados)
php artisan route:list

# Vista web de rutas + payloads (laravel-request-docs)
# Disponible en /request-docs cuando el server está arriba

# Generar la clave de la app (si te falta)
php artisan key:generate

# Compilar el frontend para producción
npm run build

# Formatear PHP con Pint
./vendor/bin/pint
```

---

## Convenciones y notas

- **No mover los nombres de permisos.** El panel admin del frontend tiene
  CASL anclado a `user-create`, `user-edit`, etc. Cambiarlos sin más rompe
  la UI silenciosamente.
- **Al añadir rutas nuevas a la API,** decidir desde el principio si son
  de zona admin, de dominio o lectura compartida y aplicar el middleware
  correspondiente (`permission:`, `role:` o solo `auth:sanctum`). Dejar
  todo en `auth:sanctum` cuando la acción es claramente de un rol abre la
  puerta a IDOR.
- **Comprobaciones de propiedad** (un candidato editándose a sí mismo, una
  empresa editando solo sus ofertas) viven en el controlador. El middleware
  por rol no basta — el `role:company` permitiría a una empresa tocar la
  oferta de otra empresa si no hay verificación adicional.
- **Imágenes y CVs** se gestionan con Spatie MediaLibrary, no con `Storage`
  directo. Eso significa que cada `User` y `JobOffer` declara sus
  `MediaCollection` en el propio modelo (ver
  [`app/Models/User.php`](app/Models/User.php) y
  [`app/Models/JobOffer.php`](app/Models/JobOffer.php)).
- **El listado público** (`/api/public/job-offers`) filtra por
  `status = 'PUBLISHED'`. Las ofertas en borrador no se exponen aunque se
  conozca su id.
- **Toda la SPA** se sirve desde `routes/web.php` con un catch-all que
  carga la misma vista Blade — el routing real lo gestiona Vue Router en
  el cliente.

Para una guía paso a paso de instalación pensada para alguien que nunca
ha tocado el proyecto, mira también
[`docs/Manual_de_Usuario.md`](docs/Manual_de_Usuario.md).
