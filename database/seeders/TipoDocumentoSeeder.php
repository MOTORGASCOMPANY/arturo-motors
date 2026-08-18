<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'DNI (Copia Legible)', 'requerido' => true, 'vencible' => true],
            ['nombre' => 'Antecedentes Penales', 'requerido' => true, 'vencible' => true],
            ['nombre' => 'Antecedentes Policiales', 'requerido' => true, 'vencible' => true],
            ['nombre' => 'Certificado de Estudios', 'requerido' => false, 'vencible' => false],
            ['nombre' => 'Currículum Vitae (CV)', 'requerido' => true, 'vencible' => false],
            ['nombre' => 'Contrato Firmado', 'requerido' => true, 'vencible' => false],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipo_documentos')->updateOrInsert(
                ['nombre' => $tipo['nombre']],
                [
                    'requerido' => $tipo['requerido'],
                    'vencible' => $tipo['vencible'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
