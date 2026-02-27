<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run()
    {
        \DB::table('disability_type')->insert([
            ['name' => 'Física'],
            ['name' => 'Sensorial - Visual'],
            ['name' => 'Sensorial - Auditiva'],
            ['name' => 'Intelectual'],
            ['name' => 'Psicosocial'],
            ['name' => 'Orgánica'],
        ]);

        \DB::table('job_category')->insert([
            ['name' => 'Informática y Telecomunicaciones'],
            ['name' => 'Administración y Finanzas'],
            ['name' => 'Comercial y Ventas'],
            ['name' => 'Hostelería y Turismo'],
            ['name' => 'Sanidad y Salud'],
            ['name' => 'Logística y Transporte'],
            ['name' => 'Marketing y Comunicación'],
            ['name' => 'Recursos Humanos'],
            ['name' => 'Educación y Formación'],
            ['name' => 'Ingeniería'],
        ]);

        \DB::table('contract_type')->insert([
            ['name' => 'Indefinido'],
            ['name' => 'Temporal'],
            ['name' => 'Prácticas'],
            ['name' => 'Formación y Aprendizaje'],
            ['name' => 'Autónomo'],
        ]);

        \DB::table('workday_type')->insert([
            ['name' => 'Jornada Completa'],
            ['name' => 'Media Jornada'],
            ['name' => 'Por Horas'],
            ['name' => 'Fin de Semana'],
        ]);

        \DB::table('modality_type')->insert([
            ['name' => 'Presencial'],
            ['name' => 'Remoto'],
            ['name' => 'Híbrido'],
        ]);

        \DB::table('tag')->insert([
            ['name' => 'Accesible'],
            ['name' => 'Teletrabajo'],
            ['name' => 'Primer Empleo'],
            ['name' => 'Sin Experiencia'],
            ['name' => 'Discapacidad'],
            ['name' => 'Urgente'],
            ['name' => 'Salario Competitivo'],
            ['name' => 'Horario Flexible'],
        ]);
    }
}
