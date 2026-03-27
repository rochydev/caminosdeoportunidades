<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seed de empresas adicionales con sus perfiles y ofertas de empleo.
 * Pensado para el portal de empleo inclusivo — incluye empresas
 * comprometidas con la diversidad y ofertas adaptadas.
 *
 * Ejecutar con: php artisan db:seed --class=CompaniesAndOffersSeeder
 */
class CompaniesAndOffersSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCompanies();
        $this->seedOffers();
    }

    // -------------------------------------------------------------------------
    // Empresas
    // -------------------------------------------------------------------------

    private function seedCompanies(): void
    {
        $companies = [
            [
                'user' => [
                    'name'      => 'ONCE',
                    'surname1'  => 'Fundación',
                    'alias'     => 'once',
                    'email'     => 'empleo@once.es',
                    'password'  => Hash::make('12345678'),
                    'status'    => 'ACTIVE',
                ],
                'profile' => [
                    'company_name'                  => 'ONCE',
                    'description'                   => 'La Organización Nacional de Ciegos Españoles es referencia mundial en inclusión laboral de personas con discapacidad. Ofrecemos empleo, formación y apoyo a más de 70.000 trabajadores.',
                    'sector'                        => 'Entidad Social',
                    'city'                          => 'Madrid',
                    'contact_phone'                 => '915894700',
                    'website'                       => 'https://www.once.es',
                    'offers_adapted_positions'      => true,
                    'offers_remote_work'            => true,
                    'offers_reasonable_adjustments' => true,
                    'validation_status'             => 'VALIDATED',
                ],
            ],
            [
                'user' => [
                    'name'      => 'IKEA',
                    'surname1'  => 'Ibérica',
                    'alias'     => 'ikea',
                    'email'     => 'rrhh@ikea.com',
                    'password'  => Hash::make('12345678'),
                    'status'    => 'ACTIVE',
                ],
                'profile' => [
                    'company_name'                  => 'IKEA Ibérica',
                    'description'                   => 'Empresa sueca de mobiliario y decoración del hogar. Uno de los mayores empleadores de España, con políticas activas de diversidad e inclusión en todos sus centros.',
                    'sector'                        => 'Retail y Decoración',
                    'city'                          => 'Barcelona',
                    'contact_phone'                 => '935070800',
                    'website'                       => 'https://www.ikea.com/es',
                    'offers_adapted_positions'      => true,
                    'offers_remote_work'            => false,
                    'offers_reasonable_adjustments' => true,
                    'validation_status'             => 'VALIDATED',
                ],
            ],
            [
                'user' => [
                    'name'      => 'Accenture',
                    'surname1'  => 'España',
                    'alias'     => 'accenture',
                    'email'     => 'rrhh@accenture.com',
                    'password'  => Hash::make('12345678'),
                    'status'    => 'ACTIVE',
                ],
                'profile' => [
                    'company_name'                  => 'Accenture España',
                    'description'                   => 'Consultora global de tecnología y gestión. Comprometida con la igualdad y la inclusión: Premio Empresa Más Inclusiva en 2023. Más de 20.000 empleados en España.',
                    'sector'                        => 'Consultoría y Tecnología',
                    'city'                          => 'Madrid',
                    'contact_phone'                 => '915966000',
                    'website'                       => 'https://www.accenture.com/es-es',
                    'offers_adapted_positions'      => true,
                    'offers_remote_work'            => true,
                    'offers_reasonable_adjustments' => true,
                    'validation_status'             => 'VALIDATED',
                ],
            ],
            [
                'user' => [
                    'name'      => 'Hospital Sant Pau',
                    'surname1'  => 'Barcelona',
                    'alias'     => 'hsantpau',
                    'email'     => 'rrhh@santpau.cat',
                    'password'  => Hash::make('12345678'),
                    'status'    => 'ACTIVE',
                ],
                'profile' => [
                    'company_name'                  => 'Hospital de la Santa Creu i Sant Pau',
                    'description'                   => 'Centro hospitalario de referencia en Cataluña, con más de 100 años de historia. Comprometido con la inserción laboral y la diversidad en el sector sanitario.',
                    'sector'                        => 'Sanidad',
                    'city'                          => 'Barcelona',
                    'contact_phone'                 => '932919000',
                    'website'                       => 'https://www.santpau.cat',
                    'offers_adapted_positions'      => true,
                    'offers_remote_work'            => false,
                    'offers_reasonable_adjustments' => true,
                    'validation_status'             => 'VALIDATED',
                ],
            ],
            [
                'user' => [
                    'name'      => 'Fundación MAPFRE',
                    'surname1'  => 'S.A.',
                    'alias'     => 'mapfre',
                    'email'     => 'rrhh@mapfre.com',
                    'password'  => Hash::make('12345678'),
                    'status'    => 'ACTIVE',
                ],
                'profile' => [
                    'company_name'                  => 'MAPFRE España',
                    'description'                   => 'Compañía aseguradora líder en España. Impulsa la integración laboral de personas con discapacidad a través de su Fundación y programas de empleo inclusivo.',
                    'sector'                        => 'Seguros y Finanzas',
                    'city'                          => 'Madrid',
                    'contact_phone'                 => '915814284',
                    'website'                       => 'https://www.mapfre.es',
                    'offers_adapted_positions'      => true,
                    'offers_remote_work'            => true,
                    'offers_reasonable_adjustments' => true,
                    'validation_status'             => 'VALIDATED',
                ],
            ],
        ];

        foreach ($companies as $data) {
            // Evitar duplicados si ya existe el email
            if (User::where('email', $data['user']['email'])->exists()) {
                continue;
            }

            $user = User::create($data['user']);
            $user->assignRole('company');

            DB::table('company_profile')->insert(array_merge(
                ['user_id' => $user->id],
                $data['profile'],
                ['created_at' => now(), 'updated_at' => now()]
            ));
        }
    }

    // -------------------------------------------------------------------------
    // Ofertas de empleo
    // -------------------------------------------------------------------------

    private function seedOffers(): void
    {
        // Buscamos los IDs de empresa por email para no depender de IDs hardcodeados
        $companies = User::whereIn('email', [
            'rrhh@mercadona.com',
            'rrhh@inditex.com',
            'rrhh@telefonica.com',
            'empleo@once.es',
            'rrhh@ikea.com',
            'rrhh@accenture.com',
            'rrhh@santpau.cat',
            'rrhh@mapfre.com',
        ])->pluck('id', 'email');

        $offers = [

            // ── ONCE ──────────────────────────────────────────────────────────
            [
                'company_user_id'  => $companies['empleo@once.es'] ?? null,
                'category_id'      => 9,  // Educación y Formación
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 3,  // Híbrido
                'title'            => 'Técnico/a de Orientación Laboral',
                'description'      => 'Te incorporarás al equipo de inserción laboral de la ONCE para acompañar a personas con discapacidad visual en su búsqueda de empleo. Realizarás entrevistas de orientación, planes de mejora de empleabilidad y coordinación con empresas colaboradoras.',
                'requirements'     => 'Grado en Psicología, Pedagogía o Trabajo Social. Experiencia en orientación laboral o inserción. Se valorará conocimiento en accesibilidad y discapacidad visual.',
                'adaptations'      => 'Puesto 100% accesible. Formación en herramientas de accesibilidad proporcionada. Jornada flexible con opción de teletrabajo 2 días/semana.',
                'city'             => 'Madrid',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],
            [
                'company_user_id'  => $companies['empleo@once.es'] ?? null,
                'category_id'      => 1,  // Informática
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 2,  // Remoto
                'title'            => 'Desarrollador/a de Accesibilidad Web',
                'description'      => 'Formarás parte del equipo de tecnología de la ONCE trabajando en auditorías de accesibilidad web (WCAG 2.1), desarrollo de componentes accesibles y consultoría para empresas. Contribuirás directamente a que la web sea un lugar más inclusivo.',
                'requirements'     => 'Experiencia en desarrollo frontend (HTML, CSS, JS). Conocimiento de estándares WCAG y ARIA. Se valorará certificación CPACC o WAS.',
                'adaptations'      => 'Puesto completamente en remoto. Todos los entornos de trabajo son accesibles. Soporte para cualquier necesidad de ajuste.',
                'city'             => 'Madrid',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],

            // ── IKEA ──────────────────────────────────────────────────────────
            [
                'company_user_id'  => $companies['rrhh@ikea.com'] ?? null,
                'category_id'      => 6,  // Logística y Transporte
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 1,  // Presencial
                'title'            => 'Operario/a de Almacén y Logística',
                'description'      => 'Responsable de la preparación de pedidos, reposición de producto y control de stock en nuestro almacén de Barcelona. Trabajarás en un equipo diverso e inclusivo con todas las instalaciones adaptadas.',
                'requirements'     => 'No se requiere experiencia previa. Se proporciona formación completa. Carnet de carretillero valorable.',
                'adaptations'      => 'Instalaciones completamente adaptadas. Puesto con posibilidad de ajuste de tareas según necesidades. Turno fijo para mayor estabilidad.',
                'city'             => 'Barcelona',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],
            [
                'company_user_id'  => $companies['rrhh@ikea.com'] ?? null,
                'category_id'      => 3,  // Comercial y Ventas
                'contract_type_id' => 2,  // Temporal
                'workday_type_id'  => 2,  // Media Jornada
                'modality_id'      => 1,  // Presencial
                'title'            => 'Colaborador/a de Tienda — Sección Baño',
                'description'      => 'Asesoramiento a clientes en la sección de baño, mantenimiento del orden del área de exposición y gestión básica de stock. Media jornada con posibilidad de ampliar.',
                'requirements'     => 'Buenas habilidades comunicativas y orientación al cliente. Se valorará experiencia en retail.',
                'adaptations'      => 'Horario adaptable. Tienda accesible en silla de ruedas. Posibilidad de ajuste de ritmo de trabajo.',
                'city'             => 'Zaragoza',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],

            // ── ACCENTURE ─────────────────────────────────────────────────────
            [
                'company_user_id'  => $companies['rrhh@accenture.com'] ?? null,
                'category_id'      => 1,  // Informática
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 3,  // Híbrido
                'title'            => 'Analista de Datos — Intelligence & Analytics',
                'description'      => 'Trabajarás en proyectos de analítica avanzada para clientes del sector banca y seguros. Transformarás datos en insights accionables utilizando Python, Power BI y Azure.',
                'requirements'     => 'Grado en Ingeniería, Matemáticas o Estadística. Dominio de Python y SQL. Conocimientos de Power BI o Tableau. Inglés B2.',
                'adaptations'      => 'Teletrabajo 3 días por semana. Equipos ergonómicos a elección. Sin restricciones de movilidad (todo el trabajo es en remoto o en oficina accesible).',
                'city'             => 'Madrid',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],
            [
                'company_user_id'  => $companies['rrhh@accenture.com'] ?? null,
                'category_id'      => 1,  // Informática
                'contract_type_id' => 3,  // Prácticas
                'workday_type_id'  => 2,  // Media Jornada
                'modality_id'      => 3,  // Híbrido
                'title'            => 'Becario/a Cloud & DevOps',
                'description'      => 'Prácticas en el área de tecnología cloud. Participarás en la implementación de pipelines CI/CD, configuración de infraestructura en Azure/AWS y automatización de procesos.',
                'requirements'     => 'Estar cursando Ingeniería Informática o similar. Conocimientos básicos de Linux y Git. Ganas de aprender.',
                'adaptations'      => 'Horario flexible de lunes a viernes. Remoto los días que se desee. Tutor asignado para apoyo continuo.',
                'city'             => 'Barcelona',
                'is_adapted'       => false,
                'status'           => 'PUBLISHED',
            ],
            [
                'company_user_id'  => $companies['rrhh@accenture.com'] ?? null,
                'category_id'      => 8,  // RRHH
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 3,  // Híbrido
                'title'            => 'HR Business Partner — Diversidad e Inclusión',
                'description'      => 'Rol clave dentro del equipo de People & Culture. Liderarás las iniciativas de diversidad, equidad e inclusión: programas de inclusión de personas con discapacidad, métricas D&I y formación a managers.',
                'requirements'     => 'Grado en RRHH, Psicología o ADE. Mínimo 3 años en roles de RRHH. Experiencia en D&I valorada muy positivamente.',
                'adaptations'      => 'Teletrabajo parcial. Puesto flexible en cuanto a ubicación (Madrid o Barcelona). Sin barreras arquitectónicas.',
                'city'             => 'Madrid',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],

            // ── HOSPITAL SANT PAU ─────────────────────────────────────────────
            [
                'company_user_id'  => $companies['rrhh@santpau.cat'] ?? null,
                'category_id'      => 5,  // Sanidad y Salud
                'contract_type_id' => 2,  // Temporal
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 1,  // Presencial
                'title'            => 'Auxiliar de Enfermería',
                'description'      => 'Apoyo al equipo de enfermería en la atención a pacientes en planta de hospitalización. Tareas de higiene, movilización asistida y registro de constantes vitales.',
                'requirements'     => 'Titulación de Técnico/a en Cuidados Auxiliares de Enfermería (TCAE). Carnet de vacunación al día.',
                'adaptations'      => 'Adaptaciones de puesto disponibles según informe de discapacidad. Turnos organizados con antelación.',
                'city'             => 'Barcelona',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],
            [
                'company_user_id'  => $companies['rrhh@santpau.cat'] ?? null,
                'category_id'      => 2,  // Administración
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 2,  // Media Jornada
                'modality_id'      => 1,  // Presencial
                'title'            => 'Administrativo/a de Admisiones',
                'description'      => 'Gestión de citas y admisión de pacientes, atención presencial y telefónica, actualización de historiales clínicos y coordinación con los servicios médicos.',
                'requirements'     => 'Ciclo Formativo en Administración o Documentación Sanitaria. Buenas habilidades comunicativas. Catalan/Castellano.',
                'adaptations'      => 'Puesto sedentario en mostrador adaptado. Herramientas de accesibilidad disponibles.',
                'city'             => 'Barcelona',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],

            // ── MAPFRE ────────────────────────────────────────────────────────
            [
                'company_user_id'  => $companies['rrhh@mapfre.com'] ?? null,
                'category_id'      => 2,  // Administración y Finanzas
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 3,  // Híbrido
                'title'            => 'Agente de Atención al Cliente — Seguros',
                'description'      => 'Atención y asesoramiento a asegurados por teléfono y chat. Gestión de siniestros, consultas de pólizas y tramitación de prestaciones. Formación completa a cargo de la empresa.',
                'requirements'     => 'Bachillerato o FP. Buena capacidad de comunicación. Conocimiento de herramientas ofimáticas básicas.',
                'adaptations'      => 'Puesto de trabajo totalmente adaptado. Teletrabajo 2 días a la semana. Software de accesibilidad disponible.',
                'city'             => 'Madrid',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],
            [
                'company_user_id'  => $companies['rrhh@mapfre.com'] ?? null,
                'category_id'      => 7,  // Marketing y Comunicación
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 3,  // Híbrido
                'title'            => 'Responsable de Comunicación Inclusiva',
                'description'      => 'Diseño y ejecución de la estrategia de comunicación interna y externa en materia de diversidad e inclusión. Gestión de redes sociales del área D&I, redacción de contenidos accesibles y coordinación con medios.',
                'requirements'     => 'Grado en Comunicación, Periodismo o Marketing. Experiencia en comunicación corporativa. Conocimiento del lenguaje inclusivo y accesible.',
                'adaptations'      => 'Teletrabajo 3 días/semana. Flexibilidad horaria. Oficina totalmente accesible.',
                'city'             => 'Madrid',
                'is_adapted'       => false,
                'status'           => 'PUBLISHED',
            ],

            // ── TELEFÓNICA (más ofertas) ───────────────────────────────────────
            [
                'company_user_id'  => $companies['rrhh@telefonica.com'] ?? null,
                'category_id'      => 1,  // Informática
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 2,  // Remoto
                'title'            => 'Ingeniero/a de Redes y Comunicaciones',
                'description'      => 'Diseño, implementación y mantenimiento de infraestructuras de red de alta disponibilidad. Participarás en proyectos de despliegue 5G y fibra óptica a nivel nacional.',
                'requirements'     => 'Grado en Ingeniería de Telecomunicación. Conocimientos de routing/switching (Cisco, Juniper). Certificación CCNA valorable.',
                'adaptations'      => 'Puesto 100% en remoto. Equipamiento de trabajo premium proporcionado. Soporte IT para cualquier necesidad de accesibilidad.',
                'city'             => 'Madrid',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],

            // ── MERCADONA (más ofertas) ────────────────────────────────────────
            [
                'company_user_id'  => $companies['rrhh@mercadona.com'] ?? null,
                'category_id'      => 6,  // Logística
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 1,  // Presencial
                'title'            => 'Mozo/a de Almacén — Bloque Logístico',
                'description'      => 'Carga, descarga y clasificación de mercancía en nuestro bloque logístico. Turnos rotativos de mañana y tarde. Contrato indefinido desde el primer día.',
                'requirements'     => 'No se requiere experiencia. Disponibilidad para turnos rotativos.',
                'adaptations'      => 'Instalaciones 100% accesibles. Posibilidad de adaptar el puesto según certificado de discapacidad. Pausas adicionales si es necesario.',
                'city'             => 'Valencia',
                'is_adapted'       => true,
                'status'           => 'PUBLISHED',
            ],

            // ── INDITEX (más ofertas) ──────────────────────────────────────────
            [
                'company_user_id'  => $companies['rrhh@inditex.com'] ?? null,
                'category_id'      => 1,  // Informática
                'contract_type_id' => 1,  // Indefinido
                'workday_type_id'  => 1,  // Completa
                'modality_id'      => 3,  // Híbrido
                'title'            => 'UX Designer — Digital Products',
                'description'      => 'Diseño de experiencias digitales para las aplicaciones móviles y web del grupo Inditex. Investigación de usuarios, prototipado y pruebas de usabilidad. Enfoque en diseño accesible (WCAG).',
                'requirements'     => 'Grado en Diseño o similar. Portfolio con proyectos UX/UI. Dominio de Figma. Conocimiento de accesibilidad digital valorado.',
                'adaptations'      => 'Teletrabajo híbrido (3 días remoto). Oficinas accesibles. Herramientas de diseño a elección del candidato.',
                'city'             => 'A Coruña',
                'is_adapted'       => false,
                'status'           => 'PUBLISHED',
            ],

            // ── Borrador de ejemplo ────────────────────────────────────────────
            [
                'company_user_id'  => $companies['empleo@once.es'] ?? null,
                'category_id'      => 9,  // Educación y Formación
                'contract_type_id' => 2,  // Temporal
                'workday_type_id'  => 2,  // Media Jornada
                'modality_id'      => 1,  // Presencial
                'title'            => 'Monitor/a de Actividades de Ocio Inclusivo',
                'description'      => 'Organización y dinamización de actividades de ocio y tiempo libre para personas con discapacidad visual. Excursiones, talleres culturales y actividades deportivas adaptadas.',
                'requirements'     => 'Titulación en Animación Sociocultural o Monitor de Tiempo Libre. Empatía y capacidad de trabajo con colectivos con discapacidad.',
                'adaptations'      => 'Puesto diseñado para personas con y sin discapacidad.',
                'city'             => 'Sevilla',
                'is_adapted'       => true,
                'status'           => 'DRAFT',
            ],
        ];

        foreach ($offers as $offer) {
            if (empty($offer['company_user_id'])) {
                continue; // Si la empresa no existe, saltamos la oferta
            }

            DB::table('job_offer')->insert(array_merge(
                $offer,
                ['created_at' => now(), 'updated_at' => now()]
            ));
        }
    }
}
